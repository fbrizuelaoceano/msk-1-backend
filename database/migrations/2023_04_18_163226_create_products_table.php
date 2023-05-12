<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                ->references('id')
                ->on('contracts')->onDelete('cascade')->onUpdate('cascade');

            $table->string('contract_entity_id');
            $table->string('entity_id_crm');
            $table->integer('quantity', false, true);
            $table->integer('product_code', false, true);
            $table->decimal('price', 10, 2);
            $table->decimal('discount');
            $table->string('title');



            $table->timestamps();
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