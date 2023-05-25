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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('entity_id_crm');

            $table->string('name');
            $table->string('last_name');

            $table->foreignId('profession')->references('id')->on('professions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('speciality')->references('id')->on('specialities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('rfc')->nullable()->default(null);
            $table->string('dni')->nullable()->default(null);
            $table->string('fiscal_regime')->nullable()->default(null);

            $table->string('phone');
            $table->string('email')->unique();
            $table->string('sex');
            $table->string('date_of_birth');

            $table->string('country');
            $table->string('postal_code');
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};