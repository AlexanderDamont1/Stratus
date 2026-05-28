<x-app-layout>
<div class="mx-auto space-y-7" x-data="reporteRoboPage()" x-init="init()">

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap items-start justify-between gap-4 sm:gap-2">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Reporte de robo</h2>
            <p class="text-xs text-gray-400 mt-0.5">Registra el robo de un vehículo ArrowX</p>
        </div>
    </div>

    {{-- ===== FLASH ===== --}}
    <x-flash-messages />

    {{-- ===== VEHÍCULOS EN CUSTODIA ===== --}}
    <div x-data="custodiaSection()" x-init="cargar()">
        <template x-if="vehiculos.length > 0">
            <div class="mb-2">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
                        Vehículos en custodia — esperando recolección del dueño
                    </p>
                </div>
                <div class="space-y-3">
                    <template x-for="v in vehiculos" :key="v.id_reporte">
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                                    dark:border-amber-800 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1">
                                    <p class="text-xs font-mono font-semibold text-gray-900 dark:text-white"
                                    x-text="v.bicicleta?.num_serie ?? '—'"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="[v.bicicleta?.modelo?.marca?.nombre_marca, v.bicicleta?.modelo?.nombre_modelo].filter(Boolean).join(' ')"></p>
                                    <p class="text-xs text-gray-400">
                                        Cliente:
                                        <span class="font-medium text-gray-700 dark:text-gray-300"
                                            x-text="[v.cliente?.nombre_cliente, v.cliente?.apellido1].filter(Boolean).join(' ')"></span>
                                        · <span x-text="v.cliente?.telefono ?? '—'"></span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Reportado por:
                                        <span x-text="v.negocio_reporta?.nombre_negocio ?? '—'"></span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        En custodia desde:
                                        <span x-text="v.encontrado_at
                                            ? new Date(v.encontrado_at).toLocaleDateString('es-MX')
                                            : '—'"></span>
                                    </p>
                                </div>
                                <button @click="entregar(v.id_reporte)"
                                        :disabled="entregando === v.id_reporte"
                                        class="shrink-0 bg-amber-600 hover:bg-amber-700 text-white
                                            px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                            disabled:opacity-40 active:scale-95 whitespace-nowrap">
                                    <span x-show="entregando !== v.id_reporte">✓ Marcar entregado</span>
                                    <span x-show="entregando === v.id_reporte">Guardando...</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>


    {{-- ===== PASO 1: BUSCAR SERIE ===== --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 max-w-lg">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Paso 1 — Buscar vehículo</p>

        <div class="flex gap-2">
            <input
                type="text"
                x-model="serie"
                @keydown.enter="buscarSerie()"
                placeholder="Número de serie (17 caracteres)"
                maxlength="17"
                class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 uppercase"
                :disabled="buscando || resultado !== null">
            <button
                @click="buscarSerie()"
                :disabled="buscando || serie.length < 3 || resultado !== null"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                <span x-show="!buscando">Buscar</span>
                <span x-show="buscando" class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Buscando...
                </span>
            </button>
        </div>

        {{-- Reset --}}
        <button x-show="resultado !== null" @click="resetear()"
            class="mt-3 text-xs text-gray-400 hover:text-red-500 transition">
            ← Buscar otro vehículo
        </button>
    </div>

    {{-- ===== PASO 2: CONFIRMAR DATOS ===== --}}
    <div x-show="resultado !== null" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2">

        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Paso 2 — Confirma los datos con el cliente</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">

            {{-- Datos del vehículo --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Vehículo</p>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-400">N° de serie</dt>
                        <dd class="text-xs font-mono font-semibold text-gray-900 dark:text-white" x-text="resultado?.bici?.num_serie"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-400">Marca</dt>
                        <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.bici?.marca"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-400">Modelo</dt>
                        <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.bici?.modelo"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-400">Voltaje</dt>
                        <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.bici?.voltaje"></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-gray-400">Color</dt>
                        <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.bici?.color?.split('|')[0]?.trim()"></dd>
                    </div>
                </dl>
            </div>

            {{-- Datos del cliente --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Cliente</p>
                <template x-if="resultado?.cliente">
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-400">Nombre</dt>
                            <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.cliente?.nombre"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-400">Teléfono</dt>
                            <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.cliente?.telefono"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-400">Correo</dt>
                            <dd class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[140px]" x-text="resultado?.cliente?.correo || '—'"></dd>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-gray-100 dark:border-gray-700 mt-1">
                            <dt class="text-xs text-gray-400">Comprado en</dt>
                            <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.compra?.negocio || '—'"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-400">Fecha de compra</dt>
                            <dd class="text-xs font-medium text-gray-900 dark:text-white" x-text="resultado?.compra?.fecha || '—'"></dd>
                        </div>
                    </dl>
                </template>
                <template x-if="!resultado?.cliente">
                    <p class="text-xs text-gray-400">No se encontró un cliente registrado para este vehículo.</p>
                </template>
            </div>
        </div>

        {{-- Alerta si no tiene correo --}}
        <div x-show="resultado?.cliente && !resultado?.cliente?.correo"
            class="mt-4 max-w-2xl flex items-start gap-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
            <svg class="w-4 h-4 text-yellow-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                El cliente no tiene correo registrado. Se levantará el reporte pero <strong>no se podrá enviar la notificación por correo</strong>.
            </p>
        </div>

        {{-- ===== PASO 3: LEVANTAR REPORTE ===== --}}
        <div class="mt-6 max-w-2xl">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Paso 3 — Levantar reporte</p>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notas adicionales <span class="text-gray-400">(opcional)</span></label>
                    <textarea
                        x-model="notas"
                        rows="3"
                        placeholder="Describe las circunstancias del robo, lugar, hora aproximada..."
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none">
                    </textarea>
                </div>

                <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Al levantar el reporte, el sistema enviará un correo al cliente para que <strong>confirme el robo</strong>.
                        Una vez confirmado, el vehículo quedará marcado en toda la red ArrowX.
                    </p>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="levantarReporte()"
                        :disabled="levantando || !resultado?.cliente"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span x-show="!levantando">Levantar reporte de robo</span>
                        <span x-show="levantando" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Registrando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: REPORTE LEVANTADO ===== --}}
    <div x-show="exitoModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4">
        <div x-show="exitoModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>

            <div class="flex flex-col items-center text-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reporte registrado</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Se envió un correo al cliente para confirmar el robo.
                    El vehículo quedará marcado en la red ArrowX al confirmarse.
                </p>
                <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left">
                    <p class="text-xs text-gray-400 mb-1">Folio del reporte</p>
                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white" x-text="folioReporte"></p>
                </div>
            </div>

            <button @click="exitoModal = false; resetear()"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition active:scale-95">
                Cerrar
            </button>
        </div>
    </div>

    {{-- ===== MODAL: VEHÍCULO CON REPORTE DE ROBO (al buscar serie ya reportada) ===== --}}
    <div x-show="roboModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4">
        <div x-show="roboModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>

            <div class="flex flex-col items-center text-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Este vehículo ya tiene un reporte activo</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No se puede levantar un nuevo reporte porque este vehículo ya fue reportado como robado.
                </p>
                <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left space-y-1">
                    <p class="text-xs text-gray-400">Folio</p>
                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white" x-text="roboInfo.folio"></p>
                </div>
            </div>

            <button @click="roboModal = false; resetear()"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition active:scale-95">
                Entendido
            </button>
        </div>
    </div>

    {{-- ===== ALPINE JS ===== --}}
    <script>

    function custodiaSection() {
        return {
            vehiculos:  [],
            entregando: null,

            async cargar() {
                try {
                    const res  = await fetch('{{ route("robo.custodia") }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    if (data.ok) this.vehiculos = data.data;
                } catch {}
            },

            async entregar(idReporte) {
                this.entregando = idReporte;
                try {
                    const res  = await fetch(`{{ url('sucursal/reporte/robo/entregar') }}/${idReporte}`, {
                        method:  'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept':       'application/json',
                        },
                    });
                    const data = await res.json();
                    if (data.ok) {
                        this.vehiculos = this.vehiculos.filter(v => v.id_reporte !== idReporte);
                    }
                } catch {
                } finally {
                    this.entregando = null;
                }
            },
        }
    }
    
    function reporteRoboPage() {
        return {
            serie:        '',
            notas:        '',
            buscando:     false,
            levantando:   false,
            resultado:    null,
            exitoModal:   false,
            roboModal:    false,
            folioReporte: '',
            roboInfo:     { folio: '' },
            flashVisible: false,
            flashMsg:     '',
            flashTipo:    'success',
            flashTimer:   null,

            init() {},

            flash(msg, tipo = 'success') {
                this.flashMsg     = msg;
                this.flashTipo    = tipo;
                this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
            },

            resetear() {
                this.serie     = '';
                this.notas     = '';
                this.resultado = null;
            },

            async buscarSerie() {
                if (!this.serie.trim()) return;
                this.buscando = true;
                try {
                    const res = await fetch(`{{ route('robo.buscar') }}?num_serie=${this.serie.toUpperCase().trim()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();

                    if (res.status === 409) {
                        // Ya tiene reporte activo
                        this.roboInfo = { folio: data.folio ?? '—' };
                        this.roboModal = true;
                        return;
                    }

                    if (!res.ok) {
                        this.flash(data.mensaje || 'Vehículo no encontrado.', 'error');
                        return;
                    }

                    this.resultado = data;

                } catch {
                    this.flash('Error de conexión.', 'error');
                } finally {
                    this.buscando = false;
                }
            },

            async levantarReporte() {
                if (!this.resultado?.cliente) return;
                this.levantando = true;
                try {
                    const res = await fetch('{{ route('robo.reportar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify({
                            num_serie:  this.resultado.bici.num_serie,
                            id_cliente: this.resultado.cliente.id_cliente,
                            notas:      this.notas,
                        }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        if (res.status === 409) {
                            this.roboInfo  = { folio: data.folio ?? '—' };
                            this.roboModal = true;
                            return;
                        }
                        this.flash(data.mensaje || 'Error al registrar el reporte.', 'error');
                        return;
                    }

                    this.folioReporte = data.folio;
                    this.exitoModal   = true;

                } catch {
                    this.flash('Error de conexión.', 'error');
                } finally {
                    this.levantando = false;
                }
            },
        }
    }
    </script>

</div>
</x-app-layout>