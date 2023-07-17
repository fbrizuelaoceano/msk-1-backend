<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokensPassportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*  DB::table('tokens_passport')->insert([
             [
                 'name' => 'Grant Token',
                 'token' => '1000.6bda3a57055de57b5bb2c69100d61a74.6890f36a87babe76565de3ea0ba1c54e',
                 'hours_duration' => 1,
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'name' => 'Refresh Token',
                 'token' => '1000.77cf9f467eead9f1565d033e6b48ed95.bc4c8237257ec38a336f7271c4b47da7',
                 'hours_duration' => 4320,
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'name' => 'Access Token',
                 'token' => '1000.69f4d03c81e747fd157c78ae3f719c12.3d466f9b1544215d762375ab7937b97b',
                 'hours_duration' => 1,
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
         ]); */
    }
}