<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCRM extends Model
{
    use HasFactory;
    protected $table = 'products_crm';
    protected $fillable = ['product_code', 'cedente_code', 'platform', 'platform_url', 'entity_id'];
}