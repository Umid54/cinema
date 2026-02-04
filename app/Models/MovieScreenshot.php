<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieScreenshot extends Model
{
    protected $fillable = [
        'movie_id',
        'path',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
