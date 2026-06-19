<x-app-layout>
<div class="space-y-0">

<div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100"
    x-data="dashboard()"
    x-init="init()"
>

    {{-- ── TOPBAR ── --}}
    <div class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-8 py-4 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-[17px] font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                Hola, {{ auth()->user()->negocio->nombre_negocio ?? auth()->user()->name ?? 'Admin' }}
            </h1>
            <p class="text-[11px] text-gray-400 dark:text-gray-400 mt-0.5" x-text="clock"></p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1">
                <template x-for="p in presets.filter(x=>x.key!=='custom')" :key="p.key">
                    <button
                        class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all"
                        :class="periodo===p.key
                            ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        @click="setPeriod(p.key)"
                        x-text="p.label">
                    </button>
                </template>
                <button
                    class="text-[11px] font-medium px-3.5 py-1.5 rounded-lg transition-all"
                    :class="periodo==='custom'
                        ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-200 dark:ring-gray-600'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    @click="setPeriod('custom')">
                    Rango
                </button>
            </div>
            <div x-show="periodo==='custom'" x-cloak class="flex items-center gap-2">
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
                    class="inline-flex items-center gap-2 text-[11px] font-medium px-3 py-1.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500 transition-all">
                <span class="w-1.5 h-1.5 rounded-full shrink-0"
                      :class="enlaceEstado==='activo' ? 'bg-emerald-500' : enlaceCancelado ? 'bg-red-400' : 'bg-gray-400'"></span>
                <span x-text="enlaceEstado==='activo' ? enlaceGestor : enlaceCancelado ? 'Desconectado' : 'Enlace'"></span>
            </button>
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

    {{-- ── MAIN GRID ── --}}
    <div class="max-w-screen-2xl mx-auto px-6 py-6 grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">

        {{-- ── LEFT COLUMN ── --}}
        <div class="space-y-6">

            {{-- KPIs principales --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Panel general</p>
                    <p class="text-[11px] text-gray-400" x-text="periodoLabel"></p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <template x-if="loading">
                        <template x-for="i in 4" :key="i">
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 animate-pulse">
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-2/3 mb-5"></div>
                                <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-4/5 mb-3"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            </div>
                        </template>
                    </template>
                    <template x-if="!loading">
                        <template x-for="(k, ki) in kpis.slice(0,4)" :key="k.key">
                            <div class="relative overflow-hidden rounded-2xl p-5 cursor-pointer transition-all shadow-sm hover:shadow-md border"
                                 :class="kpiCardClass(ki)"
                                 @click="openModal(k.modal)">
                                <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full opacity-[0.12]"
                                     :class="kpiCircleClass(ki)"></div>
                                <p class="text-[10px] font-semibold uppercase tracking-widest mb-3.5 relative z-10"
                                   :class="kpiLabelClass(ki)" x-text="k.label"></p>
                                <p class="font-semibold tracking-tight leading-none mb-2.5 relative z-10"
                                   :class="[k.value && k.value.toString().length > 6 ? 'text-[22px]' : 'text-[28px]', kpiValueClass(ki)]"
                                   x-text="k.value"></p>
                                <p class="text-[11px] leading-snug relative z-10" :class="kpiSubClass(ki)" x-text="k.sub"></p>
                            </div>
                        </template>
                    </template>
                </div>
            </div>

            {{-- Ingresos chart --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700/60">
                    <div>
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Ingresos</p>
                        <p class="text-[11px] text-gray-400 mt-1" x-text="periodoLabel"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[26px] font-semibold tracking-tight leading-none text-gray-900 dark:text-gray-100"
                           x-text="kpiData.ingresos ? fmt$(kpiData.ingresos) : '—'"></p>
                        <template x-if="kpiData.descuentos > 0">
                            <p class="text-[10px] text-gray-400 mt-1" x-text="'– '+fmt$(kpiData.descuentos)+' en descuentos'"></p>
                        </template>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-4">
                    <div id="ingresos-chart-wrap" style="min-height:220px;"></div>
                </div>
            </div>

            {{-- Ventas chart --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700/60">
                    <div>
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Ventas</p>
                        <p class="text-[11px] text-gray-400 mt-1" x-text="periodoLabel"></p>
                    </div>
                    <p class="text-[26px] font-semibold tracking-tight leading-none text-gray-900 dark:text-gray-100"
                       x-text="kpiData.ventas ?? '—'"></p>
                </div>
                <div class="px-5 pb-5 pt-4">
                    <div id="ventas-chart-wrap" style="min-height:220px;"></div>
                </div>
            </div>

            {{-- Sucursales chart --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700/60">
                    <div>
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Ingresos por sucursal</p>
                        <p class="text-[11px] text-gray-400 mt-1" x-text="periodoLabel"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div x-show="!loading && graficaSucursales.length > 0" x-cloak class="flex gap-4 flex-wrap justify-end">
                            <template x-for="(s, i) in graficaSucursales" :key="s.nombre">
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-500 dark:text-gray-400">
                                    <svg width="18" height="6" viewBox="0 0 18 6">
                                        <line x1="0" y1="3" x2="18" y2="3"
                                              :stroke="COLORS[i%6]" stroke-width="2"
                                              :stroke-dasharray="i===0 ? 'none' : '4,3'"/>
                                    </svg>
                                    <span x-text="s.nombre"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1 ml-2">
                            <button class="text-[10px] font-semibold px-2.5 py-1 rounded-lg transition-all"
                                    :class="sucursalMetrica==='ingresos' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'"
                                    @click="sucursalMetrica='ingresos'; $nextTick(()=>_renderSucursalesChart())">$</button>
                            <button class="text-[10px] font-semibold px-2.5 py-1 rounded-lg transition-all"
                                    :class="sucursalMetrica==='ventas' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400'"
                                    @click="sucursalMetrica='ventas'; $nextTick(()=>_renderSucursalesChart())">#</button>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-4" x-show="!loading && graficaSucursales.length > 0" x-cloak>
                    <div id="sucursales-chart-wrap" style="min-height:220px;"></div>
                </div>
                <template x-if="!loading && graficaSucursales.length === 0">
                    <div class="flex flex-col items-center py-10">
                        <p class="text-[12px] text-gray-400">Sin datos en este periodo</p>
                    </div>
                </template>
                <div class="px-5 pb-5 pt-4" x-show="loading" x-cloak>
                    <div class="h-[220px] bg-gray-100 dark:bg-gray-700 rounded-xl animate-pulse"></div>
                </div>
            </div>

            {{-- Bottom grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Métodos de pago --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('metodosPago')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Métodos de pago</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="metodosPago.length === 0">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin datos</p>
                        </template>
                        <template x-for="(m, i) in metodosPago.slice(0, 4)" :key="m.metodo">
                            <div class="flex items-center gap-3 mb-3.5">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[100px] shrink-0 truncate capitalize" x-text="m.metodo.replace('_',' ')"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-gray-900 dark:bg-gray-300"
                                         :style="{width: metodosPago[0].monto > 0 ? Math.round(m.monto/metodosPago[0].monto*100)+'%' : '0%', opacity: 1 - i*0.2}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-900 dark:text-gray-100 text-right min-w-[64px]" x-text="fmt$(m.monto)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between pt-3 mt-1 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-[11px] text-gray-400">Total cobrado</span>
                            <span class="text-[13px] font-semibold text-gray-900 dark:text-gray-100" x-text="fmt$(metodosPago.reduce((s,m)=>s+m.monto,0))"></span>
                        </div>
                    </div>
                </div>

                {{-- Clientes --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('clientesTipo')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Clientes</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <div class="flex items-baseline gap-1.5 mb-5">
                            <p class="text-[32px] font-semibold tracking-tight leading-none text-gray-900 dark:text-gray-100" x-text="kpiData.clientes ?? '—'"></p>
                            <p class="text-[11px] text-gray-400">únicos</p>
                        </div>
                        <template x-for="(c, i) in clientesTipo" :key="c.tipo">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[80px] shrink-0" x-text="c.tipo"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-gray-900 dark:bg-gray-300"
                                         :style="{width: kpiData.clientes > 0 ? Math.round(c.cnt/kpiData.clientes*100)+'%' : '0%', opacity: i===0 ? 1 : 0.35}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 min-w-[28px] text-right" x-text="c.cnt"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Tops: modelo, config, accesorio, combo --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('topModelos')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Modelos más vendidos</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="!tops.modelo">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin ventas</p>
                        </template>
                        <template x-if="tops.modelo">
                            <div>
                                <p class="text-[22px] font-semibold tracking-tight text-gray-900 dark:text-gray-100" x-text="tops.modelo.nombre"></p>
                                <p class="text-[11px] text-gray-400 mt-1" x-text="tops.modelo.unidades + ' unidades vendidas'"></p>
                                <template x-if="tops.config">
                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Config. más vendida</p>
                                        <p class="text-[12px] font-medium text-gray-700 dark:text-gray-300" x-text="tops.config.config"></p>
                                        <p class="text-[10px] text-gray-400" x-text="tops.config.unidades + ' veces'"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Accesorios y combos --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('topAccesorios')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Accesorios y combos</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="tops.accesorio">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Accesorio top</p>
                                <p class="text-[18px] font-semibold text-gray-900 dark:text-gray-100" x-text="tops.accesorio.nombre"></p>
                                <p class="text-[11px] text-gray-400" x-text="tops.accesorio.uds + ' unidades'"></p>
                            </div>
                        </template>
                        <template x-if="tops.combo">
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Combo más frecuente</p>
                                <p class="text-[13px] font-medium text-gray-700 dark:text-gray-300" x-text="tops.combo.combo"></p>
                                <p class="text-[10px] text-gray-400" x-text="tops.combo.uds + ' veces'"></p>
                            </div>
                        </template>
                        <template x-if="!tops.accesorio && !tops.combo">
                            <p class="text-center text-gray-400 text-[11px] py-3">Sin ventas de accesorios</p>
                        </template>
                    </div>
                </div>

                {{-- Cupones --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('cupones')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Cupones</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <div class="flex items-baseline gap-1.5 mb-4">
                            <p class="text-[28px] font-semibold tracking-tight text-gray-900 dark:text-gray-100" x-text="fmt$(kpiData.cupones_descuento ?? 0)"></p>
                            <p class="text-[11px] text-gray-400">en descuentos</p>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Cupones usados</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="kpiData.cupones_usados ?? 0"></span>
                        </div>
                        <template x-if="tops.cupon">
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Cupón más usado</p>
                                <p class="text-[13px] font-medium text-gray-700 dark:text-gray-300" x-text="tops.cupon.codigo"></p>
                                <p class="text-[10px] text-gray-400" x-text="tops.cupon.usos + ' usos'"></p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Sucursales aporte --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('sucursales')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Aporte por sucursal</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5" x-show="!loading">
                        <template x-if="sucursales.length === 0">
                            <p class="text-center text-gray-400 text-[11px]">Sin ventas</p>
                        </template>
                        <template x-for="(v, i) in sucursales" :key="v.id_usuario">
                            <div class="flex items-center gap-3 mb-3.5">
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[80px] shrink-0 truncate" x-text="v.nombre"></span>
                                <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 bg-gray-900 dark:bg-gray-300"
                                         :style="{width: sucursales[0].ingresos_total > 0 ? Math.round(v.ingresos_total/sucursales[0].ingresos_total*100)+'%' : '0%', opacity: 1 - i*0.25}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 text-right min-w-[64px]" x-text="fmt$(v.ingresos_total)"></span>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Inventario + Stock vendedor --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php $maxStock = collect($stockVendedores)->max('total') ?: 1; @endphp

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm"
                     @click="openModal('bicicletas')">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Inventario de bicicletas</p>
                        <span class="text-[11px] text-gray-400">Ver detalle →</span>
                    </div>
                    <div class="p-5 flex items-center gap-6">
                        <canvas id="donut-chart" width="76" height="76" class="shrink-0"></canvas>
                        <div class="flex-1 space-y-2.5">
                            @foreach([
                                ['color'=>'#111827','label'=>'En stock','key'=>'en_stock'],
                                ['color'=>'#6b7280','label'=>'Vendidas','key'=>'vendidas'],
                                ['color'=>'#9ca3af','label'=>'En reparación','key'=>'en_reparacion'],
                            ] as $row)
                            <div class="flex items-center gap-2 text-[11px]">
                                <span class="w-2 h-2 rounded-sm shrink-0" style="background:{{ $row['color'] }}"></span>
                                <span class="text-gray-500 dark:text-gray-400 flex-1">{{ $row['label'] }}</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $biciStats[$row['key']] }}</span>
                            </div>
                            @endforeach
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                                <span class="text-[10px] text-gray-400">Total</span>
                                <span class="font-semibold text-[13px] text-gray-900 dark:text-gray-100">{{ $biciStats['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                        <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Stock por vendedor</p>
                    </div>
                    <div class="p-5 space-y-3.5">
                        @foreach($stockVendedores as $i => $v)
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 w-[100px] shrink-0 truncate">{{ $v['nombre'] }}</span>
                            <div class="flex-1 h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gray-900 dark:bg-gray-300" style="width:{{ round($v['total']/$maxStock*100) }}%;opacity:{{ 1 - $i*0.15 }};"></div>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 text-right min-w-[24px]">{{ $v['total'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actividad reciente --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
                <h3 class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-4">Actividad reciente</h3>
                <div class="space-y-4" x-show="!loading">
                    <template x-if="pedidos.length || ots.length">
                        <div>
                            <template x-for="p in pedidos.slice(0,3)" :key="p.id">
                                <div class="flex items-start gap-4 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <div class="w-2 h-2 mt-2 rounded-full bg-blue-500"></div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="p.id"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="p.cliente + ' · ' + p.fecha"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="fmt$(p.monto)"></span>
                                </div>
                            </template>
                            <template x-for="o in ots.slice(0,2)" :key="o.id">
                                <div class="flex items-start gap-4 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <div class="w-2 h-2 mt-2 rounded-full bg-amber-500"></div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="o.id"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="o.tipo + ' — ' + o.modelo"></div>
                                    </div>
                                    <span class="text-xs text-gray-400" x-text="o.dias + 'd'"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="!pedidos.length && !ots.length" class="text-sm text-gray-400 py-4 text-center">Sin actividad reciente</div>
                </div>
            </div>

        </div>{{-- /LEFT --}}

        {{-- ── RIGHT SIDEBAR ── --}}
        <div class="space-y-6">

            {{-- KPIs secundarios --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-4">Resumen del periodo</p>
                <div class="space-y-4">
                    <template x-if="loading">
                        <template x-for="i in 3" :key="i">
                            <div class="flex items-center justify-between animate-pulse">
                                <div class="space-y-1.5">
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-20"></div>
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-28"></div>
                                </div>
                                <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-14"></div>
                            </div>
                        </template>
                    </template>
                    <template x-if="!loading">
                        <template x-for="k in kpis.slice(2)" :key="k.key">
                            <div class="flex items-center justify-between cursor-pointer hover:opacity-60 transition-opacity py-0.5"
                                 @click="openModal(k.modal)">
                                <div>
                                    <p class="text-[12px] font-medium text-gray-800 dark:text-gray-200" x-text="k.label"></p>
                                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="k.sub"></p>
                                </div>
                                <p class="text-[15px] font-semibold text-gray-900 dark:text-gray-100" x-text="k.value"></p>
                            </div>
                        </template>
                    </template>
                </div>
            </div>

            {{-- Pedidos recientes --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Pedidos recientes</p>
                    <span class="text-[11px] text-gray-400 cursor-pointer hover:text-gray-600 dark:hover:text-gray-300 transition-colors" @click="openModal('pedidosList')">Ver todos →</span>
                </div>
                <div x-show="!loading">
                    <template x-if="pedidos.length === 0">
                        <div class="px-5 py-6 text-center text-[11px] text-gray-400">Sin pedidos recientes</div>
                    </template>
                    <template x-for="(p, i) in pedidos.slice(0,5)" :key="p.id">
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700/60 last:border-0 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                             @click="openModal('pedidoItem', i)">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                                <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400"
                                      x-text="p.cliente.split(' ').filter(Boolean).map(w=>w[0]).join('').slice(0,2).toUpperCase()"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-medium text-gray-800 dark:text-gray-200 truncate" x-text="p.cliente"></p>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-text="p.fecha"></p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-[12px] font-semibold text-gray-900 dark:text-gray-100" x-text="fmt$(p.monto)"></p>
                                <p class="text-[9px] text-gray-400 capitalize mt-0.5" x-text="p.estado"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Personal --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <p class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Personal</p>
                    <span class="text-[10px] text-gray-400">{{ count($personal) }} registrados</span>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @foreach($personal as $i => $p)
                    @php $ini = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(explode(' ',$p['nombre']),0,2)))); @endphp
                    <div class="flex items-center gap-3 px-5 py-3.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                         @click="openModal('personalItem', {{ $i }})">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            {{ $ini }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-medium text-gray-800 dark:text-gray-200 truncate">{{ $p['nombre'] }}</p>
                            <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $p['sucursal'] }}</p>
                        </div>
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $p['activo'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    </div>
                    @endforeach
                </div>
            </div>

            <p class="text-right text-[9px] text-gray-400 pb-2">
                TTL · bicis 3600s · pedidos 3600s · stats event-driven
            </p>

        </div>{{-- /RIGHT --}}

    </div>{{-- /MAIN GRID --}}

    {{-- ── MODAL ── --}}
    <div x-show="modalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/20 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
         @click.self="modalOpen=false" @keydown.escape.window="modalOpen=false">
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-[.98]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 translate-y-2"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md max-h-[82vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700 z-10">
                <h3 class="text-[13px] font-semibold text-gray-900 dark:text-gray-100" x-html="modalTitle"></h3>
                <button @click="modalOpen=false"
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

const isDark  = () => document.documentElement.classList.contains('dark');
const fmt$    = n  => '$' + Number(n).toLocaleString('es-MX', { maximumFractionDigits: 0 });
const pct     = (a, b) => b > 0 ? Math.round(a / b * 100) : 0;

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
        // ── Estado ──────────────────────────────────────────────────────────
        clock: '--:--',
        periodo: 'today', customDesde: '', customHasta: '',
        presets: [{key:'today',label:'Hoy'},{key:'7d',label:'7 días'},{key:'30d',label:'30 días'},{key:'month',label:'Mes'},{key:'custom',label:'Rango'}],
        get periodoLabel() { return {today:'Hoy','7d':'Últimos 7 días','30d':'Últimos 30 días',month:'Este mes',custom:'Personalizado'}[this.periodo]||''; },
        loading: true,
        diasPeriodo: 1,

        kpiData: {}, tops: {}, graficaDias: [], graficaAnterior: [],
        graficaSucursales: [], sucursalMetrica: 'ingresos',
        sucursales: [], pedidos: [], ots: [], metodosPago: [],
        clientesTipo: [], horasPico: [], ventasPersonal: [],

        // Modal
        modalOpen: false, modalLoading: false,
        modalTitle: '', modalContent: '',
        _modalPeriodoParams() {
            if (this.periodo === 'custom' && this.customDesde && this.customHasta)
                return `&desde=${this.customDesde}&hasta=${this.customHasta}`;
            return `&periodo=${this.periodo}`;
        },

        // Enlace
        enlaceEstado: SSR.enlace.estado, enlaceCancelado: SSR.enlace.cancelado,
        enlaceGestor: SSR.enlace.gestor, enlaceOpen: false,

        // KPI helpers
        kpiCardClass(i)   { return KPI_ACCENTS[i]?.card   || 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700'; },
        kpiCircleClass(i) { return KPI_ACCENTS[i]?.circle || 'bg-gray-300'; },
        kpiLabelClass(i)  { return KPI_ACCENTS[i]?.label  || 'text-gray-400'; },
        kpiValueClass(i)  { return KPI_ACCENTS[i]?.value  || 'text-gray-900 dark:text-gray-100'; },
        kpiSubClass(i)    { return KPI_ACCENTS[i]?.sub    || 'text-gray-400'; },

        get kpis() {
            const d = this.kpiData;
            if (d.ventas === undefined) return [];
            return [
                { key:'ventas',   label:'Ventas',      value: d.ventas,          sub: 'transacciones',                                              modal:'ventasKPI' },
                { key:'ingresos', label:'Ingresos',     value: fmt$(d.ingresos),  sub: 'en el periodo',                                              modal:'ingresosKPI' },
                { key:'ticket',   label:'Ticket prom.', value: fmt$(d.ticket),    sub: 'por venta',                                                  modal:'ticketKPI' },
                { key:'clientes', label:'Clientes',     value: d.clientes,        sub: (d.clientes_nuevos??0)+' nuevos · '+(d.clientes_rec??0)+' rec.', modal:'clientesTipo' },
                { key:'ots',      label:'OTs activas',  value: d.ots_activas,     sub: 'en taller ahora',                                            modal:'otsList' },
                { key:'stock',    label:'En stock',     value: SSR.biciStats.en_stock, sub: 'bicicletas',                                            modal:'bicicletas' },
                { key:'cupones',  label:'Cupones',      value: fmt$(d.cupones_descuento??0), sub: (d.cupones_usados??0)+' usos',                      modal:'cupones' },
            ];
        },

        // ── Init ────────────────────────────────────────────────────────────
        init() {
            this._clockTick();
            this.fetchStats();
            this.$nextTick(() => this._initDonut());
            this._listenEcho();
        },

        _clockTick() {
            this.clock = new Date().toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit' });
            setTimeout(() => this._clockTick(), 30000);
        },

        setPeriod(p) { this.periodo = p; if (p !== 'custom') this.fetchStats(); },

        // ── Fetch stats (lee de estadisticas_diarias vía backend) ───────────
        async fetchStats() {
            this.loading = true;
            try {
                let url = SSR.statsUrl + '?periodo=' + this.periodo;
                if (this.periodo === 'custom' && this.customDesde && this.customHasta)
                    url = SSR.statsUrl + '?desde=' + this.customDesde + '&hasta=' + this.customHasta;

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

        // ── Fetch detalle (lazy — solo al abrir modal) ───────────────────────
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

        // ── SVG financiero ──────────────────────────────────────────────────
        _buildFinancialSVG({ containerId, data, anterior, labels, isMoney, yTicks, multiSeries }) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = '';

            const d0    = isDark();
            const W     = 500, H = 180, PAD_B = 20, PAD_T = 10;
            const plotH = H - PAD_B - PAD_T;

            const isMulti = Array.isArray(multiSeries) && multiSeries.length > 0;
            const allData = isMulti ? multiSeries.map(s => s.data) : [data];
            const allMax  = Math.max(...allData.flat(), 1);
            const ticks   = yTicks || calcYTicks(allMax);
            const maxVal  = ticks[ticks.length - 1];
            const n       = (isMulti ? multiSeries[0].data : data).length;
            if (n < 2) return;

            const xOf = i => (i / (n - 1)) * W;
            const yOf = v => PAD_T + plotH - Math.min(v / maxVal, 1) * plotH;

            const yAxisDiv = document.createElement('div');
            yAxisDiv.style.cssText = 'display:flex;flex-direction:column-reverse;justify-content:space-between;padding-bottom:20px;margin-right:8px;min-width:28px;flex-shrink:0;';
            ticks.forEach(t => {
                const s = document.createElement('span');
                s.textContent = isMoney ? (t >= 1000 ? Math.round(t/1000)+'k' : t) : t;
                s.style.cssText = `font-size:10px;color:${d0?'rgba(107,114,128,0.9)':'rgba(156,163,175,0.9)'};line-height:1;text-align:right;display:block;font-family:'Figtree',ui-sans-serif;`;
                yAxisDiv.appendChild(s);
            });

            const flex    = document.createElement('div');
            flex.style.cssText = 'display:flex;position:relative;';
            const svgWrap = document.createElement('div');
            svgWrap.style.cssText = 'flex:1;position:relative;height:180px;';

            const NS = 'http://www.w3.org/2000/svg';
            const el = (tag, attrs) => {
                const e = document.createElementNS(NS, tag);
                for (const [k,v] of Object.entries(attrs)) e.setAttribute(k, v);
                return e;
            };

            const svg = el('svg', { viewBox:`0 0 ${W} ${H}`, preserveAspectRatio:'none',
                style:'width:100%;height:100%;display:block;overflow:visible;' });
            const defs = el('defs', {});

            if (!isMulti) {
                const patId  = containerId + '-pat';
                const clipId = containerId + '-clip';
                const pat = el('pattern', { id:patId, x:'0', y:'0', width:'3', height:'200', patternUnits:'userSpaceOnUse' });
                pat.appendChild(el('line', { x1:'0',y1:'0',x2:'0',y2:'200',
                    stroke: d0?'rgba(255,255,255,0.12)':'rgba(0,0,0,0.10)', 'stroke-width':'1.2' }));
                defs.appendChild(pat);
                const ptsFlat = data.map((v,i)=>`${xOf(i)},${yOf(v)}`).join(' L ');
                const areaD   = `M ${xOf(0)},${H-PAD_B} L ${ptsFlat} L ${xOf(n-1)},${H-PAD_B} Z`;
                const clip    = el('clipPath', { id:clipId });
                clip.appendChild(el('path', { d:areaD }));
                defs.appendChild(clip);
                svg.appendChild(defs);
                svg.appendChild(el('rect', { x:'0',y:'0',width:W,height:H,
                    fill:`url(#${patId})`, 'clip-path':`url(#${clipId})` }));
            } else {
                svg.appendChild(defs);
            }

            ticks.slice(1).forEach(t => {
                svg.appendChild(el('line', { x1:'0',y1:yOf(t),x2:W,y2:yOf(t),
                    stroke: d0?'rgba(255,255,255,0.04)':'rgba(0,0,0,0.045)', 'stroke-width':'0.5' }));
            });

            const seriesList  = isMulti ? multiSeries : [{ data, label:'', color: d0?'#e5e7eb':'#111827', dashed:false }];
            const dotsBySeries = [];

            seriesList.forEach((series, si) => {
                const pts   = series.data;
                const color = series.color || (isMulti ? COLORS[si%6] : (d0?'#e5e7eb':'#111827'));
                const poly  = el('polyline', { points: pts.map((v,i)=>`${xOf(i)},${yOf(v)}`).join(' '),
                    fill:'none', stroke:color, 'stroke-width':'2',
                    'stroke-linejoin':'round', 'stroke-linecap':'round' });
                if (series.dashed) poly.setAttribute('stroke-dasharray', '5,4');
                svg.appendChild(poly);
                const dot = el('circle', { cx:'0',cy:'0',r:'4.5',
                    fill: d0?'#1f2937':'#ffffff', stroke:color, 'stroke-width':'2', opacity:'0' });
                svg.appendChild(dot);
                dotsBySeries.push({ dot, data:pts, color });
            });

            const stepX = Math.max(1, Math.floor(n/6));
            labels.forEach((lbl, i) => {
                if (i % stepX !== 0 && i !== n-1) return;
                const t = el('text', { x:xOf(i), y:H-5,
                    'text-anchor': i===0?'start':i===n-1?'end':'middle',
                    'font-size':'9.5', fill: d0?'rgba(107,114,128,0.9)':'rgba(107,114,128,0.8)',
                    'font-family':"'Figtree',ui-sans-serif" });
                t.textContent = lbl;
                svg.appendChild(t);
            });

            const vLine = el('line', { x1:'0',y1:PAD_T-4,x2:'0',y2:H-PAD_B,
                stroke: d0?'rgba(148,163,184,0.5)':'rgba(17,24,39,0.25)', 'stroke-width':'1', opacity:'0' });
            svg.appendChild(vLine);
            svg.appendChild(el('rect', { x:'0',y:'0',width:W,height:H, fill:'transparent', style:'cursor:crosshair' }));

            const tip    = document.createElement('div');
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

            const tdEl   = tip.querySelector('.tip-d');
            const tvEl   = tip.querySelector('.tip-v');
            const tpEl   = tip.querySelector('.tip-p');
            const taEl   = tip.querySelector('.tip-ant');
            const trRows = tip.querySelector('.tip-rows');

            function onMove(clientX) {
                const rect = svg.getBoundingClientRect();
                const mx   = (clientX - rect.left) / rect.width * W;
                const ci   = Math.max(0, Math.min(n-1, Math.round(mx/W*(n-1))));
                const cx   = xOf(ci);

                vLine.setAttribute('x1',cx); vLine.setAttribute('x2',cx); vLine.setAttribute('opacity','1');
                dotsBySeries.forEach(({dot, data:pts}) => {
                    dot.setAttribute('cx',cx); dot.setAttribute('cy',yOf(pts[ci])); dot.setAttribute('opacity','1');
                });
                tdEl.textContent = labels[ci];

                if (isMulti) {
                    trRows.innerHTML = seriesList.map((s,si) => {
                        const val  = s.data[ci];
                        const disp = isMoney ? fmt$(Math.round(val)) : Math.round(val)+' ventas';
                        return `<div style="display:flex;align-items:center;gap:8px;font-size:11px;margin-top:3px;"><span style="width:7px;height:7px;border-radius:50%;flex-shrink:0;background:${s.color};"></span><span style="color:${d0?'#9ca3af':'#6b7280'};flex:1;">${s.label}</span><span style="font-weight:600;color:${d0?'#f3f4f6':'#111827'};">${disp}</span></div>`;
                    }).join('');
                } else {
                    const val  = data[ci];
                    const prev = anterior && anterior.some(v=>v>0) ? (anterior[ci]??null) : (ci>0?data[ci-1]:null);
                    const prevLabel = anterior && anterior.some(v=>v>0) ? 'periodo ant.' : 'día ant.';
                    let deltaStr = null, deltaForzado = null;
                    if (prev !== null) {
                        if      (prev===0 && val===0) {}
                        else if (prev===0 && val>0)   { deltaForzado = {text:'▲ nuevo',isPos:true}; }
                        else if (prev>0  && val===0)  { deltaStr = '-100.0'; }
                        else                           { deltaStr = ((val-prev)/prev*100).toFixed(1); }
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
                const tx   = (cx/W)*iw;
                const ty   = (yOf(dotsBySeries[0].data[ci])/H)*svgWrap.clientHeight;
                const tipW = tip.offsetWidth||128;
                const tipH = tip.offsetHeight||60;
                tip.style.left    = Math.min(tx+14, iw-tipW-6)+'px';
                tip.style.top     = Math.max(Math.min(ty-tipH/2, svgWrap.clientHeight-tipH-4),4)+'px';
                tip.style.opacity = '1';
            }

            function onLeave() {
                dotsBySeries.forEach(({dot})=>dot.setAttribute('opacity','0'));
                vLine.setAttribute('opacity','0'); tip.style.opacity='0';
            }

            svg.addEventListener('mousemove',  e => onMove(e.clientX));
            svg.addEventListener('mouseleave', onLeave);
            svg.addEventListener('touchmove',  e => { e.preventDefault(); onMove(e.touches[0].clientX); }, { passive:false });
            svg.addEventListener('touchend',   onLeave);

            flex.appendChild(yAxisDiv);
            flex.appendChild(svgWrap);
            container.appendChild(flex);
        },

        _renderIngresosChart() {
            const labels   = this.graficaDias.map(d => d.label);
            const data     = this.graficaDias.map(d => d.ingresos);
            const anterior = this.graficaAnterior.map(d => d.ingresos);
            if (!labels.length) return;
            this._buildFinancialSVG({ containerId:'ingresos-chart-wrap', data, anterior, labels, isMoney:true,
                yTicks: calcYTicks(Math.max(...data, 1)) });
        },

        _renderVentasChart() {
            const labels   = this.graficaDias.map(d => d.label);
            const data     = this.graficaDias.map(d => d.ventas);
            const anterior = this.graficaAnterior.map(d => d.ventas);
            if (!labels.length) return;
            this._buildFinancialSVG({ containerId:'ventas-chart-wrap', data, anterior, labels, isMoney:false,
                yTicks: calcYTicks(Math.max(...data, 1)) });
        },

        _renderSucursalesChart() {
            const wrap = document.getElementById('sucursales-chart-wrap');
            if (!wrap || !this.graficaSucursales.length) return;
            const labels  = this.graficaDias.map(d => d.label);
            const metrica = this.sucursalMetrica;
            const multiSeries = this.graficaSucursales.map((s, i) => ({
                label:  s.nombre,
                data:   s[metrica] || [],
                color:  COLORS[i%6],
                dashed: i !== 0,
            }));
            this._buildFinancialSVG({ containerId:'sucursales-chart-wrap', data:null, anterior:null,
                labels, isMoney: metrica==='ingresos',
                yTicks: calcYTicks(Math.max(...multiSeries.flatMap(s=>s.data), 1)),
                multiSeries });
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
                        backgroundColor:['#111827','#6b7280','#9ca3af'], borderWidth:0, hoverOffset:4 }]
                },
                options: { responsive:false, cutout:'74%', animation:{duration:700},
                    plugins:{ legend:{display:false},
                        tooltip:{ backgroundColor: isDark()?'rgba(17,24,39,0.97)':'rgba(255,255,255,0.99)',
                            borderColor: isDark()?'rgba(55,65,81,0.8)':'rgba(226,232,240,0.9)',
                            borderWidth:1, padding:{x:10,y:8}, cornerRadius:12, displayColors:false,
                            titleFont:{size:11,weight:'600'}, bodyFont:{size:10},
                            titleColor: isDark()?'#f3f4f6':'#111827', bodyColor: isDark()?'#9ca3af':'#6b7280',
                            callbacks:{label:l=>`  ${l.label}: ${l.parsed}`} }} }
            });
            this._donutInited = true;
        },

        // ── Modales ──────────────────────────────────────────────────────────
        async openModal(type, idx = 0) {
            this.modalOpen    = true;
            this.modalLoading = true;
            this.modalContent = '';

            // Modales que NO necesitan fetch (datos ya en memoria)
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
            };

            if (localHandlers[type]) {
                const { title, content } = localHandlers[type]();
                this.modalTitle   = title;
                this.modalContent = content;
                this.modalLoading = false;
                return;
            }

            // Modales que necesitan fetch lazy
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

        // ── Modales locales (sin fetch) ──────────────────────────────────────
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

        // ── Modales con fetch lazy ────────────────────────────────────────────
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

            // Detalle del modelo top
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

            // Buscar serie del personal para la gráfica
            const serie = this.ventasPersonal.find(s => s.nombre === pNom);

            let content = this._table([
                ['Sucursal', p.sucursal],
                ['Estado',   p.activo ? 'Activo' : 'Inactivo'],
            ]);

            // Fetch detalle de vendedor de la sucursal
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

        // ── Helpers HTML ─────────────────────────────────────────────────────
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

        // ── Echo ─────────────────────────────────────────────────────────────
        _listenEcho() {
            if (!window.Echo) return;
            window.Echo.private(`enlace-vendedor.{{ auth()->user()->id_usuario }}`)
                .listen('.enlace.updated', ({action, enlace}) => {
                    if (action==='aceptado')  { this.enlaceCancelado=false; this.enlaceEstado='activo';  this.enlaceGestor=enlace.gestor; }
                    if (action==='cancelado') { this.enlaceCancelado=true;  this.enlaceEstado='';        this.enlaceGestor=''; }
                });
        },
    };
}
</script>
</div>
</x-app-layout>