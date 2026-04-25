<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CalendarService
{
    protected int $totalDayOfMonth;
    protected int $startDayOfMonth;
    protected int $endDayOfMonth;
    protected string $monthName;
    public function __construct(public int $currentYear, public int $currentMonth)
    {
        $currentDate = Carbon::create(
            $this->currentYear,
            $this->currentMonth,
            1
        );
        $this->totalDayOfMonth = $currentDate->daysInMonth();
        $this->startDayOfMonth = $currentDate->startOfMonth()->dayOfWeek;
        $this->endDayOfMonth = $currentDate->endofmonth()->dayOfWeek;
        $this->monthName = $currentDate->monthName;
    }
    public function today(){
        return [
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
        ];
    }
    public function previous()
    {
        $this->currentMonth--;
        if ($this->currentMonth < 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        }
        return [
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
        ];
    }
    public function next()
    {
        $this->currentMonth++;
        if ($this->currentMonth > 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        }
        return [
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
        ];
    }
    public function getPreviousMonthDay(): int
    {
        $currentDate = Carbon::create(
            $this->currentYear,
            $this->currentMonth - 1 ,
            1
        );
        return $currentDate->daysInMonth();

    }
    public function getTotalDayOfMonth(): int
    {
        return $this->totalDayOfMonth;
    }
    public function getStartDayOfMonth(): int
    {
        return $this->startDayOfMonth;
    }
    public function getEndDayOfMonth(): int
    {
        return $this->endDayOfMonth;
    }
    public function getMonthName(): string
    {
        return $this->monthName;
    }

    public function buildMonthView(): array
    {
        $calendarDate = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $cells = array_fill(0, $this->getStartDayOfMonth(), null);

        for ($day = 1; $day <= $this->getTotalDayOfMonth(); $day++) {
            $cells[] = $day;
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        return [
            'calendarDate' => $calendarDate,
            'cells' => $cells,
            'previousMonth' => Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth(),
            'nextMonth' => Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth(),
        ];
    }
    public static function scopedLeaveQuery(): Builder
    {
        $user = auth()->user();

        $query = LeaveRequest::query();

        if ($user->hasRole([UserRole::SuperAdmin->value, UserRole::Admin->value])) {
            return $query;
        }

        $managedStructure = $user->managedStructure;

        if ($managedStructure) {
            $structureIds = $managedStructure->descendantsAndSelf()->pluck('id');
            return $query->whereHas('user', fn ($q) => $q->whereIn('structure_id', $structureIds));
        }

        return $query->whereHas('user', fn ($q) => $q->where('structure_id', $user->structure_id));
    }
}
