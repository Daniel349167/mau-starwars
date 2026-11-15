<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportarPersonajeRequest;
use App\Http\Requests\PersonajeRequest;
use App\Models\Personaje;
use App\Services\SwapiService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PersonajeController extends Controller
{
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'genero' => ['nullable', Rule::in(Personaje::GENEROS)],
        ]);

        $personajes = Personaje::query()
            ->when($filtros['buscar'] ?? null, fn ($query, $buscar) => $query->where('nombre', 'like', '%'.$buscar.'%'))
            ->when($filtros['genero'] ?? null, fn ($query, $genero) => $query->where('genero', $genero))
            ->latest('id')->paginate(10)->withQueryString();

        return Inertia::render('Personajes/Index', [
            'personajes' => $personajes,
            'filtros' => ['buscar' => $filtros['buscar'] ?? '', 'genero' => $filtros['genero'] ?? ''],
            'resumen' => ['total' => Personaje::count(), 'swapi' => Personaje::where('origen_swapi', true)->count()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Personajes/Create');
    }

    public function store(PersonajeRequest $request): RedirectResponse
    {
        Personaje::create($request->validated());

        return to_route('personajes.index')->with('success', 'Personaje creado correctamente.');
    }

    public function edit(Personaje $personaje): Response
    {
        return Inertia::render('Personajes/Edit', ['personaje' => $personaje]);
    }

    public function update(PersonajeRequest $request, Personaje $personaje): RedirectResponse
    {
        $personaje->update($request->validated());

        return to_route('personajes.index')->with('success', 'Cambios guardados correctamente.');
    }

    public function destroy(Personaje $personaje): RedirectResponse
    {
        $personaje->delete();

        return to_route('personajes.index')->with('success', 'Personaje eliminado correctamente.');
    }

    public function importar(ImportarPersonajeRequest $request, SwapiService $swapi): RedirectResponse
    {
        $resultado = $swapi->obtenerPersonaje((int) $request->validated('swapi_id'));

        try {
            $personaje = Personaje::create($resultado['personaje']);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['swapi_id' => 'Este personaje de SWAPI ya fue importado.']);
        }

        return to_route('personajes.index')->with('success', "{$personaje->nombre} se importó correctamente.")
            ->with('warning', $resultado['aviso']);
    }
}
