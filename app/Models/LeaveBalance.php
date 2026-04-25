<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'entitled_days',
        'carried_days',
        'used_days',
    ];

    protected $casts = [
        'year' => 'integer',
        'entitled_days' => 'integer',
        'carried_days' => 'integer',
        'used_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
