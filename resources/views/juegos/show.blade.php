{{-- resources/views/juegos/show.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="max-w-5xl mx-auto py-8 text-white">

      {{-- ← Volver --}}
      <a href="{{ route('welcome') }}"
         class="inline-flex items-center text-purple-300 hover:text-white transition mb-6">
        ← Volver
      </a>

      {{-- Título --}}
      <h1 class="text-3xl font-bold mb-4">{{ $juego->titulo }}</h1>

      {{-- Media principal: vídeo si existe, sino imagen --}}
      @php
        $video  = $juego->multimedias->firstWhere('tipo','video');
        $imagen = $juego->multimedias->firstWhere('tipo','imagen');
      @endphp

      @if($video)
        <video controls
               class="w-full max-h-[400px] mb-6 rounded shadow-lg bg-black">
          <source src="{{ asset($video->url) }}" type="video/mp4">
          Tu navegador no soporta la etiqueta <code>&lt;video&gt;</code>.
        </video>
      @elseif($imagen)
        <img src="{{ asset($imagen->url) }}"
             alt="{{ $juego->titulo }}"
             class="w-full max-h-[400px] object-contain mb-6 rounded shadow-lg">
      @endif

      {{-- Descripción --}}
      <p class="mb-6 text-purple-200">{{ $juego->descripcion }}</p>

      {{-- Botón compra/descarga --}}
      @livewire('boton-descarga-juego', ['juego' => $juego])

      {{-- Logs para Electron --}}
      <pre id="electron-log"
           class="bg-black text-green-400 text-xs p-2 rounded max-h-48 overflow-y-auto"></pre>
      <script>
        if (window.electronAPI?.ping) {
          window.electronAPI.ping();
        }
      </script>

      {{-- Sección Anuncios Vigentes --}}
      @php
        $now = now();
        $anunciosVigentes = $juego->anuncios
          ->filter(fn($a) => $now->between($a->fecha_inicio, $a->fecha_fin));
      @endphp

      @if($anunciosVigentes->isNotEmpty())
        <section class="mt-12 bg-purple-900/50 p-6 rounded-lg shadow-inner">
          <h2 class="text-2xl font-semibold mb-4">Anuncios</h2>
          <div class="space-y-4">
            @foreach($anunciosVigentes as $anuncio)
              <div class="p-4 bg-purple-800/70 rounded">
                <h3 class="text-xl font-bold text-purple-100 mb-1">
                  {{ $anuncio->titulo }}
                </h3>
                <p class="text-purple-200 text-sm">
                  {{ $anuncio->descripcion }}
                </p>
                <p class="mt-2 text-xs text-purple-400">
                  Válido desde
                  {{ $anuncio->fecha_inicio->format('d/m/Y') }}
                  hasta
                  {{ $anuncio->fecha_fin->format('d/m/Y') }}
                </p>
              </div>
            @endforeach
          </div>
        </section>
      @endif

      {{-- … aquí vendrían reseñas, metadatos, galería, etc. … --}}
    </div>
  </x-self.base>
</x-app-layout>
