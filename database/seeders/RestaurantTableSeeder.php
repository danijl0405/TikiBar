<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['code' => 'T01', 'capacity' => 2, 'zone' => 'terraza'],
            ['code' => 'T02', 'capacity' => 2, 'zone' => 'terraza'],
            ['code' => 'T03', 'capacity' => 4, 'zone' => 'terraza'],
            ['code' => 'T04', 'capacity' => 4, 'zone' => 'terraza'],
            ['code' => 'T05', 'capacity' => 6, 'zone' => 'terraza'],
            ['code' => 'I01', 'capacity' => 2, 'zone' => 'interior'],
            ['code' => 'I02', 'capacity' => 4, 'zone' => 'interior'],
            ['code' => 'I03', 'capacity' => 4, 'zone' => 'interior'],
            ['code' => 'I04', 'capacity' => 8, 'zone' => 'interior'],
            ['code' => 'C01', 'capacity' => 4, 'zone' => 'chiringuito'],
            ['code' => 'C02', 'capacity' => 4, 'zone' => 'chiringuito'],
            ['code' => 'C03', 'capacity' => 6, 'zone' => 'chiringuito'],
            ['code' => 'C04', 'capacity' => 10, 'zone' => 'chiringuito'],
        ];

        foreach ($tables as $table) {
            RestaurantTable::updateOrCreate(['code' => $table['code']], $table + ['is_active' => true]);
        }
    }
}
