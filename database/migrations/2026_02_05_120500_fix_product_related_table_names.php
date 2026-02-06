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
        if (Schema::hasTable('product_caracteristics') && !Schema::hasTable('product_characteristics')) {
            Schema::rename('product_caracteristics', 'product_characteristics');
        }

        if (Schema::hasTable('product_video') && !Schema::hasTable('product_videos')) {
            Schema::rename('product_video', 'product_videos');
        }

        if (!Schema::hasTable('product_characteristics')) {
            Schema::create('product_characteristics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('title', 100);
                $table->string('image', 500)->nullable();
                $table->text('description')->nullable();
            });
        }

        if (!Schema::hasTable('product_videos')) {
            Schema::create('product_videos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
                $table->string('video_url', 500)->nullable();
                $table->text('texte')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_videos') && !Schema::hasTable('product_video')) {
            Schema::rename('product_videos', 'product_video');
        }

        if (Schema::hasTable('product_characteristics') && !Schema::hasTable('product_caracteristics')) {
            Schema::rename('product_characteristics', 'product_caracteristics');
        }
    }
};
