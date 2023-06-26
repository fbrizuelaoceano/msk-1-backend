<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';
    protected $fillable = [
        'Fecha_finalizaci_n',
        'Nombre_de_curso',
        'Estado_de_OV',
        'field_states',
        'Created_Time',
        'Parent_Id',
        'Nota',
        'Estado_cursada',
        'Avance',
        'Fecha_de_expiraci_n',
        'in_merge',
        'Fecha_de_compra',
        'entity_id_crm',
        'Enrollamiento',
        'Fecha_de_ltima_sesi_n',
        'contact_id',
        'course_progress',
    ];
    public static $formAttributes = [
        'Fecha_finalizaci_n',
        'Nombre_de_curso',
        'Estado_de_OV',
        'field_states',
        'Created_Time',
        'Parent_Id',
        'Nota',
        'Estado_cursada',
        'Avance',
        'Fecha_de_expiraci_n',
        'in_merge',
        'Fecha_de_compra',
        'Enrollamiento',
        'Fecha_de_ltima_sesi_n',
        'course_progress',
    ];
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
}