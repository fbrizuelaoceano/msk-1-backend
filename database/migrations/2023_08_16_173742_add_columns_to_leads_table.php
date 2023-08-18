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
            $table->string('career')->nullable()->default(null);
            $table->string('year')->nullable()->default(null);
            $table->string('country')->nullable()->default(null);
            $table->string('other_profession')->nullable()->default(null);
            $table->string('other_speciality')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('career');
            $table->dropColumn('year');
            $table->dropColumn('country');
            $table->dropColumn('other_profession');
            $table->dropColumn('other_speciality');
        });
    }
};
