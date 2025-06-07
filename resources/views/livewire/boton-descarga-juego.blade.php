<div x-data="gameManager()" x-init="checkEstadoJuego()" class="space-y-4">

    {{-- Botón DESCARGAR --}}
    <template x-if="!descargando && !descargado">
        <button @click="descargarJuego()"
                class="bg-purple-600 hover:bg-purple-500 text-white font-bold py-2 px-4 rounded shadow">
            Descargar
        </button>
    </template>

    {{-- Barra de progreso --}}
    <template x-if="descargando">
        <div>
            <div class="text-sm text-purple-300 mb-1">
                Descargando... <span x-text="progreso + '%'"></span>
            </div>
            <div class="w-full bg-purple-900 rounded h-4 overflow-hidden">
                <div class="h-full bg-purple-400 transition-all" :style="`width: ${progreso}%`"></div>
            </div>
        </div>
    </template>

    {{-- Botón JUGAR --}}
    <template x-if="descargado">
        <button @click="jugarJuego()"
                class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded shadow">
            Jugar
        </button>
    </template>

    <script>
        function gameManager() {
            return {
                descargando: false,
                descargado: false,
                progreso: 0,
                exePath: null,

                checkEstadoJuego() {
                    const exePath = this.buildExePath();
                    window.electronAPI.checkIfFileExists(exePath, (exists) => {
                        if (exists) {
                            this.descargado = true;
                            this.exePath = exePath;
                        }
                    });
                },

                buildExePath() {
                    const ejecutable = "{{ $juego->nombre_ejecutable }}";
                    const gameFolder = "{{ Str::slug($juego->titulo) }}";
                    const path = window.electronAPI.getExecutablePath(gameFolder, ejecutable);
                    console.log('[DEBUG Blade] Ejecutable construido:', path);
                    return path;
                },

                descargarJuego() {
                    this.descargando = true;
                    const ejecutable = "{{ $juego->nombre_ejecutable }}";
                    const gameFolder = "{{ Str::slug($juego->titulo) }}";
                    // Usamos directamente la URL absoluta de S3 que tienes en url_descarga
                    const url = "{{ $juego->url_descarga }}";

                    window.electronAPI.downloadGameWithProgress(
                        url,
                        ejecutable,
                        gameFolder,
                        (percent) => { this.progreso = percent; },
                        () => {
                            this.descargando = false;
                            this.descargado = true;
                            this.exePath = this.buildExePath();
                        },
                        (error) => {
                            alert("Error al descargar el juego: " + error);
                            this.descargando = false;
                        }
                    );
                },

                jugarJuego() {
                    console.log('DEBUG: Ruta final del .exe que se intenta lanzar: ', this.exePath);
                    if (this.exePath) {
                        window.electronAPI.launchGame(this.exePath);
                    } else {
                        alert("No se encontró el ejecutable.");
                    }
                }
            }
        }
    </script>

</div>
