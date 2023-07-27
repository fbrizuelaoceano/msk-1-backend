<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Speciality;

class Profession extends Model
{
    use HasFactory;
    protected $timestamp = true;
    protected $fillable = ['id','name'];
    protected $hidden = ['created_at','updated_at'];
    
    public function getName(){
        $name = $this->name;
        return $name;
    }
    public function specialities()
    {
        return $this->belongsToMany(Speciality::class, 'profession_speciality', 'profession_id', 'speciality_id');
    }
}
