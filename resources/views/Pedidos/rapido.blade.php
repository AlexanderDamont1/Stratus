<x-app-layout>
    <style>
        /* ===========================
           TIPOGRAFÍA Y BASE
           =========================== */
        .rapido-root { font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .mono { font-family: 'IBM Plex Mono', 'Consolas', monospace !important; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }
        
        /* ===========================
           TARJETAS
           =========================== */
        .aws-card {
            background-color: #ffffff;
            border-radius: 0.875rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dark .aws-card {
            background-color: #1e293b;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .aws-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06), 0 4px 6px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }
        
        .dark .aws-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border-color: #475569;
        }
        
        /* ===========================
           LABELS Y TEXTOS
           =========================== */
        .aws-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        
        .dark .aws-label {
            color: #94a3b8;
        }
        
        .section-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
        }
        
        .dark .section-title {
            color: #e2e8f0;
        }
        
        /* ===========================
           INPUTS Y SELECTS
           =========================== */
        .aws-input, .aws-select {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #ffffff;
            width: 100%;
        }
        
        .dark .aws-input, .dark .aws-select {
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }
        
        .aws-input:focus, .aws-select:focus {
            outline: none;
            border-color: #0073bb;
            box-shadow: 0 0 0 3px rgba(0, 115, 187, 0.12);
        }
        
        .aws-input::placeholder {
            color: #94a3b8;
        }
        
        /* Mobile: inputs más grandes */
        @media (max-width: 640px) {
            .aws-input, .aws-select {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
        }
        
        /* ===========================
           BOTONES
           =========================== */
        .aws-button-primary {
            background: linear-gradient(135deg, #0073bb 0%, #005a91 100%);
            color: white;
            font-weight: 600;
            font-size: 0.8125rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 1px 2px rgba(0, 115, 187, 0.3);
        }
        
        .aws-button-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #0073bb 0%, #0068a3 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 115, 187, 0.25);
        }
        
        .aws-button-primary:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .aws-button-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .aws-button-secondary {
            background-color: #ffffff;
            color: #475569;
            font-weight: 500;
            font-size: 0.8125rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dark .aws-button-secondary {
            background-color: #1e293b;
            color: #e2e8f0;
            border-color: #475569;
        }
        
        .aws-button-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .dark .aws-button-secondary:hover {
            background-color: #334155;
        }
        
        .aws-button-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            font-weight: 600;
            font-size: 0.8125rem;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 1px 2px rgba(220, 38, 38, 0.3);
        }
        
        .aws-button-danger:hover:not(:disabled) {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        .aws-button-danger:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        /* ===========================
           SERIES CHIP
           =========================== */
        .series-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
            font-family: 'IBM Plex Mono', monospace;
            color: #334155;
            transition: all 0.15s ease;
        }
        
        .dark .series-chip {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #475569;
            color: #e2e8f0;
        }
        
        .series-chip:hover {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border-color: #94a3b8;
        }
        
        .dark .series-chip:hover {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
            border-color: #64748b;
        }
        
        /* ===========================
           TABLA
           =========================== */
        .aws-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
        }
        
        .aws-table th {
            padding: 0.75rem 0.75rem 0.75rem 0;
            font-weight: 600;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }
        
        .dark .aws-table th {
            color: #94a3b8;
            border-bottom-color: #334155;
        }
        
        .aws-table td {
            padding: 0.875rem 0.75rem 0.875rem 0;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        
        .dark .aws-table td {
            border-bottom-color: #1e293b;
            color: #e2e8f0;
        }
        
        .aws-table tr:last-child td {
            border-bottom: none;
        }
        
        .aws-table tr:hover td {
            background-color: #f8fafc;
        }
        
        .dark .aws-table tr:hover td {
            background-color: #0f172a;
        }
        
        /* ===========================
           ANIMACIONES
           =========================== */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-up {
            animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        @keyframes scanPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 115, 187, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(0, 115, 187, 0); }
        }
        
        .scan-pulse {
            animation: scanPulse 2s infinite;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        
        /* ===========================
           MODAL / BOTTOM SHEET
           =========================== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .dark .modal-overlay {
            background-color: rgba(0, 0, 0, 0.75);
        }
        
        .modal-content {
            background-color: white;
            border-radius: 1rem;
            max-width: 28rem;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .dark .modal-content {
            background-color: #1e293b;
        }
        
        @media (max-width: 640px) {
            .modal-overlay {
                align-items: flex-end;
                padding: 0;
            }
            
            .modal-content {
                max-width: 100%;
                max-height: 85vh;
                border-radius: 1.25rem 1.25rem 0 0;
            }
        }
        
        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            border-radius: 1.25rem 1.25rem 0 0;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.15);
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .dark .bottom-sheet {
            background-color: #1e293b;
        }
        
        .bottom-sheet.active {
            transform: translateY(0);
        }
        
        .bottom-sheet-handle {
            width: 40px;
            height: 4px;
            background: linear-gradient(90deg, #cbd5e1, #e2e8f0);
            border-radius: 2px;
            margin: 0.75rem auto;
        }
        
        .dark .bottom-sheet-handle {
            background: linear-gradient(90deg, #475569, #64748b);
        }
        
        /* ===========================
           ITEM CARD (MOBILE)
           =========================== */
        .item-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.15s ease;
        }
        
        .item-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .dark .item-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .dark .item-card:hover {
            border-color: #475569;
        }
        
        /* ===========================
           FLOATING ACTION BUTTON
           =========================== */
        .fab {
            position: fixed;
            bottom: 5.5rem;
            right: 1.5rem;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #0073bb, #005a91);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 115, 187, 0.4), 0 2px 6px rgba(0, 0, 0, 0.2);
            z-index: 40;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .fab:hover:not(:disabled) {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 115, 187, 0.5), 0 4px 10px rgba(0, 0, 0, 0.25);
        }
        
        .fab:active:not(:disabled) {
            transform: scale(1);
        }
        
        .fab:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* ===========================
           PREVIEW PANEL (IFRAME-LIKE)
           =========================== */
        .preview-panel {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            overflow: hidden;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .dark .preview-panel {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-color: #334155;
        }
        
        .preview-header {
            background: linear-gradient(90deg, #f1f5f9, #e2e8f0);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .dark .preview-header {
            background: linear-gradient(90deg, #334155, #1e293b);
            border-bottom-color: #475569;
        }
        
        .preview-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .preview-dot.red { background-color: #ef4444; }
        .preview-dot.yellow { background-color: #f59e0b; }
        .preview-dot.green { background-color: #22c55e; }
        
        .preview-body {
            padding: 1.5rem;
            min-height: 400px;
            max-height: calc(100vh - 280px);
            overflow-y: auto;
        }
        
        /* ===========================
           BADGES Y STATUS
           =========================== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .status-badge.success {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .dark .status-badge.success {
            background-color: #166534;
            color: #dcfce7;
        }
        
        .status-badge.warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .dark .status-badge.warning {
            background-color: #92400e;
            color: #fef3c7;
        }
        
        .status-badge.info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .dark .status-badge.info {
            background-color: #1e40af;
            color: #dbeafe;
        }
        
        /* ===========================
           SCROLLBAR PERSONALIZADA
           =========================== */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        
        /* ===========================
           UTILIDADES
           =========================== */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #0073bb, #005a91);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    <div
        x-data="pedidoRapido({{ Js::from($modelos->map(fn($m) => ['id' => $m->id_modelo, 'nombre' => $m->nombre_modelo])) }})"
        class="rapido-root"
        :class="isMobile ? 'px-3 py-4 pb-28' : 'px-4 sm:px-6 py-6'">

        {{-- ===========================
             HEADER
             =========================== --}}
        <div class="animate-slide-up mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#0073bb] to-[#004d80] flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Emisión Rápida</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-0.5">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                Genera PDF sin registrar en sistema
                            </span>
                        </p>
                    </div>
                </div>
                
                {{-- Contador rápido --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300" 
                          x-text="items.reduce((s, i) => s + i.series.length, 0)"></span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">unidades</span>
                </div>
            </div>
        </div>

        <form id="formRapido" method="POST" action="{{ route('pedidos.rapido.pdf') }}" target="_blank">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- ===========================
                     COLUMNA IZQUIERDA: FORMULARIO
                     =========================== --}}
                <div class="lg:col-span-2 space-y-5">
                    
                    {{-- Datos generales --}}
                    <div class="aws-card p-5 sm:p-6 animate-slide-up" style="animation-delay: 0.05s">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-1.5 h-5 bg-gradient-to-b from-[#0073bb] to-[#004d80] rounded-full"></div>
                            <span class="aws-label">Datos del Formulario</span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Fecha</label>
                                <input type="text" name="fecha" x-model="fechaHoy"
                                    class="aws-input text-sm bg-gray-50 dark:bg-gray-800/50 font-mono" readonly>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Cliente</label>
                                <input type="text" name="cliente" placeholder="Nombre"
                                    class="aws-input text-sm" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Distancia</label>
                                <input type="text" name="distancia" placeholder="—"
                                    class="aws-input text-sm" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Transporte</label>
                                <input type="text" name="transporte" placeholder="Fábrica"
                                    class="aws-input text-sm" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Costo</label>
                                <input type="text" name="costo_envio" placeholder="$"
                                    class="aws-input text-sm" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    {{-- Scanner (Desktop) --}}
                    <div class="aws-card p-5 sm:p-6 animate-slide-up hidden lg:block" style="animation-delay: 0.1s">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-1.5 h-5 bg-gradient-to-b from-[#0073bb] to-[#004d80] rounded-full"></div>
                            <span class="aws-label">Escanear Bicicleta</span>
                        </div>

                        <div class="space-y-4">
                            {{-- Número de serie --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    Número de serie
                                    <span class="text-gray-400 ml-1.5 font-mono text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">(17 caracteres)</span>
                                </label>
                                <div class="relative">
                                    <input
                                        x-ref="serieInput"
                                        type="text"
                                        x-model="numSerie"
                                        @input="onSerieInput()"
                                        @keydown.enter.prevent="agregarItem()"
                                        maxlength="17"
                                        placeholder="Escribe o escanea el código..."
                                        autocomplete="off"
                                        class="aws-input pl-4 pr-24 py-3.5 font-mono tracking-wide text-base"
                                        :class="{ 'border-blue-400 ring-2 ring-blue-100 dark:ring-blue-900': numSerie.length === 17 }">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                        <span class="text-xs font-mono text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md"
                                              x-text="numSerie.length + '/17'"></span>
                                        <button type="button" @click="limpiarSerie()"
                                            x-show="numSerie.length > 0"
                                            class="text-gray-400 hover:text-red-500 transition p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Feedback --}}
                                <div x-show="modeloDetectado || errorSerie" x-cloak class="mt-2.5">
                                    <template x-if="modeloDetectado">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                            <span class="text-xs text-green-700 dark:text-green-400">Modelo:</span>
                                            <span class="text-xs font-semibold text-green-800 dark:text-green-300" x-text="modeloDetectado"></span>
                                        </div>
                                    </template>
                                    <template x-if="errorSerie">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs text-red-600 dark:text-red-400" x-text="errorSerie"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Lote --}}
                            <div class="max-w-md">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    Lote de batería <span class="text-gray-400">(opcional)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" x-model="lote" placeholder="Ej: L-2024-09"
                                        class="aws-input text-sm pr-10">
                                    <button type="button" @click="lote = ''"
                                        x-show="lote.length > 0"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Voltaje y Color --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Voltaje</label>
                                    <select x-model="form.id_voltaje" :disabled="!form.voltajes.length"
                                        class="aws-select text-sm disabled:opacity-50">
                                        <option value="">— Seleccionar —</option>
                                        <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Color</label>
                                    <select x-model="form.id_color" :disabled="!form.colores.length"
                                        class="aws-select text-sm disabled:opacity-50">
                                        <option value="">— Seleccionar —</option>
                                        <template x-for="c in form.colores" :key="c.id_color">
                                            <option :value="c.id_color" x-text="c.color"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" @click="agregarItem()"
                                        :disabled="numSerie.length !== 17 || !form.id_modelo || !form.id_voltaje || !form.id_color"
                                        class="aws-button-primary w-full flex items-center justify-center gap-2 h-[42px]"
                                        :class="{ 'scan-pulse': numSerie.length === 17 && form.id_modelo && form.id_voltaje && form.id_color }">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Agregar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de items (Desktop) --}}
                    <div class="aws-card p-5 sm:p-6 animate-slide-up hidden lg:block" style="animation-delay: 0.15s">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-1.5 h-5 bg-gradient-to-b from-[#0073bb] to-[#004d80] rounded-full"></div>
                                <span class="aws-label">Artículos Escaneados</span>
                            </div>
                            <span class="status-badge info" x-text="items.reduce((s, i) => s + i.series.length, 0) + ' unidades'"></span>
                        </div>

                        <div x-show="items.length === 0" x-cloak class="py-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Escanea una bicicleta para comenzar</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Usa el scanner o el botón + en mobile</p>
                        </div>

                        <div x-show="items.length > 0" x-cloak class="overflow-x-auto custom-scrollbar">
                            <table class="aws-table min-w-[700px]">
                                <thead>
                                    <tr>
                                        <th class="pl-0">Modelo</th>
                                        <th>Voltaje</th>
                                        <th>Color</th>
                                        <th class="text-center">Cant.</th>
                                        <th>Números de Serie</th>
                                        <th>Lote</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <input type="hidden" :name="`items[${index}][id_modelo]`" :value="item.id_modelo">
                                            <input type="hidden" :name="`items[${index}][id_voltaje]`" :value="item.id_voltaje">
                                            <input type="hidden" :name="`items[${index}][id_color]`" :value="item.id_color">
                                            <input type="hidden" :name="`items[${index}][cantidad]`" :value="item.series.length">
                                            <input type="hidden" :name="`items[${index}][lote]`" :value="item.lote">
                                            <template x-for="(serie, si) in item.series">
                                                <input type="hidden" :name="`items[${index}][series][${si}]`" :value="serie">
                                            </template>

                                            <td class="pl-0 font-semibold text-gray-900 dark:text-white" x-text="item.modelo_nombre"></td>
                                            <td>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800"
                                                      x-text="item.voltaje_nombre"></span>
                                            </td>
                                            <td class="text-gray-600 dark:text-gray-400" x-text="item.color_nombre"></td>
                                            <td class="text-center">
                                                <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-bold bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full shadow-sm"
                                                      x-text="item.series.length"></span>
                                            </td>
                                            <td>
                                                <div class="flex flex-col gap-1.5 max-w-xs">
                                                    <template x-for="(serie, si) in item.series.slice(0, 3)" :key="si">
                                                        <div class="series-chip group">
                                                            <span class="font-mono text-xs truncate" x-text="serie"></span>
                                                            <button type="button" @click="quitarSerie(index, si)"
                                                                class="text-gray-400 hover:text-red-500 transition flex-shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <span x-show="item.series.length > 3"
                                                          class="text-xs text-gray-400 dark:text-gray-500 pl-1"
                                                          x-text="'+' + (item.series.length - 3) + ' más'"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" x-model="item.lote" placeholder="—"
                                                       class="w-28 px-2 py-1 text-xs border border-gray-200 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 font-mono focus:outline-none focus:border-blue-400">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" @click="quitarItem(index)"
                                                    class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium transition inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ===========================
                     COLUMNA DERECHA: PREVIEW EN VIVO
                     =========================== --}}
                <div class="lg:col-span-1 hidden lg:block animate-slide-up" style="animation-delay: 0.2s">
                    <div class="sticky top-6">
                        <div class="preview-panel">
                            <div class="preview-header">
                                <span class="preview-dot red"></span>
                                <span class="preview-dot yellow"></span>
                                <span class="preview-dot green"></span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 ml-2">Vista Previa del PDF</span>
                            </div>
                            <div class="preview-body custom-scrollbar bg-white dark:bg-gray-900">
                                {{-- Header del preview --}}
                                <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#0073bb] to-[#004d80] flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Orden de Emisión</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="fechaHoy"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-gray-400">Cliente:</span>
                                            <p class="font-medium text-gray-700 dark:text-gray-300 truncate" x-text="document.querySelector('[name=cliente]')?.value || '—'"></p>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Transporte:</span>
                                            <p class="font-medium text-gray-700 dark:text-gray-300 truncate" x-text="document.querySelector('[name=transporte]')?.value || '—'"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Lista de items en preview --}}
                                <div x-show="items.length === 0" class="text-center py-8">
                                    <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-400">Los items aparecerán aquí</p>
                                </div>

                                <div x-show="items.length > 0" class="space-y-3">
                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="item.modelo_nombre"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span x-text="item.voltaje_nombre"></span> • <span x-text="item.color_nombre"></span>
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold bg-blue-500 text-white rounded-full"
                                                      x-text="item.series.length"></span>
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="serie in item.series.slice(0, 5)" :key="serie">
                                                    <span class="inline-block px-1.5 py-0.5 bg-white dark:bg-gray-700 rounded text-[9px] font-mono text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
                                                          x-text="serie"></span>
                                                </template>
                                                <span x-show="item.series.length > 5"
                                                      class="text-[9px] text-gray-400"
                                                      x-text="'+' + (item.series.length - 5)"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Total --}}
                                <div x-show="items.length > 0" class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Total Unidades</span>
                                        <span class="text-lg font-bold text-gray-900 dark:text-white" 
                                              x-text="items.reduce((s, i) => s + i.series.length, 0)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        {{-- ===========================
             MOBILE: Bottom Sheet Scanner
             =========================== --}}
        <div x-show="showScannerMobile" x-cloak
             class="bottom-sheet active lg:hidden"
             @click.self="showScannerMobile = false">
            <div class="bottom-sheet-handle"></div>
            
            <div class="px-5 pb-6 pt-2">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Escanear Bicicleta</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ingresa el número de serie</p>
                    </div>
                    <button type="button" @click="showScannerMobile = false"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Número de serie --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                            Número de serie
                            <span class="text-gray-400 ml-1.5 font-mono text-[10px]">(17 caracteres)</span>
                        </label>
                        <div class="relative">
                            <input
                                x-ref="serieInputMobile"
                                type="text"
                                x-model="numSerie"
                                @input="onSerieInput()"
                                @keydown.enter.prevent="agregarItem()"
                                maxlength="17"
                                placeholder="Escribe o escanea..."
                                autocomplete="off"
                                class="aws-input pl-4 pr-16 py-4 font-mono tracking-wide text-base"
                                :class="{ 'border-blue-400 ring-2 ring-blue-100': numSerie.length === 17 }">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs font-mono text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-md"
                                      x-text="numSerie.length + '/17'"></span>
                            </div>
                        </div>
                        
                        <div x-show="modeloDetectado || errorSerie" x-cloak class="mt-2.5">
                            <template x-if="modeloDetectado">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <span class="text-xs text-green-700 dark:text-green-400" x-text="modeloDetectado"></span>
                                </div>
                            </template>
                            <template x-if="errorSerie">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs text-red-600 dark:text-red-400" x-text="errorSerie"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Lote --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                            Lote <span class="text-gray-400">(opcional)</span>
                        </label>
                        <input type="text" x-model="lote" placeholder="Ej: L-2024-09"
                            class="aws-input text-sm">
                    </div>

                    {{-- Voltaje y Color --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Voltaje</label>
                            <select x-model="form.id_voltaje" :disabled="!form.voltajes.length"
                                class="aws-select text-sm disabled:opacity-50">
                                <option value="">— —</option>
                                <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                    <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Color</label>
                            <select x-model="form.id_color" :disabled="!form.colores.length"
                                class="aws-select text-sm disabled:opacity-50">
                                <option value="">— —</option>
                                <template x-for="c in form.colores" :key="c.id_color">
                                    <option :value="c.id_color" x-text="c.color"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Botón Agregar --}}
                    <button type="button" @click="agregarItem()"
                        :disabled="numSerie.length !== 17 || !form.id_modelo || !form.id_voltaje || !form.id_color"
                        class="aws-button-primary w-full py-4 text-base flex items-center justify-center gap-2 mt-2"
                        :class="{ 'scan-pulse': numSerie.length === 17 && form.id_modelo && form.id_voltaje && form.id_color }">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Agregar Bicicleta</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===========================
             MOBILE: Lista de Items (Cards)
             =========================== --}}
        <div class="aws-card p-5 lg:hidden animate-slide-up" style="animation-delay: 0.15s">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-1.5 h-5 bg-gradient-to-b from-[#0073bb] to-[#004d80] rounded-full"></div>
                    <span class="aws-label">Artículos</span>
                </div>
                <span class="status-badge info" x-text="items.reduce((s, i) => s + i.series.length, 0) + ' un.'"></span>
            </div>

            <div x-show="items.length === 0" x-cloak class="py-8 text-center">
                <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Toca + para escanear</p>
            </div>

            <div x-show="items.length > 0" x-cloak class="space-y-3 max-h-64 overflow-y-auto custom-scrollbar">
                <template x-for="(item, index) in items" :key="index">
                    <div class="item-card" @click="abrirModalItem(index)">
                        <div class="flex items-start justify-between mb-2">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white truncate" x-text="item.modelo_nombre"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    <span x-text="item.voltaje_nombre"></span> • <span x-text="item.color_nombre"></span>
                                </p>
                            </div>
                            <div class="text-right ml-3 flex-shrink-0">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white font-mono font-bold text-sm shadow-sm"
                                      x-text="item.series.length"></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-1 mt-2">
                            <template x-for="(serie, si) in item.series.slice(0, 3)" :key="si">
                                <span class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-[10px] font-mono text-gray-600 dark:text-gray-400 truncate max-w-[70px]"
                                      x-text="serie"></span>
                            </template>
                            <span x-show="item.series.length > 3"
                                  class="inline-block px-2 py-0.5 bg-gray-200 dark:bg-gray-600 rounded text-[10px] font-mono text-gray-600 dark:text-gray-400"
                                  x-text="'+' + (item.series.length - 3)"></span>
                        </div>

                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-[10px] text-gray-400">Lote:</span>
                            <span class="text-xs font-mono text-gray-600 dark:text-gray-300 ml-1" 
                                  x-text="item.lote || '—'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===========================
             MOBILE: Floating Action Button
             =========================== --}}
        <button type="button" @click="showScannerMobile = true"
            class="fab lg:hidden"
            title="Escanear bicicleta">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        {{-- ===========================
             Botón Generar PDF (Sticky en mobile)
             =========================== --}}
        <div class="fixed bottom-0 left-0 right-0 p-3 lg:static lg:p-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700 lg:bg-transparent lg:border-0 lg:animate-slide-up"
             style="lg:animation-delay: 0.2s; z-index: 30;">
            <button type="button" @click="generarPdf()"
                :disabled="items.length === 0"
                class="aws-button-danger w-full lg:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 text-base shadow-lg"
                :class="items.length === 0 ? 'opacity-50' : ''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-text="items.length === 0 ? 'Sin artículos' : 'Generar PDF (' + items.reduce((s, i) => s + i.series.length, 0) + ' un.)'"></span>
            </button>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pedidoRapido', (catalogoModelos) => ({

                catalogoModelos: catalogoModelos,
                items: [],
                numSerie: '',
                lote: '',
                modeloDetectado: '',
                errorSerie: '',
                isMobile: window.innerWidth < 1024,
                showScannerMobile: false,
                showItemModal: false,
                selectedItem: null,
                selectedItemIndex: null,

                form: {
                    id_modelo: '',
                    id_voltaje: '',
                    id_color: '',
                    voltajes: [],
                    colores: [],
                },

                fechaHoy: new Date().toLocaleDateString('es-MX', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                }).replace(/\//g, '-'),

                mapaModelos: {
                    '14': 'Zeus', '05': 'Galaxy', '03': 'Primavera', '19': 'Reina',
                    '09': 'VmpS5', '06': 'Rayo', '11': 'Polar', '24': 'Urbex',
                    '18': 'Eclipce', '07': 'Aguila', '08': 'Sol', '16': 'Sol Pro',
                },

                init() {
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                    });
                },

                limpiarSerie() {
                    this.numSerie = '';
                    this.modeloDetectado = '';
                    this.errorSerie = '';
                    this.form.id_modelo = '';
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];
                    this.focusInput();
                },

                focusInput() {
                    this.$nextTick(() => {
                        if (this.isMobile && this.$refs.serieInputMobile) {
                            this.$refs.serieInputMobile.focus();
                        } else if (this.$refs.serieInput) {
                            this.$refs.serieInput.focus();
                        }
                    });
                },

                onSerieInput() {
                    this.numSerie = this.numSerie.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    this.errorSerie = '';
                    this.modeloDetectado = '';

                    if (this.numSerie.length === 17) {
                        const codigo = this.numSerie.substring(11, 13);
                        const nombreModelo = this.mapaModelos[codigo] ?? null;

                        if (nombreModelo) {
                            this.modeloDetectado = nombreModelo;
                            this.detectarModelo(nombreModelo);
                        } else {
                            this.errorSerie = `Código no reconocido: ${codigo}`;
                            this.form.id_modelo = '';
                            this.form.voltajes = [];
                            this.form.colores = [];
                        }
                    } else {
                        this.form.id_modelo = '';
                        this.form.voltajes = [];
                        this.form.colores = [];
                    }
                },

                async detectarModelo(nombreModelo) {
                    const modeloEncontrado = this.catalogoModelos.find(m =>
                        m.nombre.trim().toLowerCase() === nombreModelo.trim().toLowerCase()
                    );

                    if (!modeloEncontrado) {
                        this.errorSerie = `Modelo "${nombreModelo}" no encontrado`;
                        return;
                    }

                    this.form.id_modelo = modeloEncontrado.id;
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];

                    try {
                        const [voltajes, colores] = await Promise.all([
                            fetch(`/voltaje-por-modelo/${modeloEncontrado.id}`).then(r => r.json()),
                            fetch(`/colores-por-modelo/${modeloEncontrado.id}`).then(r => r.json()),
                        ]);
                        
                        this.form.voltajes = voltajes || [];
                        this.form.colores = colores || [];

                        if (this.form.voltajes.length === 1) {
                            this.form.id_voltaje = this.form.voltajes[0].id_voltaje;
                        }
                        if (this.form.colores.length === 1) {
                            this.form.id_color = this.form.colores[0].id_color;
                        }
                    } catch (e) {
                        console.error(e);
                        this.errorSerie = 'Error cargando opciones';
                    }
                },

                agregarItem() {
                    this.errorSerie = '';

                    if (this.numSerie.length !== 17) {
                        this.errorSerie = 'Debe tener 17 caracteres';
                        return;
                    }
                    if (!this.form.id_modelo || !this.form.id_voltaje || !this.form.id_color) {
                        this.errorSerie = 'Selecciona voltaje y color';
                        return;
                    }

                    const serieExiste = this.items.some(i => i.series.includes(this.numSerie));
                    if (serieExiste) {
                        this.errorSerie = 'Esta serie ya fue agregada';
                        return;
                    }

                    const voltajeObj = this.form.voltajes.find(v => v.id_voltaje == this.form.id_voltaje);
                    const colorObj = this.form.colores.find(c => c.id_color == this.form.id_color);

                    const existing = this.items.find(i =>
                        i.id_modelo == this.form.id_modelo &&
                        i.id_voltaje == this.form.id_voltaje &&
                        i.id_color == this.form.id_color
                    );

                    const serie = this.numSerie;

                    if (existing) {
                        existing.series.push(serie);
                        this.items = [...this.items];
                    } else {
                        this.items.push({
                            id_modelo: this.form.id_modelo,
                            id_voltaje: this.form.id_voltaje,
                            id_color: this.form.id_color,
                            modelo_nombre: this.modeloDetectado,
                            voltaje_nombre: voltajeObj?.voltaje ?? '',
                            color_nombre: colorObj?.color ?? '',
                            lote: this.lote,
                            series: [serie],
                        });
                    }

                    this.numSerie = '';
                    this.modeloDetectado = '';
                    this.errorSerie = '';
                    this.form.id_modelo = '';
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];
                    
                    if (this.isMobile) {
                        this.showScannerMobile = false;
                    }
                    
                    this.focusInput();
                },

                quitarSerie(itemIndex, serieIndex) {
                    this.items[itemIndex].series.splice(serieIndex, 1);
                    if (this.items[itemIndex].series.length === 0) {
                        this.items.splice(itemIndex, 1);
                    }
                    this.items = [...this.items];
                },

                quitarItem(index) {
                    this.items.splice(index, 1);
                    this.items = [...this.items];
                },

                abrirModalItem(index) {
                    this.selectedItemIndex = index;
                    this.selectedItem = { ...this.items[index] };
                    this.showItemModal = true;
                },

                generarPdf() {
                    if (this.items.length === 0) return;
                    document.getElementById('formRapido').submit();
                },

            }));
        });
    </script>
    @endpush
</x-app-layout>