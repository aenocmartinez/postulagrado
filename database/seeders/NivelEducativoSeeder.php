<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\admisiones\dao\mysql\NivelEducativoDao;

class NivelEducativoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveles = [
            ['nombre' => 'PREGRADO'],
            ['nombre' => 'POSTGRADO'],
        ];

        foreach ($niveles as $nivel) {
            NivelEducativoDao::create($nivel);
        }
    }
}
