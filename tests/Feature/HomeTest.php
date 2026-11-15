<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_inicio_redirige_al_archivo_de_personajes(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/personajes');
    }
}
