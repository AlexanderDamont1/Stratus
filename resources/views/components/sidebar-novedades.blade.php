{{-- resources/views/components/sidebar-novedades.blade.php --}}
@php
    use App\Services\NovedadService;
    $esRoot       = Auth::user()->id_rol === 0;
    $todasLasNovs = NovedadService::all();
@endphp

@if(!empty($todasLasNovs) || $esRoot)
<div
    x-data="sidebarNovedades()"
    x-init="init()"
    class="border-t border-gray-200 dark:border-gray-700 px-2 py-2.5 shrink-0">

    {{-- Header (igual) --}}
    <div class="flex items-center justify-between px-1 mb-2">
        <div class="flex items-center gap-1.5">
            <div class="relative">
                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span x-show="visibles.length > 0" 
                      x-transition:enter="transition ease-out duration-300"
                      x-transition:enter-start="scale-0"
                      x-transition:enter-end="scale-100"
                      class="absolute -top-1 -right-1.5 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            </div>
            <span class="nav-group-label" style="padding:0">Novedades</span>
        </div>

        @if($esRoot)
        <button @click="modalAbierto = true"
                class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-500
                       hover:text-gray-600 dark:hover:text-gray-300 transition px-1.5 py-0.5
                       rounded hover:bg-gray-100 dark:hover:bg-gray-800">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Gestionar
        </button>
        @endif
    </div>

    {{-- Carrusel con animación crossfade suave --}}
    <div class="relative min-h-[80px]">
        {{-- Tarjeta actual con animación de opacidad pura --}}
        <template x-if="currentNovedad">
            <div :key="currentNovedad.id"
                 @mouseenter="pauseRotation"
                 @mouseleave="resumeRotation"
                 x-transition:enter="transition ease-in-out duration-800"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in-out duration-600"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="relative rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 group"
                 :class="{
                     'bg-blue-100 dark:bg-blue-800/30':      currentNovedad.tipo === 'info',
                     'bg-red-100 dark:bg-red-800/30':        currentNovedad.tipo === 'warning',
                     'bg-emerald-100 dark:bg-emerald-800/30': currentNovedad.tipo === 'success',
                     'bg-purple-100 dark:bg-purple-800/30':   currentNovedad.tipo === 'danger',
                 }">

                {{-- Barra lateral (sin cambios) --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-xl"
                     :class="{
                         'bg-blue-500 shadow-[0_0_4px_rgba(59,130,246,0.5)]':     currentNovedad.tipo === 'info',
                         'bg-red-500 shadow-[0_0_4px_rgba(239,68,68,0.5)]':       currentNovedad.tipo === 'warning',
                         'bg-emerald-500 shadow-[0_0_4px_rgba(16,185,129,0.5)]':   currentNovedad.tipo === 'success',
                         'bg-purple-500 shadow-[0_0_4px_rgba(168,85,247,0.5)]':    currentNovedad.tipo === 'danger',
                     }"></div>

                <div class="pl-3 pr-7 py-2.5">
                    <div class="flex items-start gap-2">
                        <div class="shrink-0 mt-0.5">
                            <svg x-show="currentNovedad.tipo === 'info'" class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="currentNovedad.tipo === 'warning'" class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <svg x-show="currentNovedad.tipo === 'success'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="currentNovedad.tipo === 'danger'" class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[11.5px] font-semibold leading-tight"
                               :class="{
                                   'text-blue-900 dark:text-blue-200':     currentNovedad.tipo === 'info',
                                   'text-red-900 dark:text-red-200':       currentNovedad.tipo === 'warning',
                                   'text-emerald-900 dark:text-emerald-200': currentNovedad.tipo === 'success',
                                   'text-purple-900 dark:text-purple-200': currentNovedad.tipo === 'danger',
                               }"
                               x-text="currentNovedad.titulo"></p>
                            <p class="text-[11px] mt-0.5 leading-snug"
                               :class="{
                                   'text-blue-800 dark:text-blue-300':     currentNovedad.tipo === 'info',
                                   'text-red-800 dark:text-red-300':       currentNovedad.tipo === 'warning',
                                   'text-emerald-800 dark:text-emerald-300': currentNovedad.tipo === 'success',
                                   'text-purple-800 dark:text-purple-300': currentNovedad.tipo === 'danger',
                               }"
                               x-text="currentNovedad.mensaje"></p>
                        </div>
                    </div>
                </div>

                @if($esRoot)
                <button @click="descartar(currentNovedad.id)"
                        class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-0.5 rounded-md hover:bg-white/20"
                        :class="{
                            'text-blue-500 hover:text-blue-700':    currentNovedad.tipo === 'info',
                            'text-red-500 hover:text-red-700':      currentNovedad.tipo === 'warning',
                            'text-emerald-500 hover:text-emerald-700': currentNovedad.tipo === 'success',
                            'text-purple-500 hover:text-purple-700': currentNovedad.tipo === 'danger',
                        }">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>
        </template>

        {{-- Mensaje vacío --}}
        <p x-show="visibles.length === 0 && {{ $esRoot ? 'true' : 'false' }}"
           class="text-[11px] text-gray-400 dark:text-gray-600 text-center py-2 px-2 italic">
            ✨ Sin novedades activas. Crea una con "Gestionar".
        </p>
    </div>

   {{-- MODAL ADMIN --}}
    @if($esRoot)
    <div x-show="modalAbierto" x-cloak class="fixed inset-0 z-[90] flex items-end sm:items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm" @click="modalAbierto = false"></div>

        <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop>

            <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Gestionar novedades</p>
                    <p class="text-xs text-gray-400 mt-0.5">Visible para todos los usuarios</p>
                </div>
                <button @click="modalAbierto = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-5 py-3 max-h-56 overflow-y-auto space-y-2">
                <template x-if="lista.length === 0">
                    <p class="text-xs text-gray-400 dark:text-gray-600 text-center py-4">Sin novedades. Crea la primera abajo.</p>
                </template>
                <template x-for="n in lista" :key="n.id">
                    <div class="flex items-start gap-2.5 p-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 group transition hover:shadow-sm">
                        <span class="shrink-0 mt-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded-md uppercase"
                              :class="{
                                  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300':     n.tipo === 'info',
                                  'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300':        n.tipo === 'warning',
                                  'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300': n.tipo === 'success',
                                  'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300': n.tipo === 'danger',
                              }"
                              x-text="n.tipo"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate" x-text="n.titulo"></p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate" x-text="n.mensaje"></p>
                        </div>
                        <button @click="toggleActiva(n)"
                                class="shrink-0 opacity-0 group-hover:opacity-100 transition"
                                :title="n.activa ? 'Desactivar' : 'Activar'">
                            <div class="relative inline-flex h-4 w-7 rounded-full transition-colors"
                                 :class="n.activa ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                                <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform"
                                      :class="n.activa ? 'translate-x-3' : 'translate-x-0'"></span>
                            </div>
                        </button>
                        <button @click="eliminar(n.id)"
                                class="shrink-0 text-gray-300 dark:text-gray-600 hover:text-red-500 dark:hover:text-red-400 transition opacity-0 group-hover:opacity-100 p-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4 space-y-3">
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Nueva novedad</p>
                <div class="flex gap-2">
                    <template x-for="t in ['info','warning','success','danger']" :key="t">
                        <button type="button" @click="form.tipo = t"
                            :class="{
                                'ring-2 ring-offset-1 ring-gray-900 dark:ring-white': form.tipo === t,
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400':       t === 'info',
                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400':           t === 'warning',
                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400': t === 'success',
                                'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400': t === 'danger',
                            }"
                            class="text-[10px] font-semibold uppercase px-2.5 py-1 rounded-lg transition"
                            x-text="t">
                        </button>
                    </template>
                </div>
                <input type="text" x-model="form.titulo" placeholder="Título de la novedad" maxlength="80"
                    class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-500">
                <textarea x-model="form.mensaje" placeholder="Mensaje breve (máx. 200 caracteres)"
                    maxlength="200" rows="2"
                    class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-500 resize-none"></textarea>
                <button @click="crear()"
                    :disabled="guardando || !form.titulo.trim() || !form.mensaje.trim()"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-2.5 rounded-xl text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98] hover:opacity-90 flex items-center justify-center gap-2">
                    <svg x-show="guardando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="guardando ? 'Publicando...' : 'Publicar novedad'"></span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

@once
@push('scripts')
<script>
function sidebarNovedades() {
    return {
        modalAbierto: false,
        guardando: false,
        descartadas: JSON.parse(sessionStorage.getItem('nov_descartadas') || '[]'),
        lista: @json($todasLasNovs),
        form: { tipo: 'info', titulo: '', mensaje: '' },

        currentIndex: 0,
        rotationInterval: null,
        intervalMs: 30000, // 5 minutos
        hoverPaused: false,

        get visibles() {
            @if($esRoot)
            return this.lista.filter(n => n.activa && !this.descartadas.includes(n.id));
            @else
            return this.lista.filter(n => n.activa);
            @endif
        },

        get currentNovedad() {
            if (this.visibles.length === 0) return null;
            return this.visibles[this.currentIndex] || null;
        },

        init() {
            this.initRotation();
            this.$watch('lista', () => this.refreshRotation());
            this.$watch('descartadas', () => this.refreshRotation());
        },

        next() {
            if (this.visibles.length === 0) return;
            this.currentIndex = (this.currentIndex + 1) % this.visibles.length;
        },

        pauseRotation() {
            this.hoverPaused = true;
            if (this.rotationInterval) clearInterval(this.rotationInterval);
        },

        resumeRotation() {
            this.hoverPaused = false;
            if (this.visibles.length > 1) this.startRotation();
        },

        startRotation() {
            if (this.rotationInterval) clearInterval(this.rotationInterval);
            this.rotationInterval = setInterval(() => {
                if (!this.hoverPaused) this.next();
            }, this.intervalMs);
        },

        stopRotation() {
            if (this.rotationInterval) {
                clearInterval(this.rotationInterval);
                this.rotationInterval = null;
            }
        },

        refreshRotation() {
            if (this.visibles.length === 0) {
                this.stopRotation();
                this.currentIndex = 0;
                return;
            }
            if (this.currentIndex >= this.visibles.length || !this.currentNovedad) {
                this.currentIndex = 0;
            }
            this.stopRotation();
            if (this.visibles.length > 1) this.startRotation();
        },

        initRotation() {
            this.refreshRotation();
        },

        descartar(id) {
            this.descartadas = [...this.descartadas, id];
            sessionStorage.setItem('nov_descartadas', JSON.stringify(this.descartadas));
            this.refreshRotation();
        },

        async crear() {
            if (!this.form.titulo.trim() || !this.form.mensaje.trim()) return;
            this.guardando = true;
            try {
                const res = await fetch('{{ route("root.novedades.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (data.ok) {
                    this.lista = data.novedades;
                    this.form = { tipo: 'info', titulo: '', mensaje: '' };
                }
            } catch(e) { console.error(e); }
            finally { this.guardando = false; }
        },

        async toggleActiva(n) {
            const nueva = !n.activa;
            this.lista = this.lista.map(x => x.id === n.id ? { ...x, activa: nueva } : x);
            try {
                await fetch(`/root/novedades/${n.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ activa: nueva }),
                });
            } catch(e) {
                this.lista = this.lista.map(x => x.id === n.id ? { ...x, activa: !nueva } : x);
            }
        },

        async eliminar(id) {
            const anterior = this.lista;
            this.lista = this.lista.filter(n => n.id !== id);
            try {
                await fetch(`/root/novedades/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                this.descartar(id);
            } catch(e) {
                this.lista = anterior;
            }
        },
    }
}
</script>
@endpush
@endonce