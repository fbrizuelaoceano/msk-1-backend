<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionDatabaseSeeder extends Seeder
{
    protected $dumpSQL = __DIR__."/../dumps/dbtjmi0llubfgr.sql";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sql = file_get_contents($this->dumpSQL);
        DB::unprepared($sql);
    }
}
