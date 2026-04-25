<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{

    protected $fillable = [
        'approvable_id',
        'approvable_type',
        'approver_id',
        'step',
        'status',
        'acted_at',
        'note',
    ];
    protected $casts = [
        'acted_at' => 'datetime',
    ];
    public function approvable()
    {
        return $this->morphTo();
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

}
