<?php

namespace App\Console\Commands;

use App\Enums\RequestStatus;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-overdue-leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark absent days and recalculate payroll for overdue approved leaves.';

    public function handle(): void
    {
        $overdueLeaves = LeaveRequest::query()
            ->with('user')
            ->where('status', RequestStatus::Approved->value)
            ->whereDate('from', '<=', now()->toDateString())
            ->whereDate('to', '<', now()->toDateString())
            ->whereHas('user', fn ($q) => $q->where('is_return', false))
            ->get();

        $this->info("Found {$overdueLeaves->count()} overdue leave(s).");

        foreach ($overdueLeaves as $leaveRequest) {
            $user = $leaveRequest->user;

            $payroll = $user->payrolls()
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->first();

            if (! $payroll) {
                $this->warn("No payroll for {$user->name} this month — skipping.");
                continue;
            }

            $absentDays = Carbon::parse($leaveRequest->to)->diffInDays(now());

            $payroll->update(['absent_days' =>floor($absentDays)]);

            $this->info("Marked {$user->name}: {$absentDays} absent day(s).");
        }
    }
}
