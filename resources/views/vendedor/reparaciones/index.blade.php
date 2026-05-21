{{-- resources/views/vendedor/reparaciones/index.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-5" x-data="otIndex()" x-init="init()">

    
    {{-- ── Header ── --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Órdenes de trabajo</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                <span x-text="stats.activas"></span> activas ·
                <span x-text="stats.listas"></span> listas para entrega
            </p>
        </div>
        <a href="{{ route('reparaciones.create') }}"
           class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                  text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90
                  transition active:scale-[.98] shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva OT
        </a>
    </div>


    {{-- ── Flash ── --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo==='error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-600 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>


    {{-- ── Filtros ── --}}
    <div class="flex gap-2 overflow-x-auto pb-1" style="scrollbar-width:none">
        <template x-for="f in filtros" :key="f.val">
            <button @click="cambiarFiltro(f.val)"
                    :class="filtroActivo===f.val
                        ? 'bg-gray-900 dark:bg-white dark:text-gray-900 text-white border-transparent'
                        : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-gray-400'"
                    class="flex items-center gap-1.5 border px-3.5 py-1.5 rounded-full text-xs
                           font-medium whitespace-nowrap transition-all duration-150 shrink-0">
                <span x-text="f.label"></span>
            </button>
        </template>
    </div>

    {{-- ── Grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        {{-- Lista --}}
        <div class="lg:col-span-1 space-y-2 overflow-y-auto" style="max-height:calc(100vh - 220px)">

            <div x-show="cargando" class="flex flex-col gap-2">
                <template x-for="i in 5" :key="i">
                    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700
                                rounded-xl p-4 animate-pulse space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                    </div>
                </template>
            </div>

            <template x-if="!cargando">
                <div class="space-y-2">
                    <template x-for="ot in ots" :key="ot.id_ot">
                        <div @click="seleccionar(ot.id_ot)"
                             class="bg-white dark:bg-gray-800 rounded-xl p-4 cursor-pointer transition-all duration-150"
                             :class="{
                                 'border-2 border-gray-900 dark:border-gray-200': seleccionada===ot.id_ot,
                                 'border border-l-[3px] border-l-purple-500 border-gray-100 dark:border-gray-700': ot.tipo==='garantia' && seleccionada!==ot.id_ot,
                                 'border border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600': ot.tipo!=='garantia' && seleccionada!==ot.id_ot,
                             }">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400" x-text="ot.id_ot"></span>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                                          :class="estadoClass(ot.estado)" x-text="estadoLabel(ot.estado)"></span>
                                    <span x-show="ot.tipo==='garantia'"
                                          class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                                 bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400">
                                        Garantía
                                    </span>
                                </div>
                                <span class="text-[10px] text-gray-400 shrink-0" x-text="ot.tiempo ?? ''"></span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                               x-text="ot.cliente_nombre ?? ot.cliente?.nombre_cliente ?? '—'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5"
                               x-text="ot.num_serie ?? ot.bici_descripcion ?? '—'"></p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1.5 line-clamp-1"
                               x-text="ot.problema_reportado"></p>
                            <div class="flex items-center justify-between mt-2.5">
                                <span class="text-[11px] text-gray-400"
                                      x-text="ot.tecnico ? '👤 ' + ot.tecnico.nombre_usuario : 'Sin técnico'"></span>
                                <span class="text-xs font-semibold"
                                      :class="ot.tipo==='garantia' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-700 dark:text-gray-300'"
                                      x-text="ot.tipo==='garantia' ? '$0 ✦' : (ot.costo_total > 0 ? '$'+Number(ot.costo_total).toLocaleString('es-MX') : 'Por cotizar')">
                                </span>
                            </div>
                        </div>
                    </template>

                    <div x-show="ots.length===0"
                         class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin órdenes</p>
                        <p class="text-xs text-gray-400 mt-1">No hay OTs con ese filtro</p>
                    </div>
                </div>
            </template>

            {{-- Paginación --}}
            <div x-show="lastPage > 1" class="flex items-center justify-between pt-2">
                <button @click="cambiarPagina(paginaActual - 1)" :disabled="paginaActual===1"
                        class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                               text-gray-500 dark:text-gray-400 disabled:opacity-40
                               hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    ← Anterior
                </button>
                <span class="text-xs text-gray-400">
                    <span x-text="paginaActual"></span> / <span x-text="lastPage"></span>
                </span>
                <button @click="cambiarPagina(paginaActual + 1)" :disabled="paginaActual===lastPage"
                        class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                               text-gray-500 dark:text-gray-400 disabled:opacity-40
                               hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Siguiente →
                </button>
            </div>
        </div>

        {{-- Panel detalle --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                 style="min-height:420px">

                {{-- Sin selección --}}
                <div x-show="!seleccionada && !cargandoDetalle"
                     class="flex flex-col items-center justify-center py-24 px-6 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Selecciona una OT</p>
                    <p class="text-xs text-gray-400 mt-1 max-w-[200px]">Haz clic para ver el detalle y avanzar el estado</p>
                </div>

                {{-- Loading detalle --}}
                <div x-show="cargandoDetalle" class="p-6 animate-pulse space-y-4">
                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="h-16 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                        <div class="h-16 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                    </div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>

                {{-- Detalle --}}
                <template x-if="otDetalle && !cargandoDetalle">
                    <div>
                        {{-- Header panel --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4
                                    border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300"
                                      x-text="otDetalle.id_ot"></span>
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full"
                                      :class="estadoClass(otDetalle.estado)"
                                      x-text="estadoLabel(otDetalle.estado)"></span>
                                <span x-show="otDetalle.tipo==='garantia'"
                                      class="text-[11px] font-medium px-2.5 py-1 rounded-full
                                             bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400">
                                    ✦ Garantía
                                </span>
                            </div>
                            <button @click="cerrarDetalle()"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                           p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-5">

                            {{-- Stats --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Cliente</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                                       x-text="otDetalle.cliente_nombre ?? otDetalle.cliente?.nombre_cliente ?? '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Unidad</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate font-mono"
                                       x-text="otDetalle.num_serie ?? otDetalle.bici_descripcion ?? '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Costo</p>
                                    <p class="text-sm font-semibold"
                                       :class="otDetalle.tipo==='garantia' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-800 dark:text-gray-200'"
                                       x-text="otDetalle.tipo==='garantia' ? '$0 ✦' : '$'+Number(otDetalle.costo_total).toLocaleString('es-MX')">
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Técnico</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                                       x-text="otDetalle.tecnico?.nombre_usuario ?? '—'"></p>
                                </div>
                            </div>

                            {{-- Banner garantía --}}
                            <div x-show="otDetalle.tipo==='garantia'"
                                 class="flex items-start gap-2.5 bg-purple-50 dark:bg-purple-900/20
                                        border border-purple-200 dark:border-purple-800 rounded-xl px-4 py-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                <p class="text-xs text-purple-800 dark:text-purple-300"
                                   x-text="'Garantía aprobada — Reclamo ' + otDetalle.id_garantia_aprobada"></p>
                            </div>

                            {{-- Problema / diagnóstico --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-400 mb-1.5 font-medium">Problema reportado</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"
                                       x-text="otDetalle.problema_reportado"></p>
                                </div>
                                <div x-show="otDetalle.diagnostico">
                                    <p class="text-xs text-gray-400 mb-1.5 font-medium">Diagnóstico</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"
                                       x-text="otDetalle.diagnostico"></p>
                                </div>
                            </div>

                            {{-- Piezas --}}
                            <div x-show="otDetalle.piezas && otDetalle.piezas.length > 0">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs text-gray-400 font-medium">Piezas</p>
                                    <a :href="'{{ url('sucursal/reparaciones') }}/' + otDetalle.id_ot + '/piezas'"
                                       class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700
                                              dark:hover:text-gray-200 underline underline-offset-2 transition">
                                        Editar piezas
                                    </a>
                                </div>
                                <div class="space-y-1.5">
                                    <template x-for="p in otDetalle.piezas" :key="p.id">
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span x-show="p.es_garantia"
                                                      class="text-[9px] px-1.5 py-0.5 rounded font-medium shrink-0
                                                             bg-purple-100 dark:bg-purple-800/30 text-purple-700 dark:text-purple-400">
                                                    Garantía
                                                </span>
                                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate"
                                                      x-text="(p.pieza?.nombre ?? p.descripcion ?? '—') + ' × ' + p.cantidad"></span>
                                            </div>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 shrink-0 ml-2"
                                                  x-text="p.es_garantia ? '$0' : '$'+Number(p.subtotal).toLocaleString('es-MX')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Progreso --}}
                            <div>
                                <p class="text-xs text-gray-400 mb-3 font-medium">Progreso</p>
                                <div class="flex items-start gap-0">
                                    <template x-for="(paso, idx) in pasos" :key="paso.val">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <div class="flex flex-col items-center gap-1 min-w-0">
                                                <div class="w-2.5 h-2.5 rounded-full transition-colors duration-300 shrink-0"
                                                     :class="pasoActivo(paso.val) ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-600'">
                                                </div>
                                                <span class="text-[9px] text-center leading-tight w-full"
                                                      :class="pasoActivo(paso.val) ? 'text-gray-700 dark:text-gray-300 font-medium' : 'text-gray-400'"
                                                      x-text="paso.label"></span>
                                            </div>
                                            <div x-show="idx < pasos.length - 1"
                                                 class="h-px flex-1 -mt-4 mx-0.5 transition-colors duration-300"
                                                 :class="pasoActivo(pasos[idx+1]?.val) ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-600'">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Historial --}}
                            <div x-show="otDetalle.historial && otDetalle.historial.length > 0">
                                <p class="text-xs text-gray-400 mb-2 font-medium">Historial</p>
                                <div class="space-y-1.5 max-h-28 overflow-y-auto">
                                    <template x-for="h in otDetalle.historial" :key="h.id">
                                        <div class="flex items-start gap-2 text-xs">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 mt-1.5 shrink-0"></div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-300" x-text="estadoLabel(h.estado_nuevo)"></span>
                                                <span class="text-gray-400 mx-1">·</span>
                                                <span class="text-gray-400" x-text="h.usuario?.nombre_usuario ?? '—'"></span>
                                                <span x-show="h.nota" class="block text-gray-400 mt-0.5 italic" x-text="h.nota"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="pt-1 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <template x-for="accion in accionesPermitidas(otDetalle.estado)" :key="accion.estado">
                                        <button @click="avanzarEstado(otDetalle.id_ot, accion.estado)"
                                                :disabled="avanzando"
                                                class="flex items-center gap-1.5 bg-gray-900 dark:bg-white
                                                       dark:text-gray-900 text-white px-4 py-2.5 rounded-xl
                                                       text-sm font-semibold hover:opacity-90 transition
                                                       disabled:opacity-40 active:scale-[.98]">
                                            <svg x-show="avanzando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            <span x-text="accion.label"></span>
                                        </button>
                                    </template>

                                    <a :href="'{{ url('sucursal/reparaciones') }}/' + otDetalle.id_ot + '/piezas'"
                                       class="flex items-center gap-1.5 border border-gray-200 dark:border-gray-600
                                              text-gray-500 dark:text-gray-400 px-4 py-2.5 rounded-xl
                                              text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                                        </svg>
                                        Piezas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function otIndex() {
    return {
        ots: [], otDetalle: null, seleccionada: null,
        cargando: true, cargandoDetalle: false, avanzando: false,
        filtroActivo: 'todas', paginaActual: 1, lastPage: 1,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,
        stats: { activas: 0, listas: 0 },

        filtros: [
            { val: 'todas',                label: 'Todas' },
            { val: 'recibida',             label: 'Recibidas' },
            { val: 'diagnostico',          label: 'Diagnóstico' },
            { val: 'esperando_aprobacion', label: 'Esp. aprobación' },
            { val: 'en_proceso',           label: 'En proceso' },
            { val: 'mandado_fabrica',      label: 'En fábrica' },
            { val: 'lista',                label: 'Listas' },
        ],

        pasos: [
            { val: 'recibida',             label: 'Recibida' },
            { val: 'diagnostico',          label: 'Diagnóst.' },
            { val: 'esperando_aprobacion', label: 'Aprob.' },
            { val: 'en_proceso',           label: 'Proceso' },
            { val: 'lista',                label: 'Lista' },
            { val: 'entregada',            label: 'Entregada' },
        ],

        transiciones: {
            recibida:             [{ estado: 'diagnostico',          label: 'Iniciar diagnóstico' },
                                   { estado: 'mandado_fabrica',      label: 'Enviar a fábrica' }],
            diagnostico:          [{ estado: 'esperando_aprobacion', label: 'Enviar cotización' },
                                   { estado: 'en_proceso',           label: 'Iniciar trabajo' },
                                   { estado: 'mandado_fabrica',      label: 'Enviar a fábrica' }],
            esperando_aprobacion: [{ estado: 'en_proceso',           label: 'Aprobado — iniciar' }],
            en_proceso:           [{ estado: 'lista',                 label: 'Marcar como lista' }],
            mandado_fabrica:      [{ estado: 'lista',                 label: 'Llegó de fábrica' }],
            lista:                [{ estado: 'entregada',             label: 'Entregar y cobrar' }],
            entregada: [], cancelada: [],
        },

        init() { this.cargarOts(); },

        async cargarOts() {
            this.cargando = true;
            try {
                const p = new URLSearchParams({ page: this.paginaActual });
                if (this.filtroActivo !== 'todas') p.set('estado', this.filtroActivo);
                const res  = await fetch(`{{ route('reparaciones.index') }}?${p}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (!data.ok) return;
                this.ots      = data.data.data ?? [];
                this.lastPage = data.data.last_page ?? 1;
                this.stats.activas = this.ots.filter(o => !['entregada','cancelada'].includes(o.estado)).length;
                this.stats.listas  = this.ots.filter(o => o.estado === 'lista').length;
            } catch { this.flash('Error cargando órdenes', 'error'); }
            finally  { this.cargando = false; }
        },

        cambiarFiltro(val) {
            this.filtroActivo = val;
            this.paginaActual = 1;
            this.cerrarDetalle();
            this.cargarOts();
        },

        cambiarPagina(n) {
            if (n < 1 || n > this.lastPage) return;
            this.paginaActual = n;
            this.cargarOts();
        },

        async seleccionar(idOt) {
            if (this.seleccionada === idOt) return;
            this.seleccionada    = idOt;
            this.otDetalle       = null;
            this.cargandoDetalle = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${idOt}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.ok) this.otDetalle = data.data;
            } catch { this.flash('Error cargando detalle', 'error'); }
            finally  { this.cargandoDetalle = false; }
        },

        cerrarDetalle() { this.seleccionada = null; this.otDetalle = null; },

        async avanzarEstado(idOt, estado) {
            this.avanzando = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${idOt}/estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ estado }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash(data.mensaje);
                await this.seleccionar(idOt);
                await this.cargarOts();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.avanzando = false; }
        },

        pasoActivo(val) {
            const orden = ['recibida','diagnostico','esperando_aprobacion','en_proceso','lista','entregada'];
            return orden.indexOf(val) <= orden.indexOf(this.otDetalle?.estado ?? '');
        },

        accionesPermitidas(estado) { return this.transiciones[estado] ?? []; },

        estadoLabel(e) {
            return { recibida:'Recibida', diagnostico:'Diagnóstico', esperando_aprobacion:'Esp. aprobación',
                     en_proceso:'En proceso', mandado_fabrica:'En fábrica', lista:'Lista',
                     entregada:'Entregada', cancelada:'Cancelada' }[e] ?? e;
        },

        estadoClass(e) {
            return { recibida:'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                     diagnostico:'bg-blue-100 dark:bg-blue-800/30 text-blue-800 dark:text-blue-400',
                     esperando_aprobacion:'bg-amber-100 dark:bg-amber-800/30 text-amber-800 dark:text-amber-400',
                     en_proceso:'bg-green-100 dark:bg-green-800/30 text-green-800 dark:text-green-400',
                     mandado_fabrica:'bg-orange-100 dark:bg-orange-800/30 text-orange-800 dark:text-orange-400',
                     lista:'bg-teal-100 dark:bg-teal-800/30 text-teal-800 dark:text-teal-400',
                     entregada:'bg-gray-100 dark:bg-gray-700 text-gray-500',
                     cancelada:'bg-red-100 dark:bg-red-800/30 text-red-800 dark:text-red-400' }[e] ?? '';
        },

        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo==='error' ? 4500 : 3000);
        },
    }
}
</script>
</x-app-layout>