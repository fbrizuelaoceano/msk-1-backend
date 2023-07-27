<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Speciality;
use App\Models\Career;

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
    public function careers()
    {
        return $this->belongsToMany(Career::class, 'profession_career', 'profession_id', 'career_id');
    }
}
