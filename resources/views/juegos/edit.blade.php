{{-- resources/views/juegos/edit.blade.php --}}
<x-app-layout>
    <x-self.base>
        <div class="min-h-screen flex flex-col items-center justify-center py-8 px-4 sm:px-6 lg:px-8">

            {{-- Botón Volver --}}
            <a href="{{ url()->previous() }}"
                class="absolute top-6 left-6 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>

            <div class="w-full max-w-6xl space-y-8">
                <h2 class="text-3xl font-extrabold text-white text-center mb-6 border-b border-purple-700 pb-4">
                    Editar Juego: <span class="text-purple-400">{{ $juego->titulo }}</span>
                </h2>

                <x-validation-errors class="text-red-400 bg-red-900/20 p-4 rounded-lg mb-6" />

                <form method="POST"
                    action="{{ route('juegos.update', $juego) }}"
                    enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- ================================
                         GRID PRINCIPAL: 2 columnas
                         ================================ --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        {{-- ------------------------------------------------------------
   Columna 1: Multimedia (Existente + Nuevos) – con “X” funcional
   ------------------------------------------------------------ --}}
                        <div
                            x-data='{
        /* 1) Multimedia existente (inyectado con toJson) */
        existingMedia: {!! 
            $juego->multimedias
                ->map(function($m){
                    return [
                        "id"   => $m->id,
                        "url"  => asset($m->url),
                        "tipo" => $m->tipo, // "imagen" o "video"
                    ];
                })
                ->toJson()
        !!},

        /* 2) Arrays para los archivos nuevos */
        imageFiles: [],
        videoFiles: [],

        /* 3) IDs de multimedia existente marcadas para eliminar */
        mediaIdsToRemove: [],

        /* 4) Estado del “drag over” (para cambiar bordes cuando arrastres) */
        dragOver: false,

        /* Función auxiliar: cuenta cuántas imágenes hay “existentes” */
        countExistingImages() {
            return this.existingMedia.filter(m => m.tipo === "imagen").length;
        },
        /* Función auxiliar: cuenta cuántos vídeos hay “existentes” */
        countExistingVideos() {
            return this.existingMedia.filter(m => m.tipo !== "imagen").length;
        },

        /* 5) Procesar archivos soltados */
        onDrop(event) {
            this.dragOver = false;
            this.processFiles(Array.from(event.dataTransfer.files));
        },
        /* 6) Procesar archivos seleccionados vía input */
        onSelect(event) {
            this.processFiles(Array.from(event.target.files));
        },
        /* 7) Añadir archivos nuevos a los arrays, respetando límites totales */
        processFiles(list) {
            // Variable para controlar si se intentó agregar alguna imagen de más
            let excesoImagenes = false;

            list.forEach(file => {
                if (file.type.startsWith("image/")) {
                    // Calcular cuántas imágenes hay en total (existentes + nuevas)
                    const totalImages = this.countExistingImages() + this.imageFiles.length;
                    if (totalImages < 4) {
                        this.imageFiles.push(file);
                    } else {
                        excesoImagenes = true;
                    }
                } else if (
                    ["video/mp4","video/x-matroska","video/webm"].includes(file.type)
                ) {
                    // Calcular cuántos vídeos hay en total (existentes + nuevos)
                    const totalVideos = this.countExistingVideos() + this.videoFiles.length;
                    if (totalVideos < 2) {
                        this.videoFiles.push(file);
                    } else {
                        alert("No puedes tener más de 2 vídeos en total.");
                    }
                } else {
                    alert("Tipo de archivo no soportado: " + file.name);
                }
            });

            // Mostrar un solo aviso si se excedió el límite de imágenes
            if (excesoImagenes) {
                alert("No puedes tener más de 4 imágenes en total.");
            }

            this.$refs.mediaInput.value = null; // permitir reseleccionar mismos archivos
            this.syncMediaInputs();
        },

        /* 8) Eliminar un archivo recién agregado */
        removeNewFile(type, index) {
            if (type === "image") {
                this.imageFiles.splice(index, 1);
            } else {
                this.videoFiles.splice(index, 1);
            }
            this.syncMediaInputs();
        },
        /* 9) Marcar un elemento existente para eliminarlo y quitarlo de la vista */
        removeExistingMedia(index) {
            const id = this.existingMedia[index].id;
            this.mediaIdsToRemove.push(id);
            this.existingMedia.splice(index, 1);
        },

        /* 10) Sincronizar inputs ocultos con archivos nuevos */
        syncMediaInputs() {
            document.querySelectorAll(".js-media-input").forEach(el => el.remove());
            const parentForm = this.$el.closest("form");

            // Inputs de imágenes nuevas
            if (this.imageFiles.length) {
                const dt = new DataTransfer();
                this.imageFiles.forEach(f => dt.items.add(f));
                const inp = document.createElement("input");
                inp.type = "file";
                inp.multiple = true;
                inp.name = "imagenes[]";
                inp.files = dt.files;
                inp.classList.add("js-media-input");
                inp.style.display = "none";
                parentForm.appendChild(inp);
            }

            // Inputs de vídeos nuevos
            if (this.videoFiles.length) {
                const dt2 = new DataTransfer();
                this.videoFiles.forEach(f => dt2.items.add(f));
                const inp2 = document.createElement("input");
                inp2.type = "file";
                inp2.multiple = true;
                inp2.name = "videos[]";
                inp2.files = dt2.files;
                inp2.classList.add("js-media-input");
                inp2.style.display = "none";
                parentForm.appendChild(inp2);
            }
        }
    }'
                            class="space-y-6">
                            {{-- ======== 1) Miniaturas de multimedia existente ======== --}}
                            <div class="bg-gray-800/50 p-6 rounded-xl shadow-lg">
                                <h3 class="text-xl font-semibold text-purple-200 mb-4">
                                    Multimedia (Existente)
                                </h3>

                                <div class="flex flex-wrap gap-4 p-2 bg-gray-700/40 rounded-md max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-purple-600 scrollbar-track-gray-700">
                                    <template x-for="(m, i) in existingMedia" :key="m.id">
                                        <div class="relative w-28 h-20 border border-gray-700 rounded-lg overflow-hidden group shadow-md">
                                            <div class="w-full h-full bg-black flex items-center justify-center">
                                                <template x-if="m.tipo === 'imagen'">
                                                    <img :src="m.url"
                                                        class="w-full h-full object-cover"
                                                        :alt="'Imagen ' + m.id" />
                                                </template>
                                                <template x-if="m.tipo !== 'imagen'">
                                                    <video :src="m.url"
                                                        muted
                                                        playsinline
                                                        loop
                                                        class="w-full h-full object-cover"></video>
                                                </template>
                                            </div>

                                            {{-- Botón “X” para eliminar un elemento existente --}}
                                            <button
                                                type="button"
                                                class="absolute top-1 right-1 bg-red-600/80 hover:bg-red-700 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center transition-colors"
                                                @click.prevent="removeExistingMedia(i)"
                                                title="Eliminar este elemento existente">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Mensaje si no hay nada --}}
                                    <template x-if="existingMedia.length === 0">
                                        <p class="text-gray-400 text-sm">No hay contenido multimedia asociado.</p>
                                    </template>
                                </div>

                                {{-- Nota para el usuario --}}
                                <template x-if="existingMedia.length">
                                    <p class="text-gray-400 text-xs mt-2">
                                        Haz clic en la “X” para marcar el elemento multimedia para su eliminación al guardar.
                                    </p>
                                </template>
                            </div>

                            {{-- ======== 2) Zona de “Arrastra o haz clic para añadir” ======== --}}
                            <div
                                x-on:dragover.prevent="dragOver = true"
                                x-on:dragleave.prevent="dragOver = false"
                                x-on:drop.prevent="onDrop($event)"
                                :class="dragOver ? 'border-purple-400 bg-purple-900/50' : ''"
                                class="relative flex flex-col items-center justify-center border-4 border-dashed border-purple-600 bg-purple-900/30 rounded-xl p-6 min-h-[250px] cursor-pointer hover:border-purple-500 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white/60 mb-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L20 20m-6-6l-2-2m2-2l2-2" />
                                </svg>
                                <p class="text-white/80 text-center font-medium">Arrastra o haz clic para añadir</p>
                                <p class="text-white/60 text-sm mt-1">hasta 4 imágenes y 2 vídeos (JPG, PNG, MP4, MKV, WebM)</p>

                                {{-- Input oculto y botón “invisible” que sólo cubre esta zona de “arrastrar” --}}
                                <input
                                    x-ref="mediaInput"
                                    type="file"
                                    multiple
                                    accept="image/jpeg,image/png,video/mp4,video/x-matroska,video/webm"
                                    class="hidden"
                                    x-on:change="onSelect($event)" />
                                <button
                                    type="button"
                                    class="absolute inset-0"
                                    @click.prevent="$refs.mediaInput.click()"></button>
                            </div>

                            {{-- ======== 3) Vista previa de archivos nuevos ======== --}}
                            <div class="bg-gray-800/50 p-6 rounded-xl shadow-lg">
                                <h3 class="text-xl font-semibold text-purple-200 mb-4">
                                    Nuevos archivos multimedia
                                </h3>
                                <div class="flex flex-wrap justify-center gap-4">
                                    {{-- Imágenes nuevas --}}
                                    <template x-for="(file, i) in imageFiles" :key="'img-new-' + i">
                                        <div class="relative w-20 h-20 bg-white/10 rounded-lg overflow-hidden border border-gray-700 shadow-sm flex items-center justify-center">
                                            <img :src="URL.createObjectURL(file)"
                                                class="w-full h-full object-cover"
                                                :alt="file.name" />
                                            <button
                                                type="button"
                                                class="absolute top-1 right-1 bg-red-600/80 hover:bg-red-700 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center transition-colors"
                                                @click.prevent="removeNewFile('image', i)"
                                                title="Eliminar imagen nueva">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Vídeos nuevos --}}
                                    <template x-for="(file, i) in videoFiles" :key="'vid-new-' + i">
                                        <div class="relative w-20 h-20 bg-black rounded-lg overflow-hidden border border-gray-700 shadow-sm flex items-center justify-center">
                                            <video :src="URL.createObjectURL(file)"
                                                class="w-full h-full object-cover"
                                                muted
                                                playsinline
                                                loop
                                                preload="metadata"></video>
                                            <button
                                                type="button"
                                                class="absolute top-1 right-1 bg-red-600/80 hover:bg-red-700 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center transition-colors"
                                                @click.prevent="removeNewFile('video', i)"
                                                title="Eliminar vídeo nuevo">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Mensaje si no hay archivos nuevos --}}
                                    <template x-if="!imageFiles.length && !videoFiles.length">
                                        <p class="text-gray-400 text-sm">Aún no has seleccionado nuevos archivos.</p>
                                    </template>
                                </div>

                                {{-- Nota si hay al menos uno nuevo --}}
                                <template x-if="imageFiles.length || videoFiles.length">
                                    <p class="text-purple-300 text-sm mt-4 text-center">
                                        Nuevos archivos multimedia seleccionados para subir.
                                    </p>
                                </template>
                            </div>

                            {{-- ======== 4) Inputs ocultos para enviar datos al servidor ======== --}}
                            <template x-for="id in mediaIdsToRemove" :key="'remove-' + id">
                                <input type="hidden" name="remove_media[]" :value="id" class="js-remove-media-input">
                            </template>
                        </div>
                        {{-- / FIN Columna 1 --}}



                        {{-- ----------------------------------------------------------------------------
                           Columna 2: ZIP (Existente + Nuevo)
                           ---------------------------------------------------------------------------- --}}
                        <div
                            x-data='{
                                /* 1) ZIP existente: URL y nombre */
                                existingZipUrl: {{ json_encode($juego->url_descarga) }},
                                existingZipName: {{ json_encode(
                                    $juego->url_descarga 
                                        ? \Illuminate\Support\Str::afterLast($juego->url_descarga, "/") 
                                        : null
                                ) }},
                                newZipFile: null,
                                removeZipFlag: false,
                                dragOverZip: false,

                                /* 2) Procesar arrastre de ZIP nuevo */
                                onZipDrop(event) {
                                    this.dragOverZip = false;
                                    const files = event.dataTransfer.files;
                                    if (files.length && files[0].name.toLowerCase().endsWith(".zip")) {
                                        this.newZipFile = files[0];
                                        this.$refs.zipInput.files = files;
                                        this.removeZipFlag = true;
                                        this.existingZipUrl = null;
                                        this.existingZipName = null;
                                    } else {
                                        alert("Por favor, suelta un archivo ZIP válido.");
                                    }
                                },
                                /* 3) Procesar selección de ZIP nuevo */
                                onZipSelect(event) {
                                    if (event.target.files.length && event.target.files[0].name.toLowerCase().endsWith(".zip")) {
                                        this.newZipFile = event.target.files[0];
                                        this.removeZipFlag = true;
                                        this.existingZipUrl = null;
                                        this.existingZipName = null;
                                    } else {
                                        alert("Solo se permiten archivos .zip.");
                                        event.target.value = null;
                                    }
                                },
                                /* 4) Marcar ZIP existente para eliminarlo */
                                removeExistingZip() {
                                    this.removeZipFlag = true;
                                    this.existingZipUrl = null;
                                    this.existingZipName = null;
                                }
                            }'
                            x-on:dragover.prevent="dragOverZip = true"
                            x-on:dragleave.prevent="dragOverZip = false"
                            x-on:drop.prevent="onZipDrop($event)"
                            :class="dragOverZip ? 'border-purple-400 bg-purple-900/50' : ''"
                            class="relative flex flex-col items-center justify-start border-4 border-dashed border-purple-600 bg-purple-900/30 rounded-xl p-6 min-h-[350px] cursor-pointer hover:border-purple-500 transition-all duration-200">
                            {{-- Título Sección --}}
                            <h3 class="text-xl font-semibold text-purple-200 mb-4">
                                Archivo ZIP
                            </h3>

                            {{-- ==============================================
                                 a) Mostrar ZIP existente si lo hay y no se cargó uno nuevo
                                 ============================================== --}}
                            <template x-if="existingZipUrl && !newZipFile">
                                <div class="flex items-center space-x-3 text-sm bg-gray-700/40 p-4 rounded-md border border-gray-700 w-full mb-4">
                                    {{-- Botón “X” para eliminar el ZIP existente --}}
                                    <button
                                        type="button"
                                        @click.stop="removeExistingZip()"
                                        class="bg-red-600 bg-opacity-80 rounded-full w-6 h-6 flex items-center justify-center text-white text-sm hover:bg-red-700 transition-colors"
                                        title="Eliminar ZIP existente">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <a
                                        :href="existingZipUrl"
                                        target="_blank"
                                        class="truncate text-blue-300 hover:underline flex-1 text-base font-medium"
                                        style="max-width: calc(100% - 80px);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 align-middle"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span x-text="existingZipName"></span>
                                    </a>
                                </div>
                            </template>

                            {{-- ==============================================
                                 b) Zona “Arrastra o haz clic para seleccionar ZIP nuevo”
                                 ============================================== --}}
                            <div class="flex flex-col items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white/60 mb-2" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7m-5 4l-5 5m0 0l-5-5m5 5V2" />
                                </svg>
                                <p class="text-white/80 text-center font-medium">
                                    Arrastra o haz clic para seleccionar un nuevo ZIP (opcional)
                                </p>
                                <p class="text-white/60 text-sm mt-1">(Máx. 500MB, formato .zip)</p>

                                <input
                                    x-ref="zipInput"
                                    type="file"
                                    name="zip"
                                    accept=".zip"
                                    class="hidden"
                                    x-on:change="onZipSelect($event)" />
                                <button type="button" class="absolute inset-0" @click="$refs.zipInput.click()"></button>
                            </div>

                            {{-- ==============================================
                                 c) Mostrar nombre del nuevo ZIP si se seleccionó
                                 ============================================== --}}
                            <template x-if="newZipFile">
                                <p class="mt-4 text-purple-200 text-sm text-center truncate px-4" style="max-width: 100%;">
                                    Archivo seleccionado:<br>
                                    <span class="font-semibold" x-text="newZipFile.name"></span>
                                </p>
                            </template>

                            {{-- ==============================================
                                 d) Input oculto para indicar “remove_zip” al backend
                                 ============================================== --}}
                            <template x-if="removeZipFlag">
                                <input type="hidden" name="remove_zip" value="1">
                            </template>
                        </div>
                        {{-- / FIN Columna 2: ZIP --}}

                    </div>
                    {{-- / FIN GRID PRINCIPAL de archivos --}}

                    {{-- ==============================================
                         Sección de Información del Juego (campos de texto)
                         ============================================== --}}
                    <div class="bg-gray-800/50 p-6 rounded-xl shadow-lg space-y-6">
                        <h3 class="text-xl font-semibold text-purple-200 mb-4">Detalles del Juego</h3>

                        <div>
                            <x-label for="titulo" value="Título del Juego"
                                class="text-purple-300 text-base mb-1" />
                            <x-input
                                id="titulo"
                                name="titulo"
                                type="text"
                                value="{{ old('titulo', $juego->titulo) }}"
                                required
                                readonly
                                placeholder="Ej. Mi Aventura Gráfica"
                                class="mt-1 block w-full bg-gray-700/40 placeholder-gray-400 text-white text-base border-gray-700 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" />
                        </div>

                        <div>
                            <x-label for="descripcion" value="Descripción del Juego"
                                class="text-purple-300 text-base mb-1" />
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                required
                                rows="6"
                                placeholder="Escribe una descripción detallada de tu juego, su jugabilidad, historia, etc."
                                class="mt-1 block w-full bg-gray-700/40 placeholder-gray-400 text-white text-base border-gray-700 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm p-3">{{ old('descripcion', $juego->descripcion) }}</textarea>
                        </div>

                        <div>
                            <x-label for="fecha_lanzamiento" value="Fecha de Lanzamiento"
                                class="text-purple-300 text-base mb-1" />
                            <x-input
                                id="fecha_lanzamiento"
                                name="fecha_lanzamiento"
                                type="date"
                                value="{{ old('fecha_lanzamiento', $juego->fecha_lanzamiento ? \Carbon\Carbon::parse($juego->fecha_lanzamiento)->format('Y-m-d') : '') }}"
                                class="mt-1 block w-full bg-gray-700/40 text-white text-base border-gray-700 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" />
                        </div>

                        <div>
                            <x-label for="precio" value="Precio (€)" class="text-purple-300 text-base mb-1" />
                            <x-input
                                id="precio"
                                name="precio"
                                type="number"
                                step="0.01"
                                min="0"
                                value="{{ old('precio', $juego->precio) }}"
                                required
                                placeholder="0.00"
                                class="mt-1 block w-full bg-gray-700/40 placeholder-gray-400 text-white text-base border-gray-700 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" />
                        </div>

                        <div>
                            <x-label for="nombre_ejecutable" value="Ruta del Ejecutable (dentro del ZIP)"
                                class="text-purple-300 text-base mb-1" />
                            <x-input
                                id="nombre_ejecutable"
                                name="nombre_ejecutable"
                                type="text"
                                value="{{ old('nombre_ejecutable', $juego->nombre_ejecutable) }}"
                                required
                                placeholder="Ej. MiJuego/bin/MiJuego.exe o MiJuego.app (para Mac)"
                                class="mt-1 block w-full bg-gray-700/40 placeholder-gray-400 text-white text-base border-gray-700 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" />
                        </div>
                    </div>

                    {{-- Botón para enviar --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="w-full sm:w-auto px-10 py-4 bg-purple-600 hover:bg-purple-700 text-white text-xl font-bold rounded-lg transition-all duration-200 shadow-xl transform hover:scale-105 active:scale-95">
                            Actualizar Juego
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-self.base>
</x-app-layout>