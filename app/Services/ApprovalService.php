<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\ApprovalRequest;
use App\Models\LeaveBalance;
use App\Models\Notification;

class ApprovalService
{
    public function __construct(private readonly LeaveRequestService $leaveRequestService, private readonly PayRollService $payRollService) {}

    public function handleStructureRequest(ApprovalRequest $approvalRequest, array $validated): string
    {
        $approvalRequest->loadMissing(['approvable.user', 'approvable.structure']);

        $structureRequest = $approvalRequest->approvable;
        $requester = $structureRequest->user;

        $approvalRequest->update([
            'status' => $validated['decision'],
            'acted_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        if ($validated['decision'] === 'approved') {
            $structureRequest->update(['status' => RequestStatus::Approved->value]);

            $requester->update(['structure_id' => $structureRequest->structure_id]);

            Notification::create([
                'title' => 'Structure Request Approved',
                'user_id' => $requester->id,
                'body' => 'Your structure assignment request has been approved.',
                'read' => false,
            ]);

            return 'Structure request approved.';
        }

        $structureRequest->update(['status' => RequestStatus::Rejected->value]);

        Notification::create([
            'title' => 'Structure Request Rejected',
            'user_id' => $requester->id,
            'body' => 'Your structure assignment request has been rejected.',
            'read' => false,
        ]);

        return 'Structure request rejected.';
    }

    public function handleLeaveRequest(ApprovalRequest $approvalRequest, array $validated): array
    {
        $approvalRequest->loadMissing([
            'approvable.user.structure.parent',
            'approvable.leaveType.approvalRule',
            'approver',
        ]);

        $leaveRequest = $approvalRequest->approvable;
        $requester = $leaveRequest->user;

        if ($validated['decision'] === 'approved') {
            $nextStructure = $this->leaveRequestService
                ->approvalStructures($leaveRequest)
                ->values()
                ->get($approvalRequest->step);

            if ($nextStructure?->manager_id) {

                $approvalRequest->update([
                    'status' => $validated['decision'],
                    'acted_at' => now(),
                    'note'  => $validated['note'] ?? null,
                ]);

                $leaveRequest->approval()->firstOrCreate(
                    ['step' => $approvalRequest->step + 1],
                    ['approver_id' => $nextStructure->manager_id, 'status' => 'pending']
                );

                $leaveRequest->update([
                    'status' => 'pending',
                    'current_step' => $approvalRequest->step + 1,
                ]);
                Notification::create([
                    'user_id' => $nextStructure->manager_id,
                    'title' => 'New Leave Request',
                    'body' => sprintf(
                        '%s submitted a leave request from %s to %s.',
                        $requester->name,
                        $leaveRequest->from,
                        $leaveRequest->to
                    ),
                    'read' => false,
                ]);

                return ['status' => 'Leave request forwarded to the next approver.'];
            }

            if ($leaveRequest->deductsFromBalance()) {
                $balance = LeaveBalance::where('user_id', $requester->id)
                    ->where('leave_type_id', $leaveRequest->leave_type_id)
                    ->where('year', now()->year)
                    ->first();

                $remaining = ($balance->entitled_days + $balance->carried_days) - $balance->used_days;

                if ($leaveRequest->requested_days > $remaining) {
                    return ['error' => 'Not enough leave balance.'];
                }
            }

            $approvalRequest->update([
                'status' => $validated['decision'],
                'acted_at' => now(),
                'note' => $validated['note'] ?? null,
            ]);

            if ($leaveRequest->deductsFromBalance()) {
                $balance->increment('used_days', (int) $leaveRequest->requested_days);
            }

            $leaveRequest->update([
                'status'       => 'approved',
                'current_step' => $approvalRequest->step,
            ]);

            if (mb_strtolower($leaveRequest->leaveType->pay_type) === 'unpaid') {
                $this->payRollService->calculateSalary($leaveRequest->user, $leaveRequest->requested_days);
            }

            Notification::create([
                'title' => 'Leave Request Approved',
                'user_id' => $requester->id,
                'body' => 'Your leave request has been approved.',
                'read' => false,
            ]);

            return ['status' => 'Leave request approved.'];
        }

        $approvalRequest->update([
            'status'=> $validated['decision'],
            'acted_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        $leaveRequest->update([
            'status'       => 'rejected',
            'current_step' => $approvalRequest->step,
        ]);

        Notification::create([
            'title' => 'Leave Request Rejected',
            'user_id' => $requester->id,
            'body' => 'Your leave request has been rejected.',
            'read' => false,
        ]);

        return ['status' => 'Leave request rejected.'];
    }
}
