<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->uuid = (string) Str::uuid();
    }

    public function created(User $user): void
    {
        $year = now()->year;
        $leaveTypes = DB::table('leave_types')->get();

        foreach ($leaveTypes as $leaveType) {
            DB::table('leave_balances')->insert([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
                'entitled_days' => $leaveType->days ?? 0,
                'carried_days' => 0,
                'used_days' => 0,
            ]);
        }

    }
}
