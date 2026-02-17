<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveTypeApprovalRule extends Model
{
    protected $fillable = [
        'leave_type_id',
        'level',
    ];

    public function leaveType(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
