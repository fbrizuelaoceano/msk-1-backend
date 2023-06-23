<?php

namespace Database\Seeders;

use App\Models\MethodContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MethodContactSeeder extends Seeder
{
    public $data = [
        ["name" => "Teléfono"],
        ["name" => "E-Mail"],
        ["name" => "Whatsapp"],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data as $d) {
            MethodContact::create($d);
        }
    }
}