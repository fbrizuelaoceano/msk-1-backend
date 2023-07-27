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
        Schema::create('profession_career', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profession_id');
            $table->unsignedBigInteger('career_id');
            $table->timestamps();
    
            $table->foreign('profession_id')->references('id')->on('professions');
            $table->foreign('career_id')->references('id')->on('careers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_career');
    }
};