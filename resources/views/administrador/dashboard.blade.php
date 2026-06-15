<x-app-layout>
<div class="space-y-0">

<div
    class="p-5 flex flex-col gap-4 bg-gray-50 dark:bg-gray-950 min-h-screen font-sans text-[11px] text-gray-900 dark:text-gray-100"
    x-data="dashboard()"
    x-init="init()"
>

    

    {{-- ── TOPBAR ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 font-semibold text-sm text-gray-900 dark:text-white tracking-tight">
                <i class="ti ti-layout-dashboard"></i> Panel general
            </div>
            <div class="text-[10px] text-gray-400 mt-0.5">
                {{ auth()->user()->negocio->nombre_negocio ?? 'Negocio' }}
                <span class="mx-1 text-gray-300 dark:text-gray-600">·</span>
                <span x-text="clock">--:--</span>
            </div>
        </div>
        <button @click="enlaceOpen=true"
                class="inline-flex items-center gap-2 text-[10px] font-semibold px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-900 dark:hover:text-white hover:-translate-y-px transition-all">
            <span class="w-1.5 h-1.5 rounded-full shrink-0"
                  :class="enlaceEstado==='activo' ? 'bg-green-500' : enlaceEstado==='pendiente' ? 'bg-amber-500' : enlaceCancelado ? 'bg-red-500' : 'bg-gray-400'">
            </span>
            <span x-text="enlaceEstado==='activo' ? 'Enlace · '+enlaceGestor : enlaceEstado==='pendiente' ? 'Enlace pendiente' : enlaceCancelado ? 'Enlace desconectado' : 'Enlace'"></span>
        </button>
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
        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 shadow-lg">
            <svg class="h-4 w-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-[12px] font-medium text-gray-900 dark:text-white flex-1">{{ session('success') }}</p>
            <button @click="show=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ── BARRA DE PERIODO ── --}}
    <div class="flex items-center gap-2 flex-wrap bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2">
        <i class="ti ti-calendar-stats text-gray-400 text-[13px]"></i>
        <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mr-1">Periodo</span>
        <template x-for="p in presets" :key="p.key">
            <button class="text-[10px] font-semibold px-2.5 py-1 rounded-lg border transition-all"
                    :class="periodo===p.key
                        ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 border-gray-900 dark:border-white'
                        : 'bg-transparent text-gray-500 dark:text-gray-400 border-transparent hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white hover:border-gray-200 dark:hover:border-gray-600'"
                    @click="setPeriod(p.key)"
                    x-text="p.label">
            </button>
        </template>
        <div x-show="periodo==='custom'" x-cloak class="flex items-center gap-2 ml-1">
            <input type="date" x-model="customDesde" @change="fetchStats()"
                   class="text-[10px] px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
            <span class="text-gray-400 text-[11px]">–</span>
            <input type="date" x-model="customHasta" @change="fetchStats()"
                   class="text-[10px] px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        </div>
        <span class="ml-auto text-[10px] text-gray-400 font-medium" x-text="periodoLabel"></span>
        <svg x-show="loading" x-cloak class="w-3.5 h-3.5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
    </div>

    {{-- ── KPIs ── --}}
    <div class="grid grid-cols-[repeat(auto-fill,minmax(140px,1fr))] gap-2.5">
        <template x-if="loading">
            <template x-for="i in 9" :key="i">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 animate-pulse">
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-3/5 mb-3"></div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-4/5 mb-1.5"></div>
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
            </template>
        </template>
        <template x-if="!loading">
            <template x-for="k in kpis" :key="k.key">
                <div class="relative overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 cursor-pointer hover:-translate-y-0.5 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all"
                     @click="openModal(k.modal)">
                    {{-- Franja de color superior --}}
                    <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-xl" :style="'background:'+k.color"></div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-semibold uppercase tracking-widest text-gray-400" x-text="k.label"></span>
                        <div class="w-5 h-5 rounded-md flex items-center justify-center" :style="{background:k.bg}">
                            <i :class="'ti '+k.icon+' text-[12px]'" :style="{color:k.color}"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-medium leading-none tracking-tight text-gray-900 dark:text-white mb-1" x-text="k.value"></div>
                    <div class="text-[10px] text-gray-500 dark:text-gray-400" x-text="k.sub"></div>
                    <div class="text-[10px] text-gray-400 mt-0.5" x-text="k.delta"></div>
                </div>
            </template>
        </template>
    </div>

    {{-- ── GRÁFICAS VENTAS / INGRESOS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-receipt text-gray-400"></i> Ventas
                    <span class="text-[10px] font-normal text-gray-400" x-text="'· '+periodoLabel"></span>
                </div>
                <span class="font-semibold text-[17px] text-gray-900 dark:text-white" x-text="kpiData.ventas ?? '—'"></span>
            </div>
            <div class="p-3"><div class="h-[170px]"><canvas id="ventas-chart"></canvas></div></div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-trending-up text-gray-400"></i> Ingresos
                    <span class="text-[10px] font-normal text-gray-400" x-text="'· '+periodoLabel"></span>
                </div>
                <span class="font-semibold text-[17px] text-gray-900 dark:text-white" x-text="kpiData.ingresos ? fmt$(kpiData.ingresos) : '—'"></span>
            </div>
            <div class="p-3"><div class="h-[170px]"><canvas id="ingresos-chart"></canvas></div></div>
        </div>
    </div>

    {{-- ── HEATMAP ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                <i class="ti ti-chart-dots text-gray-400"></i> Mapa de calor — ventas por hora y día
            </div>
            <span class="text-[10px] text-gray-400">más oscuro = mayor actividad</span>
        </div>
        <template x-if="!loading && hasData">
            <div class="p-3 pl-4" x-html="heatmapHtml"></div>
        </template>
        <template x-if="!loading && !hasData">
            <div class="flex flex-col items-center justify-center gap-3 py-8 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-200 dark:border-gray-600">
                    <i class="ti ti-calendar-off text-2xl text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Todavía no hay suficientes datos</p>
                <p class="text-xs text-gray-400">Mañana habrá más ventas — vuelve pronto</p>
            </div>
        </template>
        <div class="p-4" x-show="loading" x-cloak>
            <div class="h-36 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
        </div>
    </div>

    {{-- ── REGRESIÓN + MEDIA MÓVIL ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-chart-line text-gray-400"></i> Regresión lineal — ingresos
                </div>
                <span class="text-[10px] text-gray-400" x-text="regression.r2 ? 'R² = ' + regression.r2 : '—'"></span>
            </div>
            <template x-if="!loading && hasData">
                <div class="p-3">
                    <div class="h-[160px]"><canvas id="regression-chart"></canvas></div>
                    <p class="text-center text-[10px] text-gray-400 mt-2" x-text="regression.equation"></p>
                    <p class="text-center text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5"
                       x-show="regression.prediction"
                       x-text="'Predicción a 7 días: ' + fmt$(regression.prediction)"></p>
                </div>
            </template>
            <template x-if="!loading && !hasData">
                <div class="flex flex-col items-center gap-2 py-8 text-center">
                    <i class="ti ti-chart-line text-2xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-xs text-gray-400">Sin datos para análisis de tendencia</p>
                </div>
            </template>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-chart-arrows text-gray-400"></i> Estacionalidad — media móvil 7 días
                </div>
            </div>
            <template x-if="!loading && hasData">
                <div class="p-3 h-[210px]"><canvas id="moving-average-chart"></canvas></div>
            </template>
            <template x-if="!loading && !hasData">
                <div class="flex flex-col items-center gap-2 py-8 text-center">
                    <i class="ti ti-wave-sine text-2xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-xs text-gray-400">No hay suficientes puntos para la media móvil</p>
                </div>
            </template>
        </div>
    </div>

    {{-- ── MÉTODOS DE PAGO + CLIENTES ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm cursor-pointer hover:-translate-y-0.5 hover:shadow-md transition-all"
             @click="openModal('metodosPago')">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-credit-card text-gray-400"></i> Métodos de pago
                </div>
                <span class="text-[10px] text-gray-400 cursor-pointer">ver detalle</span>
            </div>
            <div class="p-3.5" x-show="!loading">
                <template x-if="metodosPago.length === 0">
                    <p class="text-center text-gray-400 py-2">Sin datos</p>
                </template>
                <template x-for="(m, i) in metodosPago.slice(0, 4)" :key="m.metodo">
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[90px] shrink-0 truncate capitalize" x-text="m.metodo.replace('_',' ')"></span>
                        <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 :style="{width: metodosPago[0].monto > 0 ? Math.round(m.monto/metodosPago[0].monto*100)+'%' : '0%', background: COLORS[i%6]}">
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 text-right min-w-[52px]" x-text="fmt$(m.monto)"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm cursor-pointer hover:-translate-y-0.5 hover:shadow-md transition-all"
             @click="openModal('clientesTipo')">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-users text-gray-400"></i> Clientes
                </div>
                <span class="text-[10px] text-gray-400">ver detalle</span>
            </div>
            <div class="p-3.5" x-show="!loading">
                <template x-for="(c, i) in clientesTipo" :key="c.tipo">
                    <div class="flex justify-between py-1.5 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :style="{background: ['#3b82f6','#2c6e49'][i%2]}"></span>
                            <span class="text-[11px] text-gray-700 dark:text-gray-300" x-text="c.tipo"></span>
                        </div>
                        <span class="font-semibold text-[11px]" x-text="c.cnt"></span>
                    </div>
                </template>
                <div class="flex justify-between pt-2">
                    <span class="text-[10px] text-gray-400">Total únicos</span>
                    <span class="font-semibold" x-text="kpiData.clientes ?? '—'"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── INGRESOS POR SUCURSAL + TOP MODELOS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-trophy text-gray-400"></i> Ingresos por sucursal
                    <span class="text-[10px] font-normal text-gray-400" x-text="'· '+periodoLabel"></span>
                </div>
            </div>
            <div class="p-3.5" x-show="!loading">
                <template x-if="ingresosVendedor.length === 0">
                    <p class="text-center text-gray-400">Sin ventas</p>
                </template>
                <template x-for="(v, i) in ingresosVendedor" :key="v.nombre">
                    <div class="flex items-center gap-2.5 mb-2 cursor-pointer hover:translate-x-0.5 transition-transform"
                         @click="openModal('sucursalDetalle', i)">
                        <span class="text-[11px] text-gray-500 w-[90px] shrink-0 truncate" x-text="v.nombre"></span>
                        <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 :style="{width: ingresosVendedor[0].monto > 0 ? Math.round(v.monto/ingresosVendedor[0].monto*100)+'%' : '0%', background: COLORS[i%6]}">
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-gray-500 text-right min-w-[52px]" x-text="fmt$(v.monto)"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-bike text-gray-400"></i> Modelos más vendidos
                    <span class="text-[10px] font-normal text-gray-400" x-text="'· '+periodoLabel"></span>
                </div>
            </div>
            <div class="p-3.5" x-show="!loading">
                <template x-if="topModelos.length === 0">
                    <p class="text-center text-gray-400">Sin ventas</p>
                </template>
                <template x-for="(m, i) in topModelos.slice(0, 6)" :key="m.id_modelo">
                    <div class="flex items-center gap-2.5 mb-2 cursor-pointer hover:translate-x-0.5 transition-transform"
                         @click="openModal('modeloDetalle', i)">
                        <span class="text-[11px] text-gray-500 w-[90px] shrink-0 truncate" x-text="m.nombre"></span>
                        <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 :style="{width: topModelos[0].unidades > 0 ? Math.round(m.unidades/topModelos[0].unidades*100)+'%' : '0%', background: COLORS[i%6]}">
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-gray-500 text-right min-w-[52px]" x-text="m.unidades+' u.'"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ── INVENTARIO BICICLETAS + STOCK POR VENDEDOR ── --}}
    @php
        $profColors = ['#3b82f6','#2c6e49','#b3432e','#6b4e9e','#cc7b2c','#64748b'];
        $maxStock   = collect($stockVendedores)->max('total') ?: 1;
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm cursor-pointer hover:-translate-y-0.5 hover:shadow-md transition-all"
             @click="openModal('bicicletas')">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-bike text-gray-400"></i> Inventario de bicicletas
                    <span class="text-[10px] font-normal text-gray-400">— click</span>
                </div>
            </div>
            <div class="p-3.5 flex items-center gap-5">
                <canvas id="donut-chart" width="80" height="80"></canvas>
                <div class="flex-1 flex flex-col gap-1.5">
                    @foreach([['color'=>'#3b82f6','label'=>'En stock','key'=>'en_stock'],['color'=>'#2c6e49','label'=>'Vendidas','key'=>'vendidas'],['color'=>'#b3432e','label'=>'En reparación','key'=>'en_reparacion']] as $row)
                    <div class="flex items-center gap-2 text-[11px]">
                        <span class="w-2 h-2 rounded-sm shrink-0" style="background:{{ $row['color'] }}"></span>
                        <span class="text-gray-500 dark:text-gray-400 flex-1">{{ $row['label'] }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $biciStats[$row['key']] }}</span>
                    </div>
                    @endforeach
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-1 flex justify-between">
                        <span class="text-[11px] text-gray-400">Total</span>
                        <span class="font-semibold text-[11px] text-gray-900 dark:text-white">{{ $biciStats['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-chart-bar text-gray-400"></i> Stock por vendedor
                </div>
            </div>
            <div class="p-3.5">
                @foreach($stockVendedores as $i => $v)
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="text-[11px] text-gray-500 w-[90px] shrink-0 truncate">{{ $v['nombre'] }}</span>
                    <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ round($v['total']/$maxStock*100) }}%;background:{{ $profColors[$i%6] }};"></div>
                    </div>
                    <span class="text-[11px] font-medium text-gray-500 text-right min-w-[52px]">{{ $v['total'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── ACCESORIOS + PEDIDOS RECIENTES ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-shopping-bag text-gray-400"></i> Accesorios más vendidos
                    <span class="text-[10px] font-normal text-gray-400" x-text="'· '+periodoLabel"></span>
                </div>
            </div>
            <div class="p-3.5" x-show="!loading">
                <template x-if="topAccesorios.length === 0">
                    <p class="text-center text-gray-400">Sin ventas de accesorios</p>
                </template>
                <template x-for="(a, i) in topAccesorios" :key="a.nombre">
                    <div class="flex items-center gap-2.5 mb-2">
                        <span class="text-[11px] text-gray-500 w-[90px] shrink-0 truncate" x-text="a.nombre"></span>
                        <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 :style="{width: topAccesorios[0].unidades > 0 ? Math.round(a.unidades/topAccesorios[0].unidades*100)+'%' : '0%', background: COLORS[i%6]}">
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-gray-500 text-right min-w-[52px]" x-text="a.unidades+' u.'"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-clipboard-list text-gray-400"></i> Pedidos recientes
                </div>
                <span class="text-[10px] text-gray-400 cursor-pointer hover:text-blue-500 transition-colors" @click="openModal('pedidosList')">ver todos</span>
            </div>
            <div x-show="!loading">
                <template x-if="pedidos.length === 0">
                    <div class="py-6 text-center text-sm text-gray-400">Sin pedidos recientes</div>
                </template>
                <template x-for="(p, i) in pedidos" :key="p.id">
                    <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-0.5 transition-all"
                         @click="openModal('pedidoItem', i)">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                             :style="{background: ICON_BG[i%4]}">
                            <i class="ti ti-clipboard text-[13px]" :style="{color: ICON_COL[i%4]}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[12px] font-medium text-gray-900 dark:text-white truncate" x-text="p.id"></div>
                            <div class="text-[10px] text-gray-400" x-text="p.cliente+' · '+p.fecha"></div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-semibold text-sm text-gray-900 dark:text-white" x-text="fmt$(p.monto)"></div>
                            <span class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full uppercase tracking-wider"
                                  :class="{'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': p.estado==='proceso', 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': p.estado==='pendiente', 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400': p.estado==='completado'}"
                                  x-text="p.estado">
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ── PIEZAS BAJAS + OTs ACTIVAS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-tool text-gray-400"></i> Piezas con stock bajo
                </div>
            </div>
            @forelse($piezasBajas as $p)
            <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-0.5 transition-all"
                 @click="openModal('piezaItem', {{ $loop->index }})">
                <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                    <i class="ti ti-tool text-[13px] text-red-500"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[12px] font-medium text-gray-900 dark:text-white truncate">{{ $p['nombre'] }}</div>
                    <div class="text-[10px] text-gray-400">
                        mín. {{ $p['minimo'] }} · actual:
                        <strong class="{{ $p['stock']==0 ? 'text-red-500' : 'text-amber-500' }}">{{ $p['stock'] }}</strong>
                        · {{ $p['cat'] }}
                    </div>
                </div>
                @if($p['stock']==0)
                    <span class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-wider">Agotado</span>
                @else
                    <span class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase tracking-wider">Bajo</span>
                @endif
            </div>
            @empty
            <div class="py-6 text-center text-sm text-gray-400">Sin alertas de stock</div>
            @endforelse
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                    <i class="ti ti-tools text-gray-400"></i> OTs activas
                    <span x-show="ots.length > 0" x-cloak
                          class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 uppercase tracking-wider"
                          x-text="ots.length">
                    </span>
                </div>
                <span class="text-[10px] text-gray-400 cursor-pointer hover:text-blue-500 transition-colors" @click="openModal('otsList')">ver todas</span>
            </div>
            <div x-show="!loading">
                <template x-if="ots.length === 0">
                    <div class="py-6 text-center text-sm text-gray-400">Sin OTs activas</div>
                </template>
                <template x-for="(o, i) in ots.slice(0, 5)" :key="o.id">
                    <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:translate-x-0.5 transition-all"
                         @click="openModal('otItem', i)">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                            <i class="ti ti-tools text-[13px] text-amber-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[12px] font-medium text-gray-900 dark:text-white truncate" x-text="o.id"></div>
                            <div class="text-[10px] text-gray-400" x-text="o.tipo+' — '+o.modelo"></div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center text-[9px] font-semibold px-1.5 py-0.5 rounded-full uppercase tracking-wider"
                                  :class="{'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': o.estado==='en_proceso', 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': o.estado==='pendiente'}"
                                  x-text="o.estado.replace('_',' ')">
                            </span>
                            <div class="text-[9px] text-gray-400 mt-0.5" x-text="o.dias+'d'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ── PERSONAL ── --}}
    @php
        $avBgs  = ['rgba(59,130,246,.12)','rgba(44,110,73,.12)','rgba(179,67,46,.12)','rgba(107,78,158,.12)','rgba(204,123,44,.12)','rgba(100,116,139,.12)'];
        $avCols = ['#3b82f6','#2c6e49','#b3432e','#6b4e9e','#cc7b2c','#64748b'];
    @endphp
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-1.5 font-semibold text-[12px] text-gray-900 dark:text-white">
                <i class="ti ti-id-badge text-gray-400"></i> Personal
            </div>
            <span class="text-[10px] text-gray-400">click para detalle</span>
        </div>
        <div class="grid grid-cols-[repeat(auto-fill,minmax(130px,1fr))] gap-2 p-3">
            @foreach($personal as $i => $p)
            @php $ini = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(explode(' ',$p['nombre']),0,2)))); @endphp
            <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl p-2.5 cursor-pointer hover:-translate-y-0.5 hover:shadow-sm hover:border-gray-300 dark:hover:border-gray-500 transition-all"
                 @click="openModal('personalItem', {{ $i }})">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold mb-2"
                     style="background:{{ $avBgs[$i%6] }};color:{{ $avCols[$i%6] }};">
                    {{ $ini }}
                </div>
                <div class="text-[11px] font-semibold text-gray-900 dark:text-white truncate">{{ $p['nombre'] }}</div>
                <div class="text-[9px] text-gray-400 truncate">{{ $p['sucursal'] }}</div>
                <div class="mt-1 flex items-center">
                    <span class="w-1.5 h-1.5 rounded-full inline-block mr-1" style="background:{{ $p['activo'] ? '#2c6e49' : '#94a3b8' }};"></span>
                    <span class="text-[9px] font-semibold uppercase tracking-wider text-gray-400">{{ $p['activo'] ? 'activo' : 'inactivo' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="text-right pb-1">
        <span class="text-[9px] text-gray-300 dark:text-gray-600">TTL · bicis 3600s · pedidos 3600s · piezas 300s · ventas 300s</span>
    </div>

    {{-- ── MODAL ── --}}
    <div x-show="modalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4"
         @click.self="modalOpen=false"
         @keydown.escape.window="modalOpen=false">
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700 z-10">
                <h3 class="text-[12px] font-semibold text-gray-900 dark:text-white flex items-center gap-2" x-html="modalTitle"></h3>
                <button @click="modalOpen=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 text-[11px] leading-relaxed text-gray-600 dark:text-gray-300" x-html="modalContent"></div>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const SSR = {
    biciStats:       @json($biciStats),
    stockVendedores: @json($stockVendedores),
    piezasBajas:     @json($piezasBajas),
    personal:        @json($personal),
    enlace: {
        estado:       '{{ in_array($enlace->estado ?? "", ["pendiente","activo"]) ? $enlace->estado : "" }}',
        cancelado:    {{ ($enlace->estado ?? '') === 'cancelado' ? 'true' : 'false' }},
        gestor:       '{{ addslashes($enlace->usuarioDestino->nombre_usuario ?? "") }}',
        gestorCorreo: '{{ addslashes($enlace->usuarioDestino->correo ?? "") }}',
        token:        '{{ $enlace->token_enlace ?? "" }}',
    },
    statsUrl:  '{{ route("admin.dashboard.stats") }}',
    csrfToken: '{{ csrf_token() }}',
};

const COLORS   = ['#3b82f6','#2c6e49','#b3432e','#6b4e9e','#cc7b2c','#64748b'];
const ICON_BG  = ['rgba(59,130,246,.1)','rgba(44,110,73,.1)','rgba(179,67,46,.1)','rgba(107,78,158,.1)'];
const ICON_COL = ['#3b82f6','#2c6e49','#b3432e','#6b4e9e'];
const AV_BG    = ['rgba(59,130,246,.12)','rgba(44,110,73,.12)','rgba(179,67,46,.12)','rgba(107,78,158,.12)','rgba(204,123,44,.12)','rgba(100,116,139,.12)'];
const AV_COL   = ['#3b82f6','#2c6e49','#b3432e','#6b4e9e','#cc7b2c','#64748b'];

const isDark = () => document.documentElement.classList.contains('dark');
function fmt$(n) { return '$' + Number(n).toLocaleString('es-MX', { maximumFractionDigits: 0 }); }
function pct(a, b) { return b > 0 ? Math.round(a / b * 100) : 0; }
function initials(n) { return (n || '').split(' ').filter(Boolean).map(w => w[0]).join('').toUpperCase().slice(0, 2); }
function badgeClass(e) { return { proceso: 'badge-blue', pendiente: 'badge-amber', completado: 'badge-green' }[e] || ''; }
function badgeTw(estado) {
    const map = {
        proceso:    'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        pendiente:  'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        completado: 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    };
    return map[estado] || 'bg-gray-100 text-gray-600';
}

Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, sans-serif";

const TIP = () => ({
    backgroundColor: isDark() ? 'rgba(15,23,42,0.96)' : 'rgba(255,255,255,0.98)',
    borderColor:     isDark() ? 'rgba(51,65,85,0.8)'  : 'rgba(226,232,240,0.9)',
    borderWidth: 1, padding: { x: 10, y: 8 }, cornerRadius: 12, displayColors: false,
    titleFont: { size: 11, weight: '600' }, bodyFont: { size: 10 },
    titleColor: isDark() ? '#f1f5f9' : '#0f172a',
    bodyColor:  isDark() ? '#cbd5e1' : '#475569',
});
const XAXIS = (t={}) => ({ grid:{ display:false }, border:{ display:false }, ticks:{ font:{ size:9 }, color: isDark()?'#64748b':'#94a3b8', maxRotation:0, ...t } });
const YAXIS = (t={}) => ({ grid:{ color: isDark()?'rgba(255,255,255,0.04)':'rgba(0,0,0,0.04)' }, border:{ display:false }, ticks:{ font:{ size:9 }, color: isDark()?'#64748b':'#94a3b8', maxTicksLimit:4, ...t } });

function makeGrad(canvas, top, bot) {
    const ctx = canvas.getContext('2d');
    const g   = ctx.createLinearGradient(0, 0, 0, canvas.parentElement?.clientHeight || 170);
    g.addColorStop(0, top); g.addColorStop(1, bot);
    return g;
}

function dashboard() {
    return {
        clock: '--:--',
        _clockTick() { this.clock = new Date().toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'}); setTimeout(()=>this._clockTick(), 30000); },
        periodo: 'today', customDesde: '', customHasta: '',
        presets: [{key:'today',label:'Hoy'},{key:'7d',label:'7 días'},{key:'30d',label:'30 días'},{key:'month',label:'Este mes'},{key:'custom',label:'Rango'}],
        get periodoLabel() { return {today:'Hoy','7d':'Últimos 7 días','30d':'Últimos 30 días',month:'Este mes',custom:'Personalizado'}[this.periodo]||''; },
        loading: true,

        // ── Estado ──
        kpiData:{}, graficaDias:[], pedidos:[], ots:[], ingresosVendedor:[],
        metodosPago:[], topModelos:[], detalleModelos:{}, topAccesorios:[],
        clientesTipo:[], horasPico:[],
        // Datos estadísticos (vienen del backend)
        regression: { slope:0, intercept:0, r2:0, equation:'', prediction:0, line:[], scatter:[] },
        movingAverage: [],
        heatmap: { matrix:[], matrix_raw:[], max:1 },
        heatmapHtml: '',

        get hasData() { return this.graficaDias.some(d => d.ventas > 0 || d.ingresos > 0); },

        get kpis() {
            const d = this.kpiData;
            if (d.ventas === undefined) return [];
            return [
                {key:'ventas',   label:'Ventas',        value:d.ventas,                        sub:'transacciones',    delta:'periodo actual',                    icon:'ti-receipt',       bg:'rgba(59,130,246,.1)',  color:'#3b82f6', modal:'ventasKPI'},
                {key:'ingresos', label:'Ingresos',       value:fmt$(d.ingresos),                sub:'en el periodo',    delta:d.descuentos>0?'desc. '+fmt$(d.descuentos):'sin descuentos', icon:'ti-currency-dollar', bg:'rgba(44,110,73,.1)',   color:'#2c6e49', modal:'ingresosKPI'},
                {key:'ticket',   label:'Ticket prom.',   value:fmt$(d.ticket),                  sub:'por venta',        delta:'promedio ponderado',                icon:'ti-ticket',        bg:'rgba(107,78,158,.1)', color:'#6b4e9e', modal:'ticketKPI'},
                {key:'clientes', label:'Clientes',       value:d.clientes,                      sub:'únicos en periodo', delta:(d.clientes_nuevos??0)+' nuevos · '+(d.clientes_rec??0)+' rec.', icon:'ti-users', bg:'rgba(204,123,44,.1)', color:'#cc7b2c', modal:'clientesTipo'},
                {key:'conv',     label:'Conversión',     value:Math.round((d.conversion||0)*100)+'%', sub:'pedidos → ventas', delta:'heurística',              icon:'ti-percent',       bg:'rgba(179,67,46,.1)',  color:'#b3432e', modal:'convKPI'},
                {key:'ots',      label:'OTs activas',    value:d.ots_activas,                   sub:'en taller ahora',  delta:'click para ver',                    icon:'ti-tools',         bg:'rgba(100,116,139,.1)',color:'#64748b', modal:'otsList'},
                {key:'stock',    label:'En stock',       value:SSR.biciStats.en_stock,          sub:'bicicletas',       delta:'inventario actual',                 icon:'ti-bike',          bg:'rgba(59,130,246,.1)', color:'#3b82f6', modal:'bicicletas'},
                {key:'pedidos',  label:'Pedidos activos',value:d.pedidos_activos,               sub:'pendientes+proceso',delta:'click para ver',                   icon:'ti-package',       bg:'rgba(44,110,73,.1)',  color:'#2c6e49', modal:'pedidosList'},
                {key:'desc',     label:'Descuentos',     value:fmt$(d.descuentos??0),           sub:'total otorgado',   delta:'en el periodo',                     icon:'ti-discount',      bg:'rgba(179,67,46,.1)',  color:'#b3432e', modal:'ingresosKPI'},
            ];
        },

        enlaceEstado: SSR.enlace.estado, enlaceCancelado: SSR.enlace.cancelado,
        enlaceGestor: SSR.enlace.gestor, enlaceOpen: false,
        modalOpen: false, modalTitle: '', modalContent: '',

        init() {
            this._clockTick();
            this.fetchStats();
            this.$nextTick(() => this._initDonut());
            this._listenEcho();
        },
        setPeriod(p) { this.periodo = p; if (p !== 'custom') this.fetchStats(); },

        async fetchStats() {
            this.loading = true;
            try {
                let url = SSR.statsUrl + '?periodo=' + this.periodo;
                if (this.periodo === 'custom' && this.customDesde && this.customHasta)
                    url = SSR.statsUrl + '?desde=' + this.customDesde + '&hasta=' + this.customHasta;

                const res  = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': SSR.csrfToken } });
                const data = await res.json();

                this.kpiData          = data.kpi            || {};
                this.graficaDias      = data.grafica         || [];
                this.pedidos          = data.pedidos?.recientes || [];
                this.ots              = data.ots             || [];
                this.ingresosVendedor = data.ingresos_vendedor || [];
                this.metodosPago      = data.metodos_pago    || [];
                this.topModelos       = data.top_modelos     || [];
                this.detalleModelos   = data.detalle_modelos || {};
                this.topAccesorios    = data.top_accesorios  || [];
                this.horasPico        = data.horas_pico      || [];
                this.clientesTipo     = data.clientes_tipo   || [];

                // ── Datos estadísticos del backend ──
                this.regression    = data.regression    || { slope:0, intercept:0, r2:0, equation:'', prediction:0, line:[], scatter:[] };
                this.movingAverage = data.moving_average || [];
                this.heatmap       = data.heatmap        || { matrix:[], matrix_raw:[], max:1 };

                this.$nextTick(() => {
                    this._renderVentasChart();
                    this._renderIngresosChart();
                    if (this.hasData) {
                        this._renderRegressionChart();
                        this._renderMovingAverageChart();
                        this._renderHeatmap();
                    }
                });
            } catch(e) { console.error(e); }
            finally    { this.loading = false; }
        },

        // ── Charts (solo renderizan, sin calcular) ──────────────────────────
        _ventasChart: null,
        _renderVentasChart() {
            const c = document.getElementById('ventas-chart'); if (!c) return;
            if (this._ventasChart) this._ventasChart.destroy();
            const labels = this.graficaDias.map(d => d.label);
            const data   = this.graficaDias.map(d => d.ventas);
            this._ventasChart = new Chart(c, {
                type: 'bar',
                data: { labels, datasets: [{ data, backgroundColor: data.map((_, i) => i === data.length-1 ? '#3b82f6' : 'rgba(59,130,246,0.2)'), hoverBackgroundColor: '#3b82f6', borderRadius: 6, barPercentage: 0.65, categoryPercentage: 0.8 }] },
                options: { responsive:true, maintainAspectRatio:false, animation:{ duration:700, easing:'easeOutQuart' }, plugins:{ legend:{ display:false }, tooltip:{ ...TIP(), callbacks:{ title:([c])=>labels[c.dataIndex]||'', label:ctx=>`  ${ctx.parsed.y} ventas` } } }, scales:{ x: XAXIS(), y: YAXIS() } },
            });
        },

        _ingresosChart: null,
        _renderIngresosChart() {
            const c = document.getElementById('ingresos-chart'); if (!c) return;
            if (this._ingresosChart) this._ingresosChart.destroy();
            const labels = this.graficaDias.map(d => d.label);
            const data   = this.graficaDias.map(d => d.ingresos);
            const grad   = makeGrad(c, 'rgba(44,110,73,0.4)', 'rgba(44,110,73,0.02)');
            this._ingresosChart = new Chart(c, {
                type: 'line',
                data: { labels, datasets: [{ data, borderColor:'#2c6e49', borderWidth:2.5, backgroundColor:grad, fill:true, tension:0.4, pointRadius:0, pointHoverRadius:5, pointHoverBackgroundColor:'#2c6e49', pointHoverBorderColor:'#fff', pointHoverBorderWidth:2 }] },
                options: { responsive:true, maintainAspectRatio:false, animation:{ duration:800, easing:'easeOutQuart' }, plugins:{ legend:{ display:false }, tooltip:{ ...TIP(), callbacks:{ title:([c])=>labels[c.dataIndex]||'', label:ctx=>`  ${fmt$(Math.round(ctx.parsed.y))}` } } }, scales:{ x: XAXIS(), y: YAXIS({ callback:v=>'$'+(v>=1000?Math.round(v/1000)+'k':v) }) } },
            });
        },

        _regressionChart: null,
        _renderRegressionChart() {
            const canvas = document.getElementById('regression-chart'); if (!canvas) return;
            if (this._regressionChart) this._regressionChart.destroy();
            const { scatter, line } = this.regression;
            const labels = scatter.map(p => p.x);
            this._regressionChart = new Chart(canvas, {
                type: 'scatter',
                data: { datasets: [
                    { label:'Ingreso diario', data: scatter, type:'scatter', backgroundColor:'rgba(59,130,246,0.6)', pointRadius:3.5, pointHoverRadius:6, borderWidth:0 },
                    { label:'Tendencia', data: line.map((y, x) => ({ x, y })), type:'line', borderColor:'#cc7b2c', borderWidth:2.5, borderDash:[6,4], fill:false, pointRadius:0, tension:0 },
                ]},
                options: { responsive:true, maintainAspectRatio:true, animation:{ duration:800 }, plugins:{ legend:{ position:'bottom', labels:{ font:{ size:9 }, boxWidth:12, padding:6, color: isDark()?'#64748b':'#94a3b8' } }, tooltip:{ ...TIP(), callbacks:{ label:c=>`  ${c.dataset.label}: ${fmt$(Math.round(c.parsed.y))}` } } }, scales:{ x: XAXIS(), y: YAXIS({ callback:v=>fmt$(v) }) } },
            });
        },

        _maChart: null,
        _renderMovingAverageChart() {
            const canvas = document.getElementById('moving-average-chart'); if (!canvas) return;
            if (this._maChart) this._maChart.destroy();
            const labels  = this.graficaDias.map((d, i) => d.label || `Día ${i+1}`);
            const incomes = this.graficaDias.map(d => d.ingresos || 0);
            const grad    = makeGrad(canvas, 'rgba(59,130,246,0.25)', 'rgba(59,130,246,0.01)');
            this._maChart = new Chart(canvas, {
                type: 'line',
                data: { labels, datasets: [
                    { label:'Ingresos diarios', data: incomes, borderColor:'#3b82f6', borderWidth:2, backgroundColor:grad, fill:true, tension:0.3, pointRadius:0, pointHoverRadius:5 },
                    { label:'Media móvil 7 días', data: this.movingAverage, borderColor:'#cc7b2c', borderWidth:2, borderDash:[8,5], fill:false, tension:0.4, pointRadius:0 },
                ]},
                options: { responsive:true, maintainAspectRatio:false, animation:{ duration:800 }, plugins:{ legend:{ position:'bottom', labels:{ font:{ size:9 }, boxWidth:12, padding:6, color: isDark()?'#64748b':'#94a3b8' } }, tooltip:{ ...TIP(), callbacks:{ title:([c])=>labels[c.dataIndex]||'', label:ctx=>`  ${ctx.dataset.label}: ${fmt$(Math.round(ctx.parsed.y))}` } } }, scales:{ x: XAXIS({ maxTicksLimit:8 }), y: YAXIS({ callback:v=>fmt$(v) }) } },
            });
        },

        _renderHeatmap() {
            const { matrix, matrix_raw, max } = this.heatmap;
            if (!matrix.length) return;
            const days = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
            const dark = isDark();

            // Convierte valor normalizado 0-1 a color azul
            const toColor = v => {
                if (!v) return dark ? 'rgba(51,65,85,0.3)' : '#f1f5f9';
                const r = Math.round(219 + (30  - 219) * v);
                const g = Math.round(234 + (58  - 234) * v);
                const b = Math.round(254 + (138 - 254) * v);
                return `rgb(${r},${g},${b})`;
            };

            const hours = Array.from({length:24}, (_, h) =>
                `<div class="text-center text-[9px] text-gray-400">${h%3===0 ? h+'h' : ''}</div>`
            ).join('');

            const rows = days.map((day, d) => `
                <div style="display:grid;grid-template-columns:32px repeat(24,1fr);gap:2px;margin-bottom:3px;">
                    <div class="text-[10px] font-medium text-gray-400 flex items-center justify-end pr-2">${day}</div>
                    ${Array.from({length:24}, (_, h) => `
                        <div style="height:28px;border-radius:6px;background:${toColor(matrix[d]?.[h]??0)};cursor:pointer;transition:transform .15s"
                             title="${day} ${h}:00 — ${matrix_raw[d]?.[h]??0} ventas"
                             onmouseover="this.style.transform='scale(1.08)'"
                             onmouseout="this.style.transform='scale(1)'">
                        </div>
                    `).join('')}
                </div>
            `).join('');

            const legend = [0, 0.25, 0.5, 0.75, 1].map(v =>
                `<div style="width:20px;height:10px;border-radius:3px;background:${toColor(v)};"></div>`
            ).join('');

            this.heatmapHtml = `
                <div style="overflow-x:auto;">
                    <div style="min-width:480px;">
                        <div style="display:grid;grid-template-columns:32px repeat(24,1fr);gap:2px;margin-bottom:6px;">
                            <div></div>${hours}
                        </div>
                        ${rows}
                        <div class="flex items-center justify-end gap-1.5 mt-3">
                            <span class="text-[9px] text-gray-400">Menos</span>${legend}<span class="text-[9px] text-gray-400">Más</span>
                        </div>
                    </div>
                </div>`;
        },

        _donutInited: false,
        _initDonut() {
            if (this._donutInited) return;
            const c = document.getElementById('donut-chart'); if (!c) return;
            const d = SSR.biciStats;
            new Chart(c, {
                type: 'doughnut',
                data: { labels:['En stock','Vendidas','En reparación'], datasets:[{ data:[d.en_stock, d.vendidas, d.en_reparacion], backgroundColor:['#3b82f6','#2c6e49','#b3432e'], borderWidth:0, hoverOffset:6 }] },
                options: { responsive:false, cutout:'72%', animation:{ duration:700 }, plugins:{ legend:{ display:false }, tooltip:{ ...TIP(), callbacks:{ label:l=>`  ${l.label}: ${l.parsed}` } } } },
            });
            this._donutInited = true;
        },

        // ── MODAL BUILDER ───────────────────────────────────────────────────
        openModal(type, idx=0) {
            let title='', content='';
            const d            = this.kpiData;
            const totalMonto   = this.metodosPago.reduce((s,m) => s+m.monto, 0);
            const METODO_ICON  = {efectivo:'ti-cash',tarjeta:'ti-credit-card',transferencia:'ti-arrows-exchange',credito_interno:'ti-building-bank'};
            const METODO_COLOR = {efectivo:'#2c6e49',tarjeta:'#3b82f6',transferencia:'#6b4e9e',credito_interno:'#cc7b2c'};

            if (type==='ingresosKPI') {
                title = '<i class="ti ti-currency-dollar"></i> Ingresos del periodo';
                content = this._stats([
                    {v:fmt$(d.ingresos),l:'Total ingresos',col:'#2c6e49'},
                    {v:fmt$(d.descuentos??0),l:'Descuentos dados',col:'#b3432e'},
                    {v:fmt$((d.ingresos||0)+(d.descuentos||0)),l:'Precio de lista'},
                    {v:d.ventas,l:'Ventas'},
                ]) + this._sec('Aporte por sucursal')
                + this.ingresosVendedor.map((v,i) => this._bar(v.nombre, v.monto, this.ingresosVendedor[0]?.monto||1, COLORS[i%6], '', fmt$)).join('')
                + this._sec('Métodos de pago')
                + this.metodosPago.map((m,i) => {
                    const ic=METODO_ICON[m.metodo]||'ti-cash', col=METODO_COLOR[m.metodo]||COLORS[i%6];
                    return `<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;background:${col}08;border-radius:12px;padding:8px 12px;">
                        <div style="width:28px;height:28px;border-radius:10px;background:${col}18;display:flex;align-items:center;justify-content:center;">
                            <i class="ti ${ic}" style="font-size:13px;color:${col};"></i>
                        </div>
                        <div style="flex:1;"><div style="font-size:12px;font-weight:500;">${m.metodo.replace('_',' ')}</div>
                        <div style="font-size:9px;color:#94a3b8;">${m.usos} uso${m.usos!==1?'s':''} · ${pct(m.monto,totalMonto)}% del total</div></div>
                        <span style="font-size:13px;font-weight:600;">${fmt$(m.monto)}</span>
                    </div>`;
                }).join('');
            }
            else if (type==='ventasKPI') {
                title = '<i class="ti ti-receipt"></i> Análisis de ventas';
                content = this._stats([
                    {v:d.ventas,l:'Total ventas',col:'#3b82f6'},{v:fmt$(d.ticket),l:'Ticket promedio'},
                    {v:d.clientes,l:'Clientes únicos'},{v:Math.round((d.conversion||0)*100)+'%',l:'Conversión'},
                ]) + this._sec('Pico de ventas')
                + (()=>{
                    const sorted = [...this.horasPico].sort((a,b)=>b.cnt-a.cnt).slice(0,3);
                    if (!sorted.length) return `<p class="text-gray-400">Sin datos</p>`;
                    return sorted.map(h=>`<div class="text-xs mb-1"><strong>${h.hora}:00 – ${h.hora+1}:00 h</strong> — ${h.cnt} venta${h.cnt!==1?'s':''}</div>`).join('');
                })();
            }
            else if (type==='ticketKPI') {
                title = '<i class="ti ti-ticket"></i> Ticket promedio';
                content = this._stats([
                    {v:fmt$(d.ticket),l:'Ticket del periodo',col:'#6b4e9e'},{v:d.ventas,l:'Ventas totales'},{v:fmt$(d.ingresos),l:'Ingresos totales'},
                ]) + this._sec('Por sucursal')
                + this.ingresosVendedor.map((v,i) => this._bar(v.nombre, v.ticket, Math.max(...this.ingresosVendedor.map(x=>x.ticket),1), COLORS[i%6], '', fmt$)).join('');
            }
            else if (type==='convKPI') {
                const conv = Math.round((d.conversion||0)*100);
                const col  = conv>=70?'#2c6e49':conv>=50?'#cc7b2c':'#b3432e';
                title   = '<i class="ti ti-percent"></i> Tasa de conversión';
                content = this._stats([{v:conv+'%',l:'Conversión',col},{v:d.ventas,l:'Ventas concretadas'},{v:d.pedidos_activos,l:'Pedidos sin cerrar'}])
                    + `<div style="background:${conv>=70?'rgba(44,110,73,.08)':conv>=50?'rgba(204,123,44,.08)':'rgba(179,67,46,.08)'};border-radius:12px;padding:12px;">
                        <p style="font-weight:600;color:${col};">${conv>=70?'✓ Buena conversión':conv>=50?'⚠ Conversión media':'✗ Conversión baja'}</p>
                        <p style="font-size:10px;color:#94a3b8;margin-top:4px;">${conv>=70?'Más del 70% de los pedidos se convierten.':conv>=50?'Hay oportunidad de mejorar el seguimiento.':'Revisa el proceso de cierre de pedidos.'}</p>
                    </div>`;
            }
            else if (type==='metodosPago') {
                title   = '<i class="ti ti-credit-card"></i> Métodos de pago';
                content = this._stats([{v:fmt$(totalMonto),l:'Total cobrado'},{v:this.metodosPago.reduce((s,m)=>s+m.usos,0),l:'Transacciones'}])
                    + this.metodosPago.map((m,i) => {
                        const ic=METODO_ICON[m.metodo]||'ti-cash', col=METODO_COLOR[m.metodo]||COLORS[i%6], share=pct(m.monto,totalMonto);
                        return `<div style="background:${col}08;border-radius:14px;padding:10px 12px;margin-bottom:10px;">
                            <div style="display:flex;align-items:center;gap:12px;"><div style="width:32px;height:32px;border-radius:12px;background:${col}18;display:flex;align-items:center;justify-content:center;"><i class="ti ${ic}" style="font-size:14px;color:${col};"></i></div>
                            <div><div style="font-size:12px;font-weight:500;">${m.metodo.replace('_',' ')}</div><div style="font-size:9px;color:#94a3b8;">${m.usos} uso${m.usos!==1?'s':''}</div></div>
                            <div style="margin-left:auto;text-align:right;"><div style="font-weight:600;">${fmt$(m.monto)}</div><div style="font-size:9px;color:${col};">${share}%</div></div></div>
                            <div style="margin-top:8px;height:6px;background:rgba(0,0,0,0.06);border-radius:99px;"><div style="width:${share}%;height:100%;background:${col};border-radius:99px;"></div></div>
                        </div>`;
                    }).join('');
            }
            else if (type==='clientesTipo') {
                const nuevos=d.clientes_nuevos??0, rec=d.clientes_rec??0, total=d.clientes||1;
                title   = '<i class="ti ti-users"></i> Análisis de clientes';
                content = this._stats([
                    {v:d.clientes,l:'Clientes únicos'},{v:nuevos,l:'Nuevos',col:'#3b82f6'},
                    {v:rec,l:'Recurrentes',col:'#2c6e49'},{v:pct(rec,total)+'%',l:'Tasa retención',col:'#2c6e49'},
                ]) + `<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div style="background:rgba(59,130,246,.07);border-radius:16px;padding:12px;text-align:center;"><div style="font-size:24px;font-weight:600;color:#3b82f6;">${nuevos}</div><div style="font-size:11px;color:#94a3b8;margin-top:4px;">Nuevos</div><div style="font-size:10px;color:#3b82f6;">${pct(nuevos,total)}%</div></div>
                    <div style="background:rgba(44,110,73,.07);border-radius:16px;padding:12px;text-align:center;"><div style="font-size:24px;font-weight:600;color:#2c6e49;">${rec}</div><div style="font-size:11px;color:#94a3b8;margin-top:4px;">Recurrentes</div><div style="font-size:10px;color:#2c6e49;">${pct(rec,total)}%</div></div>
                </div>`;
            }
            else if (type==='sucursalDetalle') {
                const v=this.ingresosVendedor[idx], totalIngs=this.ingresosVendedor.reduce((s,x)=>s+x.monto,0);
                title   = `<span style="width:28px;height:28px;border-radius:12px;background:${AV_BG[idx%6]};color:${AV_COL[idx%6]};display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:6px;">${initials(v.nombre)}</span>${v.nombre}`;
                content = this._stats([{v:fmt$(v.monto),l:'Ingresos',col:COLORS[0]},{v:v.ventas,l:'Ventas'},{v:fmt$(v.ticket),l:'Ticket prom.'},{v:pct(v.monto,totalIngs)+'%',l:'Del total'}])
                    + this._sec('Ranking') + this.ingresosVendedor.map((x,i) => this._bar(x.nombre, x.monto, this.ingresosVendedor[0].monto||1, x.nombre===v.nombre?COLORS[0]:COLORS[(i+2)%6], x.nombre===v.nombre?'font-weight:600;':'', fmt$)).join('');
            }
            else if (type==='modeloDetalle') {
                const m=this.topModelos[idx], det=this.detalleModelos[m.id_modelo]||{colores:[],voltajes:[]};
                title   = `<i class="ti ti-bike"></i> ${m.nombre}`;
                content = this._stats([{v:m.unidades,l:'Unidades vendidas',col:'#3b82f6'},{v:fmt$(m.ingresos),l:'Ingresos generados'},{v:m.unidades>0?fmt$(Math.round(m.ingresos/m.unidades)):'—',l:'Precio prom.'}]);
                if (det.colores.length) { content+=this._sec('Colores'); content+=det.colores.map((c,i)=>this._bar(c.color,c.cnt,det.colores[0]?.cnt||1,COLORS[i%6])).join(''); }
                if (det.voltajes.length) { content+=this._sec('Voltajes'); content+=det.voltajes.map((v,i)=>this._bar(v.voltaje+'V',v.cnt,det.voltajes[0]?.cnt||1,COLORS[i%6])).join(''); }
            }
            else if (type==='bicicletas') {
                const d2=SSR.biciStats;
                title   = '<i class="ti ti-bike"></i> Inventario de bicicletas';
                content = this._stats([{v:d2.en_stock,l:'En stock',col:'#3b82f6'},{v:d2.vendidas,l:'Vendidas',col:'#2c6e49'},{v:d2.en_reparacion,l:'En reparación',col:'#b3432e'},{v:d2.total,l:'Total'}])
                    + this._sec('Por vendedor') + SSR.stockVendedores.map((v,i)=>this._bar(v.nombre,v.total,SSR.stockVendedores[0]?.total||1,COLORS[i%6])).join('');
            }
            else if (type==='pedidosList') {
                title   = '<i class="ti ti-package"></i> Pedidos recientes';
                content = this._stats([{v:this.pedidos.filter(p=>p.estado==='pendiente').length,l:'Pendientes'},{v:this.pedidos.filter(p=>p.estado==='proceso').length,l:'En proceso'},{v:fmt$(this.pedidos.reduce((s,p)=>s+p.monto,0)),l:'Monto total'}])
                    + this.pedidos.map((p,i) => `<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.06);cursor:pointer;" onclick="">
                        <div style="font-size:12px;flex:1;"><strong>${p.id}</strong> — ${p.cliente}<div style="font-size:10px;color:#94a3b8;">${fmt$(p.monto)} · ${p.fecha}</div></div>
                        <span style="font-size:9px;font-weight:600;padding:2px 8px;border-radius:99px;background:{proceso:'rgba(59,130,246,.1)',pendiente:'rgba(204,123,44,.1)',completado:'rgba(44,110,73,.1)'}[p.estado]||'rgba(0,0,0,.05)'};color:{proceso:'#3b82f6',pendiente:'#cc7b2c',completado:'#2c6e49'}[p.estado]||'#64748b';">${p.estado}</span>
                    </div>`).join('')
            }
            else if (type==='pedidoItem') {
                const p=this.pedidos[idx];
                title   = `<i class="ti ti-clipboard-list"></i> ${p.id}`;
                content = this._stats([{v:fmt$(p.monto),l:'Total'},{v:p.items,l:'Artículos'}])
                    + this._table([['Cliente',p.cliente],['Fecha',p.fecha],['Folio',p.id]]);
            }
            else if (type==='piezaItem') {
                const p=SSR.piezasBajas[idx], falt=Math.max(0,p.minimo-p.stock);
                title   = `<i class="ti ti-tool"></i> ${p.nombre}`;
                content = this._stats([{v:p.stock,l:'Stock actual',col:p.stock===0?'#b3432e':'#cc7b2c'},{v:p.minimo,l:'Mínimo'},{v:falt,l:'Faltante',col:falt>0?'#b3432e':'#2c6e49'}])
                    + this._table([['Clave',p.clave],['Categoría',p.cat]]);
            }
            else if (type==='otsList') {
                title   = '<i class="ti ti-tools"></i> Órdenes de trabajo activas';
                content = this._stats([{v:this.ots.length,l:'Activas'},{v:this.ots.filter(o=>o.estado==='en_proceso').length,l:'En proceso'},{v:this.ots.filter(o=>o.estado==='pendiente').length,l:'Pendientes'}])
                    + this.ots.map((o,i) => `<div style="display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.06);">
                        <div style="width:6px;height:6px;border-radius:50%;background:${COLORS[i%6]};flex-shrink:0;"></div>
                        <div style="flex:1;font-size:12px;"><strong>${o.id}</strong> <span style="color:#94a3b8;font-size:10px;">${o.dias}d</span><div style="font-size:10px;color:#94a3b8;">${o.tipo} — ${o.modelo}</div></div>
                    </div>`).join('');
            }
            else if (type==='otItem') {
                const o=this.ots[idx], diasCol=o.dias>5?'#b3432e':o.dias>2?'#cc7b2c':'#2c6e49';
                title   = `<i class="ti ti-tools"></i> ${o.id}`;
                content = this._stats([{v:o.dias,l:'Días en taller',col:diasCol},{v:o.estado.replace('_',' '),l:'Estado'}])
                    + this._table([['Tipo',o.tipo],['Modelo',o.modelo],['Días',o.dias]])
                    + (o.dias>5?`<div style="margin-top:12px;padding:12px;background:rgba(179,67,46,.08);border-radius:12px;color:#b3432e;font-size:12px;">Esta OT lleva más de 5 días. Considera darle prioridad.</div>`:'');
            }
            else if (type==='personalItem') {
                const p=SSR.personal[idx], iv=this.ingresosVendedor.find(v=>v.nombre===p.nombre)||{monto:0,ventas:0,ticket:0};
                title   = `<span style="width:28px;height:28px;border-radius:12px;background:${AV_BG[idx%6]};color:${AV_COL[idx%6]};display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:8px;">${initials(p.nombre)}</span>${p.nombre}`;
                content = this._stats([{v:iv.ventas,l:'Ventas',col:COLORS[0]},{v:fmt$(iv.monto),l:'Ingresos',col:COLORS[1]},{v:iv.ticket?fmt$(iv.ticket):'—',l:'Ticket prom.'},{v:`<span style="color:${p.activo?'#2c6e49':'#94a3b8'};">${p.activo?'Activo':'Inactivo'}</span>`,l:'Estado'}])
                    + this._table([['Sucursal',p.sucursal]])
                    + (this.ingresosVendedor.length > 1 ? this._sec('vs equipo') + this.ingresosVendedor.map((v,i)=>this._bar(v.nombre,v.ventas,this.ingresosVendedor[0].ventas||1,v.nombre===p.nombre?COLORS[0]:COLORS[(i+2)%6],v.nombre===p.nombre?'font-weight:600;':'')).join('') : '');
            }

            if (!title) return;
            this.modalTitle   = title;
            this.modalContent = content;
            this.modalOpen    = true;
        },

        // ── Helpers para el modal ────────────────────────────────────────────
        _stats(items) {
            return `<div style="display:grid;grid-template-columns:repeat(${Math.min(items.length,4)},1fr);gap:8px;margin-bottom:20px;">`
                + items.map(it => `<div style="background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.06);border-radius:12px;padding:10px;text-align:center;">
                    <div style="font-size:18px;font-weight:600;color:${it.col||'inherit'};">${it.v}</div>
                    <div style="font-size:9px;color:#94a3b8;margin-top:4px;text-transform:uppercase;letter-spacing:.06em;">${it.l}</div>
                </div>`).join('') + '</div>';
        },
        _sec(l) { return `<div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin:16px 0 8px;">${l}</div>`; },
        _table(rows) {
            return `<table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:16px;">`
                + rows.map(([k,v]) => `<tr><td style="padding:6px 0;color:#94a3b8;border-bottom:1px solid rgba(0,0,0,0.06);">${k}</td><td style="padding:6px 0;text-align:right;font-weight:500;border-bottom:1px solid rgba(0,0,0,0.06);">${v}</td></tr>`).join('')
                + '</table>';
        },
        _bar(label, val, max, color, extra='', fmtFn=null) {
            const p    = max > 0 ? Math.min(100, Math.round(val/max*100)) : 0;
            const disp = fmtFn ? fmtFn(val) : val;
            return `<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <div style="width:80px;font-size:11px;color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;${extra}">${label}</div>
                <div style="flex:1;height:6px;background:rgba(0,0,0,0.06);border-radius:99px;">
                    <div style="width:${p}%;height:100%;background:${color};border-radius:99px;"></div>
                </div>
                <div style="font-size:11px;font-weight:500;color:#94a3b8;text-align:right;min-width:48px;">${disp}</div>
            </div>`;
        },

        _listenEcho() {
            if (!window.Echo) return;
            window.Echo.private(`enlace-vendedor.{{ auth()->user()->id_usuario }}`).listen('.enlace.updated', ({action, enlace}) => {
                if (action==='aceptado')  { this.enlaceCancelado=false; this.enlaceEstado='activo';  this.enlaceGestor=enlace.gestor; }
                if (action==='cancelado') { this.enlaceCancelado=true;  this.enlaceEstado='';        this.enlaceGestor=''; }
            });
        },
    };
}
</script>
</div>
</x-app-layout>