<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Person extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'photo',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class)
            ->withPivot(['role', 'character'])
            ->withTimestamps();
    }
}
