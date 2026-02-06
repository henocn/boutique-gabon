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
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 255);
            $table->integer('purchase_price');
            $table->integer('shipping_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('image', 128);
            $table->string('carousel1', 128)->nullable();
            $table->string('carousel2', 128)->nullable();
            $table->string('carousel3', 128)->nullable();
            $table->string('carousel4', 128)->nullable();
            $table->string('carousel5', 128)->nullable();
            $table->mediumText('description')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('product_caracteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title', 100);
            $table->string('image', 500)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('product_countries', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('selling_price');
            $table->primary(['product_id', 'country_id']);
        });

        Schema::create('product_managers', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->primary(['product_id', 'manager_id']);
        });

        Schema::create('product_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 255)->nullable();
            $table->string('image', 500);
            $table->integer('quantity')->nullable();
            $table->integer('price')->nullable();
        });

        Schema::create('product_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('video_url', 500)->nullable();
            $table->text('texte')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_video');
        Schema::dropIfExists('product_packs');
        Schema::dropIfExists('product_managers');
        Schema::dropIfExists('product_countries');
        Schema::dropIfExists('product_caracteristics');
        Schema::dropIfExists('products');
    }
};
