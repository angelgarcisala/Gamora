<?php

use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\JuegoController;
use App\Models\Juego;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $juegos = Juego::with('multimedias')->take(10)->get();
    return view('dashboard', compact('juegos'));
})->name('dashboard');

Route::middleware('auth')->group(function () {
    // Biblioteca
    Route::get('biblioteca', [BibliotecaController::class, 'index'])
         ->name('biblioteca.index');

    // Tienda pública y compra
    Route::get('tienda', [JuegoController::class, 'tienda'])
         ->name('tienda.index');
    Route::post('juegos/{juego}/comprar', [BibliotecaController::class, 'store'])
         ->name('juegos.comprar');

    // CRUD de juegos para el desarrollador
    Route::get('juegos/create', [JuegoController::class, 'create'])
         ->name('juegos.create');
    Route::post('juegos', [JuegoController::class, 'store'])
         ->name('juegos.store');
    Route::get('juegos/mis-juegos', [JuegoController::class, 'mis_juegos'])
         ->name('juegos.mis_juegos');
    Route::get('juegos/{juego}/edit', [JuegoController::class, 'edit'])
         ->name('juegos.edit');
    Route::put('juegos/{juego}', [JuegoController::class, 'update'])
         ->name('juegos.update');
    Route::delete('juegos/{juego}', [JuegoController::class, 'destroy'])
         ->name('juegos.destroy');

    // Ver un juego
    Route::get('juegos/{juego}', [JuegoController::class, 'show'])
         ->name('juegos.show');
});
