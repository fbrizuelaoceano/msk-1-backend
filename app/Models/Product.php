<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'quantity',
        'price',
        'product_code',
        'discount',
        'contract_id',
        'contract_entity_id',
        'entity_id_crm'
    ];
    private static $formAttributes = [
        'id',
        'quantity',
        'price',
        'discount',
        'contract_id',
        'title'
    ];
    public $hidden = ['created_at', 'updated_at', 'id'];

    protected $table = 'products';
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }
}