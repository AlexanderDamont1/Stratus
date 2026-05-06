<x-app-layout>
<div
    x-data="personalManager()"
    x-init="init()"
    class="space-y-6 max-w-4xl mx-auto"
>

    {{-- ══ TOAST ══ --}}
    <div x-show="flash.msg" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 min-w-[300px] max-w-sm pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 px-4 py-3 shadow-lg ring-1"
             :class="{
                 'ring-green-200 dark:ring-green-800': flash.tipo === 'ok',
                 'ring-red-200   dark:ring-red-800':   flash.tipo === 'error'
             }">
            <span class="w-2 h-2 rounded-full shrink-0"
                  :class="{ 'bg-green-500': flash.tipo === 'ok', 'bg-red-500': flash.tipo === 'error' }"></span>
            <p class="text-sm font-medium text-gray-800 dark:text-white flex-1" x-text="flash.msg"></p>
        </div>
    </div>

    {{-- ══ MODAL BASE ══ --}}
    <template x-teleport="body">
        <div x-show="modal.open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
                 @click="cerrarModal()"></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 @click.stop>

                {{-- Header modal --}}
                <div class="flex items-center justify-between px-6 py-5 border-b dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white"
                            x-text="modal.modo === 'crear' ? 'Nuevo vendedor' : 'Editar vendedor'"></h3>
                        <p class="text-xs text-gray-400 mt-0.5"
                           x-text="modal.modo === 'crear'
                               ? 'Asigna el vendedor a una o más sucursales'
                               : 'Actualiza el nombre o las sucursales asignadas'"></p>
                    </div>
                    <button @click="cerrarModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body modal --}}
                <div class="px-6 py-5 space-y-5">

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Nombre completo <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               x-model="modal.nombre"
                               @keyup.enter="guardar()"
                               placeholder="Ej: Carlos Hernández"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                      px-4 py-2.5 text-sm bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-gray-400
                                      placeholder:text-gray-300 dark:placeholder:text-gray-600 transition">
                        <template x-if="modal.errores.nombre">
                            <p class="text-xs text-red-400 mt-1" x-text="modal.errores.nombre"></p>
                        </template>
                    </div>

                    {{-- Sucursales --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                            Sucursales asignadas <span class="text-red-400">*</span>
                        </label>

                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            @foreach($sucursales as $s)
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                   :class="modal.sucursales.includes('{{ $s->id_usuario }}')
                                       ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/50'
                                       : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="checkbox"
                                       value="{{ $s->id_usuario }}"
                                       :checked="modal.sucursales.includes('{{ $s->id_usuario }}')"
                                       @change="toggleSucursal('{{ $s->id_usuario }}')"
                                       class="sr-only">
                                {{-- Check visual --}}
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center shrink-0 transition"
                                     :class="modal.sucursales.includes('{{ $s->id_usuario }}')
                                         ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white'
                                         : 'border-gray-300 dark:border-gray-600'">
                                    <svg x-show="modal.sucursales.includes('{{ $s->id_usuario }}')"
                                         class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                                        {{ $s->nombre_usuario }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">{{ $s->correo }}</p>
                                </div>
                                {{-- Badge de personal existente --}}
                                <template x-if="conteoPersonalPorSucursal['{{ $s->id_usuario }}']">
                                    <span class="shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full
                                                 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400"
                                          x-text="conteoPersonalPorSucursal['{{ $s->id_usuario }}'] + ' vendedor(es)'"></span>
                                </template>
                            </label>
                            @endforeach
                        </div>

                        <template x-if="modal.errores.sucursales">
                            <p class="text-xs text-red-400 mt-1.5" x-text="modal.errores.sucursales"></p>
                        </template>
                    </div>

                    {{-- Activo (solo en edición) --}}
                    <template x-if="modal.modo === 'editar'">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative w-9 h-5 shrink-0">
                                <input type="checkbox" x-model="modal.activo" class="sr-only peer">
                                <div class="w-9 h-5 rounded-full transition peer-checked:bg-gray-900 dark:peer-checked:bg-white
                                            bg-gray-200 dark:bg-gray-600 cursor-pointer"
                                     @click="modal.activo = !modal.activo"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white dark:bg-gray-900
                                            shadow transition-transform peer-checked:translate-x-4 pointer-events-none"
                                     :class="modal.activo ? 'translate-x-4' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Vendedor activo</span>
                        </label>
                    </template>

                </div>

                {{-- Footer modal --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4
                            bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700">
                    <button type="button" @click="cerrarModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300
                                   hover:text-gray-900 dark:hover:text-white transition">
                        Cancelar
                    </button>
                    <button type="button" @click="guardar()"
                            :disabled="guardando || !modal.nombre.trim() || modal.sucursales.length === 0"
                            class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold
                                   bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                   hover:opacity-90 active:scale-[.98] transition
                                   disabled:opacity-30 disabled:cursor-not-allowed">
                        <template x-if="guardando">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </template>
                        <span x-text="guardando ? 'Guardando…' : (modal.modo === 'crear' ? 'Crear vendedor' : 'Guardar cambios')"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ HEADER ══ --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personal</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los vendedores de tus sucursales.</p>
        </div>
        <button @click="abrirCrear()"
                class="flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900
                       text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo vendedor
        </button>
    </div>

    {{-- Flash sesión --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0"
             class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800
                    text-green-700 dark:text-green-400 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ══ FILTRO POR SUCURSAL ══ --}}
    @if($sucursales->count() > 1)
    <div class="flex items-center gap-2 flex-wrap">
        <button @click="filtroSucursal = ''"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                :class="filtroSucursal === ''
                    ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'">
            Todas las sucursales
        </button>
        @foreach($sucursales as $s)
        <button @click="filtroSucursal = '{{ $s->id_usuario }}'"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                :class="filtroSucursal === '{{ $s->id_usuario }}'
                    ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'">
            {{ $s->nombre_usuario }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- ══ TABLA DE PERSONAL ══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

        {{-- Header tabla --}}
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Vendedores registrados</h3>
            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full"
                  x-text="personalFiltrado.length + ' resultado(s)'"></span>
        </div>

        {{-- Empty state --}}
        <template x-if="personalFiltrado.length === 0">
            <div class="px-6 py-16 text-center">
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No hay vendedores</p>
                <p class="text-xs text-gray-400 mt-1">
                    <template x-if="filtroSucursal !== ''">
                        <span>No hay personal en esta sucursal.</span>
                    </template>
                    <template x-if="filtroSucursal === ''">
                        <span>Crea el primer vendedor con el botón de arriba.</span>
                    </template>
                </p>
            </div>
        </template>

        {{-- Lista --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
            <template x-for="p in personalFiltrado" :key="p.id_personal">
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">

                    {{-- Avatar inicial --}}
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm"
                         :class="p.activo
                             ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                             : 'bg-gray-200 dark:bg-gray-700 text-gray-400'">
                        <span x-text="p.nombre.charAt(0).toUpperCase()"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate"
                               x-text="p.nombre"
                               :class="!p.activo && 'line-through text-gray-400'"></p>
                            <template x-if="!p.activo">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-gray-100 dark:bg-gray-700 text-gray-400 uppercase tracking-wide">
                                    Inactivo
                                </span>
                            </template>
                        </div>
                        {{-- Sucursales donde está asignado --}}
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                            <template x-for="suc in p.sucursales" :key="suc.id_usuario">
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-600 dark:text-blue-400
                                             border border-blue-200 dark:border-blue-800"
                                      x-text="suc.nombre_usuario"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
                        <button @click="abrirEditar(p)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg
                                       text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                title="Editar">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="confirmarEliminar(p)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg
                                       text-gray-300 hover:text-red-500 dark:hover:text-red-400
                                       hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                :title="p.activo ? 'Desactivar' : 'Activar'">
                            <template x-if="p.activo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </template>
                            <template x-if="!p.activo">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
function personalManager() {
    return {
        
        personal:              @json($personal),
        sucursalesDisponibles: @json($sucursalesJs),

        filtroSucursal: '',
        guardando:      false,
        flash:          { msg: '', tipo: 'ok', _t: null },

        // ── Modal ─────────────────────────────────────────────────────────────
        modal: {
            open:      false,
            modo:      'crear',   // 'crear' | 'editar'
            id:        null,
            nombre:    '',
            sucursales: [],       // array de id_usuario seleccionados
            activo:    true,
            errores:   {},
        },

        // Conteo de personal por sucursal para el badge en los checkboxes
        get conteoPersonalPorSucursal() {
            const conteo = {};
            this.personal.forEach(p => {
                if (!p.activo) return;
                p.sucursales.forEach(s => {
                    conteo[s.id_usuario] = (conteo[s.id_usuario] || 0) + 1;
                });
            });
            return conteo;
        },

        // ── Personal filtrado ─────────────────────────────────────────────────
        get personalFiltrado() {
            if (!this.filtroSucursal) return this.personal;
            return this.personal.filter(p =>
                p.sucursales.some(s => s.id_usuario === this.filtroSucursal)
            );
        },

        // ── Init ──────────────────────────────────────────────────────────────
        init() {},

        // ── Modal helpers ─────────────────────────────────────────────────────
        abrirCrear() {
            this.modal = {
                open: true, modo: 'crear', id: null,
                nombre: '', sucursales: [], activo: true, errores: {},
            };
        },
        abrirEditar(p) {
            this.modal = {
                open:      true,
                modo:      'editar',
                id:        p.id_personal,
                nombre:    p.nombre,
                sucursales: p.sucursales.map(s => s.id_usuario),
                activo:    p.activo,
                errores:   {},
            };
        },
        cerrarModal() {
            this.modal.open = false;
        },
        toggleSucursal(id) {
            const idx = this.modal.sucursales.indexOf(id);
            if (idx >= 0) this.modal.sucursales.splice(idx, 1);
            else          this.modal.sucursales.push(id);
        },

        // ── CRUD ──────────────────────────────────────────────────────────────
        async guardar() {
            this.modal.errores = {};

            if (!this.modal.nombre.trim()) {
                this.modal.errores.nombre = 'El nombre es obligatorio.';
                return;
            }
            if (this.modal.sucursales.length === 0) {
                this.modal.errores.sucursales = 'Selecciona al menos una sucursal.';
                return;
            }

            this.guardando = true;
            try {
                const esCrear = this.modal.modo === 'crear';
                const url     = esCrear
                    ? '{{ route("admin.personal.store") }}'
                    : '{{ url("admin/personal") }}/' + this.modal.id;

                const body = new URLSearchParams();
                body.append('_token', '{{ csrf_token() }}');
                if (!esCrear) body.append('_method', 'PUT');
                body.append('nombre', this.modal.nombre.trim());
                body.append('activo', this.modal.activo ? '1' : '0');
                this.modal.sucursales.forEach(id => body.append('sucursales[]', id));

                const res  = await fetch(url, {
                    method:  'POST',
                    headers: { 'Accept': 'application/json' },
                    body,
                });
                const data = await res.json();

                if (!res.ok) {
                    // Errores de validación Laravel
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => {
                            this.modal.errores[k] = v[0];
                        });
                    } else {
                        this.mostrarFlash(data.message || 'Error al guardar.', 'error');
                    }
                    return;
                }

                // Actualizar estado local sin recargar
                if (esCrear) {
                    this.personal.push(data.personal);
                } else {
                    const idx = this.personal.findIndex(p => p.id_personal === this.modal.id);
                    if (idx >= 0) this.personal[idx] = data.personal;
                }

                this.cerrarModal();
                this.mostrarFlash(data.message || 'Guardado correctamente.', 'ok');

            } catch {
                this.mostrarFlash('Error de conexión.', 'error');
            } finally {
                this.guardando = false;
            }
        },

        async confirmarEliminar(p) {
            const accion = p.activo ? 'desactivar' : 'activar';
            if (!confirm(`¿${accion.charAt(0).toUpperCase() + accion.slice(1)} a ${p.nombre}?`)) return;

            try {
                const res  = await fetch(`{{ url('admin/personal') }}/${p.id_personal}`, {
                    method:  'POST',
                    headers: { 'Accept': 'application/json' },
                    body:    new URLSearchParams({
                        _token:  '{{ csrf_token() }}',
                        _method: 'DELETE',
                    }),
                });
                const data = await res.json();
                if (!res.ok) { this.mostrarFlash(data.message || 'Error.', 'error'); return; }

                // Toggle local
                const item = this.personal.find(x => x.id_personal === p.id_personal);
                if (item) item.activo = !item.activo;
                this.mostrarFlash(data.message || 'Actualizado.', 'ok');

            } catch {
                this.mostrarFlash('Error de conexión.', 'error');
            }
        },

        // ── Flash ─────────────────────────────────────────────────────────────
        mostrarFlash(msg, tipo = 'ok') {
            if (this.flash._t) clearTimeout(this.flash._t);
            this.flash.msg  = msg;
            this.flash.tipo = tipo;
            this.flash._t   = setTimeout(() => { this.flash.msg = ''; }, 3000);
        },
    };
}
</script>
</x-app-layout>