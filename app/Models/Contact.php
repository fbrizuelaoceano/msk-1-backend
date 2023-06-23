<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Lead};

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';

    protected $fillable = [
        'entity_id_crm',
        'Discount',
        '$currency_symbol',
        '$field_states',
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
        'M_todo_de_pago',
        'Quote_Number',
        'name'
    ];

    private static $formAttributes = [
        'entity_id_crm',
    ];

    public function user()
    {
        $user = $this->belongsTo(User::class, 'user_id');
        return $user;
    }
    public static function getFormAttributes()
    {
        return self::$formAttributes;
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}