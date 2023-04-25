<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenPassport extends Model
{
    use HasFactory;
    protected $table = 'tokens_passport';
    protected $fillable = [
        'name',
        'token',
        'hours_duration',
    ];
    public $timestamps = true;
}
