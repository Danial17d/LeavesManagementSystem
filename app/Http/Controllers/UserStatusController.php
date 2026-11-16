<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Enums\RequestStatus;
use App\Services\PayRollService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserStatusController extends Controller
{
    public function __invoke(Request $request, PayRollService $payRollService)
    {
        Gate::authorize(PermissionType::PayRollCalculate);

        $user = auth()->user();

        $leaveRequest = $user->leave()
            ->where('status', RequestStatus::Approved)
            ->orderBy('id', 'desc')
            ->first();

        if (! $leaveRequest) {
            return redirect()->route('dashboard')->with(['error' => 'No approved leave request found.']);
        }

        $user->update(['is_return' => true]);


        $leaveEnd = Carbon::parse($leaveRequest->to);

        if ($leaveRequest->deductsFromBalance() && now()->lt($leaveEnd)) {
            $remainingDays = now()->diffInDays($leaveEnd);

            $balance = $user->leaveBalances()
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();

            if ($balance) {
                $balance->used_days = max(0, floor($balance->used_days - $remainingDays));
                $balance->save();
            }
        }

        $payRollService->calculateSalaryForAbsentDays($user);

        return redirect()->route('dashboard')->with(['status' => 'New Salary Calculated']);
    }
}
