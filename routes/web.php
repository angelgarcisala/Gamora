<?php

use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\JuegoController;
use App\Models\Juego;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $juegos = Juego::with('multimedias')->take(10)->get();
    return view('dashboard', compact('juegos'));
})->name('dashboard');

Route::get('/juegos/{juego}', [JuegoController::class, 'show'])
     ->name('juegos.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
});
Route::post('juegos/{juego}/comprar', [BibliotecaController::class,'store'])
     ->name('juegos.comprar')->middleware('auth');
Route::middleware('auth')->get('biblioteca', [BibliotecaController::class,'index'])
     ->name('biblioteca.index');
