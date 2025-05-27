<style>
  @keyframes float {
    0%, 100% {
      transform: translateY(0) rotate(-2deg);
    }
    50% {
      transform: translateY(-10px) rotate(2deg);
    }
  }
  .float-space {
    animation: float 6s ease-in-out infinite;
  }
  .dynamic-floating {
    position: absolute;      /* Relativo al contenedor .page-container */
    width: 3rem;             /* tamaño 12 */
    pointer-events: none;
    transition: opacity 1s ease-in-out;
    opacity: 1;              /* visibles desde el inicio */
  }
</style>

<x-app-layout>
    <x-self.base>
        <div class="relative page-container">
            {{-- Generamos 15 mandos en posiciones absolutas dentro del flujo de la página --}}
            @php
              $files = ['mando.png','mando2.png','mando3.png', 'mando4.png'];
            @endphp
            @for ($i = 0; $i < 10; $i++)
              <img
                src="{{ asset('storage/media/'.$files[$i % 4]) }}"
                alt="Mando {{ $i+1 }}"
                class="dynamic-floating float-space"
                data-index="{{ $i }}"
              />
            @endfor

            {{-- Header --}}
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Profile') }}
                </h2>
            </x-slot>

            {{-- Contenido principal --}}
            <div class="max-w-5xl mx-auto py-10 space-y-12 px-4 sm:px-6 lg:px-8">
                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    <section class="bg-black/10 backdrop-blur-md rounded-2xl shadow-xl p-6">
                        @livewire('profile.update-profile-information-form')
                    </section>
                @endif

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                    <section class="bg-black/10 backdrop-blur-md rounded-2xl shadow-xl p-6">
                        @livewire('profile.update-password-form')
                    </section>
                @endif

                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                    <section class="bg-black/10 backdrop-blur-md rounded-2xl shadow-xl p-6">
                        @livewire('profile.two-factor-authentication-form')
                    </section>
                @endif

                <section class="bg-black/10 backdrop-blur-md rounded-2xl shadow-xl p-6">
                    @livewire('profile.logout-other-browser-sessions-form')
                </section>

                @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                    <section class="bg-black/10 backdrop-blur-md rounded-2xl shadow-xl p-6">
                        @livewire('profile.delete-user-form')
                    </section>
                @endif
            </div>
        </div>
    </x-self.base>

    {{-- Script para posicionar en todo el scroll de la página --}}
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('.page-container');
        const imgs = container.querySelectorAll('.dynamic-floating');
        const W = container.clientWidth;
        const H = container.scrollHeight;

        imgs.forEach(img => {
          // posición inicial aleatoria
          const initX = Math.random() * (W - img.clientWidth);
          const initY = Math.random() * (H - img.clientHeight);
          img.style.left = `${initX}px`;
          img.style.top  = `${initY}px`;

          const cycle = () => {
            // permanecen visibles 1–4s
            const visibleFor = 1000 + Math.random() * 3000;
            setTimeout(() => {
              img.style.opacity = 0;
              // tras 1s, reposicionan y reaparecen
              setTimeout(() => {
                const x = Math.random() * (W - img.clientWidth);
                const y = Math.random() * (H - img.clientHeight);
                img.style.left = `${x}px`;
                img.style.top  = `${y}px`;
                img.style.opacity = 1;
                cycle();
              }, 1000);
            }, visibleFor);
          };

          // empezamos el ciclo con un pequeño retraso
          setTimeout(cycle, 500 + Math.random() * 500);
        });
      });
    </script>
</x-app-layout>