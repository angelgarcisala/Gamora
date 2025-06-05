{{-- resources/views/juegos/create.blade.php --}}
<x-app-layout>
  <x-self.base>
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
      <a href="{{ url()->previous() }}"
         class="absolute top-6 left-6 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition">
        ← Volver
      </a>

      <div class="w-full max-w-6xl space-y-8">
        <h2 class="text-2xl font-bold text-white">Subir Nuevo Juego</h2>
        <x-validation-errors class="text-red-400 mb-4" />

        <form method="POST"
              action="{{ route('juegos.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
          @csrf

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Izquierda: imágenes y vídeos --}}
            <div 
              <div x-data="{
                dragOver: false,
                imageFiles: [],
                videoFiles: [],
                onDrop(event) {
                  this.dragOver = false;
                  const files = Array.from(event.dataTransfer.files);
                  console.log('[Dropzone] onDrop recibió:', files);
                  this.processFiles(files);
                },
                onSelect(event) {
                  const files = Array.from(event.target.files);
                  console.log('[Dropzone] onSelect recibió:', files);
                  this.processFiles(files);
                },
                processFiles(list) {
                  console.log('[Dropzone] processFiles lista inicial:', list);
                  list.forEach(file => {
                    if (file.type.startsWith('image/')) {
                      if (this.imageFiles.length < 4) this.imageFiles.push(file);
                      else alert('Máximo 4 imágenes');
                    }
                    else if (['video/mp4','video/x-matroska'].includes(file.type)) {
                      if (this.videoFiles.length < 2) this.videoFiles.push(file);
                      else alert('Máximo 2 vídeos');
                    }
                  });
                  console.log('[Dropzone] imageFiles ahora es:', this.imageFiles);
                  console.log('[Dropzone] videoFiles ahora es:', this.videoFiles);
                  this.$refs.mediaInput.value = null;
                  this.syncInputs();
                },
                removeImage(i) {
                  this.imageFiles.splice(i,1);
                  console.log('[Dropzone] tras removeImage, imageFiles:', this.imageFiles);
                  this.syncInputs();
                },
                removeVideo(i) {
                  this.videoFiles.splice(i,1);
                  console.log('[Dropzone] tras removeVideo, videoFiles:', this.videoFiles);
                  this.syncInputs();
                },
                syncInputs() {
                  console.log('[Dropzone] syncInputs va a inyectar inputs ocultos');
                  document.querySelectorAll('.js-img-input').forEach(el => el.remove());
                  document.querySelectorAll('.js-vid-input').forEach(el => el.remove());
                  const parentForm = this.$el.closest('form');

                  if (this.imageFiles.length) {
                    const dt = new DataTransfer();
                    this.imageFiles.forEach(f => dt.items.add(f));
                    const inp = document.createElement('input');
                    inp.type = 'file';
                    inp.multiple = false;
                    inp.name = 'imagenes[]';
                    inp.files = dt.files;
                    inp.classList.add('js-img-input');
                    inp.style.display = 'none';
                    parentForm.appendChild(inp);
                    console.log('[Dropzone] inyectó input.imagenes[] con files:', dt.files);
                  }
                  if (this.videoFiles.length) {
                    const dt2 = new DataTransfer();
                    this.videoFiles.forEach(f => dt2.items.add(f));
                    const inp2 = document.createElement('input');
                    inp2.type = 'file';
                    inp2.multiple = false;
                    inp2.name = 'videos[]';
                    inp2.files = dt2.files;
                    inp2.classList.add('js-vid-input');
                    inp2.style.display = 'none';
                    parentForm.appendChild(inp2);
                    console.log('[Dropzone] inyectó input.videos[] con files:', dt2.files);
                  }
                }
              }"
              x-on:dragover.prevent="dragOver = true"
              x-on:dragleave.prevent="dragOver = false"
              x-on:drop.prevent="onDrop($event)"
              :class="dragOver ? 'border-purple-400 bg-purple-900/50' : ''"
              class="relative flex flex-col items-center justify-center border-4 border-dashed border-purple-600 bg-purple-900/30 rounded-xl p-6 min-h-[350px]">

              <svg xmlns="http://www.w3.org/2000/svg"
                   class="h-16 w-16 text-white/60"
                   fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
              </svg>
              <p class="mt-2 text-white/70 text-center">
                Arrastra o selecciona<br>hasta 4 imágenes y 2 vídeos
              </p>

              <input x-ref="mediaInput"
                     type="file"
                     multiple
                     accept="image/jpeg,image/png,video/mp4,video/x-matroska"
                     class="hidden"
                     x-on:change="onSelect($event)" />

              <button type="button"
                      class="absolute inset-0"
                      x-on:click="$refs.mediaInput.click()">
              </button>

              <template x-if="imageFiles.length > 0">
                <div class="mt-6 w-full overflow-x-auto">
                  <div class="flex space-x-2">
                    <template x-for="(file, i) in imageFiles" :key="file.name">
                      <div class="relative w-20 h-20 bg-white/20 rounded overflow-hidden">
                        <img :src="URL.createObjectURL(file)" class="w-full h-full object-cover" />
                        <button type="button"
                                class="absolute top-1 right-1 bg-red-600/80 hover:bg-red-700 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center"
                                x-on:click="removeImage(i)">x</button>
                      </div>
                    </template>
                  </div>
                </div>
              </template>

              <template x-if="videoFiles.length > 0">
                <div class="mt-6 w-full overflow-x-auto">
                  <div class="flex space-x-2">
                    <template x-for="(file, i) in videoFiles" :key="file.name">
                      <div class="relative w-20 h-20 bg-black rounded overflow-hidden flex items-center justify-center">
                        <video :src="URL.createObjectURL(file)" class="w-full h-full object-cover"></video>
                        <button … x-on:click="removeVideo(i)">×</button>
                      </div>
                    </template>
                  </div>
                </div>
              </template>
            </div>

            {{-- Derecha: ZIP del juego --}}
            <div 
              x-data="{
                dragOverZip: false,
                zipFileName: '',
                onDropZip(e) {
                  this.dragOverZip = false;
                  const file = e.dataTransfer.files[0];
                  if (file && file.name.toLowerCase().endsWith('.zip')) {
                    this.$refs.zipInput.files = e.dataTransfer.files;
                    this.zipFileName = file.name;   // <— asignamos aquí
                  } else {
                    alert('Solo archivos .zip');
                  }
                },
                onSelectZip(e) {
                  const file = e.target.files[0];
                  if (file && file.name.toLowerCase().endsWith('.zip')) {
                    this.zipFileName = file.name;   // <— y aquí
                  } else {
                    alert('Solo .zip');
                    this.$refs.zipInput.value = null;
                    this.zipFileName = '';
                  }
                }
              }"
              x-on:dragover.prevent="dragOverZip = true"
              x-on:dragleave.prevent="dragOverZip = false"
              x-on:drop.prevent="onDropZip($event)"
              :class="dragOverZip ? 'border-purple-400 bg-purple-900/50' : ''"
              class="relative flex flex-col items-center justify-center 
                     border-4 border-dashed border-purple-600 bg-purple-900/30 
                     rounded-xl p-6 min-h-[350px]">

              <svg xmlns="http://www.w3.org/2000/svg" 
                   class="h-16 w-16 text-white/60" 
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7 
                         m-5 4l-5 5m0 0l-5-5m5 5V2" />
              </svg>
              <p class="mt-2 text-white/70 text-center">
                Arrastra o selecciona<br>el ZIP del juego
              </p>

              <input 
                x-ref="zipInput"
                type="file"
                name="zip"
                accept=".zip"
                class="hidden"
                required
                x-on:change="onSelectZip($event)"
              />

              <button 
                type="button" 
                class="absolute inset-0"
                x-on:click="$refs.zipInput.click()">
              </button>

              <template x-if="zipFileName">
                <p class="mt-4 text-purple-200 text-sm text-center">
                  Archivo seleccionado:<br>
                  <span x-text="zipFileName"></span>
                </p>
              </template>
            </div>
          </div>

          <div>
            <x-label for="titulo" value="Título del juego" class="text-purple-200" />
            <x-input id="titulo" name="titulo" type="text"
                     :value="old('titulo')"
                     required placeholder="Nombre de tu juego"
                     class="mt-1 block w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500" />
          </div>

          <div>
            <x-label for="descripcion" value="Descripción" class="text-purple-200" />
            <textarea id="descripcion"
                      name="descripcion"
                      required
                      rows="4"
                      placeholder="Descripción breve del juego"
                      class="mt-1 block w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500 rounded-lg p-3">{{ old('descripcion') }}</textarea>
          </div>

          <div>
            <x-label for="fecha_lanzamiento" value="Fecha de lanzamiento" class="text-purple-200" />
            <x-input id="fecha_lanzamiento"
                     name="fecha_lanzamiento"
                     type="date"
                     :value="old('fecha_lanzamiento')"
                     class="mt-1 block w-full bg-white/20 text-white border-none focus:ring-purple-500 focus:border-purple-500" />
          </div>

          <div>
            <x-label for="precio" value="Precio (€)" class="text-purple-200" />
            <x-input id="precio"
                     name="precio"
                     type="number"
                     step="0.01"
                     min="0"
                     :value="old('precio')"
                     required
                     placeholder="0.00"
                     class="mt-1 block w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500" />
          </div>

          <div>
            <x-label for="nombre_ejecutable" value="Ruta del ejecutable (dentro del ZIP)" class="text-purple-200" />
            <x-input id="nombre_ejecutable"
                     name="nombre_ejecutable"
                     type="text"
                     :value="old('nombre_ejecutable')"
                     required
                     placeholder="carpetaJuego/ejecutable.exe"
                     class="mt-1 block w-full bg-white/20 placeholder-purple-300 text-white border-none focus:ring-purple-500 focus:border-purple-500" />
          </div>

          <div>
            <button type="submit"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg transition">
              Subir juego
            </button>
          </div>
        </form>
      </div>
    </div>

        <script>
        function fileUploader() {
            return {
            dragOver: false,
            imageFiles: [],
            videoFiles: [],

            onDrop(event) {
                this.dragOver = false;
                this.processFiles(Array.from(event.dataTransfer.files));
            },

            onSelectFiles(event) {
                this.processFiles(Array.from(event.target.files));
            },

            processFiles(list) {
                list.forEach(file => {
                if (file.type.startsWith('image/')) {
                    if (this.imageFiles.length < 4) this.imageFiles.push(file);
                    else alert('Ya has alcanzado el máximo de 4 imágenes.');
                }else if (['video/mp4','video/x-matroska'].includes(file.type)) {
                  if (this.videoFiles.length < 2) this.videoFiles.push(file);
                  else alert('Máximo 2 vídeos');
                }
                });
                this.$refs.mediaInput.value = null;
                this.syncHiddenInputs();
            },

            removeImage(idx) {
                this.imageFiles.splice(idx, 1);
                this.syncHiddenInputs();
            },
            removeVideo(idx) {
                this.videoFiles.splice(idx, 1);
                this.syncHiddenInputs();
            },

            syncHiddenInputs() {
                document.querySelectorAll('.js-hidden-img').forEach(el => el.remove());
                document.querySelectorAll('.js-hidden-vid').forEach(el => el.remove());

                if (this.imageFiles.length) {
                const dt = new DataTransfer();
                this.imageFiles.forEach(f => dt.items.add(f));
                const input = document.createElement('input');
                input.type = 'file';
                input.multiple = false;
                input.name = 'imagenes[]';
                input.files = dt.files;
                input.classList.add('js-hidden-img');
                input.style.display = 'none';
                document.querySelector('form').appendChild(input);
                }
                if (this.videoFiles.length) {
                const dt2 = new DataTransfer();
                this.videoFiles.forEach(f => dt2.items.add(f));
                const input2 = document.createElement('input');
                input2.type = 'file';
                input2.multiple = false;
                input2.name = 'videos[]';
                input2.files = dt2.files;
                input2.classList.add('js-hidden-vid');
                input2.style.display = 'none';
                document.querySelector('form').appendChild(input2);
                }
            }
            }
        }
    </script>
  </x-self.base>
</x-app-layout>
