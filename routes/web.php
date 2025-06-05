<?php

use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\JuegoController;
use App\Models\Juego;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $juegos = Juego::with('multimedias')->take(10)->get();
    return view('dashboard', compact('juegos'));
})->name('dashboard');

Route::get('juegos/create', [JuegoController::class, 'create'])
     ->name('juegos.create')
     ->middleware('auth');

Route::get('juegos/mis-juegos', [JuegoController::class, 'mis_juegos'])
     ->name('juegos.mis_juegos')
     ->middleware('auth');

Route::post('juegos', [JuegoController::class, 'store'])
     ->name('juegos.store')
     ->middleware('auth');

Route::post('juegos/{juego}/comprar', [BibliotecaController::class, 'store'])
     ->name('juegos.comprar')
     ->middleware('auth');

Route::put('juegos/{juego}', [JuegoController::class, 'update'])
     ->name('juegos.update')
     ->middleware('auth');

Route::get('juegos/{juego}/edit', [JuegoController::class, 'edit'])
     ->name('juegos.edit')
     ->middleware('auth');

Route::get('biblioteca', [BibliotecaController::class, 'index'])
     ->name('biblioteca.index')
     ->middleware('auth');

Route::get('/juegos/{juego}', [JuegoController::class, 'show'])
     ->name('juegos.show')
     ->middleware('auth');

Route::get('tienda', [JuegoController::class, 'tienda'])
     ->name('tienda.index')
     ->middleware('auth');