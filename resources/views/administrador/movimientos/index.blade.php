<x-app-layout>
    <style>
        /* Animación más lenta: 1.2s */
        @keyframes slideInHighlight {
            0% {
                opacity: 0;
                transform: translateX(-8px);
                background-color: rgba(34, 197, 94, 0.3);
            }
            30% {
                opacity: 1;
                transform: translateX(0);
                background-color: rgba(34, 197, 94, 0.5);
            }
            100% {
                background-color: transparent;
            }
        }

        .animate-new-feed {
            animation: slideInHighlight 1.2s ease-in-out;
            border-left: 3px solid #10b981;
        }

        .animate-new-timeline {
            animation: slideInHighlight 1.2s ease-in-out;
            border-left: 3px solid #10b981;
        }

        /* Borde transparente por defecto para evitar saltos */
        .feed-item-enter, .timeline-item-enter {
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
    </style>

    <div class="mx-auto space-y-6" x-data="movimientosApp()" x-init="init()">

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Tracking de Movimientos</h2>
                <p class="text-xs text-gray-400 mt-0.5">Historial completo por número de serie</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    En vivo
                </span>
                <a href="{{ route('admin.movimientos.tabla') }}"
                   class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                          text-white px-3.5 py-2 rounded-lg text-xs font-semibold hover:opacity-90
                          active:scale-[0.98] transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
                    </svg>
                    Ver en tablas
                </a>
            </div>
        </div>

        {{-- ===== GRID PRINCIPAL ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- COLUMNA IZQUIERDA: Buscador + Timeline --}}
            <div class="lg:col-span-3 space-y-4">

                {{-- BUSCADOR CON BOTÓN LIMPIAR --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">
                        Buscar por número de serie
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="query"
                            @input.debounce.300ms="buscar"
                            @keydown.escape="cerrarSugerencias"
                            maxlength="17"
                            placeholder="Ej: SN-2024-0042..."
                            class="w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
                        />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>

                        <!-- Botón para limpiar búsqueda (X) -->
                        <button 
                            x-show="query.length > 0"
                            @click="limpiarBusqueda()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                            title="Limpiar búsqueda"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div x-show="cargando" class="absolute right-10 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Sugerencias autocomplete --}}
                    <div x-show="sugerencias.length > 0" x-cloak
                        class="mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
                        <template x-for="s in sugerencias" :key="s.num_serie">
                            <button
                                @click="seleccionarSerie(s.num_serie)"
                                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-between group"
                            >
                                <span class=" font-medium text-gray-800 dark:text-gray-200" x-text="s.num_serie"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': s.status === 1,
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': s.status === 2,
                                        'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': s.status === 3
                                    }"
                                    x-text="labelStatus(s.status)">
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- TIMELINE --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

                    {{-- Estado vacío --}}
                    <div x-show="!serieActiva && !cargandoHistorial" class="flex flex-col items-center justify-center py-16 text-center">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Busca un número de serie<br>para ver su historial</p>
                    </div>

                    {{-- Loading historial --}}
                    <div x-show="cargandoHistorial" class="flex items-center justify-center py-16">
                        <svg class="w-6 h-6 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>

                    {{-- Header bicicleta activa --}}
                    <div x-show="serieActiva && !cargandoHistorial" class="p-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide font-medium mb-1">Vehículo</p>
                                <p class=" text-lg font-semibold text-gray-900 dark:text-white" x-text="serieActiva"></p>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="bicicletaInfo.marca + ' · ' + bicicletaInfo.modelo"></span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500" x-text="bicicletaInfo.voltaje"></span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': bicicletaInfo.status === 1,
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': bicicletaInfo.status === 2,
                                            'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': bicicletaInfo.status === 3
                                        }"
                                        x-text="labelStatus(bicicletaInfo.status)">
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500" x-text="movimientos.length + ' movimientos'"></span>
                        </div>
                    </div>

                    {{-- Timeline items --}}
                    <div x-show="serieActiva && !cargandoHistorial" class="p-4 space-y-0">
                        <template x-for="(mov, i) in movimientos" :key="mov.id_movimiento ?? `temp-timeline-${i}`">
                            <div :class="{'animate-new-timeline': mov.animate}" class="relative flex gap-4 pb-6 last:pb-0 transition-all feed-item-enter">
                                {{-- Línea vertical --}}
                                <div x-show="i < movimientos.length - 1" class="absolute left-[19px] top-6 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                                {{-- Dot / Icono --}}
                                <div class="relative flex flex-col items-center z-10">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-white dark:bg-gray-800 border-2"
                                        :class="{
                                            'border-green-500': mov.tipo_movimiento === 'entrada_stock',
                                            'border-blue-500': mov.tipo_movimiento === 'transferencia_sucursal',
                                            'border-yellow-500': mov.tipo_movimiento === 'venta',
                                            'border-purple-500': mov.tipo_movimiento === 'mantenimiento',
                                            'border-gray-400': mov.tipo_movimiento === 'ajuste',
                                            'border-orange-500': mov.tipo_movimiento === 'ingreso_ot',
                                            'border-teal-500': mov.tipo_movimiento === 'entrega_ot'
                                        }">
                                        <div x-html="iconoTipoSVG(mov.tipo_movimiento)" class="w-5 h-5"></div>
                                    </div>
                                </div>

                                {{-- Contenido --}}
                                <div class="flex-1 pt-1.5 pb-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mb-1"
                                                :class="{
                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': mov.tipo_movimiento === 'entrada_stock',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': mov.tipo_movimiento === 'transferencia_sucursal',
                                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': mov.tipo_movimiento === 'venta',
                                                    'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': mov.tipo_movimiento === 'mantenimiento',
                                                    'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300': mov.tipo_movimiento === 'ajuste',
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400': mov.tipo_movimiento === 'ingreso_ot',
                                                    'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400': mov.tipo_movimiento === 'entrega_ot'
                                                }"
                                                x-text="labelTipo(mov.tipo_movimiento)">
                                            </span>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                <span x-show="mov.origen" x-text="mov.origen"></span>
                                                <span x-show="mov.origen && mov.destino" class="text-gray-400 dark:text-gray-500 mx-1">→</span>
                                                <span x-show="mov.destino" x-text="mov.destino"></span>
                                            </p>
                                            <p x-show="mov.notas" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="mov.notas"></p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400" x-text="formatFecha(mov.fecha_movimiento)"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5" x-text="mov.usuario ?? 'Sistema'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Feed en vivo --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm sticky top-6">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Actividad reciente</h2>
                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="feedItems.length + ' eventos'"></span>
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-700 max-h-[calc(100vh-220px)] overflow-y-auto">
                        <template x-for="(item, i) in feedItems" :key="item.id_movimiento ?? `temp-feed-${i}`">
                            <div :class="{'animate-new-feed': item.animate}"
                                 class="feed-item-enter px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                                 @click="seleccionarSerie(item.num_serie)">
                                <div class="flex items-start gap-3">
                                    <div x-html="iconoTipoSVG(item.tipo_movimiento)" class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="item.num_serie"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            <span x-text="labelTipo(item.tipo_movimiento)"></span>
                                            <span x-show="item.destino" class="text-gray-400 dark:text-gray-500"> · <span x-text="item.destino"></span></span>
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0" x-text="item.relativo"></span>
                                </div>
                            </div>
                        </template>

                        <div x-show="feedItems.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                            <p class="text-sm text-gray-400 dark:text-gray-500">Sin actividad reciente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function movimientosApp() {
            return {
                query:            '',
                sugerencias:      [],
                cargando:         false,
                serieActiva:      null,
                bicicletaInfo:    {},
                movimientos:      [],
                cargandoHistorial:false,
                feedItems:        [],
                wsConectado:      false,
                _canal:           null,
                _interval:        null,

                init() {
                    // Inicializar feedItems con relativo y sin animación
                    this.feedItems = @json($recientes ?? []).map(item => ({
                        ...item,
                        id_movimiento: item.id_movimiento ?? item.id ?? crypto.randomUUID(),
                        animate: false,
                        relativo: this.formatRelativo(item.fecha_movimiento)
                    }));

                    this.conectarEcho();
                    this.iniciarActualizacionTiempos();

                    const params = new URLSearchParams(window.location.search);
                    const serie  = params.get('serie');
                    if (serie) this.seleccionarSerie(serie);

                    // Limpieza al destruir el componente (cuando se navega fuera)
                    window.addEventListener('beforeunload', () => {
                        if (this._interval) clearInterval(this._interval);
                        if (this._canal && window.Echo) {
                            window.Echo.leaveChannel(`movimientos.{{ auth()->user()->id_negocio }}`);
                        }
                    });
                },

                limpiarBusqueda() {
                    this.query = '';
                    this.sugerencias = [];
                    this.serieActiva = null;
                    this.movimientos = [];
                    this.bicicletaInfo = {};
                    history.pushState({}, '', window.location.pathname); // Limpiar URL
                },

                iniciarActualizacionTiempos() {
                    this._interval = setInterval(() => {
                        this.feedItems = this.feedItems.map(item => ({
                            ...item,
                            relativo: this.formatRelativo(item.fecha_movimiento)
                        }));
                    }, 10000);
                },

                conectarEcho() {
                    if (!window.Echo) return;

                    if (this._canal) {
                        window.Echo.leaveChannel(`movimientos.{{ auth()->user()->id_negocio }}`);
                    }

                    this._canal = window.Echo.channel(`movimientos.{{ auth()->user()->id_negocio }}`);

                    this._canal.listen('.movimiento.nuevo', (e) => {
                        const nuevoMov = {
                            ...e,
                            id_movimiento: e.id_movimiento ?? e.id ?? Date.now() + Math.random(),
                            animate: true,
                            relativo: this.formatRelativo(e.fecha_movimiento)
                        };

                        // Evitar duplicados en feedItems
                        const yaExisteFeed = this.feedItems.some(item => item.id_movimiento === nuevoMov.id_movimiento);
                        if (!yaExisteFeed) {
                            this.feedItems.unshift(nuevoMov);
                            if (this.feedItems.length > 50) this.feedItems.pop();

                            // 🔧 FIX: eliminar animación usando id_movimiento (no índice)
                            setTimeout(() => {
                                const item = this.feedItems.find(i => i.id_movimiento === nuevoMov.id_movimiento);
                                if (item) item.animate = false;
                            }, 1200); // Coincide con la nueva duración de la animación
                        }

                        // Evitar duplicados en timeline y agregar si es la serie activa
                        if (this.serieActiva === nuevoMov.num_serie) {
                            const yaExisteTimeline = this.movimientos.some(mov => mov.id_movimiento === nuevoMov.id_movimiento);
                            if (!yaExisteTimeline) {
                                this.movimientos.push({ ...nuevoMov, animate: true });
                                // 🔧 FIX: eliminar animación usando id_movimiento
                                setTimeout(() => {
                                    const mov = this.movimientos.find(m => m.id_movimiento === nuevoMov.id_movimiento);
                                    if (mov) mov.animate = false;
                                }, 1200);
                            }
                        }
                    });
                },

                async buscar() {
                    if (this.query.length < 2) { this.sugerencias = []; return; }
                    this.cargando = true;
                    try {
                        const res = await fetch(`{{ route('admin.movimientos.buscar') }}?q=${encodeURIComponent(this.query)}`, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        this.sugerencias = await res.json();
                    } finally {
                        this.cargando = false;
                    }
                },

                async seleccionarSerie(numSerie) {
                    // Si ya está seleccionada la misma, no hacer nada
                    if (this.serieActiva === numSerie) return;

                    this.serieActiva       = numSerie;
                    this.query             = numSerie;
                    this.sugerencias       = [];
                    this.cargandoHistorial = true;
                    history.pushState({}, '', `?serie=${numSerie}`);

                    try {
                        const res  = await fetch(`{{ url('admin/movimientos/historial') }}/${encodeURIComponent(numSerie)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await res.json();
                        this.movimientos = (data.movimientos || []).map(m => ({
                            ...m,
                            id_movimiento: m.id_movimiento ?? m.id ?? crypto.randomUUID(),
                            animate: false
                        }));
                        this.bicicletaInfo = {
                            marca:   data.bicicleta.marca?.nombre_marca  ?? '—',
                            modelo:  data.bicicleta.modelo?.nombre_modelo ?? '—',
                            voltaje: data.bicicleta.voltaje?.voltaje      ?? '—',
                            status:  data.bicicleta.status,
                        };

                        // 🔧 FIX NOTABLE: agregar al timeline los movimientos de esta serie que ya estén en feedItems (y no existan aún)
                        const movimientosFeedDeEstaSerie = this.feedItems.filter(f => f.num_serie === numSerie);
                        for (let feedMov of movimientosFeedDeEstaSerie) {
                            const yaExiste = this.movimientos.some(m => m.id_movimiento === feedMov.id_movimiento);
                            if (!yaExiste) {
                                this.movimientos.push({
                                    ...feedMov,
                                    animate: false  // sin animación porque ya estaban en el feed
                                });
                            }
                        }
                        // Ordenar cronológicamente (más reciente al final o principio? El timeline usa orden ascendente? En la vista se muestran en orden de llegada, pero conviene ordenar por fecha)
                        this.movimientos.sort((a, b) => new Date(a.fecha_movimiento) - new Date(b.fecha_movimiento));

                    } finally {
                        this.cargandoHistorial = false;
                    }
                },

                cerrarSugerencias() { this.sugerencias = []; },

                labelTipo(tipo) {
                    const m = {
                        entrada_stock:          'Entrada stock',
                        transferencia_sucursal: 'Transferencia',
                        venta:                  'Venta',
                        mantenimiento:          'Mantenimiento',
                        ajuste:                 'Ajuste',
                        ingreso_ot:             'Ingreso a taller',
                        entrega_ot:             'Entrega a cliente',
                    };
                    return m[tipo] ?? tipo;
                },

                iconoTipoSVG(tipo) {
                    const svgs = {
                        entrada_stock: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>`,
                        transferencia_sucursal: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>`,
                        venta: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 21h6M12 17v4" /></svg>`,
                        mantenimiento: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`,
                        ajuste: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>`,
                        ingreso_ot: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>`,
                        entrega_ot: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
                    };
                    return svgs[tipo] || `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/></svg>`;
                },

                labelStatus(status) {
                    const m = { 1: 'En stock', 2: 'Vendida', 3: 'En reparación' };
                    return m[status] ?? status;
                },

                formatFecha(fecha) {
                    return new Date(fecha).toLocaleDateString('es-MX', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                },

                formatRelativo(fecha) {
                    const diff = Math.floor((Date.now() - new Date(fecha)) / 1000);
                    if (diff < 60)   return 'ahora';
                    if (diff < 3600) return Math.floor(diff/60) + 'm';
                    if (diff < 86400)return Math.floor(diff/3600) + 'h';
                    return Math.floor(diff/86400) + 'd';
                },
            };
        }
    </script>
</x-app-layout>