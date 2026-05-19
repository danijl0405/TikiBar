<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            MenuItemSeeder::class,
            RestaurantTableSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@tikibar.es'],
            [
                'name'     => 'Tiki Admin',
                'phone'    => '+34 952 00 00 00',
                'password' => Hash::make('tikibar123'),
            ],
        );

        User::updateOrCreate(
            ['email' => 'cliente@tikibar.es'],
            [
                'name'     => 'Antonio Malagueño',
                'phone'    => '+34 600 11 22 33',
                'password' => Hash::make('password'),
            ],
        );
    }
}
