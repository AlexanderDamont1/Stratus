```blade
<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6"
         x-data="garantiasIndex()">

        {{-- ═══════════════════════════════════════
            HEADER
        ═══════════════════════════════════════ --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600
                                flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                     a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                            Centro de Garantías
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Consulta garantías, bicicletas registradas y estado de cobertura
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400">Consultas hoy</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $estadisticas['consultas_hoy'] ?? 0 }}
                    </p>
                </div>

                <div class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <p class="text-[10px] uppercase tracking-widest text-gray-400">Garantías activas</p>
                    <p class="text-xl font-semibold text-green-600 dark:text-green-400">
                        {{ $estadisticas['activas'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <x-flash-messages />

        {{-- ═══════════════════════════════════════
            HERO SEARCH
        ═══════════════════════════════════════ --}}
        <div class="relative overflow-hidden rounded-3xl border border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-950 shadow-2xl">

            {{-- glow --}}
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-56 h-56 bg-indigo-500/20 rounded-full blur-3xl"></div>

            <div class="relative p-6 sm:p-8 lg:p-10">

                <div class="grid lg:grid-cols-[1.2fr_.8fr] gap-8 items-center">

                    {{-- LEFT --}}
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                                    bg-white/10 border border-white/10 text-white/80 text-xs mb-5">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            Sistema de validación en tiempo real
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-white leading-tight">
                            Busca una bicicleta
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-cyan-200">
                                por número de serie
                            </span>
                        </h1>

                        <p class="mt-4 text-sm sm:text-base text-gray-300 max-w-2xl leading-relaxed">
                            Verifica cobertura, historial, reemplazos y estado de garantía.
                            El taller digital de las bicicletas ⚡
                        </p>

                        {{-- SEARCH --}}
                        <div class="mt-7">
                            <div class="flex flex-col sm:flex-row gap-3">

                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>

                                    <input
                                        type="text"
                                        x-model="numSerie"
                                        @keydown.enter="buscar()"
                                        maxlength="60"
                                        placeholder="Ej. TEST000000000001"
                                        class="w-full h-14 rounded-2xl border border-white/10 bg-white/10 backdrop-blur
                                               text-white placeholder:text-gray-400 pl-12 pr-4
                                               focus:outline-none focus:ring-2 focus:ring-blue-400/40
                                               focus:border-blue-400/30 text-sm font-mono tracking-wider uppercase"
                                        @input="numSerie = $event.target.value.toUpperCase()">

                                </div>

                                <button
                                    @click="buscar()"
                                    :disabled="buscando || !numSerie.trim()"
                                    class="h-14 px-7 rounded-2xl bg-white text-gray-900
                                           font-semibold text-sm shadow-xl
                                           hover:scale-[1.02] active:scale-[0.98]
                                           transition-all duration-200
                                           disabled:opacity-40 disabled:cursor-not-allowed
                                           flex items-center justify-center gap-2">

                                    <svg x-show="!buscando" class="w-5 h-5" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>

                                    <svg x-show="buscando" class="animate-spin w-5 h-5"
                                         fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25"
                                                cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75"
                                              fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>

                                    <span x-text="buscando ? 'Buscando...' : 'Consultar garantía'"></span>
                                </button>
                            </div>

                            {{-- ERROR --}}
                            <div x-show="error"
                                 x-transition
                                 class="mt-4 rounded-2xl border border-red-500/20 bg-red-500/10
                                        text-red-200 px-4 py-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
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

                        {{-- QUICK TAGS --}}
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button @click="numSerie = 'TEST000000000001'"
                                    class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/15
                                           border border-white/10 text-xs text-gray-200 transition">
                                TEST000000000001
                            </button>

                            <button @click="numSerie = 'GW2026MX001'"
                                    class="px-3 py-1.5 rounded-full bg-white/10 hover:bg-white/15
                                           border border-white/10 text-xs text-gray-200 transition">
                                GW2026MX001
                            </button>
                        </div>
                    </div>

                    {{-- RIGHT CARD --}}
                    <div class="grid gap-4">

                        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur p-5">
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
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400">
                                    Reclamos
                                </p>

                                <p class="mt-2 text-2xl font-bold text-white">
                                    {{ $estadisticas['reclamos'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400">
                                    Reemplazos
                                </p>

                                <p class="mt-2 text-2xl font-bold text-white">
                                    {{ $estadisticas['reemplazos'] ?? 0 }}
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
            BICICLETAS RECIENTES
        ═══════════════════════════════════════ --}}
        <div class="grid lg:grid-cols-[1fr_320px] gap-6">

            {{-- TABLE --}}
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm
                        border border-gray-200 dark:border-gray-700 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700
                            flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Bicicletas registradas
                        </h3>

                        <p class="text-sm text-gray-400 mt-1">
                            Historial reciente de garantías activas
                        </p>
                    </div>

                    <div class="relative w-full sm:w-72">
                        <input type="text"
                               x-model="filtro"
                               placeholder="Filtrar serie o modelo..."
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600
                                      bg-gray-50 dark:bg-gray-900
                                      px-4 py-2.5 text-sm
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/20">

                        <svg class="w-4 h-4 text-gray-400 absolute right-4 top-3"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- TABLE DESKTOP --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                    Serie
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                    Modelo
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                    Cliente
                                </th>

                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                    Estado
                                </th>

                                <th class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                    Fecha
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                            @forelse($bicicletas as $bicicleta)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white font-mono">
                                        {{ $bicicleta->num_serie }}
                                    </div>

                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $bicicleta->marca->nombre ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $bicicleta->modelo->nombre_modelo ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $bicicleta->venta?->cliente_nombre ?? 'Sin asignar' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($bicicleta->garantia_activa)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-green-100 text-green-700
                                                     dark:bg-green-900/20 dark:text-green-400
                                                     text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-gray-100 text-gray-700
                                                     dark:bg-gray-700 dark:text-gray-300
                                                     text-xs font-semibold">
                                            Expirada
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-xs text-gray-400">
                                    {{ $bicicleta->updated_at?->format('d/m/Y') }}
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-20 text-center">

                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-3xl bg-gray-100 dark:bg-gray-700
                                                    flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7
                                                         a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                                         a1 1 0 01.707.293l5.414 5.414
                                                         a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>

                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            No hay bicicletas registradas
                                        </p>
                                    </div>

                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $bicicletas->withQueryString()->links() }}
                </div>

            </div>

            {{-- SIDEBAR --}}
            <div class="space-y-6">

                {{-- ACTIVITY --}}
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200
                            dark:border-gray-700 shadow-sm p-5">

                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Actividad reciente
                        </h3>

                        <span class="text-xs text-gray-400">
                            Tiempo real
                        </span>
                    </div>

                    <div class="mt-5 space-y-4">

                        @forelse($actividadReciente ?? [] as $item)
                        <div class="flex gap-3">

                            <div class="mt-0.5">
                                <div class="w-9 h-9 rounded-2xl
                                            bg-blue-100 dark:bg-blue-900/20
                                            flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                         fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-snug">
                                    Garantía consultada para
                                    <span class="font-semibold">
                                        {{ $item->num_serie }}
                                    </span>
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $item->created_at->diffForHumans() }}
                                </p>
                            </div>

                        </div>
                        @empty
                        <p class="text-sm text-gray-400">
                            Sin actividad reciente.
                        </p>
                        @endforelse

                    </div>
                </div>

                {{-- HELP CARD --}}
                <div class="relative overflow-hidden rounded-3xl
                            bg-gradient-to-br from-blue-600 to-indigo-700
                            p-6 text-white shadow-xl">

                    <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10"></div>

                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-white/10
                                    flex items-center justify-center mb-4">
                            <svg class="w-6 h-6"
                                 fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12
                                         a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <h3 class="text-lg font-semibold">
                            Consejo rápido
                        </h3>

                        <p class="mt-2 text-sm text-blue-100 leading-relaxed">
                            Usa el número de serie exacto para encontrar historial,
                            reemplazos y cobertura de garantía al instante.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function garantiasIndex() {
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
```
