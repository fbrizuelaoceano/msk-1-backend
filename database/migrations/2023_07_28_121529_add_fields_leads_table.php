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
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('year')->nullable()->default(null);
            $table->integer('career')->nullable()->default(null);
                     
            $table->foreignId('career_id')->nullable()->default(null)
                ->references('id')
                ->on('careers');
        });
    }

    /**
     * Reverse the migrations.0¡
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('year');
            $table->dropColumn('career');

            $table->dropForeign(['career_id']);
        });
    }
};