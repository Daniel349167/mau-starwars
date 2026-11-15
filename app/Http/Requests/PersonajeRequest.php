<?php

namespace App\Http\Requests;

use App\Models\Personaje;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
            'altura' => ['nullable', 'integer', 'min:1', 'max:'.Personaje::ALTURA_MAXIMA],
            'masa' => ['nullable', 'integer', 'min:1', 'max:'.Personaje::MASA_MAXIMA],
            'color_cabello' => ['nullable', 'string', 'max:100'],
            'color_ojos' => ['nullable', 'string', 'max:100'],
            'genero' => ['required', Rule::in(Personaje::GENEROS)],
            'planeta_origen' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return ['color_cabello' => 'color de cabello', 'color_ojos' => 'color de ojos', 'genero' => 'género', 'planeta_origen' => 'planeta de origen'];
    }
}
