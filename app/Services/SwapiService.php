<?php

namespace App\Services;

use App\Models\Personaje;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SwapiService
{
    public function obtenerPersonaje(int $id): array
    {
        $response = $this->consultar("people/{$id}/");

        if ($response->notFound()) {
            $this->fallar('No existe un personaje en SWAPI con ese ID. Prueba con otro.');
        }

        if (! $response->successful()) {
            $this->fallar('SWAPI no está disponible en este momento. Inténtalo más tarde.');
        }

        $datos = $response->json();

        if (! is_array($datos) || ! is_string($datos['name'] ?? null)
            || mb_strlen(trim($datos['name'])) < 3 || mb_strlen($datos['name']) > 100) {
            $this->fallar('SWAPI devolvió datos inválidos. No se guardó ningún personaje.');
        }

        $altura = $this->normalizarEntero($datos['height'] ?? null, Personaje::ALTURA_MAXIMA);
        $masa = $this->normalizarEntero($datos['mass'] ?? null, Personaje::MASA_MAXIMA);
        $planeta = $this->obtenerPlaneta($datos['homeworld'] ?? null);

        return [
            'personaje' => [
                'nombre' => trim($datos['name']),
                'altura' => $altura,
                'masa' => $masa,
                'color_cabello' => $this->normalizarTexto($datos['hair_color'] ?? null),
                'color_ojos' => $this->normalizarTexto($datos['eye_color'] ?? null),
                'genero' => match ($datos['gender'] ?? null) {
                    'male' => 'masculino',
                    'female' => 'femenino',
                    default => 'n/a',
                },
                'planeta_origen' => $planeta,
                'origen_swapi' => true,
                'swapi_id' => $id,
            ],
            'aviso' => $altura === null || $masa === null || $planeta === null
                ? 'Los datos desconocidos, fuera de rango o no disponibles se guardaron sin especificar.'
                : null,
        ];
    }

    private function consultar(string $ruta): Response
    {
        try {
            return Http::acceptJson()->connectTimeout(3)->timeout(8)
                ->get(rtrim(config('services.swapi.url'), '/').'/'.$ruta);
        } catch (ConnectionException $exception) {
            report($exception);
            $this->fallar('No pudimos conectar con SWAPI o se agotó el tiempo de espera. Vuelve a intentarlo.');
        }
    }

    private function obtenerPlaneta(mixed $url): ?string
    {
        if (! is_string($url) || ! preg_match('~^https?://swapi\.py4e\.com/api/planets/(\d+)/$~', $url, $coincidencias)) {
            return null;
        }

        try {
            $response = $this->consultar("planets/{$coincidencias[1]}/");

            return $response->successful() ? $this->normalizarTexto($response->json('name')) : null;
        } catch (ValidationException) {
            return null;
        }
    }

    private function normalizarEntero(mixed $valor, int $maximo): ?int
    {
        if (! is_string($valor) && ! is_numeric($valor)) {
            return null;
        }

        $numero = str_replace(',', '', trim((string) $valor));

        if (! is_numeric($numero) || (float) $numero < 1 || (float) $numero > $maximo) {
            return null;
        }

        return (int) round((float) $numero);
    }

    private function normalizarTexto(mixed $valor): ?string
    {
        if (! is_string($valor) || in_array(strtolower(trim($valor)), ['', 'unknown', 'n/a', 'none'])) {
            return null;
        }

        return mb_substr(trim($valor), 0, 100);
    }

    private function fallar(string $mensaje): never
    {
        throw ValidationException::withMessages(['swapi_id' => $mensaje]);
    }
}
