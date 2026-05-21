<?php

namespace App\Rules;

use App\Enums\LeaveType as LeaveTypeEnum;
use App\Models\LeaveType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsHasBalance implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $leaveType = LeaveType::where("name" , request()->input('leave_type'))->get()->first();

        if (!$leaveType) {
            $fail("Invalid leave type.");
            return;
        }

        if ($leaveType->name === 'unpaid:leave') {
            return;
        }

        $leaveBalance = $user->leaveBalances()
            ->where('leave_type_id', $leaveType->id)
            ->where('year', now()->year)
            ->first();

        if (! $leaveBalance) {
            $fail("No leave balance found for this leave type.");
            return;
        }

        $balance = ($leaveBalance->entitled_days + $leaveBalance->carried_days) - $leaveBalance->used_days;

        if ((int) $value > $balance) {
            $fail("You don't have enough balance. Available: {$balance} days.");
        }
    }
}
