<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'structure_id',
        'salary',
    ];
    public function structure(): BelongsTo{
        return $this->belongsTo(Structure::class, 'structure_id');
    }
    public function user(){
        $this->belongsTo(User::class);
    }
}
