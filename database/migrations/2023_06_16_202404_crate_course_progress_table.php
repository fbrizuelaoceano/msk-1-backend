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
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();

            $table->string('Fecha_finalizaci_n')->nullable()->default(null);    // "Fecha_finalizaci_n": null,
            $table->string('Nombre_de_curso')->nullable()->default(null);       // "Nombre_de_curso": {
            $table->string('Estado_de_OV')->nullable()->default(null);          // "Estado_de_OV": null,
            $table->string('field_states')->nullable()->default(null);          // "$field_states": null,
            $table->string('Created_Time')->nullable()->default(null);          // "Created_Time": "2023-05-24T11:10:41-03:00",
            $table->string('Parent_Id')->nullable()->default(null);             // "Parent_Id": {
            $table->string('Nota')->nullable()->default(null);                  // "Nota": null,
            $table->string('Estado_cursada')->nullable()->default(null);        // "Estado_cursada": "Activo",
            $table->string('Avance')->nullable()->default(null);                // "Avance": "4.08",
            $table->string('Fecha_de_expiraci_n')->nullable()->default(null);   // "Fecha_de_expiraci_n": "2025-09-01T11:25:00-03:00",
            $table->string('in_merge')->nullable()->default(null);              // "$in_merge": false,
            $table->string('Fecha_de_compra')->nullable()->default(null);       // "Fecha_de_compra": "2023-05-24T11:10:00-03:00",
            $table->string('entity_id_crm')->nullable()->default(null);                    // "id": "5344455000004413001",
            $table->string('Enrollamiento')->nullable()->default(null);         // "Enrollamiento": "2023-05-24T11:10:00-03:00",
            $table->string('Fecha_de_ltima_sesi_n')->nullable()->default(null); // "Fecha_de_ltima_sesi_n": null
            
            $table->timestamps();
            // "Nombre_de_curso": {
            //     "name": "Medicina Interna",
            //     "id": "5344455000000968004"
            // },
            // "Parent_Id": {
            //     "name": "Eva Marmolejo",
            //     "id": "5344455000004398022"
            // },

            

            // $Formulario_de_cursada = '{
            //     "Fecha_finalizaci_n": null,
            //     "Nombre_de_curso": {
            //         "name": "Medicina Interna",
            //         "id": "5344455000000968004"
            //     },
            //     "Estado_de_OV": null,
            //     "$field_states": null,
            //     "Created_Time": "2023-05-24T11:10:41-03:00",
            //     "Parent_Id": {
            //         "name": "Eva Marmolejo",
            //         "id": "5344455000004398022"
            //     },
            //     "Nota": null,
            //     "Estado_cursada": "Activo",
            //     "Avance": "4.08",
            //     "Fecha_de_expiraci_n": "2025-09-01T11:25:00-03:00",
            //     "$in_merge": false,
            //     "Fecha_de_compra": "2023-05-24T11:10:00-03:00",
            //     "id": "5344455000004413001",
            //     "Enrollamiento": "2023-05-24T11:10:00-03:00",
            //     "Fecha_de_ltima_sesi_n": null
            // }';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
