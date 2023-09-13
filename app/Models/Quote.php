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
        'id',
        // entity_id_crm
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

    public static function mappingData($data)
    {
        return [
            'entity_id_crm' => $data['id'],
            'Discount' => $data['Discount'],
            'currency_symbol' => $data['$currency_symbol'],
            'field_states' => $data['$field_states'],
            'Seleccione_total_de_pagos_recurrentes' => $data['Seleccione_total_de_pagos_recurrentes'],
            'M_todo_de_pago' => $data['M_todo_de_pago'],
            'Currency' => $data['Currency'],
            'otro_so' => $data['otro_so'],
            'Modo_de_pago' => $data['Modo_de_pago'],
            'Quote_Stage' => $data['Quote_Stage'],
            'Grand_Total' => $data['Grand_Total'],
            'Modified_Time' => $data['Modified_Time'],
            'Sub_Total' => $data['Sub_Total'],
            'Subject' => $data['Subject'],
            'Quote_Number' => $data['Quote_Number'],
        ];
    }

}
