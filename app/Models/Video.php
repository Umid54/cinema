<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $fillable = [
        'movie_id',
        'episode_id',

        // тип видео
        'type',        // mp4 | hls | iframe

        // путь или html
        'path',        // путь к master.m3u8 или mp4
        'url',         // только для iframe (опционально)

        // тех. данные
        'duration',    // секунды
        'resolution',  // ['720p'] | ['360p','480p','720p','1080p']

        // очередь / ffmpeg
        'status',      // pending | processing | ready | error
        'error',
    ];

    protected $casts = [
        'movie_id'   => 'integer',
        'episode_id' => 'integer',
        'duration'   => 'integer',
        'resolution' => 'array',
    ];

    /* ================= Relations ================= */

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    /* ================= Accessors ================= */

    /**
     * HTML для плеера (MVP)
     */
    public function getPlayerHtmlAttribute(): string
    {
        // iframe (внешние плееры)
        if ($this->type === 'iframe' && $this->url) {
            return $this->url;
        }

        // MP4 (fallback / старые видео)
        if ($this->type === 'mp4') {
            return sprintf(
                '<video src="%s" controls playsinline width="100%%"></video>',
                asset($this->path)
            );
        }

        // HLS (master.m3u8)
        if ($this->type === 'hls') {
            return sprintf(
                '<video class="video-js vjs-default-skin" controls playsinline width="100%%" data-setup=\'{}\'>
                    <source src="%s" type="application/x-mpegURL">
                 </video>',
                asset($this->path)
            );
        }

        return '';
    }
}
