<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Register your command here
    protected $commands = [
        Commands\CheckOverdueLeaves::class,
    ];

    // Schedule it to run every morning at 9:00
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:check-overdue-leaves')
            ->everyMinute();


    }

    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }
}
