<x-app-layout>
    @php
        function parsearColor($colorStr) {
            if (!$colorStr) return ['nombre' => '—', 'hexes' => ['#cccccc']];

            $parts = explode('|', $colorStr, 2);
            $nombre = trim($parts[0] ?? $colorStr);
            $hexPart = $parts[1] ?? '';
            $hexes = $hexPart ? explode('/', $hexPart) : ['#cccccc'];

            return [
                'nombre' => $nombre,
                'hexes' => $hexes
            ];
        }
    @endphp

    <div
        x-data="garantiasPage()"
        class="space-y-6">

        {{-- ═══════════════════════════════════════
            HEADER
        ═══════════════════════════════════════ --}}
        <div class="flex flex-wrap items-start justify-between gap-4">

            <div>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl
                                bg-gradient-to-br from-indigo-600 to-blue-500
                                shadow-lg shadow-indigo-500/20
                                flex items-center justify-center">
                        <svg class="w-5 h-5 text-white"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5
                                     a2 2 0 012-2h5.586a1 1 0 01.707.293
                                     l5.414 5.414a1 1 0 01.293.707V19a2 2
                                     0 01-2 2z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            Garantías
                        </h2>

                        <p class="text-xs text-gray-400 mt-0.5">
                            Centro de búsqueda y validación de bicicletas
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-xl px-4 py-2 shadow-sm min-w-[100px]">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400">
                        Activas
                    </p>

                    <p class="text-xl font-semibold text-green-600 dark:text-green-400">
                        {{ $stats['activas'] ?? 0 }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-xl px-4 py-2 shadow-sm min-w-[100px]">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400">
                        Consultas
                    </p>

                    <p class="text-xl font-semibold text-blue-600 dark:text-blue-400">
                        {{ $stats['consultas'] ?? 0 }}
                    </p>
                </div>

            </div>
        </div>

        <x-flash-messages />

        {{-- ═══════════════════════════════════════
            HERO BUSCADOR
        ═══════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-3xl
                    bg-gradient-to-br from-gray-900 via-slate-900 to-indigo-950
                    shadow-2xl border border-white/5">

            {{-- glow --}}
            <div class="absolute -top-20 right-0 w-72 h-72 bg-blue-500/20 blur-3xl rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/20 blur-3xl rounded-full"></div>

            <div class="relative p-6 sm:p-8 lg:p-10">

                <div class="grid lg:grid-cols-[1.2fr_.8fr] gap-8 items-center">

                    {{-- LEFT --}}
                    <div>

                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                                    bg-white/10 border border-white/10 text-xs text-gray-200 mb-5">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            Validación instantánea
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight">
                            Busca una bicicleta
                            <span class="bg-gradient-to-r from-cyan-300 to-blue-400 bg-clip-text text-transparent">
                                por número de serie
                            </span>
                        </h1>

                        <p class="text-sm sm:text-base text-gray-300 mt-4 max-w-2xl leading-relaxed">
                            Consulta cobertura, estado, historial y datos de garantía.
                            El taller digital donde cada bicicleta deja huella ⚡
                        </p>

                        {{-- SEARCH --}}
                        <div class="mt-7">
                            <div class="flex flex-col sm:flex-row gap-3">

                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0
                                                     7 7 0 0114 0z"/>
                                        </svg>
                                    </div>

                                    <input
                                        type="text"
                                        x-model="numSerie"
                                        @keydown.enter="buscar()"
                                        maxlength="60"
                                        placeholder="Ej. HE0EA2A00SA963753"
                                        class="w-full h-14 rounded-2xl
                                               border border-white/10
                                               bg-white/10 backdrop-blur-md
                                               text-white placeholder:text-gray-400
                                               pl-12 pr-4
                                               focus:outline-none
                                               focus:ring-2 focus:ring-blue-400/30
                                               font-mono tracking-wider uppercase text-sm"
                                        @input="numSerie = $event.target.value.toUpperCase()">
                                </div>

                                <button
                                    @click="buscar()"
                                    :disabled="buscando || !numSerie.trim()"
                                    class="h-14 px-7 rounded-2xl
                                           bg-white text-gray-900
                                           font-semibold text-sm
                                           hover:scale-[1.02]
                                           active:scale-[0.98]
                                           transition-all duration-200
                                           disabled:opacity-40
                                           disabled:cursor-not-allowed
                                           flex items-center justify-center gap-2 shadow-xl">

                                    <svg x-show="!buscando"
                                         class="w-5 h-5"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0
                                                 7 7 0 0114 0z"/>
                                    </svg>

                                    <svg x-show="buscando"
                                         class="animate-spin w-5 h-5"
                                         fill="none"
                                         viewBox="0 0 24 24">
                                        <circle class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"/>
                                        <path class="opacity-75"
                                              fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0
                                                 C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>

                                    <span x-text="buscando ? 'Buscando...' : 'Consultar'"></span>
                                </button>

                            </div>

                            {{-- ERROR --}}
                            <div x-show="error"
                                 x-transition
                                 class="mt-4 rounded-2xl border border-red-500/20
                                        bg-red-500/10 text-red-200 px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 8v4m0 4h.01M5.07 19h13.86
                                                 c1.54 0 2.5-1.67 1.73-3L13.73 4
                                                 c-.77-1.33-2.69-1.33-3.46 0L3.34 16
                                                 c-.77 1.33.19 3 1.73 3z"/>
                                    </svg>

                                    <span x-text="error"></span>
                                </div>
                            </div>

                        </div>

                        {{-- QUICK SEARCH --}}
                        <div class="mt-5 flex flex-wrap gap-2">

                            <button
                                @click="numSerie = 'HE0EA2A00SA963753'"
                                class="px-3 py-1.5 rounded-full bg-white/10
                                       hover:bg-white/15 border border-white/10
                                       text-xs text-gray-200 transition">
                                HE0EA2A00SA963753
                            </button>

                            <button
                                @click="numSerie = 'GW2026MX001'"
                                class="px-3 py-1.5 rounded-full bg-white/10
                                       hover:bg-white/15 border border-white/10
                                       text-xs text-gray-200 transition">
                                GW2026MX001
                            </button>

                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="space-y-4">

                        <div class="rounded-3xl bg-white/5 border border-white/10
                                    backdrop-blur p-5">

                            <div class="flex items-start justify-between">

                                <div>
                                    <p class="text-xs uppercase tracking-widest text-gray-400">
                                        Última garantía
                                    </p>

                                    <h3 class="mt-2 text-lg font-semibold text-white">
                                        {{ $ultimaGarantia->num_serie ?? 'Sin registros' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-400">
                                        {{ $ultimaGarantia->modelo->nombre_modelo ?? '—' }}
                                    </p>
                                </div>

                                <div class="w-12 h-12 rounded-2xl bg-green-500/20
                                            flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-300"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                                <p class="text-[10px] uppercase tracking-widest text-gray-400">
                                    Reclamos
                                </p>

                                <p class="mt-2 text-2xl font-bold text-white">
                                    {{ $stats['reclamos'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                                <p class="text-[10px] uppercase tracking-widest text-gray-400">
                                    Reemplazos
                                </p>

                                <p class="mt-2 text-2xl font-bold text-white">
                                    {{ $stats['reemplazos'] ?? 0 }}
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
            TABLA
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden">

            <div class="px-6 py-4 border-b dark:border-gray-700
                        flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Bicicletas registradas
                    </h3>

                    <p class="text-xs text-gray-400 mt-0.5">
                        Historial reciente de garantías
                    </p>
                </div>

                <div class="relative w-full sm:w-72">

                    <input type="text"
                           x-model="filtro"
                           placeholder="Buscar bicicleta..."
                           class="w-full rounded-xl border border-gray-200 dark:border-gray-700
                                  bg-gray-50 dark:bg-gray-900
                                  px-4 py-2.5 pr-10 text-sm
                                  text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-300">

                    <svg class="w-4 h-4 text-gray-400 absolute right-3 top-3"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0
                                 7 7 0 0114 0z"/>
                    </svg>

                </div>

            </div>

            @php
                $estados = [
                    '1' => ['texto' => 'Activa', 'color' => 'green'],
                    '2' => ['texto' => 'Expirada', 'color' => 'yellow'],
                    '3' => ['texto' => 'Reclamo', 'color' => 'purple'],
                ];
            @endphp

            {{-- DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">

                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                N° Serie
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                Modelo
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                Voltaje
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                Color
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">

                        @forelse($bicicletas as $bicicleta)

                            @php
                                $statusKey = $bicicleta->status_garantia ?? 1;
                                $estado = $estados[$statusKey] ?? ['texto' => 'Desconocido', 'color' => 'red'];
                                $color = $estado['color'];
                                $colorInfo = parsearColor($bicicleta->color->color ?? '');
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">

                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white font-mono">
                                        {{ $bicicleta->num_serie }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $bicicleta->marca->nombre ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $bicicleta->modelo->nombre_modelo ?? '—' }}
                                </td>

                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $bicicleta->voltaje->voltaje ?? '—' }}
                                </td>

                                <td class="px-4 py-3">

                                    <div class="flex items-center gap-1.5">

                                        @if(count($colorInfo['hexes']) >= 2)

                                            <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                                <span class="absolute left-0 top-0 w-1/2 h-full"
                                                      style="background: {{ $colorInfo['hexes'][0] }}"></span>

                                                <span class="absolute right-0 top-0 w-1/2 h-full"
                                                      style="background: {{ $colorInfo['hexes'][1] }}"></span>
                                            </span>

                                        @else

                                            <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                                  style="background: {{ $colorInfo['hexes'][0] }}"></span>

                                        @endif

                                        <span class="text-gray-700 dark:text-gray-300 text-sm">
                                            {{ $colorInfo['nombre'] }}
                                        </span>

                                    </div>

                                </td>

                                <td class="px-4 py-3 text-center">

                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($color)
                                            @case('green')
                                                bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400
                                            @break

                                            @case('yellow')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400
                                            @break

                                            @case('purple')
                                                bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400
                                            @break

                                            @default
                                                bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                        @endswitch
                                    ">
                                        {{ $estado['texto'] }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    class="px-6 py-14 text-center text-gray-500 dark:text-gray-400">

                                    <div class="flex flex-col items-center">

                                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700
                                                    flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-gray-400"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5
                                                         a2 2 0 012-2h5.586a1 1 0 01.707.293
                                                         l5.414 5.414a1 1 0 01.293.707V19a2 2
                                                         0 01-2 2z"/>
                                            </svg>
                                        </div>

                                        <p class="text-sm font-medium">
                                            No se encontraron bicicletas
                                        </p>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- MOBILE --}}
            <div class="block md:hidden divide-y divide-gray-200 dark:divide-gray-700">

                @forelse($bicicletas as $bicicleta)

                    @php
                        $statusKey = $bicicleta->status_garantia ?? 1;
                        $estado = $estados[$statusKey] ?? ['texto' => 'Desconocido', 'color' => 'red'];
                        $color = $estado['color'];
                        $colorInfo = parsearColor($bicicleta->color->color ?? '');
                    @endphp

                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="font-mono text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $bicicleta->num_serie }}
                                </div>

                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $bicicleta->modelo->nombre_modelo ?? '—' }}
                                </div>

                                <div class="flex items-center gap-2 mt-2">

                                    @if(count($colorInfo['hexes']) >= 2)

                                        <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                            <span class="absolute left-0 top-0 w-1/2 h-full"
                                                  style="background: {{ $colorInfo['hexes'][0] }}"></span>

                                            <span class="absolute right-0 top-0 w-1/2 h-full"
                                                  style="background: {{ $colorInfo['hexes'][1] }}"></span>
                                        </span>

                                    @else

                                        <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                              style="background: {{ $colorInfo['hexes'][0] }}"></span>

                                    @endif

                                    <span class="text-xs text-gray-500 dark:text-gray-300">
                                        {{ $colorInfo['nombre'] }}
                                    </span>

                                </div>

                            </div>

                            <div class="text-right">

                                <span class="px-2 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap
                                    @switch($color)
                                        @case('green')
                                            bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400
                                        @break

                                        @case('yellow')
                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400
                                        @break

                                        @case('purple')
                                            bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400
                                        @break

                                        @default
                                            bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                    @endswitch
                                ">
                                    {{ $estado['texto'] }}
                                </span>

                                <div class="text-xs text-gray-400 mt-2">
                                    {{ $bicicleta->voltaje->voltaje ?? '—' }}
                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="px-4 py-12 text-center text-gray-500 text-sm">
                        No hay bicicletas registradas
                    </div>

                @endforelse

            </div>

            @if($bicicletas->hasPages())
                <div class="px-6 py-4 border-t dark:border-gray-700">
                    {{ $bicicletas->withQueryString()->links() }}
                </div>
            @endif

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

                        const res = await fetch(`{{ route('garantias.buscar') }}?num_serie=${encodeURIComponent(serie)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        const data = await res.json();

                        if (!data.ok) {
                            this.error = data.mensaje || 'No se encontró la garantía';
                            return;
                        }

                        window.location.href = data.redirect;

                    } catch (e) {

                        this.error = 'Error de conexión.';

                    } finally {

                        this.buscando = false;

                    }
                }
            }
        }
    </script>
</x-app-layout>