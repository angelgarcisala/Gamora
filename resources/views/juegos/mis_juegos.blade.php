{{-- resources/views/juegos/mis_juegos.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      <h1 class="text-3xl font-bold text-white mb-6">Mis Juegos</h1>

      @if($juegos->isEmpty())
        <p class="text-purple-200">Aún no has subido ningún juego.</p>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($juegos as $juego)
            @php
              $thumb = $juego->multimedias
                       ->firstWhere('tipo','imagen')
                       ?->url
                       ?? 'https://via.placeholder.com/400x225?text=Sin+imagen';
            @endphp

            <div class="relative bg-gradient-to-br from-purple-800 to-purple-900 rounded-2xl overflow-hidden shadow-xl transform hover:scale-105 transition">
              {{-- Card --}}
              <a href="{{ route('juegos.show', $juego) }}" class="block">
                <img src="{{ asset($thumb) }}" alt="{{ $juego->titulo }}" class="w-full h-44 object-cover">
                <div class="p-4 space-y-1">
                  <h3 class="text-lg font-semibold text-white">{{ Str::limit($juego->titulo, 30) }}</h3>
                  <p class="text-purple-200 text-sm">Dev: {{ $juego->desarrollador }}</p>
                  <p class="text-purple-400 text-xs">{{ optional($juego->fecha_lanzamiento)->format('d/m/Y') }}</p>
                </div>
              </a>

              {{-- Actions --}}
              <div class="px-4 pb-4 flex justify-between">
                @can('update', $juego)
                  <a href="{{ route('juegos.edit', $juego) }}"
                     class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">
                    Editar
                  </a>
                @endcan

                @can('delete', $juego)
                  <form
                    action="{{ route('juegos.destroy', $juego) }}"
                    method="POST"
                    class="delete-form inline"
                  >
                    @csrf
                    @method('DELETE')
                    <button
                      type="submit"
                      class="px-3 py-1 bg-red-600 hover:bg-red-500 text-white text-sm rounded-lg transition"
                    >
                      Eliminar
                    </button>
                  </form>
                @endcan
              </div>
            </div>
          @endforeach
        </div>
      @endif

    </div>
  </x-self.base>

  {{-- SweetAlert delete confirmation --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: '¿Estás seguro?',
            text: 'Al eliminar este juego, se reembolsará a todos los compradores.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            customClass: {
              popup: 'bg-gray-900 text-white rounded-2xl p-6',
              title: 'text-2xl font-bold mb-2',
              content: 'text-base'
            }
          }).then(result => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });
  </script>
</x-app-layout>
