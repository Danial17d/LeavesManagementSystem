<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Nevadskiy\Tree\AsTree;

class Structure extends Model
{
    protected $fillable = ['name', 'type', 'path', 'parent_id', 'manager_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Structure::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'structure_id');
    }
}
