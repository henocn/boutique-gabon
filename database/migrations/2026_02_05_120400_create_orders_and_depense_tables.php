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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pack_id')->nullable()->constrained('product_packs')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->integer('unit_price');
            $table->integer('purchase_price');
            $table->integer('total_price');
            $table->string('client_name', 64);
            $table->foreignId('client_country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('client_phone', 64);
            $table->string('client_adress', 128);
            $table->string('client_note', 128)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('manager_note', 128)->nullable();
            $table->enum('status', ['new', 'deliver', 'processing', 'remind', 'unreachable', 'canceled'])->default('new');
            $table->timestamps();
        });

        Schema::create('depense', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['products', 'users', 'campagn', 'others']);
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('cout');
            $table->dateTime('date')->useCurrent();
            $table->string('description', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depense');
        Schema::dropIfExists('orders');
    }
};
