<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Nevadskiy\Tree\AsTree;

class Structure extends Model
{
    use AsTree;

    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'path',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Structure::class, 'parent_id');
    }
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'structure_id');
    }
}
