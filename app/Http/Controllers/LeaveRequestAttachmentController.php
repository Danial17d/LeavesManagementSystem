<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestAttachmentController extends Controller
{
    public function __invoke(LeaveRequest $leaveRequest): StreamedResponse
    {
        $this->authorize('download', $leaveRequest);

        abort_unless($leaveRequest->attachment, 404);
        abort_unless(Storage::exists($leaveRequest->attachment), 404);

        return Storage::download($leaveRequest->attachment, basename($leaveRequest->attachment));
    }
}

