<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{
    public $data = [
        ['name' => "Personal médico"],
        ['name' => "Residente"],
        ['name' => "Licenciado de la salud"],
        ['name' => "Personal de enfermería"],
        ['name' => "Auxiliar de enfermería"],
        ['name' => "Fuerza pública"],
        ['name' => "Técnico universitario"],
        ['name' => "Estudiante"],
        ['name' => "Otra profesión"],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // DB::statement('SET FOREIGN_KEY_CHECKS = 0;'); // Desactivamos la revisión de claves foráneas
        try{DB::table('professions')->truncate();} catch (\Exception $e){};
        //DB::statement('SET FOREIGN_KEY_CHECKS = 1;'); // Reactivamos la revisión de claves foráneas
        foreach ($this->data as $d) {
            Profession::create($d);
        }
    }
}
