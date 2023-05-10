<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfessionSeeder extends Seeder
{

    public $data = [
        ['name' => "Personal médico"],
        ['name' => "Licenciado de la salud"],
        ['name' => "Personal de enfermería"],
        ['name' => "Auxiliar de enfermería"],
        ['name' => "Fuerza pública"],
        ['name' => "Técnico universitario"],
        ['name' => "Residente"],
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
        DB::table('professions')->truncate();
        foreach ($this->data as $d) {
            Profession::create($d);
        }
    }
}
