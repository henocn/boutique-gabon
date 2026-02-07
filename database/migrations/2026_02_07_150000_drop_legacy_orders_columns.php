<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            foreach (['client_nom', 'commentaire', 'produits', 'managers', 'statut'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $columns[] = $column;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'client_nom')) {
                $table->string('client_nom');
            }
            if (! Schema::hasColumn('orders', 'commentaire')) {
                $table->text('commentaire')->nullable();
            }
            if (! Schema::hasColumn('orders', 'produits')) {
                $table->longText('produits');
            }
            if (! Schema::hasColumn('orders', 'managers')) {
                $table->longText('managers')->nullable();
            }
            if (! Schema::hasColumn('orders', 'statut')) {
                $table->enum('statut', ['en attente', 'validée', 'annulée'])->default('en attente');
            }
        });
    }
};
