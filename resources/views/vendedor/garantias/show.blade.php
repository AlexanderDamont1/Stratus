{{-- resources/views/vendedor/garantias/show.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-6" x-data="garantiaShow()">

    {{-- Flash --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl min-w-[300px]"
             :class="flashTipo === 'error' ? 'ring-1 ring-red-200' : 'ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-5 w-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

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
    </div>

    {{-- Mapa de componentes --}}
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Estado de componentes</p>

        @if($garantias->isEmpty())
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Esta bicicleta no tiene garantías configuradas para su marca.
                </p>
            </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($garantias as $g)
            @php
                $colorClasses = match($g['color_mapa']) {
                    'green'  => 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/10',
                    'yellow' => 'border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/10',
                    default  => 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10',
                };
                $dotColor = match($g['color_mapa']) {
                    'green'  => 'bg-green-500',
                    'yellow' => 'bg-yellow-500',
                    default  => 'bg-red-500',
                };
                $textColor = match($g['color_mapa']) {
                    'green'  => 'text-green-700 dark:text-green-400',
                    'yellow' => 'text-yellow-700 dark:text-yellow-400',
                    default  => 'text-red-700 dark:text-red-400',
                };
            @endphp

            <div class="border {{ $colorClasses }} rounded-xl p-4 cursor-pointer hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                 @click="abrirDetalle({{ json_encode($g) }})">

                {{-- Indicador + nombre --}}
                <div class="flex items-start gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full {{ $dotColor }} mt-1.5 shrink-0"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $g['nombre'] }}
                    </span>
                </div>

                {{-- Barra de vida --}}
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-2">
                    <div class="h-1.5 rounded-full transition-all duration-500
                        {{ $g['color_mapa'] === 'green' ? 'bg-green-500' : ($g['color_mapa'] === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ $g['porcentaje_vida'] }}%">
                    </div>
                </div>

                <p class="text-[11px] {{ $textColor }}">
                    @if($g['dias_restantes'] > 0)
                        {{ $g['dias_restantes'] }} días restantes
                    @else
                        Expirada
                    @endif
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">Hasta {{ $g['fecha_expiracion'] }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Historial de reclamos --}}
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Historial de reclamos</p>

        @if($reclamos->isEmpty())
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 text-center">
                <p class="text-sm text-gray-400">Sin reclamos registrados para esta bicicleta.</p>
            </div>
        @else
        <div class="space-y-2">
            @foreach($reclamos as $r)
            @php
                $estadoColor = match($r->estado) {
                    'pendiente'      => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                    'en_diagnostico' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                    'aprobado'       => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                    'en_reparacion'  => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                    'finalizado'     => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
                    'rechazado'      => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                    default          => 'bg-gray-100 dark:bg-gray-700 text-gray-500',
                };
            @endphp
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $r->bicicletaGarantia->garantiaDef->nombre_componente ?? $r->clave_componente }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $r->motivo_reclamo }}</p>
                        @if($r->resultado)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">{{ $r->resultado }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $estadoColor }}">
                            {{ ucfirst(str_replace('_', ' ', $r->estado)) }}
                        </span>
                        <span class="text-[10px] text-gray-400">
                            {{ $r->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── MODAL: Detalle de componente + abrir reclamo ── --}}
    <div x-show="detalleModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-end sm:items-center justify-center z-50 px-4 pb-4 sm:pb-0"
         @click.self="detalleModal = false">

        <div x-show="detalleModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>

            {{-- Header del modal --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full"
                          :class="{
                              'bg-green-500': componenteActivo?.color_mapa === 'green',
                              'bg-yellow-500': componenteActivo?.color_mapa === 'yellow',
                              'bg-red-500': componenteActivo?.color_mapa === 'red',
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

            {{-- Info del componente --}}
            <div class="space-y-3 mb-5">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Estado</span>
                        <span class="font-medium"
                              :class="{
                                  'text-green-600 dark:text-green-400': componenteActivo?.estado_visual === 'vigente',
                                  'text-yellow-600 dark:text-yellow-400': componenteActivo?.estado_visual === 'por_vencer',
                                  'text-red-600 dark:text-red-400': ['expirada','reemplazada','invalidada'].includes(componenteActivo?.estado_visual),
                              }"
                              x-text="componenteActivo?.estado_visual?.replace('_', ' ')">
                        </span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Expira</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.fecha_expiracion"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Días restantes</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.dias_restantes + ' días'"></span>
                    </div>
                    <div x-show="componenteActivo?.cobertura" class="flex justify-between text-xs">
                        <span class="text-gray-400">Cobertura</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.cobertura"></span>
                    </div>
                    <div x-show="componenteActivo?.num_serie_comp" class="flex justify-between text-xs">
                        <span class="text-gray-400">N° serie comp.</span>
                        <span class="font-mono font-medium text-gray-700 dark:text-gray-300"
                              x-text="componenteActivo?.num_serie_comp"></span>
                    </div>
                </div>

                {{-- Piezas incluidas --}}
                <div x-show="componenteActivo?.incluye?.length">
                    <p class="text-xs text-gray-400 mb-1.5">Incluye:</p>
                    <div class="flex flex-wrap gap-1">
                        <template x-for="pieza in (componenteActivo?.incluye ?? [])">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700
                                         text-gray-600 dark:text-gray-300 capitalize"
                                  x-text="pieza"></span>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Formulario de reclamo --}}
            <div x-show="componenteActivo?.estado_visual === 'vigente' || componenteActivo?.estado_visual === 'por_vencer'">
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

                    <button
                        @click="abrirReclamo()"
                        :disabled="enviandoReclamo || !motivoReclamo.trim()"
                        class="mt-2 w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                               px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition
                               disabled:opacity-40 disabled:cursor-not-allowed active:scale-95
                               flex items-center justify-center gap-2">
                        <svg x-show="enviandoReclamo" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="enviandoReclamo ? 'Registrando...' : 'Registrar reclamo'"></span>
                    </button>
                </div>
            </div>

            {{-- Garantía no vigente --}}
            <div x-show="!['vigente','por_vencer'].includes(componenteActivo?.estado_visual)"
                 class="border-t border-gray-100 dark:border-gray-700 pt-4">
                <p class="text-xs text-center text-gray-400">
                    Este componente no tiene garantía activa. No se puede abrir un reclamo.
                </p>
            </div>
        </div>
    </div>

    <script>
    function garantiaShow() {
        return {
            detalleModal:     false,
            componenteActivo: null,
            motivoReclamo:    '',
            enviandoReclamo:  false,
            flashVisible:     false,
            flashMsg:         '',
            flashTipo:        'success',
            flashTimer:       null,

            flash(msg, tipo = 'success') {
                this.flashMsg     = msg;
                this.flashTipo    = tipo;
                this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false,
                    tipo === 'error' ? 4000 : 3000);
            },

            abrirDetalle(componente) {
                this.componenteActivo = componente;
                this.motivoReclamo    = '';
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
                        }),
                    });
                    const data = await res.json();

                    if (!data.ok) { this.flash(data.mensaje, 'error'); return; }

                    this.detalleModal = false;
                    this.flash(data.mensaje);
                    setTimeout(() => window.location.reload(), 1200);

                } catch {
                    this.flash('Error de conexión.', 'error');
                } finally {
                    this.enviandoReclamo = false;
                }
            },
        }
    }
    </script>
</div>
</x-app-layout>