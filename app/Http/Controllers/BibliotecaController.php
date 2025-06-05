<?php

namespace App\Http\Controllers;

use App\Models\Biblioteca;
use App\Models\Juego;
use Illuminate\Http\Request;

class BibliotecaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $biblioteca = $user->biblioteca()->with('juegos.multimedias')->first();
        $juegos = $biblioteca ? $biblioteca->juegos : collect();

        return view('biblioteca.index', compact('juegos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Juego $juego)
    {
        $user = $request->user();

        if ($user->sueldo < $juego->precio) {
            return back()->withErrors('Saldo insuficiente');
        }

        $user->decrement('sueldo', $juego->precio);

        $biblioteca = $user->biblioteca
            ?? $user->biblioteca()->create(['fecha_adquisicion' => now()]);

        $biblioteca->juegos()->attach($juego->id);

        return back()->with('success', 'Juego comprado y añadido a tu biblioteca');
    }

    /**
     * Display the specified resource.
     */
    public function show(Biblioteca $biblioteca)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Biblioteca $biblioteca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Biblioteca $biblioteca)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Biblioteca $biblioteca)
    {
        //
    }
}
