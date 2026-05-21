<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Models\ApprovalRequest;
use App\Models\LeaveRequest;
use App\Models\StructureRequest;
use App\Services\ApprovalService;
use App\Services\LeaveRequestService;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalRequestController extends Controller
{
    public function index(LeaveRequestService $leaveRequestService)
    {
        Gate::authorize(PermissionType::LeaveApprovalList);

        $approvalRequests = ApprovalRequest::query()
            ->with([
                'approvable' => function (MorphTo $morphTo) {
                    $morphTo->constrain([
                        LeaveRequest::class => function ($query) {
                            $query->with(['user.structure.parent', 'leaveType.approvalRule', 'approval.approver']);
                        },
                        StructureRequest::class => function ($query) {
                            $query->with(['user', 'structure']);
                        },
                    ]);
                },
            ])
            ->where('approver_id', auth()->id())
            ->latest()
            ->get()
            ->map(function (ApprovalRequest $approvalRequest) use ($leaveRequestService) {
                if ($approvalRequest->approvable instanceof LeaveRequest) {
                    $leaveRequestService->buildProcessSteps($approvalRequest->approvable);
                }

                return $approvalRequest;
            });

        return view('leave_approvals.index', [
            'approvalRequests' => $approvalRequests,
        ]);
    }

    public function update(Request $request, ApprovalRequest $approvalRequest, ApprovalService $approvalService): RedirectResponse
    {
        Gate::authorize(PermissionType::LeaveApprovalEdit);

        $validated = $request->validate([
            'salary' => ['sometimes','numeric'],
            'decision' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'string'],
        ]);

        $approvalRequest->loadMissing('approvable');

        if ($approvalRequest->approvable instanceof StructureRequest) {
            $status = $approvalService->handleStructureRequest($approvalRequest, $validated);

            return redirect()->route('leave-approvals.index')->with('status', $status);
        }

        $result = $approvalService->handleLeaveRequest($approvalRequest, $validated);

        if (isset($result['error'])) {
            return redirect()->route('leave-approvals.index')->withErrors(['approval' => $result['error']]);
        }

        return redirect()->route('leave-approvals.index')->with('status', $result['status']);
    }
}
