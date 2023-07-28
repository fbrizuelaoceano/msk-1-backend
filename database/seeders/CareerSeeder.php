<?php

namespace Database\Seeders;
use App\Models\Career;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public $data = [
        ["name" => "Medicina"],
        ["name" => "Enfermería"],
        ["name" => "Lic. en salud"],
        ["name" => "Técnico en salud"],
        ["name" => "Otra"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data as $d) {
            Career::create($d);
        }
    }
}
