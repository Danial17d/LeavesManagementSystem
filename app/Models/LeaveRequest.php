<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'from',
        'to',
        'status',
        'current_step',
        'reason',
        'attachment',
    ];
    public function leaveType(){
        return $this->belongsTo(LeaveType::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function approval(){
        return $this->hasMany(ApprovalRequest::class)->orderBy('step');
    }
    public function currentApproval()
    {
        return $this->approvals()
            ->where('status', 'pending')
            ->first();
    }
}
