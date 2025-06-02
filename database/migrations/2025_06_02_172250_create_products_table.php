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
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->unsignedBigInteger('enterprise_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->longText('description')->nullable();
            $table->integer('value')->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('enterprise_id')->references('id')->on('enterprises')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
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

