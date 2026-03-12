<x-app-layout>
<div
    x-data="{
        deleteModal: false,
        deleteId: null,
        deleteNombre: '',
        deleteAction: '',
        createModal: false,
        openDelete(id, nombre, action) {
            this.deleteId     = id;
            this.deleteNombre = nombre;
            this.deleteAction = action;
            this.deleteModal  = true;
        },
        openCreate() {
            this.createModal = true;
        },
        closeCreate() {
            this.createModal = false;
        }
    }"
    class="space-y-6"
>
    {{-- ===== MENSAJE FLASH ===== --}}
   
        <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona el inventario de bicicletas</p>
        </div>
        <button @click="openCreate()"
           class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
            + Nueva Bicicleta
        </button>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $bicicletas->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $bicicletas->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $bicicletas->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $bicicletas->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== FILTROS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
        <form method="GET" action="{{ route('bicicletas.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="N° Serie o Status"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                    Filtrar
                </button>
                <a href="{{ route('bicicletas.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bicicletas registradas</h3>
            <span class="text-xs text-gray-400">{{ $bicicletas->total() }} total</span>
        </div>

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
                       
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->color->color ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($bicicleta->status == 'disponible') bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400
                                    @elseif($bicicleta->status == 'en_mantenimiento') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400
                                    @elseif($bicicleta->status == 'prestado') bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400
                                    @else bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bicicleta->status)) }}
                                </span>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron bicicletas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista móvil --}}
        <div class="block md:hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serie</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                       
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($bicicletas as $bicicleta)
                     
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-3 py-3">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</div>
                                <div class="text-xs text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($bicicleta->status == 'disponible') bg-green-100 text-green-800
                                    @elseif($bicicleta->status == 'en_mantenimiento') bg-yellow-100 text-yellow-800
                                    @elseif($bicicleta->status == 'prestado') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bicicleta->status)) }}
                                </span>
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-8 text-center text-gray-500 text-xs">No hay bicicletas</td>
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

    {{-- ===== MODAL CREAR BICICLETA ===== --}}
    <div x-show="createModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4 py-6 overflow-y-auto"
        @click.self="closeCreate()">
        <div x-show="createModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl my-auto" @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva Bicicleta</h3>
                <button @click="closeCreate()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            @if($errors->any())
            <div class="mx-6 mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 rounded-lg">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-red-600 dark:text-red-400 text-sm flex items-center gap-1">
                            <span class="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="p-6">
                <div class="mb-5 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <span class="font-semibold">Negocio:</span> {{ $negocio->id_negocio }}
                    </p>
                </div>

                <form id="formBicicleta"
                      action="{{ route('gestor.vehiculos.bicicletas.store') }}"
                      method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Número de Serie (QR) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="num_serie" id="num_serie" required maxlength="17"
                                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm bg-gray-100 cursor-not-allowed focus:outline-none"
                                   placeholder="Escanea el código QR" >
                            <button type="button" id="btnEscanearQR"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                                📷 Escanear QR
                            </button>
                        </div>
                        <p id="qr-error" class="text-red-500 text-xs mt-1 hidden">
                            El código QR debe tener exactamente 17 caracteres.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Modelo <span class="text-red-500">*</span>
                            </label>
                            <select name="id_modelo" id="id_modelo" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione un modelo</option>
                                @foreach($modelos as $modelo)
                                    <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Voltaje <span class="text-red-500">*</span>
                            </label>
                            <select name="id_voltaje" id="id_voltaje" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Primero seleccione un modelo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <select name="id_color" id="id_color" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Primero seleccione un modelo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <button type="button" @click="closeCreate()"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                            Guardar Bicicleta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== MODAL ELIMINAR ===== --}}
    

</div>

{{-- ===== SCRIPTS (dentro del x-app-layout) ===== --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    
    // Configuración
    const CONFIG = {
        TIMEOUT: 5000,
        CACHE_TTL: 300000, // 5 minutos
        DEBOUNCE_WAIT: 300
    };

    // Cache singleton
    const Cache = {
        data: new Map(),
        
        get(key) {
            const item = this.data.get(key);
            if (!item) return null;
            if (Date.now() - item.timestamp > CONFIG.CACHE_TTL) {
                this.data.delete(key);
                return null;
            }
            return item.data;
        },
        
        set(key, data) {
            this.data.set(key, {
                data: data,
                timestamp: Date.now()
            });
        },
        
        clear() {
            this.data.clear();
        }
    };

    // Utility functions
    const utils = {
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        async fetchWithTimeout(url, options = {}) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);
            
            try {
                const response = await fetch(url, {
                    ...options,
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
                
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return await response.json();
            } catch (error) {
                clearTimeout(timeoutId);
                throw error;
            }
        },
        
        generateOptions(data, defaultText = 'Seleccione una opción') {
            if (!data || data.length === 0) {
                return '<option value="" disabled>No hay opciones disponibles</option>';
            }
            
            const valueKey = data[0].id_voltaje ? 'id_voltaje' : 'id_color';
            const textKey = data[0].voltaje ? 'voltaje' : 'color';
            
            return `<option value="">${defaultText}</option>` + 
                   data.map(item => `<option value="${item[valueKey]}">${item[textKey]}</option>`).join('');
        }
    };

    // Elementos DOM
    const elementos = {
        modelo: document.getElementById('id_modelo'),
        voltaje: document.getElementById('id_voltaje'),
        color: document.getElementById('id_color'),
        qrBtn: document.getElementById('btnEscanearQR'),
        qrInput: document.getElementById('num_serie'),
        qrError: document.getElementById('qr-error'),
        form: document.getElementById('formBicicleta')
    };

    // Validar elementos necesarios
    if (!elementos.modelo || !elementos.voltaje || !elementos.color) {
        console.error('Elementos necesarios no encontrados');
        return;
    }

    // QR Scanner
    class QRScanner {
        constructor(elementos) {
            this.elementos = elementos;
            this.scanner = null;
            this.modal = null;
            this.cerrado = false;
        }
        
        async iniciar() {
            this.cerrado = false;
            this.elementos.qrError?.classList.add('hidden');
            
            this.modal = this.crearModal();
            document.body.appendChild(this.modal);
            
            try {
                this.scanner = new Html5Qrcode("qr-reader");
                const config = {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                    aspectRatio: 1.0
                };
                
                await this.scanner.start(
                    { facingMode: { ideal: "environment" } },
                    config,
                    (text) => this.onScanExito(text),
                    () => {}
                );
            } catch (error) {
                console.error('Error iniciando scanner:', error);
                this.cerrar();
            }
        }
        
        crearModal() {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-[60] p-4';
            modal.innerHTML = `
                <div class="bg-white rounded-xl w-full max-w-sm shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b">
                        <h3 class="font-bold text-lg">📷 Escanear QR</h3>
                        <button class="close-qr text-gray-400 hover:text-red-500 text-2xl leading-none">&times;</button>
                    </div>
                    <div class="p-4">
                        <p class="qr-status text-center text-sm text-gray-500 mb-3">Iniciando cámara...</p>
                        <div id="qr-reader" class="rounded overflow-hidden"></div>
                    </div>
                </div>`;
            
            modal.querySelector('.close-qr').addEventListener('click', () => this.cerrar());
            modal.addEventListener('click', (e) => {
                if (e.target === modal) this.cerrar();
            });
            
            return modal;
        }
        
        onScanExito(text) {
            if (this.cerrado || !this.elementos.qrInput) return;
            
            const statusEl = this.modal?.querySelector('.qr-status');
            
            if (text.length === 17) {
                this.elementos.qrInput.value = text;
                this.elementos.qrError?.classList.add('hidden');
                this.cerrar();
            } else {
                if (statusEl) {
                    statusEl.innerHTML = `<span class="text-red-500">⚠️ QR inválido (${text.length}/17 caracteres)</span>`;
                    setTimeout(() => {
                        if (statusEl) statusEl.textContent = 'Apunta la cámara al código QR';
                    }, 2000);
                }
                this.elementos.qrError?.classList.remove('hidden');
            }
        }
        
        async cerrar() {
            if (this.cerrado) return;
            this.cerrado = true;
            
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                    await this.scanner.clear();
                } catch (e) {
                    console.warn('Error cerrando scanner:', e);
                } finally {
                    this.scanner = null;
                }
            }
            
            if (this.modal?.parentNode) {
                this.modal.remove();
                this.modal = null;
            }
        }
    }

    // Inicializar QR Scanner
    if (elementos.qrBtn && elementos.qrInput) {
        const scanner = new QRScanner(elementos);
        elementos.qrBtn.addEventListener('click', () => scanner.iniciar());
    }

    // Carga de datos con debounce y caché
    const cargarDatos = utils.debounce(async (modeloId) => {
        if (!modeloId) {
            elementos.voltaje.innerHTML = '<option value="">Primero seleccione un modelo</option>';
            elementos.color.innerHTML = '<option value="">Primero seleccione un modelo</option>';
            return;
        }

        // Mostrar estado de carga
        elementos.voltaje.innerHTML = '<option value="">Cargando...</option>';
        elementos.color.innerHTML = '<option value="">Cargando...</option>';

        const urls = [
            `/voltaje-por-modelo/${modeloId}`,
            `/colores-por-modelo/${modeloId}`
        ];

        try {
            const [voltajes, colores] = await Promise.all(
                urls.map(async url => {
                    const cached = Cache.get(url);
                    if (cached) return cached;
                    
                    const data = await utils.fetchWithTimeout(url);
                    Cache.set(url, data);
                    return data;
                })
            );

            elementos.voltaje.innerHTML = utils.generateOptions(voltajes, 'Seleccione un voltaje');
            elementos.color.innerHTML = utils.generateOptions(colores, 'Seleccione un color');
            
        } catch (error) {
            console.error('Error cargando datos:', error);
            
            if (error.name === 'AbortError') {
                elementos.voltaje.innerHTML = '<option value="">Tiempo de espera agotado</option>';
                elementos.color.innerHTML = '<option value="">Tiempo de espera agotado</option>';
            } else {
                elementos.voltaje.innerHTML = '<option value="">Error al cargar</option>';
                elementos.color.innerHTML = '<option value="">Error al cargar</option>';
            }
        }
    }, CONFIG.DEBOUNCE_WAIT);

    // Event listener para cambio de modelo
    elementos.modelo.addEventListener('change', function() {
        cargarDatos(this.value);
    });

    // Manejo de errores de validación
    @if($errors->any())
        setTimeout(() => {
            const alpineData = document.querySelector('[x-data]')?._x_dataStack?.[0];
            if (alpineData?.openCreate) {
                alpineData.openCreate();
            }
        }, 150);
    @endif

    // Cleanup opcional
    window.addEventListener('beforeunload', function() {
        Cache.clear();
    });
});
</script>
</x-app-layout>