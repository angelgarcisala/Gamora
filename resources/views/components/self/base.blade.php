@props([])

{{-- 1) Pre-pintado: si ya vimos el logo, detenemos sólo las animaciones internas y mostramos la UI --}}
<script>
(function(){
    const key    = 'logoAnimated';
    if (!sessionStorage.getItem(key)) return;
    const css = `
      /* Pausar todas las CSS animations dentro del SVG */
      #logo-svg, #logo-svg * {
        animation-play-state: paused !important;
        animation-fill-mode: forwards !important;
        animation-delay: -9999s !important;
      }
      /* UI ya visible sin delay */
      #ui-container {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      /* Desbloquear clicks desde el inicio */
      #logo-container {
        pointer-events: none !important;
      }
    `;
    const st = document.createElement('style');
    st.innerHTML = css;
    document.head.appendChild(st);
})();
</script>

{{-- 2) DOMContentLoaded: solo en primera visita corre la animación; en las demás fija inline el transform final --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const key           = 'logoAnimated';
    const logoContainer = document.getElementById('logo-container');
    const wrapper       = document.getElementById('logo-svg');
    const logoSvgEl     = wrapper.querySelector('svg');
    const uiContainer   = document.getElementById('ui-container');

    const initialScale   = 3;
    const finalScale     = 0.5;
    const movePctX       = 0.4;
    const movePctY       = 0.4;
    const waitBeforeMove = 4000;
    const moveDuration   = 1200;
    let moved = false, resizeTO;

    function finalTransform() {
        return {
            tx: -(window.innerWidth * movePctX),
            ty: -(window.innerHeight * movePctY)
        };
    }

    function animateToCorner() {
        const { tx, ty } = finalTransform();
        wrapper.style.transition = `transform ${moveDuration}ms ease-in-out`;
        wrapper.style.transform  = `translate(${tx}px, ${ty}px) scale(${finalScale})`;
    }

    function stopBlocking() {
        logoContainer.style.pointerEvents = 'none';
    }

    if (sessionStorage.getItem(key)) {
        // ── Visita posterior ──
        moved = true;
        // Fijamos inline estado final SIN animar:
        const { tx, ty } = finalTransform();
        wrapper.style.transition = 'none';
        wrapper.style.transform  = `translate(${tx}px, ${ty}px) scale(${finalScale})`;
        stopBlocking();

        // Pausamos SMIL (si existiera) para que esté “completo”
        if (logoSvgEl && logoSvgEl.pauseAnimations) {
            try { logoSvgEl.pauseAnimations(); } catch {}
        }
        const anims = logoSvgEl?.querySelectorAll('animate, animateTransform, set');
        anims?.forEach(a => { try { a.endElement(); } catch {} });

        // Y listo: no tocamos el UIContainer porque el CSS pre-pintado ya lo dejó visible.
    } else {
        // ── Primera visita ──
        // 1) Estado inicial centrado y grande
        wrapper.style.transform = `scale(${initialScale})`;

        // 2) Tras el delay, animamos a la esquina y luego mostramos la UI
        setTimeout(() => {
            animateToCorner();
            moved = true;
            setTimeout(() => {
                uiContainer.classList.remove('opacity-0','translate-y-10');
                uiContainer.classList.add   ('opacity-100');
                stopBlocking();
                sessionStorage.setItem(key,'true');
            }, moveDuration);
        }, waitBeforeMove);
    }

    // ── Resize: reposicionamos con animación siempre que ya esté movido ──
    window.addEventListener('resize', () => {
        if (!moved) return;
        logoContainer.style.pointerEvents = 'auto';
        animateToCorner();
        clearTimeout(resizeTO);
        resizeTO = setTimeout(stopBlocking, moveDuration + 100);
    });

    // ── Detección Electron (si aplica) ──
    if (window.electronAPI?.isElectron) {
        document.getElementById('electron-only')?.classList.remove('hidden');
    }
});
</script>

<div class="relative w-screen h-screen overflow-hidden bg-steam-gradient">
  {{-- Splash/logo bloqueando clicks --}}
  <div id="logo-container"
       class="absolute inset-0 flex items-center justify-center z-30">
    <div id="logo-svg" class="transition-transform duration-1000 ease-in-out">
      {!! file_get_contents(public_path('storage/media/Gamora-gradient-faster.svg')) !!}
    </div>
  </div>

  {{-- Nav + slot + footer --}}
  <div id="ui-container"
       class="absolute inset-0 flex flex-col opacity-0 translate-y-10 transition-all duration-1000 ease-in-out z-10">
    <nav class="flex items-center justify-between px-8 py-4">
      <div class="flex items-center space-x-4"></div>
      <div class="flex space-x-8 text-white font-semibold text-lg">
        <a href="#" class="hover:text-purple-400 transition">Inicio</a>
        <a href="#" class="hover:text-purple-400 transition">Tienda</a>
        <a href="#" class="hover:text-purple-400 transition">Biblioteca</a>
        <a href="#" class="hover:text-purple-400 transition">Comunidad</a>

      @guest
        <div class="flex space-x-4">
          <a href="{{ route('login') }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
            Iniciar sesión
          </a>
          <a href="{{ route('register') }}"
            class="px-4 py-2 border border-indigo-600 hover:bg-indigo-50 text-indigo-600 rounded-lg transition">
            Registrarse
          </a>
        </div>
      @else
        <div class="flex items-center space-x-4">
          <a href="{{ route('profile.show') }}"
            class="px-3 py-2 hover:text-purple-300 transition">
            {{ Auth::user()->name }}
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition">
              Cerrar sesión
            </button>
          </form>
        </div>
      @endguest
      </div>
    </nav>

    <main class="flex-1 p-8 overflow-y-auto">
      {{ $slot }}
    </main>

    <footer class="text-center py-4 text-purple-400 text-sm bg-gradient-to-t from-black via-gray-900 to-transparent">
      © 2025 Gamora. Todos los derechos reservados.
    </footer>
  </div>
</div>
