<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Боевик',
            'Комедия',
            'Драма',
            'Триллер',
            'Ужасы',
            'Фантастика',
            'Фэнтези',
            'Детектив',
            'Мелодрама',
            'Мультфильм',
        ];

        foreach ($genres as $name) {
            Genre::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }
    }
}
