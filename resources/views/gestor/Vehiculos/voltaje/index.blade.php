<x-app-layout>

<style>
    @keyframes flash-bg {
        0% { background-color: #fef08a; }  /* amarillo claro */
        100% { background-color: transparent; }
    }
    .animate-flash {
        animation: flash-bg 0.6s ease-out;
    }
    /* Modo oscuro: ajuste del color de flash */
    @media (prefers-color-scheme: dark) {
        @keyframes flash-bg-dark {
            0% { background-color: #facc15; }  /* amarillo más intenso */
            100% { background-color: transparent; }
        }
        .animate-flash {
            animation: flash-bg-dark 0.6s ease-out;
        }
    }
</style>

<div
    x-data="voltajesPage()"
    x-init="init()"
    class="space-y-6">

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Voltajes</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los voltajes del catálogo de bicicletas</p>
        </div>
        <button
            @click="createModal = true; $nextTick(() => $refs.inputVoltaje.focus())"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition active:scale-[0.98] shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Crear voltaje
        </button>
    </div>

    {{-- ===== FLASH TOAST ===== --}}
    <x-flash-toast />

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-2 py-2 sm:px-4 sm:py-3">
            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider mb-0.5 sm:mb-1">Total voltajes</p>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white" x-text="stats.total"></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-2 py-2 sm:px-4 sm:py-3">
            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider mb-0.5 sm:mb-1">Esta página</p>
            <p class="text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-400" x-text="voltajes.length"></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-2 py-2 sm:px-4 sm:py-3">
            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider mb-0.5 sm:mb-1">Página actual</p>
            <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $voltajes->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $voltajes->lastPage() }}</span>
            </p>
        </div>
    </div>

    {{-- ===== GRID DE TARJETAS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Voltajes registrados</h3>
            <span class="text-xs text-gray-400" x-text="stats.total + ' total'"></span>
        </div>

        <div class="p-6">
            {{-- Empty state --}}
            <div x-show="voltajes.length === 0" class="flex flex-col items-center justify-center py-16 gap-3 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <p class="text-sm">No hay voltajes registrados. Crea el primero usando el botón superior.</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                <template x-for="v in voltajes" :key="v.id_voltaje">
                    <button
                        @click="openEdit(v)"
                        class="group relative flex flex-col items-center gap-2 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-900 hover:border-gray-400 dark:hover:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all"
                        :class="{ 'animate-flash': flashId === v.id_voltaje }">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate w-full text-center" x-text="v.voltaje"></span>
                        <span class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                            </svg>
                        </span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Paginación --}}
        @if($voltajes->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $voltajes->links() }}
        </div>
        @endif
    </div>

    {{-- ===== MODAL: CREAR VOLTAJE ===== --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
        @click.self="createModal = false"
        @keydown.escape.window="createModal = false">
        <div
            x-show="createModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md"
            @click.stop>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nuevo voltaje</h3>
                </div>
                <button
                    @click="createModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Voltaje <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    x-model="nuevoVoltaje"
                    x-ref="inputVoltaje"
                    @keydown.enter="crearVoltaje()"
                    placeholder="Ej: 12V, 24V, 36V"
                    maxlength="10"
                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                       rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-500 transition">
                <p x-show="createError" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    class="text-xs text-red-500 mt-1.5" x-text="createError"></p>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    @click="createModal = false"
                    :disabled="creando"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                       transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                       disabled:opacity-40 disabled:cursor-not-allowed">
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="crearVoltaje()"
                    :disabled="creando || !nuevoVoltaje.trim()"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg
                       text-sm font-semibold hover:opacity-90 transition active:scale-[0.98]
                       disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="creando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <span x-text="creando ? 'Guardando...' : 'Guardar voltaje'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: EDITAR VOLTAJE ===== --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
        @click.self="editModal = false"
        @keydown.escape.window="editModal = false">
        <div
            x-show="editModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md"
            @click.stop>
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Editar voltaje</h3>
                </div>
                <button
                    @click="editModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Voltaje <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    x-model="editVoltaje"
                    x-ref="inputEditVoltaje"
                    @keydown.enter="actualizarVoltaje()"
                    placeholder="Ej: 12V, 24V, 36V"
                    maxlength="10"
                    class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                       rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-500 transition">
                <p x-show="editError" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    class="text-xs text-red-500 mt-1.5" x-text="editError"></p>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    @click="editModal = false"
                    :disabled="actualizando"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                       transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                       disabled:opacity-40 disabled:cursor-not-allowed">
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="actualizarVoltaje()"
                    :disabled="actualizando || !editVoltaje.trim() || editVoltaje.trim() === editOriginal"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg
                       text-sm font-semibold hover:opacity-90 transition active:scale-[0.98]
                       disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="actualizando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <span x-text="actualizando ? 'Guardando...' : 'Guardar cambios'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function voltajesPage() {
    return {
        // Estado
        voltajes: @json($voltajes->getCollection()->map(fn($v) => ['id_voltaje' => $v->id_voltaje, 'voltaje' => $v->voltaje])),
        stats: { total: {{ $voltajes->total() }} },

        // Flash
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

        // Modal crear
        createModal: false,
        nuevoVoltaje: '',
        createError: '',
        creando: false,

        // Modal editar
        editModal: false,
        editId: null,
        editVoltaje: '',
        editOriginal: '',
        editError: '',
        actualizando: false,

        // Rol del usuario (inyectado desde Blade)
        userRol: {{ auth()->user()->id_rol }},

        // Flash animation
        flashId: null,

        // Init
        init() {
            this.$watch('createModal', v => {
                if (v) { this.nuevoVoltaje = ''; this.createError = ''; }
            });
            this.$watch('editModal', v => {
                if (!v) { this.editId = null; this.editVoltaje = ''; this.editError = ''; }
            });
        },

        // Abrir modal editar
        openEdit(v) {
            this.editId       = v.id_voltaje;
            this.editVoltaje  = v.voltaje;
            this.editOriginal = v.voltaje;
            this.editModal    = true;
            this.$nextTick(() => this.$refs.inputEditVoltaje.focus());
        },

        // Crear voltaje
        async crearVoltaje() {
            this.createError = '';
            if (!this.nuevoVoltaje.trim()) return;

            this.creando = true;
            try {
                const storeUrl = this.userRol === 1
                    ? '{{ route("admin.catalogo.voltajes.store") }}'
                    : '{{ route("gestor.vehiculos.voltajes.store") }}';

                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ voltaje: this.nuevoVoltaje.trim() }),
                });

                const data = await res.json();

                if (!data.ok) {
                    if (data.errors?.voltaje) {
                        this.createError = data.errors.voltaje[0];
                    } else {
                        this.createError = data.mensaje ?? data.message ?? 'Error al crear.';
                    }
                    return;
                }

                this.voltajes.push(data.voltaje);
                this.stats.total++;
                // Activar flash
                this.flashId = data.voltaje.id_voltaje;
                setTimeout(() => { this.flashId = null; }, 600);
                this.createModal = false;
                this.flash(data.mensaje);

            } catch {
                this.createError = 'Error de conexión.';
            } finally {
                this.creando = false;
            }
        },

        // Actualizar voltaje
        async actualizarVoltaje() {
            this.editError = '';
            if (!this.editVoltaje.trim()) return;

            this.actualizando = true;
            try {
                const updateUrl = this.userRol === 1
                    ? `/admin/catalogo/voltajes/${this.editId}`
                    : `/gestor/vehiculos/voltajes/${this.editId}`;

                const res = await fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ voltaje: this.editVoltaje.trim() }),
                });

                const data = await res.json();

                if (!data.ok) {
                    if (data.errors?.voltaje) {
                        this.editError = data.errors.voltaje[0];
                    } else {
                        this.editError = data.mensaje ?? data.message ?? 'Error al actualizar.';
                    }
                    return;
                }

                const idx = this.voltajes.findIndex(v => v.id_voltaje === this.editId);
                if (idx !== -1) {
                    this.voltajes[idx].voltaje = this.editVoltaje.trim();
                    // Activar flash también en edición (opcional)
                    this.flashId = this.editId;
                    setTimeout(() => { this.flashId = null; }, 600);
                }

                this.editModal = false;
                this.flash(data.mensaje ?? 'Voltaje actualizado correctamente.');

            } catch {
                this.editError = 'Error de conexión.';
            } finally {
                this.actualizando = false;
            }
        },

        // Flash toast
        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
        },
    }
}
</script>
</x-app-layout>