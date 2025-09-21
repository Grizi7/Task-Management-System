<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRoleEnum;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed managers and users
        User::create([
            'name' => 'Manager One',
            'email' => 'manager@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::manager->value,
        ]);

        User::create([
            'name' => 'User One',
            'email' => 'user@softxpert.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::user->value,
        ]);
    }
}
