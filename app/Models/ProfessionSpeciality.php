<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessionSpeciality extends Model
{
    use HasFactory;
    use HasFactory;
    
    protected $timestamp = true;
    protected $table = 'profession_speciality';
    protected $fillable = [
        'profession_id',
        'speciality_id',
    ];
    protected $hidden = ['created_at','updated_at'];

}
