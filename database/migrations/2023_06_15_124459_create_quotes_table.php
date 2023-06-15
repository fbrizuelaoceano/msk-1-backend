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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string("entity_id_crm")->nullable()->default(null);
            $table->decimal('Discount')->nullable()->default(null);
            $table->string('$currency_symbol')->nullable()->default(null);
            $table->string('$field_states')->nullable()->default(null);
            $table->integer('Seleccione_total_de_pagos_recurrentes')->nullable()->default(null);

            $table->string('M_todo_de_pago')->nullable()->default(null);// "M_todo_de_pago": "Mercado Pago",
            $table->string('Currency')->nullable()->default(null);// "Currency": "MXN",
            $table->string('otro_so')->nullable()->default(null);// "otro_so": "2000339000578191351",
            $table->string('Modo_de_pago')->nullable()->default(null);// "Modo_de_pago": "Cobro recurrente",
            $table->string('Quote_Stage')->nullable()->default(null);// "Quote_Stage": "Confirmado",
            $table->integer('Grand_Total')->nullable()->default(null);// "Grand_Total": 21000,
            $table->string('Modified_Time')->nullable()->default(null);// "Modified_Time": "2023-06-14T19:05:56-03:00",
            $table->integer('Sub_Total')->nullable()->default(null);// "Sub_Total": 21000,
            $table->string('Subject')->nullable()->default(null);// "Subject": "Presupuesto",
            $table->string('M_todo_de_pago')->nullable()->default(null);// "M_todo_de_pago": "Mercado Pago",
            $table->string('Quote_Number')->nullable()->default(null);// "Quote_Number": "5344455000005509042",

            $table->timestamps();
            
            // $objeto = '{
            //     "Owner": {
            //         "name": "Integraciones Administrador",
            //         "id": "5344455000001853001",
            //         "email": "integraciones@msklatam.com"
            //     },
            //     "Discount": 0,  //Migrado
            //     "$currency_symbol": "MX$",  //Migrado
            //     "$field_states": null,  //Migrado
            //     "Seleccione_total_de_pagos_recurrentes": "12",  //Migrado
            //     "$review_process": {
            //         "approve": false,
            //         "reject": false,
            //         "resubmit": false
            //     },
            //     "Tax": 0,
            //     "Pais_de_facturaci_n": "México",
            //     "subscription_id": "cfcfdbbc5ad34e38b82eb7520d86284e",
            //     "Cantidad_de_pagos_recurrentes_restantes": null,
            //     "Modified_By": {
            //         "name": "Roberto Flores",
            //         "id": "5344455000000369001",
            //         "email": "administrador@msklatam.com"
            //     },
            //     "$review": null,
            //     "$state": "save",
            //     "$converted": true,
            //     "$process_flow": false,
            //     "Exchange_Rate": 17.77,
            //     "Valid_Till": null,
            //     "Currency": "MXN",
            //     "LINK_de_COBRO": null,
            //     "otro_so": "2000339000578191351",
            //     "$locked_for_me": false,
            //     "id": "5344455000005509041",  //Migrado: entity_id_crm
            //     "Monto_de_cada_pago_restantes": null,
            //     "mp_subscription_id": null,
            //     "$approved": true,
            //     "Modo_de_pago": "Cobro recurrente",
            //     "Quote_Stage": "Confirmado",
            //     "Grand_Total": 21000,
            //     "$approval": {
            //         "delegate": false,
            //         "approve": false,
            //         "reject": false,
            //         "resubmit": false
            //     },
            //     "Modified_Time": "2023-06-14T19:05:56-03:00",
            //     "Monto_de_parcialidad": null,
            //     "Adjustment": 0,
            //     "Created_Time": "2023-06-14T17:30:20-03:00",
            //     "Terms_and_Conditions": null,
            //     "Sub_Total": 21000,
            //     "$editable": true,
            //     "Product_Details": [
            //         {
            //             "product": {
            //                 "Product_Code": "9005619",
            //                 "Currency": "USD",
            //                 "name": "Curso Acreditado en Ginecología",
            //                 "id": "5344455000002859194"
            //             },
            //             "quantity": 1,
            //             "Discount": 0,
            //             "total_after_discount": 21000,
            //             "net_total": 21000,
            //             "book": null,
            //             "Tax": 0,
            //             "list_price": 21000,
            //             "unit_price": 800,
            //             "quantity_in_stock": 0,
            //             "total": 21000,
            //             "id": "5344455000005509044",
            //             "product_description": "",
            //             "line_tax": []
            //         },
            //         {
            //             "product": {
            //                 "Product_Code": "9004755",
            //                 "Currency": "USD",
            //                 "name": "ELECTROCARDIOGRAFIA",
            //                 "id": "5344455000002859363"
            //             },
            //             "quantity": 1,
            //             "Discount": 0,
            //             "total_after_discount": 0,
            //             "net_total": 0,
            //             "book": null,
            //             "Tax": 0,
            //             "list_price": 0,
            //             "unit_price": 0,
            //             "quantity_in_stock": 0,
            //             "total": 0,
            //             "id": "5344455000005509046",
            //             "product_description": "",
            //             "line_tax": []
            //         }
            //     ],
            //     "Subject": "Presupuesto",
            //     "$orchestration": false,
            //     "Contact_Name": {
            //         "name": "Alicia Lizbeth Álvarez Olalde",
            //         "id": "5344455000005496024"
            //     },
            //     "M_todo_de_pago": "Mercado Pago",
            //     "Quote_Number": "5344455000005509042",
            //     "$in_merge": false,
            //     "Locked__s": false,
            //     "$line_tax": [],
            //     "Tag": [],
            //     "Created_By": {
            //         "name": "Integraciones Administrador",
            //         "id": "5344455000001853001",
            //         "email": "integraciones@msklatam.com"
            //     },
            //     "$approval_state": "approved"
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
