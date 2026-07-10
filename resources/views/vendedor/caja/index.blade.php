<x-app-layout>

    <div class="space-y-6" x-data="{ modal: '' }"
        x-init="
            const p = new URLSearchParams(window.location.search);
            if (p.get('abrir') === '1') modal = 'abrir';

            @if(session('limite_excedido'))
            modal = 'limite_excedido';
            @endif
        ">

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Mi Caja</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $caja->nombre ?? 'Caja principal' }} &nbsp;·&nbsp; {{ auth()->user()->nombre_usuario }}</p>
            </div>
            @if($sesion)
                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Abierta
                </span>
            @else
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Cerrada</span>
            @endif
        </div>

        {{-- ===== ALERTAS ===== --}}
        @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-2 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- ══════════════════════════════════════════════
             CAJA CERRADA
        ══════════════════════════════════════════════ --}}
        @if(!$sesion)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Caja cerrada</h3>
                <p class="text-sm text-gray-400 mb-6 max-w-xs mx-auto">Abre una sesión para comenzar a registrar los movimientos del día.</p>
                <button type="button" @click="modal = 'abrir'"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Abrir sesión de caja
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             SESIÓN ABIERTA
        ══════════════════════════════════════════════ --}}
        @elseif($sesion && $snapshot)

        {{-- Card principal de la caja --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col">

            {{-- Cabecera --}}
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->nombre_usuario }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-0.5 truncate">{{ $caja->nombre ?? 'Caja principal' }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Abierta
                </span>
            </div>

            {{-- Stats grid --}}
            <div class="px-5 py-4 flex-1">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Total sistema</p>
                        <p id="ws-total-sistema"
                           class="text-lg font-semibold text-green-600 dark:text-green-400 font-medium transition-all duration-300">
                            ${{ number_format($snapshot['totales']['total_sistema'], 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Ventas cobradas</p>
                        <p id="ws-ingresos-ventas"
                           class="text-lg font-semibold text-yellow-600 dark:text-yellow-400 font-medium transition-all duration-300">
                            ${{ number_format($snapshot['totales']['ingresos_ventas'], 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5"># Ventas</p>
                        <p id="ws-ventas-count"
                           class="text-lg font-semibold text-gray-900 dark:text-white font-medium transition-all duration-300">
                            {{ $snapshot['ventas_count'] }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Fondo inicial</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-medium">
                            ${{ number_format($snapshot['sesion']['fondo_inicial'], 2) }}
                        </p>
                    </div>
                </div>

                {{-- Rows de detalle --}}
                <div class="border-t dark:border-gray-700 pt-2 space-y-1">
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-gray-400">Abierta desde</span>
                        <span class="font-medium font-medium text-gray-600 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    @if(($snapshot['totales']['ingresos_manuales'] ?? 0) > 0)
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-gray-400">Ing. manuales</span>
                        <span class="font-medium font-medium text-blue-600 dark:text-blue-400">+${{ number_format($snapshot['totales']['ingresos_manuales'], 2) }}</span>
                    </div>
                    @endif
                    @if(($snapshot['totales']['retiros'] ?? 0) > 0)
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-gray-400">Retiros</span>
                        <span class="font-medium font-medium text-red-600 dark:text-red-400">-${{ number_format($snapshot['totales']['retiros'], 2) }}</span>
                    </div>
                    @endif
                    @if(($snapshot['totales']['ajustes_neto'] ?? 0) != 0)
                    @php $aj = $snapshot['totales']['ajustes_neto']; @endphp
                    <div class="flex items-center justify-between text-xs py-1">
                        <span class="text-gray-400">Ajustes</span>
                        <span class="font-medium font-medium {{ $aj >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $aj >= 0 ? '+' : '' }}${{ number_format($aj, 2) }}
                        </span>
                    </div>
                    @endif

                    {{-- Por método de pago --}}
                    @if(!empty($snapshot['por_metodo']))
                    <div class="pt-2 border-t dark:border-gray-700">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1.5">Por método de pago</p>
                        @foreach($snapshot['por_metodo'] as $m)
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm">{{ ($m['es_efectivo'] ?? false) ? '💵' : '💳' }}</span>
                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $m['label'] }}</span>
                            </div>
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">${{ number_format($m['total'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer acciones --}}
            <div class="px-5 py-3 border-t dark:border-gray-700 flex flex-wrap gap-2">
                <button type="button" @click="modal = 'ingreso'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    + Ingreso manual
                </button>
                {{-- NUEVO --}}
                <button type="button" @click="modal = 'gasto'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    − Registrar gasto
                </button>
                <button type="button" @click="modal = 'corte'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Corte parcial
                </button>
                <button type="button" @click="modal = 'cierre'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:opacity-80 transition">
                    Cerrar caja
                </button>
            </div>
        </div>

        @endif


        {{-- ══════════════════════════════════════════════
            GASTOS DE ESTA SEMANA
        ══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Gastos de esta semana</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ now()->startOfWeek()->format('d/m') }} – {{ now()->endOfWeek()->format('d/m') }}</p>
                </div>
                <button type="button" @click="modal = 'gasto'"
                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                    + Registrar gasto
                </button>
            </div>
            <div class="px-5 py-4">
                <div class="flex items-end justify-between mb-2">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Gastado</p>
                        <p class="text-xl font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($gastoSemana, 2) }}
                        </p>
                    </div>
                    @if($limiteGasto !== null)
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Límite</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                            ${{ number_format($limiteGasto, 2) }}
                        </p>
                    </div>
                    @endif
                </div>

                @if($limiteGasto !== null)
                @php
                    $pct = $limiteGasto > 0 ? min(100, round($gastoSemana / $limiteGasto * 100)) : 0;
                    $excedido = $gastoSemana > $limiteGasto;
                @endphp
                <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $excedido ? 'bg-red-500' : ($pct > 80 ? 'bg-amber-500' : 'bg-green-500') }}"
                        style="width: {{ $pct }}%"></div>
                </div>
                @if($excedido)
                <p class="text-xs text-red-600 dark:text-red-400 font-medium mt-2">
                    ⚠ Superaste el límite semanal. Pide a tu supervisor que aumente el tope.
                </p>
                @endif
                @else
                <p class="text-xs text-gray-400">Esta sucursal no tiene límite configurado.</p>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
             Historial de sesiones
        ══════════════════════════════════════════════ --}}
        @if($historial->count())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Últimas sesiones</h3>
                <span class="text-xs text-gray-400">{{ $historial->count() }} registros</span>
            </div>

            {{-- Vista PC --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Apertura</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cierre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fondo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Sistema</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Diferencia</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($historial as $ses)
                        @php $corteRec = $ses->cortes()->orderByDesc('created_at')->first(); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white">{{ $ses->abierta_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-400">{{ $ses->cerrada_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white">${{ number_format($ses->fondo_inicial, 2) }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white">${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-xs font-medium {{ (($ses->diferencia ?? 0) < 0) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                @if($ses->diferencia !== null)
                                    {{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($ses->estado === 'abierta')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">Abierta</span>
                                @elseif($ses->estado === 'auto_cerrada')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Auto</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-400">Cerrada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($corteRec)
                                <a href="{{ route('caja.corte.pdf', $corteRec->id_corte) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    PDF
                                </a>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Vista móvil --}}
            <div class="block md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($historial as $ses)
                @php $corteRec = $ses->cortes()->orderByDesc('created_at')->first(); @endphp
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $ses->abierta_at?->format('d/m/Y H:i') }}</span>
                        @if($ses->estado === 'abierta')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">Abierta</span>
                        @elseif($ses->estado === 'auto_cerrada')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Auto</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-400">Cerrada</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Sistema: <span class="font-medium text-gray-900 dark:text-white">${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}</span></span>
                        <span class="font-medium {{ (($ses->diferencia ?? 0) < 0) ? 'text-red-500' : 'text-green-500' }}">
                            @if($ses->diferencia !== null){{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}@endif
                        </span>
                        @if($corteRec)
                        <a href="{{ route('caja.corte.pdf', $corteRec->id_corte) }}" target="_blank"
                            class="text-red-500 dark:text-red-400 font-semibold">PDF</a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════
             MODAL: ABRIR SESIÓN
        ════════════════════════════════ --}}
        <div x-show="modal === 'abrir'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Abrir sesión de caja</h3>
                <p class="text-xs text-gray-400 mb-5">Ingresa el fondo inicial en efectivo.</p>
                <form method="POST" action="{{ route('caja.abrir') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Fondo inicial (efectivo)</label>
                        <input type="number" name="fondo_inicial" step="0.01" min="0" max="999999.99"
                               placeholder="0.00" required
                               x-init="$watch('modal', v => v === 'abrir' && $nextTick(() => $el.focus()))"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                            Abrir caja
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             MODAL: INGRESO MANUAL
        ════════════════════════════════ --}}
        <div x-show="modal === 'ingreso'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar ingreso manual</h3>
                <p class="text-xs text-gray-400 mb-5">Entrada de dinero que no proviene de una venta.</p>
                <form method="POST" action="{{ route('caja.ingreso') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto</label>
                            <input type="number" name="monto" step="0.01" min="0.01" max="999999.99" placeholder="0.00" required
                                   x-init="$watch('modal', v => v === 'ingreso' && $nextTick(() => $el.focus()))"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Método de pago</label>
                            <select name="metodo"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto</label>
                            <input type="text" name="concepto" maxlength="200" placeholder="Ej. Depósito de efectivo..." required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Referencia <span class="normal-case font-normal text-gray-400">(opcional)</span>
                            </label>
                            <input type="text" name="referencia" maxlength="120" placeholder="Folio, número de transferencia..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                            Registrar ingreso
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
     MODAL: REGISTRAR GASTO
════════════════════════════════ --}}
<div x-show="modal === 'gasto'" x-cloak
     x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
     @click.self="modal = ''">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar gasto</h3>
        <p class="text-xs text-gray-400 mb-5">Salida de dinero de la sucursal (proveedor, gasolina, papelería, etc.)</p>
        <form method="POST" action="{{ route('caja.gasto') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99" placeholder="0.00" required
                           x-init="$watch('modal', v => v === 'gasto' && $nextTick(() => $el.focus()))"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Motivo</label>
                    <input type="text" name="motivo" maxlength="100" placeholder="Ej. Gasolina, Proveedor X, Papelería..." required
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Referencia <span class="normal-case font-normal text-gray-400">(opcional)</span>
                    </label>
                    <input type="text" name="referencia" maxlength="120" placeholder="Folio, factura..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Notas <span class="normal-case font-normal text-gray-400">(opcional)</span>
                    </label>
                    <textarea name="notas" rows="2" maxlength="500" placeholder="Observaciones..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition resize-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" @click="modal = ''"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Registrar gasto
                </button>
            </div>
        </form>
    </div>
</div>

        {{-- ════════════════════════════════
             MODAL: CORTE PARCIAL
        ════════════════════════════════ --}}
        <div x-show="modal === 'corte'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Generar corte parcial</h3>
                <p class="text-xs text-gray-400 mb-5">La sesión <strong class="text-gray-700 dark:text-gray-300">no se cierra</strong> — solo se genera el PDF.</p>
                <form method="POST" action="{{ route('caja.corte.parcial') }}">
                    @csrf
                    @if($snapshot)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3 space-y-2 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Total sistema</span>
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t dark:border-gray-700 pt-2">
                            <span class="text-xs text-gray-400">Ventas</span>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $snapshot['ventas_count'] }}</span>
                        </div>
                        @if(($snapshot['totales']['retiros'] ?? 0) > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Retiros</span>
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">-${{ number_format($snapshot['totales']['retiros'], 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center border-t dark:border-gray-700 pt-2">
                            <span class="text-xs text-gray-400">Abierta desde</span>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m H:i') }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ════════════════════════════════
            MODAL: LÍMITE EXCEDIDO
        ════════════════════════════════ --}}
        @if(session('limite_excedido'))
        <div x-show="modal === 'limite_excedido'" x-cloak
            x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
            @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
                <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Superaste tu límite de gasto</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Llevas <strong>${{ number_format(session('limite_excedido_data')['total_semana'] ?? 0, 2) }}</strong>
                    esta semana, contra un límite de
                    <strong>${{ number_format(session('limite_excedido_data')['limite'] ?? 0, 2) }}</strong>.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                    Pídele a tu supervisor que aumente el tope del límite si necesitas seguir registrando gastos.
                </p>
                <button type="button" @click="modal = ''"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                    Entendido
                </button>
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════
             MODAL: CERRAR SESIÓN
        ════════════════════════════════ --}}
        <div x-show="modal === 'cierre'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">⚠ Cerrar sesión de caja</h3>
                <p class="text-xs text-gray-400 mb-5">Se generará el corte de cierre. Acción irreversible.</p>
                <form method="POST" action="{{ route('caja.cerrar') }}">
                    @csrf
                    @if($snapshot)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3 space-y-2 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Total sistema</span>
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t dark:border-gray-700 pt-2">
                            <span class="text-xs text-gray-400">Ventas</span>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $snapshot['ventas_count'] }}</span>
                        </div>
                        @if(($snapshot['totales']['retiros'] ?? 0) > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">Retiros</span>
                            <span class="text-xs font-medium text-red-600 dark:text-red-400">-${{ number_format($snapshot['totales']['retiros'], 2) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Monto declarado <span class="normal-case font-normal">(opcional)</span>
                            </label>
                            <input type="number" name="monto_declarado" step="0.01" min="0" max="9999999.99"
                                   placeholder="{{ $snapshot ? number_format($snapshot['totales']['total_sistema'], 2) : '0.00' }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                            <p class="text-xs text-gray-400 mt-1">Si lo dejas vacío se usa el total del sistema.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Notas <span class="normal-case font-normal">(opcional)</span>
                            </label>
                            <textarea name="notas" rows="2" maxlength="500" placeholder="Observaciones del cierre..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Cerrar y generar corte
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             TOAST WEBSOCKET
        ════════════════════════════════ --}}
        <div id="ws-toast"
             class="fixed bottom-5 right-5 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200
                        dark:border-gray-700 rounded-xl shadow-xl px-4 py-3 min-w-[260px]">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-0.5">Nueva venta</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="ws-toast-msg"></p>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <style>[x-cloak]{display:none!important;}</style>

    @if($sesion)
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const elTotalSistema   = document.getElementById('ws-total-sistema');
        const elVentasCount    = document.getElementById('ws-ventas-count');
        const elIngresosVentas = document.getElementById('ws-ingresos-ventas');
        const elToast          = document.getElementById('ws-toast');
        const elToastMsg       = document.getElementById('ws-toast-msg');

        const miIdUsuario = '{{ auth()->user()->id_usuario }}';

        function fmt(n) {
            return '$' + Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function pulsar(el) {
            if (!el) return;
            el.classList.add('scale-105');
            setTimeout(() => el.classList.remove('scale-105'), 300);
        }

        function mostrarToast(msg) {
            elToastMsg.textContent = msg;
            elToast.classList.remove('opacity-0', 'pointer-events-none');
            elToast.classList.add('opacity-100');
            clearTimeout(window._toastTimer);
            window._toastTimer = setTimeout(() => {
                elToast.classList.remove('opacity-100');
                elToast.classList.add('opacity-0', 'pointer-events-none');
            }, 4000);
        }

        window.Echo.private(`negocio.{{ auth()->user()->id_negocio }}`)
        .listen('.venta.registrada', (e) => {
            if (e.id_usuario !== miIdUsuario) return;

            if (elTotalSistema)   { elTotalSistema.textContent   = fmt(e.total_sistema);    pulsar(elTotalSistema); }
            if (elVentasCount)    { elVentasCount.textContent    = e.ventas_count;          pulsar(elVentasCount); }
            if (elIngresosVentas) { elIngresosVentas.textContent = fmt(e.total_sistema);    pulsar(elIngresosVentas); } // ← usa total_sistema directo

            mostrarToast(`${fmt(e.total)} · ${e.hora}`);
        });
    });
    </script>
    @endif
    @endpush

</x-app-layout>