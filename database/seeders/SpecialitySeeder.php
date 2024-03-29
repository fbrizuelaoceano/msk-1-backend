<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitySeeder extends Seeder
{
    public $data = [
        ["name" => "Alergia e inmunología"],
        ["name" => "Anatomía patológica"],
        ["name" => "Coloproctología"],
        ["name" => "Flebología y linfología"],
        ["name" => "Hepatología"],
        ["name" => "Mastología"],
        ["name" => "Medicina de la industria farmaceútica"],
        ["name" => "Medicina del trabajo / ocupacional"],
        ["name" => "Medicina estética"],
        ["name" => "Medicina física y rehabilitación"],
        ["name" => "Medicina legal"],
        ["name" => "Medicina paliativa y dolor"],
        ["name" => "Medicina reproductiva y fertilidad"],
        ["name" => "Neumonología"],
        ["name" => "Reumatología"],
        ["name" => "Toxicología"],
        ["name" => "Trasplante"],
        ["name" => "Urología"],
        ["name" => "Enfermería familiar y comunitaria"],
        ["name" => "Enfermería en administración y gestión sanitaria"],
        ["name" => "Enfermería en análisis clínicos"],
        ["name" => "Enfermería en cardiología y UCO"],
        ["name" => "Enfermería en cuidados intensivos de adultos"],
        ["name" => "Enfermería en cuidados intensivos pediátricos y neonatales"],
        ["name" => "Enfermería en cuidados paliativos y dolor"],
        ["name" => "Enfermería en emergencias y atención primaria"],
        ["name" => "Enfermería en internación domiciliaria"],
        ["name" => "Enfermería en internación general"],
        ["name" => "Enfermería en investigación"],
        ["name" => "Enfermería en lactancia y puerperio"],
        ["name" => "Enfermería en reproducción asistida"],
        ["name" => "Enfermería en salud mental"],
        ["name" => "Enfermería en unidades de trasplantes"],
        ["name" => "Enfermería escolar"],
        ["name" => "Enfermería geriátrica y gerontológica"],
        ["name" => "Enfermería hematológica"],
        ["name" => "Enfermería nefrológica y diálisis"],
        ["name" => "Enfermería neonatal"],
        ["name" => "Enfermería obstétrica y ginecológica"],
        ["name" => "Enfermería oncológica"],
        ["name" => "Enfermería pediátrica"],
        ["name" => "Enfermería quirúrgica"],
        ["name" => "Enfermería radiológica"],
        ["name" => "Otras especialidades"],
        ["name" => "Producción de bioimágenes"],
        ["name" => "Bioquímica"],
        ["name" => "Psicología"],
        ["name" => "Farmacia"],
        ["name" => "Instrumentación quirúrgica"],
        ["name" => "Kinesiología y fisiatría"],
        ["name" => "Óptica"],
        ["name" => "Osteopatía"],
        ["name" => "Podología"],
        ["name" => "Terapia ocupacional"],
        ["name" => "Otra carrera o licenciatura"],
        ["name" => "Tecnicatura en laboratorio clínico"],
        ["name" => "Tecnicatura en radiología e imágenes diagnósticas"],
        ["name" => "Tecnicatura en atención de adicciones"],
        ["name" => "Tecnicatura en optometría"],
        ["name" => "Tecnicatura en hemoterapia e inmunohematología"],
        ["name" => "Tecnicatura en partería profesional con enfoque intercultural"],
        ["name" => "Tecnicatura en visita médica"],
        ["name" => "Tecnicatura en cuidados geriátricos"],
        ["name" => "Tecnicatura en tecnología en ciencias del esteticismo"],
        ["name" => "Tecnicatura en ciencia y tecnología de alimentos"],
        ["name" => "Tecnicatura en prácticas cardiológicas"],
        ["name" => "Tecnicatura en esterilización"],
        ["name" => "Tecnicatura en asistencia dental"],
        ["name" => "Tecnicatura en cosmetología"],
        ["name" => "Policía"],
        ["name" => "Bombero"],
        ["name" => "Guardavidas / Rescatista"],
        ["name" => "Auditoría y administración sanitaria"],
        ["name" => "Diabetes"],
        ["name" => "Generalista"],
        ["name" => "Medicina del deporte"],
        ["name" => "Medicina familiar y comunitaria"],
        ["name" => "Medicina intensiva"],
        ["name" => "Medicina interna / clínica"],
        ["name" => "Nutrición"],
        ["name" => "Traumatología y ortopedia"],
        ["name" => "Anestesiología"],
        ["name" => "Diagnóstico por Imágenes"],
        ["name" => "Cardiología"],
        ["name" => "Cirugía"],
        ["name" => "Cuidados críticos e intensivos"],
        ["name" => "Dermatología"],
        ["name" => "Emergentología"],
        ["name" => "Endocrinología"],
        ["name" => "Gastroenterología"],
        ["name" => "Generalista - Clínica - Medicina interna"],
        ["name" => "Geriatría y Gerontología"],
        ["name" => "Ginecología"],
        ["name" => "Hematología"],
        ["name" => "Infectología"],
        ["name" => "Internación domiciliaria y cuidados paliativos"],
        ["name" => "Nefrología"],
        ["name" => "Neonatología"],
        ["name" => "Neurología"],
        ["name" => "Nutrición y alimentación"],
        ["name" => "Obstetricia"],
        ["name" => "Obstetricia y Ginecología"],
        ["name" => "Odontología"],
        ["name" => "Oftalmología"],
        ["name" => "Oncología"],
        ["name" => "Ortopedia y Traumatología"],
        ["name" => "Otorrinolaringología"],
        ["name" => "Pediatría"],
        ["name" => "Psiquiatría"],
        ["name" => "Radiología"],
        ["name" => "Otra Especialidad"]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Desactivamos la revisión de claves foráneas
        DB::table('specialities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Reactivamos la revisión de claves foráneas
        foreach ($this->data as $d) {
            Speciality::create($d);
        }
    }
}