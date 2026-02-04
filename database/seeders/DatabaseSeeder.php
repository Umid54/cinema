<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ⚠️ ВАЖНО
        |--------------------------------------------------------------------------
        | RoleSeeder, GenreSeeder, CountrySeeder ВРЕМЕННО ОТКЛЮЧЕНЫ,
        | т.к. соответствующие таблицы и модели ещё не созданы.
        | Это НОРМАЛЬНО на этапе разработки стриминга.
        */

        /* =======================
           Admin user (без ролей)
        ======================= */
        User::firstOrCreate(
            ['email' => 'admin@cinema.local'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
