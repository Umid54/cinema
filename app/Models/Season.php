<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    /**
     * Mass assignment
     */
    protected $fillable = [
        'series_id',
        'number',
        'is_active',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'number'    => 'integer',
        'is_active'=> 'boolean',
    ];

    /* ================= Relations ================= */

    /**
     * 🎬 Сериал
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * 📺 Серии сезона
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('number');
    }

    /* ================= Accessors ================= */

    /**
     * Человекочитаемое название сезона
     */
    public function getDisplayTitleAttribute(): string
    {
        return 'Сезон ' . $this->number;
    }
}
