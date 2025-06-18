<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Anuncio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnuncioController extends Controller
{
    /**
     * Mostrar el formulario para crear un anuncio asociado a un juego.
     */
    public function create(Juego $juego)
    {
        // Sólo el desarrollador puede añadir anuncios a su juego
        if (Auth::user()->name !== $juego->desarrollador) {
            abort(403, 'No tienes permisos para crear anuncios en este juego.');
        }

        return view('anuncios.create', compact('juego'));
    }

    /**
     * Almacenar el nuevo anuncio en la base de datos.
     */
    public function store(Request $request, Juego $juego)
    {
        // Sólo el desarrollador puede guardar anuncios en su juego
        if (Auth::user()->name !== $juego->desarrollador) {
            abort(403, 'No tienes permisos para crear anuncios en este juego.');
        }

        // Validación de los datos
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Crear el anuncio asociado
        $juego->anuncios()->create($data);

        return redirect()
            ->route('juegos.show', $juego)
            ->with('success', 'Anuncio creado correctamente.');
    }
}
