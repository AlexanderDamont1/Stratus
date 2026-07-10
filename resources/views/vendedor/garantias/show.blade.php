{{-- resources/views/vendedor/garantias/show.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-6" x-data="garantiaShow()" x-init="init()">

    

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('garantias.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 flex items-center gap-1 mb-2 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al buscador
            </a>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Garantía — <span class="font-mono">{{ $bici->num_serie }}</span>
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $bici->modelo->marca->nombre_marca ?? '—' }} ·
                {{ $bici->modelo->nombre_modelo ?? '—' }} ·
                {{ $bici->voltaje->voltaje ?? '—' }} ·
                {{ $bici->color->color ?? '—' }}
            </p>
        </div>

        {{-- Leyenda compacta --}}
        @if(!$garantias->isEmpty())
        <div class="flex items-center gap-3 mt-1 shrink-0">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-[12px] text-gray-500 dark:text-gray-400">Vigente</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                <span class="text-[12px] text-gray-500 dark:text-gray-400">Por vencer</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-[12px] text-gray-500 dark:text-gray-400">Expirada</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Flash --}}
    <x-flash-messages />

    {{-- Flash propio (para mensajes generados en JS) --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo === 'error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COMPONENTES CON GARANTÍA
    ══════════════════════════════════════════════════════ --}}
    @if($garantias->isEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-10 text-center">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Esta bicicleta no tiene garantías configuradas para su marca.</p>
        </div>
    @else
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Componentes ({{ $garantias->count() }})
            </p>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <template x-for="g in componentes" :key="g.id">
                <button type="button" @click="abrirDetalle(g)"
                        class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors duration-150
                               hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <span class="w-2 h-2 rounded-full shrink-0" :class="dotClass(g)"></span>

                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200 w-24 sm:w-40 shrink-0 truncate"
                          x-text="g.nombre"></span>

                    <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" :class="barClass(g)"
                             :style="'width: ' + g.porcentaje_vida + '%'"></div>
                    </div>

                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-12 text-right shrink-0"
                          x-text="g.dias_restantes > 0 ? g.dias_restantes + 'd' : 'Exp.'"></span>

                    <span class="hidden sm:inline-block text-[12px] font-semibold px-2 py-0.5 rounded-full shrink-0 w-20 text-center"
                          :class="badgeClass(g)" x-text="estadoLabelComponente(g)"></span>

                    <template x-if="g.reclamo_activo">
                        <span class="text-[12px] font-semibold text-amber-600 dark:text-amber-400 shrink-0 whitespace-nowrap">
                            Reclamo en curso
                        </span>
                    </template>
                </button>
            </template>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         HISTORIAL DE RECLAMOS
    ══════════════════════════════════════════════════════ --}}
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Historial de reclamos</p>

        <template x-if="reclamos.length === 0">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 text-center">
                <p class="text-sm text-gray-400">Sin reclamos registrados para esta bicicleta.</p>
            </div>
        </template>

        <div class="space-y-2">
            <template x-for="r in reclamos" :key="r.id_reclamo">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="r.componente"></p>
                            <p class="text-xs text-gray-400 mt-0.5 line-clamp-2" x-text="r.motivo"></p>

                            <template x-if="r.kilometraje !== null && r.kilometraje !== undefined">
                                <p class="text-[12px] text-gray-400 mt-1">
                                    Kilometraje: <span x-text="Number(r.kilometraje).toLocaleString('es-MX')"></span> km
                                </p>
                            </template>

                            {{-- Respuesta de la IA --}}
                            <template x-if="r.ia_sugerencia">
                                <p class="text-xs mt-1.5" :class="iaColorClass(r.ia_sugerencia)">
                                    <span class="font-semibold">Respuesta de la IA:</span>
                                    <span x-text="r.ia_razonamiento"></span>
                                </p>
                            </template>
                            <template x-if="!r.ia_sugerencia && r._analizando">
                                <p class="text-[12px] text-gray-400 mt-1.5 flex items-center gap-1.5">
                                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Analizando con IA...
                                </p>
                            </template>

                            <template x-if="r.resultado">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="r.resultado"></p>
                            </template>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            <span class="text-[12px] font-semibold px-2 py-0.5 rounded-full" :class="estadoColorReclamo(r)"
                                  x-text="estadoLabelReclamo(r)"></span>
                            <template x-if="r.id_reparacion">
                                <span class="text-[12px] text-gray-400 font-mono" x-text="r.id_reparacion"></span>
                            </template>
                            <span class="text-[12px] text-gray-400" x-text="r.created_at"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL: Detalle de componente + abrir reclamo
    ══════════════════════════════════════════════════════ --}}
    <div x-show="detalleModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-end sm:items-center justify-center z-50 px-4 pb-4 sm:pb-0"
         @click.self="detalleModal = false">

        <div x-show="detalleModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>

            {{-- Header del modal con barra de color --}}
            <div class="h-1 w-full rounded-t-xl transition-colors duration-300"
                 :class="{
                     'bg-green-500':  componenteActivo?.color_mapa === 'green',
                     'bg-yellow-500': componenteActivo?.color_mapa === 'yellow',
                     'bg-red-500':    componenteActivo?.color_mapa === 'red',
                 }"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full transition-colors duration-300"
                              :class="{
                                  'bg-green-500':  componenteActivo?.color_mapa === 'green',
                                  'bg-yellow-500': componenteActivo?.color_mapa === 'yellow',
                                  'bg-red-500':    componenteActivo?.color_mapa === 'red',
                              }"></span>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white"
                            x-text="componenteActivo?.nombre"></h3>
                    </div>
                    <button @click="detalleModal = false"
                            class="w-7 h-7 flex items-center justify-center rounded-md text-gray-400
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Barra de vida grande --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">Vida útil restante</span>
                        <span class="font-semibold"
                              :class="{
                                  'text-green-600 dark:text-green-400':  componenteActivo?.color_mapa === 'green',
                                  'text-yellow-600 dark:text-yellow-400': componenteActivo?.color_mapa === 'yellow',
                                  'text-red-600 dark:text-red-400':      componenteActivo?.color_mapa === 'red',
                              }"
                              x-text="(componenteActivo?.porcentaje_vida ?? 0) + '%'">
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-700"
                             :class="{
                                 'bg-green-500':  componenteActivo?.color_mapa === 'green',
                                 'bg-yellow-500': componenteActivo?.color_mapa === 'yellow',
                                 'bg-red-500':    componenteActivo?.color_mapa === 'red',
                             }"
                             :style="'width: ' + (componenteActivo?.porcentaje_vida ?? 0) + '%'">
                        </div>
                    </div>
                </div>

                {{-- Info del componente --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 space-y-2 mb-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Estado</span>
                        <span class="font-medium capitalize"
                              :class="{
                                  'text-green-600 dark:text-green-400':  componenteActivo?.estado_visual === 'vigente',
                                  'text-yellow-600 dark:text-yellow-400': componenteActivo?.estado_visual === 'por_vencer',
                                  'text-red-600 dark:text-red-400': ['expirada','reemplazada','invalidada'].includes(componenteActivo?.estado_visual),
                              }"
                              x-text="componenteActivo?.estado_visual?.replace('_', ' ')">
                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Vence</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.fecha_expiracion"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Días restantes</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"
                              x-text="(componenteActivo?.dias_restantes ?? 0) + ' días'"></span>
                    </div>
                    <div x-show="componenteActivo?.cobertura" class="flex justify-between text-xs">
                        <span class="text-gray-400">Cobertura</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300 text-right max-w-[60%]"
                              x-text="componenteActivo?.cobertura"></span>
                    </div>
                    <div x-show="componenteActivo?.num_serie_comp" class="flex justify-between text-xs">
                        <span class="text-gray-400">N° serie comp.</span>
                        <span class="font-mono font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.num_serie_comp"></span>
                    </div>
                </div>

                {{-- Piezas incluidas --}}
                <div x-show="componenteActivo?.incluye?.length" class="mb-4">
                    <p class="text-xs text-gray-400 mb-1.5">Incluye:</p>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="pieza in (componenteActivo?.incluye ?? [])">
                            <span class="text-[12px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700
                                         text-gray-600 dark:text-gray-300 capitalize"
                                  x-text="pieza"></span>
                        </template>
                    </div>
                </div>

                {{-- Ya existe un reclamo activo para este componente --}}
                <div x-show="componenteActivo?.reclamo_activo"
                     class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <p class="text-xs text-center text-amber-600 dark:text-amber-400 font-medium">
                        Ya existe un reclamo activo para este componente. No se puede abrir otro hasta que se resuelva.
                    </p>
                </div>

                {{-- Formulario de reclamo --}}
                <div x-show="!componenteActivo?.reclamo_activo && (componenteActivo?.estado_visual === 'vigente' || componenteActivo?.estado_visual === 'por_vencer')">
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Abrir reclamo de garantía
                        </p>
                        <textarea
                            x-model="motivoReclamo"
                            placeholder="Describe el problema del componente..."
                            rows="3"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                   focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none">
                        </textarea>

                        <div class="mt-2">
                            <label class="block text-[12px] text-gray-400 mb-1">
                                Kilometraje actual <span class="font-normal">(opcional, revisa el odómetro)</span>
                            </label>
                            <input type="number" x-model="kilometraje" min="0" max="999999" placeholder="Ej. 850"
                                   class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-mono
                                          focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <p class="text-[12px] text-gray-400 mt-1">
                                Ayuda al admin a decidir: una unidad con mucho uso en poco tiempo es una señal a considerar.
                            </p>
                        </div>

                        <button
                            @click="abrirReclamo()"
                            :disabled="enviandoReclamo || !motivoReclamo.trim()"
                            class="mt-2 w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition
                                   disabled:opacity-40 disabled:cursor-not-allowed active:scale-95
                                   flex items-center justify-center gap-2">
                            <svg x-show="enviandoReclamo" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="enviandoReclamo ? 'Procesando el reclamo...' : 'Registrar reclamo'"></span>
                        </button>
                    </div>
                </div>

                {{-- Garantía no vigente --}}
                <div x-show="!componenteActivo?.reclamo_activo && !['vigente','por_vencer'].includes(componenteActivo?.estado_visual)"
                     class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <p class="text-xs text-center text-gray-400">
                        Este componente no tiene garantía activa. No se puede abrir un reclamo.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function garantiaShow() {
        return {
            componentes:      @json($garantias->values()),
            reclamos:         @json($reclamos->values()),

            detalleModal:     false,
            componenteActivo: null,
            motivoReclamo:    '',
            kilometraje:      '',
            enviandoReclamo:  false,
            flashVisible:     false,
            flashMsg:         '',
            flashTipo:        'success',
            flashTimer:       null,

            init() {
                // Reanuda el sondeo de IA para reclamos que quedaron sin respuesta
                // (por ejemplo si el vendedor recargó la página mientras esperaba).
                this.reclamos
                    .filter(r => !r.ia_sugerencia && r.ot_estado === 'en_revision')
                    .forEach(r => this.pollIA(r.id_reclamo));
            },

            flash(msg, tipo = 'success') {
                this.flashMsg     = msg;
                this.flashTipo    = tipo;
                this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false,
                    tipo === 'error' ? 4000 : 3000);
            },

            dotClass(g) {
                return { green: 'bg-green-500', yellow: 'bg-yellow-500' }[g.color_mapa] ?? 'bg-red-500';
            },
            badgeClass(g) {
                return {
                    green:  'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                    yellow: 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
                }[g.color_mapa] ?? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300';
            },
            barClass(g) {
                return { green: 'bg-green-500', yellow: 'bg-yellow-500' }[g.color_mapa] ?? 'bg-red-500';
            },
            estadoLabelComponente(g) {
                return { green: 'Vigente', yellow: 'Por vencer' }[g.color_mapa] ?? 'Expirada';
            },
            iaColorClass(sugerencia) {
                return {
                    cubre:    'text-green-600 dark:text-green-400',
                    no_cubre: 'text-red-500 dark:text-red-400',
                    revisar:  'text-amber-600 dark:text-amber-400',
                }[sugerencia] ?? 'text-gray-500 dark:text-gray-400';
            },
            capitalize(s) {
                return s ? s.charAt(0).toUpperCase() + s.slice(1) : s;
            },
            estadoLabelReclamo(r) {
                if (r.ot_estado === 'en_revision') return 'En revisión';
                if (r.ot_estado === 'cancelada' && r.reclamo_estado === 'rechazado') return 'Rechazado';
                if (!r.ot_estado) return this.capitalize((r.reclamo_estado || '').replace('_', ' '));
                return 'OT: ' + this.capitalize(r.ot_estado.replace('_', ' '));
            },
            estadoColorReclamo(r) {
                if (r.ot_estado === 'en_revision') return 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300';
                if (r.ot_estado === 'entregada')   return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
                if (r.ot_estado === 'cancelada')   return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
                return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300';
            },

            abrirDetalle(componente) {
                this.componenteActivo = componente;
                this.motivoReclamo    = '';
                this.kilometraje      = '';
                this.detalleModal     = true;
            },

            async abrirReclamo() {
                if (!this.motivoReclamo.trim() || !this.componenteActivo) return;

                this.enviandoReclamo = true;
                try {
                    const res = await fetch('{{ route('garantias.reclamo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify({
                            num_serie:             '{{ $bici->num_serie }}',
                            id_bicicleta_garantia: this.componenteActivo.id,
                            motivo_reclamo:        this.motivoReclamo.trim(),
                            kilometraje:           this.kilometraje !== '' ? parseInt(this.kilometraje) : null,
                        }),
                    });
                    const data = await res.json();

                    if (!data.ok) { this.flash(data.mensaje, 'error'); return; }

                    // Marca el componente como con reclamo activo, sin recargar.
                    const comp = this.componentes.find(c => c.id === this.componenteActivo.id);
                    if (comp) comp.reclamo_activo = true;

                    // Inserta el reclamo nuevo al historial y arranca el sondeo de IA.
                    const nuevo = { ...data.reclamo, _analizando: true };
                    this.reclamos.unshift(nuevo);
                    this.pollIA(nuevo.id_reclamo);

                    this.detalleModal = false;
                    this.flash(data.mensaje);

                } catch {
                    this.flash('Error de conexión.', 'error');
                } finally {
                    this.enviandoReclamo = false;
                }
            },

            // Sondea /reclamo/{id}/ia cada 2.5s (máx. ~12 intentos) hasta que la
            // opinión de IA esté lista, y actualiza la tarjeta en vivo.
            pollIA(idReclamo, intento = 0) {
                if (intento >= 12) {
                    const r = this.reclamos.find(x => x.id_reclamo === idReclamo);
                    if (r) r._analizando = false;
                    return;
                }

                setTimeout(async () => {
                    try {
                        const res = await fetch(`/sucursal/garantias/reclamo/${idReclamo}/ia`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        const data = await res.json();

                        const r = this.reclamos.find(x => x.id_reclamo === idReclamo);
                        if (!r) return;

                        if (data.ok && data.listo) {
                            r.ia_sugerencia   = data.ia_sugerencia;
                            r.ia_razonamiento = data.ia_razonamiento;
                            r._analizando     = false;
                            return;
                        }

                        this.pollIA(idReclamo, intento + 1);
                    } catch {
                        this.pollIA(idReclamo, intento + 1);
                    }
                }, 2500);
            },
        }
    }
    </script>
</div>
</x-app-layout>
