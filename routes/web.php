<?php

use App\Http\Controllers\PersonajeController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/personajes');

Route::prefix('personajes')->name('personajes.')->controller(PersonajeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/crear', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::post('/importar', 'importar')->name('importar');
    Route::get('/{personaje}/editar', 'edit')->whereNumber('personaje')->name('edit');
    Route::put('/{personaje}', 'update')->whereNumber('personaje')->name('update');
    Route::delete('/{personaje}', 'destroy')->whereNumber('personaje')->name('destroy');
});
