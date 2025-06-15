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
        $media = $juego->multimedias;
      @endphp

      <div 
        x-data="{
          media: @js($media->map(fn($m) => [
            'id'   => $m->id,
            'tipo' => $m->tipo,
            'url'  => asset($m->url),
          ])),
          selected: null,
          init() { this.selected = this.media[0] },
        }"
        x-init="init()"
        class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-8"
      >
        {{-- Columna izquierda: galería --}}
        <div>
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
          <p class="text-purple-200">{{ $juego->descripcion }}</p>

          <div>
            <span class="font-semibold">Desarrollador:</span>
            {{ $juego->desarrollador }}
          </div>
          <div>
            <span class="font-semibold">Editor:</span>
            {{ $juego->editor }}
          </div>

          {{-- Editar (si eres desarrollador) --}}
          @if(Auth::user()->name === $juego->desarrollador)
            <a href="{{ route('juegos.edit', $juego) }}"
               class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl transition">
              Editar juego
            </a>
          @endif

          {{-- Tags --}}
          <div class="flex flex-wrap gap-2">
            @foreach($juego->etiquetas ?? [] as $et)
              <span class="px-3 py-1 bg-purple-700 rounded-full text-sm">{{ $et->nombre }}</span>
            @endforeach
          </div>

          {{-- Compra / Descarga --}}
          <div class="mt-6 space-y-4">
            {{-- Comprar --}}
            @can('comprar', $juego)
              <form action="{{ route('juegos.comprar', $juego) }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-2xl shadow-lg transition">
                  Comprar por €{{ number_format($juego->precio, 2) }}
                </button>
              </form>
            @endcan

            {{-- Jugar / Descargar --}}
            @can('jugar', $juego)
              {{-- Electron: botón Livewire --}}
              <div id="electron-only" class="hidden">
                @livewire('boton-descarga-juego', ['juego' => $juego])
              </div>

              {{-- Browser: mensaje + botón descarga --}}
              <div id="browser-only" class="hidden space-y-2 text-center">
                <p class="text-yellow-300">
                  No puedes jugar desde aquí. Para acceder al juego, descarga Gamora Desktop:
                </p>
                <button
                  data-download-btn
                  data-url="{{ Storage::disk('s3')->url('download/Gamora Setup 1.0.0.exe') }}"
                  class="inline-block px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-2xl shadow-lg transition"
                >
                  Descargar Gamora Desktop
                </button>
              </div>
            @endcan
          </div>
        </div>
      </div>

      {{-- … cualquier otra sección adicional … --}}
    </div>
  </x-self.base>
</x-app-layout>
