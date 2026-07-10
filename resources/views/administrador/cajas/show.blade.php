<x-app-layout>

    <div class="space-y-6" x-data="{ modal: '', ajusteDir: 1 }">

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

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <a href="{{ route('admin.cajas.index') }}" class="hover:text-gray-600 dark:hover:text-gray-200 transition">← Cajas</a>
                    <span>/</span>
                    <span>{{ $sucursal->nombre_usuario }}</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $sucursal->nombre_usuario }}</h2>
                <p class="text-xs text-gray-400 mt-0.5 font-medium">
                    {{ $caja->nombre ?? 'Sin caja asignada' }}
                    @if($caja) &nbsp;·&nbsp; {{ $caja->id_caja }} @endif
                </p>
            </div>
            @if($sesion)
                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Sesión abierta
                </span>
            @elseif($caja)
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Sesión cerrada</span>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════
             SIN CAJA
        ══════════════════════════════════════════════ --}}
        @if(!$caja)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="text-center py-16 px-6">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Esta sucursal no tiene caja asignada</h3>
                <p class="text-sm text-gray-400 mb-6 max-w-xs mx-auto">Crea una caja para que el vendedor pueda abrir sesiones y registrar movimientos.</p>
                <button type="button" @click="modal = 'crear'"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Crear caja para esta sucursal
                </button>
            </div>
        </div>

        @else

        {{-- ══════════════════════════════════════════════
             CON CAJA — SESIÓN ACTIVA
        ══════════════════════════════════════════════ --}}
        @if($sesion && $snapshot)

        {{-- Card principal --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col">

            {{-- Cabecera --}}
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $sucursal->nombre_usuario }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-0.5 truncate">{{ $caja->nombre ?? 'Caja principal' }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 shrink-0 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Abierta
                </span>
            </div>

            {{-- Stats grid --}}
            <div class="px-5 py-4 flex-1">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Total sistema</p>
                        <p id="ws-show-total-sistema"
                           class="text-lg font-semibold text-green-600 dark:text-green-400 font-medium transition-all duration-300">
                            ${{ number_format($snapshot['totales']['total_sistema'], 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Ventas</p>
                        <p id="ws-show-ingresos-ventas"
                           class="text-lg font-semibold text-yellow-600 dark:text-yellow-400 font-medium transition-all duration-300">
                            ${{ number_format($snapshot['totales']['ingresos_ventas'], 2) }}
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5"># Ventas</p>
                        <p id="ws-show-ventas-count"
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
                        <span class="text-gray-400">Ajustes neto</span>
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
                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $m['nombre'] ?? $m['label'] }}</span>
                            </div>
                            <span class="text-xs font-medium font-semibold text-green-600 dark:text-green-400">${{ number_format($m['total'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="border-t dark:border-gray-700 pt-2 space-y-1">
                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-gray-400">Abierta por</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $snapshot['usuario']['nombre_usuario'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-gray-400">Desde</span>
                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-gray-400">ID sesión</span>
                            <span class="font-medium text-gray-400 text-[10px]">{{ $sesion->id_sesion }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer acciones --}}
            <div class="px-5 py-3 border-t dark:border-gray-700 flex flex-wrap gap-2">
                <button type="button" @click="modal = 'ingreso'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    + Ingreso
                </button>
                <button type="button" @click="modal = 'retiro'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    − Retiro
                </button>
                <button type="button" @click="modal = 'ajuste'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-yellow-300 dark:border-yellow-700/50 bg-yellow-50 dark:bg-yellow-900/10 text-yellow-700 dark:text-yellow-400 hover:opacity-80 transition">
                    Ajuste contable
                </button>
                <button type="button" @click="modal = 'cierre'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:opacity-80 transition">
                    Forzar cierre
                </button>
            </div>
        </div>

        @else
        {{-- Sesión cerrada --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="text-center py-12 px-6">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Sin sesión activa</h3>
                <p class="text-xs text-gray-400">No hay sesión activa en esta caja.<br>El vendedor debe abrir la sesión desde su panel.</p>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════════
             HISTORIAL DE SESIONES
        ══════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Historial de sesiones</h3>
                @if($historial instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span class="text-xs text-gray-400">{{ $historial->total() }} registros</span>
                @endif
            </div>

            @if(($historial instanceof \Illuminate\Pagination\LengthAwarePaginator ? $historial->isEmpty() : $historial->isEmpty()))
            <div class="px-6 py-10 text-center text-sm text-gray-400">No hay sesiones registradas aún.</div>
            @else

            {{-- Vista PC --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase whitespace-nowrap">Apertura</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase whitespace-nowrap">Cierre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Duración</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fondo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Sistema</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Declarado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Diferencia</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Motivo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($historial as $ses)
                        @php $corteRec = $ses->cortes()->orderByDesc('created_at')->first(); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $ses->abierta_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-400 whitespace-nowrap">{{ $ses->cerrada_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $ses->duracion }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white">${{ number_format($ses->fondo_inicial, 2) }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900 dark:text-white">${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">
                                @if($ses->monto_cierre_declarado !== null) ${{ number_format($ses->monto_cierre_declarado, 2) }}
                                @else <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs font-medium font-semibold {{ (($ses->diferencia ?? 0) < 0) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                @if($ses->diferencia !== null) {{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}
                                @else <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($ses->motivo_cierre === 'admin')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-800/30 dark:text-yellow-400">Admin</span>
                                @elseif($ses->motivo_cierre === 'vendedor')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-800/30 dark:text-blue-400">Vendedor</span>
                                @elseif($ses->motivo_cierre === 'sistema')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Sistema</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
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
                                <a href="{{ route('admin.cajas.corte.pdf', $corteRec->id_corte) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
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
                <div class="px-4 py-4 space-y-3">
                    {{-- Fecha + estado --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $ses->abierta_at?->format('d/m/Y H:i') }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">
                                {{ $ses->cerrada_at ? 'Cerró ' . $ses->cerrada_at->format('d/m/Y H:i') . ' · ' . $ses->duracion : 'Sesión en curso' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($ses->motivo_cierre === 'admin')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-800/30 dark:text-yellow-400">Admin</span>
                            @elseif($ses->motivo_cierre === 'vendedor')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-800/30 dark:text-blue-400">Vendedor</span>
                            @elseif($ses->motivo_cierre === 'sistema')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Sistema</span>
                            @endif
                            @if($ses->estado === 'abierta')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400">Abierta</span>
                            @elseif($ses->estado === 'auto_cerrada')
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Auto</span>
                            @else
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-400">Cerrada</span>
                            @endif
                        </div>
                    </div>

                    {{-- Montos --}}
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-2.5 py-2">
                            <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Sistema</p>
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                                ${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-2.5 py-2">
                            <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Declarado</p>
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">
                                {{ $ses->monto_cierre_declarado !== null ? '$'.number_format($ses->monto_cierre_declarado, 2) : '—' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-2.5 py-2">
                            <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Diferencia</p>
                            <p class="text-xs font-semibold truncate {{ (($ses->diferencia ?? 0) < 0) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                @if($ses->diferencia !== null)
                                    {{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- PDF --}}
                    @if($corteRec)
                    <a href="{{ route('admin.cajas.corte.pdf', $corteRec->id_corte) }}" target="_blank"
                       class="flex items-center justify-center gap-1.5 w-full py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ver PDF
                    </a>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($historial instanceof \Illuminate\Pagination\LengthAwarePaginator && $historial->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $historial->links() }}
            </div>
            @endif

            @endif
        </div>

        @endif {{-- fin @if(!$caja) --}}

        {{-- ════════════════════════════════
             MODAL: CREAR CAJA
        ════════════════════════════════ --}}
        <div x-show="modal === 'crear'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Crear caja — {{ $sucursal->nombre_usuario }}</h3>
                <p class="text-xs text-gray-400 mb-5">Solo se puede tener una caja por sucursal.</p>
                <form method="POST" action="{{ route('admin.cajas.store') }}">
                    @csrf
                    <input type="hidden" name="id_usuario" value="{{ $sucursal->id_usuario }}">
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Nombre de la caja <span class="normal-case font-normal">(opcional)</span>
                        </label>
                        <input type="text" name="nombre" maxlength="80" placeholder="Caja principal"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        <p class="text-xs text-gray-400 mt-1">Si lo dejas vacío se usará "Caja principal".</p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">Crear caja</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             MODAL: INGRESO
        ════════════════════════════════ --}}
        <div x-show="modal === 'ingreso'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar ingreso</h3>
                <p class="text-xs text-gray-400 mb-5">Entrada de dinero en la sesión activa de <strong class="text-gray-700 dark:text-gray-300">{{ $sucursal->nombre_usuario }}</strong>.</p>
                <form method="POST" action="{{ route('admin.cajas.ingreso', $sucursal->id_usuario) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto</label>
                            <input type="number" name="monto" step="0.01" min="0.01" max="999999.99" placeholder="0.00" required
                                   x-init="$watch('modal', v => v === 'ingreso' && $nextTick(() => $el.focus()))"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto</label>
                            <input type="text" name="concepto" maxlength="200" required placeholder="Ej. Transferencia de fondo de reserva..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Referencia <span class="normal-case font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="referencia" maxlength="120" placeholder="Folio, número..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">Registrar ingreso</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             MODAL: RETIRO
        ════════════════════════════════ --}}
        <div x-show="modal === 'retiro'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar retiro</h3>
                <p class="text-xs text-gray-400 mb-5">Salida de efectivo de la sesión activa de <strong class="text-gray-700 dark:text-gray-300">{{ $sucursal->nombre_usuario }}</strong>.</p>
                <form method="POST" action="{{ route('admin.cajas.retiro', $sucursal->id_usuario) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto a retirar</label>
                            <input type="number" name="monto" step="0.01" min="0.01" max="999999.99" placeholder="0.00" required
                                   x-init="$watch('modal', v => v === 'retiro' && $nextTick(() => $el.focus()))"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto</label>
                            <input type="text" name="concepto" maxlength="200" required placeholder="Ej. Depósito a banco, gastos operativos..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Referencia <span class="normal-case font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="referencia" maxlength="120"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:opacity-80 transition">Registrar retiro</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             MODAL: AJUSTE CONTABLE
        ════════════════════════════════ --}}
        <div x-show="modal === 'ajuste'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Ajuste contable</h3>
                <p class="text-xs text-gray-400 mb-5">Corrección de saldo con justificación. Queda registrado como ajuste de administrador.</p>
                <form method="POST" action="{{ route('admin.cajas.ajuste', $sucursal->id_usuario) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Dirección del ajuste</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                @click="ajusteDir = 1"
                                :class="ajusteDir === 1
                                    ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 text-green-700 dark:text-green-400'
                                    : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                class="px-3 py-2 rounded-lg border text-xs font-semibold transition text-center">
                                ↑ Entrada (suma)
                            </button>
                            <button type="button"
                                @click="ajusteDir = 0"
                                :class="ajusteDir === 0
                                    ? 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 text-red-700 dark:text-red-400'
                                    : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                class="px-3 py-2 rounded-lg border text-xs font-semibold transition text-center">
                                ↓ Salida (resta)
                            </button>
                        </div>
                        <input type="hidden" name="es_entrada" :value="ajusteDir">
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto</label>
                            <input type="number" name="monto" step="0.01" min="0.01" max="999999.99" placeholder="0.00" required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto / justificación</label>
                            <input type="text" name="concepto" maxlength="200" required
                                placeholder="Ej. Corrección por error de captura..."
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-semibold bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700/50 text-yellow-700 dark:text-yellow-400 hover:opacity-80 transition">Aplicar ajuste</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             MODAL: CIERRE FORZADO
        ════════════════════════════════ --}}
        <div x-show="modal === 'cierre'" x-cloak
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
             @click.self="modal = ''">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop
                 x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">⚠ Forzar cierre de sesión</h3>
                <p class="text-xs text-gray-400 mb-4">
                    Se cerrará la sesión activa de <strong class="text-gray-700 dark:text-gray-300">{{ $sucursal->nombre_usuario }}</strong>.
                    Se generará un corte con motivo <em>«admin»</em>.
                </p>
                @if($snapshot)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3 space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">Total sistema</span>
                        <span class="text-xs font-medium font-semibold text-green-600 dark:text-green-400">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t dark:border-gray-700 pt-2">
                        <span class="text-xs text-gray-400">Ventas registradas</span>
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $snapshot['ventas_count'] }}</span>
                    </div>
                </div>
                @endif
                <form method="POST" action="{{ route('admin.cajas.cerrar.forzado', $sucursal->id_usuario) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Notas <span class="normal-case font-normal">(opcional)</span>
                        </label>
                        <textarea name="notas" rows="2" maxlength="500" placeholder="Razón del cierre forzado..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="modal = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancelar</button>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Forzar cierre y generar PDF</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ════════════════════════════════
             TOAST WEBSOCKET
        ════════════════════════════════ --}}
        <div id="ws-toast-show"
             class="fixed bottom-5 right-5 z-50 opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200
                        dark:border-gray-700 rounded-xl shadow-xl px-4 py-3 min-w-[280px]">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-0.5">Nueva venta</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" id="ws-toast-show-msg"></p>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <style>[x-cloak]{display:none!important;}</style>

   @if($sesion)
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const elTotal    = document.getElementById('ws-show-total-sistema');
        const elVentas   = document.getElementById('ws-show-ingresos-ventas');
        const elCount    = document.getElementById('ws-show-ventas-count');
        const elToast    = document.getElementById('ws-toast-show');
        const elToastMsg = document.getElementById('ws-toast-show-msg');

        const idSucursal = '{{ $sucursal->id_usuario }}';

        function fmt(n) {
            return '$' + Number(n).toLocaleString('es-MX', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
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
            clearTimeout(window._toastShowTimer);
            window._toastShowTimer = setTimeout(() => {
                elToast.classList.remove('opacity-100');
                elToast.classList.add('opacity-0', 'pointer-events-none');
            }, 5000);
        }

        window.Echo.private(`negocio.{{ auth()->user()->id_negocio }}`)
            .listen('.venta.registrada', (e) => {
                if (e.id_usuario !== idSucursal) return;

                // Usa valores directos del evento — no acumular sobre el DOM
                if (elTotal)  { elTotal.textContent  = fmt(e.total_sistema); pulsar(elTotal); }
                if (elCount)  { elCount.textContent  = e.ventas_count;       pulsar(elCount); }
                if (elVentas) { elVentas.textContent = fmt(e.total_sistema); pulsar(elVentas); }

                mostrarToast(`${fmt(e.total)} · ${e.hora}`);
            });
    });
    </script>
    @endif
    @endpush

</x-app-layout>