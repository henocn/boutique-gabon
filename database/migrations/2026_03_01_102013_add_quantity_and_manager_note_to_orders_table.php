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
            if (! Schema::hasColumn('orders', 'quantity')) {
                $table->integer('quantity')->default(1)->after('product_id');
            }
            if (! Schema::hasColumn('orders', 'manager_note')) {
                $table->text('manager_note')->nullable()->after('client_comment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'manager_note']);
        });
    }
};
