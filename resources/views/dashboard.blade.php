<x-app-layout>
    <x-self.base>
        <div class="max-w-5xl mx-auto py-8">
            <h2 class="text-xl font-bold text-purple-200 mb-4 uppercase tracking-wide">
                Destacados y recomendados
            </h2>

            <div id="carouselContainer"
                 class="relative flex bg-purple-900/30 rounded-lg overflow-hidden shadow-lg"
                 style="height: 340px;">

                @foreach ($juegos as $index => $juego)
                    @php
                        // Elegir video si existe, si no imagen
                        $primary = $juego->multimedias
                                       ->firstWhere('tipo','video')
                                   ?: $juego->multimedias
                                       ->firstWhere('tipo','imagen')
                                   ?: $juego->multimedias->first();
                        $url        = $primary ? asset($primary->url) : null;
                        $miniaturas = $juego->multimedias
                                            ->where('tipo','imagen')
                                            ->take(4);
                    @endphp

                    <div class="carousel-slide absolute inset-0 flex w-full h-full transition-opacity duration-700 ease-in-out
                         {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                         data-index="{{ $index }}">

                        {{-- Media destacada --}}
                        <div class="w-2/3 bg-black flex items-center justify-center p-2">
                            @if($primary && $primary->tipo === 'video')
                                <video autoplay muted loop playsinline
                                       class="main-video max-h-full h-full w-auto object-contain rounded shadow-lg opacity-100 transition-opacity duration-500 ease-in-out">
                                    <source src="{{ $url }}" type="video/mp4">
                                </video>
                            @elseif($url)
                                <img id="mainImage-{{ $index }}"
                                     src="{{ $url }}"
                                     alt="{{ $juego->titulo }}"
                                     onerror="this.src='{{ asset('storage/media/Gamora-loading.svg') }}'"
                                     class="main-image max-h-full h-full w-auto object-contain rounded shadow-lg opacity-100 transition-opacity duration-500 ease-in-out">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-black">
                                    {!! file_get_contents(public_path('storage/media/Gamora-loading.svg')) !!}
                                </div>
                            @endif
                        </div>

                        {{-- Panel derecho --}}
                        <div class="w-1/3 p-4 text-white flex flex-col justify-between bg-gradient-to-b from-purple-950 to-purple-800">
                            <div>
                                <h3 class="text-base font-bold mb-2">
                                    <a href="{{ route('juegos.show', $juego) }}"
                                       class="hover:text-purple-300 transition">
                                        {{ $juego->titulo }}
                                    </a>
                                </h3>

                                @if($miniaturas->count())
                                    <div class="grid grid-cols-2 gap-1 mb-2">
                                        @foreach ($miniaturas as $media)
                                            <img src="{{ asset($media->url) }}"
                                                 data-src="{{ asset($media->url) }}"
                                                 alt="Miniatura"
                                                 class="thumbnail-preview w-full h-16 object-cover rounded shadow transition-opacity duration-300 ease-in-out"
                                                 data-slide-index="{{ $index }}"
                                                 onerror="this.style.display='none';">
                                        @endforeach
                                    </div>
                                @endif

                                <p class="text-xs text-purple-200">
                                    {{ Str::limit($juego->descripcion, 80) }}
                                </p>
                            </div>

                            <div class="flex justify-between items-center mt-2">
                                <span class="bg-green-600 text-[10px] px-2 py-1 rounded uppercase font-semibold">
                                    Lo más vendido
                                </span>
                                <div class="text-right text-sm font-bold">
                                    {{ $juego->precio }}€
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Botones Prev/Next --}}
                <button id="prevBtn"
                        class="absolute left-1 top-1/2 transform -translate-y-1/2 bg-purple-700 hover:bg-purple-600 text-white w-8 h-8 rounded-full z-10 text-sm">
                    &#8592;
                </button>
                <button id="nextBtn"
                        class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-purple-700 hover:bg-purple-600 text-white w-8 h-8 rounded-full z-10 text-sm">
                    &#8594;
                </button>
            </div>

            {{-- Indicadores --}}
            <div id="carouselIndicators" class="flex justify-center gap-1 mt-2">
                @foreach ($juegos as $i => $juego)
                    <button class="indicator w-3 h-3 rounded-full transition-all duration-300
                           {{ $i === 0 ? 'bg-white scale-110' : 'bg-white/30' }}"
                            data-index="{{ $i }}">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Script del carrusel --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const slides      = document.querySelectorAll('.carousel-slide');
                const indicators  = document.querySelectorAll('.indicator');
                const intervalTime = 10000; // 10 segundos
                let   currentIndex = 0;
                let   interval     = null;

                function showSlide(idx) {
                    slides.forEach((s,i) => {
                        s.classList.toggle('opacity-100', i===idx);
                        s.classList.toggle('z-10',       i===idx);
                        s.classList.toggle('opacity-0',   i!==idx);
                        s.classList.toggle('z-0',        i!==idx);
                        // reiniciar playback si es video
                        const vid = s.querySelector('video');
                        if (vid) {
                            vid.currentTime = 0;
                            vid.play();
                        }
                    });
                    indicators.forEach((d,i) => {
                        d.classList.toggle('bg-white',    i===idx);
                        d.classList.toggle('scale-110',    i===idx);
                        d.classList.toggle('bg-white/30',  i!==idx);
                    });
                }

                function resetInterval() {
                    clearInterval(interval);
                    interval = setInterval(() => {
                        const currentSlide = slides[currentIndex];
                        // solo auto-avanza si no hay video
                        if (!currentSlide.querySelector('video')) {
                            currentIndex = (currentIndex+1) % slides.length;
                            showSlide(currentIndex);
                        }
                    }, intervalTime);
                }

                document.getElementById('prevBtn').onclick = () => {
                    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                    showSlide(currentIndex);
                    resetInterval();
                };
                document.getElementById('nextBtn').onclick = () => {
                    currentIndex = (currentIndex + 1) % slides.length;
                    showSlide(currentIndex);
                    resetInterval();
                };
                indicators.forEach(dot => {
                    dot.onclick = () => {
                        currentIndex = +dot.dataset.index;
                        showSlide(currentIndex);
                        resetInterval();
                    };
                });

                // miniaturas: hover para cambiar principal
                document.querySelectorAll('.thumbnail-preview').forEach(thumb => {
                    const idx      = thumb.dataset.slideIndex;
                    const slide    = slides[idx];
                    const mainEl   = slide.querySelector('.w-2\\/3');
                    const origHTML = mainEl.innerHTML;

                    thumb.addEventListener('mouseenter', () => {
                        mainEl.innerHTML = `<img src="${thumb.dataset.src}" class="max-h-full h-full w-auto object-contain rounded shadow-lg">`;
                    });
                    thumb.addEventListener('mouseleave', () => {
                        mainEl.innerHTML = origHTML;
                        // reattach video if needed
                        const vid = mainEl.querySelector('video');
                        if (vid) vid.play();
                    });
                });

                // iniciar
                showSlide(0);
                resetInterval();
            });
        </script>
    </x-self.base>
</x-app-layout>
