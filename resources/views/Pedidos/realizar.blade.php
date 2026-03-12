<x-app-layout>

    @php
    $totalRequerido = collect($resumen)->sum('requerido');
    $totalEscaneado = collect($resumen)->sum('escaneado');
    @endphp

    <style>
        .realizar-root * {
            font-family: 'IBM Plex Sans', sans-serif;
        }


        /* ── Stagger entrada ── */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .s1 {
            animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        .s2 {
            animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .07s both;
        }

        .s3 {
            animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .14s both;
        }

        .s4 {
            animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .21s both;
        }

        /* ── Fila flash al registrar ── */
        @keyframes rowFlash {
            0% {
                background: rgba(22, 163, 74, .2);
            }

            100% {
                background: transparent;
            }
        }

        .row-flash {
            animation: rowFlash 1.1s ease both;
        }

        /* ── Bump en contador ── */
        @keyframes countBump {
            0% {
                transform: scale(1);
            }

            45% {
                transform: scale(1.4);
                color: #16a34a;
            }

            100% {
                transform: scale(1);
            }
        }

        .count-bump {
            animation: countBump .4s cubic-bezier(.22, .68, 0, 1.5) both;
        }

        /* ── Progreso barra ── */
        .prog-track {
            height: 3px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 14px;
        }

        .dark .prog-track {
            background: #1e2330;
        }

        .prog-fill {
            height: 100%;
            border-radius: 2px;
            transition: width .6s cubic-bezier(.4, 0, .2, 1), background .3s;
        }

        /* ── Badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .dark .badge-blue {
            background: #0d1f3c;
            color: #60a5fa;
        }

        /* ── Dot pulso en líneas completas ── */
        @keyframes pulseDot {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(22, 163, 74, .5);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(22, 163, 74, 0);
            }
        }

        .dot-pulse {
            animation: pulseDot 1.6s ease infinite;
        }

        /* ── Scanner beam ── */
        @keyframes scanBeam {
            0% {
                top: 10%;
            }

            50% {
                top: 84%;
            }

            100% {
                top: 10%;
            }
        }

        .scan-beam {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #2563eb 30%, #2563eb 70%, transparent);
            animation: scanBeam 1.8s ease-in-out infinite;
        }

        .sc {
            position: absolute;
            width: 18px;
            height: 18px;
            border-color: #2563eb;
            border-style: solid;
        }

        .sc-tl {
            top: 0;
            left: 0;
            border-width: 2px 0 0 2px;
            border-radius: 3px 0 0 0;
        }

        .sc-tr {
            top: 0;
            right: 0;
            border-width: 2px 2px 0 0;
            border-radius: 0 3px 0 0;
        }

        .sc-bl {
            bottom: 0;
            left: 0;
            border-width: 0 0 2px 2px;
            border-radius: 0 0 0 3px;
        }

        .sc-br {
            bottom: 0;
            right: 0;
            border-width: 0 2px 2px 0;
            border-radius: 0 0 3px 0;
        }

        /* ── Modal backdrop ── */
        .modal-bg {
            background: rgba(0, 0, 0, .55);
            backdrop-filter: blur(3px);
        }

        /* ── Botón escáner flotante ── */
        @keyframes scannerPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, .4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
            }
        }

        .btn-scanner-pulse {
            animation: scannerPulse 2.5s ease infinite;
        }

        /* ===== MEJORAS MOBILE ===== */
        @media (max-width: 640px) {
            .mobile-stack {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.75rem !important;
            }
            
            .mobile-full-width {
                width: 100%;
            }
            
            .mobile-text-sm {
                font-size: 0.8125rem;
            }
            
            .mobile-px-3 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .mobile-py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            
            /* Botón flotante para escanear en móvil */
            .mobile-fab {
                position: fixed;
                bottom: 1.5rem;
                right: 1.5rem;
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 9999px;
                background: #2563eb;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
                z-index: 40;
                transition: all 0.2s;
                border: 2px solid white;
            }
            
            .dark .mobile-fab {
                border-color: #1f2937;
            }
            
            .mobile-fab:active {
                transform: scale(0.95);
            }
            
            /* Header compacto para móvil */
            .mobile-header-compact {
                padding: 0.75rem;
            }
            
            /* Tabla deslizable para móvil */
            .mobile-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0;
                padding: 0;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f1f5f9;
            }
            
            .mobile-table-container::-webkit-scrollbar {
                height: 4px;
            }
            
            .mobile-table-container::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }
            
            .mobile-table-container::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 4px;
            }
            
            .mobile-table-container::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            .mobile-table {
                min-width: 800px;
                width: 100%;
                border-collapse: collapse;
                font-size: 0.75rem;
            }
            
            .mobile-table th {
                background: #f9fafb;
                color: #6b7280;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.75rem 0.5rem;
                white-space: nowrap;
                border-bottom: 1px solid #e5e7eb;
            }
            
            .dark .mobile-table th {
                background: #374151;
                color: #9ca3af;
                border-bottom-color: #4b5563;
            }
            
            .mobile-table td {
                padding: 0.75rem 0.5rem;
                white-space: nowrap;
                border-bottom: 1px solid #f3f4f6;
            }
            
            .dark .mobile-table td {
                border-bottom-color: #374151;
            }
            
            .mobile-table tr:last-child td {
                border-bottom: none;
            }
            
            .mobile-table .series-cell {
                max-width: 200px;
                white-space: normal;
            }
            
            .mobile-series-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                background: #f3f4f6;
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                margin: 0.125rem;
                font-size: 0.7rem;
            }
            
            .dark .mobile-series-badge {
                background: #2d3748;
            }
            
            /* Ocultar vista de escritorio en móvil */
            .desktop-table {
                display: none;
            }
            
            .mobile-table-container {
                display: block;
            }

            /* PDF Viewer en móvil - conserva el estilo exacto del PDF */
            .mobile-pdf-viewer {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: white;
                z-index: 100;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .mobile-pdf-viewer iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
            
            .mobile-pdf-viewer .pdf-controls {
                position: sticky;
                top: 0;
                background: white;
                padding: 0.75rem;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 10;
            }
        }
        
        @media (min-width: 641px) {
            .desktop-table {
                display: block;
            }
            
            .mobile-table-container {
                display: none;
            }
            
            .mobile-fab {
                display: none;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div
        x-data="realizarPedido({{ Js::from($resumen) }}, '{{ route('gestor.vehiculos.bicicletas.store') }}')"
        x-init="init()"
        class="realizar-root space-y-6 px-4 sm:px-0">

        {{-- ===== BREADCRUMB + HEADER ===== --}}
        <div class="s1 mobile-header-compact">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1.5 mb-3 text-xs text-gray-400 dark:text-gray-500 overflow-x-auto pb-1 whitespace-nowrap">
                <a href="{{ route('pedidos.index') }}" class="hover:text-blue-500 transition-colors">Pedidos</a>
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="mono text-gray-400 flex-shrink-0">#{{ $pedido->id_pedido }}</span>
                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-semibold text-gray-700 dark:text-gray-300 flex-shrink-0">Realizar Pedido</span>
            </div>

            {{-- Título + progreso --}}
            <div class="flex items-start justify-between gap-4 flex-wrap mobile-stack">
                <div class="mobile-full-width">
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Realizar Pedido</h1>
                        <span class="badge badge-blue mono">#{{ $pedido->id_pedido }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-[200px] sm:max-w-none">
                        {{ optional($pedido->negocio)->nombre_negocio ?? '—' }}
                        <span class="text-gray-300 dark:text-gray-600 mx-1.5">·</span>
                        {{ optional($pedido->usuario)->nombre_usuario ?? '—' }}
                    </p>
                </div>

                {{-- Círculo progreso + botón escáner --}}
                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-0.5">Progreso</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white mono">
                            <span x-text="totalEscaneado" :class="justRegistered ? 'count-bump' : ''"></span>
                            <span class="text-gray-400 font-normal"> / {{ $totalRequerido }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 relative">
                        <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3" />
                            <circle cx="18" cy="18" r="15.9" fill="none"
                                :stroke="totalEscaneado >= {{ $totalRequerido }} ? '#16a34a' : '#2563eb'"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-dasharray="100"
                                :stroke-dashoffset="100 - (Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 100)"
                                style="transition: stroke-dashoffset 0.55s cubic-bezier(.4,0,.2,1), stroke .3s" />
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-gray-900 dark:text-white mono"
                            x-text="Math.round(Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 100) + '%'">
                        </span>
                    </div>

                    {{-- Botón escáner desktop --}}
                    <button
                        type="button"
                        @click="scanModal = true; $nextTick(() => $refs.qrInput?.focus())"
                        class="btn-scanner-pulse hidden sm:flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Escanear
                    </button>
                </div>
            </div>

            {{-- Barra progreso --}}
            <div class="prog-track">
                <div class="prog-fill"
                    :style="`width:${Math.min(100, Math.round(totalEscaneado / {{ $totalRequerido }} * 100))}%; background:${totalEscaneado >= {{ $totalRequerido }} ? '#16a34a' : '#2563eb'}`">
                </div>
            </div>
        </div>

        {{-- ===== ESTADO DEL PEDIDO ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 overflow-hidden s2">
            <div class="px-4 sm:px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Estado del Pedido</p>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="flex items-center gap-1.5 text-xs mono text-gray-400">
                        <span x-text="resumen.filter(i => i.escaneado >= i.requerido).length" class="font-bold text-gray-900 dark:text-white"></span>
                        <span>/</span>
                        <span>{{ count($resumen) }}</span>
                    </div>
                    <button
                        type="button"
                        @click="loteModal = true"
                        class="flex items-center gap-1.5 px-2 sm:px-3 py-1.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="hidden sm:inline">Lote de Batería</span>
                    </button>
                </div>
            </div>
            
            {{-- VISTA DESKTOP (TABLA) --}}
            <div class="desktop-table overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Color</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Voltaje</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Req.</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Esc.</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Series</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, idx) in resumen" :key="idx">
                            <tr :class="[
                                item.escaneado >= item.requerido ? 'bg-green-50 dark:bg-green-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30',
                                item._flash ? 'row-flash' : ''
                            ]" style="transition: background .15s;">
                                <td class="px-5 py-3 text-xs font-semibold text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full shrink-0 transition-all duration-500"
                                            :class="item.escaneado >= item.requerido ? 'bg-green-500 dot-pulse' : 'bg-blue-400'">
                                        </div>
                                        <span x-text="item.modelo"></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-400" x-text="item.color"></td>
                                <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-400 mono" x-text="item.voltaje"></td>
                                <td class="px-5 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 mono" x-text="item.requerido"></td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-bold "
                                        :class="item.escaneado >= item.requerido ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400'"
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
                                    <template x-if="item.escaneado > 0 && item.escaneado < item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="item.requerido - item.escaneado"></span> restante<span x-text="(item.requerido - item.escaneado) !== 1 ? 's' : ''"></span>
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado === 0">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-semibold rounded-full">
                                            Pendiente (<span x-text="item.requerido"></span>)
                                        </span>
                                    </template>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        <template x-for="serie in item.num_series" :key="serie">
                                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded px-2 py-1">
                                                <span class="text-xs mono text-gray-700 dark:text-gray-300" x-text="serie"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="abrirModalEliminar(serie)"
                                                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <span x-show="!item.num_series || item.num_series.length === 0"
                                            class="text-xs text-gray-400">—</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            {{-- VISTA MÓVIL (TABLA DESLIZABLE) --}}
            <div class="mobile-table-container">
                <table class="mobile-table">
                    <thead>
                        <tr>
                            <th>Modelo</th>
                            <th>Color</th>
                            <th>Voltaje</th>
                            <th>Req.</th>
                            <th>Esc.</th>
                            <th>Estado</th>
                            <th>Series</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in resumen" :key="idx">
                            <tr :class="item._flash ? 'row-flash' : ''">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full"
                                            :class="item.escaneado >= item.requerido ? 'bg-green-500 dot-pulse' : 'bg-blue-400'">
                                        </div>
                                        <span class="font-semibold" x-text="item.modelo"></span>
                                    </div>
                                </td>
                                <td x-text="item.color"></td>
                                <td class="mono" x-text="item.voltaje"></td>
                                <td class="text-center font-semibold" x-text="item.requerido"></td>
                                <td class="text-center">
                                    <span class="font-bold" :class="item.escaneado >= item.requerido ? 'text-green-600' : 'text-blue-600'" x-text="item.escaneado"></span>
                                </td>
                                <td>
                                    <template x-if="item.escaneado >= item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-semibold rounded-full whitespace-nowrap">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completo
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado > 0 && item.escaneado < item.requerido">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-semibold rounded-full whitespace-nowrap">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="item.requerido - item.escaneado"></span> restante
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado === 0">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-semibold rounded-full whitespace-nowrap">
                                            Pendiente
                                        </span>
                                    </template>
                                </td>
                                <td class="series-cell">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="serie in item.num_series" :key="serie">
                                            <span class="mobile-series-badge">
                                                <span class="mono" x-text="serie"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="abrirModalEliminar(serie)"
                                                    class="text-red-500 hover:text-red-700 dark:hover:text-red-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="!item.num_series || item.num_series.length === 0"
                                            class="text-xs text-gray-400">—</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== FORMULARIO EMISIÓN ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700 s3">
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Formulario de Emisión</p>
                <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}" target="_blank"
                    class="flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Descargar PDF
                </a>
            </div>
            <div class="hidden md:block">
                <iframe x-ref="pdfFrame" src="{{ route('pedidos.pdf', $pedido->id_pedido) }}"
                    class="w-full rounded-b-xl" style="height:600px; border:none;"></iframe>
            </div>
            <div class="block md:hidden p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Vista previa no disponible en móvil.</p>
                <button @click="pdfModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition">
                    Ver Formulario de Emisión
                </button>
            </div>
        </div>


        {{-- ===== BOTÓN FLOTANTE MÓVIL ===== --}}
        <button
            type="button"
            @click="scanModal = true; $nextTick(() => $refs.qrInput?.focus())"
            class="mobile-fab">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </button>


        {{-- ===== MODAL PDF MOBILE ===== --}}
        <div x-show="pdfModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 z-50 md:hidden"
            @click.self="pdfModal = false">
            <div x-show="pdfModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
                class="bg-white rounded-t-2xl w-full overflow-y-auto"
                style="height:80vh; position:fixed; bottom:0; left:0; right:0;"
                @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b sticky top-0 bg-white z-10">
                    <p class="text-sm font-semibold text-gray-900">Formulario de Emisión</p>
                    <div class="flex items-center gap-3">
                        <button @click="imprimirFormulario()" class="text-xs text-blue-600 font-semibold flex items-center gap-1">
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
                <div class="p-3 overflow-x-auto">
                    <div style="min-width:520px; font-family:Arial,sans-serif; font-size:10px; font-weight:bold;">
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td colspan="6" style="text-align:center; padding:8px; border:1px solid #000; font-size:14px; font-weight:bold; font-style:italic;">Formulario de Emisión de Fábrica</td>
                            </tr>
                            <tr>
                                <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;"><strong>Fecha:</strong><br>{{ now()->format('d/m/Y') }}</td>
                                <td style="width:18%;text-align:center;border:1px solid #000;padding:4px;"><strong>Código:</strong><br>{{ $pedido->id_pedido }}</td>
                                <td style="width:21%;text-align:center;border:1px solid #000;padding:4px;"><strong>Usuario:</strong><br>{{ optional($pedido->usuario)->nombre_usuario ?? '' }}</td>
                                <td style="width:10%;text-align:center;border:1px solid #000;padding:4px;"><strong>Negocio:</strong><br>{{ optional($pedido->negocio)->nombre_negocio ?? 'N/D' }}</td>
                                <td style="width:25%;text-align:center;border:1px solid #000;padding:4px;"><strong>Transporte:</strong><br>Evobike</td>
                                <td style="width:16%;text-align:center;border:1px solid #000;padding:4px;"><strong>Notas:</strong><br>{{ $pedido->notas ?? '-' }}</td>
                            </tr>
                        </table>
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
                                @php $modelRowspan = 0;
                                foreach ($modelGroup['voltajes'] as $vg) { foreach ($vg['colores'] as $cn => $its) { $modelRowspan += max(1, $its[0]->cantidad); } }
                                $printedModel = false; @endphp
                                @foreach ($modelGroup['voltajes'] as $voltajeName => $voltGroup)
                                @php $voltajeRowspan = 0;
                                foreach ($voltGroup['colores'] as $cn => $its) { $voltajeRowspan += max(1, $its[0]->cantidad); }
                                $printedVoltaje = false; @endphp
                                @foreach ($voltGroup['colores'] as $colorName => $items)
                                @php $cantidad = $items[0]->cantidad; $numSeries = $bicGroups[$modelName][$voltajeName][$colorName] ?? []; @endphp
                                @for ($i = 0; $i < max(1, $cantidad); $i++)
                                    <tr>
                                    @if ($i === 0)<td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1,$cantidad) }}">{{ $rowNumber }}</td>@endif
                                    @if (!$printedModel && $i === 0)<td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $modelRowspan }}">{{ $modelName }}</td>@php $printedModel=true; @endphp@endif
                                    @if ($i === 0)
                                    <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1,$cantidad) }}">{{ $colorName }}</td>
                                    <td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ max(1,$cantidad) }}">{{ $cantidad }}</td>
                                    @endif
                                    @if (!$printedVoltaje && $i === 0)<td style="border:1px solid #000;padding:4px;text-align:center;" rowspan="{{ $voltajeRowspan }}">{{ $voltajeName }}</td>@php $printedVoltaje=true; @endphp@endif
                                    <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $numSeries[$i] ?? '' }}</td>
                                    </tr>
                                    @endfor
                                    @php $rowNumber++; @endphp
                                    @endforeach
                                    @endforeach
                                    @endforeach
                            </tbody>
                        </table>
                        <table style="width:100%; border-collapse:collapse; margin-top:-1px;">
                            <tr>
                                <td style="width:59%; height:70px; font-size:9px; padding:4px; font-style:italic; text-align:center; font-weight:bold; border:1px solid #000;">Este pedido es por duplicado, uno se enviará al destino con la mercancía, otro se guardará en fábrica y el archivo electrónico se enviará al departamento comercial.</td>
                                <td rowspan="2" style="width:41%; vertical-align:top; font-size:9px; padding:4px; font-style:italic; text-align:center; font-weight:bold; border:1px solid #000;">Sello o firma del responsable de fábrica:</td>
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
                                <td style="font-style:italic; text-align:center; height:50px; vertical-align:top; border:1px solid #000; padding:4px;">Firma del responsable de la tienda (el recibo se recibirá tras confirmar el pedido):</td>
                            </tr>
                        </table>
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="border:1px solid #000; padding:5px;">Observación:</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; padding:5px; height:30px;">Para cualquier aclaración comuníquese con: {{ optional($pedido->negocio)->nombre_negocio ?? 'N/D' }}</td>
                            </tr>
                            <tr>
                                <td style="border:1px solid #000; padding:5px; height:25px; color:red;">El pedido deberá ser supervisado por el cliente, una vez firmado este documento la empresa no se hace responsable.</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        {{-- ================================================================ --}}
        {{-- MODAL: ESCANEAR BICICLETA                                        --}}
        {{-- ================================================================ --}}
        <div x-show="scanModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 modal-bg"
            @click.self="scanModal = false">

            <div x-show="scanModal"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden"
                @click.stop>

                {{-- Header modal --}}
                <div class="flex items-center justify-between px-4 sm:px-5 py-4 border-b dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Registrar Bicicleta</h3>
                            <p class="text-xs text-gray-400">Ingresa o escanea el número de serie</p>
                        </div>
                    </div>
                    <button @click="scanModal = false; cerrarCamara()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-5 space-y-4">

                    {{-- Input QR --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Número de Serie (QR)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full pl-10 pr-20 py-3 rounded-xl border text-sm tracking-widest focus:outline-none focus:ring-2 transition dark:bg-gray-700 dark:text-white mono">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                <span class="text-xs text-gray-400 mono" x-text="numSerie.length + '/17'"></span>
                                <button type="button" @click="limpiarQr()" x-show="numSerie.length > 0"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Selects --}}
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                            <select x-model="formBic.id_modelo" @change="onModeloChange()"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">— Seleccionar modelo —</option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                                <select x-model="formBic.id_voltaje" :disabled="!formBic.voltajes.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 transition">
                                    <option value="">— voltaje —</option>
                                    <template x-for="v in formBic.voltajes" :key="v.id_voltaje">
                                        <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
                                <select x-model="formBic.id_color" :disabled="!formBic.colores.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 transition">
                                    <option value="">— color —</option>
                                    <template x-for="c in formBic.colores" :key="c.id_color">
                                        <option :value="c.id_color" x-text="c.color"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Vista cámara --}}
                    <div x-show="camaraActiva" x-cloak>
                        <div class="relative rounded-xl overflow-hidden bg-black" style="height:200px;">
                            <video x-ref="videoEl" autoplay playsinline class="w-full h-full object-cover" style="opacity:.92;"></video>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="relative" style="width:140px; height:140px;">
                                    <div class="sc sc-tl"></div>
                                    <div class="sc sc-tr"></div>
                                    <div class="sc sc-bl"></div>
                                    <div class="sc sc-br"></div>
                                    <div class="scan-beam"></div>
                                </div>
                            </div>
                            <button @click="cerrarCamara()"
                                class="absolute top-2.5 right-2.5 bg-black/50 hover:bg-black/70 text-white rounded-lg p-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <canvas x-ref="canvasEl" class="hidden"></canvas>
                    </div>

                    {{-- Footer del modal --}}
                    <div class="flex gap-2 pt-1">
                        <button type="button" @click="abrirCamara()"
                            class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="sm:hidden">Cámara</span>
                        </button>
                        <button type="button"
                            @click="registrarBicicleta()"
                            :disabled="!numSerie || numSerie.length !== 17 || !formBic.id_modelo || !formBic.id_voltaje || !formBic.id_color || guardando"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
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
                            <span x-text="guardando ? 'Registrando...' : 'Registrar'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- ===== MODAL ERROR ===== --}}
        <div x-show="errorModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4"
            @click.self="errorModal = false">
            <div x-show="errorModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Bicicleta no válida</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="errorMensaje"></p>
                    </div>
                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left">
                        <p class="text-xs text-gray-400 mb-2 font-semibold uppercase tracking-wider">Datos escaneados</p>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between"><span class="text-gray-400">Serie</span><span class="mono font-semibold text-gray-800 dark:text-gray-200" x-text="numSerie"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Modelo</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.modeloNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Color</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.colorNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Voltaje</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.voltajeNombre || '—'"></span></div>
                        </div>
                    </div>
                    <button @click="errorModal = false; limpiarQr()"
                        class="w-full py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-xl text-sm font-semibold hover:opacity-90 transition">
                        Intentar de nuevo
                    </button>
                </div>
            </div>
        </div>


        {{-- ================================================================ --}}
        {{-- MODAL: CONFIRMAR ELIMINACIÓN                                     --}}
        {{-- ================================================================ --}}
        <div x-show="eliminarModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4"
            @click.self="eliminarModal = false">
            <div x-show="eliminarModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">¿Eliminar bicicleta?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            Vas a eliminar la bicicleta con número de serie:
                        </p>
                        <p class="text-sm font-bold mono text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 py-2 px-3 rounded-lg break-all" x-text="numSerieEliminar"></p>
                        <p class="text-xs text-gray-400 mt-3">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="flex gap-3 w-full">
                        <button @click="eliminarModal = false"
                            class="flex-1 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button @click="confirmarEliminar()"
                            :disabled="eliminando"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!eliminando">Eliminar</span>
                            <span x-show="eliminando" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="hidden sm:inline">Eliminando...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- ================================================================ --}}
        {{-- MODAL: LOTE DE BATERÍA                                           --}}
        {{-- ================================================================ --}}
        <div x-show="loteModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-0 sm:px-4 modal-bg"
            @click.self="loteModal = false">

            <div x-show="loteModal"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden"
                @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 sm:px-5 py-4 border-b dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Lote de Batería</h3>
                            <p class="text-xs text-gray-400">Máximo {{ $totalRequerido }} entradas</p>
                        </div>
                    </div>
                    <button @click="loteModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-4 sm:p-5 space-y-3 overflow-y-auto" style="max-height: 60vh;">
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Ingresa los códigos de lote en orden. Puedes dejar vacíos los que no apliquen.
                    </p>

                    <template x-for="(lote, idx) in loteBaterias" :key="idx">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-gray-400 mono w-6 text-right shrink-0" x-text="idx + 1"></span>
                            <input
                                type="text"
                                x-model="loteBaterias[idx]"
                                @keydown.enter.prevent="idx < loteBaterias.length - 1 && $refs['loteInput' + (idx + 1)]?.[0]?.focus()"
                                :x-ref="'loteInput' + idx"
                                maxlength="30"
                                placeholder="Código de lote..."
                                autocomplete="off"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm mono focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-4 sm:px-5 py-4 border-t dark:border-gray-700 flex gap-2">
                    <button type="button" @click="limpiarLotes()"
                        class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1">
                        Limpiar
                    </button>
                    <button type="button" @click="aplicarLotes()"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Aplicar
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL PEDIDO COMPLETO ===== --}}
        <div x-show="completoModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4">
            <div x-show="completoModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 sm:p-8 w-full max-w-sm text-center mx-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2">¡Pedido Completo!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Todas las bicicletas han sido registradas.
                </p>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}" target="_blank"
                        class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar PDF
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
            errorModal: false,
            completoModal: false,
            pdfModal: false,
            errorMensaje: '',
            inputClass: 'border-gray-300 dark:border-gray-600 focus:ring-blue-500',
            eliminando: false,
            eliminarModal: false,
            numSerieEliminar: '',
            pdfUrl: '{{ route("pedidos.pdf", $pedido->id_pedido) }}',

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

            get totalEscaneado() {
                return this.resumen.reduce((sum, i) => sum + i.escaneado, 0);
            },

            init() {
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

            abrirPdfModal() {
                this.pdfUrl = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                this.pdfModal = true;
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
                    console.error('Error cargando datos:', e);
                    this.formBic.voltajes = [];
                    this.formBic.colores = [];
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
                        setTimeout(() => { this.resumen[idx]._flash = false; }, 1100);
                        this.resumen = [...this.resumen];
                    }

                    this.justRegistered = true;
                    setTimeout(() => { this.justRegistered = false; }, 450);

                    setTimeout(() => {
                        this.numSerie = '';
                        this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-blue-500';
                        this.$nextTick(() => { if (this.$refs.qrInput) this.$refs.qrInput.focus(); });
                    }, 600);

                    setTimeout(() => {
                        if (this.$refs.pdfFrame) {
                            this.$refs.pdfFrame.src = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                        }
                        this.pdfUrl = '{{ route("pedidos.pdf", $pedido->id_pedido) }}' + '?t=' + Date.now();
                    }, 500);

                    if (data.pedido_completo) {
                        setTimeout(() => {
                            this.scanModal = false;
                            this.completoModal = true;
                        }, 900);
                    }

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
                lotesFiltrados.forEach((l, i) => {
                    if (l) params.append('lotes[' + i + ']', l);
                });
                const base = '{{ route("pedidos.pdf", $pedido->id_pedido) }}';
                const url = lotesFiltrados.some(l => l) ? base + '?' + params.toString() : base;

                if (this.$refs.pdfFrame) {
                    this.$refs.pdfFrame.src = url;
                }
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

<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

</x-app-layout>