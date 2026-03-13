<x-app-layout>
    <style>
        /* Tipografía refinada */
        .rapido-root { font-family: 'IBM Plex Sans', sans-serif; }
        .mono { font-family: 'IBM Plex Mono', monospace !important; letter-spacing: -0.01em; }
        [x-cloak] { display: none !important; }
        
        /* Estilos AWS */
        .aws-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #eaecf0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02), 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.2s ease;
        }
        
        .dark .aws-card {
            background-color: #1e293b;
            border-color: #2d3a4f;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .aws-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03), 0 2px 4px rgba(0, 0, 0, 0.04);
        }
        
        .dark .aws-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }
        
        .aws-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #5f6b7a;
        }
        
        .dark .aws-label {
            color: #9aa6b5;
        }
        
        .aws-input, .aws-select {
            border: 1px solid #d0d9e8;
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            background-color: white;
            width: 100%;
        }
        
        .dark .aws-input, .dark .aws-select {
            background-color: #1e293b;
            border-color: #3a4a62;
            color: #f0f4fa;
        }
        
        .aws-input:focus, .aws-select:focus {
            outline: none;
            border-color: #0073bb;
            box-shadow: 0 0 0 3px rgba(0, 115, 187, 0.15);
        }
        
        .aws-button-primary {
            background-color: #0073bb;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        
        .aws-button-primary:hover:not(:disabled) {
            background-color: #1a7fc1;
            transform: translateY(-1px);
        }
        
        .aws-button-primary:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .aws-button-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        
        .aws-button-secondary {
            background-color: white;
            color: #1e2a3a;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid #d0d9e8;
            transition: all 0.15s ease;
        }
        
        .dark .aws-button-secondary {
            background-color: #2d3748;
            color: #e2e8f0;
            border-color: #4a5a72;
        }
        
        .aws-button-danger {
            background-color: #d13212;
            color: white;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
        }
        
        .aws-button-danger:hover:not(:disabled) {
            background-color: #bc2d0f;
        }
        
        .series-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background-color: #f2f6fc;
            border: 1px solid #d9e2ef;
            border-radius: 0.375rem;
            padding: 0.25rem 0.625rem;
            font-size: 0.75rem;
            font-family: 'IBM Plex Mono', monospace;
            color: #1e2a3a;
            transition: all 0.15s ease;
        }
        
        .dark .series-chip {
            background-color: #25344a;
            border-color: #3e516b;
            color: #cfddee;
        }
        
        .series-chip:hover {
            background-color: #e9f0fa;
            border-color: #b8c9e0;
        }
        
        .aws-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        
        .aws-table th {
            padding: 0.875rem 0.5rem 0.875rem 0;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #5f6b7a;
            border-bottom: 1px solid #e4e9f0;
            text-align: left;
        }
        
        .dark .aws-table th {
            color: #9aa6b5;
            border-bottom-color: #2d3a4f;
        }
        
        .aws-table td {
            padding: 0.875rem 0.5rem 0.875rem 0;
            border-bottom: 1px solid #f0f4fa;
            color: #1e2a3a;
        }
        
        .dark .aws-table td {
            border-bottom-color: #253040;
            color: #e8edf5;
        }
        
        .aws-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Animación de entrada suave */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-up {
            animation: slideUp 0.3s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        
        /* Barra de escaneo animada */
        @keyframes scanPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 115, 187, 0.3); }
            50% { box-shadow: 0 0 0 4px rgba(0, 115, 187, 0); }
        }
        
        .scan-pulse {
            animation: scanPulse 2s infinite;
        }
    </style>

    <div
        x-data="pedidoRapido({{ Js::from($modelos->map(fn($m) => ['id' => $m->id_modelo, 'nombre' => $m->nombre_modelo])) }})"
        class="rapido-root space-y-6 max-w-5xl mx-auto py-6 px-4 sm:px-6">

        {{-- Header refinado --}}
        <div class="animate-slide-up">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#0073bb] to-[#004d80] flex items-center justify-center shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Emisión Rápida</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Genera un PDF de emisión sin registrar en el sistema
                    </p>
                </div>
            </div>
        </div>

        <form id="formRapido" method="POST" action="{{ route('pedidos.rapido.pdf') }}" target="_blank" class="space-y-6">
            @csrf

            {{-- Datos generales --}}
            <div class="aws-card p-6 animate-slide-up" style="animation-delay: 0.05s">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1 h-5 bg-[#0073bb] rounded-full"></div>
                    <span class="aws-label">Datos del Formulario</span>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Fecha</label>
                        <input type="text" name="fecha" x-model="fechaHoy"
                            class="aws-input text-sm bg-gray-50 dark:bg-gray-800/50 font-mono" readonly>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Cliente</label>
                        <input type="text" name="cliente" placeholder="Nombre del cliente"
                            class="aws-input text-sm" autocomplete="off">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Distancia</label>
                        <input type="text" name="distancia" placeholder="—"
                            class="aws-input text-sm" autocomplete="off">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Transporte</label>
                        <input type="text" name="transporte" placeholder="Recoge en fábrica"
                            class="aws-input text-sm" autocomplete="off">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Costo envío</label>
                        <input type="text" name="costo_envio" placeholder="$"
                            class="aws-input text-sm" autocomplete="off">
                    </div>
                </div>
            </div>

            {{-- Scanner --}}
            <div class="aws-card p-6 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1 h-5 bg-[#0073bb] rounded-full"></div>
                    <span class="aws-label">Escanear Bicicleta</span>
                </div>

                <div class="space-y-4">
                    {{-- Número de serie con diseño mejorado --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                            Número de serie
                            <span class="text-gray-400 ml-1 font-mono text-[10px]">(17 caracteres)</span>
                        </label>
                        <div class="relative group">
                            <input
                                x-ref="serieInput"
                                type="text"
                                x-model="numSerie"
                                @input="onSerieInput()"
                                @keydown.enter.prevent="agregarItem()"
                                maxlength="17"
                                placeholder="Escribe o escanea el código..."
                                autocomplete="off"
                                class="aws-input pl-4 pr-24 py-3 font-mono tracking-wider text-base"
                                :class="{ 'border-blue-400 ring-1 ring-blue-200 dark:ring-blue-900': numSerie.length === 17 }">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                <span class="text-xs font-mono text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700"
                                      x-text="numSerie.length + '/17'"></span>
                                <button type="button"
                                    @click="numSerie = ''; modeloDetectado = ''; errorSerie = ''; form.id_voltaje = ''; form.id_color = ''; form.voltajes = []; form.colores = []; $refs.serieInput.focus()"
                                    x-show="numSerie.length > 0"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        {{-- Feedback de detección --}}
                        <div x-show="modeloDetectado || errorSerie" x-cloak class="mt-2 flex items-center gap-2">
                            <template x-if="modeloDetectado">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    <span class="text-gray-600 dark:text-gray-400">Modelo detectado:</span>
                                    <span class="font-semibold text-[#0073bb] dark:text-blue-400" x-text="modeloDetectado"></span>
                                </div>
                            </template>
                            <template x-if="errorSerie">
                                <div class="flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="errorSerie"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Voltaje y Color --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Voltaje</label>
                            <select x-model="form.id_voltaje" :disabled="!form.voltajes.length"
                                class="aws-select text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">— Seleccionar voltaje —</option>
                                <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                    <option :value="v.id_voltaje" x-text="v.voltaje" :selected="form.voltajes.length === 1"></option>
                                </template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Color</label>
                            <select x-model="form.id_color" :disabled="!form.colores.length"
                                class="aws-select text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">— Seleccionar color —</option>
                                <template x-for="c in form.colores" :key="c.id_color">
                                    <option :value="c.id_color" x-text="c.color" :selected="form.colores.length === 1"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <button type="button" @click="agregarItem()"
                                :disabled="numSerie.length !== 17 || !form.id_modelo || !form.id_voltaje || !form.id_color"
                                class="aws-button-primary w-full flex items-center justify-center gap-2 scan-pulse"
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

            {{-- Tabla de items --}}
            <div class="aws-card p-6 animate-slide-up" style="animation-delay: 0.15s">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-5 bg-[#0073bb] rounded-full"></div>
                        <span class="aws-label">Artículos escaneados</span>
                    </div>
                    <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600"
                          x-text="items.reduce((s, i) => s + i.series.length, 0) + ' unidades'"></span>
                </div>

                <div x-show="items.length === 0" x-cloak class="py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Escanea una bicicleta para comenzar</p>
                </div>

                <div x-show="items.length > 0" x-cloak class="overflow-x-auto -mx-6 px-6">
                    <table class="aws-table">
                        <thead>
                            <tr>
                                <th class="pl-0">Modelo</th>
                                <th>Voltaje</th>
                                <th>Color</th>
                                <th class="text-center">Cant.</th>
                                <th>Series</th>
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
                                    <template x-for="(serie, si) in item.series" :key="si">
                                        <input type="hidden" :name="`items[${index}][series][${si}]`" :value="serie">
                                    </template>

                                    <td class="font-medium text-gray-900 dark:text-white" x-text="item.modelo_nombre"></td>
                                    <td class="font-mono text-gray-600 dark:text-gray-400" x-text="item.voltaje_nombre"></td>
                                    <td class="text-gray-600 dark:text-gray-400" x-text="item.color_nombre"></td>
                                    <td class="text-center font-mono font-semibold text-gray-900 dark:text-white" x-text="item.series.length"></td>
                                    <td>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="(serie, si) in item.series" :key="si">
                                                <div class="series-chip group">
                                                    <span class="font-mono" x-text="serie"></span>
                                                    <button type="button" @click="quitarSerie(index, si)"
                                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="quitarItem(index)"
                                            class="text-xs font-medium text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                                            Quitar todo
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botón generar PDF --}}
            <div class="flex justify-end animate-slide-up" style="animation-delay: 0.2s">
                <button type="button" @click="generarPdf()"
                    :disabled="items.length === 0"
                    class="aws-button-danger inline-flex items-center gap-2 px-8 py-3 text-base shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Generar PDF de Emisión</span>
                </button>
            </div>

        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pedidoRapido', (catalogoModelos) => ({

                catalogoModelos: catalogoModelos,
                items: [],
                numSerie: '',
                modeloDetectado: '',
                errorSerie: '',

                form: {
                    id_modelo: '',
                    id_voltaje: '',
                    id_color: '',
                    voltajes: [],
                    colores: [],
                    voltaje_nombre: '',
                    color_nombre: '',
                },

                fechaHoy: new Date().toLocaleDateString('es-MX', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                }).replace(/\//g, '-'),

                mapaModelos: {
                    '14': 'Zeus', '05': 'Galaxy', '03': 'Primavera', '19': 'Reina',
                    '09': 'VmpS5', '06': 'Rayo', '11': 'Polar', '24': 'Urbex',
                    '18': 'Eclipce', '07': 'Aguila', '08': 'Sol', '16': 'Sol Pro',
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
                            this.errorSerie = `Código de modelo no reconocido: ${codigo}`;
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
                        this.errorSerie = 'El número de serie debe tener 17 caracteres';
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

                    if (existing) {
                        existing.series.push(this.numSerie);
                        this.items = [...this.items];
                    } else {
                        this.items.push({
                            id_modelo: this.form.id_modelo,
                            id_voltaje: this.form.id_voltaje,
                            id_color: this.form.id_color,
                            modelo_nombre: this.modeloDetectado,
                            voltaje_nombre: voltajeObj?.voltaje ?? '',
                            color_nombre: colorObj?.color ?? '',
                            series: [this.numSerie],
                        });
                    }

                    // Reset form
                    this.numSerie = '';
                    this.modeloDetectado = '';
                    this.errorSerie = '';
                    this.form.id_modelo = '';
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];
                    this.$nextTick(() => this.$refs.serieInput.focus());
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

                generarPdf() {
                    if (this.items.length === 0) return;
                    document.getElementById('formRapido').submit();
                },

            }));
        });
    </script>
    @endpush
</x-app-layout>