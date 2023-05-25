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

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contact_id')->references('id')->on('contacts')->onDelete('cascade')->onUpdate('cascade');

            $table->string('installments')->nullable()->default(null);
            $table->string('entity_id_crm');
            $table->string('so_crm');
            $table->string('status');
            $table->string('status_payment');
            $table->string('country');
            $table->string('currency');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};