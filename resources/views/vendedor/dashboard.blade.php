<x-app-layout>
    <div
        x-data="{
            // Estado del modal y escáner
            ingresarModal: false,
            scanner: null,
            scanData: null,
            scanError: '',
            scannerActivo: false,
            manualSerie: '',
            cargando: false,          // Para mostrar spinner en botones
            mensajeExito: '',          // Para notificaciones sin alert

            // Abrir modal
            openIngresar() {
                this.ingresarModal = true;
                this.scanData = null;
                this.scanError = '';
                this.mensajeExito = '';
            },

            // Cerrar modal y detener escáner
            async closeIngresar() {
                this.ingresarModal = false;
                this.scanData = null;
                this.scanError = '';
                this.mensajeExito = '';
                await this.detenerScanner();
            },

            // Iniciar escáner QR
            async iniciarScanner() {
                if (!this.ingresarModal || this.scannerActivo) return;

                // Esperar a que el DOM esté actualizado (el contenedor #qr-reader debe existir)
                await this.$nextTick();

                if (typeof Html5Qrcode === 'undefined') {
                    this.scanError = 'No se cargó el lector QR. Recarga la página.';
                    return;
                }

                const contenedor = document.getElementById('qr-reader');
                if (!contenedor) {
                    this.scanError = 'Error interno: contenedor no encontrado.';
                    return;
                }

                this.scanner = new Html5Qrcode('qr-reader');
                this.scannerActivo = true;

                try {
                    await this.scanner.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        async (decodedText) => {
                            await this.buscarBicicleta(decodedText);
                        }
                    );
                } catch (error) {
                    console.error('Error al iniciar cámara:', error);
                    this.scanError = 'No se pudo activar la cámara. Asegúrate de dar permisos.';
                    this.scannerActivo = false;
                }
            },

            // Detener escáner
            async detenerScanner() {
                if (this.scanner && this.scannerActivo) {
                    try {
                        await this.scanner.stop();
                        await this.scanner.clear();
                    } catch (e) {
                        console.warn('Error al detener escáner:', e);
                    }
                }
                this.scanner = null;
                this.scannerActivo = false;
            },

            // Buscar bicicleta por número de serie (desde QR o manual)
            async buscarBicicleta(numSerie) {
                if (!numSerie || this.cargando) return;

                this.cargando = true;
                this.scanError = '';
                this.scanData = null;
                this.mensajeExito = '';

                // Detener escáner mientras se procesa la búsqueda
                await this.detenerScanner();

                try {
                    const url = `{{ url('/bicicletas/qrv') }}/${encodeURIComponent(numSerie)}`;
                    const res = await fetch(url);

                    if (!res.ok) {
                        const errorData = await res.json().catch(() => ({}));
                        throw new Error(errorData.message || 'Error al consultar la bicicleta');
                    }

                    const data = await res.json();

                    if (!data.ok) {
                        throw new Error(data.message || 'No se encontró la bicicleta');
                    }

                    // Datos correctos
                    this.scanData = data.bicicleta;
                    // Actualizar el campo oculto del formulario (si existe)
                    if (this.$refs.numSerieInput) {
                        this.$refs.numSerieInput.value = data.bicicleta.num_serie;
                    }

                } catch (error) {
                    console.error('Error en búsqueda:', error);
                    this.scanError = error.message;
                } finally {
                    this.cargando = false;
                }
            },

            // Asignar bicicleta al usuario actual
            async asignarBici() {
                if (!this.scanData || this.cargando) return;

                this.cargando = true;
                this.scanError = '';
                this.mensajeExito = '';

                try {
                    const res = await fetch('{{ route('bicicletas.asignarUsuario') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            num_serie: this.scanData.num_serie
                        })
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        // Si la respuesta no es JSON (ej. error HTML del servidor)
                        throw new Error(`Error ${res.status}: ${res.statusText}`);
                    }

                    if (!res.ok || !data.ok) {
                        throw new Error(data.message || 'Error al asignar bicicleta');
                    }

                    // Éxito
                    this.mensajeExito = 'Bicicleta asignada correctamente';
                    setTimeout(() => {
                        this.closeIngresar();
                        // Recargar la página para actualizar la tabla
                        location.reload();
                    }, 1500);

                } catch (error) {
                    console.error('Error en asignación:', error);
                    this.scanError = error.message;
                } finally {
                    this.cargando = false;
                }
            }
        }"
        x-init="
            // Inicializar efecto: al cambiar ingresarModal, inicia o detiene escáner
            $watch('ingresarModal', value => {
                if (value) {
                    this.iniciarScanner();
                } else {
                    this.detenerScanner();
                }
            });
        "
        class="space-y-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
                <p class="text-xs text-gray-400">Gestiona el inventario</p>
            </div>

            <button
                @click="openIngresar()"
                 class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
                Ingresar bicis
            </button>
        </div>

        {{-- ===== TABLA ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bicicletas registradas</h3>
                <span class="text-xs text-gray-400">{{ $bicicletas->total() }} total</span>
            </div>

            @php
            // Mapeo de estados: número BD => texto mostrado y clase de color
            $estados = [
                '1' => ['texto' => 'En Stock', 'color' => 'green'],
                '2' => ['texto' => 'Vendido', 'color' => 'purple'],
                '3' => ['texto' => 'Reparación', 'color' => 'yellow'],
                
               
            ];
            @endphp

            {{-- Vista PC --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">N° Serie</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Modelo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Voltaje</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Color</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @forelse($bicicletas as $bicicleta)
                        @php
                        $statusKey = $bicicleta->status;
                        $estado = $estados[$statusKey] ?? ['texto' => ucfirst(str_replace('_', ' ', $statusKey)), 'color' => 'red'];
                        $color = $estado['color'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->color->color ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($color)
                                            @case('green') bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 @break
                                            @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                            @case('purple') bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400 @break
                                            @default bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                        @endswitch
                                    ">
                                    {{ $estado['texto'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron bicicletas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Vista móvil --}}
            <div class="block md:hidden overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">N° Serie</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Voltaje</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($bicicletas as $bicicleta)
                        @php
                        $statusKey = $bicicleta->status;
                        $estado = $estados[$statusKey] ?? ['texto' => ucfirst(str_replace('_', ' ', $statusKey)), 'color' => 'red'];
                        $color = $estado['color'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-3 py-3">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</div>
                                <div class="text-xs text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-500">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-3 py-3 text-xs text-gray-500">{{ $bicicleta->color->color ?? '—' }}</td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                        @switch($color)
                                            @case('green') bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 @break
                                            @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                            @case('purple') bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400 @break
                                            @default bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                        @endswitch
                                    ">
                                    {{ $estado['texto'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $bicicleta->updated_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-gray-500 text-xs">No hay bicicletas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            

            

            @if($bicicletas->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $bicicletas->withQueryString()->links() }}
            </div>
            @endif
        </div>

        {{-- ===== MODAL FORMAL Y MINIMALISTA ===== --}}
<div
    x-show="ingresarModal"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 px-4"
    @click.self="closeIngresar()"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
>
    <div
        x-show="ingresarModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-3xl"
        @click.stop
    >
        {{-- Cabecera minimalista --}}
        <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center shrink-0 ">
                       <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13v4m0 0l-2-2m2 2l2-2" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ingresar Bicicleta</h3>
                </div>
            <button
                @click="closeIngresar()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1"
                aria-label="Cerrar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Contenido en grid --}}
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Columna izquierda: Lector QR --}}
            <div>
                <div id="qr-reader" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900"></div>

                <p x-show="scanError" x-text="scanError" class="text-red-600 mt-2 text-sm"></p>
                <p x-show="mensajeExito" x-text="mensajeExito" class="text-green-600 mt-2 text-sm"></p>

               
            </div>

            {{-- Columna derecha: Búsqueda manual y resultado --}}
            <div class="space-y-4">
                {{-- Búsqueda manual --}}
                <div>
                    <label for="manualSerie" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                         Numero de Serie <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input
                            id="manualSerie"
                            type="text"
                            x-model="manualSerie"
                            @keyup.enter="buscarBicicleta(manualSerie)"
                            class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                            placeholder="HE0EA2A00SA963753" 
                            :disabled="cargando"
                        >
                        <button
                            type="button"
                            @click="buscarBicicleta(manualSerie)"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition
                             :disabled="cargando"
                        >
                            <span x-show="!cargando">Buscar</span>
                            <span x-show="cargando" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Buscando</span>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Resultado de búsqueda --}}
                <template x-if="scanData">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-2 bg-gray-50 dark:bg-gray-900">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-900 dark:text-white">Serie:</span> <span x-text="scanData.num_serie"></span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-900 dark:text-white">Modelo:</span> <span x-text="scanData.modelo"></span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-900 dark:text-white">Voltaje:</span> <span x-text="scanData.voltaje"></span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-900 dark:text-white">Color:</span> <span x-text="scanData.color"></span></p>

                        <input type="hidden" x-ref="numSerieInput" :value="scanData.num_serie">

                        <button
                            type="button"
                            @click="asignarBici()"
                            class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-sm font-medium transition disabled:opacity-50 flex items-center justify-center"
                            :disabled="cargando"
                        >
                            <span x-show="!cargando">Asignar bicicleta</span>
                            <span x-show="cargando" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Asignando</span>
                            </span>
                        </button>
                    </div>
                </template>

                {{-- Mensaje por defecto --}}
                <template x-if="!scanData && !cargando">
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                        Escanea un código o ingresa un número de serie.
                    </p>
                </template>
            </div>
        </div>
    </div>
</div>

    </div>

    {{-- Scripts externos --}}
    <script src="https://unpkg.com/html5-qrcode" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>