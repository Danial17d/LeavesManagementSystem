<?php
//Annual leave   → carried_days matters   (roll over unused days)
//Sick leave     → carried_days = 0 always (reset every year)
//Marriage       → carried_days = 0 always (one time, use it or lose it)
//Maternity      → carried_days = 0 always (event-based, not yearly)
//Hajj           → carried_days = 0 always (once in a lifetime)
//Paternity      → carried_days = 0 always
//Bereavement    → carried_days = 0 always
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
            self::UnpaidLeave => null,
        };
    }

    public function payType(): string
    {
        return match ($this) {
            self::UnpaidLeave => 'unpaid',
            default => 'paid',
        };
    }

    public function deductsBalance(): bool
    {
        return match ($this) {
            self::UnpaidLeave => false,
            default => true,
        };
    }

}
