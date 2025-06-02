{{-- resources/views/juegos/show.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="max-w-7xl mx-auto py-8 text-white">

      {{-- ← Volver --}}
      <a href="{{ url()->previous() }}"
         class="inline-flex items-center text-purple-300 hover:text-white transition mb-6">
        ← Volver
      </a>

      {{-- Título --}}
      <h1 class="text-3xl font-bold mb-4">{{ $juego->titulo }}</h1>

      @php
        // Recogemos toda la galería de multimedia
        $media = $juego->multimedias;
      @endphp

      <div 
        x-data="{
          media: @js($media->map(fn($m) => [
            'id'  => $m->id,
            'tipo'=> $m->tipo,
            'url' => asset($m->url),
          ])),
          selected: null,
          init() { this.selected = this.media[0] },
        }"
        x-init="init()"
        class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-8"
      >
        {{-- Columna izquierda: galería --}}
        <div>
          {{-- Área principal --}}
          <div class="bg-black rounded shadow-lg overflow-hidden aspect-video">
            <template x-if="selected.tipo === 'video'">
              <video 
                x-bind:src="selected.url" 
                autoplay muted loop playsinline 
                class="w-full h-full object-cover"
              ></video>
            </template>
            <template x-if="selected.tipo === 'imagen'">
              <img 
                x-bind:src="selected.url" 
                class="w-full h-full object-contain" 
                alt="{{ $juego->titulo }}"
              />
            </template>
          </div>

          {{-- Thumbnails --}}
          <div class="flex space-x-2 mt-4 overflow-x-auto">
            <template x-for="item in media" :key="item.id">
              <button
                @click="selected = item"
                class="flex-shrink-0 w-24 h-16 rounded overflow-hidden border-2"
                :class="{
                  'border-purple-500': selected.id === item.id,
                  'border-transparent': selected.id !== item.id
                }"
              >
                <template x-if="item.tipo === 'video'">
                  <video 
                    x-bind:src="item.url" 
                    muted playsinline 
                    class="w-full h-full object-cover"
                  ></video>
                </template>
                <template x-if="item.tipo === 'imagen'">
                  <img 
                    x-bind:src="item.url" 
                    class="w-full h-full object-cover" 
                    alt=""
                  />
                </template>
              </button>
            </template>
          </div>
        </div>

        {{-- Columna derecha: detalles --}}
        <div class="space-y-4">
          {{-- Descripción --}}
          <p class="text-purple-200">{{ $juego->descripcion }}</p>

          {{-- Reseñas --}}
          <div>
            <span class="font-semibold">{{ $juego->valoraciones->count() }}</span>
            reseñas
          </div>

          {{-- Desarrollador y editor --}}
          <div>
            <span class="font-semibold">Desarrollador:</span>
            {{ $juego->desarrollador }}
          </div>
          <div>
            <span class="font-semibold">Editor:</span>
            {{ $juego->editor }}
          </div>

          {{-- Tags populares --}}
          <div class="flex flex-wrap gap-2">
            @foreach($juego->etiquetas ?? [] as $et)
              <span class="px-3 py-1 bg-purple-700 rounded-full text-sm">
                {{ $et->nombre }}
              </span>
            @endforeach
          </div>

          {{-- Compra / Descarga --}}
          <div class="mt-6 space-y-4">
            {{-- Botón de comprar (solo si no lo tiene) --}}
            @can('comprar', $juego)
              <form action="{{ route('juegos.comprar', $juego) }}" method="POST">
                @csrf
                <button
                  type="submit"
                  class="w-full text-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-2xl shadow-lg transition"
                >
                  Comprar por €{{ number_format($juego->precio, 2) }}
                </button>
              </form>
            @endcan

            {{-- Botón de descargar/jugar (solo si ya lo tiene) --}}
            @can('jugar', $juego)
              <div id="electron-only" class="hidden">
                @livewire('boton-descarga-juego', ['juego' => $juego])
              </div>
              <div id="browser-only" class="hidden">
                <a 
                  href="{{ asset('storage/download/Gamora Setup 1.0.0.exe') }}" 
                  class="inline-block w-full text-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-2xl shadow-lg transition"
                >
                  Descargar Gamora Desktop
                </a>
              </div>
            @endcan
          </div>

        </div>
      </div>

      {{-- … aquí vendrían anuncios, reseñas detalladas, etc… --}}
    </div>
  </x-self.base>
</x-app-layout>
