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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description_html')->nullable();
            $table->decimal('price_buy', 12, 2)->default(0);
            $table->decimal('price_sell', 12, 2);
            $table->decimal('price_shipping', 12, 2)->default(0);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users');
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
