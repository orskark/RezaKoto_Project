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
        Schema::table('stocks', function (Blueprint $table) {
            // Nueva columna y relación con warehouses
            $table->unsignedInteger('warehouse_id')->nullable()->after('product_variant_id');

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                  ->onDelete('set null'); // O 'cascade', según tu lógica de negocio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Primero eliminar la clave foránea
            $table->dropForeign(['warehouse_id']);
            // Luego eliminar la columna
            $table->dropColumn('warehouse_id');
        });
    }
};
