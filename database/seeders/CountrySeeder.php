<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'США', 'code' => 'US'],
            ['name' => 'Россия', 'code' => 'RU'],
            ['name' => 'Великобритания', 'code' => 'GB'],
            ['name' => 'Франция', 'code' => 'FR'],
            ['name' => 'Канада', 'code' => 'CA'],
            ['name' => 'Германия', 'code' => 'DE'],
        ];

        foreach ($countries as $country) {
            \App\Models\Country::firstOrCreate(
                ['code' => $country['code']],
                ['name' => $country['name']]
            );
        }
    }
}
