{{-- resources/views/vendedor/reparaciones/create.blade.php --}}
<x-app-layout>
<div class="mx-auto max-w-2xl space-y-6" x-data="otCreate()" x-init="init()">

 
    {{-- ── Header ── --}}
    <div>
        <a href="{{ route('reparaciones.index') }}"
           class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                  flex items-center gap-1 mb-2 transition w-fit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a órdenes
        </a>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Nueva orden de trabajo</h2>
        <p class="text-xs text-gray-400 mt-0.5">
            Busca por número de serie para autocompletar los datos del cliente
        </p>
    </div>

       {{-- ── Flash ── --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo==='error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-600 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>


    {{-- ── Paso 1: Unidad ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Unidad</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Busca por número de serie o describe la bicicleta manualmente si no está registrada
            </p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Buscador --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Número de serie
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text"
                               x-model="numSerie"
                               @keydown.enter.prevent="buscarBicicleta()"
                               placeholder="Ej: REVI-48V-00123"
                               :disabled="biciEncontrada"
                               class=" w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5
                                      text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400
                                      disabled:opacity-50 transition font-mono pr-10">
                        <svg x-show="buscandoBici"
                             class="animate-spin w-4 h-4 text-gray-400 absolute right-3 top-2.5"
                             fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <button @click="buscarBicicleta()"
                            :disabled="buscandoBici || !numSerie.trim() || biciEncontrada"
                            class="px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   rounded-xl text-sm font-semibold hover:opacity-90 transition
                                   disabled:opacity-40 active:scale-[.98]">
                        Buscar
                    </button>
                    <button x-show="biciEncontrada || biciNoEncontrada"
                            @click="limpiarBici()"
                            class="px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl
                                   text-xs text-gray-500 dark:text-gray-400 hover:bg-gray-50
                                   dark:hover:bg-gray-700 transition">
                        Limpiar
                    </button>
                </div>
            </div>

            {{-- Encontrada --}}
            <div x-show="biciEncontrada && bici"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20
                        border border-green-200 dark:border-green-800 rounded-xl p-3.5">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-xs font-semibold text-green-800 dark:text-green-400">
                        Unidad encontrada en el sistema
                    </p>
                    <p class="text-xs text-green-700 dark:text-green-500 mt-0.5"
                       x-text="[bici?.marca, bici?.modelo, bici?.color, bici?.voltaje].filter(Boolean).join(' · ')"></p>
                </div>
            </div>

            {{-- No encontrada → descripción manual --}}
            <div x-show="biciNoEncontrada"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 class="space-y-3">
                <div class="flex items-start gap-2.5 bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-700 rounded-xl px-3.5 py-3">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-800 dark:text-amber-400">
                        Número de serie no registrado.
                        <span class="font-semibold">Describe la unidad manualmente.</span>
                    </p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Descripción de la unidad <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="biciDescripcion"
                           placeholder="Ej: Bicicleta eléctrica negra 36V sin marca"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
            </div>
        </div>
    </div>

    {{-- ── Paso 2: Cliente ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Cliente</p>
            <p class="text-xs text-gray-400 mt-0.5">
                <span x-show="clienteAutocompletado"
                      class="text-green-600 dark:text-green-400 font-medium">
                    ✓ Autocompletado desde el sistema ·
                </span>
                El cliente debe presentar identificación oficial al entregar la unidad
            </p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Banner autocomplete --}}
            <div x-show="clienteAutocompletado"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20
                        border border-blue-200 dark:border-blue-700 rounded-xl px-3.5 py-2.5">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs text-blue-800 dark:text-blue-400">
                    Cliente vinculado al número de serie en el sistema
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Nombre del cliente <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="clienteNombre"
                           placeholder="Nombre completo"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Correo electrónico
                        <span class="font-normal text-gray-400">(notificación)</span>
                    </label>
                    <input type="email" x-model="clienteEmail"
                           placeholder="cliente@correo.com"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
            </div>

            {{-- Verificación ID --}}
            <label class="flex items-start gap-3 cursor-pointer select-none">
                <div class="relative mt-0.5 shrink-0">
                    <input type="checkbox" x-model="idVerificada" class="sr-only">
                    <div @click="idVerificada = !idVerificada"
                         :class="idVerificada
                             ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white'
                             : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600'"
                         class="w-5 h-5 rounded-md border-2 transition-colors flex items-center justify-center cursor-pointer">
                        <svg x-show="idVerificada"
                             class="w-3 h-3 text-white dark:text-gray-900"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        Identificación verificada
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        El cliente presentó una identificación oficial al entregar la unidad
                    </p>
                </div>
            </label>
        </div>
    </div>

    {{-- ── Paso 3: Detalle OT ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Detalle de la orden</p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Tipo --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">
                    Tipo de OT
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="t in tipos" :key="t.val">
                        <button type="button" @click="tipo = t.val"
                                :class="tipo===t.val
                                    ? 'border-gray-900 dark:border-gray-300 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-900 dark:ring-gray-300'
                                    : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                                class="border rounded-xl px-3 py-3 text-center transition-all duration-150">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="t.label"></p>
                            <p class="text-[10px] text-gray-400 mt-0.5" x-text="t.desc"></p>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Problema --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Problema reportado <span class="text-red-400">*</span>
                </label>
                <textarea x-model="problemaReportado" rows="3"
                          placeholder="Describe el problema que reporta el cliente..."
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3
                                 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
            </div>

            {{-- Notas internas --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Notas internas
                    <span class="font-normal text-gray-400">(solo visible para el equipo)</span>
                </label>
                <textarea x-model="notasInternas" rows="2"
                          placeholder="Estado físico al recibir, accesorios incluidos, observaciones..."
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3
                                 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
            </div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="flex items-center justify-between gap-3 pb-8">
        <a href="{{ route('reparaciones.index') }}"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700
                  dark:hover:text-gray-200 transition">
            Cancelar
        </a>
        <button @click="crearOt()"
                :disabled="creando || !problemaReportado.trim() || (!numSerie.trim() && !biciDescripcion.trim())"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-6 py-2.5
                       rounded-xl text-sm font-semibold hover:opacity-90 transition
                       disabled:opacity-40 disabled:cursor-not-allowed active:scale-[.98]
                       flex items-center gap-2 shadow-sm">
            <svg x-show="creando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="creando ? 'Creando...' : 'Crear orden de trabajo'"></span>
        </button>
    </div>
</div>

<script>
function otCreate() {
    return {
        numSerie: '', biciDescripcion: '', bici: null,
        biciEncontrada: false, biciNoEncontrada: false, buscandoBici: false,
        idCliente: null, clienteNombre: '', clienteEmail: '',
        idVerificada: false, clienteAutocompletado: false,
        tipo: 'reparacion', problemaReportado: '', notasInternas: '',
        creando: false,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

        tipos: [
            { val: 'reparacion',    label: 'Reparación',    desc: 'Falla o daño' },
            { val: 'mantenimiento', label: 'Mantenimiento', desc: 'Servicio preventivo' },
            { val: 'garantia',      label: 'Garantía',      desc: 'Cobertura activa' },
        ],

        init() {},

        async buscarBicicleta() {
            if (!this.numSerie.trim() || this.buscandoBici) return;
            this.buscandoBici     = true;
            this.biciEncontrada   = false;
            this.biciNoEncontrada = false;
            this.limpiarCliente();

            try {
                const res  = await fetch('{{ route("reparaciones.buscar-bicicleta") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ num_serie: this.numSerie.trim() }),
                });
                const data = await res.json();

                if (data.ok && data.data) {
                    this.bici           = data.data.bicicleta;
                    this.biciEncontrada = true;

                    if (data.data.cliente) {
                        const c = data.data.cliente;
                        this.idCliente             = c.id_cliente;
                        this.clienteNombre         = [c.nombre_cliente, c.apellido1, c.apellido2].filter(Boolean).join(' ');
                        this.clienteEmail          = c.correo ?? '';
                        this.clienteAutocompletado = true;
                    }
                } else {
                    this.biciNoEncontrada = true;
                }
            } catch {
                this.flash('Error al buscar la unidad', 'error');
            } finally {
                this.buscandoBici = false;
            }
        },

        limpiarBici() {
            this.numSerie = ''; this.bici = null;
            this.biciEncontrada = false; this.biciNoEncontrada = false;
            this.biciDescripcion = '';
            this.limpiarCliente();
        },

        limpiarCliente() {
            this.idCliente = null; this.clienteNombre = '';
            this.clienteEmail = ''; this.idVerificada = false;
            this.clienteAutocompletado = false;
        },

        async crearOt() {
            if (!this.problemaReportado.trim()) {
                this.flash('El problema reportado es obligatorio', 'error'); return;
            }
            if (!this.numSerie.trim() && !this.biciDescripcion.trim()) {
                this.flash('Ingresa el número de serie o la descripción de la unidad', 'error'); return;
            }

            this.creando = true;
            try {
                const res  = await fetch('{{ route("reparaciones.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        // Solo manda num_serie si la bici fue encontrada en el sistema
                        num_serie:          this.biciEncontrada ? (this.numSerie.trim() || null) : null,
                        bici_descripcion:   !this.biciEncontrada ? (this.biciDescripcion.trim() || null) : null,
                        id_cliente:         this.idCliente,
                        cliente_nombre:     this.clienteNombre.trim() || null,
                        cliente_email:      this.clienteEmail.trim() || null,
                        id_verificada:      this.idVerificada,
                        tipo:               this.tipo,
                        problema_reportado: this.problemaReportado.trim(),
                        notas_internas:     this.notasInternas.trim() || null,
                    }),
                });
                const data = await res.json();

                if (!data.ok) { this.flash(data.mensaje ?? 'Error al crear la OT', 'error'); return; }

                this.flash(data.mensaje);
                setTimeout(() => { window.location.href = '{{ route("reparaciones.index") }}'; }, 1200);
            } catch {
                this.flash('Error de conexión', 'error');
            } finally {
                this.creando = false;
            }
        },

        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo==='error' ? 4500 : 3000);
        },
    }
}
</script>
</x-app-layout>