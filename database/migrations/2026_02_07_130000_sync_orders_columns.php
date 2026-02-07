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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'client_name')) {
                $table->string('client_name')->nullable()->after('id');
            }
            if (! Schema::hasColumn('orders', 'client_contact')) {
                $table->string('client_contact')->nullable()->after('client_name');
            }
            if (! Schema::hasColumn('orders', 'client_comment')) {
                $table->text('client_comment')->nullable()->after('client_contact');
            }
            if (! Schema::hasColumn('orders', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('client_comment');
            }
            if (! Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('new')->index()->after('product_id');
            }
        });

        if (Schema::hasColumn('orders', 'client_nom') && Schema::hasColumn('orders', 'client_name')) {
            DB::table('orders')
                ->whereNull('client_name')
                ->update(['client_name' => DB::raw('client_nom')]);
        }

        if (Schema::hasColumn('orders', 'commentaire') && Schema::hasColumn('orders', 'client_comment')) {
            DB::table('orders')
                ->whereNull('client_comment')
                ->update(['client_comment' => DB::raw('commentaire')]);
        }

        if (Schema::hasColumn('orders', 'statut') && Schema::hasColumn('orders', 'status')) {
            DB::statement("UPDATE orders SET status = CASE
                WHEN statut IN ('livre', 'livree', 'delivered') THEN 'delivered'
                WHEN statut IN ('valide', 'validated') THEN 'validated'
                WHEN statut IN ('annule', 'cancelled') THEN 'cancelled'
                WHEN statut IN ('traite', 'processed') THEN 'processed'
                WHEN statut IN ('injoignable', 'unreachable') THEN 'unreachable'
                WHEN statut IN ('nouveau', 'new') THEN 'new'
                ELSE 'new'
            END");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            foreach (['client_name', 'client_contact', 'client_comment', 'product_id', 'status'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $columns[] = $column;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
