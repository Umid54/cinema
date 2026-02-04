<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    /**
     * Разрешённые для mass assignment поля
     */
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'is_premium',
    ];

    /**
     * Касты
     */
    protected $casts = [
        'is_active'  => 'boolean',
        'is_premium'=> 'boolean',
    ];

    /**
     * 📦 Сезоны сериала
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * 📺 Все серии сериала (через сезоны)
     */
    public function episodes()
    {
        return $this->hasManyThrough(
            Episode::class,
            Season::class
        );
    }
}
