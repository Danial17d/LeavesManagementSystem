<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $year = (int) now()->format('Y');

        $users = User::query()->select('id')->get();
        $leaveTypes = LeaveType::query()->select('id', 'days')->get();

        if ($users->isEmpty() || $leaveTypes->isEmpty()) {
            $this->command->warn('Leave balances were skipped because users or leave types are missing.');

            return;
        }

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                DB::table('leave_balances')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                    ],
                    [
                        'entitled_days' => $leaveType->days ?? 0,
                        'carried_days' => 0,
                        'used_days' => 0,
                    ]
                );
            }
        }

        $this->command->info('Leave balances seeded successfully.');
    }
}
