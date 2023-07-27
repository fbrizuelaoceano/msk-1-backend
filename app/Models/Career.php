<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profession;

class Career extends Model
{
    use HasFactory;
    protected $timestamp = true;
    protected $fillable = ['id','name'];
    protected $hidden = ['created_at','updated_at'];
    public function getName(){
        $name = $this->name;
        return $name;
    }
    public function professions()
    {
        return $this->belongsToMany(Profession::class, 'profession_career', 'career_id', 'profession_id');
    }
}