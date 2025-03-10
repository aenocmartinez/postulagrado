<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProgramaContactosSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $programasIds = DB::table('programas')->pluck('id')->toArray();

        if (empty($programasIds)) {
            $this->command->info('No hay programas en la base de datos. Se necesita poblar la tabla programas primero.');
            return;
        }

        $contactos = [];

        for ($i = 0; $i < 25; $i++) {
            $contactos[] = [
                'nombre' => $faker->name(),
                'telefono' => $faker->numerify('3#########'), 
                'email' => $faker->unique()->safeEmail(),
                'observacion' => $faker->sentence(8),
                'programa_id' => $faker->randomElement($programasIds),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('programa_contactos')->insert($contactos);
    }
}
