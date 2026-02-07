<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::statement('ALTER TABLE products MODIFY price_buy INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY price_sell INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY price_shipping INT UNSIGNED NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::statement('ALTER TABLE products MODIFY price_buy DECIMAL(12,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY price_sell DECIMAL(12,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY price_shipping DECIMAL(12,2) NOT NULL DEFAULT 0');
    }
};
