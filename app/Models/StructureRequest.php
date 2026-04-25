<?php

namespace App\Models;

use App\Observers\StructureRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[ObservedBy([StructureRequestObserver::class])]
class StructureRequest extends Model
{
    protected $fillable = [
        'user_id',
        'structure_id',
        'status',
        'type',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approval()
    {
        return $this->morphMany(ApprovalRequest::class, 'approvable')->orderBy('step');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }
}
