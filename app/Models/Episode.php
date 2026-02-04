<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeView extends Model
{
    /**
     * Mass assignment
     */
    protected $fillable = [
        'user_id',
        'episode_id',
        'ip',
        'view_date',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'view_date' => 'date',
        'user_id'   => 'integer',
        'episode_id'=> 'integer',
    ];

    /* ================= Relations ================= */

    /**
     * 👤 Пользователь (может быть null для гостей)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 📺 Серия
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
