<?php

namespace Database\Seeders;

use App\Enums\LeaveType as LeaveTypeEnum;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding leave types...');

        foreach (LeaveTypeEnum::cases() as $leaveType) {
            $this->command->info('leave type: '.$leaveType->value);

            LeaveType::updateOrCreate([
                'name' => $leaveType->value,
            ], [
                'days' => $leaveType->leaveDays(),
            ]);
        }

        $this->command->info('Leave types seeded successfully.');
    }
}
