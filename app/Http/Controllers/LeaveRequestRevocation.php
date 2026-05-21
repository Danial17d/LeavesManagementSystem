<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Enums\RequestStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Services\PayRollService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LeaveRequestRevocation extends Controller
{
    public function update(LeaveRequest $leaveRequest, PayRollService $payRollService)
    {
        Gate::authorize(PermissionType::LeaveRequestRevoke);

        if ($leaveRequest->status !== RequestStatus::Approved->value) {
            return redirect()->back()->withErrors(['revoke' => 'Only approved leave requests can be revoked.']);
        }

        if (Carbon::parse($leaveRequest->to)->isPast()) {
            return redirect()->back()->withErrors(['revoke' => 'Cannot revoke a leave request that has already ended.']);
        }

        $leaveRequest->loadMissing('user', 'leaveType');

        if ($leaveRequest->deductsFromBalance()) {
            LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', now()->year)
                ->decrement('used_days', $leaveRequest->requested_days);
        }

        if (mb_strtolower($leaveRequest->leaveType->pay_type) === 'unpaid') {
            $payRollService->reverseUnpaidLeaveDeduction($leaveRequest->user);
        }

        $leaveRequest->approval()->update(['status' => RequestStatus::Rejected->value]);

        $leaveRequest->update(['status' => RequestStatus::Rejected->value]);

        $leaveRequest->user->update(['is_return' => true]);

        Notification::create([
            'title'   => 'Leave Request Revoked',
            'user_id' => $leaveRequest->user_id,
            'body'    => 'Your approved leave request has been revoked by an administrator.',
            'read'    => false,
        ]);

        return redirect()->route('leave-requests.show', $leaveRequest)
            ->with('status', 'Leave request revoked successfully.');
    }
}
