<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;
use App\Models\Career;
use App\Models\ProfessionCareer;

class ProfessionCareerSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationshipsArray = [
            ['profession' => 'Estudiante', 'career' => 'Medicina'],
            ['profession' => 'Estudiante', 'career' => 'Enfermería'],
            ['profession' => 'Estudiante', 'career' => 'Lic. en salud'],
            ['profession' => 'Estudiante', 'career' => 'Técnico en salud'],
            ['profession' => 'Estudiante', 'career' => 'Otra'],
        ];
        $professionsDB = Profession::all();
        $carrersDB = Career::all();

        foreach ($professionsDB as $pDB) {
            // Estudiante
            foreach ($relationshipsArray as $rsArray) {
                // [Personal médico - Alergia e inmunología]
                if ($pDB->name === $rsArray["profession"]) {
                    foreach ($carrersDB as $csDB) {
                        // Alergia e inmunología
                        if ($rsArray["career"] === $csDB->name) {
                            ProfessionCareer::create([
                                "profession_id" => $pDB->id,
                                "career_id" => $csDB->id,
                            ]);
                        }
                    }
                }
            }
        }
    }
}