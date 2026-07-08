<x-app-layout>
<div class="space-y-0">

<div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100"
    x-data="dashboard()"
    x-init="init()"
>

    {{-- ── TOPBAR (solo escritorio) ── --}}
    <div class="hidden lg:block sticky top-0 z-30 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 px-6 sm:px-8 py-4">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-800 to-gray-600 dark:from-gray-200 dark:to-gray-400 flex items-center justify-center shrink-0 text-white dark:text-gray-900 font-semibold text-[14px] shadow-sm">
                    {{ strtoupper(substr(auth()->user()->negocio->nombre_negocio ?? auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h1 class="text-[16px] font-semibold tracking-tight text-gray-900 dark:text-gray-100 truncate">
                        {{ auth()->user()->negocio->nombre_negocio ?? auth()->user()->name ?? 'Admin' }}
                    </h1>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <p class="text-[11px] text-gray-400 capitalize" x-text="todayLabel"></p>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                        <p class="text-[11px] text-gray-400 tabular-nums" x-text="clock"></p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex items-center gap-3 flex-wrap">
                {{-- Botones de periodo --}}
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1">
                    <template x-for="p in presets.filter(x=>x.key!=='custom')" :key="p.key">
                        <button
                            class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all duration-150"
                            :class="periodo===p.key
                                ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            @click="setPeriod(p.key)"
                            x-text="p.label">
                        </button>
                    </template>
                    <button
                        class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all duration-150"
                        :class="periodo==='custom'
                            ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        @click="setPeriod('custom')">
                        Rango
                    </button>
                </div>

                <!-- NUEVO: Selector de sucursal -->
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1">
                    <button
                        class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all duration-150"
                        :class="!sucursalSeleccionada
                            ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        @click="setSucursal(null)">
                        Todas
                    </button>
                    <template x-for="s in sucursalesDisponibles" :key="'suc-sel-'+s.id_usuario">
                        <button
                            class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all duration-150 max-w-[110px] truncate"
                            :class="sucursalSeleccionada===s.id_usuario
                                ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            @click="setSucursal(s.id_usuario)"
                            x-text="s.nombre">
                        </button>
                    </template>
                </div>

                <div x-show="periodo==='custom'" x-cloak
                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-x-1"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="flex items-center gap-2">
                    <input type="date" x-model="customDesde" @change="fetchStats()"
                           class="text-[11px] px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <span class="text-gray-400">–</span>
                    <input type="date" x-model="customHasta" @change="fetchStats()"
                           class="text-[11px] px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <svg x-show="loading" x-cloak class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <button @click="enlaceOpen=true"
                        class="inline-flex items-center gap-2 text-[11px] font-medium px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 hover:shadow-sm transition-all duration-150">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                          :class="enlaceEstado==='activo' ? 'bg-emerald-500' : enlaceCancelado ? 'bg-red-400' : 'bg-gray-400'"></span>
                    <span x-text="enlaceEstado==='activo' ? enlaceGestor : enlaceCancelado ? 'Desconectado' : 'Enlace'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── TOAST ── --}}
    @if(session('success'))
    <div class="fixed top-5 left-1/2 -translate-x-1/2 z-50"
         x-data="{ show: true }" x-show="show" x-cloak
         x-init="setTimeout(()=>show=false, 3200)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 shadow-xl">
            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-[12px] font-medium text-gray-900 dark:text-white">{{ session('success') }}</p>
            <button @click="show=false" class="text-gray-400 hover:text-gray-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ═══════════════════  MOBILE HOME (estilo banca)  ═══════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="lg:hidden pb-6">

        {{-- Encabezado simple (reemplaza al topbar oculto en mobile) --}}
        <div class="px-5 pt-5 pb-1 flex items-center justify-between">
            <div>
                <p class="text-[20px] font-semibold text-gray-900 dark:text-gray-100 tracking-tight">
                    {{ auth()->user()->negocio->nombre_negocio ?? 'Panel' }}
                </p>
                <p class="text-[11px] text-gray-400 capitalize" x-text="todayLabel"></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-white dark:text-gray-900 font-semibold text-[14px] shrink-0">
                {{ strtoupper(substr(auth()->user()->negocio->nombre_negocio ?? 'A', 0, 1)) }}
            </div>
        </div>

        {{-- selector de periodo integrado en la tarjeta --}}
        <div class="flex gap-1.5 mt-3 overflow-x-auto no-scrollbar">
            <template x-for="p in presets.filter(x=>x.key!=='custom')" :key="'mp-'+p.key">
                <button
                    class="shrink-0 text-[10.5px] font-medium px-3 py-1.5 rounded-full transition-all"
                    :class="periodo===p.key
                        ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900'
                        : 'bg-gray-50 dark:bg-gray-700/50 text-gray-400'"
                    @click="setPeriod(p.key)"
                    x-text="p.label">
                </button>
            </template>
            <button
                class="shrink-0 text-[10.5px] font-medium px-3 py-1.5 rounded-full transition-all"
                :class="periodo==='custom'
                    ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900'
                    : 'bg-gray-50 dark:bg-gray-700/50 text-gray-400'"
                @click="setPeriod('custom')">
                Rango
            </button>
        </div>
        <div x-show="periodo==='custom'" x-cloak class="flex items-center gap-2 mt-2">
            <input type="date" x-model="customDesde" @change="fetchStats()"
                   class="flex-1 text-[11px] px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
            <span class="text-gray-400">–</span>
            <input type="date" x-model="customHasta" @change="fetchStats()"
                   class="flex-1 text-[11px] px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        {{-- HERO — Ingresos totales, estilo Revnu (tarjeta blanca) --}}
        <div class="mx-4 mt-3 rounded-3xl bg-gradient-to-br from-gray-900 to-gray-800 dark:from-gray-100 dark:to-gray-200 p-6 relative overflow-hidden shadow-xl">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-[0.12em]">Ingresos totales</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 shrink-0" x-text="periodoLabel"></p>
            </div>
            <div class="flex items-end gap-2">
                <p class="text-[34px] font-bold tracking-tight leading-none text-white dark:text-gray-900"
                x-text="kpiData.ingresos ? fmt$(kpiData.ingresos) : '$0.00'"></p>
                <span x-show="kpis[1]?.trend"
                    class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full mb-1"
                    :class="kpis[1]?.trend > 0 ? 'text-emerald-300 bg-emerald-900/40 dark:text-emerald-700 dark:bg-emerald-100/60' : 'text-red-300 bg-red-900/40 dark:text-red-700 dark:bg-red-100/60'"
                    x-text="(kpis[1]?.trend > 0 ? '↑' : '↓') + ' ' + Math.abs(kpis[1]?.trend||0).toFixed(1) + '%'"></span>
            </div>

            {{-- sparkline --}}
            <div class="relative h-[64px] mt-4">
                <canvas id="ingresos-chart-mobile"></canvas>
                <div id="ingresos-chart-mobile-empty" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-1.5">
                    <svg class="w-7 h-7 text-gray-200 dark:text-gray-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                    </svg>
                    <p class="text-[10px] text-gray-200 dark:text-gray-800 font-medium">Aún no hay datos</p>
                </div>
            </div>

            {{-- Stats row — sin clientes, solo ventas y ticket --}}
            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-white/10 dark:border-gray-900/10">
                <div class="flex-1 text-center">
                    <p class="text-[15px] font-semibold text-white dark:text-gray-900" x-text="kpis[0]?.value ?? '0'"></p>
                    <p class="text-[9px] text-gray-400 dark:text-gray-600 mt-0.5">Ventas</p>
                </div>
                <div class="w-px h-8 bg-white/10 dark:bg-gray-900/10"></div>
                <div class="flex-1 text-center">
                    <p class="text-[15px] font-semibold text-white dark:text-gray-900" x-text="kpis[2]?.value ?? '$0.00'"></p>
                    <p class="text-[9px] text-gray-400 dark:text-gray-600 mt-0.5">Ticket prom.</p>
                </div>
            </div>
        </div>

        
        {{-- Movimientos — feed de ventas estilo banca ("+monto Sucursal") --}}
        <div class="mx-4 mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4">
                <p class="text-[14px] font-semibold text-gray-900 dark:text-gray-100">Ingresos Recientes</p>
                <a href="{{ url('/admin/movimientos') }}" class="text-[11px] font-medium text-gray-400">Ver todos →</a>
            </div>

            <template x-if="feedVentas.length === 0">
                <div class="flex flex-col items-center justify-center gap-2 py-8">
                    <svg class="w-9 h-9 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                        <path d="M9 11h.01M15 11h.01"/>
                    </svg>
                    <p class="text-gray-100 dark:text-gray-800 text-[11px] font-medium">Aún no hay datos para mostrar</p>
                </div>
            </template>

            <template x-for="v in feedVentas.slice(0,8)" :key="'fv-'+v.id_venta">
                <div :class="{'animate-new-feed': v.animate}"
                     class="feed-item-enter flex items-center gap-3 px-5 py-3.5 border-t border-gray-50 dark:border-gray-700/60">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-emerald-50 dark:bg-emerald-950/30">
                        <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12.5px] font-medium text-gray-800 dark:text-gray-200 truncate" x-text="v.sucursal"></p>
                        <p class="text-[10px] text-gray-400 truncate" x-text="v.hora + ' · ' + v.relativo"></p>
                    </div>
                    <span class="text-[13px] font-semibold text-gray-900 dark:text-gray-100 shrink-0" x-text="fmt$(v.monto)"></span>
                </div>
            </template>
        </div>

        {{-- Aporte por sucursal — barra tipo banca --}}
        <div class="mx-4 mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm"
             @click="openModal('sucursales')">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Sucursales</p>
                <span class="text-[10px] text-gray-400">Ver →</span>
            </div>
            <template x-if="sucursales.length === 0">
                <p class="text-center text-gray-400 text-[11px] py-2">Sin ventas</p>
            </template>
            <template x-for="(v,i) in sucursales.slice(0,3)" :key="'msuc-'+v.id_usuario">
                <div class="flex items-center gap-3 mb-3 last:mb-0">
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[70px] shrink-0 truncate" x-text="v.nombre"></span>
                    <div class="flex-1 h-[6px] bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                             :class="i===0 ? 'bg-gray-900 dark:bg-gray-100' : 'bg-gray-300 dark:bg-gray-600'"
                             :style="{width: sucursales[0]?.ingresos_total > 0 ? Math.round(v.ingresos_total/sucursales[0].ingresos_total*100)+'%' : '0%'}"></div>
                    </div>
                    <span class="text-[11px] font-medium text-gray-700 dark:text-gray-300 min-w-[54px] text-right" x-text="fmt$(v.ingresos_total)"></span>
                </div>
            </template>
        </div>

        {{-- Inventario resumido --}}
        <div class="mx-4 mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm"
             @click="openModal('bicicletas')">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 dark:border-gray-700/60">
                <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Inventario</p>
                <span class="text-[10px] text-gray-400">Ver →</span>
            </div>
            <div class="p-5 flex items-center gap-4">
                <canvas id="donut-chart-mobile" width="56" height="56" class="shrink-0"></canvas>
                <div class="flex-1 space-y-2">
                    @foreach([
                        ['color'=>'#10b981','label'=>'Stock','key'=>'en_stock'],
                        ['color'=>'#4c23bb','label'=>'Vendidas','key'=>'vendidas'],
                        ['color'=>'#ef4444','label'=>'Reparación','key'=>'en_reparacion'],
                    ] as $row)
                    <div class="flex items-center gap-2 text-[10px]">
                        <span class="w-2 h-2 rounded-sm shrink-0" style="background:{{ $row['color'] }}"></span>
                        <span class="text-gray-500 dark:text-gray-400 flex-1">{{ $row['label'] }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $biciStats[$row['key']] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Actividad reciente (movimientos de inventario/bicicletas) --}}
        <div class="mx-4 mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 dark:border-gray-700/60">
                <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Actividad reciente</p>
                <a href="{{ url('/admin/movimientos') }}" class="text-[11px] text-gray-400">Ver todas →</a>
            </div>
            <template x-if="feedMovimientos.length === 0">
                <p class="text-center text-gray-400 text-[11px] py-6">Sin movimientos aún</p>
            </template>
            <template x-for="m in feedMovimientos.slice(0,5)" :key="'mract-'+m.id_movimiento">
                <div :class="{'animate-new-feed': m.animate}"
                     class="feed-item-enter flex items-center gap-3 px-5 py-3.5 border-b border-gray-50 dark:border-gray-700/60 last:border-0"
                     @click="window.location.href = '{{ route('admin.movimientos.index') }}?serie=' + m.num_serie">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 bg-white dark:bg-gray-800 border-2"
                         :class="{
                            'border-emerald-500': m.tipo_movimiento === 'entrada_stock',
                            'border-blue-500': m.tipo_movimiento === 'transferencia_sucursal',
                            'border-amber-500': m.tipo_movimiento === 'venta',
                            'border-purple-500': m.tipo_movimiento === 'mantenimiento',
                            'border-gray-400': m.tipo_movimiento === 'ajuste'
                         }">
                        <div class="w-4 h-4"
                             :class="{
                                'text-emerald-600': m.tipo_movimiento === 'entrada_stock',
                                'text-blue-600': m.tipo_movimiento === 'transferencia_sucursal',
                                'text-amber-600': m.tipo_movimiento === 'venta',
                                'text-purple-600': m.tipo_movimiento === 'mantenimiento',
                                'text-gray-500': m.tipo_movimiento === 'ajuste'
                             }"
                             x-html="iconoTipoMovSVG(m.tipo_movimiento)"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-medium text-gray-800 dark:text-gray-200 truncate" x-text="labelTipoMov(m.tipo_movimiento)"></p>
                        <p class="text-[10px] text-gray-400 truncate" x-text="m.num_serie"></p>
                    </div>
                    <span class="text-[10px] text-gray-400 shrink-0" x-text="m.relativo"></span>
                </div>
            </template>
        </div>

        {{-- Personal --}}
        <div class="mx-4 mt-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50 dark:border-gray-700/60">
                <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Personal</p>
                <span class="text-[10px] text-gray-400">{{ count($personal) }}</span>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700/60">
                @foreach($personal as $i => $p)
                @php $ini = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(explode(' ',$p['nombre']),0,2)))); @endphp
                <div class="flex items-center gap-3 px-5 py-3"
                     @click="openModal('personalItem', {{ $i }})">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-semibold shrink-0 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        {{ $ini }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-medium text-gray-800 dark:text-gray-200 truncate">{{ $p['nombre'] }}</p>
                        <p class="text-[9px] text-gray-400 truncate">{{ $p['sucursal'] }}</p>
                    </div>
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $p['activo'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    {{-- ══════════════ ESCRITORIO ══════════════ --}}

    {{-- ── MAIN GRID (solo escritorio) ── --}}
    <div class="hidden lg:block max-w-screen-2xl mx-auto px-6 py-6 space-y-6">

        {{-- Panel resumen --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 grid grid-cols-1 lg:grid-cols-[1fr_1px_1fr] gap-6">
            <div>
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Ingresos totales</p>
                <p class="text-[34px] font-semibold tracking-tight leading-none text-gray-900 dark:text-gray-100 mb-1"
                   x-text="kpiData.ingresos ? fmt$(kpiData.ingresos) : '$0.00'"></p>
                <p class="text-[11px] text-gray-400" x-text="periodoLabel"></p>

                <div class="grid grid-cols-3 gap-3 mt-5">
                    <template x-if="!loading">
                        <template x-for="(k, ki) in kpis.slice(0,3)" :key="'sumcard-'+k.key">
                            <div class="cursor-pointer group transition-all duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/30 rounded-lg p-2 -m-2"
                                 @click="openModal(k.modal)">
                                <div class="flex items-center justify-between mb-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="kpiDotClass(ki)"></span>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide" x-text="k.label"></p>
                                    </div>
                                    <span x-show="k.trend !== undefined && k.trend !== null && k.trend !== 0"
                                          class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full"
                                          :class="k.trend > 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30' : 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/30'">
                                        <span x-text="k.trend > 0 ? '↑' : '↓'"></span>
                                        <span x-text="Math.abs(k.trend).toFixed(1) + '%'"></span>
                                    </span>
                                </div>
                                <p class="text-[16px] font-semibold text-gray-900 dark:text-gray-100 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors" x-text="k.value"></p>
                            </div>
                        </template>
                    </template>
                </div>
            </div>

            <div class="hidden lg:block bg-gray-100 dark:bg-gray-700"></div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest">Distribución (métodos de pago)</p>
                    <span class="text-[10px] text-gray-400">Total: <span x-text="metodosPago.length"></span></span>
                </div>
                <div class="flex h-3 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mb-4" x-show="metodosPago.length">
                    <template x-for="(m,i) in metodosPago" :key="'segb-'+m.metodo">
                        <div class="h-full transition-all duration-500" :style="{width: pctWidth(m.monto), background: COLORS[i%6]}"></div>
                    </template>
                </div>
                <div class="space-y-2">
                    <template x-for="(m, i) in metodosPago.slice(0, 4)" :key="'lb-'+m.metodo">
                        <div class="flex items-center gap-2 text-[11px]">
                            <span class="w-2 h-2 rounded-full shrink-0" :style="{background: COLORS[i%6]}"></span>
                            <span class="text-gray-500 dark:text-gray-400 capitalize flex-1 truncate" x-text="m.metodo.replace('_',' ')"></span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="fmt$(m.monto)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Gráficas principales: a todo el ancho de la vista --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

            {{-- Ingresos --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col cursor-pointer"
                 @click="openChartModal('ingresos')">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60 shrink-0 flex items-center justify-between">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Ingresos</p>
                    <span class="text-[10px] text-gray-400" x-text="periodoLabel"></span>
                </div>
                <div class="px-4 pb-4 pt-3 h-[200px] relative">
                    <canvas id="ingresos-chart-wrap"></canvas>
                    <div id="ingresos-chart-wrap-empty" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300 text-[11px] font-medium">Aún no hay datos para mostrar</p>
                    </div>
                </div>
            </div>

            {{-- Ventas --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col cursor-pointer"
                 @click="openChartModal('ventas')">
                <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60 shrink-0 flex items-center justify-between">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Ventas</p>
                    <span class="text-[10px] text-gray-400" x-text="periodoLabel"></span>
                </div>
                <div class="px-4 pb-4 pt-3 h-[200px] relative">
                    <canvas id="ventas-chart-wrap"></canvas>
                    <div id="ventas-chart-wrap-empty" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300 text-[11px] font-medium">Aún no hay datos para mostrar</p>
                    </div>
                </div>
            </div>

            {{-- Sucursales --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col cursor-pointer"
                 @click="openChartModal('sucursales')">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60 shrink-0">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Por sucursal</p>
                    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-lg p-0.5" @click.stop>
                        <button class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md transition-all"
                                :class="sucursalMetrica==='ingresos' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'"
                                @click="sucursalMetrica='ingresos'; $nextTick(()=>_renderSucursalesChart())">$</button>
                        <button class="text-[9px] font-semibold px-1.5 py-0.5 rounded-md transition-all"
                                :class="sucursalMetrica==='ventas' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'"
                                @click="sucursalMetrica='ventas'; $nextTick(()=>_renderSucursalesChart())">#</button>
                    </div>
                </div>
                <div class="px-4 pb-4 pt-3 h-[200px] relative">
                    <canvas id="sucursales-chart-wrap"></canvas>
                    <div id="sucursales-chart-wrap-empty" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-2">
                        <svg class="w-9 h-9 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                        </svg>
                        <p class="text-gray-700 dark:text-gray-300 text-[11px] font-medium">Aún no hay datos para mostrar</p>
                    </div>
                </div>
            </div>

        </div>{{-- /gráficas principales --}}

        {{-- Grid principal --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_300px] gap-6">

            {{-- Grid de tarjetas --}}
            <div class="columns-1 sm:columns-2 xl:columns-3 gap-6 [&>*]:mb-6">

                {{-- Clientes --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('clientesTipo')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Clientes</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <p class="text-[26px] font-semibold tracking-tight leading-none text-gray-900 dark:text-gray-100" x-text="kpiData.clientes ?? '—'"></p>
                            <p class="text-[11px] text-gray-400">únicos</p>
                        </div>
                        <template x-for="(c, i) in clientesTipo" :key="c.tipo">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[70px] shrink-0 capitalize" x-text="c.tipo"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-blue-600 dark:bg-blue-400"
                                         :style="{width: kpiData.clientes > 0 ? Math.round(c.cnt/kpiData.clientes*100)+'%' : '0%', opacity: i===0 ? 1 : 0.35}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 min-w-[24px] text-right" x-text="c.cnt"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Modelos top --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('topModelos')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Modelos top</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="!tops.modelo">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin ventas</p>
                        </template>
                        <template x-if="tops.modelo">
                            <div>
                                <p class="text-[18px] font-semibold tracking-tight text-gray-900 dark:text-gray-100" x-text="tops.modelo.nombre"></p>
                                <p class="text-[11px] text-gray-400 mt-1" x-text="tops.modelo.unidades + ' unidades'"></p>
                                <template x-if="tops.config">
                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Config. top</p>
                                        <p class="text-[12px] font-medium text-gray-700 dark:text-gray-300" x-text="tops.config.config"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Accesorios y combos --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('topAccesorios')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Accesorios y combos</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="tops.accesorio">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Accesorio top</p>
                                <p class="text-[15px] font-semibold text-gray-900 dark:text-gray-100" x-text="tops.accesorio.nombre"></p>
                            </div>
                        </template>
                        <template x-if="tops.combo">
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Combo top</p>
                                <p class="text-[12px] font-medium text-gray-700 dark:text-gray-300" x-text="tops.combo.combo"></p>
                            </div>
                        </template>
                        <template x-if="!tops.accesorio && !tops.combo">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin datos</p>
                        </template>
                    </div>
                </div>

                {{-- Cupones --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('cupones')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Cupones</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <p class="text-[22px] font-semibold tracking-tight text-gray-900 dark:text-gray-100" x-text="fmt$(kpiData.cupones_descuento ?? 0)"></p>
                        <p class="text-[11px] text-gray-400 mb-3">en descuentos</p>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Usados</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="kpiData.cupones_usados ?? 0"></span>
                        </div>
                    </div>
                </div>

                {{-- Inventario --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('bicicletas')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Inventario</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5 flex items-center gap-4">
                        <canvas id="donut-chart" width="64" height="64" class="shrink-0"></canvas>
                        <div class="flex-1 space-y-2">
                            @foreach([
                                ['color'=>'#10b981','label'=>'Stock','key'=>'en_stock'],
                                ['color'=>'#4c23bb','label'=>'Vendidas','key'=>'vendidas'],
                                ['color'=>'#ef4444','label'=>'Reparación','key'=>'en_reparacion'],
                            ] as $row)
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="w-2 h-2 rounded-sm shrink-0" style="background:{{ $row['color'] }}"></span>
                                <span class="text-gray-500 dark:text-gray-400 flex-1">{{ $row['label'] }}</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $biciStats[$row['key']] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Aporte sucursal --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('sucursales')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Aporte sucursal</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="sucursales.length === 0">
                            <p class="text-center text-gray-400 text-[11px]">Sin ventas</p>
                        </template>
                        <template x-for="(v, i) in sucursales" :key="v.id_usuario">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[70px] shrink-0 truncate" x-text="v.nombre"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         :class="i === 0 ? 'bg-blue-600 dark:bg-blue-400' : 'bg-gray-400 dark:bg-gray-500'"
                                         :style="{width: sucursales[0].ingresos_total > 0 ? Math.round(v.ingresos_total/sucursales[0].ingresos_total*100)+'%' : '0%'}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 text-right min-w-[54px]" x-text="fmt$(v.ingresos_total)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Stock por vendedor --}}
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">
                    @php $maxStock = collect($stockVendedores)->max('total') ?: 1; @endphp
                    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Stock por vendedor</p>
                    </div>
                    <div class="p-5 space-y-3">
                        @foreach($stockVendedores as $i => $v)
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[90px] shrink-0 truncate">{{ $v['nombre'] }}</span>
                            <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-blue-600 dark:bg-blue-400 transition-all duration-500" style="width:{{ round($v['total']/$maxStock*100) }}%;opacity:{{ 1 - $i*0.15 }};"></div>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 text-right min-w-[20px]">{{ $v['total'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- NUEVO: Rotación de inventario -->
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     @click="openModal('rotacionInventario')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Sin movimiento</p>
                        <span class="text-[10px] text-gray-400">45+ días</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <p class="text-[26px] font-semibold tracking-tight text-gray-900 dark:text-gray-100" x-text="rotacionInventario.total_estancadas"></p>
                        <p class="text-[11px] text-gray-400 mt-1">bicicletas estancadas</p>
                        <template x-if="rotacionInventario.por_modelo?.[0]">
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Modelo más estancado</p>
                                <p class="text-[12px] font-medium text-gray-700 dark:text-gray-300" x-text="rotacionInventario.por_modelo[0].modelo + ' (' + rotacionInventario.por_modelo[0].cantidad + ')'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- NUEVO: Margen / rentabilidad -->
                <div class="break-inside-avoid bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md transition-all duration-200"
                     x-show="!sucursalSeleccionada"
                     @click="openModal('margenSucursales')">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Margen por sucursal</p>
                        <span class="text-[10px] text-gray-400">Ver →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="margenSucursales.length === 0">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin datos</p>
                        </template>
                        <template x-for="(m,i) in margenSucursales.slice(0,3)" :key="'marg-'+m.id_usuario">
                            <div class="flex items-center gap-3 mb-3 last:mb-0">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[70px] shrink-0 truncate" x-text="m.nombre"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-500" :style="{width: margenSucursales[0]?.margen > 0 ? Math.round(m.margen/margenSucursales[0].margen*100)+'%' : '0%'}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 min-w-[54px] text-right" x-text="fmt$(m.margen)"></span>
                            </div>
                        </template>
                        <p class="text-[9px] text-gray-400 mt-2 italic">* Gastos aún no capturados — margen = ingresos</p>
                    </div>
                </div>

            </div>{{-- /grid tarjetas --}}

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Acciones recientes --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Acciones recientes</p>
                        <a href="{{ url('/admin/movimientos') }}" 
                        class="text-[11px] text-gray-400 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        Ver todas →
                        </a>
                    </div>

                    {{-- Feed de movimientos con colores estilo garantías --}}
                    <template x-for="m in feedMovimientos.slice(0,6)" :key="'ract-m-'+m.id_movimiento">
                        <div :class="{'animate-new-feed': m.animate}"
                            class="feed-item-enter flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                            @click="window.location.href = '{{ route('admin.movimientos.index') }}?serie=' + m.num_serie">

                            {{-- Círculo con borde de color según tipo --}}
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-white dark:bg-gray-800 border-2"
                                :class="{
                                    'border-emerald-500': m.tipo_movimiento === 'entrada_stock',
                                    'border-blue-500': m.tipo_movimiento === 'transferencia_sucursal',
                                    'border-amber-500': m.tipo_movimiento === 'venta',
                                    'border-purple-500': m.tipo_movimiento === 'mantenimiento',
                                    'border-gray-400': m.tipo_movimiento === 'ajuste'
                                }">
                                <div class="w-4 h-4 flex items-center justify-center"
                                    :class="{
                                        'text-emerald-600 dark:text-emerald-400': m.tipo_movimiento === 'entrada_stock',
                                        'text-blue-600 dark:text-blue-400': m.tipo_movimiento === 'transferencia_sucursal',
                                        'text-amber-600 dark:text-amber-400': m.tipo_movimiento === 'venta',
                                        'text-purple-600 dark:text-purple-400': m.tipo_movimiento === 'mantenimiento',
                                        'text-gray-500 dark:text-gray-400': m.tipo_movimiento === 'ajuste'
                                    }"
                                    x-html="iconoTipoMovSVG(m.tipo_movimiento)"></div>
                            </div>

                            {{-- Contenido: número de serie + badge de tipo --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-medium text-gray-800 dark:text-gray-200 truncate" x-text="m.num_serie"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[9px] font-medium px-2 py-0.5 rounded-full"
                                        :class="{
                                            'bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-400': m.tipo_movimiento === 'entrada_stock',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': m.tipo_movimiento === 'transferencia_sucursal',
                                            'bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-400': m.tipo_movimiento === 'venta',
                                            'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': m.tipo_movimiento === 'mantenimiento',
                                            'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': m.tipo_movimiento === 'ajuste'
                                        }"
                                        x-text="labelTipoMov(m.tipo_movimiento)">
                                    </span>
                                    <span x-show="m.destino" class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span x-show="m.destino" class="text-[10px] text-gray-400" x-text="m.destino"></span>
                                </div>
                            </div>

                            {{-- Tiempo relativo --}}
                            <span class="text-[10px] text-gray-400 shrink-0" x-text="m.relativo"></span>
                        </div>
                    </template>
                </div>

                {{-- Personal --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Personal</p>
                        <span class="text-[10px] text-gray-400">{{ count($personal) }}</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @foreach($personal as $i => $p)
                        @php $ini = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(explode(' ',$p['nombre']),0,2)))); @endphp
                        <div class="flex items-center gap-3 px-5 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                             @click="openModal('personalItem', {{ $i }})">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-semibold shrink-0 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                {{ $ini }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-medium text-gray-800 dark:text-gray-200 truncate">{{ $p['nombre'] }}</p>
                                <p class="text-[9px] text-gray-400 truncate">{{ $p['sucursal'] }}</p>
                            </div>
                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $p['activo'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-right text-[9px] text-gray-400 pb-2">
                    TTL · bicis 3600s · pedidos 3600s · stats event-driven
                </p>

            </div>{{-- /sidebar --}}

        </div>{{-- /grid principal --}}

    </div>{{-- /MAIN GRID --}}

    {{-- ── MODAL (tamaño dinámico + transición suave para gráficas) ── --}}
    <div x-show="modalOpen" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/20 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
        @click.self="closeModal()" @keydown.escape.window="closeModal()">
        <div x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-[.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 translate-y-2"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-h-[85vh] overflow-y-auto transition-[max-width] duration-300 ease-in-out"
            :class="isChartModal ? modalChartWidthClass : 'max-w-md'">
            <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700 z-10">
                <h3 class="text-[13px] font-semibold text-gray-900 dark:text-gray-100" x-html="modalTitle"></h3>
                <button @click="closeModal()"
                        class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            {{-- Spinner del modal --}}
            <div x-show="modalLoading" class="flex items-center justify-center py-16">
                <svg class="w-6 h-6 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
            <div x-show="!modalLoading" class="p-5 text-[11px] leading-relaxed text-gray-500 dark:text-gray-400" x-html="modalContent"></div>
        </div>
    </div>

</div>

{{-- ── SCRIPTS ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

<style>
/* Animación de entrada para el feed de movimientos en vivo */
@keyframes slideInHighlight {
    0%   { opacity: 0; transform: translateX(-8px); background-color: rgba(34,197,94,0.3); }
    30%  { opacity: 1; transform: translateX(0);    background-color: rgba(34,197,94,0.5); }
    100% { background-color: transparent; }
}
.animate-new-feed {
    animation: slideInHighlight 1.2s ease-in-out;
    border-left: 3px solid #10b981;
}
.feed-item-enter {
    border-left: 3px solid transparent;
    transition: all 0.2s;
}

/* Selector de periodo mobile — sin scrollbar visible */
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
const SSR = {
    biciStats:       @json($biciStats),
    stockVendedores: @json($stockVendedores),
    piezasBajas:     @json($piezasBajas),
    personal:        @json($personal),
    movimientosRecientes: @json($movimientosRecientes ?? []),
    feedVentasRecientes:  @json($feedVentasRecientes ?? []),
    // NUEVO: lista de sucursales para el selector
    sucursalesDisponibles: @json($sucursalesDisponibles ?? []),
    enlace: {
        estado:       '{{ in_array($enlace->estado ?? "", ["pendiente","activo"]) ? $enlace->estado : "" }}',
        cancelado:    {{ ($enlace->estado ?? '') === 'cancelado' ? 'true' : 'false' }},
        gestor:       '{{ addslashes($enlace->usuarioDestino->nombre_usuario ?? "") }}',
        gestorCorreo: '{{ addslashes($enlace->usuarioDestino->correo ?? "") }}',
        token:        '{{ $enlace->token_enlace ?? "" }}',
    },
    statsUrl:   '{{ route("admin.dashboard.stats") }}',
    detalleUrl: '{{ route("admin.dashboard.detalle") }}',
    csrfToken:  '{{ csrf_token() }}',
};

const COLORS = ['#3b82f6','#16a34a','#d97706','#7c3aed','#0ea5e9','#64748b'];

const KPI_ACCENTS = [
    { card: 'bg-blue-50 dark:bg-blue-950/30 border-blue-100 dark:border-blue-900/50 hover:border-blue-200', circle: 'bg-blue-400', label: 'text-blue-500 dark:text-blue-400', value: 'text-blue-900 dark:text-blue-100', sub: 'text-blue-400 dark:text-blue-500' },
    { card: 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-900/50 hover:border-emerald-200', circle: 'bg-emerald-400', label: 'text-emerald-600 dark:text-emerald-400', value: 'text-emerald-900 dark:text-emerald-100', sub: 'text-emerald-400 dark:text-emerald-500' },
    { card: 'bg-violet-50 dark:bg-violet-950/30 border-violet-100 dark:border-violet-900/50 hover:border-violet-200', circle: 'bg-violet-400', label: 'text-violet-600 dark:text-violet-400', value: 'text-violet-900 dark:text-violet-100', sub: 'text-violet-400 dark:text-violet-500' },
    { card: 'bg-amber-50 dark:bg-amber-950/30 border-amber-100 dark:border-amber-900/50 hover:border-amber-200', circle: 'bg-amber-400', label: 'text-amber-600 dark:text-amber-400', value: 'text-amber-900 dark:text-amber-100', sub: 'text-amber-400 dark:text-amber-500' },
];

const KPI_CHIPS = [
    { chip: 'bg-blue-50 dark:bg-blue-950/40',    dot: 'bg-blue-500' },
    { chip: 'bg-emerald-50 dark:bg-emerald-950/40', dot: 'bg-emerald-500' },
    { chip: 'bg-violet-50 dark:bg-violet-950/40',   dot: 'bg-violet-500' },
    { chip: 'bg-amber-50 dark:bg-amber-950/40',     dot: 'bg-amber-500' },
];

const isDark  = () => window.matchMedia('(prefers-color-scheme: dark)').matches;
const fmt$    = n  => '$' + Number(n).toLocaleString('es-MX', { maximumFractionDigits: 0 });
const pct     = (a, b) => b > 0 ? Math.round(a / b * 100) : 0;

function timeAgo(dateStr) {
    if (!dateStr) return '';
    const now = new Date();
    const past = new Date(dateStr);
    const diff = Math.floor((now - past) / 1000);
    if (diff < 60) return 'hace ' + diff + 's';
    if (diff < 3600) return 'hace ' + Math.floor(diff/60) + 'm';
    if (diff < 86400) return 'hace ' + Math.floor(diff/3600) + 'h';
    if (diff < 604800) return 'hace ' + Math.floor(diff/86400) + 'd';
    return past.toLocaleDateString('es-MX');
}

function calcYTicks(maxVal, targetTicks = 4) {
    if (!maxVal || maxVal <= 0) return [0,1,2,3,4];
    const raw  = maxVal / targetTicks;
    const exp  = Math.floor(Math.log10(raw));
    const base = Math.pow(10, exp);
    let step   = base;
    for (const m of [1,2,5,10]) { step = m * base; if (step >= raw) break; }
    const top   = Math.ceil((maxVal + step * 0.01) / step) * step;
    const count = Math.round(top / step);
    return Array.from({ length: count + 1 }, (_, i) => +(i * step).toPrecision(12));
}

Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, sans-serif";

function dashboard() {
    return {
        // ── Estado ──
        clock: '--:--',
        todayLabel: new Date().toLocaleDateString('es-MX', { weekday:'long', day:'numeric', month:'short' }),
        periodo: 'today', customDesde: '', customHasta: '',
        presets: [{key:'today',label:'Hoy'},{key:'7d',label:'7 días'},{key:'30d',label:'30 días'},{key:'month',label:'Mes'},{key:'custom',label:'Rango'}],
        get periodoLabel() { return {today:'Hoy','7d':'Últimos 7 días','30d':'Últimos 30 días',month:'Este mes',custom:'Personalizado'}[this.periodo]||''; },
        loading: true,
        diasPeriodo: 1,

        kpiData: {}, tops: {}, graficaDias: [], graficaAnterior: [],
        graficaSucursales: [], sucursalMetrica: 'ingresos',
        sucursales: [], pedidos: [], ots: [], metodosPago: [],
        clientesTipo: [], horasPico: [], ventasPersonal: [],

        // ── NUEVAS PROPIEDADES ──
        sucursalSeleccionada: null, // null = "Todas"
        sucursalesDisponibles: SSR.sucursalesDisponibles,
        rotacionInventario: { total_estancadas: 0, dias_umbral: 45, bicicletas: [], por_modelo: [] },
        margenSucursales: [],

        // ── Feed en vivo de movimientos de bicicletas ──
        feedMovimientos: [],
        _canalMovimientos: null,

        // ── Feed en vivo de ventas (estilo banca) ──
        feedVentas: [],
        _canalVentas: null,

        // ── Charts.js instances (gráficas principales) ──
        _charts: {},
        _destroyChart(id) {
            if (this._charts[id]) { this._charts[id].destroy(); delete this._charts[id]; }
        },
        _toggleEmpty(containerId, show) {
            const empty  = document.getElementById(containerId + '-empty');
            const canvas = document.getElementById(containerId);
            if (empty)  empty.classList.toggle('hidden', !show);
            if (canvas) canvas.classList.toggle('hidden', show);
        },

        // Modal
        modalOpen: false, modalLoading: false,
        modalTitle: '', modalContent: '',
        isChartModal: false,
        modalChartWidthClass: 'max-w-3xl',

        _modalPeriodoParams() {
            if (this.periodo === 'custom' && this.customDesde && this.customHasta)
                return `&desde=${this.customDesde}&hasta=${this.customHasta}`;
            return `&periodo=${this.periodo}`;
        },

        enlaceEstado: SSR.enlace.estado, enlaceCancelado: SSR.enlace.cancelado,
        enlaceGestor: SSR.enlace.gestor, enlaceOpen: false,

        kpiCardClass(i)   { return KPI_ACCENTS[i]?.card   || 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700'; },
        kpiCircleClass(i) { return KPI_ACCENTS[i]?.circle || 'bg-gray-300'; },
        kpiLabelClass(i)  { return KPI_ACCENTS[i]?.label  || 'text-gray-400'; },
        kpiValueClass(i)  { return KPI_ACCENTS[i]?.value  || 'text-gray-900 dark:text-gray-100'; },
        kpiSubClass(i)    { return KPI_ACCENTS[i]?.sub    || 'text-gray-400'; },

        kpiChipClass(i) { return KPI_CHIPS[i]?.chip || 'bg-gray-100 dark:bg-gray-700'; },
        kpiDotClass(i)  { return KPI_CHIPS[i]?.dot  || 'bg-gray-400'; },
        pctWidth(monto) {
            const t = this.metodosPago.reduce((s,m)=>s+m.monto,0);
            return t > 0 ? Math.round(monto/t*100)+'%' : '0%';
        },

        get kpis() {
            const d = this.kpiData;
            if (d.ventas === undefined) return [];
            const calcTrend = (actual, anterior) => {
                if (!anterior || anterior === 0) return 0;
                return ((actual - anterior) / anterior) * 100;
            };
            const trendVentas    = calcTrend(d.ventas, d.ventas_anterior);
            const trendIngresos  = calcTrend(d.ingresos, d.ingresos_anterior);
            const trendTicket    = calcTrend(d.ticket, d.ticket_anterior);
            const trendClientes  = calcTrend(d.clientes, d.clientes_anterior);

            return [
                { key:'ventas',   label:'Ventas',      value: d.ventas,          sub: 'transacciones',                                              modal:'ventasKPI',    trend: trendVentas },
                { key:'ingresos', label:'Ingresos',     value: fmt$(d.ingresos),  sub: 'en el periodo',                                              modal:'ingresosKPI',  trend: trendIngresos },
                { key:'ticket',   label:'Ticket prom.', value: fmt$(d.ticket),    sub: 'por venta',                                                  modal:'ticketKPI',    trend: trendTicket },
                { key:'clientes', label:'Clientes',     value: d.clientes,        sub: (d.clientes_nuevos??0)+' nuevos · '+(d.clientes_rec??0)+' rec.', modal:'clientesTipo', trend: trendClientes },
                { key:'ots',      label:'OTs activas',  value: d.ots_activas,     sub: 'en taller ahora',                                            modal:'otsList',      trend: 0 },
                { key:'stock',    label:'En stock',     value: SSR.biciStats.en_stock, sub: 'bicicletas',                                            modal:'bicicletas',   trend: 0 },
                { key:'cupones',  label:'Cupones',      value: fmt$(d.cupones_descuento??0), sub: (d.cupones_usados??0)+' usos',                      modal:'cupones',      trend: 0 },
            ];
        },

        // ── NUEVO MÉTODO: setSucursal ──
        setSucursal(idUsuario) {
            this.sucursalSeleccionada = idUsuario;
            this.fetchStats();
        },

        // ── Init ──
        init() {
            this._clockTick();
            this.fetchStats();
            this.$nextTick(() => { this._initDonut(); this._initDonutMobile(); });
            this._listenEcho();
            this._watchDarkMode();

            // Precargar feed con movimientos ya existentes en la BD
            this.feedMovimientos = SSR.movimientosRecientes.map(item => ({
                ...item,
                id_movimiento: item.id ?? crypto.randomUUID(),
                animate: false,
                relativo: this.formatRelativo(item.fecha_movimiento),
            }));

            this._listenMovimientos();

            // Precargar feed de ventas (estilo banca)
            this.feedVentas = SSR.feedVentasRecientes.map(v => ({
                ...v,
                animate: false,
                relativo: this.formatRelativo(v.fecha),
            }));
            this._listenVentas();

            setInterval(() => {
                this.feedMovimientos = this.feedMovimientos.map(m => ({
                    ...m,
                    relativo: this.formatRelativo(m.fecha_movimiento)
                }));
                this.feedVentas = this.feedVentas.map(v => ({
                    ...v,
                    relativo: this.formatRelativo(v.fecha)
                }));
            }, 10000);
        },

        _clockTick() {
            this.clock = new Date().toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit' });
            setTimeout(() => this._clockTick(), 30000);
        },

        _watchDarkMode() {
            const target = document.documentElement;
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((m) => {
                    if (m.attributeName === 'class') {
                        // Reconstruir todas las gráficas con los colores correctos
                        this._renderIngresosChart();
                        this._renderVentasChart();
                        this._renderSucursalesChart();
                    }
                });
            });
            observer.observe(target, { attributes: true, attributeFilter: ['class'] });
        },

        setPeriod(p) { this.periodo = p; if (p !== 'custom') this.fetchStats(); },

        // ── Fetch stats (MODIFICADO) ──
        async fetchStats() {
            this.loading = true;
            try {
                let url = SSR.statsUrl + '?periodo=' + this.periodo;
                if (this.periodo === 'custom' && this.customDesde && this.customHasta)
                    url = SSR.statsUrl + '?desde=' + this.customDesde + '&hasta=' + this.customHasta;

                // Añadir filtro de sucursal si está seleccionada
                if (this.sucursalSeleccionada) {
                    url += '&id_sucursal=' + this.sucursalSeleccionada;
                }

                const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': SSR.csrfToken } });
                const data = await res.json();

                this.kpiData           = data.kpi               || {};
                this.tops              = data.tops              || {};
                this.graficaDias       = data.grafica           || [];
                this.graficaAnterior   = data.grafica_anterior  || [];
                this.graficaSucursales = data.grafica_sucursales || [];
                this.sucursales        = data.sucursales        || [];
                this.pedidos           = data.pedidos?.recientes || [];
                this.ots               = data.ots               || [];
                this.metodosPago       = data.metodos_pago      || [];
                this.clientesTipo      = data.clientes_tipo     || [];
                this.horasPico         = data.horas_pico        || [];
                this.ventasPersonal    = data.ventas_personal   || [];
                this.diasPeriodo       = data.periodo?.dias     || 1;

                // Nuevos campos
                this.rotacionInventario = data.rotacion_inventario || { total_estancadas: 0, dias_umbral: 45, bicicletas: [], por_modelo: [] };
                this.margenSucursales   = data.margen_sucursales || [];

                this.pedidos = this.pedidos.map(p => ({
                    ...p,
                    fecha_relativa: timeAgo(p.fecha)
                }));

                this.$nextTick(() => {
                    setTimeout(() => {
                        this._renderIngresosChart();
                        this._renderVentasChart();
                        this._renderSucursalesChart();
                    }, 50);
                });
            } catch(e) { console.error(e); }
            finally    { this.loading = false; }
        },

        async fetchDetalle(tipo, extra = {}) {
            this.modalLoading = true;
            this.modalContent = '';
            try {
                let url = SSR.detalleUrl + '?tipo=' + tipo + this._modalPeriodoParams();
                for (const [k, v] of Object.entries(extra)) url += `&${k}=${v}`;
                const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': SSR.csrfToken } });
                const json = await res.json();
                return json.data;
            } catch(e) { console.error(e); return null; }
            finally    { this.modalLoading = false; }
        },

        // ── Sparkline con Chart.js (hero mobile) ──
        _buildSparkline(containerId, data) {
            const canvas = document.getElementById(containerId);
            if (!canvas) return;
            this._destroyChart(containerId);

            const hasData = data && data.some(v => v > 0);
            this._toggleEmpty(containerId, !hasData);
            if (!hasData) return;

        
        const isDarkMode = isDark();

            this._charts[containerId] = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: data.map((_, i) => i),
                    datasets: [{
                        data,
                        borderColor: isDarkMode ? '#000000' : '#ffffff',
                        backgroundColor: isDarkMode ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.20)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: isDarkMode ? '#000000' : '#ffffff',
                        pointHoverBorderColor: isDarkMode ? '#ffffff' : '#000000',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: { title: () => '', label: (ctx) => fmt$(ctx.parsed.y) }
                        }
                    },
                    scales: { x: { display: false }, y: { display: false } },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        },

   
        _renderEmptyState(container) {
            container.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:8px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:${isDark()?'#4b5563':'#d1d5db'};">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M9 15l-1.5 1.5M15 15l1.5 1.5M7.5 16.5L9 15M16.5 16.5L15 15"/>
                        <path d="M9 11h.01M15 11h.01"/>
                    </svg>
                    <p style="font-size:11px;color:${isDark()?'#ffffff':'#000000'};font-weight:500;">Aún no hay datos para mostrar</p>
                </div>`;
        },

        _buildFinancialSVG({ containerId, data, anterior, labels, isMoney, yTicks, multiSeries, chartType = 'bar' }) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const isMulti0 = Array.isArray(multiSeries) && multiSeries.length > 0;
            const hasData  = isMulti0
                ? multiSeries.some(s => (s.data || []).some(v => v > 0))
                : (data || []).some(v => v > 0);

            if (!hasData) {
                container.innerHTML = '';
                this._renderEmptyState(container);
                return;
            }

            container.innerHTML = '';

            const d0    = isDark();
            const W     = 500, H = 180, PAD_B = 25, PAD_T = 15;
            const plotH = H - PAD_B - PAD_T;

            const isMulti = Array.isArray(multiSeries) && multiSeries.length > 0;
            const allData = isMulti ? multiSeries.map(s => s.data) : [data];
            const allMax  = Math.max(...allData.flat(), 1);
            const ticks   = yTicks || calcYTicks(allMax);
            const maxVal  = ticks[ticks.length - 1];
            const n       = (isMulti ? multiSeries[0].data : data).length;
            if (n < 2) return;

            const xOf = i => (i / (n - 1)) * W;
            const barWidth = Math.min(24, W / n * 0.6);

            const yAxisDiv = document.createElement('div');
            yAxisDiv.style.cssText = 'display:flex;flex-direction:column-reverse;justify-content:space-between;padding-bottom:25px;margin-right:8px;min-width:32px;flex-shrink:0;';
            ticks.forEach(t => {
                const s = document.createElement('span');
                s.textContent = isMoney ? (t >= 1000 ? Math.round(t/1000)+'k' : t) : t;
                s.style.cssText = `font-size:10px;color:${d0?'rgba(107,114,128,0.9)':'rgba(156,163,175,0.9)'};line-height:1;text-align:right;display:block;font-family:'Figtree',ui-sans-serif;`;
                yAxisDiv.appendChild(s);
            });

            const flex    = document.createElement('div');
            flex.style.cssText = 'display:flex;position:relative;height:100%;';
            const svgWrap = document.createElement('div');
            svgWrap.style.cssText = 'flex:1;position:relative;width:100%;height:100%;';

            const NS = 'http://www.w3.org/2000/svg';
            const el = (tag, attrs) => {
                const e = document.createElementNS(NS, tag);
                for (const [k,v] of Object.entries(attrs)) e.setAttribute(k, v);
                return e;
            };

            const svg = el('svg', { viewBox:`0 0 ${W} ${H}`, preserveAspectRatio:'none', style:'width:100%;height:100%;display:block;overflow:visible;' });

            ticks.slice(1).forEach(t => {
                svg.appendChild(el('line', { x1:'0', y1:PAD_T + plotH - Math.min(t/maxVal,1)*plotH,
                                            x2:W, y2:PAD_T + plotH - Math.min(t/maxVal,1)*plotH,
                                            stroke: d0?'rgba(255,255,255,0.06)':'rgba(0,0,0,0.04)',
                                            'stroke-width':'0.5' }));
            });

            const seriesList = isMulti ? multiSeries : [{ data, label:'', color: d0?'#94a3b8':'#1f2937', dashed:false }];
            const barData = [];

            const defs = el('defs', {});
            const glowId = containerId + '-glow';
            const filter = el('filter', { id:glowId, x:'-10%', y:'-10%', width:'120%', height:'120%' });
            filter.appendChild(el('feDropShadow', { dx:'0', dy:'2', stdDeviation:'3', 'flood-color': d0?'#94a3b8':'#0f172a', 'flood-opacity':'0.3' }));
            defs.appendChild(filter);

            const gradId = containerId + '-grad';
            const grad = el('linearGradient', { id: gradId, x1:'0', y1:'0', x2:'0', y2:'1' });
            grad.appendChild(el('stop', { offset:'0%',   'stop-color': d0 ? '#a3e635' : '#111827', 'stop-opacity': '0.22' }));
            grad.appendChild(el('stop', { offset:'100%', 'stop-color': d0 ? '#a3e635' : '#111827', 'stop-opacity': '0' }));
            defs.appendChild(grad);
            svg.appendChild(defs);

            // Si es multiSeries y chartType === 'line' -> líneas
            if (isMulti && chartType === 'line') {
                seriesList.forEach((series, si) => {
                    const color = series.color || COLORS[si % COLORS.length];
                    const pts = series.data;
                    const linePts = pts.map((v,i) => `${xOf(i)},${PAD_T + plotH - Math.min(v/maxVal,1)*plotH}`);

                    const areaPath = `M${linePts[0]} L${linePts.slice(1).join(' L')} L${xOf(n-1)},${PAD_T+plotH} L${xOf(0)},${PAD_T+plotH} Z`;
                    svg.appendChild(el('path', { d: areaPath, fill: color + '18', stroke: 'none' }));

                    svg.appendChild(el('path', {
                        d: `M${linePts.join(' L')}`,
                        fill: 'none',
                        stroke: color,
                        'stroke-width': '2',
                        'stroke-linejoin': 'round',
                        'stroke-linecap': 'round'
                    }));

                    pts.forEach((v, i) => {
                        const cx = xOf(i);
                        const cy = PAD_T + plotH - Math.min(v/maxVal,1)*plotH;
                        svg.appendChild(el('circle', {
                            cx, cy, r: '2.5',
                            fill: color,
                            stroke: d0 ? '#1f2937' : '#ffffff',
                            'stroke-width': '0.5'
                        }));
                    });

                    pts.forEach((val, i) => {
                        if (!barData[i]) barData[i] = { label: labels[i], values: [] };
                        barData[i].values.push({ label: series.label, val, color });
                    });
                });
            } else {
                // Comportamiento original (barras + área para serie única)
                if (!isMulti) {
                    const pts = seriesList[0].data;
                    const linePts = pts.map((v,i) => `${xOf(i)},${PAD_T + plotH - Math.min(v/maxVal,1)*plotH}`);
                    const areaPath = `M0,${PAD_T+plotH} L${linePts.join(' L')} L${W},${PAD_T+plotH} Z`;
                    svg.appendChild(el('path', { d: areaPath, fill:`url(#${gradId})`, stroke:'none' }));
                    svg.appendChild(el('path', {
                        d: `M${linePts.join(' L')}`, fill:'none',
                        stroke: d0 ? '#e2e8f0' : '#111827', 'stroke-width':'2',
                        'stroke-linejoin':'round', 'stroke-linecap':'round'
                    }));
                }

                seriesList.forEach((series, si) => {
                    const color = series.color || COLORS[si % COLORS.length];
                    const pts = series.data;
                    const isMain = !isMulti && si === 0;

                    pts.forEach((val, i) => {
                        const x = xOf(i) - barWidth/2 + (isMulti ? (si - (seriesList.length-1)/2) * (barWidth * 0.4) : 0);
                        const y = PAD_T + plotH - Math.min(val / maxVal, 1) * plotH;
                        const height = Math.min(val / maxVal, 1) * plotH;

                        const rect = el('rect', {
                            x, y,
                            width: barWidth,
                            height: height,
                            rx: 2,
                            ry: 2,
                            fill: isMain ? (d0 ? '#e2e8f0' : '#1e293b') : color,
                            opacity: isMain ? 0.16 : 0.75,
                        });
                        svg.appendChild(rect);
                        if (!barData[i]) barData[i] = { label: labels[i], values: [] };
                        barData[i].values.push({ label: series.label, val, color });
                    });
                });
            }

            // Etiquetas X
            const stepX = Math.max(1, Math.floor(n / 6));
            labels.forEach((lbl, i) => {
                if (i % stepX !== 0 && i !== n-1) return;
                const t = el('text', { x:xOf(i), y:H-3,
                    'text-anchor': i===0?'start':i===n-1?'end':'middle',
                    'font-size':'9.5', fill: d0?'rgba(107,114,128,0.9)':'rgba(107,114,128,0.8)',
                    'font-family':"'Figtree',ui-sans-serif" });
                t.textContent = lbl;
                svg.appendChild(t);
            });

            // Tooltip
            const tip = document.createElement('div');
            const tipBg  = d0?'rgba(17,24,39,0.98)':'rgba(255,255,255,0.99)';
            const tipBdr = d0?'rgba(55,65,81,0.9)':'rgba(209,213,219,0.9)';
            tip.style.cssText = `position:absolute;pointer-events:none;z-index:20;background:${tipBg};border:0.5px solid ${tipBdr};border-radius:12px;padding:9px 13px;opacity:0;transition:opacity 0.08s ease;white-space:nowrap;font-family:'Figtree',ui-sans-serif;min-width:115px;box-shadow:0 4px 20px ${d0?'rgba(0,0,0,0.5)':'rgba(0,0,0,0.08)'};`;
            if (isMulti) {
                tip.innerHTML = `<div style="font-size:10px;color:${d0?'#9ca3af':'#6b7280'};margin-bottom:6px;font-weight:500;" class="tip-d"></div><div class="tip-rows"></div>`;
            } else {
                tip.innerHTML = `<div style="font-size:10px;color:${d0?'#9ca3af':'#6b7280'};margin-bottom:5px;font-weight:500;" class="tip-d"></div><div style="display:flex;align-items:center;gap:8px;"><span style="font-size:17px;font-weight:600;color:${d0?'#f3f4f6':'#111827'};letter-spacing:-0.4px;line-height:1;" class="tip-v"></span><span style="font-size:11px;font-weight:600;border-radius:6px;padding:2px 7px;line-height:1.5;display:none;" class="tip-p"></span></div><div style="font-size:10px;color:${d0?'#6b7280':'#9ca3af'};margin-top:4px;" class="tip-ant"></div>`;
            }
            svgWrap.appendChild(svg);
            svgWrap.appendChild(tip);

            const tdEl = tip.querySelector('.tip-d');
            const tvEl = tip.querySelector('.tip-v');
            const tpEl = tip.querySelector('.tip-p');
            const taEl = tip.querySelector('.tip-ant');
            const trRows = tip.querySelector('.tip-rows');

            let highlightIndex = -1;

            function updateTip(clientX) {
                const rect = svg.getBoundingClientRect();
                const mx   = (clientX - rect.left) / rect.width * W;
                const ci   = Math.max(0, Math.min(n-1, Math.round(mx/W*(n-1))));
                if (ci === highlightIndex) return;
                highlightIndex = ci;

                tdEl.textContent = labels[ci];

                if (isMulti) {
                    const values = barData[ci]?.values || [];
                    trRows.innerHTML = values.map(v => `
                        <div style="display:flex;align-items:center;gap:8px;font-size:11px;margin-top:3px;">
                            <span style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:${v.color};"></span>
                            <span style="color:${d0?'#9ca3af':'#6b7280'};flex:1;">${v.label}</span>
                            <span style="font-weight:600;color:${d0?'#f3f4f6':'#111827'};">${isMoney ? fmt$(Math.round(v.val)) : Math.round(v.val)+' ventas'}</span>
                        </div>
                    `).join('');
                } else {
                    const val  = data[ci];
                    const prev = anterior && anterior.some(v=>v>0) ? (anterior[ci]??null) : (ci>0?data[ci-1]:null);
                    const prevLabel = anterior && anterior.some(v=>v>0) ? 'periodo ant.' : 'día ant.';
                    let deltaStr = null, deltaForzado = null;
                    if (prev !== null) {
                        if (prev===0 && val===0) {}
                        else if (prev===0 && val>0)   { deltaForzado = {text:'▲ nuevo',isPos:true}; }
                        else if (prev>0 && val===0)  { deltaStr = '-100.0'; }
                        else                         { deltaStr = ((val-prev)/prev*100).toFixed(1); }
                    }
                    tvEl.textContent = isMoney ? fmt$(Math.round(val)) : Math.round(val)+' ventas';
                    if (deltaForzado) {
                        tpEl.textContent = deltaForzado.text; tpEl.style.display='';
                        tpEl.style.color='#16a34a'; tpEl.style.background=d0?'rgba(22,163,74,0.15)':'#dcfce7';
                    } else if (deltaStr!==null) {
                        const isPos = parseFloat(deltaStr)>=0;
                        tpEl.textContent=(isPos?'+':'')+deltaStr+'%'; tpEl.style.display='';
                        tpEl.style.color=isPos?'#16a34a':'#dc2626';
                        tpEl.style.background=isPos?(d0?'rgba(22,163,74,0.15)':'#dcfce7'):(d0?'rgba(220,38,38,0.15)':'#fee2e2');
                    } else { tpEl.style.display='none'; }
                    taEl.textContent = (prev!==null&&prev>0)?'vs '+(isMoney?fmt$(Math.round(prev)):prev+' ventas')+' '+prevLabel:'';
                }

                const iw   = svgWrap.clientWidth;
                const tx   = (xOf(ci)/W)*iw;
                const ty   = (PAD_T + plotH - Math.min((barData[ci]?.values[0]?.val || 0) / maxVal, 1) * plotH) / H * svgWrap.clientHeight;
                const tipW = tip.offsetWidth||128;
                const tipH = tip.offsetHeight||60;
                tip.style.left    = Math.min(tx+14, iw-tipW-6)+'px';
                tip.style.top     = Math.max(Math.min(ty-tipH/2, svgWrap.clientHeight-tipH-4),4)+'px';
                tip.style.opacity = '1';
            }

            function clearTip() {
                highlightIndex = -1;
                tip.style.opacity = '0';
            }

            svg.addEventListener('mousemove', e => updateTip(e.clientX));
            svg.addEventListener('mouseleave', clearTip);
            svg.addEventListener('touchmove', e => { e.preventDefault(); updateTip(e.touches[0].clientX); }, { passive:false });
            svg.addEventListener('touchend', clearTip);

            flex.appendChild(yAxisDiv);
            flex.appendChild(svgWrap);
            container.appendChild(flex);
        },

        // ── Gráficas principales (dashboard) con Chart.js sobre <canvas> ──
        _renderIngresosChart() {
            const labels = this.graficaDias.map(d => d.label);
            const data   = this.graficaDias.map(d => d.ingresos);
            if (!labels.length) return;

            const containerId = 'ingresos-chart-wrap';
            const canvas = document.getElementById(containerId);
            if (canvas) {
                this._destroyChart(containerId);
                const hasData = data.some(v => v > 0);
                this._toggleEmpty(containerId, !hasData);
                if (hasData) {
                    const d0 = isDark();
                    const tick = '#9ca3af';
                    const grid = d0 ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
                    this._charts[containerId] = new Chart(canvas, {
                        type: 'line',
                        data: { labels, datasets: [{
                            data,
                            borderColor: d0 ? '#3987e5' : '#2a78d6',
                            backgroundColor: d0 ? 'rgba(57,135,229,0.12)' : 'rgba(42,120,214,0.10)',
                            fill: true, tension: 0.35, borderWidth: 2,
                            pointRadius: 0, pointHoverRadius: 5,
                            pointHoverBackgroundColor: d0 ? '#3987e5' : '#2a78d6',
                            pointHoverBorderColor: d0 ? '#1f2937' : '#ffffff',
                            pointHoverBorderWidth: 2,
                        }]},
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: {
                                callbacks: { label: (ctx) => fmt$(ctx.parsed.y) }
                            }},
                            scales: {
                                x: { grid: { display: false }, ticks: { color: tick, font: { size: 10 } } },
                                y: { grid: { color: grid }, border: { display: false },
                                    ticks: { color: tick, font: { size: 10 }, maxTicksLimit: 4,
                                            callback: v => v >= 1000 ? Math.round(v/1000)+'k' : v } }
                            }
                        }
                    });
                }
            }

            if (document.getElementById('ingresos-chart-mobile')) {
                this._buildSparkline('ingresos-chart-mobile', data);
            }
        },

        _renderVentasChart() {
            const labels = this.graficaDias.map(d => d.label);
            const data   = this.graficaDias.map(d => d.ventas);
            if (!labels.length) return;

            const containerId = 'ventas-chart-wrap';
            const canvas = document.getElementById(containerId);
            if (!canvas) return;

            this._destroyChart(containerId);
            const hasData = data.some(v => v > 0);
            this._toggleEmpty(containerId, !hasData);
            if (!hasData) return;

            const d0   = isDark();
            const tick = '#9ca3af';
            const grid = d0 ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';

            this._charts[containerId] = new Chart(canvas, {
                type: 'bar',
                data: { labels, datasets: [{
                    data,
                    backgroundColor: d0 ? '#199e70' : '#1baf7a',
                    borderRadius: 4,
                    maxBarThickness: 22,
                }]},
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: {
                        callbacks: { label: (ctx) => ctx.parsed.y + ' ventas' }
                    }},
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tick, font: { size: 10 } } },
                        y: { grid: { color: grid }, border: { display: false },
                            ticks: { color: tick, font: { size: 10 }, maxTicksLimit: 4 } }
                    }
                }
            });
        },

        _renderSucursalesChart() {
            const containerId = 'sucursales-chart-wrap';
            const canvas = document.getElementById(containerId);
            if (!canvas) return;

            this._destroyChart(containerId);

            const hasData = this.graficaSucursales.length > 0;
            this._toggleEmpty(containerId, !hasData);
            if (!hasData) return;

            const labels  = this.graficaDias.map(d => d.label);
            const metrica = this.sucursalMetrica;
            const d0      = isDark();
            const tick    = '#9ca3af';
            const grid    = d0 ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';

            const datasets = this.graficaSucursales.map((s, i) => ({
                label: s.nombre,
                data: s[metrica] || [],
                borderColor: COLORS[i % COLORS.length],
                backgroundColor: 'transparent',
                borderDash: i === 0 ? [] : [4, 3],
                tension: 0.3, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4,
            }));

            this._charts[containerId] = new Chart(canvas, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: {
                        callbacks: { label: (ctx) => ctx.dataset.label + ': ' +
                            (metrica === 'ingresos' ? fmt$(ctx.parsed.y) : ctx.parsed.y + ' ventas') }
                    }},
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tick, font: { size: 10 } } },
                        y: { grid: { color: grid }, border: { display: false },
                            ticks: { color: tick, font: { size: 10 }, maxTicksLimit: 4,
                                    callback: v => metrica === 'ingresos' && v >= 1000 ? Math.round(v/1000)+'k' : v } }
                    }
                }
            });
        },

        _donutInited: false,
        _initDonut() {
            if (this._donutInited) return;
            const c = document.getElementById('donut-chart'); if (!c) return;
            const d = SSR.biciStats;
            new Chart(c, {
                type: 'doughnut',
                data: {
                    labels: ['En stock','Vendidas','En reparación'],
                    datasets: [{ data:[d.en_stock,d.vendidas,d.en_reparacion],
                        backgroundColor: ['#10b981', '#4c23bb', '#ef4444'], borderWidth:0, hoverOffset:4 }]
                },
                options: { responsive:false, cutout:'74%', animation:{duration:700},
                    plugins:{ legend:{display:false},
                        tooltip:{ backgroundColor: isDark()?'rgba(17,24,39,0.97)':'rgba(255,255,255,0.99)',
                            borderColor: isDark()?'rgba(9, 219, 54, 0.8)':'rgba(226,232,240,0.9)',
                            borderWidth:1, padding:{x:10,y:8}, cornerRadius:12, displayColors:false,
                            titleFont:{size:11,weight:'600'}, bodyFont:{size:10},
                            titleColor: isDark()?'#f3f4f6':'#111827', bodyColor: isDark()?'#9ca3af':'#6b7280',
                            callbacks:{label:l=>`  ${l.label}: ${l.parsed}`} }} }
            });
            this._donutInited = true;
        },

        _donutMobileInited: false,
        _initDonutMobile() {
            if (this._donutMobileInited) return;
            const c = document.getElementById('donut-chart-mobile'); if (!c) return;
            const d = SSR.biciStats;
            new Chart(c, {
                type: 'doughnut',
                data: {
                    labels: ['En stock','Vendidas','En reparación'],
                    datasets: [{ data:[d.en_stock,d.vendidas,d.en_reparacion],
                        backgroundColor: ['#10b981', '#4c23bb', '#ef4444'], borderWidth:0, hoverOffset:4 }]
                },
                options: { responsive:false, cutout:'74%', animation:{duration:700},
                    plugins:{ legend:{display:false},
                        tooltip:{ backgroundColor: isDark()?'rgba(17,24,39,0.97)':'rgba(255,255,255,0.99)',
                            borderColor: isDark()?'rgba(9, 219, 54, 0.8)':'rgba(226,232,240,0.9)',
                            borderWidth:1, padding:{x:10,y:8}, cornerRadius:12, displayColors:false,
                            titleFont:{size:11,weight:'600'}, bodyFont:{size:10},
                            titleColor: isDark()?'#f3f4f6':'#111827', bodyColor: isDark()?'#9ca3af':'#6b7280',
                            callbacks:{label:l=>`  ${l.label}: ${l.parsed}`} }} }
            });
            this._donutMobileInited = true;
        },

        // ── MÉTODOS PARA MODAL DE GRÁFICAS (tamaño dinámico) ──
        openChartModal(type) {
            if (this.loading) return;
            if (type === 'sucursales' && !this.graficaSucursales.length) return;
            if (type !== 'sucursales' && !this.graficaDias.length) return;

            this.isChartModal = true;
            this.modalOpen = true;
            this.modalLoading = false;
            const titles = { ingresos: 'Ingresos - Detalle', ventas: 'Ventas - Detalle', sucursales: 'Por sucursal - Detalle' };
            this.modalTitle = titles[type] || 'Gráfica';

            // Calcula el ancho del modal según la cantidad de datos/series
            this.modalChartWidthClass = this._chartModalSize(type);

            this.modalContent = `<div id="modal-chart-wrap" style="width:100%;height:420px;"></div>`;

            this.$nextTick(() => {
                setTimeout(() => {
                    this._renderChartModal(type);
                }, 50);
            });
        },

        // Determina el tamaño del modal según el volumen de datos a graficar
        _chartModalSize(type) {
            const n = this.graficaDias.length || 0;
            if (type === 'sucursales') {
                const series = this.graficaSucursales.length;
                if (series > 5 || n > 30) return 'max-w-6xl';
                if (series > 2 || n > 14) return 'max-w-5xl';
                return 'max-w-4xl';
            }
            if (n > 30) return 'max-w-6xl';
            if (n > 14) return 'max-w-5xl';
            if (n > 7)  return 'max-w-4xl';
            return 'max-w-3xl';
        },

        closeModal() {
            this.modalOpen = false;
            this.isChartModal = false;
            this.modalLoading = false;
            this.modalContent = '';
        },

        _renderChartModal(type) {
            const container = document.getElementById('modal-chart-wrap');
            if (!container) return;
            const labels = this.graficaDias.map(d => d.label);

            if (type === 'ingresos') {
                const data = this.graficaDias.map(d => d.ingresos);
                const anterior = this.graficaAnterior.map(d => d.ingresos);
                this._buildFinancialSVG({
                    containerId: 'modal-chart-wrap',
                    data, anterior, labels,
                    isMoney: true,
                    yTicks: calcYTicks(Math.max(...data, 1))
                });
            } else if (type === 'ventas') {
                const data = this.graficaDias.map(d => d.ventas);
                const anterior = this.graficaAnterior.map(d => d.ventas);
                this._buildFinancialSVG({
                    containerId: 'modal-chart-wrap',
                    data, anterior, labels,
                    isMoney: false,
                    yTicks: calcYTicks(Math.max(...data, 1))
                });
            } else if (type === 'sucursales') {
                const metrica = this.sucursalMetrica;
                const multiSeries = this.graficaSucursales.map((s, i) => ({
                    label: s.nombre,
                    data: s[metrica] || [],
                    color: COLORS[i % COLORS.length],
                    dashed: i !== 0,
                }));
                this._buildFinancialSVG({
                    containerId: 'modal-chart-wrap',
                    data: null,
                    anterior: null,
                    labels,
                    isMoney: metrica === 'ingresos',
                    yTicks: calcYTicks(Math.max(...multiSeries.flatMap(s => s.data), 1)),
                    multiSeries,
                    chartType: 'line'
                });
            }
        },

        // ── Modales (locales y con fetch) ──
        async openModal(type, idx = 0) {
            // Si el modal ya está abierto como gráfica, lo cerramos primero
            if (this.isChartModal) {
                this.closeModal();
                await new Promise(r => setTimeout(r, 150));
            }

            this.modalOpen    = true;
            this.modalLoading = true;
            this.modalContent = '';
            this.isChartModal = false;

            const localHandlers = {
                ingresosKPI:  () => this._modalIngresosKPI(),
                ventasKPI:    () => this._modalVentasKPI(),
                ticketKPI:    () => this._modalTicketKPI(),
                metodosPago:  () => this._modalMetodosPago(),
                clientesTipo: () => this._modalClientesTipo(),
                bicicletas:   () => this._modalBicicletas(),
                pedidosList:  () => this._modalPedidosList(),
                pedidoItem:   () => this._modalPedidoItem(idx),
                otsList:      () => this._modalOtsList(),
                sucursales:   () => this._modalSucursales(),
                sucursalDetalle: () => this._modalSucursalDetalle(idx),
                // NUEVOS HANDLERS
                rotacionInventario: () => this._modalRotacionInventario(),
                margenSucursales:   () => this._modalMargenSucursales(),
            };

            if (localHandlers[type]) {
                const { title, content } = localHandlers[type]();
                this.modalTitle   = title;
                this.modalContent = content;
                this.modalLoading = false;
                return;
            }

            const fetchHandlers = {
                topModelos:      () => this._modalTopModelosFetch(),
                topAccesorios:   () => this._modalTopAccesoriosFetch(),
                cupones:         () => this._modalCuponesFetch(),
                clientesDetalle: () => this._modalClientesDetalleFetch(),
                personalItem:    () => this._modalPersonalItemFetch(idx),
            };

            if (fetchHandlers[type]) {
                await fetchHandlers[type]();
            } else {
                this.modalLoading = false;
            }
        },

        // ── Modales locales ──
        _modalIngresosKPI() {
            const d = this.kpiData;
            const totalMonto = this.metodosPago.reduce((s,m)=>s+m.monto,0);
            return {
                title: 'Ingresos del periodo',
                content: this._stats([
                    {v:fmt$(d.ingresos),l:'Total ingresos'},{v:fmt$(d.descuentos??0),l:'Descuentos'},
                    {v:fmt$((d.ingresos||0)+(d.descuentos||0)),l:'Precio lista'},{v:d.ventas,l:'Ventas'},
                ]) + this._sec('Aporte por sucursal')
                + this.sucursales.map(v => this._bar(v.nombre, v.ingresos_total, this.sucursales[0]?.ingresos_total||1,'',fmt$)).join('')
                + this._sec('Métodos de pago')
                + this.metodosPago.map(m => `<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;padding:8px 12px;border:1px solid rgba(0,0,0,0.07);border-radius:12px;"><div style="flex:1;"><div style="font-size:12px;font-weight:500;text-transform:capitalize;color:#1f2937;">${m.metodo.replace('_',' ')}</div><div style="font-size:9px;color:#9ca3af;">${m.usos} uso${m.usos!==1?'s':''} · ${pct(m.monto,totalMonto)}% del total</div></div><span style="font-size:13px;font-weight:600;color:#111827;">${fmt$(m.monto)}</span></div>`).join(''),
            };
        },

        _modalVentasKPI() {
            const d = this.kpiData;
            const sorted = [...this.horasPico].sort((a,b)=>b.cnt-a.cnt).slice(0,3);
            return {
                title: 'Análisis de ventas',
                content: this._stats([
                    {v:d.ventas,l:'Total ventas'},{v:fmt$(d.ticket),l:'Ticket promedio'},{v:d.clientes,l:'Clientes únicos'},
                ]) + this._sec('Pico de ventas')
                + (sorted.length ? sorted.map(h=>`<div style="font-size:11px;margin-bottom:6px;color:#374151;"><strong style="color:#1f2937;">${h.hora}:00 – ${h.hora+1}:00 h</strong> — ${h.cnt} venta${h.cnt!==1?'s':''}</div>`).join('') : '<p style="color:#9ca3af;font-size:11px;">Sin datos</p>'),
            };
        },

        _modalTicketKPI() {
            const d = this.kpiData;
            return {
                title: 'Ticket promedio',
                content: this._stats([
                    {v:fmt$(d.ticket),l:'Ticket del periodo'},{v:d.ventas,l:'Ventas totales'},{v:fmt$(d.ingresos),l:'Ingresos totales'},
                ]) + this._sec('Por sucursal')
                + this.sucursales.map(v => this._bar(v.nombre, v.ticket_promedio, Math.max(...this.sucursales.map(x=>x.ticket_promedio),1),'',fmt$)).join(''),
            };
        },

        _modalMetodosPago() {
            const totalMonto = this.metodosPago.reduce((s,m)=>s+m.monto,0);
            return {
                title: 'Métodos de pago',
                content: this._stats([{v:fmt$(totalMonto),l:'Total cobrado'},{v:this.metodosPago.reduce((s,m)=>s+m.usos,0),l:'Transacciones'}])
                + this.metodosPago.map(m => {
                    const share = pct(m.monto, totalMonto);
                    return `<div style="border:1px solid rgba(0,0,0,0.07);border-radius:14px;padding:10px 12px;margin-bottom:10px;"><div style="display:flex;align-items:center;gap:12px;"><div><div style="font-size:12px;font-weight:500;text-transform:capitalize;color:#1f2937;">${m.metodo.replace('_',' ')}</div><div style="font-size:9px;color:#9ca3af;">${m.usos} uso${m.usos!==1?'s':''}</div></div><div style="margin-left:auto;text-align:right;"><div style="font-weight:600;color:#111827;">${fmt$(m.monto)}</div><div style="font-size:9px;color:#6b7280;">${share}%</div></div></div><div style="margin-top:8px;height:4px;background:rgba(0,0,0,0.06);border-radius:99px;"><div style="width:${share}%;height:100%;background:#111827;border-radius:99px;"></div></div></div>`;
                }).join(''),
            };
        },

        _modalClientesTipo() {
            const d = this.kpiData;
            const nuevos=d.clientes_nuevos??0, rec=d.clientes_rec??0, total=d.clientes||1;
            return {
                title: 'Clientes',
                content: this._stats([
                    {v:d.clientes,l:'Únicos'},{v:nuevos,l:'Nuevos'},{v:rec,l:'Recurrentes'},{v:pct(rec,total)+'%',l:'Retención'},
                ]) + `<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div style="background:rgba(0,0,0,0.04);border-radius:16px;padding:12px;text-align:center;"><div style="font-size:24px;font-weight:600;color:#111827;">${nuevos}</div><div style="font-size:11px;color:#9ca3af;margin-top:4px;">Nuevos</div><div style="font-size:10px;color:#6b7280;">${pct(nuevos,total)}%</div></div>
                    <div style="background:rgba(0,0,0,0.04);border-radius:16px;padding:12px;text-align:center;"><div style="font-size:24px;font-weight:600;color:#111827;">${rec}</div><div style="font-size:11px;color:#9ca3af;margin-top:4px;">Recurrentes</div><div style="font-size:10px;color:#6b7280;">${pct(rec,total)}%</div></div>
                </div>`,
            };
        },

        _modalBicicletas() {
            const d = SSR.biciStats;
            return {
                title: 'Inventario de bicicletas',
                content: this._stats([{v:d.en_stock,l:'En stock'},{v:d.vendidas,l:'Vendidas'},{v:d.en_reparacion,l:'En reparación'},{v:d.total,l:'Total'}])
                + this._sec('Por vendedor')
                + SSR.stockVendedores.map(v => this._bar(v.nombre, v.total, SSR.stockVendedores[0]?.total||1,'')).join(''),
            };
        },

        _modalPedidosList() {
            return {
                title: 'Pedidos recientes',
                content: this._stats([
                    {v:this.pedidos.filter(p=>p.estado==='pendiente').length,l:'Pendientes'},
                    {v:this.pedidos.filter(p=>p.estado==='proceso').length,l:'En proceso'},
                    {v:fmt$(this.pedidos.reduce((s,p)=>s+p.monto,0)),l:'Monto total'},
                ]) + this.pedidos.map(p=>`<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.05);"><div style="font-size:12px;flex:1;color:#1f2937;"><strong>${p.id}</strong> — ${p.cliente}<div style="font-size:10px;color:#9ca3af;">${fmt$(p.monto)} · ${p.fecha}</div></div><span style="font-size:9px;font-weight:600;color:#6b7280;text-transform:capitalize;">${p.estado}</span></div>`).join(''),
            };
        },

        _modalPedidoItem(idx) {
            const p = this.pedidos[idx];
            return {
                title: p.id,
                content: this._stats([{v:fmt$(p.monto),l:'Total'},{v:p.items,l:'Artículos'}])
                + this._table([['Cliente',p.cliente],['Fecha',p.fecha],['Estado',p.estado]]),
            };
        },

        _modalOtsList() {
            return {
                title: 'Órdenes de trabajo activas',
                content: this._stats([
                    {v:this.ots.length,l:'Activas'},
                    {v:this.ots.filter(o=>o.estado==='en_proceso').length,l:'En proceso'},
                    {v:this.ots.filter(o=>o.estado==='pendiente').length,l:'Pendientes'},
                ]) + this.ots.map(o=>`<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.05);"><div style="flex:1;font-size:12px;color:#1f2937;"><strong>${o.id}</strong> <span style="color:#9ca3af;font-size:10px;">${o.dias}d</span><div style="font-size:10px;color:#9ca3af;">${o.tipo} — ${o.modelo}</div></div></div>`).join(''),
            };
        },

        _modalSucursales() {
            const totalIngs = this.sucursales.reduce((s,x)=>s+x.ingresos_total,0);
            return {
                title: 'Sucursales',
                content: this._stats([
                    {v:this.sucursales.length,l:'Sucursales'},{v:fmt$(totalIngs),l:'Ingresos totales'},
                ]) + this.sucursales.map((v,i) => `
                    <div style="border:1px solid rgba(0,0,0,0.07);border-radius:14px;padding:10px 12px;margin-bottom:10px;">
                        <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px;">${v.nombre}</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center;">
                            <div><div style="font-size:15px;font-weight:600;color:#111827;">${fmt$(v.ingresos_total)}</div><div style="font-size:9px;color:#9ca3af;">Ingresos</div></div>
                            <div><div style="font-size:15px;font-weight:600;color:#111827;">${v.ventas_count}</div><div style="font-size:9px;color:#9ca3af;">Ventas</div></div>
                            <div><div style="font-size:15px;font-weight:600;color:#111827;">${v.aporte_pct}%</div><div style="font-size:9px;color:#9ca3af;">Aporte</div></div>
                        </div>
                        <div style="margin-top:8px;display:grid;grid-template-columns:1fr 1fr;gap:8px;text-align:center;">
                            <div><div style="font-size:13px;font-weight:500;color:#374151;">${v.unidades_bicis}</div><div style="font-size:9px;color:#9ca3af;">Bicis vendidas</div></div>
                            <div><div style="font-size:13px;font-weight:500;color:#374151;">${v.unidades_accesorios}</div><div style="font-size:9px;color:#9ca3af;">Accesorios</div></div>
                        </div>
                    </div>`).join(''),
            };
        },

        _modalSucursalDetalle(idx) {
            const v = this.sucursales[idx];
            const totalIngs = this.sucursales.reduce((s,x)=>s+x.ingresos_total,0);
            return {
                title: v.nombre,
                content: this._stats([
                    {v:fmt$(v.ingresos_total),l:'Ingresos'},{v:v.ventas_count,l:'Ventas'},
                    {v:fmt$(v.ticket_promedio),l:'Ticket prom.'},{v:pct(v.ingresos_total,totalIngs)+'%',l:'Del total'},
                ]) + this._table([
                    ['Bicis vendidas', v.unidades_bicis],
                    ['Accesorios',     v.unidades_accesorios],
                    ['Descuentos',     fmt$(v.descuentos_total)],
                    ['Vendedor top',   v.vendedor_top?.nombre ?? '—'],
                ]),
            };
        },

        // ── NUEVOS MODALES ──
        _modalRotacionInventario() {
            const r = this.rotacionInventario;
            return {
                title: `Bicicletas sin movimiento (${r.dias_umbral}+ días)`,
                content: this._stats([
                    {v: r.total_estancadas, l:'Total estancadas'},
                ]) + this._sec('Por modelo')
                + (r.por_modelo?.length
                    ? r.por_modelo.map(m => this._bar(m.modelo, m.cantidad, r.por_modelo[0]?.cantidad||1,'')).join('')
                    : '<p style="color:#9ca3af;text-align:center;">Sin datos</p>')
                + this._sec('Detalle')
                + r.bicicletas.slice(0, 20).map(b => `
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.05);">
                        <div style="flex:1;font-size:12px;color:#1f2937;">
                            <strong>${b.num_serie}</strong> — ${b.modelo}
                            <div style="font-size:10px;color:#9ca3af;">${b.color} · ${b.voltaje}V</div>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:#dc2626;">${b.dias}d</span>
                    </div>`).join(''),
            };
        },

        _modalMargenSucursales() {
            return {
                title: 'Margen por sucursal',
                content: this.margenSucursales.map(m => `
                    <div style="border:1px solid rgba(0,0,0,0.07);border-radius:14px;padding:12px;margin-bottom:10px;">
                        <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:8px;">${m.nombre}</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center;">
                            <div><div style="font-size:14px;font-weight:600;color:#111827;">${fmt$(m.ingresos)}</div><div style="font-size:9px;color:#9ca3af;">Ingresos</div></div>
                            <div><div style="font-size:14px;font-weight:600;color:#dc2626;">${fmt$(m.gastos)}</div><div style="font-size:9px;color:#9ca3af;">Gastos</div></div>
                            <div><div style="font-size:14px;font-weight:600;color:#16a34a;">${fmt$(m.margen)}</div><div style="font-size:9px;color:#9ca3af;">Margen</div></div>
                        </div>
                    </div>`).join('') + '<p style="font-size:10px;color:#9ca3af;font-style:italic;margin-top:8px;">* Gastos pendientes de captura — margen actual = ingresos totales</p>',
            };
        },

        // ── Modales con fetch ──
        async _modalTopModelosFetch() {
            this.modalTitle = 'Modelos más vendidos';
            const data = await this.fetchDetalle('top_modelos');
            if (!data) { this.modalContent = '<p style="color:#9ca3af;text-align:center;">Error al cargar</p>'; return; }

            let content = this._stats([
                {v: data.length, l:'Modelos vendidos'},
                {v: data.reduce((s,m)=>s+m.unidades,0), l:'Unidades totales'},
                {v: fmt$(data.reduce((s,m)=>s+m.ingresos,0)), l:'Ingresos'},
            ]);

            content += this._sec('Ranking');
            content += data.map((m,i) => this._bar(m.nombre, m.unidades, data[0]?.unidades||1,'')).join('');

            if (this.tops.config) {
                content += this._sec('Configuración más vendida');
                content += `<div style="background:rgba(0,0,0,0.04);border-radius:12px;padding:12px;margin-bottom:8px;">
                    <div style="font-size:14px;font-weight:600;color:#111827;">${this.tops.config.config}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">${this.tops.config.unidades} veces vendida</div>
                </div>`;
            }

            if (data[0]) {
                const det = await this.fetchDetalle('detalle_modelo', { id_modelo: data[0].id_modelo });
                if (det) {
                    content += this._sec(data[0].nombre + ' — Colores');
                    content += det.colores.map(c => this._bar(c.color.split('|')[0], c.cnt, det.colores[0]?.cnt||1,'')).join('');
                    content += this._sec(data[0].nombre + ' — Voltajes');
                    content += det.voltajes.map(v => this._bar(v.voltaje+'V', v.cnt, det.voltajes[0]?.cnt||1,'')).join('');
                }
            }

            this.modalContent = content;
        },

        async _modalTopAccesoriosFetch() {
            this.modalTitle = 'Accesorios y combos';
            const [accesorios, combos] = await Promise.all([
                this.fetchDetalle('top_accesorios'),
                this.fetchDetalle('combos'),
            ]);

            let content = '';

            if (accesorios?.length) {
                content += this._stats([
                    {v: accesorios.length, l:'Accesorios'},
                    {v: accesorios.reduce((s,a)=>s+a.unidades,0), l:'Unidades'},
                    {v: fmt$(accesorios.reduce((s,a)=>s+a.ingresos,0)), l:'Ingresos'},
                ]);
                content += this._sec('Ranking de accesorios');
                content += accesorios.map(a => this._bar(a.nombre, a.unidades, accesorios[0]?.unidades||1,'')).join('');
            }

            if (combos?.length) {
                content += this._sec('Combos más frecuentes');
                content += combos.map(c => this._bar(c.combo, c.uds, combos[0]?.uds||1,'')).join('');
            }

            if (!accesorios?.length && !combos?.length) {
                content = '<p style="color:#9ca3af;text-align:center;padding:20px 0;">Sin datos en este periodo</p>';
            }

            this.modalContent = content;
        },

        async _modalCuponesFetch() {
            this.modalTitle = 'Cupones';
            const data = await this.fetchDetalle('cupones');
            const d    = this.kpiData;

            let content = this._stats([
                {v: fmt$(d.cupones_descuento??0), l:'Total descontado'},
                {v: d.cupones_usados??0, l:'Usos totales'},
                {v: data?.length??0, l:'Cupones distintos'},
            ]);

            if (data?.length) {
                content += this._sec('Detalle por cupón');
                content += data.map(c => `
                    <div style="border:1px solid rgba(0,0,0,0.07);border-radius:12px;padding:10px 12px;margin-bottom:8px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:#111827;">${c.codigo}</div>
                                <div style="font-size:10px;color:#9ca3af;">${c.nombre}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:13px;font-weight:600;color:#dc2626;">${fmt$(c.total_descuento)}</div>
                                <div style="font-size:9px;color:#9ca3af;">${c.usos} uso${c.usos!==1?'s':''}</div>
                            </div>
                        </div>
                    </div>`).join('');
            } else {
                content += '<p style="color:#9ca3af;text-align:center;padding:12px 0;">Sin cupones usados</p>';
            }

            this.modalContent = content;
        },

        async _modalClientesDetalleFetch() {
            this.modalTitle = 'Clientes — Top compradores';
            const data = await this.fetchDetalle('clientes');
            const d    = this.kpiData;

            let content = this._stats([
                {v:d.clientes,l:'Únicos'},{v:d.clientes_nuevos??0,l:'Nuevos'},{v:d.clientes_rec??0,l:'Recurrentes'},
            ]);

            if (data?.top_clientes?.length) {
                content += this._sec('Top compradores');
                content += data.top_clientes.map(c => `
                    <div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.05);">
                        <div style="flex:1;font-size:12px;color:#1f2937;font-weight:500;">${c.nombre}</div>
                        <div style="text-align:right;">
                            <div style="font-size:12px;font-weight:600;color:#111827;">${fmt$(c.total_gastado)}</div>
                            <div style="font-size:9px;color:#9ca3af;">${c.compras} compra${c.compras!==1?'s':''}</div>
                        </div>
                    </div>`).join('');
            }

            this.modalContent = content;
        },

        async _modalPersonalItemFetch(idx) {
            const p      = SSR.personal[idx];
            const pNom   = p.nombre;
            const suc    = this.sucursales.find(v => v.nombre === p.sucursal) || {};
            this.modalTitle = pNom;

            const serie = this.ventasPersonal.find(s => s.nombre === pNom);

            let content = this._table([
                ['Sucursal', p.sucursal],
                ['Estado',   p.activo ? 'Activo' : 'Inactivo'],
            ]);

            const idUsuario = this.sucursales.find(v => v.nombre === p.sucursal)?.id_usuario;
            if (idUsuario) {
                const det = await this.fetchDetalle('vendedor_detalle', { id_usuario: idUsuario });
                const personalRow = det?.find(r => r.nombre === pNom);
                if (personalRow) {
                    content += this._stats([
                        {v: personalRow.ventas,          l:'Ventas'},
                        {v: fmt$(personalRow.ingresos),  l:'Ingresos'},
                        {v: personalRow.ventas > 0 ? fmt$(Math.round(personalRow.ingresos/personalRow.ventas)) : '—', l:'Ticket prom.'},
                    ]);
                }
            }

            if (serie && serie.ventas.some(v => v > 0)) {
                content += this._sec('Ventas diarias');
                content += `<div id="modal-personal-ventas" style="min-height:160px;"></div>`;
                content += this._sec('Ingresos diarios');
                content += `<div id="modal-personal-ingresos" style="min-height:160px;"></div>`;
            }

            this.modalContent = content;

            if (serie) {
                this.$nextTick(() => {
                    setTimeout(() => {
                        const labels = this.graficaDias.map(d => d.label);
                        if (document.getElementById('modal-personal-ventas')) {
                            this._buildFinancialSVG({ containerId:'modal-personal-ventas', data:serie.ventas,
                                anterior:null, labels, isMoney:false, yTicks:calcYTicks(Math.max(...serie.ventas,1)) });
                        }
                        if (document.getElementById('modal-personal-ingresos')) {
                            this._buildFinancialSVG({ containerId:'modal-personal-ingresos', data:serie.ingresos,
                                anterior:null, labels, isMoney:true, yTicks:calcYTicks(Math.max(...serie.ingresos,1)) });
                        }
                    }, 80);
                });
            }
        },

        // ── Helpers HTML ──
        _stats(items) {
            const cols = Math.min(items.length, 4);
            return `<div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:8px;margin-bottom:20px;">`
                + items.map(it=>`<div style="background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.06);border-radius:12px;padding:10px;text-align:center;"><div style="font-size:18px;font-weight:600;color:#111827;letter-spacing:-0.5px;">${it.v}</div><div style="font-size:9px;color:#9ca3af;margin-top:4px;text-transform:uppercase;letter-spacing:.06em;">${it.l}</div></div>`).join('')+'</div>';
        },
        _sec(l) {
            return `<div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin:16px 0 8px;">${l}</div>`;
        },
        _table(rows) {
            return `<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:16px;">`
                + rows.map(([k,v])=>`<tr><td style="padding:6px 0;color:#9ca3af;border-bottom:1px solid rgba(0,0,0,0.05);">${k}</td><td style="padding:6px 0;text-align:right;font-weight:500;border-bottom:1px solid rgba(0,0,0,0.05);color:#111827;">${v}</td></tr>`).join('')
                +'</table>';
        },
        _bar(label, val, max, _color='', fmtFn=null) {
            const p    = max > 0 ? Math.min(100, Math.round(val/max*100)) : 0;
            const disp = fmtFn ? fmtFn(val) : val;
            return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;"><div style="width:80px;font-size:11px;color:#6b7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${label}</div><div style="flex:1;height:4px;background:rgba(0,0,0,0.06);border-radius:99px;"><div style="width:${p}%;height:100%;background:#111827;border-radius:99px;"></div></div><div style="font-size:11px;font-weight:500;color:#374151;text-align:right;min-width:52px;">${disp}</div></div>`;
        },

        // ── Echo (enlace vendedor) ──
        _listenEcho() {
            if (!window.Echo) return;
            window.Echo.private(`enlace-vendedor.{{ auth()->user()->id_usuario }}`)
                .listen('.enlace.updated', ({action, enlace}) => {
                    if (action==='aceptado')  { this.enlaceCancelado=false; this.enlaceEstado='activo';  this.enlaceGestor=enlace.gestor; }
                    if (action==='cancelado') { this.enlaceCancelado=true;  this.enlaceEstado='';        this.enlaceGestor=''; }
                });
        },

        // ── Echo (feed de movimientos de bicicletas, canal público) ──
        _listenMovimientos() {
            if (!window.Echo) return;
            if (this._canalMovimientos) {
                window.Echo.leaveChannel(`movimientos.{{ auth()->user()->id_negocio }}`);
            }
            this._canalMovimientos = window.Echo.channel(`movimientos.{{ auth()->user()->id_negocio }}`);

            this._canalMovimientos.listen('.movimiento.nuevo', (e) => {
                const nuevo = {
                    ...e,
                    id_movimiento: e.id ?? Date.now() + Math.random(),
                    animate: true,
                    relativo: this.formatRelativo(e.fecha_movimiento),
                };
                const yaExiste = this.feedMovimientos.some(m => m.id_movimiento === nuevo.id_movimiento);
                if (!yaExiste) {
                    this.feedMovimientos.unshift(nuevo);
                    if (this.feedMovimientos.length > 20) this.feedMovimientos.pop();
                    setTimeout(() => {
                        const m = this.feedMovimientos.find(x => x.id_movimiento === nuevo.id_movimiento);
                        if (m) m.animate = false;
                    }, 1200);
                }
            });
        },

        
        _listenVentas() {
            if (!window.Echo) return;

            if (this._canalVentas) {
                window.Echo.leave(`negocio.{{ auth()->user()->id_negocio }}`);
            }

            this._canalVentas = window.Echo.private(`negocio.{{ auth()->user()->id_negocio }}`);

            this._canalVentas.listen('.venta.registrada', (e) => {
                const nueva = {
                    id_venta: crypto.randomUUID(),
                    monto:    e.total,
                    sucursal: e.nombre_vendedor,
                    hora:     e.hora,
                    fecha:    new Date().toISOString(),
                    animate:  true,
                    relativo: 'ahora',
                };
                this.feedVentas.unshift(nueva);
                if (this.feedVentas.length > 20) this.feedVentas.pop();
                setTimeout(() => {
                    const v = this.feedVentas.find(x => x.id_venta === nueva.id_venta);
                    if (v) v.animate = false;
                }, 1200);
            });
        },

        formatRelativo(fecha) {
            const diff = Math.floor((Date.now() - new Date(fecha)) / 1000);
            if (diff < 60)    return 'ahora';
            if (diff < 3600)  return Math.floor(diff/60) + 'm';
            if (diff < 86400) return Math.floor(diff/3600) + 'h';
            return Math.floor(diff/86400) + 'd';
        },

        labelTipoMov(tipo) {
            const m = {
                entrada_stock:          'Entrada stock',
                transferencia_sucursal: 'Transferencia',
                venta:                  'Venta',
                mantenimiento:          'Mantenimiento',
                ajuste:                 'Ajuste',
            };
            return m[tipo] ?? tipo;
        },

        iconoTipoMovSVG(tipo) {
            const svgs = {
                entrada_stock: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`,
                transferencia_sucursal: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>`,
                venta: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 17v4"/></svg>`,
                mantenimiento: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
                ajuste: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>`,
            };
            return svgs[tipo] || `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-3.5 h-3.5"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/></svg>`;
        },
    };
}
</script>
</div>
</x-app-layout>