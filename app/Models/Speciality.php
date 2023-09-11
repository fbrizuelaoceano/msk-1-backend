<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profession;

class Speciality extends Model
{
    use HasFactory;
    protected $timestamp = true;
    protected $fillable = ['name'];
    protected $hidden = ['created_at','updated_at'];

    public function professions()
    {
        return $this->belongsToMany(Profession::class, 'profession_speciality', 'speciality_id', 'profession_id');
    }
    
}
