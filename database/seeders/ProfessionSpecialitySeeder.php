<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProfessionSpeciality;
use App\Models\Profession;
use App\Models\Speciality;



class ProfessionSpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationshipsArray = [
            ['profession' => 'Personal médico', 'speciality'=>'Alergia e inmunología'],
            ['profession' => 'Personal médico', 'speciality'=>'Anatomía patológica'],
            ['profession' => 'Personal médico', 'speciality'=>'Anestesiología'],
            ['profession' => 'Personal médico', 'speciality'=>'Auditoría y administración sanitaria'],
            ['profession' => 'Personal médico', 'speciality'=>'Cardiología'],
            ['profession' => 'Personal médico', 'speciality'=>'Cirugía'],
            ['profession' => 'Personal médico', 'speciality'=>'Coloproctología'],
            ['profession' => 'Personal médico', 'speciality'=>'Dermatología'],
            ['profession' => 'Personal médico', 'speciality'=>'Diabetes'],
            ['profession' => 'Personal médico', 'speciality'=>'Diagnóstico por imágenes'],
            ['profession' => 'Personal médico', 'speciality'=>'Emergentología'],
            ['profession' => 'Personal médico', 'speciality'=>'Endocrinología'],
            ['profession' => 'Personal médico', 'speciality'=>'Flebología y linfología'],
            ['profession' => 'Personal médico', 'speciality'=>'Gastroenterología'],
            ['profession' => 'Personal médico', 'speciality'=>'Generalista'],
            ['profession' => 'Personal médico', 'speciality'=>'Geriatría y gerontología'],
            ['profession' => 'Personal médico', 'speciality'=>'Ginecología'],
            ['profession' => 'Personal médico', 'speciality'=>'Hematología'],
            ['profession' => 'Personal médico', 'speciality'=>'Hepatología'],
            ['profession' => 'Personal médico', 'speciality'=>'Infectología'],
            ['profession' => 'Personal médico', 'speciality'=>'Mastología'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina de la industria farmaceútica'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina del deporte'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina del trabajo / ocupacional'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina estética'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina familiar y comunitaria'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina física y rehabilitación'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina intensiva'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina interna / clínica'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina legal'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina paliativa y dolor'],
            ['profession' => 'Personal médico', 'speciality'=>'Medicina reproductiva y fertilidad'],
            ['profession' => 'Personal médico', 'speciality'=>'Nefrología'],
            ['profession' => 'Personal médico', 'speciality'=>'Neonatología'],
            ['profession' => 'Personal médico', 'speciality'=>'Neumonología'],
            ['profession' => 'Personal médico', 'speciality'=>'Neurología'],
            ['profession' => 'Personal médico', 'speciality'=>'Nutrición'],
            ['profession' => 'Personal médico', 'speciality'=>'Obstetricia'],
            ['profession' => 'Personal médico', 'speciality'=>'Obstetricia y Ginecología'],
            ['profession' => 'Personal médico', 'speciality'=>'Oftalmología'],
            ['profession' => 'Personal médico', 'speciality'=>'Oncología'],
            ['profession' => 'Personal médico', 'speciality'=>'Otorrinolaringología'],
            ['profession' => 'Personal médico', 'speciality'=>'Pediatría'],
            ['profession' => 'Personal médico', 'speciality'=>'Psiquiatría'],
            ['profession' => 'Personal médico', 'speciality'=>'Reumatología'],
            ['profession' => 'Personal médico', 'speciality'=>'Toxicología'],
            ['profession' => 'Personal médico', 'speciality'=>'Trasplante'],
            ['profession' => 'Personal médico', 'speciality'=>'Traumatología y ortopedia'],
            ['profession' => 'Personal médico', 'speciality'=>'Urología'],
//
            ['profession' => 'Residente', 'speciality'=> 'Alergia e inmunología'],
            ['profession' => 'Residente', 'speciality'=> 'Anatomía patológica'],
            ['profession' => 'Residente', 'speciality'=> 'Anestesiología'],
            ['profession' => 'Residente', 'speciality'=> 'Auditoría y administración sanitaria'],
            ['profession' => 'Residente', 'speciality'=> 'Cardiología'],
            ['profession' => 'Residente', 'speciality'=> 'Cirugía'],
            ['profession' => 'Residente', 'speciality'=> 'Coloproctología'],
            ['profession' => 'Residente', 'speciality'=> 'Dermatología'],
            ['profession' => 'Residente', 'speciality'=> 'Diabetes'],
            ['profession' => 'Residente', 'speciality'=> 'Diagnóstico por imágenes'],
            ['profession' => 'Residente', 'speciality'=> 'Emergentología'],
            ['profession' => 'Residente', 'speciality'=> 'Endocrinología'],
            ['profession' => 'Residente', 'speciality'=> 'Flebología y linfología'],
            ['profession' => 'Residente', 'speciality'=> 'Gastroenterología'],
            ['profession' => 'Residente', 'speciality'=> 'Generalista'],
            ['profession' => 'Residente', 'speciality'=> 'Geriatría y gerontología'],
            ['profession' => 'Residente', 'speciality'=> 'Ginecología'],
            ['profession' => 'Residente', 'speciality'=> 'Hematología'],
            ['profession' => 'Residente', 'speciality'=> 'Hepatología'],
            ['profession' => 'Residente', 'speciality'=> 'Infectología'],
            ['profession' => 'Residente', 'speciality'=> 'Mastología'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina de la industria farmaceútica'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina del deporte'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina del trabajo / ocupacional'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina estética'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina familiar y comunitaria'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina física y rehabilitación'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina intensiva'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina interna / clínica'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina legal'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina paliativa y dolor'],
            ['profession' => 'Residente', 'speciality'=> 'Medicina reproductiva y fertilidad'],
            ['profession' => 'Residente', 'speciality'=> 'Nefrología'],
            ['profession' => 'Residente', 'speciality'=> 'Neonatología'],
            ['profession' => 'Residente', 'speciality'=> 'Neumonología'],
            ['profession' => 'Residente', 'speciality'=> 'Neurología'],
            ['profession' => 'Residente', 'speciality'=> 'Nutrición'],
            ['profession' => 'Residente', 'speciality'=> 'Obstetricia'],
            ['profession' => 'Residente', 'speciality'=> 'Obstetricia y Ginecología'],
            ['profession' => 'Residente', 'speciality'=> 'Oftalmología'],
            ['profession' => 'Residente', 'speciality'=> 'Oncología'],
            ['profession' => 'Residente', 'speciality'=> 'Otorrinolaringología'],
            ['profession' => 'Residente', 'speciality'=> 'Pediatría'],
            ['profession' => 'Residente', 'speciality'=> 'Psiquiatría'],
            ['profession' => 'Residente', 'speciality'=> 'Reumatología'],
            ['profession' => 'Residente', 'speciality'=> 'Toxicología'],
            ['profession' => 'Residente', 'speciality'=> 'Trasplante'],
            ['profession' => 'Residente', 'speciality'=> 'Traumatología y ortopedia'],
            ['profession' => 'Residente', 'speciality'=> 'Urología'],
//
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería familiar y comunitaria'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en administración y gestión sanitaria'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en análisis clínicos'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en cardiología y UCO'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en cuidados intensivos de adultos'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en cuidados intensivos pediátricos y neonatales'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en cuidados paliativos y dolor'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en emergencias y atención primaria'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en internación domiciliaria'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en internación general'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en investigación'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en lactancia y puerperio'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en reproducción asistida'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en salud mental'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería en unidades de trasplantes'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería escolar'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería geriátrica y gerontológica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería hematológica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería nefrológica y diálisis'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería neonatal'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería obstétrica y ginecológica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería oncológica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería pediátrica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería quirúrgica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Enfermería radiológica'],
            ['profession' => 'Personal de enfermería', 'speciality'=> 'Otras especialidades'],
//
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería familiar y comunitaria'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en administración y gestión sanitaria'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en análisis clínicos'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en cardiología y UCO'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en cuidados intensivos de adultos'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en cuidados intensivos pediátricos y neonatales'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en cuidados paliativos y dolor'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en emergencias y atención primaria'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en internación domiciliaria'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en internación general'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en investigación'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en lactancia y puerperio'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en reproducción asistida'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en salud mental'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería en unidades de trasplantes'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería escolar'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería geriátrica y gerontológica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería hematológica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería nefrológica y diálisis'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería neonatal'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería obstétrica y ginecológica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería oncológica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería pediátrica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería quirúrgica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Enfermería radiológica'],
            ['profession' => 'Auxiliar de enfermería', 'speciality'=> 'Otras especialidades'],
//
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Producción de bioimágenes'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Bioquímica'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Odontología'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Obstetricia'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Psicología'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Farmacia'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Instrumentación quirúrgica'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Nutrición'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Kinesiología y fisiatría'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Óptica'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Osteopatía'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Radiología'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Podología'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Terapia ocupacional'],
            ['profession' => 'Licenciado de la salud', 'speciality'=> 'Otra carrera o licenciatura'],
//
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en laboratorio clínico'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en radiología e imágenes diagnósticas'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en atención de adicciones'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en optometría'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en hemoterapia e inmunohematología'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en partería profesional con enfoque intercultural'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en visita médica'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en cuidados geriátricos'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en tecnología en ciencias del esteticismo'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en ciencia y tecnología de alimentos'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en prácticas cardiológicas'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en esterilización'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en asistencia dental'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Tecnicatura en cosmetología'],
            ['profession' => 'Técnico universitario', 'speciality'=> 'Otra especialidad'],
//
            ['profession' => 'Fuerza pública', 'speciality'=> 'Policía'],
            ['profession' => 'Fuerza pública', 'speciality'=> 'Bombero'],
            ['profession' => 'Fuerza pública', 'speciality'=> 'Guardavidas / Rescatista'],
            ['profession' => 'Fuerza pública', 'speciality'=> 'Paramédico'],
            ['profession' => 'Fuerza pública', 'speciality'=> 'Otra profesión'],
//          'profession' => 'x', 'speciality'=> 'x',
        ];

        $professionsDB = Profession::all();
        $specialitiesDB = Speciality::all();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Desactivamos la revisión de claves foráneas
        DB::table('profession_speciality')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Reactivamos la revisión de claves foráneas
        
        foreach ($professionsDB as $pDB) {
            // Personal médico
            foreach ($relationshipsArray as $rsArray) {
                // [Personal médico - Alergia e inmunología]
                if($pDB->name === $rsArray["profession"]){
                    foreach ($specialitiesDB as $spDB) {
                        // Alergia e inmunología
                        if($rsArray["speciality"] === $spDB->name) {
                            ProfessionSpeciality::create([
                                "profession_id" => $pDB->id,
                                "speciality_id" => $spDB->id,
                            ]);
                        } 
                    }
                }
            }    
        }
    }
}


