<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    
    protected $fillable = [
        'entity_id_crm_contrato',
        'product_code',
        'user_id',
        'is_liked'
    ];
    
    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
