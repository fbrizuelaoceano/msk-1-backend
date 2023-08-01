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
        Schema::create('products_crm', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');                             //Product_Code
            $table->string('cedente_code')->nullable()->default(null);  //C_digo_de_Curso_Cedente
            $table->string('platform')->nullable()->default(null);      //Plataforma_enrolamiento
            $table->string('platform_url')->nullable()->default(null); //URL_plataforma
            $table->string('entity_id')->nullable()->default(null);
            $table->timestamps();
            
            // quantity     //no es del producto
            // product_code
            // price        //es el del contrato 
            // discount,    //no es del producto

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_crm');
    }
};