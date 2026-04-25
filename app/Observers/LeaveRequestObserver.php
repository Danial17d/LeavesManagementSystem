<?php

namespace App\Observers;

use App\Models\ApprovalRequest;
use App\Models\LeaveRequest;
use App\Models\Notification;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "created" event.
     */
    public function created(LeaveRequest $leaveRequest): void
    {
        $user = $leaveRequest->user;
        $structure = $user->structure;
        $managerId = $structure?->manager_id;
        $notifiedUserId = null;

        if ($managerId && $user->id === $managerId) {
            $parentStructure = $structure?->parent;
            $notifiedUserId = $parentStructure?->manager_id;

            if (! $notifiedUserId) {
                return;
            }

            $leaveRequest->approval()->create([
                'approver_id' => $notifiedUserId,
                'step' => 1,
                'status' => 'pending',
            ]);
        } else {
            if (! $managerId) {
                return;
            }

            $notifiedUserId = $managerId;

            $leaveRequest->approval()->create([
                'approver_id' => $notifiedUserId,
                'step' => 1,
                'status' => 'pending',
            ]);
        }

        if ($notifiedUserId) {
            Notification::create([
                'user_id' => $notifiedUserId,
                'title' => 'New Leave Request',
                'body' => sprintf(
                    '%s submitted a leave request from %s to %s.',
                    $user->name,
                    $leaveRequest->from,
                    $leaveRequest->to
                ),
                'read' => false,
            ]);
        }
    }

    /**
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "deleted" event.
     */
    public function deleted(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "restored" event.
     */
    public function restored(LeaveRequest $leaveRequest): void
    {
        //
    }

    /**
     * Handle the LeaveRequest "force deleted" event.
     */
    public function forceDeleted(LeaveRequest $leaveRequest): void
    {
        //
    }
}
