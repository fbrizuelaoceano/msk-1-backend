<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profession;
use App\Models\Speciality;

class ProfessionSpeciality extends Model
{
    use HasFactory;
    
    protected $timestamp = true;
    protected $table = 'profession_speciality';
    protected $fillable = [
        'profession_id',
        'speciality_id',
    ];
    protected $hidden = ['created_at','updated_at'];

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }

}
