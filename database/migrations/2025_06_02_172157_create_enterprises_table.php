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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('status_id')->nullable()->default(1);
            $table->unsignedBigInteger('enterprise_type_id')->nullable();
            $table->string('name', 255);
            $table->string('NIT', 255);
            $table->string('phone_number', 255);
            $table->string('address', 255);
            $table->string('email', 255);
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('set null');
            $table->foreign('enterprise_type_id')->references('id')->on('enterprise_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
