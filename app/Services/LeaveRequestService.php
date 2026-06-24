<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\Structure;
use Illuminate\Support\Collection;

class LeaveRequestService
{
    public function buildProcessSteps(LeaveRequest $leaveRequest): Collection
    {

        $approvalsByStep = $leaveRequest->approval->keyBy('step');
        $processSteps = collect([
            [
                'step' => 0,
                'title' => 'Request Submitted',
                'actor' => $leaveRequest->user?->name ?? 'Employee',
                'role' => 'Requester',
                'status' => 'submitted',
                'note' => $leaveRequest->reason,
                'acted_at' => $leaveRequest->created_at,
                'is_current' => false,
            ],
        ]);

        foreach ($this->approvalStructures($leaveRequest) as $index => $currentStructure) {
            $stepNumber = $index + 1;
            $approval = $approvalsByStep->get($stepNumber);
            $manager = $approval?->approver ?: $currentStructure->manager;

            $processSteps->push([
                'step' => $stepNumber,
                'title' => $stepNumber === 1 ? 'Direct Manager Review' : 'Level '.$stepNumber.' Approval',
                'actor' => $manager?->name ?? 'No manager assigned',
                'role' => $currentStructure->name ?? 'Structure',
                'status' => $approval?->status ?? ($leaveRequest->current_step === $stepNumber ? 'pending' : 'waiting'),
                'note' => $approval?->note,
                'acted_at' => $approval?->acted_at,
                'is_current' => $leaveRequest->status === 'pending' && $leaveRequest->current_step === $stepNumber,
            ]);
        }

        $leaveRequest->setRelation('processSteps', $processSteps);

        return $processSteps;
    }

    public function approvalStructures(LeaveRequest $leaveRequest): Collection
    {
        $leaveRequest->loadMissing([
            'user.structure',
            'leaveType.approvalRule',
        ]);

        $structure = $leaveRequest->user?->structure;

        if (! $structure) {
            return collect();
        }

        $levels = (int) ($leaveRequest->leaveType?->approvalRule?->level ?? 0);

        $managers = $this->managerHierarchy($structure)
            ->filter(fn (Structure $currentStructure) => $currentStructure->manager_id !== null)
            ->unique('manager_id')
            ->values();

        if ($leaveRequest->user && $managers->isNotEmpty() && $managers->first()->manager_id === $leaveRequest->user->id) {

            $managers = $managers->slice(1)->values();
        }

        if ($levels > 0) {

            $managers = $managers->take($levels)->values();
        }

        return $managers;
    }

    private function managerHierarchy(Structure $structure): Collection
    {
        return $structure->ancestorsAndSelf()
            ->with('manager')
            ->orderByDesc($structure->getDepthName())
            ->get();
    }
}
