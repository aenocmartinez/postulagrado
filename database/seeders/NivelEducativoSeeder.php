<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Src\admisiones\dao\mysql\NivelEducativoDao;

class NivelEducativoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('nivel_educativo')->truncate();
        $niveles = [
            ['nombre' => 'PREGRADO'],
            ['nombre' => 'POSTGRADO'],
        ];

        foreach ($niveles as $nivel) {
            NivelEducativoDao::create($nivel);
        }

        DB::statement('ALTER TABLE nivel_educativo AUTO_INCREMENT = 8;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
