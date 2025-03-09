<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            ['id' => 2, 'nombre' => 'TECNOLÓGICA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'nombre' => 'UNIVERSITARIA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 5, 'nombre' => 'ESPECIALIZACIÓN TECNOLÓGICA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 6, 'nombre' => 'ESPECIALIZACIÓN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 7, 'nombre' => 'MAESTRÍA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::statement('ALTER TABLE modalidades AUTO_INCREMENT = 8;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
