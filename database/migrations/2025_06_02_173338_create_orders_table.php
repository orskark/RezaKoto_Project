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
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('order_status_id')->nullable();
            $table->unsignedBigInteger('payment_status_id')->nullable();
            $table->integer('total_value')->nullable()->default(0);
            $table->string('mailing_address', 255)->nullable();
            $table->integer('tax_value')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('order_status_id')->references('id')->on('order_statuses')->onDelete('set null');
            $table->foreign('payment_status_id')->references('id')->on('payment_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

