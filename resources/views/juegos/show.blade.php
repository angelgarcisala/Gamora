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
        {{-- Galería y thumbnails (igual que antes) --}}
        <div>…</div>
        <div class="space-y-4">
          <p class="text-purple-200">{{ $juego->descripcion }}</p>
          <div><span class="font-semibold">Desarrollador:</span> {{ $juego->desarrollador }}</div>
          <div><span class="font-semibold">Editor:</span> {{ $juego->editor }}</div>

          {{-- Editar si eres dev --}}
          @if(Auth::user()->name === $juego->desarrollador)
            <a href="{{ route('juegos.edit', $juego) }}"
               class="inline-block px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl transition">
              Editar juego
            </a>
          @endif

          {{-- Tags --}}
          <div class="flex flex-wrap gap-2">…</div>

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
              {{-- En Electron: el botón de Livewire --}}
              <div id="electron-only" class="hidden">
                @livewire('boton-descarga-juego', ['juego' => $juego])
              </div>

              {{-- En Browser: mensaje + botón de descarga --}}
              <div id="browser-only" class="hidden space-y-2 text-center">
                <p class="text-yellow-300">
                  No puedes jugar desde aquí. Para acceder al juego, descarga Gamora Desktop:
                </p>
                <button
                  id="download-btn-page"
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
    </div>

    {{-- Inline script para lanzar el SweetAlert --}}
    <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Este SweetAlert viene de base.blade.php
      const btn = document.getElementById('download-btn-page');
      if (!window.electronAPI && btn) {
        // mostramos el contenedor browser-only
        document.getElementById('browser-only').classList.remove('hidden');
        btn.addEventListener('click', function(){
          Swal.fire({
            title: '¿Confirmas la descarga?',
            text: 'Se descargará el instalador de Gamora Desktop.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, descargar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            customClass: {
              popup:   'bg-gradient-to-br from-purple-800 to-purple-600 text-white rounded-2xl p-6',
              title:   'text-2xl font-bold mb-2',
              content: 'text-base'
            }
          }).then(function(result){
            if (result.isConfirmed) {
              window.location.href = btn.getAttribute('data-url');
            }
          });
        });
      }
    });
    </script>

  </x-self.base>
</x-app-layout>
