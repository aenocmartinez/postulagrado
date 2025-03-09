<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Src\admisiones\dao\mysql\ModalidadDao;

class ModalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('modalidades')->truncate();

        DB::table('modalidades')->insert([
            ['id' => 1, 'nombre' => 'PRESENCIAL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'nombre' => 'DISTANCIA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'nombre' => 'VIRTUAL', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::statement('ALTER TABLE modalidades AUTO_INCREMENT = 5;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
