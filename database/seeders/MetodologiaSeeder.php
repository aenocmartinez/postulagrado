<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodologiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('metodologias')->truncate();

        DB::table('metodologias')->insert([
            ['id' => 1, 'nombre' => 'PRESENCIAL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'nombre' => 'DISTANCIA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'nombre' => 'VIRTUAL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::statement('ALTER TABLE metodologias AUTO_INCREMENT = 5;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
