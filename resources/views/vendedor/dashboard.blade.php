<x-app-layout>

<script id="catalogo-data" type="application/json">{!! $catalogoJson !!}</script>

<div x-data="vendedorDashboard()" x-init="init()" class="space-y-6">

    

    {{-- ═══════ ENCABEZADO ═══════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Mi inventario</h2>
            <p class="text-xs text-gray-400 mt-0.5">Bicicletas asignadas a tu sucursal · {{ $bicicletas->total() }} total</p>
        </div>

        <div class="flex items-center gap-2">
            

            {{-- Botón primario: abre modal sin cámara --}}
            <button
                data-onboarding="vendedor-ingresar"
                @click="abrirModal()"
                class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white text-white
                       dark:text-gray-900 px-4 py-2 rounded-xl text-sm font-semibold
                       hover:opacity-90 active:scale-95 transition-all duration-150 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Ingresar
            </button>
        </div>
    </div>

    <x-flash-messages />

    {{-- ═══════ TABLA ═══════ --}}
    <div data-onboarding="vendedor-tabla" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800
                overflow-hidden shadow-sm">

        @php
        function parsearColorBlade($c) {
            if (!$c) return ['nombre' => '—', 'hexes' => ['#cccccc']];
            $p = explode('|', $c, 2);
            $hexes = isset($p[1]) ? explode('/', $p[1]) : ['#cccccc'];
            return ['nombre' => trim($p[0] ?? $c), 'hexes' => $hexes];
        }
        $estados = [
            '1' => ['texto' => 'En Stock',   'color' => 'green'],
            '2' => ['texto' => 'Vendido',     'color' => 'purple'],
            '3' => ['texto' => 'Reparación',  'color' => 'yellow'],
        ];
        @endphp

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        @foreach(['N° Serie','Modelo','Voltaje','Color','Estado','Fecha'] as $col)
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-400 uppercase
                                   tracking-wider {{ $col==='Estado' ? 'text-center' : '' }}">
                            {{ $col }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/80">
                    @forelse($bicicletas as $bici)
                        @php
                            $estado    = $estados[$bici->status] ?? ['texto' => $bici->status, 'color' => 'red'];
                            $colorInfo = parsearColorBlade($bici->color->color ?? '');
                            $c         = $estado['color'];
                        @endphp
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/30 transition-colors duration-100">
                            <td class="px-5 py-3.5  text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $bici->num_serie }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                {{ $bici->modelo->nombre_modelo ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                {{ $bici->voltaje->voltaje ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    @if(count($colorInfo['hexes']) >= 2)
                                        <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10
                                                     overflow-hidden relative inline-flex shrink-0">
                                            <span class="absolute left-0 top-0 w-1/2 h-full"
                                                  style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                            <span class="absolute right-0 top-0 w-1/2 h-full"
                                                  style="background:{{ $colorInfo['hexes'][1] }}"></span>
                                        </span>
                                    @else
                                        <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10
                                                     shrink-0 inline-block"
                                              style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                    @endif
                                    <span class="text-gray-700 dark:text-gray-300">{{ $colorInfo['nombre'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full
                                    @if($c==='green')  bg-green-100  text-green-800  dark:bg-green-900/30  dark:text-green-400
                                    @elseif($c==='yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($c==='purple') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                    {{ $estado['texto'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs tabular-nums">
                                {{ $bici->updated_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center
                                            justify-center mx-auto mb-3">
                                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-400">No hay bicicletas asignadas aún</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Móvil --}}
        <div class="block md:hidden divide-y divide-gray-50 dark:divide-gray-800">
            @forelse($bicicletas as $bici)
                @php
                    $estado    = $estados[$bici->status] ?? ['texto' => $bici->status, 'color' => 'red'];
                    $colorInfo = parsearColorBlade($bici->color->color ?? '');
                    $c         = $estado['color'];
                @endphp
                <div class="px-4 py-3.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $bici->num_serie }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $bici->modelo->nombre_modelo ?? '—' }}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            @if(count($colorInfo['hexes']) >= 2)
                                <span class="w-3 h-3 rounded-sm border border-black/10 overflow-hidden
                                             relative inline-flex shrink-0">
                                    <span class="absolute left-0 top-0 w-1/2 h-full"
                                          style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                    <span class="absolute right-0 top-0 w-1/2 h-full"
                                          style="background:{{ $colorInfo['hexes'][1] }}"></span>
                                </span>
                            @else
                                <span class="w-3 h-3 rounded-sm border border-black/10 shrink-0 inline-block"
                                      style="background:{{ $colorInfo['hexes'][0] }}"></span>
                            @endif
                            <span class="text-[11px] text-gray-500">{{ $colorInfo['nombre'] }}</span>
                            <span class="text-[11px] text-gray-300 dark:text-gray-600">·</span>
                            <span class="text-[11px] text-gray-500">{{ $bici->voltaje->voltaje ?? '—' }}</span>
                        </div>
                    </div>
                    <span class="shrink-0 px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap
                        @if($c==='green')  bg-green-100  text-green-800  dark:bg-green-900/30  dark:text-green-400
                        @elseif($c==='yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                        @elseif($c==='purple') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                        {{ $estado['texto'] }}
                    </span>
                </div>
            @empty
                <div class="px-4 py-12 text-center text-sm text-gray-400">Sin bicicletas asignadas</div>
            @endforelse
        </div>

        @if($bicicletas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $bicicletas->withQueryString()->links() }}
            </div>
        @endif
    </div>


    {{-- ═══════════════════════════════════════════════════
     |  MODAL — Ingresar bicicletas (carga masiva por QR)
     ═══════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="modal"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            @click.self="cerrarModal()">

            <div
                x-show="modal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-900 w-full max-w-2xl rounded-2xl shadow-2xl flex flex-col"
                style="max-height: min(90vh, 640px);"
                @click.stop>

                {{-- ── Cabecera ── --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100
                            dark:border-gray-800 shrink-0">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Ingresar bicicletas</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Escanea o escribe series — se acumulan hasta guardar</p>
                    </div>
                    <button @click="cerrarModal()"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400
                            hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100
                            dark:hover:bg-gray-800 transition ml-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- ── Cuerpo: 2 columnas ── --}}
                <div class="flex-1 min-h-0 grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x
                            divide-gray-100 dark:divide-gray-800 overflow-hidden">

                    {{-- ── Col izq: escáner + input ── --}}
                    <div class="overflow-y-auto">
                        <div class="p-4 space-y-3">

                            {{-- Cámara activa --}}
                            <div x-show="scannerActivo"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">
                                <div id="qr-reader"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                        bg-gray-50 dark:bg-gray-800 overflow-hidden min-h-[180px]"></div>
                                <div class="flex items-center justify-between mt-2 gap-2">
                                    <p class="text-xs text-gray-400">Apunta al QR — se agrega automáticamente</p>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <button x-show="scanCamaras.length > 1" @click="cambiarCamara()"
                                            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition font-medium
                                                flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Cambiar cámara
                                        </button>
                                        <button @click="detenerCamara()"
                                            class="text-xs text-gray-400 hover:text-red-500 transition font-medium
                                                flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Cerrar cámara
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Placeholder cámara apagada --}}
                            <div x-show="!scannerActivo"
                                class="w-full rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700
                                    bg-gray-50 dark:bg-gray-800/50 flex flex-col items-center justify-center
                                    gap-2.5 py-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800
                                    transition group"
                                @click="activarCamara()">
                                <div class="w-9 h-9 rounded-xl bg-white dark:bg-gray-700 border border-gray-200
                                            dark:border-gray-600 flex items-center justify-center shadow-sm
                                            group-hover:scale-105 transition">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07
                                            4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012
                                            2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Activar cámara QR</p>
                                    <p class="text-[11px] text-gray-300 dark:text-gray-600 mt-0.5">Toca para encender</p>
                                </div>
                            </div>

                            {{-- Input manual --}}
                            <div>
                                <label class="block text-[11px] font-medium text-gray-400  tracking-wide mb-1.5">
                                    O escribe el número de serie
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="input"
                                        x-ref="inputSerie"
                                        @input="input = input.toUpperCase(); errorGlobal = ''"
                                        @keyup.enter="agregarSerie(input)"
                                        maxlength="17"
                                        placeholder="HE0EA2A00SA963753"
                                        autocomplete="off"
                                        class="flex-1 border border-gray-200 dark:border-gray-700 rounded-xl
                                                px-3 py-2 text-[12px] font-medium tracking-widest
                                                bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                placeholder-gray-300 dark:placeholder-gray-600
                                                focus:outline-none  focus:ring-gray-200
                                                dark:focus:ring-gray-700 transition">
                                    <button
                                        @click="agregarSerie(input)"
                                        :disabled="input.length !== 17 || buscando"
                                        class="px-3 py-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                            text-xs font-semibold rounded-xl hover:opacity-90 transition
                                            disabled:opacity-30 disabled:cursor-not-allowed
                                            flex items-center justify-center gap-1.5 min-w-[64px]">
                                        <svg x-show="buscando" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        <span x-text="buscando ? '' : 'Agregar'"></span>
                                    </button>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <p class="text-[11px] text-gray-400">Exactamente 17 caracteres</p>
                                    <p class="text-[11px] text-gray-400 tabular-nums">
                                        <span x-text="input.length"></span>/17
                                    </p>
                                </div>
                            </div>

                            {{-- Error --}}
                            <div x-show="errorGlobal"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-end="opacity-0"
                                class="flex items-start gap-2 px-3 py-2.5 rounded-xl bg-red-50
                                    dark:bg-red-900/10 border border-red-200 dark:border-red-800/50">
                                <svg class="w-3.5 h-3.5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012
                                        0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-xs text-red-600 dark:text-red-400" x-text="errorGlobal"></p>
                            </div>

                            {{-- Éxito --}}
                            <div x-show="exitoMsg"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-end="opacity-0"
                                class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-50
                                    dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/50">
                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400" x-text="exitoMsg"></p>
                            </div>

                        </div>
                    </div>

                    {{-- ── Col der: lista acumulada ── --}}
                    <div class="flex flex-col min-h-0">

                        {{-- Header lista --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 shrink-0
                                    flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Cola de ingreso</span>
                                <span x-show="lista.length > 0"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-50"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="text-[10px] font-black bg-gray-900 dark:bg-white text-white
                                        dark:text-gray-900 px-1.5 py-0.5 rounded-full tabular-nums"
                                    x-text="lista.length">
                                </span>
                            </div>
                            <button x-show="lista.length > 0" @click="lista = []"
                                class="text-[11px] text-gray-400 hover:text-red-500 dark:hover:text-red-400
                                    transition font-medium">
                                Limpiar todo
                            </button>
                        </div>

                        {{-- Estado vacío --}}
                        <div x-show="lista.length === 0"
                            class="flex-1 flex flex-col items-center justify-center px-6 py-8 text-center">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center
                                        justify-center mb-2.5">
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9
                                        5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin bicicletas en cola</p>
                            <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Escanea o escribe series para acumularlas aquí</p>
                        </div>

                        {{-- Lista con scroll fijo --}}
                        <div x-show="lista.length > 0"
                            class="overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800/80"
                            style="max-height: 260px;">
                            <template x-for="(item, idx) in lista" :key="item.num_serie">
                                <div
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-x-2"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    class="px-4 py-2.5 flex items-center gap-3 group">

                                    <span class="text-[10px] text-gray-300 dark:text-gray-600 w-4 text-right
                                                shrink-0 tabular-nums" x-text="idx + 1"></span>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold tracking-widest text-gray-900 dark:text-white truncate"
                                        x-text="item.num_serie"></p>
                                        <div class="flex flex-wrap items-center gap-1 mt-0.5">
                                            <span class="text-[10px] bg-blue-50 dark:bg-blue-900/20 text-blue-600
                                                        dark:text-blue-400 border border-blue-100 dark:border-blue-800/50
                                                        px-1.5 py-0.5 rounded font-medium"
                                                x-text="item.marca"></span>
                                            <span class="text-[10px] bg-gray-50 dark:bg-gray-800 text-gray-500
                                                        dark:text-gray-400 border border-gray-100 dark:border-gray-700
                                                        px-1.5 py-0.5 rounded"
                                                x-text="item.modelo"></span>
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-gray-50
                                                        dark:bg-gray-800 text-gray-500 dark:text-gray-400 border
                                                        border-gray-100 dark:border-gray-700 px-1.5 py-0.5 rounded">
                                                <span class="w-2.5 h-2.5 rounded-sm border border-black/10
                                                            dark:border-white/10 shrink-0 inline-block"
                                                    :style="'background:' + item.colorHex"></span>
                                                <span x-text="item.colorNombre"></span>
                                            </span>
                                            <span class="text-[10px] bg-gray-50 dark:bg-gray-800 text-gray-400
                                                        dark:text-gray-500 border border-gray-100 dark:border-gray-700
                                                        px-1.5 py-0.5 rounded"
                                                x-text="item.voltaje"></span>
                                        </div>
                                    </div>

                                    <button type="button" @click="lista.splice(idx, 1)"
                                        class="w-6 h-6 rounded-lg flex items-center justify-center text-gray-300
                                            dark:text-gray-600 hover:text-red-500 hover:bg-red-50
                                            dark:hover:bg-red-900/20 transition shrink-0
                                            opacity-0 group-hover:opacity-100 focus:opacity-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Footer guardar --}}
                        <div class="px-4 py-3.5 border-t border-gray-100 dark:border-gray-800 shrink-0 space-y-2.5 mt-auto">
                            <div x-show="lista.length > 0"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-emerald-700 dark:text-emerald-400">
                                    <strong x-text="lista.length"></strong>
                                    <span x-text="lista.length === 1 ? ' bicicleta lista' : ' bicicletas listas'"></span>
                                    para ingresar al stock
                                </p>
                            </div>

                            <form method="POST"
                                action="{{ route('sucursal.bicicletas.storeMasivo') }}"
                                @submit.prevent="enviar($el)">
                                @csrf
                                <template x-for="(item, idx) in lista" :key="item.num_serie">
                                    <span>
                                        <input type="hidden" :name="`bicicletas[${idx}][num_serie]`" :value="item.num_serie">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="item.id_modelo">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="item.id_color">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="item.id_voltaje">
                                    </span>
                                </template>

                                <button type="submit"
                                    :disabled="enviando || lista.length === 0"
                                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                        py-2.5 rounded-xl text-sm font-semibold hover:opacity-90
                                        active:scale-[.99] transition-all disabled:opacity-30
                                        disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <template x-if="enviando">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    </template>
                                    <template x-if="!enviando">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <span x-text="enviando
                                        ? 'Guardando…'
                                        : lista.length === 0
                                            ? 'Agrega bicicletas primero'
                                            : `Guardar ${lista.length} bicicleta${lista.length !== 1 ? 's' : ''}`">
                                    </span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </template>

</div>

<script src="https://unpkg.com/html5-qrcode" defer></script>

<script>
function vendedorDashboard() {
    return {
        /* ─── estado ─────────────────────── */
        catalogo:    [],
        modal:       false,
        scannerActivo: false,   /* ← NUEVO: la cámara comienza apagada */
        input:       '',
        buscando:    false,
        errorGlobal: '',
        exitoMsg:    '',
        lista:       [],
        enviando:        false,
        _scanner:        null,
        _scannerIniciado: false,
        _scannerPausado: false,
        scanCamaras:     [],   /* ← NUEVO: cámaras traseras disponibles, para evitar el gran angular */
        scanCamaraIdx:   0,

        /* ─── init ──────────────────────── */
        init() {
            const tag     = document.getElementById('catalogo-data');
            this.catalogo = tag ? JSON.parse(tag.text) : [];
        },

        /* ─── modal: abre SIN cámara (botón "Ingresar") ── */
        abrirModal() {
            this.modal        = true;
            this.scannerActivo = false;
            this.input        = '';
            this.errorGlobal  = '';
            this.exitoMsg     = '';
            this.$nextTick(() => {
                this.modal = true;
                if (this.$refs.inputSerie) this.$refs.inputSerie.focus();
            });
        },

        /* ─── modal: abre CON cámara (botón "Escanear QR") ── */
        abrirModalConCamara() {
            this.modal        = true;
            this.input        = '';
            this.errorGlobal  = '';
            this.exitoMsg     = '';
            this.$nextTick(() => this._iniciarScanner());
        },

        /* ─── activar cámara desde placeholder ── */
        activarCamara() {
            this._iniciarScanner();
        },

        /* ─── detener cámara manualmente ── */
        async detenerCamara() {
            await this._detenerScanner();
            this.scannerActivo = false;
        },

        async cerrarModal() {
            this.modal        = false;
            this.input        = '';
            this.errorGlobal  = '';
            this.exitoMsg     = '';
            this.lista        = [];
            this.enviando     = false;
            this.scannerActivo = false;
            await this._detenerScanner();
        },

        /* ─── scanner ───────────────────── */
        async _iniciarScanner() {
            if (this._scannerIniciado) return;
            if (typeof Html5Qrcode === 'undefined') return;
            const el = document.getElementById('qr-reader');
            if (!el) return;

            this.scannerActivo    = true;
            this._scannerIniciado = true;

            // Celulares con varios lentes traseros (principal, gran angular,
            // telefoto) — pedir solo facingMode:'environment' deja que el
            // navegador elija cualquiera, y en varios Android abre el gran
            // angular, que enfoca mal de cerca y falla al leer el código.
            if (this.scanCamaras.length === 0) {
                try {
                    const camaras = await Html5Qrcode.getCameras();
                    this.scanCamaras   = camaras || [];
                    this.scanCamaraIdx = this._elegirCamaraTrasera(this.scanCamaras);
                } catch (e) {
                    this.scanCamaras = []; // sin permiso/soporte — cae a facingMode genérico
                }
            }

            await this._arrancarCamara();
        },

        _elegirCamaraTrasera(camaras) {
            if (!camaras || camaras.length <= 1) return 0;
            const evitar    = /ultra ?wide|gran ?angular|wide ?angle|tele ?photo|telefoto|macro/i;
            const esTrasera = c => /back|rear|trasera|environment/i.test(c.label || '');
            const traseras  = camaras.filter(esTrasera);
            const base      = traseras.length ? traseras : camaras;
            const buenas    = base.filter(c => !evitar.test(c.label || ''));
            const elegida   = buenas[0] || base[0];
            return camaras.indexOf(elegida);
        },

        async _arrancarCamara() {
            this._scanner = new Html5Qrcode('qr-reader');
            const camara  = this.scanCamaras[this.scanCamaraIdx];
            const fuente  = camara ? { deviceId: { exact: camara.id } } : { facingMode: 'environment' };

            try {
                await this._scanner.start(
                    fuente,
                    { fps: 10, qrbox: { width: 200, height: 200 } },
                    async (decoded) => {
                        if (this._scannerPausado) return;
                        const serie = decoded.trim().split('/').pop().toUpperCase();
                        if (serie.length === 17) {
                            this._scannerPausado = true;
                            await this.agregarSerie(serie);
                            setTimeout(() => { this._scannerPausado = false; }, 1500);
                        }
                    }
                );
            } catch (e) {
                console.warn('Cámara no disponible:', e);
                this.scannerActivo    = false;
                this._scannerIniciado = false;
            }
        },

        // Botón manual "Cambiar de cámara" — por si la heurística automática
        // no acertó en algún modelo de celular en particular.
        async cambiarCamara() {
            if (this.scanCamaras.length < 2) return;
            this.scanCamaraIdx = (this.scanCamaraIdx + 1) % this.scanCamaras.length;

            const scanner = this._scanner;
            this._scanner = null;
            if (scanner) {
                try { await scanner.stop(); scanner.clear(); } catch (e) {}
            }
            await this._arrancarCamara();
        },

        async _detenerScanner() {
            if (this._scanner && this._scannerIniciado) {
                try {
                    await this._scanner.stop();
                    await this._scanner.clear();
                } catch (_) {}
            }
            this._scanner         = null;
            this._scannerIniciado = false;
            this._scannerPausado  = false;
        },

        /* ─── agregar serie ─────────────── */
        async agregarSerie(serie) {
            serie = (serie || '').trim().toUpperCase();
            if (serie.length !== 17) return;
            if (this.buscando)       return;

            this.errorGlobal = '';
            this.exitoMsg    = '';

            if (this.lista.some(i => i.num_serie === serie)) {
                this.errorGlobal = `${serie} ya está en la cola.`;
                return;
            }

            this.buscando = true;
            try {
                const url  = `{{ url('/bicicletas/qrv') }}/${encodeURIComponent(serie)}`;
                const res  = await fetch(url);
                const json = await res.json().catch(() => ({}));

                if (!res.ok || !json.ok) {
                    this.errorGlobal = json.message || 'Bicicleta no encontrada.';
                    return;
                }

                const bici        = json.bicicleta;
                const colorParts  = (bici.color || '').split('|');
                const colorNombre = colorParts[0]?.trim() || '—';
                const colorHex    = colorParts[1]?.split('/')[0] || '#cccccc';

                let marcaNombre = '—';
                for (const m of this.catalogo) {
                    if (m.modelos.some(mo => String(mo.id_modelo) === String(bici.id_modelo ?? ''))) {
                        marcaNombre = m.nombre_marca;
                        break;
                    }
                }

                this.lista.push({
                    num_serie:   bici.num_serie,
                    id_modelo:   bici.id_modelo  ?? '',
                    id_color:    bici.id_color    ?? '',
                    id_voltaje:  bici.id_voltaje  ?? '',
                    marca:       marcaNombre,
                    modelo:      bici.modelo      || '—',
                    colorNombre,
                    colorHex,
                    voltaje:     bici.voltaje     || '—',
                });

                this.exitoMsg = `${bici.num_serie} agregada ✓`;
                setTimeout(() => { this.exitoMsg = ''; }, 2000);

                this.input = '';
                this.$nextTick(() => {
                    if (this.$refs.inputSerie) this.$refs.inputSerie.focus();
                });

            } catch (e) {
                this.errorGlobal = e.message || 'Error de conexión.';
            } finally {
                this.buscando = false;
            }
        },

        /* ─── enviar ─────────────────────── */
        enviar(form) {
            if (this.lista.length === 0 || this.enviando) return;
            this.enviando = true;
            form.submit();
        },
    };
}
</script>

</x-app-layout>