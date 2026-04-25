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

        $leaveBalance = $user->leaveBalances->firstWhere('leave_type_id', $leaveType->id);

        if (! $leaveType->name === 'unpaid:leave'){
            if (! $leaveBalance) {
                $fail("No leave balance found for this leave type.");
                return;
            }
            $balance = ($leaveBalance->entitled_day + $leaveBalance->carried_day) - $leaveBalance->used_day;

            if ((int) $value > (int) $balance ) {
                $fail("You don't have enough balance to make this request");
            }
        }
    }
}
