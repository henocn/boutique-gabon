<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
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

        if (Category::query()->count() === 0) {
            $categories = collect([
                ['name' => 'Electronique', 'description' => 'Accessoires et gadgets'],
                ['name' => 'Maison', 'description' => 'Cuisine et rangement'],
                ['name' => 'Mode', 'description' => 'Style et accessoires'],
            ])->map(function (array $data) {
                return Category::create($data + ['is_active' => true]);
            });

            foreach ($categories as $index => $category) {
                Product::create([
                    'name' => 'Produit demo '.($index + 1),
                    'description_html' => 'Produit de demonstration',
                    'price_buy' => 5000,
                    'price_sell' => 12000 + ($index * 1500),
                    'price_shipping' => 1000,
                    'category_id' => $category->id,
                    'manager_id' => $manager->id,
                    'stock' => 10,
                    'status' => ProductStatus::Active,
                ]);
            }
        }
    }
}
