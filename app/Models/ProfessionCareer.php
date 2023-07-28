<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profession;
use App\Models\Career;
class ProfessionCareer extends Model
{
    use HasFactory;
    protected $timestamp = true;
    protected $table = 'profession_career';
    protected $fillable = [
        'profession_id',
        'career_id',
    ];
    protected $hidden = ['created_at','updated_at'];

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    public function career()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }
}
