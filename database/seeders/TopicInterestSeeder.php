<?php

namespace Database\Seeders;

use App\Models\TopicInterest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicInterestSeeder extends Seeder
{
    public $data = [
        ['name' => 'Anestesiología'],
        ['name' => 'Auditoría y administración sanitaria'],
        ['name' => 'Cardiología '],
        ['name' => 'Cirugía'],
        ['name' => 'Dermatología'],
        ['name' => 'Diabetes'],
        ['name' => 'Diagnóstico por imágenes'],
        ['name' => 'Emergentología'],
        ['name' => 'Gastroenterología'],
        ['name' => 'Generalista'],
        ['name' => 'Geriatría y gerontología'],
        ['name' => 'Ginecología'],
        ['name' => 'Hematología'],
        ['name' => 'Infectología'],
        ['name' => 'Medicina del deporte'],
        ['name' => 'Medicina familiar y comunitaria'],
        ['name' => 'Medicina intensiva'],
        ['name' => 'Medicina interna / clínica'],
        ['name' => 'Neonatología'],
        ['name' => 'Nutrición'],
        ['name' => 'Obstetricia'],
        ['name' => 'Oncología'],
        ['name' => 'Pediatría'],
        ['name' => 'Psiquiatría'],
        ['name' => 'Traumatología y ortopedia'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $d) {
            TopicInterest::create($d);
        }
    }
}