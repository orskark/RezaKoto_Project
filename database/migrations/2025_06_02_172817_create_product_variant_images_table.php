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
        Schema::create('product_variant_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('image_route', 255)->nullable();
            $table->boolean('is_main')->nullable()->default(false);
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_images');
    }
};
