<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Multimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JuegoController extends Controller
{
    use AuthorizesRequests;

    public function mis_juegos() {
        $userName = Auth::user()->name;
        $juegos = Juego::with('multimedias')
                        ->where('desarrollador', $userName)
                        ->get();

        return view('juegos.mis_juegos', compact('juegos'));
    }

    public function tienda(){
        $juegos = Juego::with('multimedias')->get();
        return view('tienda.index', compact('juegos'));
    }

    public function create()
    {
        return view('juegos.create');
    }

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

        // El slug convierte "Mi Juego" en "mi-juego"
        $slug = Str::slug($datos['titulo']);

        // Guardar ZIP en storage/app/public/juegos/{slug}/{zipName}
        $zipFile = $request->file('zip');
        $zipName = "{$slug}_" . time() . ".zip";
        $zipFile->storeAs("juegos/{$slug}", $zipName, "public");
        $datos['url_descarga'] = Storage::url("juegos/{$slug}/{$zipName}");

        $juego = Juego::create($datos);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $img) {
                $imgName = "juego-{$juego->id}_" . Str::random(8) . "." . $img->getClientOriginalExtension();
                $img->storeAs("images/{$slug}", $imgName, "public");
                $juego->multimedias()->create([
                    'tipo' => 'imagen',
                    'url'  => Storage::url("images/{$slug}/{$imgName}"),
                ]);
            }
        }

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $vid) {
                $vidName = "juego-{$juego->id}_" . Str::random(8) . "." . $vid->getClientOriginalExtension();
                $vid->storeAs("videos/{$slug}", $vidName, "public");
                $juego->multimedias()->create([
                    'tipo' => 'video',
                    'url'  => Storage::url("videos/{$slug}/{$vidName}"),
                ]);
            }
        }

        return redirect()->route('tienda.index')->with('mensaje', 'Juego creado con éxito.');
    }

    public function show(Juego $juego)
    {
        $juego->load([
            'multimedias' => function($query) {
                $query->orderByDesc('id');
            }
        ]);

        return view('juegos.show', compact('juego'));
    }

    public function index()
    {
        //
    }

    public function edit(Juego $juego)
    {
        $this->authorize('update', $juego);

        return view('juegos.edit', compact('juego'));
    }

    public function update(Request $request, Juego $juego)
    {
        // 1) Validar con las mismas reglas, pasando el ID para unique
        $request->validate($this->rules($juego->id));

        // 2) Guardar el slug antiguo (en caso de que el título cambie)
        $oldSlug = Str::slug($juego->titulo);

        // 3) Eliminar multimedia existente marcada:
        if ($request->has('remove_media')) {
            foreach ($request->input('remove_media') as $mediaId) {
                $m = Multimedia::find($mediaId);
                if ($m && $m->juego_id == $juego->id) {
                    // La URL se guarda como "/storage/images/{slug}/{filename}" o "/storage/videos/{slug}/{filename}"
                    $relativePath = Str::after($m->url, '/storage/');
                    Storage::disk('public')->delete($relativePath);
                    $m->delete();
                }
            }
        }

        // 4) Procesar ZIP:
        //    a) Si marcado para eliminar:
        if ($request->has('remove_zip')) {
            if ($juego->url_descarga) {
                $oldZipPath = Str::after($juego->url_descarga, '/storage/');
                Storage::disk('public')->delete($oldZipPath);
            }
            $juego->url_descarga = null;
        }
        //    b) Si cargó un ZIP nuevo, reemplazar:
        if ($request->hasFile('zip')) {
            // Borrar ZIP antiguo si no se borró en el paso anterior
            if ($juego->url_descarga) {
                $oldZipPath = Str::after($juego->url_descarga, '/storage/');
                Storage::disk('public')->delete($oldZipPath);
            }
            // El slug puede cambiar (si el usuario actualizó el título); recalculamos:
            $newSlug = Str::slug($request->input('titulo'));
            $zipFile = $request->file('zip');
            $zipName = "{$newSlug}_" . time() . ".zip";
            $zipFile->storeAs("juegos/{$newSlug}", $zipName, "public");
            $juego->url_descarga = Storage::url("juegos/{$newSlug}/{$zipName}");
        }

        // 5) Subir nuevas imágenes (si hay)
        if ($request->hasFile('imagenes')) {
            // Recalcular slug en caso de que el título haya cambiado
            $newSlug = Str::slug($request->input('titulo'));
            foreach ($request->file('imagenes') as $img) {
                $imgName = "juego-{$juego->id}_" . Str::random(8) . "." . $img->getClientOriginalExtension();
                $img->storeAs("images/{$newSlug}", $imgName, "public");
                $juego->multimedias()->create([
                    'tipo' => 'imagen',
                    'url'  => Storage::url("images/{$newSlug}/{$imgName}"),
                ]);
            }
        }

        // 6) Subir nuevos videos (si hay)
        if ($request->hasFile('videos')) {
            $newSlug = Str::slug($request->input('titulo'));
            foreach ($request->file('videos') as $vid) {
                $vidName = "juego-{$juego->id}_" . Str::random(8) . "." . $vid->getClientOriginalExtension();
                $vid->storeAs("videos/{$newSlug}", $vidName, "public");
                $juego->multimedias()->create([
                    'tipo' => 'video',
                    'url'  => Storage::url("videos/{$newSlug}/{$vidName}"),
                ]);
            }
        }

        // 7) Actualizar los campos del juego
        $juego->titulo            = $request->input('titulo');
        $juego->descripcion       = $request->input('descripcion');
        $juego->fecha_lanzamiento = $request->input('fecha_lanzamiento');
        $juego->precio            = $request->input('precio');
        $juego->nombre_ejecutable = $request->input('nombre_ejecutable');
        // Si no se subió ni eliminó ZIP, url_descarga queda intacto
        $juego->save();

        return redirect()
            ->route('tienda.index')
            ->with('mensaje', 'Juego actualizado correctamente.');
    }

    public function destroy(Juego $juego)
    {
        //
    }

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
            'videos.*'          => ['nullable', 'mimetypes:video/mp4,video/x-matroska', 'max:10240'],
            'zip'               => $id 
                                   ? ['nullable', 'mimes:zip', 'max:102400'] 
                                   : ['required',  'mimes:zip', 'max:102400'],
            'remove_media'      => ['nullable', 'array'],
            'remove_media.*'    => ['integer', 'exists:multimedia,id'],
            'remove_zip'        => ['nullable', 'in:1'],
        ];
    }
}
