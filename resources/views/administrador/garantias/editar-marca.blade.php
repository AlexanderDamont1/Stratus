{{-- resources/views/admin/garantias/editar-marca.blade.php --}}
<x-app-layout>
    <div class="mx-auto space-y-6" x-data="editarMarca()" x-init="init()">

       

        {{-- Header --}}
        <div>
            <a href="{{ route('admin.garantias.index') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                  flex items-center gap-1 mb-2 transition w-fit">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver a garantías
            </a>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Garantía — {{ $marca->nombre_marca }}
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">Sube la póliza PDF y configura los componentes</p>
        </div>


         {{-- Flash --}}
        <div x-show="flashVisible" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-end="opacity-0 translate-y-2" class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
            <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl min-w-[300px]"
                :class="flashTipo === 'error' ? 'ring-1 ring-red-200' : 'ring-1 ring-gray-200 dark:ring-gray-700'">
                <svg x-show="flashTipo==='success'" class="h-5 w-5 text-green-500 shrink-0" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <svg x-show="flashTipo==='error'" class="h-5 w-5 text-red-500 shrink-0" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
            </div>
        </div>

        {{-- ── Sección 1: PDF ── --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Póliza PDF</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        La IA extraerá los componentes automáticamente. Revisa antes de guardar.
                    </p>
                </div>

                @php $tienePdf = $config?->tienePdf(); @endphp
                @if($tienePdf)
                    <span class="text-[10px] px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30
                                     text-blue-700 dark:text-blue-300 font-medium shrink-0">
                        {{ $config->pdf_nombre_original }}
                    </span>
                @endif
            </div>

            {{-- Estado del procesamiento --}}
            @php
                $estado = $config?->estado_procesamiento ?? 'sin_pdf';
            @endphp

            <div x-data="{ estado: '{{ $estado }}' }">

                {{-- Uploader --}}
                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-6
                        text-center hover:border-gray-400 dark:hover:border-gray-400 transition"
                    x-bind:class="subiendo ? 'opacity-60 pointer-events-none' : ''">

                    <input type="file" id="pdf-input" accept=".pdf" class="hidden" @change="subirPdf($event)">

                    <label for="pdf-input" class="cursor-pointer">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Haz clic para subir</span>
                            o arrastra el PDF aquí
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Máximo 4MB</p>
                    </label>
                </div>

                {{-- Estado de procesamiento --}}
                <div x-show="estado !== 'sin_pdf'" x-cloak class="mt-3">
                    <div x-show="estado === 'pendiente' || estado === 'procesando'"
                        class="flex items-center gap-2 text-sm text-yellow-600 dark:text-yellow-400">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Procesando con IA, espera unos segundos...
                        <button @click="verificarEstado()" class="text-xs underline ml-1">Verificar</button>
                    </div>

                    <div x-show="estado === 'error'"
                        class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        Error al procesar el PDF. Intenta subirlo de nuevo.
                    </div>

                    <div x-show="estado === 'completado'"
                        class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        PDF procesado correctamente por la IA.
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Sección 2: Componentes (resultado IA + edición manual) ── --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Componentes con garantía</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Revisa y ajusta lo que la IA extrajo. Luego guarda para activar.
                    </p>
                </div>
                <button @click="agregarComponente()" class="text-xs border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg
                           text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700
                           transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar
                </button>
            </div>

            {{-- Lista editable de componentes --}}
            <div class="space-y-3">
                <template x-for="(comp, idx) in componentes" :key="idx">
                    <div class="border border-gray-100 dark:border-gray-700 rounded-xl p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">

                            {{-- Clave (slug) --}}
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1">Clave (slug)</label>
                                <input type="text" x-model="comp.clave" placeholder="motor"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                          text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                            </div>

                            {{-- Nombre display --}}
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1">Nombre</label>
                                <input type="text" x-model="comp.nombre" placeholder="Motor"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                          text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                            </div>

                            {{-- Duración --}}
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1">Meses</label>
                                <input type="number" x-model.number="comp.duracion" min="0"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                          text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                            </div>

                            {{-- Cobertura --}}
                            <div>
                                <label class="block text-[10px] text-gray-400 mb-1">Cobertura</label>
                                <input type="text" x-model="comp.cobertura" placeholder="defecto fabrica"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                          px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                          text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                            </div>
                        </div>

                        {{-- Piezas incluidas --}}
                        <div class="mb-2">
                            <label class="block text-[10px] text-gray-400 mb-1">
                                Incluye (separado por comas)
                            </label>
                            <input type="text" :value="comp.incluye.join(', ')"
                                @input="comp.incluye = $event.target.value.split(',').map(s => s.trim()).filter(Boolean)"
                                placeholder="mando velocidad, freno, convertidor"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg
                                      px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700
                                      text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>

                        {{-- Flags + eliminar --}}
                        <div class="flex items-center gap-4 mt-2">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" x-model="comp.serializable"
                                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Serializable</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" x-model="comp.excluido"
                                    class="rounded border-gray-300 text-red-500 focus:ring-red-400">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Excluido (consumible)</span>
                            </label>
                            <button @click="eliminarComponente(idx)"
                                class="ml-auto text-xs text-red-500 hover:text-red-700 transition">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Empty --}}
                <div x-show="componentes.length === 0" class="text-center py-8 text-sm text-gray-400">
                    Sube un PDF para que la IA extraiga los componentes,
                    o agrégalos manualmente.
                </div>
            </div>

            {{-- Guardar --}}
            <div class="flex justify-end mt-4" x-show="componentes.length > 0">
                <button @click="guardarComponentes()" :disabled="guardando" class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5
                           rounded-lg text-sm font-semibold hover:opacity-90 transition
                           disabled:opacity-40 disabled:cursor-not-allowed active:scale-95
                           flex items-center gap-2">
                    <svg x-show="guardando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <span x-text="guardando ? 'Guardando...' : 'Guardar componentes'"></span>
                </button>
            </div>
        </div>

        <script>
            function editarMarca() {
                return {
                    subiendo: false,
                    guardando: false,
                    pollingInt: null,
                    flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

                    // Componentes: se cargan desde DB o desde ia_raw_json
                    @php
                        $componentesIniciales = $config && $config->componenteDefs
                            ? $config->componenteDefs->map(fn($d) => [
                                'clave' => $d->clave_componente,
                                'nombre' => $d->nombre_componente,
                                'incluye' => $d->incluye,
                                'duracion' => $d->duracion_meses,
                                'cobertura' => $d->cobertura ?? '',
                                'serializable' => $d->serializable,
                                'excluido' => $d->excluido,
                            ])
                            : [];
                    @endphp
            componentes: @json($componentesIniciales),

                    idMarcaGarantia: '{{ $config?->id_marca_garantia ?? "" }}',

                    init() {
                        // Si hay resultado de IA y aún no hay componentes guardados, cargar desde IA
                        @if($config && $config->ia_raw_json && $config->componenteDefs->isEmpty())
                            this.cargarDesdeIA({{ json_encode($config->ia_raw_json) }});
                        @endif

                        // Si está procesando, hacer polling
                        @if(in_array($config?->estado_procesamiento, ['pendiente', 'procesando']))
                            this.iniciarPolling();
                        @endif
            },

                    // Reemplaza cargarDesdeIA() completo en editar-marca.blade.php
                    cargarDesdeIA(rawJson) {
                        let componentes = [];

                        try {
                            // ia_raw_json puede llegar como string o como objeto ya parseado
                            const data = typeof rawJson === 'string' ? JSON.parse(rawJson) : rawJson;

                            // El servicio guarda el array plano de validarComponentes()
                            // que ya tiene: { clave, nombre, duracion, cobertura, incluye, serializable, excluido }
                            if (Array.isArray(data)) {
                                componentes = data.map(c => ({
                                    clave: c.clave ?? '',
                                    nombre: c.nombre ?? '',
                                    duracion: c.duracion ?? 12,
                                    cobertura: c.cobertura ?? '',
                                    incluye: Array.isArray(c.incluye) ? c.incluye : [],
                                    serializable: c.serializable ?? false,
                                    excluido: c.excluido ?? false,
                                }));
                            }
                            // Compatibilidad con formato anterior { garantias: [...] }
                            else if (data?.garantias && Array.isArray(data.garantias)) {
                                componentes = data.garantias.map(g => {
                                    // "componente" puede ser un string largo — lo convertimos a clave
                                    const claveRaw = (g.componente ?? g.clave ?? '').split(',')[0].trim();
                                    const clave = claveRaw.toLowerCase().replace(/[^a-z0-9]/g, '_').substring(0, 60);
                                    const nombre = g.nombre ?? (claveRaw.charAt(0).toUpperCase() + claveRaw.slice(1));

                                    return {
                                        clave,
                                        nombre,
                                        duracion: g.duracion_meses ?? g.duracion ?? 12,
                                        cobertura: g.cobertura ?? '',
                                        incluye: Array.isArray(g.incluye) ? g.incluye : [],
                                        serializable: ['motor', 'bateria', 'controlador'].some(k => clave.includes(k)),
                                        excluido: false,
                                    };
                                });
                            }
                        } catch (e) {
                            console.error('Error parseando ia_raw_json:', e);
                        }

                        if (componentes.length > 0) {
                            this.componentes = componentes;
                        }
                    },

                    agregarComponente() {
                        this.componentes.push({
                            clave: '', nombre: '', incluye: [], duracion: 12,
                            cobertura: '', serializable: false, excluido: false,
                        });
                    },

                    eliminarComponente(idx) {
                        this.componentes.splice(idx, 1);
                    },

                    async subirPdf(event) {
                        // ← Capturar el archivo INMEDIATAMENTE antes de cualquier otra operación
                        const file = event.target.files[0];
                        if (!file) return;

                        if (file.size > 4 * 1024 * 1024) {
                            this.flash('El PDF no debe superar 4MB.', 'error');
                            return;
                        }

                        if (!file.name.toLowerCase().endsWith('.pdf')) {
                            this.flash('Solo se aceptan archivos PDF.', 'error');
                            return;
                        }

                        // Construir FormData ANTES de cambiar subiendo=true
                        // (cambiar subiendo dispara re-render de Alpine que puede afectar el input)
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const fd = new FormData();
                        fd.append('pdf', file);  // ← file ya está capturado, el re-render no afecta

                        // Ahora sí cambiar el estado
                        this.subiendo = true;

                        try {
                            const res = await fetch('{{ route("admin.garantias.marcas.pdf", $marca->id_marca) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    // SIN Content-Type — el browser lo pone con el boundary
                                },
                                body: fd,
                            });

                            if (res.status === 419) {
                                this.flash('Sesión expirada. Recarga la página.', 'error');
                                return;
                            }

                            const data = await res.json();

                            console.log('Response status:', res.status, 'Body:', data);

                            if (!data.ok) {
                                this.flash(data.mensaje ?? 'Error.', 'error');
                                return;
                            }

                            this.idMarcaGarantia = data.config.id;
                            this.flash(data.mensaje);
                            this.iniciarPolling();

                        } catch (err) {
                            console.error('Error subiendo PDF:', err);
                            this.flash('Error de conexión.', 'error');
                        } finally {
                            this.subiendo = false;
                            event.target.value = '';
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
                                // Cargar componentes sugeridos por IA si aún no hay nada
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
                        const invalidos = this.componentes.filter(c => !c.clave.trim() || !c.nombre.trim() || c.duracion < 0);
                        if (invalidos.length) {
                            this.flash('Revisa que todos los componentes tengan clave, nombre y duración.', 'error');
                            return;
                        }

                        this.guardando = true;
                        try {
                            const res = await fetch('{{ route("admin.garantias.componentes.guardar") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    id_marca_garantia: this.idMarcaGarantia,
                                    componentes: this.componentes,
                                }),
                            });
                            const data = await res.json();
                            if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }
                            this.flash('Componentes guardados correctamente.');
                        } catch { this.flash('Error de conexión.', 'error'); }
                        finally { this.guardando = false; }
                    },

                    flash(msg, tipo = 'success') {
                        this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
                        clearTimeout(this.flashTimer);
                        this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
                    },
                }
            }
        </script>
    </div>
</x-app-layout>