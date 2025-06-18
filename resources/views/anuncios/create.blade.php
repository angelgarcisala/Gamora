{{-- resources/views/anuncios/create.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="min-h-screen flex items-start justify-center py-16 px-4 sm:px-6 lg:px-8 relative">
      {{-- Volver --}}
      <a href="{{ url()->previous() }}"
         class="absolute top-6 left-6 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition">
        ← Volver
      </a>

      <div class="w-full max-w-2xl space-y-8">
        <h2 class="text-2xl font-bold text-white text-center">
          Crear Anuncio para “{{ $juego->titulo }}”
        </h2>
        <x-validation-errors class="text-red-400 mb-4" />

        <form method="POST"
              action="{{ route('anuncios.store', $juego) }}"
              class="space-y-6 bg-gray-800/50 p-8 rounded-xl shadow-lg">
          @csrf

          {{-- Título del anuncio --}}
          <div>
            <x-label for="titulo" value="Título del anuncio" class="text-purple-200" />
            <x-input id="titulo"
                     name="titulo"
                     type="text"
                     :value="old('titulo')"
                     required
                     placeholder="Título breve"
                     class="mt-1 block w-full bg-gray-700/40 placeholder-purple-400 text-white border-none focus:ring-purple-500 focus:border-purple-500 rounded-md p-2" />
          </div>

          {{-- Descripción --}}
          <div>
            <x-label for="descripcion" value="Descripción" class="text-purple-200" />
            <textarea id="descripcion"
                      name="descripcion"
                      rows="4"
                      required
                      placeholder="Descripción del anuncio"
                      class="mt-1 block w-full bg-gray-700/40 placeholder-purple-400 text-white border-none focus:ring-purple-500 focus:border-purple-500 rounded-md p-3">{{ old('descripcion') }}</textarea>
          </div>

          {{-- Fechas de vigencia --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <x-label for="fecha_inicio" value="Fecha de inicio" class="text-purple-200" />
              <x-input id="fecha_inicio"
                       name="fecha_inicio"
                       type="date"
                       :value="old('fecha_inicio')"
                       class="mt-1 block w-full bg-gray-700/40 text-white border-none focus:ring-purple-500 focus:border-purple-500 rounded-md p-2" />
            </div>
            <div>
              <x-label for="fecha_fin" value="Fecha de fin" class="text-purple-200" />
              <x-input id="fecha_fin"
                       name="fecha_fin"
                       type="date"
                       :value="old('fecha_fin')"
                       class="mt-1 block w-full bg-gray-700/40 text-white border-none focus:ring-purple-500 focus:border-purple-500 rounded-md p-2" />
            </div>
          </div>

          {{-- Botón de envío --}}
          <div>
            <button type="submit"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg transition">
              Crear Anuncio
            </button>
          </div>
        </form>
      </div>
    </div>
  </x-self.base>
</x-app-layout>
