<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PersonajeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_lista_diez_personajes_por_pagina_en_orden_reciente(): void
    {
        $personajes = Personaje::factory()->count(12)->create();

        $this->get('/personajes')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Personajes/Index')->has('personajes.data', 10)
            ->where('personajes.total', 12)->where('personajes.data.0.id', $personajes->last()->id)
            ->where('personajes.last_page', 2));

        $this->get('/personajes?page=2')->assertInertia(fn (Assert $page) => $page->has('personajes.data', 2));
    }

    public function test_muestra_formularios_de_creacion_y_edicion(): void
    {
        $personaje = Personaje::factory()->create();
        $this->get('/personajes/crear')->assertInertia(fn (Assert $page) => $page->component('Personajes/Create'));
        $this->get("/personajes/{$personaje->id}/editar")->assertInertia(fn (Assert $page) => $page
            ->component('Personajes/Edit')->where('personaje.nombre', $personaje->nombre));
    }

    public function test_crea_personaje_manual_con_campos_opcionales_vacios(): void
    {
        $this->post('/personajes', ['nombre' => '  Ahsoka Tano  ', 'genero' => 'femenino', 'altura' => ''])
            ->assertRedirect('/personajes')->assertSessionHas('success');

        $this->assertDatabaseHas('personajes', ['nombre' => 'Ahsoka Tano', 'altura' => null, 'origen_swapi' => false, 'swapi_id' => null]);
    }

    public function test_no_permite_falsificar_el_origen_de_un_registro_manual(): void
    {
        $this->post('/personajes', ['nombre' => 'Ahsoka Tano', 'genero' => 'femenino', 'origen_swapi' => true, 'swapi_id' => 1])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('personajes', ['nombre' => 'Ahsoka Tano', 'origen_swapi' => false, 'swapi_id' => null]);
    }

    public function test_edita_los_datos_sin_perder_la_procedencia(): void
    {
        $personaje = Personaje::factory()->create(['origen_swapi' => true, 'swapi_id' => 1]);

        $this->put("/personajes/{$personaje->id}", [
            'nombre' => 'Luke actualizado', 'genero' => 'masculino', 'masa' => 80, 'origen_swapi' => false, 'swapi_id' => 2,
        ])->assertRedirect('/personajes');

        $this->assertDatabaseHas('personajes', ['id' => $personaje->id, 'nombre' => 'Luke actualizado', 'masa' => 80, 'origen_swapi' => true, 'swapi_id' => 1]);
    }

    public function test_elimina_personaje(): void
    {
        $personaje = Personaje::factory()->create();
        $this->delete("/personajes/{$personaje->id}")->assertRedirect('/personajes')->assertSessionHas('success');
        $this->assertDatabaseMissing('personajes', ['id' => $personaje->id]);
    }

    public function test_personaje_inexistente_devuelve_404(): void
    {
        $this->get('/personajes/999/editar')->assertNotFound();
        $this->put('/personajes/999', ['nombre' => 'Prueba', 'genero' => 'n/a'])->assertNotFound();
        $this->delete('/personajes/999')->assertNotFound();
    }

    #[DataProvider('datosInvalidos')]
    public function test_rechaza_datos_invalidos(string $campo, mixed $valor): void
    {
        $datos = array_replace(['nombre' => 'Ahsoka Tano', 'genero' => 'femenino'], [$campo => $valor]);
        $this->post('/personajes', $datos)->assertSessionHasErrors($campo);
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function datosInvalidos(): array
    {
        return [
            'nombre obligatorio' => ['nombre', ''], 'nombre corto' => ['nombre', 'ab'],
            'nombre largo' => ['nombre', str_repeat('a', 101)], 'nombre array' => ['nombre', ['Luke']],
            'altura negativa' => ['altura', -1], 'altura cero' => ['altura', 0],
            'altura decimal' => ['altura', 170.5], 'altura excesiva' => ['altura', 301],
            'masa negativa' => ['masa', -1], 'masa cero' => ['masa', 0],
            'masa decimal' => ['masa', 77.5], 'masa excesiva' => ['masa', 501],
            'genero obligatorio' => ['genero', ''], 'genero invalido' => ['genero', 'otro'],
            'cabello largo' => ['color_cabello', str_repeat('a', 101)],
            'ojos largos' => ['color_ojos', str_repeat('a', 101)],
            'planeta largo' => ['planeta_origen', str_repeat('a', 101)],
        ];
    }

    public function test_filtra_por_nombre_y_genero_y_conserva_la_busqueda_al_paginar(): void
    {
        Personaje::factory()->count(12)->create(['nombre' => 'Leia Organa', 'genero' => 'femenino']);
        Personaje::factory()->create(['nombre' => 'Luke Skywalker', 'genero' => 'masculino']);

        $this->get('/personajes?buscar=Leia&genero=femenino')->assertInertia(fn (Assert $page) => $page
            ->where('personajes.total', 12)->where('filtros.buscar', 'Leia')
            ->where('personajes.next_page_url', fn ($url) => str_contains($url, 'buscar=Leia') && str_contains($url, 'genero=femenino')));
    }
}
