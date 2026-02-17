<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'days',
    ];

    public function leavesRequest(){
        return $this->hasMany(LeaveRequest::Class);
    }
    public function approvalRule(){
        return $this->hasOne(LeaveTypeApprovalRule::class, 'leave_type_id');
    }
}
