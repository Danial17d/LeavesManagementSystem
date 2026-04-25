<?php

namespace App\Models;

use App\Enums\LeaveType as LeaveTypeEnum;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'days',
        'deducts_balance',
        'pay_type',
    ];

    protected $casts = [
        'deducts_balance' => 'boolean',
    ];

    public function leavesRequest(){
        return $this->hasMany(LeaveRequest::Class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function approvalRule(){
        return $this->hasOne(LeaveTypeApprovalRule::class, 'leave_type_id');
    }

    public function deductsFromBalance(): bool
    {
        if ($this->pay_type === 'unpaid') {
            return false;
        }

        return (bool) $this->deducts_balance;
    }
}
