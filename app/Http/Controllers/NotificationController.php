<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function store(Request $request, Notification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if (! $notification->read) {
            $notification->update(['read' => true]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->user()
            ->notifications()
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
