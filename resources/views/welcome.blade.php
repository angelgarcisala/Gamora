<x-app-layout>
    <x-self.base>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const logoContainer = document.getElementById('logo-container');
                const logoSvg = document.getElementById('logo-svg');
                const uiContainer = document.getElementById('ui-container');

                const initialScale = 3;
                const finalScale = 0.5;
                const movePercentageX = 0.4;
                const movePercentageY = 0.4;
                const waitTimeMs = 4000;
                const transitionDuration = '1.2s';
                const easingType = 'ease-in-out';
                let moved = false;

                function setInitialState() {
                    logoSvg.style.transform = `scale(${initialScale}) translate(0px, 0px)`;
                }

                function applyMovement() {
                    const translateX = -(window.innerWidth * movePercentageX);
                    const translateY = -(window.innerHeight * movePercentageY);
                    logoSvg.style.transition = `transform ${transitionDuration} ${easingType}`;
                    logoSvg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${finalScale})`;
                }

                setInitialState();

                setTimeout(() => {
                    applyMovement();
                    moved = true;

                    setTimeout(() => {
                        uiContainer.classList.remove('opacity-0', 'translate-y-10');
                        uiContainer.classList.add('opacity-100', 'translate-y-0');
                    }, 1200);
                }, waitTimeMs);

                window.addEventListener('resize', () => {
                    if (moved) {
                        applyMovement();
                    } else {
                        setInitialState();
                    }
                });
            });
        </script>

        <div class="relative w-screen h-screen overflow-hidden">
            <div id="logo-container" class="absolute inset-0 flex items-center justify-center transition-transform duration-1000 ease-in-out z-20">
                <div id="logo-svg" class="transition-transform duration-1000 ease-in-out">
                    {!! file_get_contents(public_path('storage/media/Gamora-gradient-faster.svg')) !!}
                </div>
            </div>

            <div id="ui-container" class="absolute inset-0 flex flex-col opacity-0 translate-y-10 transition-all duration-1000 ease-in-out z-10 bg-steam-gradient">

                <nav class="flex items-center justify-between px-8 py-4">
                    <div class="flex items-center space-x-4"></div>
                    <div class="flex space-x-8 text-white font-semibold text-lg">
                        <a href="#" class="hover:text-purple-400 transition">Inicio</a>
                        <a href="#" class="hover:text-purple-400 transition">Tienda</a>
                        <a href="#" class="hover:text-purple-400 transition">Biblioteca</a>
                        <a href="#" class="hover:text-purple-400 transition">Comunidad</a>
                    </div>
                </nav>

                <main class="flex-1 p-8 overflow-y-auto">
                    <h1 class="text-5xl text-purple-300 font-bold mb-8 mt-20">Destacados</h1>

                    <section id="carousel" class="bg-purple-800/30 rounded-xl p-6 flex flex-col md:flex-row items-center gap-6 mb-12 relative max-w-5xl mx-auto h-[28rem]">
                        <button id="prevBtn" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-purple-900/50 p-2 rounded-full text-white hover:bg-purple-700 z-10">
                            &#8592;
                        </button>

                        <div class="flex-1 h-full flex items-center justify-center">
                            <img src="storage/media/Gamora-loading.svg" id="mainImage" alt="Juego Destacado" class="rounded-lg shadow-lg object-cover h-full">
                        </div>

                        <div class="w-full md:w-1/3 text-white space-y-4">
                            <h2 id="gameTitle" class="text-3xl font-bold">Nombre del Juego</h2>
                            <div id="thumbnails" class="grid grid-cols-3 gap-2"></div>
                            <div id="tags" class="flex flex-wrap gap-2 mt-2"></div>
                        </div>

                        <button id="nextBtn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-purple-900/50 p-2 rounded-full text-white hover:bg-purple-700 z-10">
                            &#8594;
                        </button>

                        <div id="indicators" class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2"></div>
                    </section>
                </main>

                <footer class="text-center py-4 text-purple-400 text-sm bg-gradient-to-t from-black via-gray-900 to-transparent">
                    © 2025 Gamora. Todos los derechos reservados.
                </footer>
            </div>
        </div>

    </x-self.base>
</x-app-layout>
