{{-- resources/views/vendedor/garantias/show.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-6" x-data="garantiaShow()">

    {{-- Flash --}}
    <x-flash-messages />


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
                <span class="text-[10px] text-gray-500 dark:text-gray-400">Vigente</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                <span class="text-[10px] text-gray-500 dark:text-gray-400">Por vencer</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-[10px] text-gray-500 dark:text-gray-400">Expirada</span>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════
         MAPA VISUAL INTERACTIVO
    ══════════════════════════════════════════════════════ --}}
    @if($garantias->isEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-10 text-center">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Esta bicicleta no tiene garantías configuradas para su marca.</p>
        </div>
    @else

    {{-- Pasar datos de garantías a JS --}}
    @php
        $garantiasJs = $garantias->keyBy('clave');
    @endphp

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

        {{-- Layout: imagen izquierda + lista derecha --}}
        <div class="flex flex-col lg:flex-row">

            {{-- ── Panel izquierdo: imagen con puntos ── --}}
            <div class="relative lg:w-3/5 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-center p-6 min-h-[320px]">

                {{-- Imagen placeholder de bici eléctrica (SVG inline) --}}
                <div class="relative w-full max-w-[520px]" id="mapa-bici">

                    {{-- Imagen real del scooter eléctrico --}}
                    <img
                        src="{{ asset('storage/evobike.png') }}"
                        alt="Moto eléctrica"
                        class="w-full h-auto select-none pointer-events-none"
                        style="filter: drop-shadow(0 4px 24px rgba(0,0,0,0.12));"
                        draggable="false"
                    />

                    {{-- ── PUNTOS INTERACTIVOS ──
                         Posiciones en % relativas al SVG (viewBox 520x280)
                         top = y/280*100, left = x/520*100
                    --}}
                    @php
                    // Mapa de posiciones por clave de componente
                    // [top%, left%] — relativos al contenedor de la imagen del scooter
                    $posicionesDefault = [
                        // Tracción
                        'motor'            => [78, 20],   // hub rueda trasera
                        'bateria'          => [65, 45],   // cuerpo central
                        'bateria_litio'    => [65, 45],
                        'bateria_plomo'    => [65, 45],
                        'controlador'      => [75, 50],   // bajo el cuerpo
                        'cargador'         => [68, 60],   // lateral derecho

                        // Estructura
                        'marco'            => [60, 45],
                        'horquilla'        => [50, 70],   // horquilla delantera
                        'suspension'       => [50, 70],
                        'amortiguador'     => [50, 70],

                        // Controles
                        'manubrio'         => [30, 68],
                        'mango'            => [30, 68],
                        'freno'            => [30, 68],
                        'sistema_electrico'=> [35, 72],
                        'tablero'          => [25, 62],

                        // Iluminación
                        'faro'             => [65, 85],
                        'luces'            => [65, 85],

                        // Ruedas
                        'rueda'            => [78, 80],
                        'llanta'           => [78, 80],
                        'neumatico'        => [78, 80],

                        // Pedales
                        'pedales'          => [80, 50],
                    ];

                    // Para cada garantía, encontrar su posición
                    // Si la clave no está en el mapa, distribuir en fila superior
                    $usadas = [];
                    $fallbackPositions = [
                        [8, 20],[8, 35],[8, 50],[8, 65],[8, 80],
                        [92, 20],[92, 35],[92, 50],[92, 65],[92, 80],
                    ];
                    $fallbackIdx = 0;
                    @endphp

                    @foreach($garantias as $g)
                    @php
                        $clave = $g['clave'];
                        // Buscar posición: clave exacta → substring match → fallback
                        $pos = null;
                        if (isset($posicionesDefault[$clave])) {
                            $pos = $posicionesDefault[$clave];
                        } else {
                            foreach ($posicionesDefault as $k => $p) {
                                if (str_contains($clave, $k) || str_contains($k, $clave)) {
                                    $pos = $p;
                                    break;
                                }
                            }
                        }
                        if (!$pos) {
                            $pos = $fallbackPositions[$fallbackIdx % count($fallbackPositions)];
                            $fallbackIdx++;
                        }

                        $dotColor = match($g['color_mapa']) {
                            'green'  => 'bg-green-500 ring-green-200 dark:ring-green-900 shadow-green-500/40',
                            'yellow' => 'bg-yellow-500 ring-yellow-200 dark:ring-yellow-900 shadow-yellow-500/40',
                            default  => 'bg-red-500 ring-red-200 dark:ring-red-900 shadow-red-500/40',
                        };
                        $pulseColor = match($g['color_mapa']) {
                            'green'  => 'bg-green-400',
                            'yellow' => 'bg-yellow-400',
                            default  => 'bg-red-400',
                        };
                    @endphp

                    <button
                        type="button"
                        @click="abrirDetalle({{ json_encode($g) }})"
                        style="top: {{ $pos[0] }}%; left: {{ $pos[1] }}%; transform: translate(-50%, -50%);"
                        class="absolute z-10 group focus:outline-none"
                        title="{{ $g['nombre'] }}">

                        {{-- Anillo de pulso animado --}}
                        <span class="absolute inset-0 rounded-full {{ $pulseColor }} opacity-30 animate-ping"></span>

                        {{-- Punto principal --}}
                        <span class="relative flex items-center justify-center w-5 h-5 rounded-full
                                     {{ $dotColor }} ring-2 shadow-md
                                     transition-all duration-200 group-hover:scale-125 group-hover:ring-4">
                        </span>

                        {{-- Tooltip --}}
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1
                                     bg-gray-900 dark:bg-gray-700 text-white text-[10px] font-medium
                                     rounded-md whitespace-nowrap opacity-0 pointer-events-none
                                     group-hover:opacity-100 transition-opacity duration-150 z-20
                                     shadow-lg">
                            {{ $g['nombre'] }}
                            <span class="block text-[9px] font-normal opacity-70 text-center">
                                @if($g['dias_restantes'] > 0)
                                    {{ $g['dias_restantes'] }}d restantes
                                @else
                                    Expirada
                                @endif
                            </span>
                        </span>
                    </button>
                    @endforeach

                </div>{{-- /mapa-bici --}}
            </div>

            {{-- ── Panel derecho: lista de componentes ── --}}
            <div class="lg:w-2/5 border-t lg:border-t-0 lg:border-l border-gray-100 dark:border-gray-700">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Componentes ({{ $garantias->count() }})
                    </p>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700 overflow-y-auto max-h-[400px]">
                    @foreach($garantias as $g)
                    @php
                        $barColor = match($g['color_mapa']) {
                            'green'  => 'bg-green-500',
                            'yellow' => 'bg-yellow-500',
                            default  => 'bg-red-500',
                        };
                        $textColor = match($g['color_mapa']) {
                            'green'  => 'text-green-600 dark:text-green-400',
                            'yellow' => 'text-yellow-600 dark:text-yellow-400',
                            default  => 'text-red-600 dark:text-red-400',
                        };
                        $badgeBg = match($g['color_mapa']) {
                            'green'  => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                            'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
                            default  => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                        };
                    @endphp
                    <button type="button"
                            @click="abrirDetalle({{ json_encode($g) }})"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50
                                   transition-colors duration-150 group">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                {{ $g['nombre'] }}
                            </span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full shrink-0 {{ $badgeBg }}">
                                @if($g['dias_restantes'] > 0)
                                    {{ $g['dias_restantes'] }}d
                                @else
                                    Exp.
                                @endif
                            </span>
                        </div>

                        {{-- Barra de vida --}}
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1">
                            <div class="h-1 rounded-full transition-all duration-500 {{ $barColor }}"
                                 style="width: {{ $g['porcentaje_vida'] }}%"></div>
                        </div>

                        <p class="text-[10px] text-gray-400 mt-1">
                            Hasta {{ $g['fecha_expiracion'] }}
                        </p>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         HISTORIAL DE RECLAMOS
    ══════════════════════════════════════════════════════ --}}
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
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700
                                         text-gray-600 dark:text-gray-300 capitalize"
                                  x-text="pieza"></span>
                        </template>
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
                                   px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition
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