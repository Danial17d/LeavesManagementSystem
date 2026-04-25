<?php

namespace App\Http\Controllers;

use App\Enums\PermissionType;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize(PermissionType::CalendarView);

        $today = Carbon::now();
        $selectedMonth = max(1, min(12, (int) $request->input('month', $today->month)));
        $selectedYear = (int) $request->input('year', $today->year);
        $monthView = (new CalendarService($selectedYear, $selectedMonth))->buildMonthView();
        $calendarDate = $monthView['calendarDate'];

        $leaveRequests = CalendarService::scopedLeaveQuery()
            ->select('from', 'to')
            ->where('status', 'approved')
            ->get();

        $leaveEvent = [];

        foreach ($leaveRequests as $leaveRequest) {
            $startDate = Carbon::parse($leaveRequest->from)->startOfDay();
            $endDate = Carbon::parse($leaveRequest->to)->startOfDay();

            if ($endDate->lt($calendarDate->copy()->startOfMonth()) || $startDate->gt($calendarDate->copy()->endOfMonth())) {
                continue;
            }

            $startDay = $startDate->copy()->max($calendarDate->copy()->startOfMonth())->day;
            $endDay = $endDate->copy()->min($calendarDate->copy()->endOfMonth())->day;

            for ($day = $startDay; $day <= $endDay; $day++) {
                $leaveEvent[$day] = ($leaveEvent[$day] ?? 0) + 1;
            }
        }

        return view('calendar.index', [
            'weekDays' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'calendarDate' => $calendarDate,
            'previousMonth' => $monthView['previousMonth'],
            'nextMonth' => $monthView['nextMonth'],
            'today' => $today,
            'cells' => $monthView['cells'],
            'leaveEvent' => $leaveEvent,
        ]);
    }

    public function show(int $year, int $month, int $day): View
    {
        Gate::authorize(PermissionType::CalendarView);

        if (! checkdate($month, $day, $year)) {
            abort(404);
        }

        $selectedDate = Carbon::create($year, $month, $day)->startOfDay();

        $leaveRequests = CalendarService::scopedLeaveQuery()
            ->with(['user.structure', 'leaveType'])
            ->where('status', 'approved')
            ->whereDate('from', '<=', $selectedDate->toDateString())
            ->whereDate('to', '>=', $selectedDate->toDateString())
            ->orderBy('from')
            ->get();

        return view('calendar.show', [
            'selectedDate' => $selectedDate,
            'leaveRequests' => $leaveRequests,
        ]);
    }
}
