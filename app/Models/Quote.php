<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;
    protected $table = 'quotes';
    protected $fillable = [
        'entity_id_crm',
        'Discount',
        'currency_symbol',
        'field_states',
        'Seleccione_total_de_pagos_recurrentes',
        'M_todo_de_pago',
        'Currency',
        'otro_so',
        'Modo_de_pago',
        'Quote_Stage',
        'Grand_Total',
        'Modified_Time',
        'Sub_Total',
        'Subject',
        'Quote_Number',
    ];

    private static $formAttributes = [
        'id',// entity_id_crm
        'Discount',
        'currency_symbol',
        'field_states',
        'Seleccione_total_de_pagos_recurrentes',
        'M_todo_de_pago',
        'Currency',
        'otro_so',
        'Modo_de_pago',
        'Quote_Stage',
        'Grand_Total',
        'Modified_Time',
        'Sub_Total',
        'Subject',
        'Quote_Number',
    ];

}
