<?php

use App\Http\Controllers\JuegoController;
use App\Http\Controllers\WelcomeController;
use App\Models\Juego;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        // Fetch games with their multimedias
        $juegos = Juego::with('multimedias')->take(10)->get();

        // Pass the games to the view
        return view('dashboard', compact('juegos'));
    })->name('dashboard');
});
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/juegos/{juego}', [JuegoController::class, 'show'])->name('juegos.show');