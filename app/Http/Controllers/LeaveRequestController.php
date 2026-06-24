<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\PermissionType;
use App\Http\Requests\LeaveRequestStore;
use App\Models\ApprovalRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Notification;
use App\Services\LeaveRequestService;
use App\Services\PayRollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index()
    {
        Gate::authorize(PermissionType::LeaveRequestList);


        $leaveRequestService = new LeaveRequestService();

        $leaveRequests = LeaveRequest::query()
            ->with([
                'user.structure.manager',
                'leaveType.approvalRule',
                'approval.approver',
            ])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $leaveRequests->setCollection(
            $leaveRequests->getCollection()->map(function (LeaveRequest $leaveRequest) use ($leaveRequestService) {
                $leaveRequest->processSteps = $leaveRequestService->buildProcessSteps($leaveRequest);

                return $leaveRequest;
            })
        );

        return view('leave_requests.index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function show(LeaveRequest $leaveRequest, LeaveRequestService $leaveRequestService): View
    {
        Gate::authorize(PermissionType::LeaveRequestView);

        $leaveRequest->load([
            'user.structure.manager',
            'leaveType.approvalRule',
            'approval.approver',
        ]);

        $leaveRequest->processSteps = $leaveRequestService->buildProcessSteps($leaveRequest);
        $approverReason = $leaveRequest->approval
            ->whereNotNull('note')
            ->sortByDesc('acted_at')
            ->first();

        return view('leave_requests.show', [
            'leaveRequest' => $leaveRequest,
            'approverReason' => $approverReason,
        ]);
    }

    public function create()
    {
        $user = auth()->user()->loadMissing('structure.parent', 'managedStructure');

        if ($user->isChiefExecutive()) {
            return redirect()
                ->route('leave-requests.index')
                ->withErrors(['leave_request' => 'The CEO cannot request leave because no higher approver is assigned.']);
        }

        $activeLeave = LeaveRequest::where('user_id', auth()->id())->active()->first();

        if ($activeLeave && ! auth()->user()->is_return) {
            $until = \Carbon\Carbon::parse($activeLeave->to)->format('M d, Y');
            return redirect()
                ->route('leave-requests.index')
                ->withErrors(['leave_request' => "You already have an active leave request running until {$until}. You cannot submit a new one until it ends."]);
        }

        $leaveRequest = LeaveRequest::with('user')->where('user_id', auth()->id())->first();

        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', auth()->id())
            ->where('year', now()->year)
            ->get()
            ->mapWithKeys(function (LeaveBalance $balance) {
                return [
                    $balance->leaveType->name => $balance->entitled_days + $balance->carried_days - $balance->used_days,
                ];
            });
        $leaveTypes = LeaveType::with('approvalRule')
            ->select('name','days')
            ->whereHas('approvalRule', function ($query) {$query->whereNotNull('level');})
            ->get();

        $isThereTeamLeaveRequest = LeaveRequest::where('status' ,RequestStatus::Approved )
        ->whereHas('user', function ($query) {
            $query->where('structure_id', auth()->user()->structure_id);
            $query->where('is_return' , false);

        })->exists();

        return view('leave_requests.create',[
            'leaveRequest' => $leaveRequest,
            'leaveTypes' =>$leaveTypes,
            'leaveBalances' => $leaveBalances,
            'isThereTeamLeaveRequest' => $isThereTeamLeaveRequest,
        ]);
    }
    public function store(LeaveRequestStore $request)
    {
        Gate::authorize(PermissionType::LeaveRequestCreate);

        if ($request->user()->loadMissing('structure.parent')->isChiefExecutive()) {
            return redirect()
                ->route('leave-requests.index')
                ->withErrors(['leave_request' => 'The CEO cannot request leave because no higher approver is assigned.']);
        }

        $request->validate([]);

        $leaveTypeId = LeaveType::where('name', $request->leave_type)->first()->id;

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('attachments')
            : null;


        $leaveRequest = LeaveRequest::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $leaveTypeId,
            'from' => $request->start_date,
            'to' => $request->end_date,
            'requested_days' => $request->integer('days_requested'),
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
        ]);


        return redirect()->route('leave-requests.index')->with('status', 'Leave request created successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        Gate::authorize(PermissionType::LeaveRequestDelete);

        if ($leaveRequest->status === RequestStatus::Pending->value) {
            $daysSinceApproval = Carbon::parse($leaveRequest->created_at)->diffInDays(now());

            if ($daysSinceApproval >= 3) {
                return redirect()->back()->with('error', 'Leave request cannot be cancelled because it was approved more than 3 days ago.');
            }
        }

        $leaveRequest->update(['status' => RequestStatus::Cancelled]);
        $leaveRequest->approval()->update([
            'status' => RequestStatus::Cancelled,
            'approver_id' => null,
        ]);

        return redirect()->route('leave-requests.index')
            ->with('status', 'Leave request cancelled successfully.');
    }
}
