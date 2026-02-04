<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Series extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'poster',
        'description',
        'year',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /** Scope: только опубликованные */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
	
	public function favorites(): MorphMany
{
    return $this->morphMany(Favorite::class, 'favoritable');
}
}
