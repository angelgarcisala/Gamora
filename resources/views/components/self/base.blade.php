{{-- resources/views/components/self/base.blade.php --}}
@props([])

{{-- 1) Pre-pintado: si ya vimos el logo, pausamos internals y mostramos la UI --}}
<script>
(function(){
    const key = 'logoAnimated';
    if (!sessionStorage.getItem(key)) return;
    const css = `
      /* Pausar animaciones internas del SVG */
      #logo-svg, #logo-svg * {
        animation-play-state: paused !important;
        animation-fill-mode: forwards !important;
        animation-delay: -9999s !important;
      }
      /* Mostrar la UI sin delay */
      #ui-container {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      /* Wrapper bloquea todo */
      #logo-container,
      #logo-container * {
        pointer-events: none !important;
      }
      /* El enlace dentro de #logo-container debe ser clicable */
      #logo-link,
      #logo-link * {
        pointer-events: auto !important;
      }
    `;
    const st = document.createElement('style');
    st.innerHTML = css;
    document.head.appendChild(st);
})();
</script>

{{-- 2) Animación en DOMContentLoaded --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const key         = 'logoAnimated';
    const logoCont    = document.getElementById('logo-container');
    const uiContainer = document.getElementById('ui-container');
    const svgWrapper  = document.getElementById('logo-svg');

    const initialScale = 3, finalScale = 0.5;
    const movePctX     = 0.4, movePctY = 0.4;
    const waitBefore   = 4000, moveDuration = 1200;
    let moved = false, resizeTO;

    function finalTransform() {
      return {
        tx: -(window.innerWidth * movePctX),
        ty: -(window.innerHeight * movePctY)
      };
    }

    function animateToCorner() {
      const { tx, ty } = finalTransform();
      svgWrapper.style.transition = `transform ${moveDuration}ms ease-in-out`;
      svgWrapper.style.transform  = `translate(${tx}px, ${ty}px) scale(${finalScale})`;
    }

    function stopBlocking() {
      logoCont.style.pointerEvents = 'none';
    }

    if (sessionStorage.getItem(key)) {
      moved = true;
      const { tx, ty } = finalTransform();
      svgWrapper.style.transition = 'none';
      svgWrapper.style.transform  = `translate(${tx}px, ${ty}px) scale(${finalScale})`;
      stopBlocking();
    } else {
      svgWrapper.style.transform = `scale(${initialScale})`;
      setTimeout(() => {
        animateToCorner();
        moved = true;
        setTimeout(() => {
          uiContainer.classList.replace('opacity-0','opacity-100');
          uiContainer.classList.replace('translate-y-10','translate-y-0');
          stopBlocking();
          sessionStorage.setItem(key,'true');
        }, moveDuration);
      }, waitBefore);
    }

    window.addEventListener('resize', () => {
      if (!moved) return;
      logoCont.style.pointerEvents = 'auto';
      animateToCorner();
      clearTimeout(resizeTO);
      resizeTO = setTimeout(stopBlocking, moveDuration + 100);
    });

    const dashUrl = '{{ route("dashboard") }}';
    svgWrapper.addEventListener('click', () => {
      if (sessionStorage.getItem(key)) {
        window.location = dashUrl;
      }
    });
});
</script>

{{-- Inclusión de SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  use Illuminate\Support\Facades\Storage;
  $logo = Storage::disk('s3')->url('storage/media/Gamora-gradient-faster.svg');
  $downloadUrl = Storage::disk('s3')->url('download/Gamora Setup 1.0.0.exe');
@endphp

<div class="relative w-screen h-screen overflow-hidden bg-steam-gradient">

  {{-- SVG central animado --}}
  <div id="logo-container" class="absolute inset-0 flex items-center justify-center z-30 transition-opacity duration-300">
    <div id="logo-svg" class="transition-transform duration-1000 ease-in-out cursor-pointer">
      <a id="logo-link" href="{{ route('dashboard') }}">
        <img src="{{ $logo }}" alt="Logo">
      </a>
    </div>
  </div>

  <div id="ui-container" class="absolute inset-0 flex flex-col opacity-0 translate-y-10 transition-all duration-1000 ease-in-out z-10">

    {{-- ——— MÓVIL: botón hamburguesa ——— --}}
    <div class="flex items-center justify-end px-8 py-4 md:hidden">
      <button id="menu-toggle" class="text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    {{-- ——— MÓVIL: drawer off-canvas arriba ——— --}}
    <div id="mobile-drawer"
         class="fixed inset-0 bg-black bg-opacity-50 transform -translate-y-full transition-transform duration-300 ease-in-out z-40 md:hidden">
      <div class="flex justify-end p-4">
        <button id="drawer-close" class="text-white focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <nav class="flex flex-col items-center space-y-6 mt-8">
        <a href="{{ route('dashboard') }}" class="text-white text-xl hover:text-purple-400 transition">Inicio</a>
        <a href="{{ route('tienda.index') }}" class="text-white text-xl hover:text-purple-400 transition">Tienda</a>
        <a href="{{ route('biblioteca.index') }}" class="text-white text-xl hover:text-purple-400 transition">Biblioteca</a>
        <a href="{{ route('juegos.create') }}" class="text-white text-xl hover:text-purple-400 transition">Subir juego</a>

        {{-- Enlace descarga móvil --}}
        <button id="download-btn-mobile"
                data-url="{{ $downloadUrl }}"
                class="text-white text-xl hover:text-green-300 transition">
          Descargar Desktop
        </button>

        @auth
          <a href="{{ route('juegos.mis_juegos') }}" class="text-white text-xl hover:text-purple-400 transition">Mis Juegos</a>
          <a href="{{ route('profile.show') }}" class="text-white text-xl hover:text-purple-400 transition">{{ Auth::user()->name }}</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">Cerrar sesión</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Iniciar sesión</a>
          <a href="{{ route('register') }}" class="px-6 py-2 border border-indigo-600 hover:bg-indigo-50 text-indigo-600 rounded-lg transition">Registrarse</a>
        @endauth
      </nav>
    </div>

    {{-- ——— ESCRITORIO: menú original + botón descarga antes de logout/iniciar ——— --}}
    <nav class="hidden md:flex items-center justify-end px-8 py-4 space-x-6">
      <div class="flex space-x-8 text-white font-semibold text-lg">
        <a href="{{ route('dashboard') }}" class="hover:text-purple-400 transition">Inicio</a>
        <a href="{{ route('tienda.index') }}" class="hover:text-purple-400 transition">Tienda</a>
        <a href="{{ route('biblioteca.index') }}" class="hover:text-purple-400 transition">Biblioteca</a>
        <a href="{{ route('juegos.create') }}" class="hover:text-purple-400 transition">Subir juego</a>
        @auth
          <a href="{{ route('juegos.mis_juegos') }}" class="hover:text-purple-400 transition">Mis Juegos</a>
        @endauth
      </div>

      {{-- Enlace descarga desktop --}}
      <button id="download-btn-desktop"
              data-url="{{ $downloadUrl }}"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
        Descargar Desktop
      </button>

      @auth
        <div class="flex items-center space-x-4">
          <a href="{{ route('profile.show') }}" class="text-white hover:text-purple-400">{{ Auth::user()->name }}</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">Cerrar sesión</button>
          </form>
        </div>
      @else
        <div class="flex space-x-4">
          <a href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Iniciar sesión</a>
          <a href="{{ route('register') }}" class="px-4 py-2 border border-indigo-600 hover:bg-indigo-50 text-indigo-600 rounded-lg transition">Registrarse</a>
        </div>
      @endauth
    </nav>

    {{-- Saldo justo debajo de logout/perfil --}}
    @auth
      <div class="px-8 text-right text-purple-200 font-medium">
        Saldo: €{{ number_format(auth()->user()->sueldo, 2) }}
      </div>
    @endauth

    <main class="flex-1 p-8 overflow-y-auto">
      {{ $slot }}
    </main>

    <footer class="text-center py-4 text-purple-400 text-sm bg-gradient-to-t from-black via-gray-900 to-transparent">
      © 2025 Gamora. Todos los derechos reservados.
    </footer>
  </div>

  {{-- 3) Script para drawer, SweetAlert2, detección Browser/Electron --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toggle     = document.getElementById('menu-toggle');
      const drawer     = document.getElementById('mobile-drawer');
      const closeBtn   = document.getElementById('drawer-close');
      const logoCont   = document.getElementById('logo-container');
      const btnDesk    = document.getElementById('download-btn-desktop');
      const btnMobile  = document.getElementById('download-btn-mobile');

      // drawer móvil
      if (toggle && drawer && closeBtn) {
        toggle.addEventListener('click', () => drawer.classList.remove('-translate-y-full'));
        closeBtn.addEventListener('click', () => drawer.classList.add('-translate-y-full'));
      }

      // confirmación SweetAlert2
      function confirmDownload(url) {
        Swal.fire({
          title: '¿Confirmas la descarga?',
          text: 'Se descargará el instalador de Gamora Desktop.',
          icon: 'question',
          background: '#7c3aed',       // Fondo morado (purple-600)
          color: '#fff',               // Texto en blanco
          showCancelButton: true,
          confirmButtonText: 'Sí, descargar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#6d28d9', // purple-700
          cancelButtonColor: '#6b7280'   // gray-500
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = url;
          }
        });
      } 
      if (btnDesk)   btnDesk.addEventListener('click', () => confirmDownload(btnDesk.dataset.url));
      if (btnMobile) btnMobile.addEventListener('click', () => confirmDownload(btnMobile.dataset.url));

      // Detectar Browser vs Electron
      if (window.electronAPI) {
        document.getElementById('browser-only')?.classList.add('hidden');
      } else {
        document.getElementById('browser-only')?.classList.remove('hidden');
      }
    });
  </script>
</div>
