<x-app-layout>
    @php
    $totalRequerido = collect($resumen)->sum('requerido');
    $totalEscaneado = collect($resumen)->sum('escaneado');
    @endphp

    <div
        x-data="realizarPedido({{ Js::from($resumen) }}, '{{ route('gestor.vehiculos.bicicletas.store') }}')"
        x-init="init()"
        class="max-w-7xl mx-auto px-4 sm:px-6 py-6 pb-28 lg:pb-8 space-y-6"
        :class="isMobile ? 'pb-28' : ''">

        {{-- ===========================
             HEADER
             =========================== --}}
        <div class="animate-fade-up">
            {{-- Breadcrumb minimalista --}}
            <div class="flex items-center gap-1.5 mb-4 text-xs text-gray-500 dark:text-gray-400 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('pedidos.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">Pedidos</a>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-400  text-xs">#{{ $pedido->id_pedido }}</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Realizar Pedido</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-100 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Realizar Pedido</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ optional($pedido->negocio)->nombre_negocio ?? '—' }} 
                            <span class="text-gray-300 dark:text-gray-600 mx-1">•</span> 
                            {{ optional($pedido->usuario)->nombre_usuario ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Progreso circular + contador --}}
                <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-gray-700">
                    <div class="text-right">
                        <p class="text-[10px] sm:text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-0.5">Progreso</p>
                        <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white ">
                            <span x-text="totalEscaneado" :class="justRegistered ? 'count-bump' : ''"></span>
                            <span class="text-gray-400 font-normal text-sm">/ {{ $totalRequerido }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 relative">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="16" fill="none" stroke="#e5e7eb" stroke-width="3" class="dark:stroke-gray-700"/>
                            <circle cx="20" cy="20" r="16" fill="none"
                                :stroke="totalEscaneado >= {{ $totalRequerido }} ? '#16a34a' : '#111827'"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-dasharray="100.5"
                                :stroke-dashoffset="100.5 - (Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 100.5)"
                                class="transition-all duration-500 ease-out"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-gray-900 dark:text-white "
                            x-text="Math.round(Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 100) + '%'">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Barra de progreso lineal --}}
            <div class="mt-5 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 ease-out"
                    :class="totalEscaneado >= {{ $totalRequerido }} ? 'bg-green-600' : 'bg-gray-900 dark:bg-white'"
                    :style="`width:${Math.min(100, Math.round(totalEscaneado / {{ $totalRequerido }} * 100))}%`">
                </div>
            </div>
        </div>

        {{-- ===========================
             BOTONES DE ACCIÓN DESKTOP
             =========================== --}}
        <div class="hidden lg:flex items-center justify-end gap-3 animate-fade-up delay-1">
            <button type="button" @click="loteModal = true"
                class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Lote de Batería
            </button>

            <button type="button" @click="infoModal = true"
                class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Info de Envío
            </button>

            <button type="button" @click="scanModal = true; $nextTick(() => $refs.qrInput?.focus())"
                class="flex items-center gap-2 px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Escanear Bicicleta
            </button>

            <button type="button" x-show="totalEscaneado >= {{ $totalRequerido }}" x-cloak @click="finalizarPedido()"
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Finalizar Pedido
            </button>
        </div>

        {{-- ===========================
             TABLA DE ESTADO
             =========================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden animate-fade-up delay-2">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado del Pedido</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                        <span x-text="resumen.filter(i => i.escaneado >= i.requerido).length"></span>
                        <span class="mx-0.5">/</span>
                        <span>{{ count($resumen) }}</span>
                        <span class="ml-1">completados</span>
                    </span>
                </div>
                
                {{-- Botones mobile accesorios --}}
                <div class="lg:hidden flex items-center gap-2">
                    <button type="button" @click="loteModal = true" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </button>
                    <button type="button" @click="infoModal = true" class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Voltaje</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Req.</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Esc.</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Números de Serie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <template x-for="(item, idx) in resumen" :key="idx">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                                :class="item._flash ? 'bg-blue-50/50 dark:bg-blue-900/10' : ''">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full" :class="item.escaneado >= item.requerido ? 'bg-green-500' : 'bg-gray-400'"></div>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="item.modelo"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="item.color"></td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300  text-xs" x-text="item.voltaje"></td>
                                <td class="px-4 py-3 text-center  text-gray-900 dark:text-white" x-text="item.requerido"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class=" font-semibold" 
                                          :class="item.escaneado >= item.requerido ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white'"
                                          x-text="item.escaneado">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <template x-if="item.escaneado >= item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Completo
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado > 0 && item.escaneado < item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span x-text="item.requerido - item.escaneado"></span>
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado === 0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                            Pendiente
                                        </span>
                                    </template>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        <template x-for="serie in item.num_series" :key="serie">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                <span class="" x-text="serie"></span>
                                                <button type="button" @click="abrirModalEliminar(serie)" class="text-gray-400 hover:text-red-500 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="!item.num_series || item.num_series.length === 0" class="text-xs text-gray-400 ">—</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            {{-- Indicador scroll mobile --}}
            <div class="lg:hidden text-xs text-gray-400 text-center py-2 border-t border-gray-200 dark:border-gray-700">
                <span>← Desliza para ver más →</span>
            </div>
        </div>

        {{-- ===========================
             FORMULARIO DE EMISIÓN (PDF)
             =========================== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden animate-fade-up delay-3">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Formulario de Emisión
                </span>
                <a :href="pdfUrl" target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 transition-colors border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar PDF
                </a>
            </div>
            
            <div class="hidden md:block">
                <iframe x-ref="pdfFrame" src="{{ route('pedidos.pdf', $pedido->id_pedido) }}"
                    class="w-full bg-gray-100 dark:bg-gray-900" style="height:600px; border:none;"></iframe>
            </div>
            
            <div class="md:hidden p-8 text-center bg-gray-50 dark:bg-gray-800/50">
                <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Vista previa no disponible en móvil</p>
                <button @click="pdfModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                    Ver Formulario
                </button>
            </div>
        </div>

        {{-- ===========================
             MOBILE: BOTONES FLOTANTES
             =========================== --}}
        {{-- FAB Principal: Escanear --}}
        <button type="button" @click="scanModal = true; $nextTick(() => $refs.qrInput?.focus())"
            class="fixed bottom-6 right-4 lg:hidden w-14 h-14 rounded-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform z-40"
            :class="{ '!bottom-24': totalEscaneado >= {{ $totalRequerido }} }">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </button>

        {{-- FAB Secundario: Finalizar (solo cuando está completo) --}}
        <button type="button" x-show="totalEscaneado >= {{ $totalRequerido }}" x-cloak @click="finalizarPedido()"
            class="fixed bottom-6 right-4 lg:hidden w-14 h-14 rounded-full bg-green-600 text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform z-40"
            style="bottom: 6rem;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </button>

        {{-- ===========================
             MODAL: ESCANEAR (Bottom Sheet en Mobile)
             =========================== --}}
        <div x-show="scanModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 bg-black/50 backdrop-blur-[2px]"
            @click.self="scanModal = false; cerrarCamara()">

            <div x-show="scanModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-xl shadow-2xl overflow-hidden max-h-[90vh] sm:max-h-none overflow-y-auto"
                @click.stop>
                
                {{-- Handle para mobile --}}
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto my-3 sm:hidden"></div>

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Registrar Bicicleta</h3>
                    <button @click="scanModal = false; cerrarCamara()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Input de Serie --}}
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Número de Serie <span class="text-gray-400 ml-1">(17 caracteres)</span></label>
                        <div class="relative">
                            <input
                                x-ref="qrInput"
                                type="text"
                                x-model="numSerie"
                                @keydown.enter.prevent="onQrIngresado()"
                                @input="numSerie = $event.target.value.toUpperCase()"
                                maxlength="17"
                                placeholder="Ingresa el código..."
                                autocomplete="off"
                                :class="inputClass"
                                class="w-full pl-4 pr-20 py-3 rounded-lg border text-base  tracking-wider focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                <span class="text-xs text-gray-400 " x-text="numSerie.length + '/17'"></span>
                                <button type="button" @click="limpiarQr()" x-show="numSerie.length > 0"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Selects --}}
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Modelo</label>
                            <select x-model="formBic.id_modelo" @change="onModeloChange()"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                                <option value="">— Seleccionar modelo —</option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Voltaje</label>
                                <select x-model="formBic.id_voltaje" :disabled="!formBic.voltajes.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">—</option>
                                    <template x-for="v in formBic.voltajes" :key="v.id_voltaje">
                                        <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Color</label>
                                <select x-model="formBic.id_color" :disabled="!formBic.colores.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">—</option>
                                    <template x-for="c in formBic.colores" :key="c.id_color">
                                        <option :value="c.id_color" x-text="c.color"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Vista cámara --}}
                    <div x-show="camaraActiva" x-cloak class="relative rounded-xl overflow-hidden bg-black" style="height:200px;">
                        <video x-ref="videoEl" autoplay playsinline class="w-full h-full object-cover opacity-90"></video>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative w-32 h-32">
                                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-gray-900 dark:border-white rounded-tl"></div>
                                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-gray-900 dark:border-white rounded-tr"></div>
                                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-gray-900 dark:border-white rounded-bl"></div>
                                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-gray-900 dark:border-white rounded-br"></div>
                                <div class="absolute top-1/2 left-0 right-0 h-px bg-gray-900/50 dark:bg-white/50 animate-pulse"></div>
                            </div>
                        </div>
                        <button @click="cerrarCamara()" class="absolute top-2 right-2 bg-black/50 text-white p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <canvas x-ref="canvasEl" class="hidden"></canvas>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="abrirCamara()"
                            class="flex items-center justify-center gap-2 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="hidden sm:inline">Cámara</span>
                        </button>
                        <button type="button" @click="registrarBicicleta()"
                            :disabled="!numSerie || numSerie.length !== 17 || !formBic.id_modelo || !formBic.id_voltaje || !formBic.id_color || guardando"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                            <template x-if="!guardando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </template>
                            <template x-if="guardando">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </template>
                            <span x-text="guardando ? 'Registrando...' : 'Registrar'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODAL: LOTE DE BATERÍA
             =========================== --}}
        <div x-show="loteModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 bg-black/50 backdrop-blur-[2px]"
            @click.self="loteModal = false">

            <div x-show="loteModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-xl shadow-2xl overflow-hidden max-h-[80vh] flex flex-col"
                @click.stop>
                
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto my-3 sm:hidden"></div>

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Lote de Batería</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Máximo {{ $totalRequerido }} entradas</p>
                    </div>
                    <button @click="loteModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 overflow-y-auto space-y-3 flex-1">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ingresa los códigos de lote en orden. Puedes dejar vacíos los que no apliquen.</p>
                    
                    <template x-for="(lote, idx) in loteBaterias" :key="idx">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-400  w-6 text-right" x-text="idx + 1"></span>
                            <input type="text" x-model="loteBaterias[idx]" @keydown.enter.prevent="idx < loteBaterias.length - 1 && $refs['loteInput' + (idx + 1)]?.[0]?.focus()" :x-ref="'loteInput' + idx" maxlength="30" placeholder="Código de lote..." autocomplete="off"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm  focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </template>
                </div>

                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-2 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" @click="limpiarLotes()" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-white dark:hover:bg-gray-700 transition flex-1">
                        Limpiar
                    </button>
                    <button type="button" @click="aplicarLotes()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Aplicar
                    </button>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODAL: INFO DE ENVÍO
             =========================== --}}
        <div x-show="infoModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 bg-black/50 backdrop-blur-[2px]"
            @click.self="infoModal = false">

            <div x-show="infoModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-xl shadow-2xl overflow-hidden"
                @click.stop>
                
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto my-3 sm:hidden"></div>

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Información de Envío</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Datos del transporte y costos</p>
                    </div>
                    <button @click="infoModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs text-gray-500 dark:text-gray-400">Distancia</label>
                            <input type="text" x-model="distancia" placeholder="Ej: 120 km" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs text-gray-500 dark:text-gray-400">Transporte</label>
                            <input type="text" x-model="transporte" placeholder="Ej: Camión" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs text-gray-500 dark:text-gray-400">Costo de Envío</label>
                        <input type="number" x-model="costo_envio" placeholder="Ej: 1500" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex gap-2 bg-gray-50 dark:bg-gray-800/50">
                    <button type="button" @click="infoModal = false" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-white dark:hover:bg-gray-700 transition flex-1">
                        Cancelar
                    </button>
                    <button type="button" @click="aplicarInfo()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                        Guardar
                    </button>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODAL: ERROR
             =========================== --}}
        <div x-show="errorModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
            @click.self="errorModal = false">
            
            <div x-show="errorModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Bicicleta no válida</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="errorMensaje"></p>
                    </div>
                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left border border-gray-200 dark:border-gray-700">
                        <p class="text-[10px] font-medium text-gray-400 mb-2 uppercase tracking-wider">Datos escaneados</p>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between"><span class="text-gray-500">Serie</span><span class=" font-semibold text-gray-900 dark:text-white" x-text="numSerie"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Modelo</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.modeloNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Color</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.colorNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Voltaje</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.voltajeNombre || '—'"></span></div>
                        </div>
                    </div>
                    <button @click="errorModal = false; limpiarQr()" class="w-full py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                        Intentar de nuevo
                    </button>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODAL: ELIMINAR
             =========================== --}}
        <div x-show="eliminarModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
            @click.self="eliminarModal = false">
            
            <div x-show="eliminarModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">¿Eliminar bicicleta?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Serie a eliminar:</p>
                        <p class="text-sm  font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 py-2 px-3 rounded-lg break-all border border-gray-200 dark:border-gray-600" x-text="numSerieEliminar"></p>
                        <p class="text-xs text-gray-400 mt-3">Esta acción no se puede deshacer</p>
                    </div>
                    <div class="flex gap-3 w-full">
                        <button @click="eliminarModal = false" class="flex-1 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button @click="confirmarEliminar()" :disabled="eliminando" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!eliminando">Eliminar</span>
                            <span x-show="eliminando" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODAL: PEDIDO COMPLETO
             =========================== --}}
        <div x-show="completoModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4">
            
            <div x-show="completoModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-sm text-center mx-4">
                
                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">¡Pedido Completo!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Todas las bicicletas han sido registradas correctamente</p>
                
                <div class="flex flex-col gap-2">
                    <a :href="pdfUrl" target="_blank" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descargar PDF
                    </a>
                    <a href="{{ route('pedidos.index') }}" class="w-full py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                        Volver a Pedidos
                    </a>
                </div>
            </div>
        </div>
{{-- ===========================
     MODAL PDF MOBILE (PDF Oficial + Estilo Emisión Rápida)
     =========================== --}}
<div x-show="pdfModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 backdrop-blur-[2px] z-50 md:hidden"
    @click.self="pdfModal = false">

    <div x-show="pdfModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        class="bg-gray-50 dark:bg-gray-900 w-full h-[92vh] rounded-t-2xl shadow-2xl overflow-hidden flex flex-col"
        style="position: fixed; bottom: 0; left: 0; right: 0;"
        @click.stop>
        
        {{-- Handle visual --}}
        <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto my-3"></div>

        {{-- Header estilizado Emisión Rápida --}}
        <div class="flex items-center justify-between px-5 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Formulario de Emisión</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pedido #{{ $pedido->id_pedido }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button @click="imprimirFormulario()" 
                    class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
                    title="Imprimir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </button>
                <button @click="pdfModal = false" 
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Contenido PDF (Estructura Original Preservada) --}}
        <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-100 p-4">
            <div class="overflow-x-auto pb-4">
                <div style="min-width:600px; font-family:Arial,sans-serif; font-size:10px; font-weight:bold;">
                    
                    {{-- Encabezado PDF Original --}}
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td colspan="7" style="text-align:center; padding:8px; border:1px solid #000; font-size:14px; font-weight:bold; font-style:italic;">
                                Formulario de Emisión de Fábrica
                            </td>
                        </tr>
                        <tr>
                            <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;"><strong>Fecha:</strong><br>{{ now()->format('d/m/Y') }}</td>
                            <td style="width:18%;text-align:center;border:1px solid #000;padding:4px;"><strong>Código:</strong><br>{{ $pedido->id_pedido }}</td>
                            <td style="width:21%;text-align:center;border:1px solid #000;padding:4px;"><strong>Cliente:</strong><br>{{ optional($pedido->usuario)->nombre_usuario ?? '' }}</td>
                            <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;"><strong>Distancia:</strong><br>/</td>
                            <td style="width:25%;text-align:center;border:1px solid #000;padding:4px;"><strong>Transporte:</strong><br>{{ $pedido->notas ?? 'Recoge en fabrica' }}</td>
                            <td style="width:16%;text-align:center;border:1px solid #000;padding:4px;"><strong>Costo Envío:</strong><br>/</td>
                        </tr>
                    </table>

                    @php
                    $modelGroups = [];
                    foreach ($pedido->items as $item) {
                        $m = optional($item->modelo)->nombre_modelo ?? 'N/D';
                        $v = optional($item->voltaje)->voltaje ?? 'Sin Pilas';
                        $c = optional($item->color)->color ?? 'N/D';
                        $modelGroups[$m]['voltajes'][$v]['colores'][$c][] = $item;
                    }
                    $bicGroups = [];
                    foreach ($pedido->bicicletas as $bic) {
                        $m = optional($bic->modelo)->nombre_modelo ?? 'N/D';
                        $v = optional($bic->voltaje)->voltaje ?? 'Sin Pilas';
                        $c = optional($bic->color)->color ?? 'N/D';
                        $bicGroups[$m][$v][$c][] = $bic->num_serie;
                    }

                    $filas = [];
                    foreach ($modelGroups as $modelName => $modelGroup) {
                        foreach ($modelGroup['voltajes'] as $voltajeName => $voltGroup) {
                            foreach ($voltGroup['colores'] as $colorName => $items) {
                                $cantidad  = $items[0]->cantidad;
                                $numSeries = $bicGroups[$modelName][$voltajeName][$colorName] ?? [];
                                for ($i = 0; $i < max(1, $cantidad); $i++) {
                                    $filas[] = [
                                        'modelo'   => $modelName,
                                        'color'    => $colorName,
                                        'cantidad' => $cantidad,
                                        'serie'    => $numSeries[$i] ?? '',
                                        'lote'     => $lotes[count($filas)] ?? '',
                                    ];
                                }
                            }
                        }
                    }

                    $n = count($filas);
                    $modeloRowspan = array_fill(0, $n, 0);
                    $colorRowspan  = array_fill(0, $n, 0);
                    $skipModelo    = array_fill(0, $n, false);
                    $skipColor     = array_fill(0, $n, false);

                    $i = 0;
                    while ($i < $n) {
                        $j = $i;
                        while ($j < $n && $filas[$j]['modelo'] === $filas[$i]['modelo']) $j++;
                        $modeloRowspan[$i] = $j - $i;

                        $k = $i;
                        while ($k < $j) {
                            $l = $k;
                            while ($l < $j && $filas[$l]['color'] === $filas[$k]['color']) $l++;
                            $colorRowspan[$k] = $l - $k;
                            for ($m2 = $k + 1; $m2 < $l; $m2++) $skipColor[$m2] = true;
                            $k = $l;
                        }
                        for ($m2 = $i + 1; $m2 < $j; $m2++) $skipModelo[$m2] = true;
                        $i = $j;
                    }

                    // Cargadores y baterías
                    $cargadoresMobile = [];
                    $bateriasMobile   = [];
                    foreach ($pedido->items as $item) {
                        $modelo   = optional($item->modelo)->nombre_modelo ?? '';
                        $voltaje  = optional($item->voltaje)->voltaje ?? '';
                        $cantidad = $item->cantidad;
                        if ($modelo === 'VmpS5') {
                            $cargadoresMobile['48V/12Ah'] = ($cargadoresMobile['48V/12Ah'] ?? 0) + $cantidad;
                            $bateriasMobile['12V/12Ah']   = ($bateriasMobile['12V/12Ah']   ?? 0) + ($cantidad * 4);
                        } else {
                            $volts = intval($voltaje);
                            $numBaterias = intval($volts / 12);
                            if ($volts === 48) $cargadoresMobile['48V/20Ah'] = ($cargadoresMobile['48V/20Ah'] ?? 0) + $cantidad;
                            elseif ($volts === 60) $cargadoresMobile['60V']  = ($cargadoresMobile['60V']       ?? 0) + $cantidad;
                            elseif ($volts === 72) $cargadoresMobile['72V']  = ($cargadoresMobile['72V']       ?? 0) + $cantidad;
                            $bateriasMobile['12V/20Ah'] = ($bateriasMobile['12V/20Ah'] ?? 0) + ($cantidad * $numBaterias);
                        }
                    }
                    @endphp

                    {{-- Tabla de ítems Original --}}
                    <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                        <thead>
                            <tr>
                                <th style="border:1px solid #000;padding:4px;width:5%;text-align:center;">No.</th>
                                <th style="border:1px solid #000;padding:4px;width:18%;text-align:center;">Modelo</th>
                                <th style="border:1px solid #000;padding:4px;width:18%;text-align:center;">Color</th>
                                <th style="border:1px solid #000;padding:4px;width:8%;text-align:center;">Cant.</th>
                                <th style="border:1px solid #000;padding:4px;width:22%;text-align:center;">No. Serie</th>
                                <th style="border:1px solid #000;padding:4px;width:5%;text-align:center;">No. Motor</th>
                                <th style="border:1px solid #000;padding:4px;width:16%;text-align:center;">Lote Batería</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filas as $idx => $fila)
                            <tr>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $idx + 1 }}</td>

                                @if (!$skipModelo[$idx])
                                <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $modeloRowspan[$idx] }}">
                                    {{ $fila['modelo'] }}
                                </td>
                                @endif

                                @if (!$skipColor[$idx])
                                <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $colorRowspan[$idx] }}">
                                    {{ $fila['color'] }}
                                </td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $colorRowspan[$idx] }}">
                                    {{ $fila['cantidad'] }}
                                </td>
                                @endif

                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $fila['serie'] }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $fila['lote'] }}</td>
                            </tr>
                            @endforeach

                            {{-- Cargadores --}}
                            @php $globalIdx = count($filas); @endphp
                            @foreach ($cargadoresMobile as $spec => $qty)
                            <tr>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $globalIdx + 1 }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">Cargadores</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $spec }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $qty }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                            </tr>
                            @php $globalIdx++; @endphp
                            @endforeach

                            {{-- Baterías --}}
                            @foreach ($bateriasMobile as $spec => $qty)
                            <tr>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $globalIdx + 1 }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">Baterías</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $spec }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $qty }}</td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                                <td style="border:1px solid #000;padding:4px;text-align:center;"></td>
                            </tr>
                            @php $globalIdx++; @endphp
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Firmas Original --}}
                    <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                        <tr>
                            <td style="width:59%; height:70px; font-size:9px; padding:4px; font-style:italic; text-align:center; font-weight:bold; border:1px solid #000;">
                                Este pedido es por duplicado, uno se enviará al destino con la mercancía, otro se guardará en fábrica y el archivo electrónico se enviará al departamento comercial.
                            </td>
                            <td rowspan="2" style="width:41%; vertical-align:top; font-size:9px; padding:4px; font-style:italic; text-align:center; font-weight:bold; border:1px solid #000;">
                                Sello o firma del responsable de fábrica:
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:2px; font-size:9px; height:18px; line-height:1; border:1px solid #000;">Firma del inspector de calidad:</td>
                        </tr>
                    </table>
                    <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                        <tr>
                            <td style="width:40%; padding:5px; border:1px solid #000;">Firma del chofer:<br></td>
                            <td style="width:60%; padding:5px; border:1px solid #000;">Teléfono chofer:<br></td>
                        </tr>
                    </table>
                    <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                        <tr>
                            <td style="font-weight:bold; font-style:italic; text-align:center; border:1px solid #000; padding:4px;">Recibo de Emisión</td>
                        </tr>
                    </table>
                    <table style="width:100%; border-collapse:collapse; border-left:1px solid #000; border-right:1px solid #000;">
                        <tr>
                            <td style="width:33%; padding:5px; border:1px solid #000;">Verificación de orden de emisión</td>
                            <td style="width:33%; padding:5px; border:1px solid #000;">Verificado</td>
                            <td style="width:33%; padding:5px; border:1px solid #000;">Error de verificarlo</td>
                        </tr>
                    </table>
                    <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                        <tr>
                            <td style="font-style:italic; text-align:center; height:50px; vertical-align:top; border:1px solid #000; padding:4px;">
                                Firma del responsable de la tienda (el recibo se recibirá tras confirmar el pedido):
                            </td>
                        </tr>
                    </table>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="border:1px solid #000; padding:5px;">Observación:</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:5px; height:30px;">
                                Para cualquier aclaración o informe de daños comuníquese al siguiente número &nbsp; 56 7716 5697
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000; padding:5px; height:25px; color:red;">
                                El pedido deberá ser supervisado por el cliente, una vez firmado este documento la empresa no se hace responsable de cualquier daño o pérdida que pueda ocurrir durante el transporte o después de la entrega.
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Footer Estilo Emisión Rápida --}}
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shrink-0">
            <button @click="imprimirFormulario()" 
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3.5 rounded-lg text-sm font-semibold hover:opacity-90 transition active:scale-[0.98] flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Formulario Oficial
            </button>
        </div>
    </div>
</div>

        {{-- ===========================
             ANIMACIONES CSS
             =========================== --}}
        <style>
            [x-cloak] { display: none !important; }
            
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animate-fade-up {
                animation: fadeInUp 0.3s ease forwards;
            }
            
            .delay-1 { animation-delay: 0.05s; }
            .delay-2 { animation-delay: 0.1s; }
            .delay-3 { animation-delay: 0.15s; }

            @keyframes countBump {
                0% { transform: scale(1); }
                40% { transform: scale(1.3); color: #111827; }
                100% { transform: scale(1); }
            }
            
            .dark .count-bump { 40% { color: white; } }
            
            .count-bump { animation: countBump .35s cubic-bezier(.22, .68, 0, 1.5) both; }

            /* Scrollbar refinada */
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
            .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
            ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        </style>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('realizarPedido', (resumenInicial, storeUrl) => ({
                resumen: Object.values(resumenInicial).map(item => ({
                    ...item,
                    num_series: [],
                    _flash: false,
                    escaneado: item.escaneado ?? 0,
                })),
                numSerie: '',
                guardando: false,
                camaraActiva: false,
                scannerInterval: null,
                scanModal: false,
                justRegistered: false,
                loteModal: false,
                infoModal: false,
                errorModal: false,
                completoModal: false,
                pdfModal: false,
                errorMensaje: '',
                inputClass: 'border-gray-300 dark:border-gray-600 focus:ring-gray-900',
                eliminando: false,
                eliminarModal: false,
                numSerieEliminar: '',
                pdfUrl: '{{ route("pedidos.pdf", $pedido->id_pedido) }}',
                isMobile: window.innerWidth < 1024,

                formBic: {
                    id_modelo: '',
                    id_voltaje: '',
                    id_color: '',
                    voltajes: [],
                    colores: [],
                    modeloNombre: '',
                    voltajeNombre: '',
                    colorNombre: '',
                },

                loteBaterias: Array({{ $totalRequerido }}).fill(''),
                
                distancia: '{{ $pedido->distancia ?? "" }}',
                transporte: '{{ $pedido->transporte ?? "" }}',
                costo_envio: '{{ $pedido->costo_envio ?? "" }}',

                get totalEscaneado() {
                    return this.resumen.reduce((sum, i) => sum + i.escaneado, 0);
                },

                init() {
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                    });

                    @foreach($pedido->bicicletas as $bic)
                    @php $key = $bic->id_modelo . '-' . $bic->id_voltaje . '-' . $bic->id_color; @endphp
                    (function() {
                        const key = '{{ $key }}';
                        const idx = this.resumen.findIndex(i =>
                            (i.id_modelo + '-' + i.id_voltaje + '-' + i.id_color) === key
                        );
                        if (idx !== -1) {
                            if (!this.resumen[idx].num_series) this.resumen[idx].num_series = [];
                            if (!this.resumen[idx].num_series.includes('{{ $bic->num_serie }}')) {
                                this.resumen[idx].num_series.push('{{ $bic->num_serie }}');
                            }
                        }
                    }).call(this);
                    @endforeach

                    this.resumen = this.resumen.map(item => ({
                        ...item,
                        escaneado: item.num_series.length,
                    }));

                    this.$nextTick(() => {
                        if (this.$refs.qrInput) this.$refs.qrInput.focus();
                    });
                },

                limpiarQr() {
                    this.numSerie = '';
                    this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-gray-900';
                    this.$nextTick(() => {
                        if (this.$refs.qrInput) this.$refs.qrInput.focus();
                    });
                },

                onQrIngresado() {
                    if (this.numSerie.length === 17 && this.formBic.id_modelo && this.formBic.id_voltaje && this.formBic.id_color) {
                        this.registrarBicicleta();
                    }
                },

                abrirModalEliminar(numSerie) {
                    this.numSerieEliminar = numSerie;
                    this.eliminarModal = true;
                },

                async confirmarEliminar() {
                    if (this.eliminando) return;
                    this.eliminando = true;

                    try {
                        const resp = await fetch(`/gestor/vehiculos/bicicletas/${this.numSerieEliminar}/pedido`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });

                        const data = await resp.json();

                        if (!resp.ok || !data.ok) {
                            alert(data.mensaje ?? 'Error al eliminar.');
                            return;
                        }

                        this.resumen = this.resumen.map(item => {
                            if (item.num_series && item.num_series.includes(this.numSerieEliminar)) {
                                return {
                                    ...item,
                                    escaneado: Math.max(0, item.escaneado - 1),
                                    num_series: item.num_series.filter(s => s !== this.numSerieEliminar),
                                };
                            }
                            return item;
                        });

                        if (this.$refs.pdfFrame) {
                            this.$refs.pdfFrame.src = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                        }
                        this.pdfUrl = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();

                        this.eliminarModal = false;
                        this.numSerieEliminar = '';

                    } catch (e) {
                        console.error(e);
                        alert('Error de conexión.');
                    } finally {
                        this.eliminando = false;
                    }
                },

                aplicarInfo() {
                    if (this.costo_envio && isNaN(this.costo_envio)) {
                        alert('El costo de envío debe ser un número válido');
                        return;
                    }
                    
                    const params = new URLSearchParams();
                    
                    if (this.distancia && this.distancia.trim() !== '') params.append('distancia', this.distancia);
                    if (this.transporte && this.transporte.trim() !== '') params.append('transporte', this.transporte);
                    if (this.costo_envio && this.costo_envio.toString().trim() !== '') params.append('costo_envio', this.costo_envio);
                    
                    const lotesFiltrados = this.loteBaterias.map(l => l.trim());
                    lotesFiltrados.forEach((l, i) => {
                        if (l) params.append('lotes[' + i + ']', l);
                    });
                    
                    const base = '{{ route("pedidos.pdf", $pedido->id_pedido) }}';
                    const url = params.toString() ? base + '?' + params.toString() : base;
                    
                    if (this.$refs.pdfFrame) this.$refs.pdfFrame.src = url;
                    this.pdfUrl = url;
                    this.infoModal = false;
                },

                async finalizarPedido() {
                    if (this.totalEscaneado < {{ $totalRequerido }}) {
                        alert('El pedido no está completo aún');
                        return;
                    }
                    
                    this.guardando = true;
                    
                    try {
                        const statusResponse = await fetch('{{ route("pedidos.status", $pedido->id_pedido) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: 3 })
                        });
                        
                        const contentType = statusResponse.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            const text = await statusResponse.text();
                            console.error('Respuesta no JSON:', text);
                            alert('Error: El servidor no devolvió una respuesta válida.');
                            this.guardando = false;
                            return;
                        }
                        
                        const statusData = await statusResponse.json();
                        if (!statusResponse.ok || !statusData.ok) {
                            alert('Error al actualizar el status: ' + (statusData.mensaje || 'Error desconocido'));
                            this.guardando = false;
                            return;
                        }

                        const params = new URLSearchParams();
                        if (this.distancia?.trim()) params.append('distancia', this.distancia);
                        if (this.transporte?.trim()) params.append('transporte', this.transporte);
                        if (this.costo_envio?.toString().trim()) params.append('costo_envio', this.costo_envio);
                        params.append('cliente', '{{ optional($pedido->usuario)->nombre_usuario ?? '' }}');
                        
                        const lotesFiltrados = this.loteBaterias.map(l => l.trim()).filter(l => l !== '');
                        lotesFiltrados.forEach((l, i) => params.append('lotes[' + i + ']', l));
                        
                        const base = '{{ route("pedidos.pdf", $pedido->id_pedido) }}';
                        const url = params.toString() ? base + '?' + params.toString() : base;
                        
                        if (this.$refs.pdfFrame) this.$refs.pdfFrame.src = url;
                        this.pdfUrl = url;
                        
                        window.open(url, '_blank');
                        this.completoModal = true;
                        this.infoModal = false;
                        this.loteModal = false;
                        
                    } catch (e) {
                        console.error(e);
                        alert('Error de conexión: ' + e.message);
                    } finally {
                        this.guardando = false;
                    }
                },

                async onModeloChange() {
                    const modeloId = this.formBic.id_modelo;
                    this.formBic.id_voltaje = '';
                    this.formBic.id_color = '';
                    this.formBic.voltajes = [];
                    this.formBic.colores = [];

                    if (!modeloId) return;

                    try {
                        const [voltajes, colores] = await Promise.all([
                            fetch(`/voltaje-por-modelo/${modeloId}`).then(r => r.ok ? r.json() : Promise.reject('Error voltajes')),
                            fetch(`/colores-por-modelo/${modeloId}`).then(r => r.ok ? r.json() : Promise.reject('Error colores'))
                        ]);
                        this.formBic.voltajes = voltajes || [];
                        this.formBic.colores = colores || [];
                    } catch (e) {
                        console.error(e);
                    }
                },

                imprimirFormulario() {
                    window.open('{{ route("pedidos.pdf", $pedido->id_pedido) }}', '_blank');
                },

                async registrarBicicleta() {
                    if (this.guardando) return;
                    this.guardando = true;

                    const modelos = document.querySelectorAll('select[x-model="formBic.id_modelo"] option');
                    const modeloEl = [...modelos].find(o => o.value == this.formBic.id_modelo);
                    const voltajeObj = this.formBic.voltajes.find(v => v.id_voltaje == this.formBic.id_voltaje);
                    const colorObj = this.formBic.colores.find(c => c.id_color == this.formBic.id_color);
                    this.formBic.modeloNombre = modeloEl?.text ?? this.formBic.id_modelo;
                    this.formBic.voltajeNombre = voltajeObj?.voltaje ?? this.formBic.id_voltaje;
                    this.formBic.colorNombre = colorObj?.color ?? this.formBic.id_color;

                    try {
                        const resp = await fetch(storeUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                num_serie: this.numSerie,
                                id_modelo: this.formBic.id_modelo,
                                id_voltaje: this.formBic.id_voltaje,
                                id_color: this.formBic.id_color,
                                id_pedido: '{{ $pedido->id_pedido }}',
                            })
                        });

                        const data = await resp.json();

                        if (!resp.ok || !data.ok) {
                            this.inputClass = 'border-red-400 focus:ring-red-500 bg-red-50 dark:bg-red-900/10';
                            this.errorMensaje = data.mensaje ?? 'Error al registrar la bicicleta.';
                            this.errorModal = true;
                            this.guardando = false;
                            return;
                        }

                        this.inputClass = 'border-green-400 focus:ring-green-500 bg-green-50 dark:bg-green-900/10';

                        const idx = this.resumen.findIndex(i =>
                            i.id_modelo == this.formBic.id_modelo &&
                            i.id_voltaje == this.formBic.id_voltaje &&
                            i.id_color == this.formBic.id_color
                        );

                        if (idx !== -1) {
                            this.resumen[idx].escaneado++;
                            if (!this.resumen[idx].num_series) this.resumen[idx].num_series = [];
                            this.resumen[idx].num_series.push(this.numSerie);
                            this.resumen[idx]._flash = true;
                            setTimeout(() => { this.resumen[idx]._flash = false; }, 1200);
                            this.resumen = [...this.resumen];
                        }

                        this.justRegistered = true;
                        setTimeout(() => { this.justRegistered = false; }, 350);

                        setTimeout(() => {
                            this.numSerie = '';
                            this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-gray-900';
                            this.$nextTick(() => { if (this.$refs.qrInput) this.$refs.qrInput.focus(); });
                        }, 600);

                        setTimeout(() => {
                            if (this.$refs.pdfFrame) {
                                this.$refs.pdfFrame.src = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                            }
                            this.pdfUrl = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                        }, 500);

                    } catch (e) {
                        console.error(e);
                        this.errorMensaje = 'Error de conexión. Intenta de nuevo.';
                        this.errorModal = true;
                    } finally {
                        this.guardando = false;
                    }
                },

                async abrirCamara() {
                    this.camaraActiva = true;
                    await this.$nextTick();
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                        this.$refs.videoEl.srcObject = stream;
                        this.iniciarScan();
                    } catch (e) {
                        alert('No se pudo acceder a la cámara.');
                        this.camaraActiva = false;
                    }
                },

                cerrarCamara() {
                    clearInterval(this.scannerInterval);
                    if (this.$refs.videoEl?.srcObject) {
                        this.$refs.videoEl.srcObject.getTracks().forEach(t => t.stop());
                        this.$refs.videoEl.srcObject = null;
                    }
                    this.camaraActiva = false;
                },

                limpiarLotes() {
                    this.loteBaterias = Array({{ $totalRequerido }}).fill('');
                },

                aplicarLotes() {
                    const lotesFiltrados = this.loteBaterias.map(l => l.trim());
                    const params = new URLSearchParams();
                    
                    lotesFiltrados.forEach((l, i) => { if (l) params.append('lotes[' + i + ']', l); });
                    if (this.distancia?.trim()) params.append('distancia', this.distancia);
                    if (this.transporte?.trim()) params.append('transporte', this.transporte);
                    if (this.costo_envio?.toString().trim()) params.append('costo_envio', this.costo_envio);
                    
                    const base = '{{ route("pedidos.pdf", $pedido->id_pedido) }}';
                    const url = params.toString() ? base + '?' + params.toString() : base;
                    
                    if (this.$refs.pdfFrame) this.$refs.pdfFrame.src = url;
                    this.pdfUrl = url;
                    this.loteModal = false;
                },

                iniciarScan() {
                    const video = this.$refs.videoEl;
                    const canvas = this.$refs.canvasEl;
                    const ctx = canvas.getContext('2d');
                    this.scannerInterval = setInterval(() => {
                        if (video.readyState === video.HAVE_ENOUGH_DATA) {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            ctx.drawImage(video, 0, 0);
                            if ('BarcodeDetector' in window) {
                                new BarcodeDetector({ formats: ['qr_code', 'code_128', 'code_39'] })
                                    .detect(canvas)
                                    .then(codes => {
                                        if (codes.length > 0) {
                                            const val = codes[0].rawValue.toUpperCase().trim();
                                            if (val.length === 17) {
                                                this.numSerie = val;
                                                this.cerrarCamara();
                                                this.$nextTick(() => this.$refs.qrInput?.focus());
                                            }
                                        }
                                    }).catch(() => {});
                            }
                        }
                    }, 300);
                },
            }));
        });
    </script>
    @endpush
</x-app-layout>