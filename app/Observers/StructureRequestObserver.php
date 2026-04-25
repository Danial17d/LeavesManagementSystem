<?php

namespace App\Observers;

use App\Enums\RequestStatus;
use App\Models\Notification;
use App\Models\StructureRequest;

class StructureRequestObserver
{
    public function created(StructureRequest $structureRequest)
    {
        $notifiedUserId = $structureRequest->structure->manager_id;

        $structureRequest->approval()->create([
            'approver_id' => $notifiedUserId,
            'step'  => 1,
            'status' => RequestStatus::Pending->value,
        ]);

        $body = auth()->user()->name . " has requested a structure assignment or movement.";

        Notification::create([
            'user_id' => $notifiedUserId,
            'title' => "Structure Request",
            'body' => $body,
            'read' => false,
        ]);
    }
}
