<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportarPersonajeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function datosSwapi(array $cambios = []): array
    {
        return array_replace([
            'name' => 'Luke Skywalker', 'height' => '172', 'mass' => '77', 'gender' => 'male',
            'hair_color' => 'blond', 'eye_color' => 'blue', 'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
        ], $cambios);
    }

    public function test_importa_mapea_datos_y_resuelve_el_planeta(): void
    {
        Http::fake([
            '*/people/1/' => Http::response($this->datosSwapi()),
            '*/planets/1/' => Http::response(['name' => 'Tatooine']),
        ]);

        $this->post('/personajes/importar', ['swapi_id' => 1])->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker', 'altura' => 172, 'masa' => 77, 'genero' => 'masculino',
            'color_cabello' => 'blond', 'color_ojos' => 'blue', 'planeta_origen' => 'Tatooine', 'origen_swapi' => true, 'swapi_id' => 1,
        ]);
        Http::assertSentCount(2);
    }

    public function test_rechaza_duplicados_antes_de_consultar_swapi(): void
    {
        Personaje::factory()->create(['origen_swapi' => true, 'swapi_id' => 1]);
        Http::fake();
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasErrors('swapi_id');
        Http::assertNothingSent();
        $this->assertDatabaseCount('personajes', 1);
    }

    public function test_indice_unico_impide_duplicados_incluso_con_solicitudes_simultaneas(): void
    {
        Http::fake(function () {
            Personaje::factory()->create(['origen_swapi' => true, 'swapi_id' => 1]);

            return Http::response($this->datosSwapi(['homeworld' => null]));
        });

        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasErrors('swapi_id');
        $this->assertDatabaseCount('personajes', 1);
    }

    #[DataProvider('idsInvalidos')]
    public function test_valida_id_antes_de_consultar_la_api(mixed $id): void
    {
        Http::fake();
        $this->post('/personajes/importar', ['swapi_id' => $id])->assertSessionHasErrors('swapi_id');
        Http::assertNothingSent();
    }

    public static function idsInvalidos(): array
    {
        return [[0], [84], [-1], [1.5], ['abc'], [null]];
    }

    #[DataProvider('erroresApi')]
    public function test_errores_http_no_crean_registros(int $status): void
    {
        Http::fake(['*' => Http::response([], $status)]);
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasErrors('swapi_id');
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function erroresApi(): array
    {
        return [[404], [429], [500], [503]];
    }

    public function test_timeout_y_fallo_de_conexion_muestran_error_recuperable(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection timed out'));
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasErrors('swapi_id');
        $this->assertDatabaseCount('personajes', 0);
    }

    #[DataProvider('respuestasInvalidas')]
    public function test_rechaza_respuesta_invalida_sin_guardar_datos(mixed $respuesta): void
    {
        Http::fake(['*' => Http::response($respuesta)]);
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasErrors('swapi_id');
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function respuestasInvalidas(): array
    {
        return [['<html>Error</html>'], [[]], [['name' => ['Luke']]], [['name' => 'ab']]];
    }

    public function test_normaliza_desconocidos_y_valores_fuera_de_rango_con_aviso(): void
    {
        Http::fake(['*' => Http::response($this->datosSwapi([
            'height' => 'unknown', 'mass' => '1,358', 'hair_color' => 'n/a', 'gender' => 'hermaphrodite', 'homeworld' => null,
        ]))]);

        $this->post('/personajes/importar', ['swapi_id' => 22])->assertSessionHas('warning');
        $this->assertDatabaseHas('personajes', ['swapi_id' => 22, 'altura' => null, 'masa' => null, 'color_cabello' => null, 'genero' => 'n/a']);
    }

    public function test_redondea_decimales_y_mapea_genero_femenino(): void
    {
        Http::fake(['*' => Http::response($this->datosSwapi(['mass' => '49.8', 'gender' => 'female', 'homeworld' => null]))]);
        $this->post('/personajes/importar', ['swapi_id' => 5])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 5, 'masa' => 50, 'genero' => 'femenino']);
    }

    public function test_fallo_del_planeta_no_bloquea_la_importacion(): void
    {
        Http::fake(['*/people/1/' => Http::response($this->datosSwapi()), '*/planets/1/' => Http::response([], 503)]);
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHas('warning')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 1, 'planeta_origen' => null]);
    }

    public function test_no_consulta_urls_de_planetas_de_otros_dominios(): void
    {
        Http::fake(['*/people/1/' => Http::response($this->datosSwapi(['homeworld' => 'http://127.0.0.1/private']))]);
        $this->post('/personajes/importar', ['swapi_id' => 1])->assertSessionHasNoErrors();
        Http::assertSentCount(1);
    }
}
