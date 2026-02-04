<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movie extends Model
{
    /* ================= Status constants ================= */

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY      = 'ready';
    public const STATUS_ERROR      = 'error';

    protected $fillable = [
        'title',
        'original_title',
        'slug',
        'description',
        'year',
        'duration',
        'rating',
        'poster_path',
        'is_series',
        'status',
        'hls_path',
    ];

    protected $casts = [
        'is_series'  => 'boolean',
        'rating'     => 'float',
        'year'       => 'integer',
        'duration'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 🔥 Виртуальные поля для UI
     */
    protected $appends = [
        'poster_url',
        'rating_kp',
        'rating_imdb',
        'rating_color',
        'duration_human',
    ];

    /* ================= Relations ================= */

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)
            ->withPivot(['role', 'character'])
            ->withTimestamps();
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(MovieScreenshot::class)
            ->orderBy('position');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * ▶ Прогресс просмотра сериала (для Resume)
     * Только связь, фильтрация по user_id — в контроллере
     */
    public function watchProgress(): HasOne
    {
        return $this->hasOne(WatchProgress::class, 'series_id');
    }

    /* ================= Scopes ================= */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_READY);
    }

    public function scopeMovies(Builder $query): Builder
    {
        return $query->where('is_series', false);
    }

    public function scopeSeries(Builder $query): Builder
    {
        return $query->where('is_series', true);
    }

    /* ================= Accessors ================= */

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster_path
            ? asset('storage/' . $this->poster_path)
            : null;
    }

    public function getDurationHumanAttribute(): ?string
    {
        if (!$this->duration) {
            return null;
        }

        return gmdate(
            $this->duration >= 3600 ? 'H:i:s' : 'i:s',
            $this->duration
        );
    }

    public function getRatingKpAttribute(): ?float
    {
        return $this->rating !== null
            ? round($this->rating, 1)
            : null;
    }

    public function getRatingImdbAttribute(): ?float
    {
        return $this->rating !== null
            ? round($this->rating, 1)
            : null;
    }

    public function getRatingColorAttribute(): string
    {
        if ($this->rating === null) {
            return 'text-slate-400';
        }

        return match (true) {
            $this->rating >= 8.0 => 'text-emerald-400',
            $this->rating >= 7.0 => 'text-amber-400',
            default              => 'text-slate-300',
        };
    }
}
