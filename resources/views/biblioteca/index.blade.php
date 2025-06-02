{{-- resources/views/biblioteca/index.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      <h1 class="text-3xl font-bold text-white mb-6">Mi Biblioteca</h1>

      @if($juegos->isEmpty())
        <p class="text-purple-200">Aún no has comprado ningún juego.</p>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($juegos as $juego)
            @php
              $thumb = $juego->multimedias
                       ->firstWhere('tipo','imagen')
                       ?->url
                       ?? 'https://via.placeholder.com/400x225?text=Sin+imagen';
            @endphp

            <a 
              href="{{ route('juegos.show', $juego) }}"
              class="group block 
                     bg-gradient-to-br from-purple-800 to-purple-900 
                     rounded-2xl overflow-hidden shadow-xl 
                     transform hover:scale-105 transition"
            >
              <div class="relative">
                <img 
                  src="{{ asset($thumb) }}" 
                  alt="{{ $juego->titulo }}" 
                  class="w-full h-44 object-cover"
                >
              </div>

              <div class="p-4 space-y-1">
                <h3 class="text-lg font-semibold text-white 
                           group-hover:text-purple-300 transition">
                  {{ Str::limit($juego->titulo, 30) }}
                </h3>
                <p class="text-purple-200 text-sm">
                  Dev: {{ $juego->desarrollador }}
                </p>
                <p class="text-purple-400 text-xs">
                  {{ optional($juego->fecha_lanzamiento)->format('d/m/Y') }}
                </p>
              </div>
            </a>
          @endforeach
        </div>
      @endif

    </div>
  </x-self.base>
</x-app-layout>
