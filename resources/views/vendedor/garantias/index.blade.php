<x-app-layout>
    @php
        function parsearColor($colorStr) {
            if (!$colorStr) return ['nombre' => '—', 'hexes' => ['#cccccc']];
            $parts  = explode('|', $colorStr, 2);
            $nombre = trim($parts[0] ?? $colorStr);
            $hexPart = $parts[1] ?? '';
            $hexes  = $hexPart ? explode('/', $hexPart) : ['#cccccc'];
            return ['nombre' => $nombre, 'hexes' => $hexes];
        }

        $estados = [
            'vigente'     => ['texto' => 'Vigente',     'color' => 'green'],
            'por_vencer'  => ['texto' => 'Por vencer',  'color' => 'yellow'],
            'expirada'    => ['texto' => 'Expirada',    'color' => 'red'],
            'reemplazada' => ['texto' => 'Reemplazada', 'color' => 'purple'],
            'invalidada'  => ['texto' => 'Invalidada',  'color' => 'red'],
        ];
    @endphp

    <div x-data="garantiasPage()" class="space-y-5 pb-10">

        {{-- ═══════════════════════════════════════════════
            ENCABEZADO  (igual al patrón de ventas/create)
        ════════════════════════════════════════════════ --}}
        <div class="flex flex-wrap items-start justify-between gap-4">

            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Garantías
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Busca y valida el estado de cobertura de cualquier bicicleta
                </p>
            </div>

            {{-- Estadísticas compactas --}}
            <div class="flex items-center gap-2 flex-wrap">

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-xl px-4 py-2.5 shadow-sm text-center min-w-[90px]">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">
                        Vigentes
                    </p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400 tabular-nums leading-tight mt-0.5">
                        {{ $stats['activas'] ?? 0 }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-xl px-4 py-2.5 shadow-sm text-center min-w-[90px]">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">
                        Reclamos
                    </p>
                    <p class="text-xl font-bold text-amber-500 dark:text-amber-400 tabular-nums leading-tight mt-0.5">
                        {{ $stats['reclamos'] ?? 0 }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-xl px-4 py-2.5 shadow-sm text-center min-w-[90px]">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400 font-medium">
                        Reemplazos
                    </p>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400 tabular-nums leading-tight mt-0.5">
                        {{ $stats['reemplazos'] ?? 0 }}
                    </p>
                </div>

            </div>
        </div>

        <x-flash-messages />

        {{-- ═══════════════════════════════════════════════
            GRID  PRINCIPAL  — izquierda buscador / derecha info
        ════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

            {{-- ══════════════════ COLUMNA IZQUIERDA (3/5) ══════════════════ --}}
            <div class="lg:col-span-3 space-y-5">

                {{-- ── BUSCADOR ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl
                            border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700
                                flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                                Buscar bicicleta
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Ingresa el número de serie para consultar su garantía
                            </p>
                        </div>

                        <div class="w-7 h-7 rounded-lg bg-gray-900 dark:bg-gray-100
                                    flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white dark:text-gray-900"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">

                        {{-- Input + botón --}}
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    x-model="numSerie"
                                    @keydown.enter="buscar()"
                                    @input="numSerie = $event.target.value.toUpperCase()"
                                    maxlength="17"
                                    placeholder="HE0EA2A00SA963753"
                                    :disabled="buscando"
                                    class="w-full px-4 py-2.5 rounded-lg border
                                           bg-white dark:bg-gray-900
                                           text-gray-900 dark:text-white text-sm
                                           font-mono uppercase tracking-widest
                                           focus:outline-none focus:ring-2 focus:ring-gray-400 transition
                                           placeholder:normal-case placeholder:tracking-normal placeholder:font-sans
                                           placeholder:text-gray-300 dark:placeholder:text-gray-600
                                           border-gray-200 dark:border-gray-600">
                            </div>

                            <button
                                @click="buscar()"
                                :disabled="buscando || !numSerie.trim()"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                       px-5 py-2.5 rounded-lg text-sm font-semibold
                                       hover:opacity-90 active:scale-[.98] transition
                                       disabled:opacity-30 disabled:cursor-not-allowed
                                       flex items-center gap-2 shrink-0">

                                <template x-if="!buscando">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </template>
                                <template x-if="buscando">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>

                                <span x-text="buscando ? 'Buscando…' : 'Consultar'"></span>
                            </button>
                        </div>

                        {{-- Error inline --}}
                        <div x-show="error"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="flex items-center gap-2 rounded-lg border border-red-200 dark:border-red-800
                                    bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm
                                    text-red-700 dark:text-red-400">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67
                                         1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                            </svg>
                            <span x-text="error"></span>
                        </div>

                        {{-- Series de ejemplo --}}
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-2 font-medium">
                                Series de ejemplo
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="numSerie = 'HE0EA2A00SA963753'"
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600
                                           bg-gray-50 dark:bg-gray-700/40
                                           text-xs font-mono text-gray-600 dark:text-gray-300
                                           hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    HE0EA2A00SA963753
                                </button>
                                <button
                                    @click="numSerie = 'GW2026MX001'"
                                    class="px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600
                                           bg-gray-50 dark:bg-gray-700/40
                                           text-xs font-mono text-gray-600 dark:text-gray-300
                                           hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    GW2026MX001
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── TABLA DE BICICLETAS ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl
                            border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700
                                flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Bicicletas registradas
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Historial reciente de garantías
                            </p>
                        </div>

                        <div class="relative w-full sm:w-64">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                type="text"
                                x-model="filtro"
                                placeholder="Buscar bicicleta…"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-600
                                       bg-gray-50 dark:bg-gray-900
                                       pl-9 pr-4 py-2 text-sm
                                       text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-1 focus:ring-gray-300 transition
                                       placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- DESKTOP --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <th class="px-5 py-3 text-left text-[11px] font-semibold
                                               text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        N° Serie
                                    </th>
                                    <th class="px-5 py-3 text-left text-[11px] font-semibold
                                               text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Modelo
                                    </th>
                                    <th class="px-5 py-3 text-left text-[11px] font-semibold
                                               text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Voltaje
                                    </th>
                                    <th class="px-5 py-3 text-left text-[11px] font-semibold
                                               text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Color
                                    </th>
                                    <th class="px-5 py-3 text-center text-[11px] font-semibold
                                               text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700
                                          bg-white dark:bg-gray-900">

                                @forelse($bicicletas as $bicicleta)
                                    @php
                                        $statusKey = $bicicleta->status_garantia ?? 'vigente';
                                        $estado    = $estados[$statusKey] ?? ['texto' => 'Desconocido', 'color' => 'gray'];
                                        $color     = $estado['color'];
                                        $colorInfo = parsearColor($bicicleta->color->color ?? '');
                                    @endphp

                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition group">

                                        <td class="px-5 py-3.5">
                                            <span class="font-mono text-sm font-medium
                                                         text-gray-900 dark:text-white">
                                                {{ $bicicleta->num_serie }}
                                            </span>
                                            <p class="text-[11px] text-gray-400 mt-0.5">
                                                {{ $bicicleta->marca->nombre ?? '—' }}
                                            </p>
                                        </td>

                                        <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $bicicleta->modelo->nombre_modelo ?? '—' }}
                                        </td>

                                        <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-300">
                                            {{ $bicicleta->voltaje->voltaje ?? '—' }}
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-2">
                                                @if(count($colorInfo['hexes']) >= 2)
                                                    <span class="w-4 h-4 rounded border border-black/10
                                                                 dark:border-white/10 overflow-hidden
                                                                 relative inline-flex shrink-0">
                                                        <span class="absolute left-0 top-0 w-1/2 h-full"
                                                              style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                                        <span class="absolute right-0 top-0 w-1/2 h-full"
                                                              style="background:{{ $colorInfo['hexes'][1] }}"></span>
                                                    </span>
                                                @else
                                                    <span class="w-4 h-4 rounded border border-black/10
                                                                 dark:border-white/10 shrink-0 inline-block"
                                                          style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                                @endif
                                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $colorInfo['nombre'] }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $badgeClass = match($color) {
                                                    'green'  => 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400',
                                                    'yellow' => 'bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-400',
                                                    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400',
                                                    default  => 'bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-400',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                                         text-[11px] font-semibold {{ $badgeClass }}">
                                                {{ $estado['texto'] }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-3.5 text-right">
                                            <a href="{{ route('garantias.show', $bicicleta->num_serie) }}"
                                               class="invisible group-hover:visible inline-flex items-center gap-1
                                                      text-xs font-medium text-gray-500 dark:text-gray-400
                                                      hover:text-gray-900 dark:hover:text-white transition">
                                                Ver
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </td>

                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-14 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700
                                                            flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0
                                                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1
                                                                 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                    No se encontraron bicicletas
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE  --}}
                    <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-700">

                        @forelse($bicicletas as $bicicleta)
                            @php
                                $statusKey = $bicicleta->status_garantia ?? 'vigente';
                                $estado    = $estados[$statusKey] ?? ['texto' => 'Desconocido', 'color' => 'gray'];
                                $color     = $estado['color'];
                                $colorInfo = parsearColor($bicicleta->color->color ?? '');
                                $badgeClass = match($color) {
                                    'green'  => 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400',
                                    'yellow' => 'bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-400',
                                    'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400',
                                    default  => 'bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-400',
                                };
                            @endphp

                            <a href="{{ route('garantias.show', $bicicleta->num_serie) }}"
                               class="flex items-start justify-between gap-3 px-5 py-4
                                      hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">

                                <div class="min-w-0 flex-1">
                                    <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $bicicleta->num_serie }}
                                    </span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $bicicleta->modelo->nombre_modelo ?? '—' }}
                                        @if($bicicleta->voltaje->voltaje ?? false)
                                            · {{ $bicicleta->voltaje->voltaje }}
                                        @endif
                                    </p>

                                    <div class="flex items-center gap-1.5 mt-2">
                                        @if(count($colorInfo['hexes']) >= 2)
                                            <span class="w-3.5 h-3.5 rounded border border-black/10
                                                         dark:border-white/10 overflow-hidden
                                                         relative inline-flex shrink-0">
                                                <span class="absolute left-0 top-0 w-1/2 h-full"
                                                      style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                                <span class="absolute right-0 top-0 w-1/2 h-full"
                                                      style="background:{{ $colorInfo['hexes'][1] }}"></span>
                                            </span>
                                        @else
                                            <span class="w-3.5 h-3.5 rounded border border-black/10
                                                         dark:border-white/10 shrink-0 inline-block"
                                                  style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                        @endif
                                        <span class="text-xs text-gray-500 dark:text-gray-300">
                                            {{ $colorInfo['nombre'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full
                                                 text-[11px] font-semibold {{ $badgeClass }}">
                                        {{ $estado['texto'] }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>

                            </a>

                        @empty
                            <div class="px-5 py-12 text-center text-sm text-gray-400">
                                No hay bicicletas registradas
                            </div>
                        @endforelse

                    </div>

                    @if($bicicletas->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $bicicletas->withQueryString()->links() }}
                        </div>
                    @endif

                </div>

            </div>

            {{-- ══════════════════ COLUMNA DERECHA (2/5) ══════════════════ --}}
            <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-6">

                {{-- Última garantía --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl
                            border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700
                                flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                            Última garantía
                        </h3>
                        <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    </div>

                    <div class="px-5 py-4">
                        @if($ultimaGarantia)
                            <p class="font-mono text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $ultimaGarantia->num_serie }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $ultimaGarantia->bicicleta->modelo->nombre_modelo ?? '—' }}
                            </p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                             text-[11px] font-semibold
                                             bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">
                                    Registrada
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $ultimaGarantia->created_at?->diffForHumans() ?? '' }}
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Sin registros aún</p>
                        @endif
                    </div>
                </div>

                {{-- Resumen de estados --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl
                            border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                            Resumen de cobertura
                        </h3>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">

                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Vigentes</span>
                            </div>
                            <span class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white">
                                {{ $stats['activas'] ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Reclamos abiertos</span>
                            </div>
                            <span class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white">
                                {{ $stats['reclamos'] ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Reemplazos</span>
                            </div>
                            <span class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white">
                                {{ $stats['reemplazos'] ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Total consultas</span>
                            </div>
                            <span class="text-sm font-semibold tabular-nums text-gray-900 dark:text-white">
                                {{ $stats['consultas'] ?? 0 }}
                            </span>
                        </div>

                    </div>
                </div>

                {{-- Tip de uso --}}
                <div class="rounded-xl border border-gray-200 dark:border-gray-700
                            bg-gray-50 dark:bg-gray-800/50 px-5 py-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        ¿Cómo funciona?
                    </p>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="mt-0.5 text-gray-400 shrink-0">1.</span>
                            Escribe o pega el número de serie de la bicicleta.
                        </li>
                        <li class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="mt-0.5 text-gray-400 shrink-0">2.</span>
                            Presiona <strong class="text-gray-700 dark:text-gray-300">Consultar</strong>
                            o Enter para ver el detalle.
                        </li>
                        <li class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="mt-0.5 text-gray-400 shrink-0">3.</span>
                            Desde el detalle puedes abrir reclamos o ver el historial completo.
                        </li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

    <script>
        function garantiasPage() {
            return {
                numSerie: '',
                buscando: false,
                error: '',
                filtro: '',

                async buscar() {
                    this.error = '';
                    const serie = this.numSerie.trim();
                    if (!serie) return;

                    this.buscando = true;

                    try {
                        const res = await fetch(
                            `{{ route('garantias.buscar') }}?num_serie=${encodeURIComponent(serie)}`,
                            {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            }
                        );
                        const data = await res.json();

                        if (!data.ok) {
                            this.error = data.mensaje || 'No se encontró la garantía.';
                            return;
                        }

                        window.location.href = data.redirect;

                    } catch {
                        this.error = 'Error de conexión.';
                    } finally {
                        this.buscando = false;
                    }
                }
            }
        }
    </script>

</x-app-layout>