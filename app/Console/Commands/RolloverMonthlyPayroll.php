<?php

namespace App\Console\Commands;

use App\Models\PayRoll;
use App\Models\User;
use Illuminate\Console\Command;

class RolloverMonthlyPayroll extends Command
{
    protected $signature = 'app:rollover-monthly-payroll';

    protected $description = 'Create a fresh payroll record for every active user at the start of a new month, carrying forward the basic salary.';

    public function handle(): void
    {
        $month = now()->month;
        $year  = now()->year;

        $users = User::whereHas('payrolls')->get();

        $created = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $alreadyExists = PayRoll::where('user_id', $user->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($alreadyExists) {
                $skipped++;
                continue;
            }

            $previous = PayRoll::where('user_id', $user->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();

            if (! $previous) {
                $this->warn("No previous payroll for {$user->name} — skipping.");
                $skipped++;
                continue;
            }

            PayRoll::create([
                'user_id' => $user->id,
                'month' => $month,
                'year'  => $year,
                'basic_salary' => $previous->basic_salary,
                'net_salary' => $previous->basic_salary,
                'unpaid_deduction' => 0,
                'absent_days' => 0,
            ]);

            $created++;
        }

        $this->info("Payroll rollover complete — {$created} created, {$skipped} skipped.");
    }
}
