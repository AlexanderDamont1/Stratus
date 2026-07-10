<x-app-layout>
<div class="mx-auto space-y-5" x-data="mantIndex()" x-init="init()">

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

        {{-- ── Columna lista ── --}}
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
                    <template x-for="m in items" :key="m.id_reparacion">
                        <div @click="seleccionar(m.id_reparacion)"
                             class="bg-white dark:bg-gray-800 rounded-xl p-4 cursor-pointer transition-all duration-150"
                             :class="{
                                 'border-2 border-gray-900 dark:border-gray-200': seleccionada===m.id_reparacion,
                                 'border border-l-[3px] border-l-amber-400 border-gray-100 dark:border-gray-700':
                                     m.estado==='cotizacion_enviada' && seleccionada!==m.id_reparacion,
                                 'border border-gray-100 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600':
                                     m.estado!=='cotizacion_enviada' && seleccionada!==m.id_reparacion,
                             }">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400"
                                          x-text="m.id_reparacion"></span>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                                          :class="estadoClass(m.estado)"
                                          x-text="estadoLabel(m.estado)"></span>
                                    <span x-show="m.tipo==='garantia'"
                                          class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                                 bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400">
                                        Garantía
                                    </span>
                                </div>
                                <span class="text-[10px] text-gray-400 shrink-0"
                                      x-text="m.created_at_diff ?? ''"></span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                               x-text="m.cliente_nombre ?? m.cliente?.nombre_cliente ?? '—'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5"
                               x-text="m.num_serie ?? m.unidad_descripcion ?? '—'"></p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1.5 line-clamp-1"
                               x-text="m.problema_reportado"></p>
                            <div class="flex items-center justify-between mt-2.5">
                                <span x-show="cotizacionExpirada(m)"
                                      class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                             bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400">
                                    ⏰ Cotización expirada
                                </span>
                                <span x-show="!cotizacionExpirada(m)" class="text-[11px] text-gray-400">&nbsp;</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300"
                                      x-text="m.costo_total > 0 ? '$'+Number(m.costo_total).toLocaleString('es-MX') : 'Por cotizar'">
                                </span>
                            </div>
                        </div>
                    </template>

                    <div x-show="items.length===0"
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

        {{-- ── Panel detalle ── --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                 style="min-height:420px">

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

                <div x-show="cargandoDetalle" class="p-6 animate-pulse space-y-4">
                    <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="h-16 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                        <div class="h-16 bg-gray-100 dark:bg-gray-700 rounded-xl"></div>
                    </div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>

                <template x-if="detalle && !cargandoDetalle">
                    <div>
                        {{-- Header del panel --}}
                        <div class="flex items-center justify-between gap-3 px-5 py-4
                                    border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300"
                                      x-text="detalle.id_reparacion"></span>
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-full"
                                      :class="estadoClass(detalle.estado)"
                                      x-text="estadoLabel(detalle.estado)"></span>
                                <span x-show="detalle.tipo==='garantia'"
                                      class="text-[11px] font-medium px-2.5 py-1 rounded-full
                                             bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400">
                                    ✦ Garantía
                                </span>
                                <span x-show="detalle.tipo==='mantenimiento'"
                                      class="text-[11px] font-medium px-2.5 py-1 rounded-full
                                             bg-blue-100 dark:bg-blue-800/30 text-blue-800 dark:text-blue-400">
                                    Mantenimiento
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

                            {{-- ── Info cards ── --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Cliente</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                                       x-text="detalle.cliente_nombre ?? detalle.cliente?.nombre_cliente ?? '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Unidad</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate font-mono"
                                       x-text="detalle.num_serie ?? detalle.unidad_descripcion ?? '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Costo total</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                                       x-text="detalle.costo_total > 0 ? '$'+Number(detalle.costo_total).toLocaleString('es-MX') : 'Por cotizar'">
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                    <p class="text-[10px] text-gray-400 mb-1">Teléfono</p>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate font-mono"
                                       x-text="detalle.cliente_telefono ?? detalle.cliente?.telefono ?? '—'"></p>
                                </div>
                            </div>

                            {{-- ── Alerta cotización rechazada ── --}}
                            <template x-if="detalle.cotizacion && detalle.cotizacion.respuesta === 0 && detalle.tipo === 'reparacion'">
                                <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20
                                            border border-red-200 dark:border-red-800 rounded-xl px-4 py-3">
                                    <svg class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-red-700 dark:text-red-400">El cliente rechazó la cotización</p>
                                        <p class="text-xs text-red-600 dark:text-red-500 mt-0.5">
                                            Contáctalo para decidir si se cancela o se negocia:
                                            <span class="font-semibold font-mono"
                                                  x-text="detalle.cliente_telefono ?? detalle.cliente?.telefono ?? '—'"></span>
                                        </p>
                                        <div class="flex gap-2 mt-2">
                                            <a :href="'tel:'+(detalle.cliente_telefono ?? detalle.cliente?.telefono ?? '')"
                                               class="text-[11px] font-medium px-2.5 py-1 rounded-lg
                                                      bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400
                                                      hover:bg-red-200 dark:hover:bg-red-800/50 transition">
                                                📞 Llamar
                                            </a>
                                            <a :href="'https://wa.me/'+(detalle.cliente_telefono ?? detalle.cliente?.telefono ?? '').replace(/\D/g,'')"
                                               target="_blank"
                                               class="text-[11px] font-medium px-2.5 py-1 rounded-lg
                                                      bg-green-100 dark:bg-green-800/30 text-green-700 dark:text-green-400
                                                      hover:bg-green-200 dark:hover:bg-green-800/50 transition">
                                                WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- ── Alerta cotización expirada sin respuesta ── --}}
                            <template x-if="detalle.cotizacion && !detalle.cotizacion.respuesta && detalle.cotizacion.expires_at && new Date(detalle.cotizacion.expires_at) < new Date()">
                                <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20
                                            border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3">
                                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-400">Cotización expirada sin respuesta</p>
                                        <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">
                                            El cliente no respondió a tiempo. Comunícate o resuelve manualmente.
                                            Tel: <span class="font-semibold font-mono"
                                                       x-text="detalle.cliente_telefono ?? detalle.cliente?.telefono ?? '—'"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>

                            {{-- ── Problema reportado + diagnóstico ── --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-400 mb-1.5 font-medium">Problema reportado</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"
                                       x-text="detalle.problema_reportado"></p>
                                </div>
                                <div x-show="detalle?.diagnostico">
                                    <p class="text-xs text-gray-400 mb-1.5 font-medium">Diagnóstico</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed"
                                       x-text="detalle.diagnostico"></p>
                                </div>
                            </div>

                            {{-- ── Cotización snapshot ── --}}
                            <template x-if="detalle.cotizacion">
                                <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-xl p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Cotización enviada</p>
                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full"
                                              :class="{
                                                  'bg-amber-100 dark:bg-amber-800/30 text-amber-700 dark:text-amber-400': detalle.cotizacion.respuesta === null,
                                                  'bg-green-100 dark:bg-green-800/30 text-green-700 dark:text-green-400': detalle.cotizacion.respuesta === 1,
                                                  'bg-red-100 dark:bg-red-800/30 text-red-700 dark:text-red-400': detalle.cotizacion.respuesta === 0,
                                              }"
                                              x-text="detalle.cotizacion.respuesta === null ? 'Pendiente' : detalle.cotizacion.respuesta === 1 ? 'Aceptada ✓' : 'Rechazada'">
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed"
                                       x-text="detalle.cotizacion.descripcion_trabajo"></p>
                                    <template x-if="detalle.cotizacion.piezas_detalle && detalle.cotizacion.piezas_detalle.length">
                                        <div class="space-y-1">
                                            <template x-for="(p, idx) in detalle.cotizacion.piezas_detalle" :key="idx">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-600 dark:text-gray-300"
                                                          x-text="p.nombre + ' × ' + p.cantidad"></span>
                                                    <span class="font-medium text-gray-700 dark:text-gray-300"
                                                          x-text="'$'+Number(p.subtotal).toLocaleString('es-MX')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-600 pt-2">
                                        <span class="text-xs text-gray-400">Total cotización</span>
                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                                              x-text="'$'+Number(detalle.cotizacion.costo_total).toLocaleString('es-MX')"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- ── Piezas de la OT ── --}}
                            <div x-show="detalle?.piezas?.length > 0">
                                <p class="text-xs text-gray-400 mb-2 font-medium">Piezas</p>
                                <div class="space-y-1.5">
                                    <template x-for="(p, idx) in detalle.piezas" :key="p.id_reparacion_pieza ?? idx">
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                                            <span class="text-xs text-gray-700 dark:text-gray-300 truncate"
                                                  x-text="(p.pieza?.nombre ?? p.descripcion ?? '—') + ' × ' + p.cantidad"></span>
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400 shrink-0 ml-2"
                                                  x-text="'$'+Number(p.subtotal).toLocaleString('es-MX')"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- ── Progreso ── --}}
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

                            {{-- ── Historial ── --}}
                            <div x-show="detalle?.historial?.length > 0">
                                <p class="text-xs text-gray-400 mb-2 font-medium">Historial</p>
                                <div class="space-y-1.5 max-h-28 overflow-y-auto">
                                    <template x-for="(h, idx) in detalle.historial" :key="h.id_reparacion_historial ?? idx">
                                        <div class="flex items-start gap-2 text-xs">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600 mt-1.5 shrink-0"></div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-300"
                                                      x-text="estadoLabel(h.estado_nuevo)"></span>
                                                <span class="text-gray-400 mx-1">·</span>
                                                <span class="text-gray-400"
                                                      x-text="h.usuario?.nombre_usuario ?? '—'"></span>
                                                <span x-show="h.nota"
                                                      class="block text-gray-400 mt-0.5 italic"
                                                      x-text="h.nota"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- ── Acciones según estado ── --}}
                            <div class="pt-1 border-t border-gray-100 dark:border-gray-700 space-y-3">

                                {{-- recibida → iniciar diagnóstico --}}
                                <template x-if="detalle.estado === 'recibida'">
                                    <div class="flex gap-2 flex-wrap">
                                        <button @click="avanzarEstado(detalle.id_reparacion, 'diagnostico')"
                                                :disabled="avanzando"
                                                class="flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                                                       text-white px-4 py-2.5 rounded-xl text-sm font-semibold
                                                       hover:opacity-90 transition disabled:opacity-40 active:scale-[.98]">
                                            <svg x-show="avanzando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            Iniciar diagnóstico
                                        </button>
                                    </div>
                                </template>

                                {{-- diagnostico → form + cotizar --}}
                                <template x-if="detalle.estado === 'diagnostico'">
                                    <div class="space-y-3">
                                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-xl p-4 space-y-3">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ingresar diagnóstico y piezas</p>

                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Diagnóstico del técnico</label>
                                                <textarea x-model="formDiag.diagnostico" rows="2"
                                                          placeholder="Describe lo que encontró el técnico..."
                                                          class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
                                            </div>

                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Costo mano de obra</label>
                                                <input type="number" x-model="formDiag.costoManoObra" min="0" step="0.01"
                                                       placeholder="0.00"
                                                       class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                              focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                                            </div>

                                            {{-- ── Piezas con buscador de catálogo ── --}}
                                            <div>
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <label class="text-xs text-gray-400">Piezas / componentes</label>
                                                    <button type="button" @click="agregarPieza()"
                                                            class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700
                                                                   dark:hover:text-gray-200 underline underline-offset-2">
                                                        + Agregar manual
                                                    </button>
                                                </div>

                                                {{-- Buscador catálogo --}}
                                                <div class="relative mb-2">
                                                    <input type="text"
                                                           x-model="busquedaPieza"
                                                           @input.debounce.400ms="buscarPiezas()"
                                                           @focus="buscarPiezas()"
                                                           @keydown.escape="resultadosPiezas = []"
                                                           placeholder="Buscar pieza por nombre o clave..."
                                                           class="w-full text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <svg x-show="buscandoPieza"
                                                         class="animate-spin w-3.5 h-3.5 text-gray-400 absolute right-3 top-2"
                                                         fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                    </svg>

                                                    {{-- Dropdown resultados --}}
                                                    <div x-show="resultadosPiezas.length > 0"
                                                         x-transition:enter="transition ease-out duration-100"
                                                         x-transition:enter-start="opacity-0 -translate-y-1"
                                                         class="absolute z-20 top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800
                                                                border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg overflow-hidden">
                                                        <template x-for="rp in resultadosPiezas" :key="rp.id_pieza">
                                                            <button type="button"
                                                                    @click="seleccionarPiezaCatalogo(rp)"
                                                                    class="w-full flex items-center justify-between gap-3 px-3 py-2.5
                                                                           hover:bg-gray-50 dark:hover:bg-gray-700 transition text-left">
                                                                <div class="min-w-0">
                                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-200"
                                                                              x-text="rp.nombre"></span>
                                                                        <span class="text-[10px] font-mono bg-gray-100 dark:bg-gray-700
                                                                                     text-gray-500 px-1 rounded"
                                                                              x-text="rp.clave"></span>
                                                                        <span x-show="!rp.compatible"
                                                                              class="text-[10px] bg-amber-50 dark:bg-amber-900/20
                                                                                     text-amber-600 dark:text-amber-400 px-1.5 rounded
                                                                                     border border-amber-200 dark:border-amber-700">
                                                                            no verificada
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-[10px] mt-0.5"
                                                                       :class="rp.stock_bajo
                                                                           ? 'text-red-500 dark:text-red-400'
                                                                           : 'text-gray-400'"
                                                                       x-text="'Stock: ' + rp.stock_actual + (rp.stock_bajo ? ' ⚠ stock bajo' : '')">
                                                                    </p>
                                                                </div>
                                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 shrink-0"
                                                                      x-text="'$'+Number(rp.precio_venta).toLocaleString('es-MX')"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Lista piezas agregadas --}}
                                                <div class="space-y-2">
                                                    <template x-for="(p, idx) in formDiag.piezas" :key="idx">
                                                        <div class="grid grid-cols-12 gap-1.5 items-center">
                                                            <input type="text" x-model="p.descripcion"
                                                                   placeholder="Descripción"
                                                                   :readonly="!!p.id_pieza"
                                                                   :class="p.id_pieza ? 'bg-gray-50 dark:bg-gray-600' : ''"
                                                                   class="col-span-5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5
                                                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                                          focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                            <input type="number" x-model="p.cantidad" min="1" placeholder="Cant"
                                                                   class="col-span-2 text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5
                                                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                                          focus:outline-none focus:ring-1 focus:ring-gray-400 text-center">
                                                            <input type="number" x-model="p.precio_unitario" min="0" step="0.01" placeholder="Precio"
                                                                   class="col-span-4 text-xs border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5
                                                                          bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                                          focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                                                            <button type="button" @click="formDiag.piezas.splice(idx, 1)"
                                                                    class="col-span-1 text-gray-400 hover:text-red-500 transition text-center">×</button>
                                                        </div>
                                                    </template>
                                                    <p x-show="formDiag.piezas.length === 0"
                                                       class="text-xs text-gray-400 italic">Sin piezas — solo mano de obra</p>
                                                </div>
                                            </div>

                                            <button @click="guardarDiagnostico()"
                                                    :disabled="guardandoDiag || !formDiag.diagnostico.trim()"
                                                    class="w-full text-xs font-semibold py-2 rounded-lg
                                                           bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300
                                                           hover:bg-gray-300 dark:hover:bg-gray-500 transition disabled:opacity-40">
                                                <span x-text="guardandoDiag ? 'Guardando...' : 'Guardar diagnóstico'"></span>
                                            </button>
                                        </div>

                                        <div x-show="detalle?.diagnostico" class="flex gap-2 flex-wrap">
                                            <template x-if="detalle.cliente_email || detalle.cliente?.correo">
                                                <button @click="abrirModalCotizacion()"
                                                        class="flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                                                               text-white px-4 py-2.5 rounded-xl text-sm font-semibold
                                                               hover:opacity-90 transition active:scale-[.98]">
                                                    Enviar cotización por email
                                                </button>
                                            </template>
                                            <template x-if="!(detalle.cliente_email || detalle.cliente?.correo)">
                                                <div class="flex items-center gap-2 text-xs text-amber-600 dark:text-amber-400
                                                            bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700
                                                            rounded-xl px-3 py-2">
                                                    ⚠ Sin email — el cliente no puede recibir cotización.
                                                </div>
                                            </template>
                                            <button @click="avanzarEstado(detalle.id_reparacion, 'en_proceso')"
                                                    :disabled="avanzando"
                                                    class="flex items-center gap-1.5 border border-gray-200 dark:border-gray-600
                                                           text-gray-600 dark:text-gray-400 px-4 py-2.5 rounded-xl text-sm font-medium
                                                           hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40">
                                                Iniciar sin cotizar
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- cotizacion_enviada → resolver --}}
                                <template x-if="detalle.estado === 'cotizacion_enviada'">
                                    <div class="space-y-2">
                                        <template x-if="detalle.cotizacion && detalle.cotizacion.respuesta === null">
                                            <p class="text-xs text-gray-400 italic">
                                                Esperando respuesta del cliente…
                                                <span x-show="detalle.cotizacion.expires_at"
                                                      x-text="'Expira: ' + new Date(detalle.cotizacion.expires_at).toLocaleString('es-MX', {day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})">
                                                </span>
                                            </p>
                                        </template>
                                        <div class="flex gap-2 flex-wrap">
                                            <button @click="abrirModalResolver()"
                                                    class="flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                                                           text-white px-4 py-2.5 rounded-xl text-sm font-semibold
                                                           hover:opacity-90 transition active:scale-[.98]">
                                                Resolver manualmente
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- en_proceso → lista --}}
                                <template x-if="detalle.estado === 'en_proceso'">
                                    <button @click="avanzarEstado(detalle.id_reparacion, 'lista')"
                                            :disabled="avanzando"
                                            class="flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                                                   text-white px-4 py-2.5 rounded-xl text-sm font-semibold
                                                   hover:opacity-90 transition disabled:opacity-40 active:scale-[.98]">
                                        <svg x-show="avanzando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Marcar como lista
                                    </button>
                                </template>

                                {{-- lista → entregar --}}
                                <template x-if="detalle.estado === 'lista'">
                                    <button @click="avanzarEstado(detalle.id_reparacion, 'entregada')"
                                            :disabled="avanzando"
                                            class="flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                                                   text-white px-4 py-2.5 rounded-xl text-sm font-semibold
                                                   hover:opacity-90 transition disabled:opacity-40 active:scale-[.98]">
                                        <svg x-show="avanzando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Entregar y cobrar
                                    </button>
                                </template>

                                {{-- Cancelar --}}
                                <template x-if="!['entregada','cancelada','lista'].includes(detalle.estado)">
                                    <button @click="avanzarEstado(detalle.id_reparacion, 'cancelada')"
                                            :disabled="avanzando"
                                            class="text-xs text-red-500 dark:text-red-400 hover:underline transition">
                                        Cancelar orden
                                    </button>
                                </template>

                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL: Enviar cotización
    ══════════════════════════════════════════════ --}}
    <div x-show="modalCotizacion" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalCotizacion = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Enviar cotización al cliente</h3>
            <p class="text-xs text-gray-400 mb-4">
                El cliente recibirá un email con el desglose y un link para aceptar o rechazar.
                El link expira en 12 horas.
            </p>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                    Descripción del trabajo <span class="text-red-400">*</span>
                </label>
                <textarea x-model="formCot.descripcion" rows="3"
                          placeholder="Ej: Se reemplazará el motor trasero y se revisará el sistema de frenos..."
                          class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2.5
                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" @click="modalCotizacion = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" @click="enviarCotizacion()"
                        :disabled="enviandoCot || !formCot.descripcion.trim()"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40">
                    <span x-text="enviandoCot ? 'Enviando...' : 'Enviar cotización'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL: Resolver manualmente
    ══════════════════════════════════════════════ --}}
    <div x-show="modalResolver" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalResolver = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Resolución manual</h3>
            <p class="text-xs text-gray-400 mb-4">
                El cliente respondió en persona o la cotización expiró. Registra la decisión aquí.
            </p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Decisión</label>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="op in opcionesResolucion" :key="op.val">
                            <button type="button"
                                    @click="formRes.decision = op.val"
                                    :class="formRes.decision === op.val
                                        ? 'border-gray-900 dark:border-gray-300 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-900 dark:ring-gray-300'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'"
                                    class="border rounded-xl px-3 py-2.5 text-left transition-all">
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="op.label"></p>
                                <p class="text-[10px] text-gray-400 mt-0.5" x-text="op.desc"></p>
                            </button>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Nota <span class="font-normal text-gray-400">(opcional)</span>
                    </label>
                    <input type="text" x-model="formRes.nota"
                           placeholder="Ej: El cliente aceptó solo cambiar la batería"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button type="button" @click="modalResolver = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" @click="resolverCotizacion()"
                        :disabled="resolviendo || !formRes.decision"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40">
                    <span x-text="resolviendo ? 'Guardando...' : 'Confirmar decisión'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function mantIndex() {
    return {
        // ── Estado general ──
        items: [], detalle: null, seleccionada: null,
        cargando: true, cargandoDetalle: false, avanzando: false,
        guardandoDiag: false,
        filtroActivo: 'todas', paginaActual: 1, lastPage: 1,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,
        stats: { activas: 0, listas: 0 },
        formDiag: { diagnostico: '', costoManoObra: '', piezas: [] },

        // ── Buscador de piezas en diagnóstico ──
        busquedaPieza: '',
        buscandoPieza: false,
        resultadosPiezas: [],

        // ── Modales ──
        modalCotizacion: false,
        modalResolver:   false,
        enviandoCot:     false,
        resolviendo:     false,
        formCot: { descripcion: '' },
        formRes: { decision: '', nota: '', piezasAceptadas: [] },
        opcionesResolucion: [
            { val: 'aceptar',            label: 'Aceptar todo',       desc: 'Proceder con todo lo cotizado' },
            { val: 'aceptar_parcial',    label: 'Aceptar parcial',    desc: 'Solo algunas piezas' },
            { val: 'rechazar',           label: 'Rechazar',           desc: 'No procede la reparación' },
            { val: 'solo_mantenimiento', label: 'Solo mantenimiento', desc: 'Ignorar piezas extra' },
        ],

        filtros: [
            { val: 'todas',              label: 'Todas' },
            { val: 'recibida',           label: 'Recibidas' },
            { val: 'diagnostico',        label: 'Diagnóstico' },
            { val: 'cotizacion_enviada', label: 'Cotización enviada' },
            { val: 'en_proceso',         label: 'En proceso' },
            { val: 'lista',              label: 'Listas' },
        ],

        pasos: [
            { val: 'recibida',           label: 'Recibida' },
            { val: 'diagnostico',        label: 'Diagnóst.' },
            { val: 'cotizacion_enviada', label: 'Cotización' },
            { val: 'en_proceso',         label: 'Proceso' },
            { val: 'lista',              label: 'Lista' },
            { val: 'entregada',          label: 'Entregada' },
        ],

        init() { this.cargarItems(); },

        async cargarItems() {
            this.cargando = true;
            try {
                const p = new URLSearchParams({ page: this.paginaActual });
                if (this.filtroActivo !== 'todas') p.set('estado', this.filtroActivo);
                const res  = await fetch(`{{ route('reparaciones.index') }}?${p}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.ok) return;
                this.items    = data.data.data ?? [];
                this.lastPage = data.data.last_page ?? 1;
                this.stats.activas = this.items.filter(m => !['entregada','cancelada'].includes(m.estado)).length;
                this.stats.listas  = this.items.filter(m => m.estado === 'lista').length;
            } catch { this.flash('Error cargando órdenes', 'error'); }
            finally  { this.cargando = false; }
        },

        cambiarFiltro(val) {
            this.filtroActivo = val; this.paginaActual = 1;
            this.cerrarDetalle(); this.cargarItems();
        },

        cambiarPagina(n) {
            if (n < 1 || n > this.lastPage) return;
            this.paginaActual = n; this.cargarItems();
        },

        async seleccionar(id) {
            if (this.seleccionada === id) return;
            this.seleccionada     = id;
            this.busquedaPieza    = '';
            this.resultadosPiezas = [];
            await this.cargarDetalle(id);
        },

        // Recarga el detalle de la OT ya abierta (tras avanzar estado, guardar
        // diagnóstico, cotizar o resolver). A diferencia de seleccionar(), esta
        // SIEMPRE vuelve a pedir los datos aunque el id ya esté seleccionado —
        // por eso el panel dejaba de reflejar el estado nuevo: seleccionar()
        // se saltaba la recarga porque el id "ya estaba seleccionado".
        async recargarDetalle() {
            if (!this.seleccionada) return;
            await this.cargarDetalle(this.seleccionada);
        },

        async cargarDetalle(id) {
            this.detalle         = null;
            this.cargandoDetalle = true;
            this.formDiag        = { diagnostico: '', costoManoObra: '', piezas: [] };
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (data.ok) {
                    this.detalle = data.data;
                    if (this.detalle.diagnostico) {
                        this.formDiag.diagnostico   = this.detalle.diagnostico;
                        this.formDiag.costoManoObra = this.detalle.costo_mano_obra ?? '';
                        this.formDiag.piezas        = (this.detalle.piezas ?? []).map(p => ({
                            id_pieza:        p.id_pieza ?? null,
                            descripcion:     p.pieza?.nombre ?? p.descripcion ?? '',
                            cantidad:        p.cantidad,
                            precio_unitario: p.precio_unitario,
                        }));
                    }
                }
            } catch { this.flash('Error cargando detalle', 'error'); }
            finally  { this.cargandoDetalle = false; }
        },

        cerrarDetalle() {
            this.seleccionada     = null;
            this.detalle          = null;
            this.busquedaPieza    = '';
            this.resultadosPiezas = [];
        },

        agregarPieza() {
            this.formDiag.piezas.push({ id_pieza: null, descripcion: '', cantidad: 1, precio_unitario: '' });
        },

        // ── Buscador de piezas ────────────────────────────────────────────────

        async buscarPiezas() {
            if (this.busquedaPieza.trim().length < 2) {
                this.resultadosPiezas = [];
                return;
            }
            this.buscandoPieza = true;
            try {
                const p = new URLSearchParams({ q: this.busquedaPieza.trim() });
                const idModelo = this.detalle?.bicicleta?.id_modelo;
                if (idModelo) p.set('id_modelo', idModelo);

                const res  = await fetch(`{{ route('stock_piezas.buscar-diagnostico') }}?${p}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.ok) this.resultadosPiezas = data.data;
            } catch {}
            finally { this.buscandoPieza = false; }
        },

        seleccionarPiezaCatalogo(rp) {
            this.formDiag.piezas.push({
                id_pieza:        rp.id_pieza,
                descripcion:     rp.nombre,
                cantidad:        1,
                precio_unitario: rp.precio_venta,
            });
            this.busquedaPieza    = '';
            this.resultadosPiezas = [];
        },

        // ── Diagnóstico ───────────────────────────────────────────────────────

        async guardarDiagnostico() {
            if (!this.formDiag.diagnostico.trim()) return;
            this.guardandoDiag = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${this.detalle.id_reparacion}/diagnostico`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        diagnostico:     this.formDiag.diagnostico,
                        costo_mano_obra: parseFloat(this.formDiag.costoManoObra) || 0,  // ✓ ya está
                        piezas: this.formDiag.piezas
                            .filter(p => p.descripcion.trim())
                            .map(p => ({
                                id_pieza:        p.id_pieza ?? null,
                                descripcion:     p.descripcion.trim(),
                                cantidad:        parseInt(p.cantidad) || 1,
                                precio_unitario: parseFloat(p.precio_unitario) || 0,  // ✓ ya está
                            })),
                    }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash('Diagnóstico guardado.');
                await this.recargarDetalle();
                await this.cargarItems();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.guardandoDiag = false; }
        },

        // ── Modales cotización / resolver ─────────────────────────────────────

        abrirModalCotizacion() {
            this.formCot.descripcion = '';
            this.modalCotizacion     = true;
        },

        async enviarCotizacion() {
            if (!this.formCot.descripcion.trim()) return;
            this.enviandoCot = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${this.detalle.id_reparacion}/cotizacion`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ descripcion_trabajo: this.formCot.descripcion }),
                });
                const data = await res.json();
                this.modalCotizacion = false;
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash('Cotización enviada al cliente.');
                await this.recargarDetalle();
                await this.cargarItems();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.enviandoCot = false; }
        },

        abrirModalResolver() {
            this.opcionesResolucion = this.detalle.tipo === 'mantenimiento'
                ? [
                    { val: 'aceptar',            label: 'Incluir piezas',     desc: 'Proceder con el reemplazo' },
                    { val: 'solo_mantenimiento',  label: 'Solo mantenimiento', desc: 'Ignorar piezas extra' },
                  ]
                : [
                    { val: 'aceptar',         label: 'Aceptar todo',    desc: 'Proceder con todo lo cotizado' },
                    { val: 'aceptar_parcial', label: 'Aceptar parcial', desc: 'Solo algunas piezas' },
                    { val: 'rechazar',        label: 'Rechazar',        desc: 'No procede la reparación' },
                  ];
            this.formRes       = { decision: '', nota: '', piezasAceptadas: [] };
            this.modalResolver = true;
        },

        async resolverCotizacion() {
            if (!this.formRes.decision) return;
            this.resolviendo = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${this.detalle.id_reparacion}/resolver`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        decision:         this.formRes.decision,
                        nota:             this.formRes.nota || null,
                        piezas_aceptadas: this.formRes.decision === 'aceptar_parcial'
                                            ? (this.formRes.piezasAceptadas ?? [])
                                            : [],
                    }),
                });
                const data = await res.json();
                this.modalResolver = false;
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash('Decisión registrada.');
                await this.recargarDetalle();
                await this.cargarItems();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.resolviendo = false; }
        },

        // ── Avanzar estado ────────────────────────────────────────────────────

        async avanzarEstado(id, estado) {
            this.avanzando = true;
            try {
                const res  = await fetch(`{{ url('sucursal/reparaciones') }}/${id}/estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ estado }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash(data.mensaje);
                await this.recargarDetalle();
                await this.cargarItems();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.avanzando = false; }
        },

        // ── Helpers ───────────────────────────────────────────────────────────

        cotizacionExpirada(m) {
            if (!m.cotizacion) return false;
            if (m.cotizacion.respuesta !== null) return false;
            return m.cotizacion.expires_at && new Date(m.cotizacion.expires_at) < new Date();
        },

        pasoActivo(val) {
            const orden = ['recibida','diagnostico','cotizacion_enviada','en_proceso','lista','entregada'];
            return orden.indexOf(val) <= orden.indexOf(this.detalle?.estado ?? '');
        },

        estadoLabel(e) {
            return {
                recibida:           'Recibida',
                diagnostico:        'Diagnóstico',
                cotizacion_enviada: 'Cotización enviada',
                en_proceso:         'En proceso',
                lista:              'Lista',
                entregada:          'Entregada',
                cancelada:          'Cancelada',
            }[e] ?? e;
        },

        estadoClass(e) {
            return {
                recibida:           'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                diagnostico:        'bg-blue-100 dark:bg-blue-800/30 text-blue-800 dark:text-blue-400',
                cotizacion_enviada: 'bg-amber-100 dark:bg-amber-800/30 text-amber-800 dark:text-amber-400',
                en_proceso:         'bg-green-100 dark:bg-green-800/30 text-green-800 dark:text-green-400',
                lista:              'bg-teal-100 dark:bg-teal-800/30 text-teal-800 dark:text-teal-400',
                entregada:          'bg-gray-100 dark:bg-gray-700 text-gray-500',
                cancelada:          'bg-red-100 dark:bg-red-800/30 text-red-800 dark:text-red-400',
            }[e] ?? '';
        },

        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
        },
    }
}
</script>
</x-app-layout>