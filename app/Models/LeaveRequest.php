<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Observers\LeaveRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([LeaveRequestObserver::class])]
class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'from',
        'to',
        'requested_days',
        'status',
        'current_step',
        'reason',
        'attachment',
    ];

    protected $casts = [
        'requested_days' => 'integer',
    ];

    public function leaveType(){
        return $this->belongsTo(LeaveType::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function approval(){
        return $this->morphMany(ApprovalRequest::class, 'approvable')->orderBy('step');
    }

    public function deductsFromBalance(): bool
    {
        return $this->leaveType->deductsFromBalance();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [
                RequestStatus::Submitted->value,
                RequestStatus::Pending->value,
                RequestStatus::Approved->value,
            ])
            ->whereDate('to', '>=', now()->toDateString());
    }
}
