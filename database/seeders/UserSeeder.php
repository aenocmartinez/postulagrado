<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'abimelec.martinez@unicolmayor.edu.co'], // Evita duplicados
            [
                'name' => 'Abimelec Enoc Martinez Robles',
                'email' => 'abimelec.martinez@unicolmayor.edu.co',
                'password' => Hash::make('Abim3l3cEM.5'), 
            ]         
        );

        User::updateOrCreate(
            ['email' => 'lrmolina@unicolmayor.edu.co'], // Evita duplicados
            [
                'name' => 'Luisa Raquel Molina Quintero',
                'email' => 'lrmolina@unicolmayor.edu.co',
                'role' => 'ProgramaAcademico',
                'prog_id' => 3717,
                'password' => Hash::make('Programa+2024'), 
            ]            
        );        
    }
}
