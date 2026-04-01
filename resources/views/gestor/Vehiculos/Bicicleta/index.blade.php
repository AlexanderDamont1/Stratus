<x-app-layout>
    
    <div x-data="{ deleteModal: false }" 
     class="mx-auto space-y-6"
     data-negocio-id="{{ auth()->user()->id_negocio }}">


        {{-- ===== MENSAJE FLASH ===== --}}
        <x-flash-messages />

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Gestiona el inventario de bicicletas</p>
            </div>
            @if(auth()->user()->id_rol === 1)
           
            <div class="flex justify-end">
                <a href="{{ route('admin.bicicletas.create') }}"
                    class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition shadow-sm hover:scale-105 transform duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ingresar Bicicleta
                </a>
            </div>
            @endif
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

        {{-- ═══════════════════════════════════════════════════════
             ROL 1 — ADMIN
        ════════════════════════════════════════════════════════ --}}
        @if(auth()->user()->id_rol === 1)

        {{-- ===== TABLA POR VENDEDOR ===== --}}
        @php
        $estadosAdmin = [
        '1' => ['texto' => 'En Stock', 'color' => 'green'],
        '2' => ['texto' => 'Vendido', 'color' => 'purple'],
        '3' => ['texto' => 'En Reparación', 'color' => 'yellow'],
        ];
        @endphp

        @foreach($stockPorVendedor as $seccion)
        @php $idVendedor = $seccion['vendedor']?->id_usuario ?? 'sin_asignar'; @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
            x-data="{
                        abierto: false,
                        pagina: 1,
                        ultimaPagina: 1,
                        total: {{ $seccion['total'] }},
                        filas: [],
                        cargando: false,
                        idUsuario: '{{ $seccion['vendedor']?->id_usuario ?? '' }}',

                        async cargarPagina(p) {
                            this.cargando = true;
                            const params = new URLSearchParams({ page: p });
                            if (this.idUsuario) params.append('id_usuario', this.idUsuario);

                            const res  = await fetch('{{ route('bicicletas.stock.seccion') }}?' + params);
                            const json = await res.json();

                            this.filas       = json.data;
                            this.pagina      = json.current_page;
                            this.ultimaPagina = json.last_page;
                            this.total       = json.total;
                            this.cargando    = false;
                        },

                        init() {
                            this.$watch('abierto', val => {
                                if (val && this.filas.length === 0) this.cargarPagina(1);
                            });

                            window.addEventListener('bicicleta-asignada', (e) => {
                                // ── Acordeón del vendedor que recibió la bici ──
                                if (e.detail.id_usuario === this.idUsuario) {
                                    this.total++;
                                    if (this.abierto) {
                                        // Insertar fila al inicio sin recargar
                                        this.filas.unshift({
                                            num_serie:  e.detail.num_serie,
                                            modelo:     { nombre_modelo: e.detail.modelo },
                                            voltaje:    { voltaje: e.detail.voltaje },
                                            color:      { color: e.detail.color },
                                            status:     e.detail.status,
                                            updated_at: new Date().toISOString(),
                                        });
                                    }
                                }
                                if (this.idUsuario === '' && e.detail.id_usuario !== '') {
                                    this.total = Math.max(0, this.total - 1);
                                    if (this.abierto) {
                                      
                                        this.filas = this.filas.filter(f => f.num_serie !== e.detail.num_serie);
                                    }
                                }
                            });
                        },
                    }">

            {{-- Encabezado accordion --}}
            <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between space-x-2 cursor-pointer select-none"
                @click="abierto = !abierto">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                    :class="abierto ? 'rotate-90' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex-1 min-w-0 truncate">
                    @if($seccion['vendedor'])
                    Sucursal: {{ $seccion['vendedor']->nombre_usuario }}
                    @else
                    *Sin Sucursal Asignado*
                    @endif
                </h3>
                <span class="text-xs text-gray-400 whitespace-nowrap shrink-0" x-text="total + ' unidades'"></span>
            </div>

            {{-- Contenido --}}
            <div x-show="abierto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1">

                {{-- Loading --}}
                <div x-show="cargando" class="px-6 py-36 text-center text-sm text-gray-400">
                    Cargando...
                </div>

                {{-- Tabla escritorio --}}
                <div x-show="!cargando" class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Serie</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marca</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voltaje</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-if="filas.length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 italic">
                                        Sin bicicletas registradas.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="bici in filas" :key="bici.num_serie">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white" x-text="bici.num_serie"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="bici.marca_nombre ?? '—'"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="bici.modelo?.nombre_modelo ?? '—'"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="bici.voltaje?.voltaje ?? '—'"></td>
                                   <td class="px-4 py-3">
                                        <template x-if="bici.color?.color">
                                            <div>
                                                <template x-if="bici.color.color.includes('|')">
                                                    <div class="w-7 h-7 rounded-md border border-black/10 dark:border-white/10 overflow-hidden shrink-0 relative"
                                                        :title="bici.color.color.split('|')[0]">
                                                        <div class="absolute left-0 top-0 w-1/2 h-full"
                                                            :style="'background:' + (bici.color.color.split('|')[1]?.split('/')[0] ?? '#ccc')">
                                                        </div>
                                                        <div class="absolute right-0 top-0 w-1/2 h-full"
                                                            :style="'background:' + (bici.color.color.split('|')[1]?.split('/')[1] ?? bici.color.color.split('|')[1]?.split('/')[0] ?? '#ccc')">
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="!bici.color.color.includes('|')">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="bici.color?.color ?? '—'"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                            :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400':  bici.status == 1,
                                                        'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': bici.status == 2,
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': bici.status == 3,
                                                    }"
                                            x-text="bici.status == 1 ? 'En Stock' : bici.status == 2 ? 'Vendido' : 'En Reparación'">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs" x-text="bici.updated_at ? new Date(bici.updated_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'America/Mexico_City' }) : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Tabla móvil --}}
                <div x-show="!cargando" class="block md:hidden overflow-x-auto">
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
                            <template x-for="bici in filas" :key="bici.num_serie">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-3 py-3">
                                        <div class="text-xs font-medium text-gray-900 dark:text-white" x-text="bici.num_serie"></div>
                                        <div class="text-xs text-gray-400" x-text="(bici.marca_nombre ?? '—') + ' ~ ' + (bici.modelo?.nombre_modelo ?? '—')"></div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-500" x-text="bici.voltaje?.voltaje ?? '—'"></td>
                                  <td class="px-3 py-3">
                                        <template x-if="bici.color?.color">
                                            <div>
                                                <!-- Caso bicolor (con '|') -->
                                                <template x-if="bici.color.color.includes('|')">
                                                    <div x-data="{ showTooltip: false, timeout: null }"
                                                        @click="if(timeout) clearTimeout(timeout); showTooltip = true; timeout = setTimeout(() => showTooltip = false, 1500);"
                                                        class="relative inline-block cursor-pointer">
                                                        <div class="w-6 h-6 rounded-md border border-black/10 dark:border-white/10 overflow-hidden shrink-0 relative"
                                                            :title="bici.color.color.split('|')[0]">
                                                            <div class="absolute left-0 top-0 w-1/2 h-full"
                                                                :style="'background:' + (bici.color.color.split('|')[1]?.split('/')[0] ?? '#ccc')">
                                                            </div>
                                                            <div class="absolute right-0 top-0 w-1/2 h-full"
                                                                :style="'background:' + (bici.color.color.split('|')[1]?.split('/')[1] ?? bici.color.color.split('|')[1]?.split('/')[0] ?? '#ccc')">
                                                            </div>
                                                        </div>
                                                        <!-- Tooltip fijo a la izquierda, sin centrado -->
                                                        <div x-show="showTooltip"
                                                            x-transition.opacity.duration.200ms
                                                            class="absolute z-10 bottom-full left-0 mb-1 px-2 py-1 text-xs bg-black/80 text-white rounded whitespace-nowrap pointer-events-none">
                                                            <span x-text="bici.color.color.split('|')[0]"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Caso color sólido (sin '|') -->
                                                <template x-if="!bici.color.color.includes('|')">
                                                    <span class="text-sm text-gray-500" x-text="bici.color?.color ?? '—'"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap"
                                            :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400':  bici.status == 1,
                                                        'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': bici.status == 2,
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': bici.status == 3,
                                                    }"
                                            x-text="bici.status == 1 ? 'En Stock' : bici.status == 2 ? 'Vendido' : 'En Reparación'">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs" x-text="bici.updated_at ? new Date(bici.updated_at).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'America/Mexico_City' }) : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Controles de paginación --}}
                <div x-show="!cargando && ultimaPagina > 1"
                    class="px-6 py-3 border-t dark:border-gray-700 flex items-center justify-between">
                    <span class="text-xs text-gray-400" x-text="'Página ' + pagina + ' de ' + ultimaPagina"></span>
                    <div class="flex gap-2">
                        <!-- Botón Anterior -->
                        <button
                            @click="cargarPagina(pagina - 1)"
                            :disabled="pagina <= 1"
                            class="px-3 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-40 
                                bg-white text-gray-700 hover:bg-gray-100 
                                dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 
                                transition">
                            ← Anterior
                        </button>

                        <!-- Botón Siguiente -->
                        <button
                            @click="cargarPagina(pagina + 1)"
                            :disabled="pagina >= ultimaPagina"
                            class="px-3 py-1 text-xs rounded border dark:border-gray-600 disabled:opacity-40 
                                bg-white text-gray-700 hover:bg-gray-100 
                                dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 
                                transition">
                            Siguiente →
                        </button>
                    </div>
                </div>

            </div>
        </div>
        @endforeach

        {{-- ═══════════════════════════════════════════════════════
                ROL 5 — TABLA PAGINADA
            ════════════════════════════════════════════════════════ --}}
        @else

        @php
        $estados = [
        '1' => ['texto' => 'En Stock', 'color' => 'green'],
        '2' => ['texto' => 'Reparación', 'color' => 'yellow'],
        '3' => ['texto' => 'Vendido', 'color' => 'blue'],
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


            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $bicicletas->withQueryString()->links() }}
            </div>

        </div>

        @endif

        @if(auth()->user()->id_rol === 1)
            <script>
            document.addEventListener('DOMContentLoaded', () => {
                const idNegocio = document.querySelector('[data-negocio-id]')?.dataset?.negocioId;
                if (!idNegocio || !window.Echo) return;

                window.Echo.private(`catalogo.${idNegocio}`)
                    .listen('.bicicleta.asignada', (e) => {
                       
                        window.dispatchEvent(new CustomEvent('bicicleta-asignada', {
                            detail: e
                        }));
                    });
            });
            </script>
        @endif
    </div>
</x-app-layout>