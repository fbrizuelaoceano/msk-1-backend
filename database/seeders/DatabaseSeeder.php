<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            ProfessionSeeder::class,
            SpecialitySeeder::class,
            CareerSeeder::class,
            MethodContactSeeder::class,
            TopicInterestSeeder::class,
            ProfessionSpecialitySeeder::class,
            ProfessionCareerSeeder::class,
        ]);
    }
}