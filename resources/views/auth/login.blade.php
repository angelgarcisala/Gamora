<x-guest-layout>
  {{-- Contenedor full‐screen con tu gradiente --}}
  <div class="min-h-screen flex flex-col items-center justify-center bg-steam-gradient px-4 sm:px-6 lg:px-8">

    {{-- ← Volver --}}
    <a href="{{ url()->previous() }}"
       class="self-start mb-6 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition">
      ← Volver
    </a>

    {{-- Tarjeta semitransparente con blur --}}
    <div class="w-full max-w-md bg-white/10 backdrop-blur-md rounded-2xl shadow-xl p-8">
      {{-- Logo Gamora --}}
      <div class="flex justify-center mb-6">
        <img src="{{ Storage::disk('s3')->url('storage/media/gamora-gradient-faster.svg') }}"
             alt="Gamora"/>
      </div>

      {{-- Errores / estado --}}
      <x-validation-errors class="mb-4 text-red-400" />

      @session('status')
        <div class="mb-4 text-sm text-green-300">
          {{ $value }}
        </div>
      @endsession

      {{-- Formulario --}}
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
          <x-label for="email" value="Email" class="text-purple-200" />
          <x-input id="email" 
                   class="block mt-1 w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500" 
                   type="email" name="email" 
                   :value="old('email')" required autofocus 
                   placeholder="tú@correo.com" />
        </div>

        <div class="mb-4">
          <x-label for="password" value="Contraseña" class="text-purple-200" />
          <x-input id="password" 
                   class="block mt-1 w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500" 
                   type="password" name="password" required 
                   placeholder="••••••••" />
        </div>

        <div class="flex items-center justify-between mb-6">
          <label for="remember_me" class="flex items-center space-x-2 text-purple-200">
            <x-checkbox id="remember_me" name="remember" class="text-purple-500" />
            <span class="text-sm">Recuérdame</span>
          </label>
          @if (Route::has('password.request'))
            <a class="text-sm underline text-purple-300 hover:text-purple-100"
               href="{{ route('password.request') }}">
              ¿Olvidaste tu contraseña?
            </a>
          @endif
        </div>

        <div>
          <button type="submit"
                  class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg transition">
            Iniciar sesión
          </button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>
