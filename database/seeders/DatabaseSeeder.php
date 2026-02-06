<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@boutique.test',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@boutique.test',
            'role' => User::ROLE_MANAGER,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
