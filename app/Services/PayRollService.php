<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\LeaveRequest;
use App\Models\PayRoll;
use App\Models\User;
use Carbon\Carbon;

class PayRollService
{
    public function calculateSalary(User $user,int $requestedDay){

        $workingDay  = 26;

        $payRoll = PayRoll::where('year', Carbon::now()->year)
        ->where('month', Carbon::now()->month)
        ->where('user_id' , $user->id)
        ->first();

        $leaveRequest = LeaveRequest::with(['leaveType'])
            ->where('user_id' , $user->id)
            ->where('status', RequestStatus::Approved)
            ->whereHas('leaveType', function($query){
                $query->where('name','unpaid');
            })->get();
        if($payRoll){


            $dailyRate = $payRoll->basic_salary / $workingDay;

            $totalDeduction = $requestedDay * $dailyRate;

            $netSalary = $payRoll->basic_salary  - $totalDeduction;

            $user->payrolls()->update([
                'month' => now()->month,
                'year' => now()->year,
                'basic_salary' => $payRoll->basic_salary ,
                'net_salary' => $netSalary,
                'unpaid_deduction' => $totalDeduction,
                'status' => RequestStatus::Approved,
                'finalized_at' => now(),
            ]);
        }

    }
}
