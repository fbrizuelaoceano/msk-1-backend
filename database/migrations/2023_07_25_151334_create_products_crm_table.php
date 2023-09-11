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
            $table->string('product_code');
            $table->string('cedente_code')->nullable()->default(null);
            $table->string('platform')->nullable()->default(null);
            $table->string('platform_url')->nullable()->default(null);
            $table->string('entity_id');
            $table->timestamps();
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