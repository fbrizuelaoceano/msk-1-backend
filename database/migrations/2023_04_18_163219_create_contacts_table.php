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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);

            $table->string('Profes_n')->nullable()->default(null);
            $table->string('Especialidad')->nullable()->default(null);
            $table->string('RFC')->nullable()->default(null);
            $table->string('R_gimen_fiscal')->nullable()->default(null);
            
            $table->string('phone')->nullable()->default(null);
            $table->string('last_name')->nullable(false);
            $table->string('email')->unique();
            $table->string('entity_id_crm')->nullable()->default(null);
            $table->string('dni')->nullable()->default(null);
            $table->string('sex')->nullable()->default(null);
            $table->string('date_of_birth')->nullable()->default(null);
            $table->string('registration_number')->nullable()->default(null);
            $table->string('area_of_work')->nullable()->default(null);
            $table->string('training_interest')->nullable()->default(null);
            $table->string('type_of_address')->nullable()->default(null);
            $table->string('country')->nullable()->default(null);
            $table->string('postal_code')->nullable()->default(null);
            $table->string('street')->nullable()->default(null);
            $table->string('locality')->nullable()->default(null);
            $table->string('province_state')->nullable()->default(null);
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
