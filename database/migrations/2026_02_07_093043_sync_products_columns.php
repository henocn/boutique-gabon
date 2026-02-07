<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('products', 'description_html')) {
                $table->longText('description_html')->nullable()->after('name');
            }
            if (! Schema::hasColumn('products', 'price_buy')) {
                $table->decimal('price_buy', 12, 2)->default(0)->after('description_html');
            }
            if (! Schema::hasColumn('products', 'price_sell')) {
                $table->decimal('price_sell', 12, 2)->default(0)->after('price_buy');
            }
            if (! Schema::hasColumn('products', 'price_shipping')) {
                $table->decimal('price_shipping', 12, 2)->default(0)->after('price_sell');
            }
            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('price_shipping');
            }
            if (! Schema::hasColumn('products', 'status')) {
                $table->string('status')->default('active')->index()->after('stock');
            }
        });

        if (Schema::hasColumn('products', 'nom') && Schema::hasColumn('products', 'name')) {
            DB::table('products')
                ->whereNull('name')
                ->update(['name' => DB::raw('nom')]);
        }

        if (Schema::hasColumn('products', 'description') && Schema::hasColumn('products', 'description_html')) {
            DB::table('products')
                ->whereNull('description_html')
                ->update(['description_html' => DB::raw('description')]);
        }

        if (Schema::hasColumn('products', 'prix') && Schema::hasColumn('products', 'price_sell')) {
            DB::table('products')
                ->where('price_sell', 0)
                ->update(['price_sell' => DB::raw('prix')]);
        }

        if (Schema::hasColumn('products', 'categorie_id') && Schema::hasColumn('products', 'category_id')) {
            DB::table('products')
                ->whereNull('category_id')
                ->update(['category_id' => DB::raw('categorie_id')]);
        }

        if (Schema::hasColumn('products', 'statut') && Schema::hasColumn('products', 'status')) {
            DB::statement("UPDATE products SET status = CASE WHEN statut = 'actif' THEN 'active' ELSE 'inactive' END");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [];
            foreach (['name', 'description_html', 'price_buy', 'price_sell', 'price_shipping', 'category_id', 'status'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $columns[] = $column;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
