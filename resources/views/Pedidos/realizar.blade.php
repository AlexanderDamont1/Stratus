<x-app-layout>


    @php
    $totalRequerido = collect($resumen)->sum('requerido');
    $totalEscaneado = collect($resumen)->sum('escaneado');
    @endphp


    <div
        x-data="realizarPedido({{ Js::from($resumen) }}, '{{ route('gestor.vehiculos.bicicletas.store') }}')"
        x-init="init()"
        class="space-y-6">

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('pedidos.index') }}"
                    class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Realizar Pedido
                        <span class="text-sm text-gray-400 ml-1">#{{ $pedido->id_pedido }}</span>
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ optional($pedido->negocio)->nombre_negocio }} —
                        {{ optional($pedido->usuario)->nombre_usuario }}
                    </p>
                </div>
            </div>

            {{-- Progreso global --}}
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-gray-400">Progreso</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                        <span x-text="totalEscaneado"></span>
                        <span class="text-gray-400 font-normal">/ {{ $totalRequerido }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 relative">
                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3" />
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#2563eb" stroke-width="3"
                            stroke-dasharray="100"
                            :stroke-dashoffset="100 - (totalEscaneado / {{ $totalRequerido }} * 100)"
                            style="transition: stroke-dashoffset 0.5s ease" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-900 dark:text-white"
                        x-text="Math.round(totalEscaneado / {{ $totalRequerido }} * 100) + '%'">
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== ESCÁNER QR ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-4">Escanear Bicicleta</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Input QR --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Número de Serie (QR)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <input
                            x-ref="qrInput"
                            type="text"
                            x-model="numSerie"
                            @keydown.enter.prevent="onQrIngresado()"
                            @input="numSerie = $event.target.value.toUpperCase()"
                            maxlength="17"
                            placeholder="Escanea o escribe el código..."
                            autocomplete="off"
                            :class="inputClass"
                            class="w-full pl-10 pr-24 py-3 rounded-xl border text-sm tracking-widest focus:outline-none focus:ring-2 transition dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                            <span class="text-xs text-gray-400" x-text="numSerie.length + '/17'"></span>
                            <button type="button" @click="limpiarQr()" x-show="numSerie.length > 0"
                                class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="abrirCamara()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Escanear con cámara
                    </button>
                </div>

                {{-- Selects modelo/voltaje/color --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                        <select x-model="formBic.id_modelo" @change="onModeloChange()"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar modelo</option>
                            @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                            <select x-model="formBic.id_voltaje"
                                :disabled="!formBic.voltajes.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                <option value="">— voltaje —</option>
                                <template x-for="v in formBic.voltajes" :key="v.id_voltaje">
                                    <option :value="v.id_voltaje" x-text="v.voltaje ?? 'ERROR'"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
                            <select x-model="formBic.id_color"
                                :disabled="!formBic.colores.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                                <option value="">— color —</option>
                                <template x-for="c in formBic.colores" :key="c.id_color">
                                    <option :value="c.id_color" x-text="c.color ?? 'ERROR'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <button type="button"
                        @click="registrarBicicleta()"
                        :disabled="!numSerie || numSerie.length !== 17 || !formBic.id_modelo || !formBic.id_voltaje || !formBic.id_color || guardando"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <template x-if="!guardando">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                        <template x-if="guardando">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </template>
                        <span x-text="guardando ? 'Registrando...' : 'Registrar Bicicleta'"></span>
                    </button>
                </div>
            </div>

            {{-- Vista cámara QR --}}
            <div x-show="camaraActiva" x-cloak class="mt-4">
                <div class="relative rounded-xl overflow-hidden bg-black" style="height: 250px;">
                    <video x-ref="videoEl" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-48 h-48 border-2 border-white/60 rounded-lg relative">
                            <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-blue-400 rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-blue-400 rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-blue-400 rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-blue-400 rounded-br-lg"></div>
                            <div class="absolute inset-x-0 top-1/2 h-0.5 bg-blue-400/70 animate-pulse"></div>
                        </div>
                    </div>
                    <button @click="cerrarCamara()"
                        class="absolute top-3 right-3 bg-black/50 text-white rounded-full p-1.5 hover:bg-black/70 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <canvas x-ref="canvasEl" class="hidden"></canvas>
            </div>
        </div>

        {{-- ===== RESUMEN DE ITEMS ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b dark:border-gray-700">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Estado del Pedido</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Modelo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Color</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Voltaje</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Requerido</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Escaneado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, key) in resumen" :key="key">
                            <tr :class="item.escaneado >= item.requerido ? 'bg-green-50 dark:bg-green-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30'">
                                <td class="px-5 py-3 font-medium text-gray-900 dark:text-white text-xs" x-text="item.modelo"></td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300 text-xs" x-text="item.color"></td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300 text-xs" x-text="item.voltaje"></td>
                                <td class="px-5 py-3 text-center font-semibold text-gray-900 dark:text-white text-xs" x-text="item.requerido"></td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold text-sm"
                                        :class="item.escaneado >= item.requerido ? 'text-green-600' : 'text-blue-600'"
                                        x-text="item.escaneado">
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <template x-if="item.escaneado >= item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-semibold rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completo
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado < item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pendiente (<span x-text="item.requerido - item.escaneado"></span>)
                                        </span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== PREVIEW FORMULARIO EMISIÓN ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Preview — Formulario de Emisión</p>
                <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}"
                    target="_blank"
                    class="flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Descargar PDF
                </a>
            </div>

            {{-- Desktop: iframe --}}
            <div class="hidden md:block">
                <iframe
                    x-ref="pdfFrame"
                    src="{{ route('pedidos.pdf', $pedido->id_pedido) }}"
                    class="w-full rounded-b-xl"
                    style="height: 600px; border: none;">
                </iframe>
            </div>

            {{-- Mobile: botón que abre modal con PDF --}}
            <div class="block md:hidden p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Vista previa no disponible en móvil.</p>
                <button @click="pdfModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver Formulario de Emisión
                </button>
            </div>

            {{-- ===== MODAL PDF MOBILE ===== --}}
            <div x-show="pdfModal" x-cloak
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/70 z-50 md:hidden"
                @click.self="pdfModal = false">

                <div
                    x-show="pdfModal"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
                    class="bg-white rounded-t-2xl w-full overflow-y-auto"
                    style="height: 80vh; max-height: 80vh; position: fixed; bottom: 0; left: 0; right: 0;"
                    @click.stop>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b sticky top-0 bg-white z-10">
                        <p class="text-sm font-semibold text-gray-900">Formulario de Emisión</p>
                        <div class="flex items-center gap-3">
                            <button @click="imprimirFormulario()"
                                class="text-xs text-blue-600 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimir
                            </button>
                            <button @click="pdfModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Contenido idéntico al PDF --}}
                    <div class="p-3 overflow-x-auto">
                        <div style="min-width: 520px; font-family: Arial, sans-serif; font-size: 10px; font-weight: bold;">

                            {{-- Encabezado --}}
                            <table style="width:100%; border-collapse:collapse;">
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:8px; border:1px solid #000; font-size:14px; font-weight:bold; font-style:italic;">
                                        Formulario de Emisión de Fábrica
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Fecha:</strong><br>{{ now()->format('d/m/Y') }}
                                    </td>
                                    <td style="width:18%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Código:</strong><br>{{ $pedido->id_pedido }}
                                    </td>
                                    <td style="width:21%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Usuario:</strong><br>{{ optional($pedido->usuario)->nombre_usuario ?? '' }}
                                    </td>
                                    <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Negocio:</strong><br>{{ optional($pedido->negocio)->nombre_negocio ?? 'N/D' }}
                                    </td>
                                    <td style="width:25%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Transporte:</strong><br>Evobike
                                    </td>
                                    <td style="width:16%;text-align:center;border:1px solid #000;padding:4px;">
                                        <strong>Notas:</strong><br>{{ $pedido->notas ?? '-' }}
                                    </td>
                                </tr>
                            </table>

                            {{-- Detalle de ítems con rowspan idéntico al PDF --}}
                            <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                                <thead>
                                    <tr>
                                        <th style="border:1px solid #000;padding:4px;width:6%;text-align:center;">#</th>
                                        <th style="border:1px solid #000;padding:4px;width:22%;text-align:center;">Modelo</th>
                                        <th style="border:1px solid #000;padding:4px;width:18%;text-align:center;">Color</th>
                                        <th style="border:1px solid #000;padding:4px;width:8%;text-align:center;">Cant.</th>
                                        <th style="border:1px solid #000;padding:4px;width:18%;text-align:center;">Voltaje</th>
                                        <th style="border:1px solid #000;padding:4px;width:28%;text-align:center;">No. Frame</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                    $rowNumber = 1;
                                    @endphp

                                    @foreach ($modelGroups as $modelName => $modelGroup)
                                    @php
                                    $modelRowspan = 0;
                                    foreach ($modelGroup['voltajes'] as $vg) {
                                    foreach ($vg['colores'] as $colorName => $items) {
                                    $modelRowspan += max(1, $items[0]->cantidad);
                                    }
                                    }
                                    $printedModel = false;
                                    @endphp

                                    @foreach ($modelGroup['voltajes'] as $voltajeName => $voltGroup)
                                    @php
                                    $voltajeRowspan = 0;
                                    foreach ($voltGroup['colores'] as $colorName => $items) {
                                    $voltajeRowspan += max(1, $items[0]->cantidad);
                                    }
                                    $printedVoltaje = false;
                                    @endphp

                                    @foreach ($voltGroup['colores'] as $colorName => $items)
                                    @php
                                    $cantidad = $items[0]->cantidad;
                                    $numSeries = $bicGroups[$modelName][$voltajeName][$colorName] ?? [];
                                    @endphp

                                    @for ($i = 0; $i < max(1, $cantidad); $i++)
                                        <tr>
                                        @if ($i === 0)
                                        <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1, $cantidad) }}">{{ $rowNumber }}</td>
                                        @endif
                                        @if (!$printedModel && $i === 0)
                                        <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $modelRowspan }}">{{ $modelName }}</td>
                                        @php $printedModel = true; @endphp
                                        @endif
                                        @if ($i === 0)
                                        <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1, $cantidad) }}">{{ $colorName }}</td>
                                        <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1, $cantidad) }}">{{ $cantidad }}</td>
                                        @endif
                                        @if (!$printedVoltaje && $i === 0)
                                        <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $voltajeRowspan }}">{{ $voltajeName }}</td>
                                        @php $printedVoltaje = true; @endphp
                                        @endif
                                        <td style="border:1px solid #000;padding:4px;text-align:center;{{ isset($numSeries[$i])  }}">
                                            {{ $numSeries[$i] ?? '' }}
                                        </td>
                                        </tr>
                                        @endfor

                                        @php $rowNumber++; @endphp
                                        @endforeach
                                        @endforeach
                                        @endforeach
                                </tbody>
                            </table>

                            {{-- Firmas --}}
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
                                    <td style="padding:2px; font-size:9px; height:18px; line-height:1; border:1px solid #000;">
                                        Firma del inspector de calidad:
                                    </td>
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
                                    <td style="font-weight:bold; font-style:italic; text-align:center; border:1px solid #000; padding:4px;">
                                        Recibo de Emisión
                                    </td>
                                </tr>
                            </table>

                            <table style="width:100%; border-collapse:collapse; border-left:1px solid #000; border-right:1px solid #000;">
                                <tr style="border-bottom:1px solid #000;">
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

                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px;">Observación:</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; height: 30px;">
                                        Para cualquier aclaración o informe de daños comuníquese con el negocio: {{ optional($pedido->negocio)->nombre_negocio ?? 'N/D' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; height: 25px; color: red;">
                                        El pedido deberá ser supervisado por el cliente, una vez firmado este documento la empresa no se hace responsable
                                    </td>
                                </tr>
                                </table>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== MODAL ERROR ===== --}}
            <div x-show="errorModal" x-cloak
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4"
                @click.self="errorModal = false">
                <div x-show="errorModal"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm" @click.stop>
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Bicicleta no válida</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400" x-text="errorMensaje"></p>
                        </div>
                        <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left">
                            <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Datos escaneados</p>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                    <span class="text-gray-400">Serie:</span>
                                    <span class="font-mono font-semibold" x-text="numSerie"></span>
                                </p>
                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                    <span class="text-gray-400">Modelo:</span>
                                    <span x-text="formBic.modeloNombre || '—'"></span>
                                </p>
                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                    <span class="text-gray-400">Color:</span>
                                    <span x-text="formBic.colorNombre || '—'"></span>
                                </p>
                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                    <span class="text-gray-400">Voltaje:</span>
                                    <span x-text="formBic.voltajeNombre || '—'"></span>
                                </p>
                            </div>
                        </div>
                        <button @click="errorModal = false; limpiarQr()"
                            class="w-full py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                            Intentar de nuevo
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===== MODAL PEDIDO COMPLETO ===== --}}
            <div x-show="completoModal" x-cloak
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4">
                <div x-show="completoModal"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 w-full max-w-sm text-center">
                    <div class="w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">¡Pedido Completo!</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Todas las bicicletas han sido registradas. El pedido ha sido marcado como <strong>Entregado</strong>.
                    </p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}" target="_blank"
                            class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Descargar PDF de Emisión
                        </a>
                        <a href="{{ route('pedidos.index') }}"
                            class="w-full py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                            Volver a Pedidos
                        </a>
                    </div>
                </div>
            </div>

        </div>{{-- fin x-data --}}


        @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('realizarPedido', (resumenInicial, storeUrl) => ({

                    resumen: Object.values(resumenInicial).map(item => ({
                        ...item,
                        num_series: []
                    })),

                    numSerie: '',
                    guardando: false,
                    camaraActiva: false,
                    scannerInterval: null,

                    formBic: {
                        id_modelo: '',
                        id_voltaje: '',
                        id_color: '',
                        voltaje: [],
                        color: [],
                        modeloNombre: '',
                        voltajeNombre: '',
                        colorNombre: '',
                    },

                    errorModal: false,
                    errorMensaje: '',
                    completoModal: false,
                    pdfModal: false,
                    pdfTs: Date.now(),
                    inputClass: 'border-gray-300 dark:border-gray-600 focus:ring-blue-500',

                    get totalEscaneado() {
                        return this.resumen.reduce((sum, i) => sum + i.escaneado, 0);
                    },

                    init() {
                        this.$nextTick(() => {
                            if (this.$refs.qrInput) this.$refs.qrInput.focus();
                        });
                    },

                    limpiarQr() {
                        this.numSerie = '';
                        this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-blue-500';
                        this.$nextTick(() => {
                            if (this.$refs.qrInput) this.$refs.qrInput.focus();
                        });
                    },

                    onQrIngresado() {
                        if (this.numSerie.length === 17 && this.formBic.id_modelo && this.formBic.id_voltaje && this.formBic.id_color) {
                            this.registrarBicicleta();
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
                                fetch(`/voltaje-por-modelo/${modeloId}`).then(r => r.json()),
                                fetch(`/colores-por-modelo/${modeloId}`).then(r => r.json())
                            ]);
                            this.formBic.voltajes = voltajes;
                            this.formBic.colores = colores;
                        } catch (e) {
                            console.error('Error cargando catálogos', e);
                        }
                    },

                    imprimirFormulario() {
                        window.open('{{ route("pedidos.pdf", $pedido->id_pedido) }}', '_blank');
                    },

                    async registrarBicicleta() {
                        if (this.guardando) return;
                        this.guardando = true;

                        // ✅ Nombres para el modal de error — busca en ambos campos posibles
                        const modelos = document.querySelectorAll('select[x-model="formBic.id_modelo"] option');
                        const modeloEl = [...modelos].find(o => o.value == this.formBic.id_modelo);
                        const voltajeObj = this.formBic.voltajes.find(v => v.id_voltaje == this.formBic.id_voltaje);
                        const colorObj = this.formBic.colores.find(c => c.id_color == this.formBic.id_color);

                        this.formBic.modeloNombre = modeloEl?.text ?? this.formBic.id_modelo;
                        this.formBic.voltajeNombre = voltajeObj?.voltaje ?? voltajeObj?.voltaje ?? this.formBic.id_voltaje;
                        this.formBic.colorNombre = colorObj?.color ?? colorObj?.color ?? this.formBic.id_color;

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

                            // ✅ Éxito
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
                                this.resumen = [...this.resumen];
                            }


                            setTimeout(() => {
                                if (this.$refs.pdfFrame) {
                                    this.$refs.pdfFrame.src = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                                }
                            }, 500);

                            if (data.pedido_completo) {
                                setTimeout(() => {
                                    this.completoModal = true;
                                }, 900);
                            }

                        } catch (e) {
                            console.error('Error al registrar', e);
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
                            const stream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'environment'
                                }
                            });
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
                                    const detector = new BarcodeDetector({
                                        formats: ['qr_code', 'code_128', 'code_39']
                                    });
                                    detector.detect(canvas).then(codes => {
                                        if (codes.length > 0) {
                                            const valor = codes[0].rawValue.toUpperCase().trim();
                                            if (valor.length === 17) {
                                                this.numSerie = valor;
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

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @endpush


</x-app-layout>