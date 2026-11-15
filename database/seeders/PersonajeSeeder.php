<?php

namespace Database\Seeders;

use App\Models\Personaje;
use Illuminate\Database\Seeder;

class PersonajeSeeder extends Seeder
{
    public function run(): void
    {
        $personajes = [
            ['nombre' => 'Ahsoka Tano', 'genero' => 'femenino', 'altura' => 170, 'masa' => 55, 'planeta_origen' => 'Shili', 'color_cabello' => 'sin cabello', 'color_ojos' => 'azules'],
            ['nombre' => 'Din Djarin', 'genero' => 'masculino', 'altura' => 180, 'masa' => 80, 'planeta_origen' => 'Aq Vetina', 'color_cabello' => 'castaño', 'color_ojos' => 'marrones'],
            ['nombre' => 'Grogu', 'genero' => 'masculino', 'altura' => 42, 'masa' => 7, 'planeta_origen' => null, 'color_cabello' => 'blanco', 'color_ojos' => 'negros'],
        ];

        foreach ($personajes as $personaje) {
            Personaje::firstOrCreate(['nombre' => $personaje['nombre'], 'origen_swapi' => false], $personaje);
        }
    }
}
