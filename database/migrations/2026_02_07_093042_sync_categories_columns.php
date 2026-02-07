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
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (! Schema::hasColumn('categories', 'image_path')) {
                $table->string('image_path')->nullable()->after('description');
            }
            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->index()->after('image_path');
            }
        });

        if (Schema::hasColumn('categories', 'nom') && Schema::hasColumn('categories', 'name')) {
            DB::table('categories')
                ->whereNull('name')
                ->update(['name' => DB::raw('nom')]);
        }

        if (Schema::hasColumn('categories', 'image') && Schema::hasColumn('categories', 'image_path')) {
            DB::table('categories')
                ->whereNull('image_path')
                ->update(['image_path' => DB::raw('image')]);
        }

        if (Schema::hasColumn('categories', 'statut') && Schema::hasColumn('categories', 'is_active')) {
            DB::statement("UPDATE categories SET is_active = CASE WHEN statut = 'actif' THEN 1 ELSE 0 END");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $columns = [];
            foreach (['name', 'description', 'image_path', 'is_active'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $columns[] = $column;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
