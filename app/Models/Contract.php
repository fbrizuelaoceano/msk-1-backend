<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'installments',
        'entity_id_crm',
        'so_crm',
        'status',
        'status_payment',
        'country',
        'currency',
    ];
    private static $formAttributes = [
        'id',
        'contact_id',
        'installments',
        'entity_id_crm',
        'so_crm',
        'status',
        'status_payment',
        'country',
        'currency',
    ];
    protected $table = 'contracts';
    public $hidden = ['created_at', 'updated_at'];

    public static function getFormAttributes()
    {
        return self::$formAttributes;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'contract_id', 'id');
    }
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
}