<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{

    protected $fillable = [
        'leave_request_id',
        'approver_id',
        'step',
        'status',
        'acted_at',
        'note',
    ];
    protected $casts = [
        'acted_at' => 'datetime',
    ];
    public function leaveRequest(){
        return $this->belongsTo(LeaveRequest::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

}
