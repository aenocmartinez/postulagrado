<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JornadaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('jornadas')->truncate();

        DB::table('jornadas')->insert([
            ['id' => 1, 'nombre' => 'DIURNA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'nombre' => 'NOCTURNA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 7, 'nombre' => 'Completa u Ordinaria', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 10, 'nombre' => 'TARDE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 11, 'nombre' => 'SABATINA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 13, 'nombre' => 'VIERNES Y SABADO', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 22, 'nombre' => 'VIRTUAL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 24, 'nombre' => 'MARTES A JUEVES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 28, 'nombre' => 'MAÑANA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::statement('ALTER TABLE jornadas AUTO_INCREMENT = 29;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
