<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoToken extends Model
{
    protected $table = 'zoho_token';
    protected $fillable = [
        'access_token',
        'client_id',
        'expires',
    ];
}