<x-app-layout>
    @php
    // Función auxiliar para parsear el formato "Nombre|hex1/hex2"
    function parsearColor($colorStr) {
    if (!$colorStr) return ['nombre' => '—', 'hexes' => ['#cccccc']];
    $parts = explode('|', $colorStr, 2);
    $nombre = trim($parts[0] ?? $colorStr);
    $hexPart = $parts[1] ?? '';
    $hexes = $hexPart ? explode('/', $hexPart) : ['#cccccc'];
    return ['nombre' => $nombre, 'hexes' => $hexes];
    }
    @endphp

    <div
        x-data="{
        ingresarModal: false,
        scanner: null,
        scanData: null,
        scanError: '',
        scannerActivo: false,
        manualSerie: '',
        cargando: false,
        mensajeExito: '',

        openIngresar() {
            this.ingresarModal = true;
            this.scanData = null;
            this.scanError = '';
            this.mensajeExito = '';
        },

        async closeIngresar() {
            this.ingresarModal = false;
            this.scanData = null;
            this.scanError = '';
            this.mensajeExito = '';
            await this.detenerScanner();
        },

        async iniciarScanner() {
            if (!this.ingresarModal || this.scannerActivo) return;
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

        async buscarBicicleta(numSerie) {
            if (!numSerie || this.cargando) return;
            this.cargando = true;
            this.scanError = '';
            this.scanData = null;
            this.mensajeExito = '';
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
                this.scanData = data.bicicleta;
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
                    body: JSON.stringify({ num_serie: this.scanData.num_serie })
                });
                let data;
                try {
                    data = await res.json();
                } catch (e) {
                    throw new Error(`Error ${res.status}: ${res.statusText}`);
                }
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Error al asignar bicicleta');
                }
                this.mensajeExito = 'Bicicleta asignada correctamente';
                setTimeout(() => {
                    this.closeIngresar();
                    location.reload();
                }, 1500);
            } catch (error) {
                console.error('Error en asignación:', error);
                this.scanError = error.message;
            } finally {
                this.cargando = false;
            }
        },

        // Extrae solo el nombre del color para el modal
        nombreColor(colorStr) {
            if (!colorStr) return '—';
            const partes = colorStr.split('|', 2);
            return partes[0] || colorStr;
        }
    }"
        x-init="
        $watch('ingresarModal', value => {
            if (value) {
                this.iniciarScanner();
            } else {
                this.detenerScanner();
            }
        });
    "
        class="space-y-6">

        {{-- ===== ENCABEZADO (responsivo, como catálogo) ===== --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Gestiona el inventario</p>
            </div>
            <div class="flex flex-col items-end gap-2 sm:flex-row-reverse sm:items-center">
                <button @click="openIngresar()"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap hover:scale-105 transform duration-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Ingresar bicis
                </button>
            </div>
        </div>

        {{-- ===== TABLA DE BICICLETAS ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bicicletas registradas</h3>
                <span class="text-xs text-gray-400">{{ $bicicletas->total() }} total</span>
            </div>

            @php
            $estados = [
            '1' => ['texto' => 'En Stock', 'color' => 'green'],
            '2' => ['texto' => 'Vendido', 'color' => 'purple'],
            '3' => ['texto' => 'Reparación', 'color' => 'yellow'],
            ];
            @endphp

            {{-- Vista escritorio --}}
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
                        $colorInfo = parsearColor($bicicleta->color->color ?? '');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    @if(count($colorInfo['hexes']) >= 2)
                                    <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                        <span class="absolute left-0 top-0 w-1/2 h-full" style="background: {{ $colorInfo['hexes'][0] }}"></span>
                                        <span class="absolute right-0 top-0 w-1/2 h-full" style="background: {{ $colorInfo['hexes'][1] }}"></span>
                                    </span>
                                    @else
                                    <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block" style="background: {{ $colorInfo['hexes'][0] }}"></span>
                                    @endif
                                    <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $colorInfo['nombre'] }}</span>
                                </div>
                            </td>
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
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
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
                        $colorInfo = parsearColor($bicicleta->color->color ?? '');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-3 py-3">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</div>
                                <div class="text-xs text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-500">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-1.5">
                                    @if(count($colorInfo['hexes']) >= 2)
                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                        <span class="absolute left-0 top-0 w-1/2 h-full" style="background: {{ $colorInfo['hexes'][0] }}"></span>
                                        <span class="absolute right-0 top-0 w-1/2 h-full" style="background: {{ $colorInfo['hexes'][1] }}"></span>
                                    </span>
                                    @else
                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block" style="background: {{ $colorInfo['hexes'][0] }}"></span>
                                    @endif
                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $colorInfo['nombre'] }}</span>
                                </div>
                            </td>
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

        {{-- ===== MODAL MINIMALISTA CON COLOR LEGIBLE ===== --}}
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
            @click.self="closeIngresar()">
            <div
                x-show="ingresarModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl"
                @click.stop>
                {{-- Cabecera simple --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Ingresar bicicleta</h3>
                    <button @click="closeIngresar()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Contenido en dos columnas --}}
                <div class="p-5">
                    <div class="grid md:grid-cols-2 gap-5">
                        {{-- Lector QR --}}
                        <div>
                            <div id="qr-reader" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 p-1"></div>
                            <p class="text-center text-xs text-gray-400 mt-2">Apunta al código QR</p>
                        </div>

                        {{-- Búsqueda manual y resultado --}}
                        <div class="space-y-4">
                            {{-- Campo con límite de 17 caracteres y contador --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Número de serie</label>
                                <div class="flex gap-2">
                                    <input type="text"
                                        x-model="manualSerie"
                                        @input="manualSerie = manualSerie.toUpperCase()"
                                        @keyup.enter="if (manualSerie.length === 17 && !cargando) buscarBicicleta(manualSerie)"
                                        maxlength="17"
                                        class="flex-1 font-mono border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300"
                                        placeholder="HE0EA2A00SA963753">
                                    <button @click="buscarBicicleta(manualSerie)"
                                        :disabled="cargando || manualSerie.length !== 17"
                                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                        <span x-text="cargando ? '...' : 'Buscar'"></span>
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-[11px] text-gray-400">Debe tener 17 caracteres</p>
                                    <p class="text-[11px]  text-gray-400">
                                        <span x-text="manualSerie.length"></span>/17
                                    </p>
                                </div>
                            </div>

                            {{-- Mensajes de error/éxito --}}
                            <div x-show="scanError" class="text-center text-red-800 dark:text-red-400 text-sm" x-text="scanError"></div>
                            <div x-show="mensajeExito" class="text-center text-green-800 dark:text-green-400 text-sm" x-text="mensajeExito"></div>


                            {{-- Resultado de la búsqueda --}}
                            <template x-if="scanData">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 space-y-2">
                                    <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Serie:</span>
                                        <span class="font-mono text-gray-900 dark:text-white" x-text="scanData.num_serie"></span>

                                        <span class="text-gray-600 dark:text-gray-400">Modelo:</span>
                                        <span class="text-gray-900 dark:text-white" x-text="scanData.modelo"></span>

                                        <span class="text-gray-600 dark:text-gray-400">Voltaje:</span>
                                        <span class="text-gray-900 dark:text-white" x-text="scanData.voltaje"></span>

                                        <span class="text-gray-600 dark:text-gray-400">Color:</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10"
                                                :style="'background:' + (scanData.color.split('|')[1]?.split('/')[0] || '#cccccc')"></span>
                                            <span class="text-gray-900 dark:text-white" x-text="nombreColor(scanData.color)"></span>
                                        </div>
                                    </div>
                                    <input type="hidden" x-ref="numSerieInput" :value="scanData.num_serie">
                                    <button @click="asignarBici()"
                                        :disabled="cargando"
                                        class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white py-1.5 rounded-lg text-sm font-medium transition disabled:opacity-50">
                                        <span x-text="cargando ? 'Asignando...' : 'Asignar bicicleta'"></span>
                                    </button>
                                </div>
                            </template>

                            {{-- Mensaje por defecto --}}
                            <template x-if="!scanData && !cargando">
                                <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">
                                    Escanea un código QR<br>o escribe el número de serie
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Pie simplificado --}}
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button @click="closeIngresar()"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" defer></script>

</x-app-layout>