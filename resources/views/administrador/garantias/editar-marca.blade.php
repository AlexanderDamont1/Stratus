{{-- resources/views/admin/garantias/editar-marca.blade.php --}}
<x-app-layout>
    <div class="mx-auto space-y-6" x-data="editarMarca()" x-init="init()">

        {{-- Header --}}
        <div>
            <a href="{{ route('admin.garantias.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                      flex items-center gap-1 mb-2 transition w-fit">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a garantías
            </a>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Garantía — {{ $marca->nombre_marca }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Sube la póliza PDF, configura los componentes y la política de reemplazo</p>
                </div>

                {{-- Estado activa badge --}}
                @php $activa = $config?->activa ?? false; @endphp
                <div class="flex items-center gap-2 bg-white dark:bg-gray-800 border border-gray-200
                            dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Garantía activa</span>
                    <button
                        x-data="{ activa: {{ $activa ? 'true' : 'false' }} }"
                        @click="toggleActiva('{{ $marca->id_marca }}', $el, activa); activa = !activa"
                        :disabled="{{ $config ? 'false' : 'true' }}"
                        :class="activa ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-600'"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2
                               border-transparent transition-colors duration-200 ease-in-out
                               focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed"
                        title="{{ $config ? 'Activar/desactivar garantía' : 'Primero configura la póliza' }}">
                        <span :class="activa ? 'translate-x-4' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-4 w-4 transform rounded-full
                                     bg-white dark:bg-gray-900 shadow ring-0 transition duration-200 ease-in-out">
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Flash --}}
        <div x-show="flashVisible" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
            <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
                 :class="flashTipo === 'error'
                     ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                     : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
                <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-800 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
            </div>
        </div>

        {{-- ── Grid 2 columnas en desktop ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- Columna izquierda: PDF + Política --}}
            <div class="space-y-6 lg:col-span-1">

                {{-- ── Sección PDF ── --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-5 pt-5 pb-4">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Póliza PDF</p>
                        <p class="text-xs text-gray-400 mt-0.5">La IA extrae los componentes automáticamente</p>
                    </div>

                    @php $tienePdf = $config?->tienePdf(); @endphp
                    @if($tienePdf)
                    <div class="mx-5 mb-4 flex items-center gap-2.5 bg-blue-100 dark:bg-blue-800/30
                                border border-blue-200 dark:border-blue-700 rounded-lg px-3 py-2.5">
                        <svg class="w-4 h-4 text-blue-800 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs text-blue-800 dark:text-blue-400 font-medium truncate">
                            {{ $config->pdf_nombre_original }}
                        </span>
                    </div>
                    @endif

                    @php $estado = $config?->estado_procesamiento ?? 'sin_pdf'; @endphp
                    <div x-data="{ estado: '{{ $estado }}' }" class="px-5 pb-5">

                        {{-- Dropzone --}}
                        <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-5
                                    text-center hover:border-gray-400 dark:hover:border-gray-400 transition-colors
                                    cursor-pointer group"
                             :class="subiendo ? 'opacity-50 pointer-events-none' : ''"
                             @click="document.getElementById('pdf-input').click()"
                             @dragover.prevent
                             @drop.prevent="subirPdfDrop($event)">
                            <input type="file" id="pdf-input" accept=".pdf" class="hidden" @change="subirPdf($event)">

                            <div x-show="!subiendo">
                                <svg class="w-7 h-7 text-gray-300 dark:text-gray-600 mx-auto mb-2
                                            group-hover:text-gray-400 dark:group-hover:text-gray-500 transition-colors"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Haz clic</span>
                                    o arrastra el PDF
                                </p>
                                <p class="text-[10px] text-gray-400 mt-1">Máximo 4 MB</p>
                            </div>
                            <div x-show="subiendo" class="flex flex-col items-center gap-2">
                                <svg class="animate-spin w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <p class="text-xs text-gray-500">Subiendo PDF...</p>
                            </div>
                        </div>

                        {{-- Estado procesamiento --}}
                        <div x-show="estado !== 'sin_pdf'" x-cloak class="mt-3 space-y-1.5">
                            <div x-show="estado === 'pendiente' || estado === 'procesando'"
                                 class="flex items-center gap-2 text-xs text-amber-800 dark:text-amber-400
                                        bg-amber-100 dark:bg-amber-800/30 border border-amber-200 dark:border-amber-700
                                        rounded-lg px-3 py-2">
                                <svg class="animate-spin w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span>Procesando con IA...</span>
                                <button @click="verificarEstado()" class="ml-auto underline text-[10px] shrink-0">Verificar</button>
                            </div>
                            <div x-show="estado === 'error'"
                                 class="flex items-center gap-2 text-xs text-red-800 dark:text-red-400
                                        bg-red-100 dark:bg-red-800/30 border border-red-200 dark:border-red-700
                                        rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Error al procesar. Intenta subirlo de nuevo.
                            </div>
                            <div x-show="estado === 'completado'"
                                 class="flex items-center gap-2 text-xs text-green-800 dark:text-green-400
                                        bg-green-100 dark:bg-green-800/30 border border-green-200 dark:border-green-700
                                        rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                PDF procesado correctamente.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Sección: Política de reemplazo (por marca) ── --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <div class="px-5 pt-5 pb-4">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Política de reemplazo</p>
                        <p class="text-xs text-gray-400 mt-0.5">¿Qué garantía recibe un componente sustituido?</p>
                    </div>

                    <div class="px-5 pb-5 space-y-2">

                        {{-- Opciones visuales --}}
                        <template x-for="op in politicaOpciones" :key="op.key">
                            <button type="button" @click="seleccionarPolitica(op.key)"
                                    :class="politicaSeleccionada === op.key
                                        ? 'border-gray-900 dark:border-gray-300 ring-1 ring-gray-900 dark:ring-gray-300 bg-gray-50 dark:bg-gray-700'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                                    class="w-full text-left border rounded-xl px-3.5 py-3 transition-all duration-150 relative">

                                <div class="flex items-start gap-3">
                                    {{-- Radio visual --}}
                                    <div class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0 transition-all flex items-center justify-center"
                                         :class="politicaSeleccionada === op.key
                                             ? 'border-gray-900 dark:border-gray-200 bg-gray-900 dark:bg-gray-200'
                                             : 'border-gray-300 dark:border-gray-600 bg-transparent'">
                                        <div x-show="politicaSeleccionada === op.key"
                                             class="w-1.5 h-1.5 rounded-full bg-white dark:bg-gray-800"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-1.5 flex-wrap">
                                            <span x-text="op.label"></span>
                                            <span class="text-[10px] font-medium px-1.5 py-0.5 rounded-md"
                                                  :class="op.badgeClass" x-text="op.badge"></span>
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-0.5 leading-relaxed" x-text="op.desc"></p>
                                    </div>
                                </div>
                            </button>
                        </template>

                        {{-- Input días mini --}}
                        <div x-show="politicaSeleccionada === 'mini'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="mt-1">
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                                Días de garantía mini
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="number" x-model.number="miniDias" min="1" max="90"
                                       @input="politicaDirty = true"
                                       class="w-24 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                              text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-1 focus:ring-gray-400 text-center font-semibold">
                                <span class="text-xs text-gray-400">días (1–90)</span>
                            </div>
                        </div>

                        {{-- Guardar política --}}
                        <div class="pt-1">
                            <button @click="guardarPolitica()"
                                    :disabled="guardandoPolitica || !politicaDirty"
                                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                           px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition
                                           disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98]
                                           flex items-center justify-center gap-2">
                                <svg x-show="guardandoPolitica" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <span x-text="guardandoPolitica ? 'Guardando...' : (politicaDirty ? 'Guardar política' : 'Sin cambios')"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Columna derecha: Componentes --}}
            <div class="lg:col-span-2 flex flex-col">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col flex-1">
                    <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Componentes con garantía</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Revisa lo que la IA extrajo y ajusta antes de guardar.
                            </p>
                        </div>
                        <button @click="agregarComponente()"
                                class="shrink-0 flex items-center gap-1.5 text-xs border border-gray-200 dark:border-gray-600
                                       px-3 py-1.5 rounded-lg text-gray-500 dark:text-gray-400
                                       hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar
                        </button>
                    </div>

                    {{-- Lista editable: flex-1 para que empuje el footer hacia abajo --}}
                    <div class="flex-1 p-5 space-y-3 overflow-y-auto" style="max-height: calc(100vh); min-height: 120px;">

                        <template x-for="(comp, idx) in componentes" :key="idx">
                            <div class="border rounded-xl transition-all duration-150"
                                 :class="comp.excluido
                                     ? 'border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50 opacity-60'
                                     : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">

                                {{-- Header del componente --}}
                                <div class="flex items-center gap-3 px-4 pt-3.5 pb-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate"
                                           x-text="comp.nombre || 'Componente sin nombre'"></p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span x-show="comp.serializable"
                                              class="text-[9px] px-1.5 py-0.5 rounded-md font-medium
                                                     bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400">
                                            Serial
                                        </span>
                                        <span x-show="comp.excluido"
                                              class="text-[9px] px-1.5 py-0.5 rounded-md font-medium
                                                     bg-red-100 dark:bg-red-800/30 text-red-800 dark:text-red-400">
                                            Excluido
                                        </span>
                                        <button @click="eliminarComponente(idx)"
                                                class="text-gray-300 dark:text-gray-600 hover:text-red-500
                                                       dark:hover:text-red-400 transition p-0.5 rounded">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 pt-3 space-y-3">
                                    {{-- Row 1: clave + nombre + meses --}}
                                    <div class="grid grid-cols-5 gap-2.5">
                                        <div class="col-span-1">
                                            <label class="block text-[10px] text-gray-400 mb-1 font-medium">Clave</label>
                                            <input type="text" x-model="comp.clave" placeholder="motor"
                                                   @input="componentesDirty = true"
                                                   class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                          text-gray-900 dark:text-white focus:outline-none
                                                          focus:ring-1 focus:ring-gray-400 font-mono">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="block text-[10px] text-gray-400 mb-1 font-medium">Nombre</label>
                                            <input type="text" x-model="comp.nombre" placeholder="Motor eléctrico"
                                                   @input="componentesDirty = true"
                                                   class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                          text-gray-900 dark:text-white focus:outline-none
                                                          focus:ring-1 focus:ring-gray-400">
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-[10px] text-gray-400 mb-1 font-medium">Meses</label>
                                            <input type="number" x-model.number="comp.duracion" min="0"
                                                   @input="componentesDirty = true"
                                                   class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                          text-gray-900 dark:text-white focus:outline-none
                                                          focus:ring-1 focus:ring-gray-400 text-center">
                                        </div>
                                    </div>

                                    {{-- Row 2: cobertura --}}
                                    <div>
                                        <label class="block text-[10px] text-gray-400 mb-1 font-medium">Cobertura</label>
                                        <input type="text" x-model="comp.cobertura" placeholder="Defecto de fábrica, mal funcionamiento"
                                               @input="componentesDirty = true"
                                               class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                      px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                      text-gray-900 dark:text-white focus:outline-none
                                                      focus:ring-1 focus:ring-gray-400">
                                    </div>

                                    {{-- Row 3: incluye --}}
                                    <div>
                                        <label class="block text-[10px] text-gray-400 mb-1 font-medium">
                                            Incluye
                                            <span class="font-normal text-gray-300 dark:text-gray-600">(separado por comas)</span>
                                        </label>
                                        <input type="text"
                                               :value="comp.incluye.join(', ')"
                                               @input="comp.incluye = $event.target.value.split(',').map(s => s.trim()).filter(Boolean); componentesDirty = true"
                                               placeholder="mando, freno, convertidor de velocidad"
                                               class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                      px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                      text-gray-900 dark:text-white focus:outline-none
                                                      focus:ring-1 focus:ring-gray-400">
                                    </div>

                                    {{-- Row 3b: excepciones --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1 gap-2 flex-wrap">
                                            <label class="block text-[10px] text-gray-400 font-medium">
                                                Excepciones de garantía
                                                <span class="font-normal text-gray-300 dark:text-gray-600">(una por línea)</span>
                                            </label>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <select
                                                    x-show="excepcionesDisponibles.length > 0"
                                                    @change="reutilizarExcepciones(idx, $event.target.value); $event.target.value = ''"
                                                    class="text-[10px] border border-gray-200 dark:border-gray-600 rounded-md
                                                           px-1.5 py-0.5 bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400
                                                           focus:outline-none focus:ring-1 focus:ring-gray-400 max-w-[140px]">
                                                    <option value="">Reutilizar de...</option>
                                                    <template x-for="(op, opIdx) in excepcionesDisponibles" :key="opIdx">
                                                        <option :value="opIdx" x-text="op.marca + ' — ' + op.nombre + ' (' + op.excepciones.length + ')'"></option>
                                                    </template>
                                                </select>
                                                <button type="button"
                                                        @click="sugerirExcepcionesIA(idx)"
                                                        :disabled="sugiriendoIdx !== null || !comp.nombre.trim()"
                                                        class="flex items-center gap-1 text-[10px] font-medium px-1.5 py-0.5 rounded-md
                                                               border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400
                                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition
                                                               disabled:opacity-40 disabled:cursor-not-allowed">
                                                    <svg class="w-3 h-3 shrink-0" :class="sugiriendoIdx === idx ? 'animate-spin' : ''" viewBox="0 0 24 24" fill="currentColor">
                                                        <template x-if="sugiriendoIdx !== idx">
                                                            <g>
                                                                <path d="M12 2.5c.3 3.2 1.1 5.4 2.4 6.7 1.3 1.3 3.5 2.1 6.7 2.4-3.2.3-5.4 1.1-6.7 2.4-1.3 1.3-2.1 3.5-2.4 6.7-.3-3.2-1.1-5.4-2.4-6.7C8.3 12.5 6.1 11.7 2.9 11.4c3.2-.3 5.4-1.1 6.7-2.4C10.9 7.7 11.7 5.5 12 2.5z"/>
                                                                <path d="M19 14c.15 1.6.55 2.7 1.2 3.35.65.65 1.75 1.05 3.35 1.2-1.6.15-2.7.55-3.35 1.2-.65.65-1.05 1.75-1.2 3.35-.15-1.6-.55-2.7-1.2-3.35-.65-.65-1.75-1.05-3.35-1.2 1.6-.15 2.7-.55 3.35-1.2.65-.65 1.05-1.75 1.2-3.35z"/>
                                                            </g>
                                                        </template>
                                                        <template x-if="sugiriendoIdx === idx">
                                                            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="14 10"/>
                                                        </template>
                                                    </svg>
                                                    <span x-text="sugiriendoIdx === idx ? 'Generando...' : 'Generar con IA'"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <textarea
                                            :value="comp.excepciones.join('\n')"
                                            @input="comp.excepciones = $event.target.value.split('\n').map(s => s.trim()).filter(Boolean); componentesDirty = true"
                                            rows="3"
                                            placeholder="Ej: Cortos circuitos por modificaciones no originales"
                                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                                   px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                                   text-gray-900 dark:text-white focus:outline-none
                                                   focus:ring-1 focus:ring-gray-400 resize-y"></textarea>
                                    </div>

                                    {{-- Row 4: checkboxes --}}
                                    <div class="flex items-center gap-5 pt-0.5">
                                        <label class="flex items-center gap-2 cursor-pointer select-none group">
                                            <div class="relative">
                                                <input type="checkbox" x-model="comp.serializable" class="sr-only">
                                                <div @click="comp.serializable = !comp.serializable; componentesDirty = true"
                                                     :class="comp.serializable ? 'bg-purple-500 border-purple-500' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600'"
                                                     class="w-4 h-4 rounded border-2 transition-colors flex items-center justify-center cursor-pointer">
                                                    <svg x-show="comp.serializable" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Serializable</span>
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <div class="relative">
                                                <input type="checkbox" x-model="comp.excluido" class="sr-only">
                                                <div @click="comp.excluido = !comp.excluido; componentesDirty = true"
                                                     :class="comp.excluido ? 'bg-red-500 border-red-500' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600'"
                                                     class="w-4 h-4 rounded border-2 transition-colors flex items-center justify-center cursor-pointer">
                                                    <svg x-show="comp.excluido" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Excluido (consumible)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <div x-show="componentes.length === 0"
                             class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center
                                        justify-center mb-4">
                                <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin componentes todavía</p>
                            <p class="text-xs text-gray-400 mt-1 max-w-[200px]">Sube un PDF para que la IA los extraiga, o agrégalos manualmente</p>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs text-gray-400">
                                <span x-text="componentes.filter(c => !c.excluido).length"></span> activos ·
                                <span x-text="componentes.filter(c => c.excluido).length"></span> excluidos
                            </p>
                            <button @click="guardarComponentes()"
                                    :disabled="guardando || !componentesDirty"
                                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5
                                           rounded-xl text-sm font-semibold hover:opacity-90 transition
                                           disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98]
                                           flex items-center gap-2 shadow-sm">
                                <svg x-show="guardando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg x-show="!guardando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                <span x-text="guardando ? 'Guardando...' : (componentesDirty ? 'Guardar componentes' : 'Sin cambios')"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function editarMarca() {
            return {
                subiendo: false,
                guardando: false,
                guardandoPolitica: false,
                sugiriendoIdx: null,
                pollingInt: null,
                flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

                // ── Dirty state ──────────────────────────────────────────────
                componentesDirty: false,
                politicaDirty: false,

                politicaSeleccionada: '{{ $config?->politica_reemplazo ?? "mini" }}',
                miniDias: {{ $config?->mini_garantia_dias ?? 7 }},

                politicaOpciones: [
                    {
                        key: 'heredar',
                        label: 'Heredar',
                        badge: 'Tiempo restante',
                        badgeClass: 'bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400',
                        desc: 'El componente nuevo continúa con el tiempo de garantía que le quedaba al original.',
                    },
                    {
                        key: 'nueva',
                        label: 'Nueva completa',
                        badge: 'Duración original',
                        badgeClass: 'bg-emerald-100 dark:bg-emerald-800/30 text-emerald-800 dark:text-emerald-400',
                        desc: 'El componente nuevo recibe la duración total de garantía como si fuera compra nueva.',
                    },
                    {
                        key: 'mini',
                        label: 'Mini',
                        badge: 'Configurable',
                        badgeClass: 'bg-amber-100 dark:bg-amber-800/30 text-amber-800 dark:text-amber-400',
                        desc: 'Garantía corta con días configurables. Ideal para reemplazos en garantía.',
                    },
                ],

                @php
                    $componentesIniciales = $config && $config->componenteDefs
                        ? $config->componenteDefs->map(fn($d) => [
                            'clave'        => $d->clave_componente,
                            'nombre'       => $d->nombre_componente,
                            'incluye'      => $d->incluye,
                            'duracion'     => $d->duracion_meses,
                            'cobertura'    => $d->cobertura ?? '',
                            'excepciones'  => $d->excepciones ?? [],
                            'serializable' => $d->serializable,
                            'excluido'     => $d->excluido,
                        ])
                        : [];
                @endphp
                componentes: @json($componentesIniciales),
                excepcionesDisponibles: @json($excepcionesDisponibles),

                idMarcaGarantia: '{{ $config?->id_marca_garantia ?? "" }}',

                init() {
                    @if($config && $config->ia_raw_json && $config->componenteDefs->isEmpty())
                        this.cargarDesdeIA({{ json_encode($config->ia_raw_json) }});
                    @endif

                    @if(in_array($config?->estado_procesamiento, ['pendiente', 'procesando']))
                        this.iniciarPolling();
                    @endif
                },

                // ── Helpers dirty state ──────────────────────────────────────
                seleccionarPolitica(key) {
                    if (this.politicaSeleccionada !== key) {
                        this.politicaSeleccionada = key;
                        this.politicaDirty = true;
                    }
                },

                cargarDesdeIA(rawJson) {
                    let componentes = [];
                    try {
                        const data = typeof rawJson === 'string' ? JSON.parse(rawJson) : rawJson;
                        if (Array.isArray(data)) {
                            componentes = data.map(c => ({
                                clave:        c.clave ?? '',
                                nombre:       c.nombre ?? '',
                                duracion:     c.duracion ?? 12,
                                cobertura:    c.cobertura ?? '',
                                incluye:      Array.isArray(c.incluye) ? c.incluye : [],
                                excepciones:  Array.isArray(c.excepciones) ? c.excepciones : [],
                                serializable: c.serializable ?? false,
                                excluido:     c.excluido ?? false,
                            }));
                        } else if (data?.garantias && Array.isArray(data.garantias)) {
                            componentes = data.garantias.map(g => {
                                const claveRaw = (g.componente ?? g.clave ?? '').split(',')[0].trim();
                                const clave    = claveRaw.toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0, 60);
                                const nombre   = g.nombre ?? (claveRaw.charAt(0).toUpperCase() + claveRaw.slice(1));
                                return {
                                    clave,
                                    nombre,
                                    duracion:     g.duracion_meses ?? g.duracion ?? 12,
                                    cobertura:    g.cobertura ?? '',
                                    incluye:      Array.isArray(g.incluye) ? g.incluye : [],
                                    excepciones:  [],
                                    serializable: ['motor', 'bateria', 'controlador'].some(k => clave.includes(k)),
                                    excluido:     false,
                                };
                            });
                        }
                    } catch (e) { console.error('Error parseando ia_raw_json:', e); }
                    if (componentes.length > 0) {
                        this.componentes = componentes;
                        this.componentesDirty = true; // recién cargados de IA → hay que guardar
                    }
                },

                agregarComponente() {
                    this.componentes.push({
                        clave: '', nombre: '', incluye: [], duracion: 12,
                        cobertura: '', excepciones: [], serializable: false, excluido: false,
                    });
                    this.componentesDirty = true;
                },

                reutilizarExcepciones(idx, indexDisponible) {
                    if (indexDisponible === '' || indexDisponible === null) return;
                    const fuente = this.excepcionesDisponibles[indexDisponible];
                    if (!fuente) return;
                    this.componentes[idx].excepciones = [...fuente.excepciones];
                    this.componentesDirty = true;
                },

                async sugerirExcepcionesIA(idx) {
                    const comp = this.componentes[idx];
                    if (!comp || !comp.nombre.trim() || this.sugiriendoIdx !== null) return;
                    this.sugiriendoIdx = idx;
                    try {
                        const res = await fetch('{{ route("admin.garantias.componentes.sugerir-excepciones") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type':     'application/json',
                                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                            body: JSON.stringify({
                                nombre_componente: comp.nombre,
                                cobertura:         comp.cobertura,
                                incluye:           comp.incluye,
                            }),
                        });
                        const data = await res.json();
                        if (!data.ok) { this.flash(data.mensaje ?? 'Error al generar excepciones con IA.', 'error'); return; }
                        if (!data.excepciones.length) { this.flash('La IA no encontró sugerencias para este componente.', 'error'); return; }

                        const existentes = new Set(comp.excepciones.map(e => e.toLowerCase()));
                        const nuevas = data.excepciones.filter(e => !existentes.has(e.toLowerCase()));
                        comp.excepciones = [...comp.excepciones, ...nuevas];
                        this.componentesDirty = true;
                        this.flash(`${nuevas.length} excepción(es) sugerida(s) por IA.`);
                    } catch {
                        this.flash('Error de conexión con la IA.', 'error');
                    } finally {
                        this.sugiriendoIdx = null;
                    }
                },

                eliminarComponente(idx) {
                    this.componentes.splice(idx, 1);
                    this.componentesDirty = true;
                },

                subirPdfDrop(event) {
                    const file = event.dataTransfer?.files?.[0];
                    if (!file) return;
                    const fakeEvent = { target: { files: [file], value: '' } };
                    this.subirPdf(fakeEvent);
                },

                async subirPdf(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (file.size > 4 * 1024 * 1024) { this.flash('El PDF no debe superar 4MB.', 'error'); return; }
                    if (!file.name.toLowerCase().endsWith('.pdf')) { this.flash('Solo se aceptan archivos PDF.', 'error'); return; }

                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const fd = new FormData();
                    fd.append('pdf', file);

                    this.subiendo = true;
                    try {
                        const res = await fetch('{{ route("admin.garantias.marcas.pdf", $marca->id_marca) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN':     token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                            body: fd,
                        });

                        if (res.status === 419) { this.flash('Sesión expirada. Recarga la página.', 'error'); return; }

                        const data = await res.json();
                        if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }

                        this.idMarcaGarantia = data.config.id;
                        this.flash(data.mensaje);
                        this.iniciarPolling();
                    } catch (err) {
                        console.error('Error subiendo PDF:', err);
                        this.flash('Error de conexión.', 'error');
                    } finally {
                        this.subiendo = false;
                        if (event.target.value !== undefined) event.target.value = '';
                    }
                },

                iniciarPolling() {
                    clearInterval(this.pollingInt);
                    this.pollingInt = setInterval(() => this.verificarEstado(), 3000);
                },

                async verificarEstado() {
                    try {
                        const res = await fetch(
                            `{{ route("admin.garantias.marcas.editar", $marca->id_marca) }}?json=1`,
                            { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }
                        );
                        const data = await res.json();
                        if (data.estado === 'completado') {
                            clearInterval(this.pollingInt);
                            if (this.componentes.length === 0 && data.ia_raw_json) {
                                this.cargarDesdeIA(data.ia_raw_json);
                            }
                            this.flash('IA completó el procesamiento. Revisa los componentes.');
                        } else if (data.estado === 'error') {
                            clearInterval(this.pollingInt);
                            this.flash('Error al procesar el PDF.', 'error');
                        }
                    } catch { /* silencioso */ }
                },

                async guardarComponentes() {
                    const invalidos = this.componentes.filter(c => !c.clave.trim() || !c.nombre.trim() || c.duracion === null || c.duracion < 0);
                    if (invalidos.length) {
                        this.flash('Revisa que todos los componentes tengan clave, nombre y duración válida.', 'error');
                        return;
                    }
                    this.guardando = true;
                    try {
                        const res = await fetch('{{ route("admin.garantias.componentes.guardar") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type':     'application/json',
                                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                            body: JSON.stringify({
                                id_marca:     '{{ $marca->id_marca }}',
                                componentes:  this.componentes,
                            }),
                        });
                        const data = await res.json();
                        if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }
                        // La config pudo haberse creado apenas ahora (alta manual sin PDF previo)
                        if (data.id_marca_garantia) this.idMarcaGarantia = data.id_marca_garantia;
                        this.componentesDirty = false; // ← reset dirty tras guardar exitoso
                        this.flash('Componentes guardados correctamente.');
                    } catch { this.flash('Error de conexión.', 'error'); }
                    finally { this.guardando = false; }
                },

                async guardarPolitica() {
                    if (!this.idMarcaGarantia) {
                        this.flash('Primero sube la póliza PDF para poder guardar la política.', 'error');
                        return;
                    }
                    this.guardandoPolitica = true;
                    try {
                        const res = await fetch('{{ route("admin.garantias.marcas.politica", $marca->id_marca) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type':     'application/json',
                                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                            body: JSON.stringify({
                                politica_reemplazo: this.politicaSeleccionada,
                                mini_garantia_dias: this.miniDias,
                            }),
                        });
                        const data = await res.json();
                        if (!data.ok) { this.flash(data.mensaje ?? 'Error al guardar la política.', 'error'); return; }
                        this.politicaDirty = false; // ← reset dirty tras guardar exitoso
                        this.flash('Política de reemplazo guardada.');
                    } catch { this.flash('Error de conexión.', 'error'); }
                    finally { this.guardandoPolitica = false; }
                },

                async toggleActiva(idMarca, btn, activa) {
                    try {
                        const res = await fetch(`/admin/garantias/marcas/${idMarca}/activar`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                        });
                        const data = await res.json();
                        if (!data.ok) { this.flash('Error al cambiar estado.', 'error'); return; }
                        this.flash(data.activa ? 'Garantía activada.' : 'Garantía desactivada.');
                    } catch { this.flash('Error de conexión.', 'error'); }
                },

                flash(msg, tipo = 'success') {
                    this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
                    clearTimeout(this.flashTimer);
                    this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
                },
            }
        }
        </script>
    </div>
</x-app-layout>