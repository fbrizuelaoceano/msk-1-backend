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
        'Product_Code',
        'C_digo_de_Curso_Cedente',
        'Plataforma_enrolamiento',
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
        'Product_Code',
        'C_digo_de_Curso_Cedente',
        'Plataforma_enrolamiento',
    ];
    public function contact()
    {
        return $this->hasOne(Contact::class);
    }

    public static function mappingData(array $courseProgressInformation, int $contactId)
    {
        return [
            'entity_id_crm' => $courseProgressInformation['id'],
            'Fecha_finalizaci_n' => $courseProgressInformation['Fecha_finalizaci_n'],
            'Nombre_de_curso' => $courseProgressInformation['Nombre_de_curso']->name,
            'Estado_de_OV' => $courseProgressInformation['Estado_de_OV'] ?? null,
            'field_states' => $courseProgressInformation['$field_states'],
            'Created_Time' => $courseProgressInformation['Created_Time'],
            'Parent_Id' => $courseProgressInformation['Parent_Id']->id,
            'Nota' => $courseProgressInformation['Nota'],
            'Estado_cursada' => $courseProgressInformation['Estado_cursada'],
            'Avance' => $courseProgressInformation['Avance'],
            'Fecha_de_expiraci_n' => $courseProgressInformation['Fecha_de_expiraci_n'],
            'in_merge' => $courseProgressInformation['$in_merge'],
            'Fecha_de_compra' => $courseProgressInformation['Fecha_de_compra'],
            'Enrollamiento' => $courseProgressInformation['Enrollamiento'],
            'Fecha_de_ltima_sesi_n' => $courseProgressInformation['Fecha_de_ltima_sesi_n'],
            'contact_id' => $contactId,
            'Product_Code' => $courseProgressInformation['Product_Code'],
            'C_digo_de_Curso_Cedente' => $courseProgressInformation['C_digo_de_Curso_Cedente'],
            'Plataforma_enrolamiento' => $courseProgressInformation['Plataforma_enrolamiento'],
        ];

    }
}