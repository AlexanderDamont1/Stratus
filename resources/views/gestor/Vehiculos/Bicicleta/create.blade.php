<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Crear Bicicleta
        </h2>
    </x-slot>
@if($errors->any())
    <div class="bg-red-100 p-4 rounded mb-4">
        <ul>
            @foreach($errors->all() as $error)
                <li class="text-red-600 text-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="py-12">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- INFO NEGOCIO --}}
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded">
                        <strong>Negocio:</strong> {{ $negocio->id_negocio }}
                    </div>

                    <form id="formBicicleta"
                          action="{{ route('gestor.vehiculos.bicicletas.store') }}"
                          method="POST">
                        @csrf

                        {{-- Número de serie QR --}}
                        <div class="mb-6">
                            <label class="block mb-1 font-medium">Número de Serie (QR) *</label>
                            <div class="flex gap-2">
                                <input type="text" name="num_serie" id="num_serie" required limit="17" maxlength="17" 
                                       class="flex-1 rounded border p-2 bg-gray-100 cursor-not-allowed"
                                       placeholder="Escanea el código QR">
                                <button type="button" id="btnEscanearQR"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                                    Escanear QR
                                </button>
                            </div>
                            <p id="qr-error" class="text-red-500 text-sm mt-1 hidden">
                                El código QR debe tener exactamente 17 caracteres.
                            </p>
                        </div>

                        {{-- Campos --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            

                            
                            <div>
                                <label>Modelo *</label>
                                <select name="id_modelo" id="id_modelo" required class="w-full rounded border p-2">
                                    <option value="">Seleccione un modelo</option>
                                    @foreach($modelos as $modelo)
                                        <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label>Voltaje *</label>
                                <select name="id_voltaje" id="id_voltaje" required class="w-full rounded border p-2">
                                    <option value="">Primero seleccione un modelo</option>
                                </select>
                            </div>

                            <div>
                                <label>Color *</label>
                                <select name="id_color" id="id_color" required class="w-full rounded border p-2">
                                    <option value="">Primero seleccione un modelo</option>
                                </select>
                            </div>

                            
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('gestor.vehiculos.bicicletas.index') }}"
                               class="px-4 py-2 bg-gray-500 text-white rounded">Cancelar</a>
                            <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- Escaneo QR ---
        const btn = document.getElementById('btnEscanearQR');
        const input = document.getElementById('num_serie');
        const error = document.getElementById('qr-error');
        let scanner = null, modal = null, cerrado = false;

        btn.addEventListener('click', abrirScanner);

        function abrirScanner() {
            error.classList.add('hidden'); cerrado = false;
            modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4';
            modal.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-sm shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b dark:border-gray-700">
                        <h3 class="font-bold text-lg dark:text-white">📷 Escanear QR</h3>
                        <button id="closeQR" class="text-gray-400 hover:text-red-500 text-2xl leading-none">&times;</button>
                    </div>
                    <div class="p-4">
                        <p id="qr-status" class="text-center text-sm text-gray-500 mb-3">Iniciando cámara...</p>
                        <div id="qr-reader" class="rounded overflow-hidden"></div>
                    </div>
                </div>`;
            document.body.appendChild(modal);
            document.getElementById('closeQR').onclick = cerrarScanner;
            scanner = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 220, height: 220 },
                             aspectRatio: 1.0, experimentalFeatures: { useBarCodeDetectorIfSupported: true } };
            scanner.start({ facingMode: { ideal: "environment" } }, config, onScanExito, () => {})
                   .catch(() => scanner.start({ facingMode: "user" }, config, onScanExito, () => {}));
        }

        function onScanExito(text) {
            if (cerrado) return;
            if (text.length === 17) { input.value = text; error.classList.add('hidden'); cerrarScanner(); }
            else {
                const status = document.getElementById('qr-status');
                if (status) {
                    status.innerHTML = `<span class="text-red-500">⚠️ QR inválido (${text.length} chars). Se esperan 17.</span>`;
                    setTimeout(() => { status.textContent = 'Apunta la cámara al código QR'; }, 2500);
                }
                error.classList.remove('hidden');
            }
        }

        function cerrarScanner() {
            if (cerrado) return; cerrado = true;
            if (scanner) scanner.stop().then(() => { scanner.clear(); scanner = null; }).catch(() => { scanner = null; });
            if (modal) { modal.remove(); modal = null; }
        }

        document.addEventListener('click', e => { if (modal && e.target === modal) cerrarScanner(); });

        // --- Cargar voltajes y colores según id_modelo ---
        const modeloSelect = document.getElementById('id_modelo');
        const voltajeSelect = document.getElementById('id_voltaje');
        const colorSelect   = document.getElementById('id_color');

        modeloSelect.addEventListener('change', function() {
            const modeloId = this.value;

            voltajeSelect.innerHTML = '<option value="">Cargando voltajes...</option>';
            voltajeSelect.disabled = true;
            colorSelect.innerHTML = '<option value="">Cargando colores...</option>';
            colorSelect.disabled = true;

            if (!modeloId) {
                voltajeSelect.innerHTML = '<option value="">Primero seleccione un modelo</option>';
                voltajeSelect.disabled = false;
                colorSelect.innerHTML = '<option value="">Primero seleccione un modelo</option>';
                colorSelect.disabled = false;
                return;
            }

            fetch(`/gestor/vehiculos/voltaje-por-modelo/${modeloId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        voltajeSelect.innerHTML = '<option value="">No hay voltajes disponibles</option>';
                    } else {
                        let options = '<option value="">Seleccione un voltaje</option>';
                        data.forEach(item => {
                            options += `<option value="${item.id_voltaje}">${item.voltaje}</option>`;
                        });
                        voltajeSelect.innerHTML = options;
                    }
                    voltajeSelect.disabled = false;
                })
                .catch(() => {
                    voltajeSelect.innerHTML = '<option value="">Error al cargar voltajes</option>';
                    voltajeSelect.disabled = false;
                });

            fetch(`/gestor/vehiculos/colores-por-modelo/${modeloId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        colorSelect.innerHTML = '<option value="">No hay colores disponibles</option>';
                    } else {
                        let options = '<option value="">Seleccione un color</option>';
                        data.forEach(item => {
                            options += `<option value="${item.id_color}">${item.color}</option>`;
                        });
                        colorSelect.innerHTML = options;
                    }
                    colorSelect.disabled = false;
                })
                .catch(() => {
                    colorSelect.innerHTML = '<option value="">Error al cargar colores</option>';
                    colorSelect.disabled = false;
                });
        });

    });
    </script>
    @endpush
</x-app-layout>