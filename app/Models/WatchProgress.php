<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;
use App\Models\User;

class WatchProgress extends Model
{
    protected $table = 'watch_progress';

    protected $fillable = [
        'user_id',
        'series_id',
        'season',
        'episode',
        'position_seconds',
    ];

    protected $casts = [
        'position_seconds' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function series()
    {
        return $this->belongsTo(Movie::class, 'series_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
