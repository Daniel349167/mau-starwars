<?php

namespace Database\Factories;

use App\Models\Personaje;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonajeFactory extends Factory
{
    protected $model = Personaje::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'altura' => fake()->numberBetween(50, 250),
            'masa' => fake()->numberBetween(10, 200),
            'color_cabello' => 'castaño',
            'color_ojos' => 'marrón',
            'genero' => fake()->randomElement(Personaje::GENEROS),
            'planeta_origen' => null,
            'origen_swapi' => false,
            'swapi_id' => null,
        ];
    }
}
