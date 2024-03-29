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

            $table->string('profession')->nullable()->default(null);
            $table->string('speciality')->nullable()->default(null);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('rfc')->nullable()->default(null);
            $table->string('dni')->nullable()->default(null);
            $table->string('fiscal_regime')->nullable()->default(null);

            $table->string('phone');
            $table->string('email')->unique();
            $table->string('sex');//se le agrego una migracion aparte la propiedad de que sea nulleable
            $table->string('date_of_birth')->nullable()->default(null);

            $table->string('country');
            $table->string('postal_code')->nullable()->default(null);
            $table->string('address')->nullable()->default(null);
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