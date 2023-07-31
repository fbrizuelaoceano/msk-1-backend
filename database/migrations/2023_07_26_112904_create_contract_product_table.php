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
        Schema::create('contract_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('products_crm_id');
            // Agrega otros campos adicionales si es necesario

            // Definir las llaves foráneas para enlazar las tablas
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('products_crm_id')->references('id')->on('products_crm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product');
    }
};
