<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Multimedia;
use App\Models\Biblioteca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JuegoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Mostrar los juegos que ha creado el usuario.
     */
    public function mis_juegos()
    {
        $userName = Auth::user()->name;
        $juegos = Juego::with('multimedias')
                       ->where('desarrollador', $userName)
                       ->get();

        return view('juegos.mis_juegos', compact('juegos'));
    }

    /**
     * Mostrar la tienda con todos los juegos.
     */
    public function tienda()
    {
        $juegos = Juego::with('multimedias')->get();
        return view('tienda.index', compact('juegos'));
    }

    /**
     * Formulario para crear un nuevo juego.
     */
    public function create()
    {
        return view('juegos.create');
    }

    /**
     * Almacenar un nuevo juego en la base de datos. 
     * Además, lo asocia automáticamente en la biblioteca del creador sin coste.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules());

        $datos = $request->only([
            'titulo',
            'descripcion',
            'fecha_lanzamiento',
            'precio',
            'nombre_ejecutable',
        ]);

        $datos['desarrollador'] = Auth::user()->name;
        $datos['editor']        = Auth::user()->name;

        $slug = Str::slug($datos['titulo']);

        // 1) Subir el ZIP a S3
        $zipFile = $request->file('zip');
        $zipName = "{$slug}_" . time() . ".zip";
        $zipPath = $zipFile->storeAs("juegos/{$slug}", $zipName, 's3');
        $datos['url_descarga'] = Storage::disk('s3')->url($zipPath);

        // 2) Crear el juego
        $juego = Juego::create($datos);

        // 3) Asociar el juego al creador sin coste
        $user = Auth::user();
        $biblioteca = $user->biblioteca
                     ?? $user->biblioteca()->create(['fecha_adquisicion' => now()]);
        $biblioteca->juegos()->attach($juego->id);

        // 4) Subir multimedia
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $imgName = "juego-{$juego->id}_" . Str::random(8) . "." . $img->getClientOriginalExtension();
                $imgPath = $img->storeAs("images/{$slug}", $imgName, 's3');
                $juego->multimedias()->create([
                    'tipo' => 'imagen',
                    'url'  => Storage::disk('s3')->url($imgPath),
                ]);
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $vid) {
                $vidName = "juego-{$juego->id}_" . Str::random(8) . "." . $vid->getClientOriginalExtension();
                $vidPath = $vid->storeAs("videos/{$slug}", $vidName, 's3');
                $juego->multimedias()->create([
                    'tipo' => 'video',
                    'url'  => Storage::disk('s3')->url($vidPath),
                ]);
            }
        }

        return redirect()
            ->route('tienda.index')
            ->with('mensaje', 'Juego creado y añadido a tu biblioteca con éxito.');
    }

    /**
     * Mostrar detalles de un juego.
     */
    public function show(Juego $juego)
    {
        $juego->load([
            'multimedias' => function($query) {
                $query->orderByDesc('id');
            }
        ]);

        return view('juegos.show', compact('juego'));
    }

    /**
     * Formulario para editar un juego.
     */
    public function edit(Juego $juego)
    {
        $this->authorize('update', $juego);
        return view('juegos.edit', compact('juego'));
    }

    /**
     * Actualizar un juego existente.
     */
    public function update(Request $request, Juego $juego)
    {
        $request->validate($this->rules($juego->id));

        $oldSlug = Str::slug($juego->titulo);

        // Eliminar multimedia seleccionada...
        if ($request->has('remove_media')) {
            foreach ($request->input('remove_media') as $mediaId) {
                $m = Multimedia::find($mediaId);
                if ($m && $m->juego_id == $juego->id) {
                    $relative = Str::after($m->url, Storage::disk('s3')->url(''));
                    Storage::disk('s3')->delete($relative);
                    $m->delete();
                }
            }
        }

        // Actualizar ZIP si cambia...
        if ($request->has('remove_zip') && $juego->url_descarga) {
            $relative = Str::after($juego->url_descarga, Storage::disk('s3')->url(''));
            Storage::disk('s3')->delete($relative);
            $juego->url_descarga = null;
        }
        if ($request->hasFile('zip')) {
            if ($juego->url_descarga) {
                $relative = Str::after($juego->url_descarga, Storage::disk('s3')->url(''));
                Storage::disk('s3')->delete($relative);
            }
            $newSlug = Str::slug($request->input('titulo'));
            $zipFile = $request->file('zip');
            $zipName = "{$newSlug}_" . time() . ".zip";
            $zipPath = $zipFile->storeAs("juegos/{$newSlug}", $zipName, 's3');
            $juego->url_descarga = Storage::disk('s3')->url($zipPath);
        }

        // Subir nuevas imágenes y vídeos...
        if ($request->hasFile('imagenes')) {
            $newSlug = Str::slug($request->input('titulo'));
            foreach ($request->file('imagenes') as $img) {
                $imgName = "juego-{$juego->id}_" . Str::random(8) . "." . $img->getClientOriginalExtension();
                $imgPath = $img->storeAs("images/{$newSlug}", $imgName, 's3');
                $juego->multimedias()->create([
                    'tipo' => 'imagen',
                    'url'  => Storage::disk('s3')->url($imgPath),
                ]);
            }
        }
        if ($request->hasFile('videos')) {
            $newSlug = Str::slug($request->input('titulo'));
            foreach ($request->file('videos') as $vid) {
                $vidName = "juego-{$juego->id}_" . Str::random(8) . "." . $vid->getClientOriginalExtension();
                $vidPath = $vid->storeAs("videos/{$newSlug}", $vidName, 's3');
                $juego->multimedias()->create([
                    'tipo' => 'video',
                    'url'  => Storage::disk('s3')->url($vidPath),
                ]);
            }
        }

        // Guardar cambios del modelo
        $juego->update([
            'descripcion'       => $request->input('descripcion'),
            'fecha_lanzamiento' => $request->input('fecha_lanzamiento'),
            'precio'            => $request->input('precio'),
            'nombre_ejecutable' => $request->input('nombre_ejecutable'),
        ]);

        return redirect()
            ->route('tienda.index')
            ->with('mensaje', 'Juego actualizado correctamente.');
    }

    /**
     * Eliminar un juego y reembolsar a todos los compradores.
     */
    public function destroy(Juego $juego)
    {
        $this->authorize('delete', $juego);

        DB::transaction(function() use ($juego) {
            // Para cada biblioteca que tenga este juego:
            foreach ($juego->bibliotecas as $biblioteca) {
                $user = $biblioteca->user;
                // Reembolsar precio
                $user->increment('sueldo', $juego->precio);
            }

            // Eliminar pivotes en biblioteca_juego
            $juego->bibliotecas()->detach();

            // Al eliminar el juego, multimedia y pivotes se eliminarán por cascade
            $juego->delete();
        });

        return redirect()
            ->route('juegos.mis_juegos')
            ->with('mensaje', 'Juego eliminado y compradores reembolsados con éxito.');
    }

    /**
     * Reglas de validación para crear/actualizar.
     */
    private function rules(?int $id = null): array
    {
        $uniqueTitulo = 'unique:juegos,titulo' . ($id ? ",{$id}" : '');

        return [
            'titulo'            => ['required', 'string', 'max:255', $uniqueTitulo],
            'descripcion'       => ['required', 'string'],
            'fecha_lanzamiento' => ['nullable', 'date'],
            'precio'            => ['required', 'numeric', 'min:0'],
            'nombre_ejecutable' => ['required', 'string', 'max:255'],
            'imagenes.*'        => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
            'videos.*'          => ['nullable', 'mimetypes:video/mp4,video/x-matroska', 'max:102400'],
            'zip'               => $id
                                ? ['nullable', 'mimes:zip', 'max:104857600']
                                : ['required', 'mimes:zip', 'max:104857600'],
            'remove_media'      => ['nullable', 'array'],
            'remove_media.*'    => ['integer', 'exists:multimedia,id'],
            'remove_zip'        => ['nullable', 'in:1'],
        ];
    }
}
