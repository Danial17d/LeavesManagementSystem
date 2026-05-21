<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\LeaveRequest;
use App\Models\PayRoll;
use App\Models\User;
use Carbon\Carbon;

class PayRollService
{
    public function calculateSalaryForUnpaidLeaves(User $user, int $requestedDay)
    {
        $workingDay = 26;

        $payRoll = PayRoll::where('year', now()->year)
            ->where('month', now()->month)
            ->where('user_id', $user->id)
            ->first();

        if (!$payRoll) return;

        $dailyRate = $payRoll->basic_salary / $workingDay;

        $totalDeduction = $requestedDay * $dailyRate;

        $netSalary = $payRoll->basic_salary - $totalDeduction;

        $user->payrolls()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->update([
                'net_salary' => $netSalary,
                'unpaid_deduction' => $totalDeduction,
                'status' => RequestStatus::Approved,
                'finalized_at' => now(),
            ]);
    }

    public function reverseUnpaidLeaveDeduction(User $user): void
    {
        $payroll = PayRoll::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        if (! $payroll || $payroll->unpaid_deduction <= 0) {
            return;
        }

        $user->payrolls()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->update([
                'net_salary'       => $payroll->basic_salary - ($payroll->absent_deduction ?? 0),
                'unpaid_deduction' => 0,
            ]);
    }

    public function calculateSalaryForAbsentDays(User $user)
    {
        $workingDay = 26;

        $payRoll = PayRoll::where('year', now()->year)
            ->where('month', now()->month)
            ->where('user_id', $user->id)
            ->first();

        if (!$payRoll) return;

        if (!$payRoll->absent_days) return;

        $dailyRate = $payRoll->basic_salary / $workingDay;

        $totalDeduction = $payRoll->absent_days * $dailyRate;

        $netSalary = $payRoll->basic_salary - $totalDeduction;

        $user->payrolls()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->update([
                'net_salary'       => $netSalary,
                'unpaid_deduction' => $totalDeduction,
                'finalized_at'     => now(),
            ]);
    }
}
