<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Register your command here
    protected $commands = [
        Commands\CheckOverdueLeaves::class,
        Commands\RolloverMonthlyPayroll::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:check-overdue-leaves')->dailyAt('09:00');

        $schedule->command('app:rollover-monthly-payroll')->monthlyOn(1, '00:01');
    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }
}
