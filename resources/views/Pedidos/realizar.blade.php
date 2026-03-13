<x-app-layout>

    @php
    $totalRequerido = collect($resumen)->sum('requerido');
    $totalEscaneado = collect($resumen)->sum('escaneado');
    @endphp

    <style>
        /* ── Tipografía refinada ── */
        .aws-title {
            font-weight: 600;
            letter-spacing: -0.01em;
            line-height: 1.25;
        }
        
        .aws-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #5f6b7a;
        }
        
        .aws-value {
            font-weight: 600;
            color: #1e2a3a;
        }
        
        .dark .aws-label {
            color: #9ba7b6;
        }
        
        .dark .aws-value {
            color: #f0f4fa;
        }

        /* ── Badges tipo AWS ── */
        .badge-aws {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.25;
            border: 1px solid transparent;
        }
        
        .badge-aws-success {
            background-color: #f0f9f0;
            color: #1e7e34;
            border-color: #b8e0b8;
        }
        
        .badge-aws-warning {
            background-color: #fff7e8;
            color: #b76e1e;
            border-color: #ffe4b8;
        }
        
        .badge-aws-pending {
            background-color: #f2f4f8;
            color: #5f6b7a;
            border-color: #d9e0e8;
        }
        
        .dark .badge-aws-success {
            background-color: rgba(30, 126, 52, 0.15);
            color: #8cd499;
            border-color: #2d6a40;
        }
        
        .dark .badge-aws-warning {
            background-color: rgba(183, 110, 30, 0.15);
            color: #f9c78b;
            border-color: #7a5a3a;
        }
        
        .dark .badge-aws-pending {
            background-color: rgba(95, 107, 122, 0.15);
            color: #b5c1cf;
            border-color: #4a5568;
        }

        /* ── Tabla estilo AWS ── */
        .aws-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        .aws-table th {
            background-color: #f8fafc;
            padding: 0.875rem 1rem;
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #5f6b7a;
            border-bottom: 1px solid #e4e9f0;
            text-align: left;
        }
        
        .aws-table td {
            padding: 1rem 1rem;
            border-bottom: 1px solid #f0f4fa;
            color: #1e2a3a;
        }
        
        .dark .aws-table th {
            background-color: #1a2533;
            color: #9ba7b6;
            border-bottom-color: #2d3748;
        }
        
        .dark .aws-table td {
            border-bottom-color: #253040;
            color: #e8edf5;
        }
        
        .aws-table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .dark .aws-table tbody tr:hover {
            background-color: #1f2c3c;
        }

        /* ── Tarjetas AWS ── */
        .aws-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e9ecf2;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        
        .dark .aws-card {
            background-color: #1e293b;
            border-color: #2d3a4f;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* ── Botones AWS ── */
        .aws-button-primary {
            background-color: #0073bb;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        
        .aws-button-primary:hover {
            background-color: #1a7fc1;
        }
        
        .aws-button-secondary {
            background-color: white;
            color: #1e2a3a;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #d0d9e3;
            transition: all 0.15s ease;
        }
        
        .dark .aws-button-secondary {
            background-color: #2d3748;
            color: #e2e8f0;
            border-color: #4a5568;
        }

        /* ── Barra de progreso refinada ── */
        .aws-progress {
            height: 4px;
            background-color: #e9ecf2;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .dark .aws-progress {
            background-color: #2d3a4f;
        }
        
        .aws-progress-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        /* ── Input refinado ── */
        .aws-input {
            border: 1px solid #d0d9e3;
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            background-color: white;
        }
        
        .dark .aws-input {
            background-color: #1e293b;
            border-color: #3a4a62;
            color: #e8edf5;
        }
        
        .aws-input:focus {
            outline: none;
            border-color: #0073bb;
            box-shadow: 0 0 0 2px rgba(0, 115, 187, 0.2);
        }

        /* ── Select refinado ── */
        .aws-select {
            border: 1px solid #d0d9e3;
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            background-color: white;
            transition: all 0.15s ease;
        }
        
        .dark .aws-select {
            background-color: #1e293b;
            border-color: #3a4a62;
            color: #e8edf5;
        }

        /* ── Serie chip ── */
        .series-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background-color: #f2f6fc;
            border: 1px solid #d9e2ef;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-family: 'IBM Plex Mono', monospace;
            color: #1e2a3a;
        }
        
        .dark .series-chip {
            background-color: #25344a;
            border-color: #3e516b;
            color: #cfddee;
        }

        /* ── Stagger animations mantenidas ── */
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

        .s1 { animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) both; }
        .s2 { animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .07s both; }
        .s3 { animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .14s both; }
        .s4 { animation: slideUp .3s cubic-bezier(.22, .68, 0, 1.2) .21s both; }

        @keyframes rowFlash {
            0% { background: rgba(0, 115, 187, .08); }
            100% { background: transparent; }
        }

        .row-flash { animation: rowFlash 1.2s ease both; }

        @keyframes countBump {
            0% { transform: scale(1); }
            40% { transform: scale(1.3); color: #0073bb; }
            100% { transform: scale(1); }
        }

        .count-bump { animation: countBump .35s cubic-bezier(.22, .68, 0, 1.5) both; }

        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 115, 187, .4); }
            50% { box-shadow: 0 0 0 4px rgba(0, 115, 187, 0); }
        }

        .dot-pulse { animation: pulseDot 1.5s ease infinite; }

        @keyframes scanBeam {
            0% { top: 10%; }
            50% { top: 84%; }
            100% { top: 10%; }
        }

        .scan-beam {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 1.5px;
            background: linear-gradient(90deg, transparent, #0073bb 30%, #0073bb 70%, transparent);
            animation: scanBeam 1.8s ease-in-out infinite;
        }

        .sc {
            position: absolute;
            width: 18px;
            height: 18px;
            border-color: #0073bb;
            border-style: solid;
        }

        .sc-tl { top: 0; left: 0; border-width: 2px 0 0 2px; border-radius: 3px 0 0 0; }
        .sc-tr { top: 0; right: 0; border-width: 2px 2px 0 0; border-radius: 0 3px 0 0; }
        .sc-bl { bottom: 0; left: 0; border-width: 0 0 2px 2px; border-radius: 0 0 0 3px; }
        .sc-br { bottom: 0; right: 0; border-width: 0 2px 2px 0; border-radius: 0 0 3px 0; }

        .modal-bg {
            background: rgba(0, 0, 0, .55);
            backdrop-filter: blur(4px);
        }

        @keyframes scannerPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 115, 187, .4); }
            50% { box-shadow: 0 0 0 8px rgba(0, 115, 187, 0); }
        }

        .btn-scanner-pulse { animation: scannerPulse 2.5s ease infinite; }

        /* ── Mejoras mobile mantenidas y refinadas ── */
        @media (max-width: 640px) {
            .mobile-stack { flex-direction: column; align-items: flex-start !important; gap: 0.75rem !important; }
            .mobile-full-width { width: 100%; }
            .mobile-text-sm { font-size: 0.8125rem; }
            .mobile-px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .mobile-py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
            
            .mobile-fab {
                position: fixed;
                bottom: 1.5rem;
                right: 1.5rem;
                width: 3.5rem;
                height: 3.5rem;
                border-radius: 9999px;
                background: #0073bb;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0, 115, 187, 0.3);
                z-index: 40;
                transition: all 0.2s;
                border: 2px solid white;
            }
            
            .dark .mobile-fab { border-color: #1e293b; }
            .mobile-fab:active { transform: scale(0.95); }
            .mobile-header-compact { padding: 0.75rem; }
            
            .mobile-table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f1f5f9;
            }
            
            .mobile-table-container::-webkit-scrollbar { height: 4px; }
            .mobile-table-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
            .mobile-table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 4px; }
            
            .mobile-table {
                min-width: 800px;
                width: 100%;
                border-collapse: collapse;
                font-size: 0.75rem;
            }
            
            .mobile-table th {
                background: #f8fafc;
                color: #5f6b7a;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.75rem 0.5rem;
                white-space: nowrap;
                border-bottom: 1px solid #e4e9f0;
            }
            
            .dark .mobile-table th {
                background: #1a2533;
                color: #9ba7b6;
                border-bottom-color: #2d3a4f;
            }
            
            .mobile-table td {
                padding: 0.75rem 0.5rem;
                white-space: nowrap;
                border-bottom: 1px solid #f0f4fa;
            }
            
            .dark .mobile-table td { border-bottom-color: #253040; }
            .mobile-table .series-cell { max-width: 200px; white-space: normal; }
            
            .mobile-series-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                background: #f2f6fc;
                border: 1px solid #d9e2ef;
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                margin: 0.125rem;
                font-size: 0.7rem;
            }
            
            .dark .mobile-series-badge {
                background: #25344a;
                border-color: #3e516b;
                color: #cfddee;
            }
            
            .desktop-table { display: none; }
            .mobile-table-container { display: block; }

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
            
            .dark .mobile-pdf-viewer { background: #1e293b; }
            
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
                border-bottom: 1px solid #e4e9f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 10;
            }
            
            .dark .mobile-pdf-viewer .pdf-controls {
                background: #1e293b;
                border-bottom-color: #2d3a4f;
            }
        }
        
        @media (min-width: 641px) {
            .desktop-table { display: block; }
            .mobile-table-container { display: none; }
            .mobile-fab { display: none; }
        }

        [x-cloak] { display: none !important; }
    </style>

    <div
        x-data="realizarPedido({{ Js::from($resumen) }}, '{{ route('gestor.vehiculos.bicicletas.store') }}')"
        x-init="init()"
        class="realizar-root max-w-7xl mx-auto space-y-6 px-4 sm:px-6 py-6">

        {{-- ===== BREADCRUMB + HEADER ===== --}}
        <div class="s1">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-1.5 mb-4 text-xs text-gray-400 dark:text-gray-500 overflow-x-auto pb-1 whitespace-nowrap">
                <a href="{{ route('pedidos.index') }}" class="hover:text-blue-600 transition-colors font-medium">Pedidos</a>
                <svg class="w-3 h-3 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-mono text-xs text-gray-400 flex-shrink-0">#{{ $pedido->id_pedido }}</span>
                <svg class="w-3 h-3 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-medium text-gray-700 dark:text-gray-300 flex-shrink-0">Realizar Pedido</span>
            </div>

            {{-- Título + progreso refinado --}}
            <div class="flex items-start justify-between gap-4 flex-wrap mobile-stack">
                <div class="mobile-full-width">
                    <div class="flex items-center gap-3 mb-1.5">
                        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Realizar Pedido</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 font-mono">#{{ $pedido->id_pedido }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                        {{ optional($pedido->negocio)->nombre_negocio ?? '—' }}
                        <span class="text-gray-300 dark:text-gray-600 mx-1.5">•</span>
                        {{ optional($pedido->usuario)->nombre_usuario ?? '—' }}
                    </p>
                </div>

                {{-- Círculo progreso refinado + botón escáner --}}
                <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                    <div class="text-right">
                        <p class="aws-label mb-1">Progreso</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">
                            <span x-text="totalEscaneado" :class="justRegistered ? 'count-bump' : ''"></span>
                            <span class="text-gray-400 font-normal text-sm"> / {{ $totalRequerido }}</span>
                        </p>
                    </div>
                    <div class="w-14 h-14 relative">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="16.5" fill="none" stroke="#e9ecf2" stroke-width="2.5" class="dark:stroke-gray-700" />
                            <circle cx="20" cy="20" r="16.5" fill="none"
                                :stroke="totalEscaneado >= {{ $totalRequerido }} ? '#1e7e34' : '#0073bb'"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-dasharray="104"
                                :stroke-dashoffset="104 - (Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 104)"
                                style="transition: stroke-dashoffset 0.5s cubic-bezier(0.23, 1, 0.32, 1)" />
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-900 dark:text-white font-mono"
                            x-text="Math.round(Math.min(totalEscaneado, {{ $totalRequerido }}) / {{ $totalRequerido }} * 100) + '%'">
                        </span>
                    </div>

                    {{-- Botón escáner desktop refinado --}}
                    <button
                        type="button"
                        @click="scanModal = true; $nextTick(() => $refs.qrInput?.focus())"
                        class="btn-scanner-pulse hidden sm:flex items-center gap-2 px-4 py-2.5 bg-[#0073bb] hover:bg-[#1a7fc1] text-white rounded-lg text-sm font-medium transition active:scale-95 border border-transparent shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Escanear
                    </button>
                </div>
            </div>

            {{-- Barra progreso refinada --}}
            <div class="aws-progress mt-4">
                <div class="aws-progress-fill"
                    :style="`width:${Math.min(100, Math.round(totalEscaneado / {{ $totalRequerido }} * 100))}%; background:${totalEscaneado >= {{ $totalRequerido }} ? '#1e7e34' : '#0073bb'}`">
                </div>
            </div>
        </div>

        {{-- ===== ESTADO DEL PEDIDO refinado ===== --}}
        <div class="aws-card s2">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <p class="aws-label">Estado del Pedido</p>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-xs font-mono text-gray-500">
                        <span x-text="resumen.filter(i => i.escaneado >= i.requerido).length" class="font-semibold text-gray-900 dark:text-white"></span>
                        <span>/</span>
                        <span>{{ count($resumen) }}</span>
                    </div>
                    <button
                        type="button"
                        @click="loteModal = true"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:hover:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-md text-xs font-medium border border-amber-200 dark:border-amber-800 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="hidden sm:inline">Lote de Batería</span>
                    </button>
                </div>
            </div>
            
            {{-- VISTA DESKTOP refinada --}}
            <div class="desktop-table">
                <table class="aws-table">
                    <thead>
                        <tr>
                            <th>Modelo</th>
                            <th>Color</th>
                            <th>Voltaje</th>
                            <th class="text-center">Requerido</th>
                            <th class="text-center">Escaneado</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Series</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in resumen" :key="idx">
                            <tr :class="[
                                item.escaneado >= item.requerido ? 'bg-green-50/30 dark:bg-green-900/5' : '',
                                item._flash ? 'row-flash' : ''
                            ]">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full"
                                            :class="item.escaneado >= item.requerido ? 'bg-green-500' : 'bg-blue-500'">
                                        </div>
                                        <span class="font-medium" x-text="item.modelo"></span>
                                    </div>
                                </td>
                                <td class="text-gray-600 dark:text-gray-400" x-text="item.color"></td>
                                <td class="font-mono text-gray-600 dark:text-gray-400" x-text="item.voltaje"></td>
                                <td class="text-center font-mono font-medium" x-text="item.requerido"></td>
                                <td class="text-center">
                                    <span class="font-mono font-semibold"
                                        :class="item.escaneado >= item.requerido ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400'"
                                        x-text="item.escaneado">
                                    </span>
                                </td>
                                <td class="text-center">
                                    <template x-if="item.escaneado >= item.requerido">
                                        <span class="badge-aws badge-aws-success">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completo
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado > 0 && item.escaneado < item.requerido">
                                        <span class="badge-aws badge-aws-warning">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="item.requerido - item.escaneado"></span> restante
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado === 0">
                                        <span class="badge-aws badge-aws-pending">
                                            Pendiente
                                        </span>
                                    </template>
                                </td>
                                <td class="text-center">
                                    <div class="flex flex-wrap gap-1.5 justify-center">
                                        <template x-for="serie in item.num_series" :key="serie">
                                            <div class="series-chip">
                                                <span class="font-mono text-xs" x-text="serie"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="abrirModalEliminar(serie)"
                                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <span x-show="!item.num_series || item.num_series.length === 0"
                                            class="text-xs text-gray-400 font-mono">—</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            {{-- VISTA MÓVIL refinada --}}
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
                                            :class="item.escaneado >= item.requerido ? 'bg-green-500' : 'bg-blue-500'">
                                        </div>
                                        <span class="font-medium" x-text="item.modelo"></span>
                                    </div>
                                </td>
                                <td x-text="item.color"></td>
                                <td class="font-mono" x-text="item.voltaje"></td>
                                <td class="text-center font-mono font-medium" x-text="item.requerido"></td>
                                <td class="text-center">
                                    <span class="font-mono font-semibold" :class="item.escaneado >= item.requerido ? 'text-green-600' : 'text-blue-600'" x-text="item.escaneado"></span>
                                </td>
                                <td>
                                    <template x-if="item.escaneado >= item.requerido">
                                        <span class="badge-aws badge-aws-success whitespace-nowrap">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Completo
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado > 0 && item.escaneado < item.requerido">
                                        <span class="badge-aws badge-aws-warning whitespace-nowrap">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="item.requerido - item.escaneado"></span> restante
                                        </span>
                                    </template>
                                    <template x-if="item.escaneado === 0">
                                        <span class="badge-aws badge-aws-pending whitespace-nowrap">
                                            Pendiente
                                        </span>
                                    </template>
                                </td>
                                <td class="series-cell">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="serie in item.num_series" :key="serie">
                                            <span class="mobile-series-badge">
                                                <span class="font-mono text-xs" x-text="serie"></span>
                                                <button
                                                    type="button"
                                                    @click.stop="abrirModalEliminar(serie)"
                                                    class="text-gray-400 hover:text-red-500">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="!item.num_series || item.num_series.length === 0"
                                            class="text-xs text-gray-400 font-mono">—</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== FORMULARIO EMISIÓN refinado ===== --}}
        <div class="aws-card s3">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <p class="aws-label">Formulario de Emisión</p>
                <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}" target="_blank"
                    class="flex items-center gap-1.5 text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 transition-colors border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 px-3 py-1.5 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Descargar PDF
                </a>
            </div>
            <div class="hidden md:block">
                <iframe x-ref="pdfFrame" src="{{ route('pedidos.pdf', $pedido->id_pedido) }}"
                    class="w-full rounded-b-xl" style="height:600px; border:none;"></iframe>
            </div>
            <div class="block md:hidden p-8 text-center bg-gray-50 dark:bg-gray-800/50">
                <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Vista previa no disponible en móvil.</p>
                <button @click="pdfModal = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
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
            <div style="min-width:600px; font-family:Arial,sans-serif; font-size:10px; font-weight:bold;">

                {{-- Encabezado --}}
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

                {{-- Tabla de ítems --}}
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
</div>

        {{-- ===== MODAL ESCANEAR refinado ===== --}}
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
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-xl sm:rounded-xl shadow-2xl overflow-hidden"
                @click.stop>

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#0073bb] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Registrar Bicicleta</h3>
                            <p class="text-xs text-gray-500">Ingresa o escanea el número de serie</p>
                        </div>
                    </div>
                    <button @click="scanModal = false; cerrarCamara()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Input QR refinado --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Número de Serie</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
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
                                class="w-full pl-10 pr-20 py-2.5 rounded-lg border text-sm font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-[#0073bb] focus:border-transparent transition dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                <span class="text-xs text-gray-400 font-mono" x-text="numSerie.length + '/17'"></span>
                                <button type="button" @click="limpiarQr()" x-show="numSerie.length > 0"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Selects refinados --}}
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                            <select x-model="formBic.id_modelo" @change="onModeloChange()"
                                class="aws-select w-full">
                                <option value="">— Seleccionar modelo —</option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                                <select x-model="formBic.id_voltaje" :disabled="!formBic.voltajes.length"
                                    class="aws-select w-full disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">— voltaje —</option>
                                    <template x-for="v in formBic.voltajes" :key="v.id_voltaje">
                                        <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Color</label>
                                <select x-model="formBic.id_color" :disabled="!formBic.colores.length"
                                    class="aws-select w-full disabled:opacity-50 disabled:cursor-not-allowed">
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
                        <div class="relative rounded-lg overflow-hidden bg-black" style="height:200px;">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <canvas x-ref="canvasEl" class="hidden"></canvas>
                    </div>

                    {{-- Footer del modal refinado --}}
                    <div class="flex gap-2 pt-1">
                        <button type="button" @click="abrirCamara()"
                            class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="sm:hidden">Cámara</span>
                        </button>
                        <button type="button"
                            @click="registrarBicicleta()"
                            :disabled="!numSerie || numSerie.length !== 17 || !formBic.id_modelo || !formBic.id_voltaje || !formBic.id_color || guardando"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0073bb] hover:bg-[#1a7fc1] text-white rounded-lg text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                            <template x-if="!guardando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="guardando">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </template>
                            <span x-text="guardando ? 'Registrando...' : 'Registrar'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MODAL ERROR refinado ===== --}}
        <div x-show="errorModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4"
            @click.self="errorModal = false">
            <div x-show="errorModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Bicicleta no válida</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="errorMensaje"></p>
                    </div>
                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-left border border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-400 mb-2 uppercase tracking-wider">Datos escaneados</p>
                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between"><span class="text-gray-400">Serie</span><span class="font-mono font-semibold text-gray-800 dark:text-gray-200" x-text="numSerie"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Modelo</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.modeloNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Color</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.colorNombre || '—'"></span></div>
                            <div class="flex justify-between"><span class="text-gray-400">Voltaje</span><span class="text-gray-700 dark:text-gray-300" x-text="formBic.voltajeNombre || '—'"></span></div>
                        </div>
                    </div>
                    <button @click="errorModal = false; limpiarQr()"
                        class="w-full py-2.5 bg-[#232f3e] dark:bg-[#0073bb] text-white rounded-lg text-sm font-medium hover:opacity-90 transition">
                        Intentar de nuevo
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL ELIMINAR refinado ===== --}}
        <div x-show="eliminarModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4"
            @click.self="eliminarModal = false">
            <div x-show="eliminarModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.stop>
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">¿Eliminar bicicleta?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            Vas a eliminar la bicicleta con número de serie:
                        </p>
                        <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 py-2 px-3 rounded-lg break-all border border-gray-200 dark:border-gray-600" x-text="numSerieEliminar"></p>
                        <p class="text-xs text-gray-400 mt-3">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="flex gap-3 w-full">
                        <button @click="eliminarModal = false"
                            class="flex-1 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button @click="confirmarEliminar()"
                            :disabled="eliminando"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!eliminando">Eliminar</span>
                            <span x-show="eliminando" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="hidden sm:inline">Eliminando...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MODAL LOTE DE BATERÍA refinado ===== --}}
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
                class="bg-white dark:bg-gray-800 w-full sm:max-w-lg rounded-t-xl sm:rounded-xl shadow-2xl overflow-hidden"
                @click.stop>

                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Lote de Batería</h3>
                            <p class="text-xs text-gray-500">Máximo {{ $totalRequerido }} entradas</p>
                        </div>
                    </div>
                    <button @click="loteModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-3 overflow-y-auto" style="max-height: 60vh;">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Ingresa los códigos de lote en orden. Puedes dejar vacíos los que no apliquen.
                    </p>

                    <template x-for="(lote, idx) in loteBaterias" :key="idx">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-medium text-gray-400 font-mono w-6 text-right shrink-0" x-text="idx + 1"></span>
                            <input
                                type="text"
                                x-model="loteBaterias[idx]"
                                @keydown.enter.prevent="idx < loteBaterias.length - 1 && $refs['loteInput' + (idx + 1)]?.[0]?.focus()"
                                :x-ref="'loteInput' + idx"
                                maxlength="30"
                                placeholder="Código de lote..."
                                autocomplete="off"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                        </div>
                    </template>
                </div>

                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex gap-2">
                    <button type="button" @click="limpiarLotes()"
                        class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition flex-1">
                        Limpiar
                    </button>
                    <button type="button" @click="aplicarLotes()"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
                        </svg>
                        Aplicar
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL PEDIDO COMPLETO refinado ===== --}}
        <div x-show="completoModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 modal-bg flex items-center justify-center z-50 px-4">
            <div x-show="completoModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-sm text-center mx-4 border border-gray-100 dark:border-gray-700">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-2">¡Pedido Completo!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Todas las bicicletas han sido registradas.
                </p>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('pedidos.pdf', $pedido->id_pedido) }}" target="_blank"
                        class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar PDF
                    </a>
                    <a href="{{ route('pedidos.index') }}"
                        class="w-full py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg text-sm font-medium hover:opacity-90 transition border border-gray-200 dark:border-gray-600">
                        Volver a Pedidos
                    </a>
                </div>
            </div>
        </div>

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
            errorModal: false,
            completoModal: false,
            pdfModal: false,
            errorMensaje: '',
            inputClass: 'border-gray-300 dark:border-gray-600 focus:ring-[#0073bb]',
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
                this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-[#0073bb]';
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
                        setTimeout(() => { this.resumen[idx]._flash = false; }, 1200);
                        this.resumen = [...this.resumen];
                    }

                    this.justRegistered = true;
                    setTimeout(() => { this.justRegistered = false; }, 350);

                    setTimeout(() => {
                        this.numSerie = '';
                        this.inputClass = 'border-gray-300 dark:border-gray-600 focus:ring-[#0073bb]';
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