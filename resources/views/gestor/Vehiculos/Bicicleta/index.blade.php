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
        class="space-y-6">

        {{-- ===== MENSAJE FLASH ===== --}}
        <x-flash-messages />

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Gestiona el inventario de bicicletas</p>
            </div>
        </div>

        {{-- ===== ESTADÍSTICAS ===== --}}
        <div class="grid grid-cols-3 gap-4">
            @if(auth()->user()->id_rol === 1)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">En Stock</p>
                <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['en_stock'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Vendidas</p>
                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['vendidas'] }}</p>
            </div>
            @else
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
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $bicicletas->currentPage() }}
                    <span class="text-sm font-normal text-gray-400 ml-1">/{{ $bicicletas->lastPage() }}</span>
                </p>
            </div>
            @endif
        </div>

        {{-- ===== FILTROS (solo rol 5) ===== --}}
        @if(auth()->user()->id_rol !== 1)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
            <form method="GET" action="{{ route('bicicletas.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="N° Serie o Status"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('bicicletas.index') }}"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════
             ROL 1 — ADMIN
        ════════════════════════════════════════════════════════ --}}
        @if(auth()->user()->id_rol === 1)

        {{-- ===== BOTÓN NUEVA BICICLETA ===== --}}
        <div class="flex justify-end">
            <a href="{{ route('admin.bicicletas.create') }}"
            class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Bicicleta
            </a>
        </div>

        {{-- ===== MODAL CREAR BICICLETA ===== --}}
        <div x-show="createModal"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            @keydown.escape.window="closeCreate()">

            <div x-show="createModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="closeCreate()"
                class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nueva Bicicleta</h3>
                    <button @click="closeCreate()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('admin.bicicletas.store') }}" class="p-6 space-y-4" x-data="{ submitting: false }"
                    @submit="submitting = true">
                    @csrf

                    {{-- Número de Serie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Número de Serie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="num_serie" required maxlength="17" minlength="17"
                            placeholder="ABC12345678901234"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            value="{{ old('num_serie') }}">
                        <p class="text-xs text-gray-400 mt-1">Debe tener exactamente 17 caracteres</p>
                        @error('num_serie')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Marca <span class="text-red-500">*</span>
                        </label>
                        <select id="marcaSelect" name="id_marca" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm">
                            <option value="">Selecciona marca</option>
                        </select>
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Modelo <span class="text-red-500">*</span>
                        </label>
                        <select id="modeloSelect" name="id_modelo" required disabled
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Primero selecciona una marca</option>
                        </select>
                        @error('id_modelo')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color y Voltaje --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Color <span class="text-red-500">*</span>
                            </label>
                            <select id="colorSelect" name="id_color" required disabled
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Primero selecciona un modelo</option>
                            </select>
                            @error('id_color')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Voltaje <span class="text-red-500">*</span>
                            </label>
                            <select id="voltajeSelect" name="id_voltaje" required disabled
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">Primero selecciona un modelo</option>
                            </select>
                            @error('id_voltaje')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="closeCreate()"
                            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="flex-1 px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-medium hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!submitting">Crear Bicicleta</span>
                            <span x-show="submitting" class="inline-flex items-center justify-center gap-1">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>

                {{-- Script catálogo en cascada --}}
                <script>
                (function () {
                    const CATALOGO = @json(json_decode($catalogoJson));

                    const marcaSelect   = document.getElementById('marcaSelect');
                    const modeloSelect  = document.getElementById('modeloSelect');
                    const colorSelect   = document.getElementById('colorSelect');
                    const voltajeSelect = document.getElementById('voltajeSelect');

                    function llenar(select, items, keyId, keyTexto, placeholder) {
                        select.innerHTML = `<option value="">${placeholder}</option>`;
                        items.forEach(item => {
                            const opt       = document.createElement('option');
                            opt.value       = item[keyId];
                            opt.textContent = item[keyTexto];
                            select.appendChild(opt);
                        });
                        select.disabled = items.length === 0;
                    }

                    function resetear(select, placeholder) {
                        select.innerHTML = `<option value="">${placeholder}</option>`;
                        select.disabled  = true;
                        select.value     = '';
                    }

                    // Poblar marcas al cargar
                    llenar(marcaSelect, CATALOGO, 'id_marca', 'nombre_marca', 'Selecciona marca');

                    // Marca → Modelos
                    marcaSelect.addEventListener('change', function () {
                        resetear(modeloSelect,  'Selecciona modelo');
                        resetear(colorSelect,   'Primero selecciona un modelo');
                        resetear(voltajeSelect, 'Primero selecciona un modelo');

                        const marca = CATALOGO.find(m => String(m.id_marca) === this.value);
                        if (!marca) return;

                        llenar(modeloSelect, marca.modelos, 'id_modelo', 'nombre_modelo', 'Selecciona modelo');
                    });

                    // Modelo → Colores + Voltajes
                    modeloSelect.addEventListener('change', function () {
                        resetear(colorSelect,   'Selecciona color');
                        resetear(voltajeSelect, 'Selecciona voltaje');

                        const marca = CATALOGO.find(m => String(m.id_marca) === marcaSelect.value);
                        if (!marca) return;

                        const modelo = marca.modelos.find(mo => String(mo.id_modelo) === this.value);
                        if (!modelo) return;

                        llenar(colorSelect,   modelo.colores,  'id_color',   'color',   'Selecciona color');
                        llenar(voltajeSelect, modelo.voltajes, 'id_voltaje', 'voltaje', 'Selecciona voltaje');
                    });
                })();
                </script>
            </div>
        </div>

        {{-- ===== TABLA POR VENDEDOR ===== --}}
        @php
        $estadosAdmin = [
            '1' => ['texto' => 'En Stock',      'color' => 'green'],
            '2' => ['texto' => 'Vendido',        'color' => 'purple'],
            '3' => ['texto' => 'En Reparación',  'color' => 'yellow'],
        ];
        @endphp

        @foreach($stockPorVendedor as $seccion)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
            x-data="{ abierto: false }">

            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between space-x-2 cursor-pointer select-none"
                @click="abierto = !abierto">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                    :class="abierto ? 'rotate-90' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex-1 min-w-0 truncate">
                    @if($seccion['vendedor'])
                        Sucursal: {{ $seccion['vendedor']->nombre_usuario }}
                    @else
                        *Sin Sucursal Asignado*
                    @endif
                </h3>
                <span class="text-xs text-gray-400 whitespace-nowrap shrink-0">
                    {{ $seccion['bicicletas']->count() }} unidades
                </span>
            </div>

            <div x-show="abierto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1">

                @if($seccion['bicicletas']->isEmpty())
                    <p class="text-sm text-gray-400 italic px-6 py-4">Sin bicicletas registradas.</p>
                @else

                    {{-- Vista escritorio --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Serie</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voltaje</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($seccion['bicicletas'] as $bici)
                                @php
                                    $est   = $estadosAdmin[$bici->status] ?? ['texto' => 'Desconocido', 'color' => 'red'];
                                    $color = $est['color'];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $bici->num_serie }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $bici->modelo->nombre_modelo ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $bici->voltaje->voltaje ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $bici->color->color ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @switch($color)
                                                @case('green')  bg-green-100  text-green-800  dark:bg-green-800/30  dark:text-green-400  @break
                                                @case('purple') bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400 @break
                                                @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                                @default        bg-red-100    text-red-800    dark:bg-red-800/30    dark:text-red-400
                                            @endswitch">
                                            {{ $est['texto'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $bici->updated_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
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
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($seccion['bicicletas'] as $bici)
                                @php
                                    $est   = $estadosAdmin[$bici->status] ?? ['texto' => 'Desconocido', 'color' => 'red'];
                                    $color = $est['color'];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-3 py-3">
                                        <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $bici->num_serie }}</div>
                                        <div class="text-xs text-gray-400">{{ $bici->modelo->nombre_modelo ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500">{{ $bici->voltaje->voltaje ?? '—' }}</td>
                                    <td class="px-3 py-3 text-xs text-gray-500">{{ $bici->color->color ?? '—' }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                            @switch($color)
                                                @case('green')  bg-green-100  text-green-800  dark:bg-green-800/30  dark:text-green-400  @break
                                                @case('purple') bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400 @break
                                                @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                                @default        bg-red-100    text-red-800    dark:bg-red-800/30    dark:text-red-400
                                            @endswitch">
                                            {{ $est['texto'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500">{{ $bici->updated_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
            </div>
        </div>
        @endforeach

        {{-- ═══════════════════════════════════════════════════════
             ROL 5 — TABLA PAGINADA
        ════════════════════════════════════════════════════════ --}}
        @else

        @php
        $estados = [
            '1' => ['texto' => 'En Stock',    'color' => 'green'],
            '2' => ['texto' => 'Reparación',  'color' => 'yellow'],
            '3' => ['texto' => 'Vendido',     'color' => 'blue'],
        ];
        @endphp

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
                        @php
                            $statusKey = $bicicleta->status;
                            $estado    = $estados[$statusKey] ?? ['texto' => ucfirst(str_replace('_', ' ', $statusKey)), 'color' => 'red'];
                            $color     = $estado['color'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->color->color ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($color)
                                        @case('green')  bg-green-100  text-green-800  dark:bg-green-800/30  dark:text-green-400  @break
                                        @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                        @case('blue')   bg-blue-100   text-blue-800   dark:bg-blue-800/30   dark:text-blue-400   @break
                                        @default        bg-red-100    text-red-800    dark:bg-red-800/30    dark:text-red-400
                                    @endswitch">
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
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($bicicletas as $bicicleta)
                        @php
                            $statusKey = $bicicleta->status;
                            $estado    = $estados[$statusKey] ?? ['texto' => ucfirst(str_replace('_', ' ', $statusKey)), 'color' => 'red'];
                            $color     = $estado['color'];
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
                                        @case('green')  bg-green-100  text-green-800  dark:bg-green-800/30  dark:text-green-400  @break
                                        @case('yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 @break
                                        @case('blue')   bg-blue-100   text-blue-800   dark:bg-blue-800/30   dark:text-blue-400   @break
                                        @default        bg-red-100    text-red-800    dark:bg-red-800/30    dark:text-red-400
                                    @endswitch">
                                    {{ $estado['texto'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-500">{{ $bicicleta->updated_at->format('d/m/Y') }}</td>
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

        @endif
        {{-- fin @if rol --}}

    </div>
</x-app-layout>