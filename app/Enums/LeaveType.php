<?php

namespace App\Enums;
enum LeaveType: string
{
    case AnnualLeave = 'annual:leave';
    case SickLeave = 'sick:leave';
    case MarriageLeave = 'marriage:leave';
    case BereavementLeave = 'bereavement:leave';
    case MaternityLeave = 'maternity:leave';
    case PaternityLeave = 'paternity:leave';
    case HajjLeave = 'hajj:leave';
    case UnpaidLeave = 'unpaid:leave';
    case StudyLeave = 'study:leave';
    case ExamLeave = 'exam:leave';
    case OfficialHoliday = 'official:holiday';
    public function leaveDays(): ?int
    {
        return match ($this) {
            self::AnnualLeave => 21,
            self::SickLeave => 120,
            self::MarriageLeave => 5,
            self::PaternityLeave => 3,
            self::BereavementLeave => 5,
            self::HajjLeave => 15,
            self::MaternityLeave => 70,
            self::UnpaidLeave =>  21,
            self::StudyLeave => 21,
            self::ExamLeave => 21,
            self::OfficialHoliday => 21
        };
    }

}
