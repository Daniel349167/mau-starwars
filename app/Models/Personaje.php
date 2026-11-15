<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personaje extends Model
{
    use HasFactory;

    public const GENEROS = ['masculino', 'femenino', 'n/a'];

    public const ALTURA_MAXIMA = 300;

    public const MASA_MAXIMA = 500;

    protected $table = 'personajes';

    protected $fillable = [
        'nombre', 'altura', 'masa', 'color_cabello', 'color_ojos',
        'genero', 'planeta_origen', 'origen_swapi', 'swapi_id',
    ];

    protected function casts(): array
    {
        return ['altura' => 'integer', 'masa' => 'integer', 'origen_swapi' => 'boolean', 'swapi_id' => 'integer'];
    }
}
