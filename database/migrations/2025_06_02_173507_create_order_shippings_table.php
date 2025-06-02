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
        Schema::create('order_shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('enterprise_id')->nullable();
            $table->unsignedBigInteger('shipping_status_id')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('enterprise_id')->references('id')->on('enterprises')->onDelete('set null');
            $table->foreign('shipping_status_id')->references('id')->on('shipping_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shippings');
    }
};
