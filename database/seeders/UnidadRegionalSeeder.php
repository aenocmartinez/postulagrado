<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadRegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('unidad_regional')->truncate();

        DB::table('unidad_regional')->insert([
            ['id' => 31, 'nombre' => 'SEDE PRINCIPAL - BOGOTÁ D.C. - CALLE 28 N° 5B - 02', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 1992, 'nombre' => 'SEDE FUNZA - CUNDINAMARCA - CALLE 15 N° 6 - 40', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2213, 'nombre' => 'SEDE TINTAL - BOGOTÁ D.C. - CALLE 6C N° 94A - 25', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2461, 'nombre' => 'SEDE FUSAGASUGA - CUNDINAMARCA - TV 12 N° 16 BIS - 56', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2463, 'nombre' => 'SEDE BOJACÁ - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2470, 'nombre' => 'SEDE CHOACHI - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2536, 'nombre' => 'SEDE CACHIPAY - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2537, 'nombre' => 'SEDE CÁQUEZA - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2551, 'nombre' => 'SEDE LA CANDELARIA - BOGOTÁ D.C. - CARRERA 6 NO. 11-51', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2556, 'nombre' => 'SEDE CAMPOALEGRE - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2557, 'nombre' => 'SEDE ISNOS - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2562, 'nombre' => 'SEDE LA CALERA - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2456, 'nombre' => 'SEDE LA MESA - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2558, 'nombre' => 'SEDE LA PLATA - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2460, 'nombre' => 'SEDE MADRID - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2538, 'nombre' => 'SEDE MOSQUERA - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2559, 'nombre' => 'SEDE NEIVA - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2560, 'nombre' => 'SEDE SANTA MARÍA - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2539, 'nombre' => 'SEDE SOACHA - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2540, 'nombre' => 'SEDE SOPÓ - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2471, 'nombre' => 'SEDE SUBACHOQUE - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2541, 'nombre' => 'SEDE TABIO - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2542, 'nombre' => 'SEDE TENJO - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2561, 'nombre' => 'SEDE TESALIA - HUILA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2462, 'nombre' => 'SEDE ZIPAQUIRÁ - CUNDINAMARCA', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
        

        DB::statement('ALTER TABLE unidad_regional AUTO_INCREMENT = 2563;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
