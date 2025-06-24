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
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('status_id')->nullable()->default(1);
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('reserved_quantity')->nullable()->default(0);
            $table->integer('minimum_quantity')->nullable()->default(0);
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
        Schema::dropIfExists('stocks');
    }
};
