<?php

namespace Database\Seeders;

use App\Enums\Country;
use App\Enums\ProductStatus;
use App\Models\Product;
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
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@boutique.test'],
            [
                'name' => 'Admin',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        $manager = User::query()->firstOrCreate(
            ['email' => 'manager@boutique.test'],
            [
                'name' => 'Manager',
                'role' => User::ROLE_MANAGER,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );

        // Create sample products if none exist
        if (Product::query()->count() === 0) {
            $countries = [
                [Country::Togo->value],
                [Country::Congo->value],
                [Country::CentralAfrica->value],
                [Country::Togo->value, Country::Congo->value],
                [Country::Togo->value, Country::Congo->value, Country::CentralAfrica->value],
            ];

            foreach (range(1, 5) as $index) {
                Product::create([
                    'name' => 'Produit demo ' . $index,
                    'description_html' => 'Produit de demonstration',
                    'price_buy' => 5000,
                    'price_sell' => 12000 + ($index * 1500),
                    'price_shipping' => 1000,
                    'manager_id' => $manager->id,
                    'stock' => 10,
                    'status' => ProductStatus::Active,
                    'countries' => $countries[$index - 1],
                ]);
            }
        }
    }
}
