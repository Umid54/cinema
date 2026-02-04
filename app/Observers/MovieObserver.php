<?php

namespace App\Observers;

use App\Models\Movie;
use Illuminate\Support\Str;

class MovieObserver
{
    /**
     * Перед созданием фильма
     */
    public function creating(Movie $movie): void
    {
        if (empty($movie->slug)) {
            $movie->slug = $this->generateUniqueSlug($movie->title);
        }
    }

    /**
     * Перед обновлением фильма
     * (если изменили title и slug не задан вручную)
     */
    public function updating(Movie $movie): void
    {
        if (
            $movie->isDirty('title') &&
            empty($movie->slug)
        ) {
            $movie->slug = $this->generateUniqueSlug($movie->title, $movie->id);
        }
    }

    /**
     * Генерация уникального slug
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;

        while (
            Movie::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }
}
