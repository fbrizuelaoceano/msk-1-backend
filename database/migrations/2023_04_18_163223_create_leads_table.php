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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('entity_id_crm')->nullable()->default(null);
            $table->string('lead_status')->nullable()->default(null);
            $table->string('source_lead')->nullable()->default(null);
            $table->string('lead_source')->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);

            $table->foreignId('profession')->nullable()->default(null)
                ->references('id')
                ->on('professions');

            $table->foreignId('speciality')->nullable()->default(null)
                ->references('id')
                ->on('specialities');

            $table->foreignId('method_contact')->nullable()->default(null)
                ->references('id')
                ->on('method_contacts');

            $table->foreignId('contact_id')->nullable()->default(null)
                ->references('id')
                ->on('contacts');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};