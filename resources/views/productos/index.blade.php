<x-app-layout>
<div class="mx-auto space-y-7" x-data="productosPage()" x-init="init()">

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap items-start justify-between gap-4 sm:gap-2">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Precios</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                @if($esRol1) Gestiona productos y precios por sucursal
                @else Gestiona tus productos y precios
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($esRol1)
            <select x-model="sucursalSeleccionada"
                class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">Todas las sucursales</option>
                @foreach($sucursales as $s)
                <option value="{{ $s->id_usuario }}">{{ $s->nombre_usuario }}</option>
                @endforeach
            </select>
            @endif
            <button @click="abrirCrear()"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap hover:scale-105 transform duration-200 active:scale-95">
                + Nuevo producto
            </button>
        </div>
    </div>

    {{-- ===== FLASH ALPINE ===== --}}
    <div x-show="flashVisible" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl min-w-[300px]"
             :class="flashTipo === 'error' ? 'ring-1 ring-red-200 dark:ring-red-800' : 'ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo === 'success'" class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo === 'error'" class="h-5 w-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    {{-- ===== FLASH SESSION ===== --}}
    @if(session('success'))
    <div class="fixed top-6 left-1/2 -translate-x-1/2 z-50"
         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 min-w-[300px]">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- ===== BICICLETAS ===== --}}
    @if($bicicletas->count())
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Bicicletas</p>

        {{-- Skeleton --}}
        <div x-show="loading" x-cloak class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @for ($i = 0; $i < min(5, $bicicletas->count()); $i++)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden animate-pulse">
                <div class="bg-gray-100 dark:bg-gray-700 h-24"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-1/2"></div>
                    <div class="h-7 bg-gray-200 dark:bg-gray-600 rounded mt-2"></div>
                </div>
            </div>
            @endfor
        </div>

        <div x-show="!loading" x-cloak class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($bicicletas as $idModelo => $variantes)
            @php
                $primera  = $variantes->first();
                $vpm      = $primera->productoModelo?->first();
                $marca    = $vpm?->modelo?->marca?->nombre_marca ?? '—';
                $modelo   = $vpm?->modelo?->nombre_modelo ?? $primera->nombre_producto;
                $colores  = $coloresPorModelo[$idModelo] ?? [];

                $precios   = $variantes->pluck('precio')->map(fn($p) => (float)$p)->sort()->values();
                $precioMin = $precios->first();
                $precioMax = $precios->last();

                // Acumular stock de todas las variantes para el badge de la card
                $stockTotalInicial = 0;
                $stockMinimoRef    = 3;
                $allStockJson      = [];

                foreach ($variantes as $v) {
                    $idPm = $v->productoModelo?->first()?->id_producto_modelo;
                    $rows = $idPm && isset($inventario[$idPm])
                        ? (is_iterable($inventario[$idPm]) ? collect($inventario[$idPm]) : collect([$inventario[$idPm]]))
                        : collect();
                    foreach ($rows as $s) {
                        $stockTotalInicial += $s->cantidad;
                        $stockMinimoRef     = $s->stock_minimo ?? 3;
                        $allStockJson[]     = [
                            'id_usuario'   => $s->id_usuario ?? '',
                            'cantidad'     => $s->cantidad,
                            'stock_minimo' => $s->stock_minimo,
                        ];
                    }
                }

                $stockJsonStr = json_encode(array_values($allStockJson));
            @endphp

            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                 x-data="modeloStock({{ $esRol1 ? 'true' : 'false' }}, {{ $stockJsonStr }}, {{ $stockTotalInicial }}, {{ $stockMinimoRef }})"
                 x-init="init()">

                {{-- Header con rango de precios — siempre gris --}}
                <div class="px-4 py-5 text-center bg-gray-50 dark:bg-gray-700/50">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">
                        {{ $marca }} · {{ $modelo }}
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        @if($precioMin == $precioMax)
                            ${{ number_format($precioMin, 0) }}
                        @else
                            ${{ number_format($precioMin, 0) }}<span class="text-base font-normal text-gray-400 mx-1">~</span>${{ number_format($precioMax, 0) }}
                        @endif
                    </p>
                </div>

                {{-- Body --}}
                <div class="bg-white dark:bg-gray-800 px-3 py-3 flex flex-col gap-2">

                    {{-- Colores --}}
                    @if(count($colores))
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-400">Colores</span>
                        <div class="flex items-center gap-1">
                            @foreach(array_slice($colores, 0, 5) as $color)
                                @if(count($color['hex']) === 2)
                                <div class="relative w-3.5 h-3.5 rounded-full overflow-hidden border border-gray-200 dark:border-gray-600 flex-shrink-0"
                                     title="{{ $color['nombre'] }}">
                                    <div class="absolute inset-0 w-1/2" style="background:{{ $color['hex'][0] }}"></div>
                                    <div class="absolute inset-0 left-1/2 w-1/2" style="background:{{ $color['hex'][1] }}"></div>
                                </div>
                                @else
                                <div class="w-3.5 h-3.5 rounded-full border border-gray-200 dark:border-gray-600 flex-shrink-0"
                                     style="background:{{ $color['hex'][0] }}"
                                     title="{{ $color['nombre'] }}"></div>
                                @endif
                            @endforeach
                            @if(count($colores) > 5)
                            <span class="text-[10px] text-gray-400">+{{ count($colores) - 5 }}</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Stock total del modelo (badge siempre gris) --}}
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-400">Stock</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                              :class="colorBadge"
                              x-text="stockLabel">
                        </span>
                    </div>

                    {{-- Botón desplegable de variantes --}}
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full text-[11px] text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition active:scale-95 flex items-center justify-center gap-1">
                            <span x-text="open ? 'Ocultar' : '{{ $variantes->count() }} {{ $variantes->count() === 1 ? "variante" : "variantes" }}'"></span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Filas por variante --}}
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="mt-2 border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden">

                            @foreach($variantes as $v)
                            @php
                                $vpm2    = $v->productoModelo?->first();
                                $idPm2   = $vpm2?->id_producto_modelo;
                                $volt2   = $vpm2?->voltaje?->voltaje ?? '—';

                                $rows2 = $idPm2 && isset($inventario[$idPm2])
                                    ? (is_iterable($inventario[$idPm2]) ? collect($inventario[$idPm2]) : collect([$inventario[$idPm2]]))
                                    : collect();

                                $cant2   = $rows2->first()?->cantidad    ?? 0;
                                $minimo2 = $rows2->first()?->stock_minimo ?? 3;
                                $sJson2  = $rows2->map(fn($s) => [
                                    'id_usuario'   => $s->id_usuario ?? '',
                                    'cantidad'     => $s->cantidad,
                                    'stock_minimo' => $s->stock_minimo,
                                ])->values()->toJson();
                            @endphp

                            <div class="flex items-center gap-2 px-2.5 py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                                 x-data="varianteStock({{ $esRol1 ? 'true' : 'false' }}, {{ $sJson2 }}, {{ $cant2 }}, {{ $minimo2 }})"
                                 x-init="init()">

                                <span class="text-[10px] font-medium bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-0.5 rounded-full flex-shrink-0">
                                    {{ $volt2 }}
                                </span>

                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                    ${{ number_format($v->precio, 0) }}
                                </span>

                                <span class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                      :class="colorBadge"
                                      x-text="stockLabel">
                                </span>

                                <button @click.stop="abrirEditar(
                                        '{{ $v->id_producto }}',
                                        '{{ addslashes($v->nombre_producto) }}',
                                        '{{ $v->tipo }}',
                                        '{{ $v->precio }}'
                                    )"
                                    class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-200 dark:border-gray-600 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition hover:scale-105 active:scale-95 flex-shrink-0"
                                    title="Editar precio">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== ACCESORIOS ===== --}}
    @if($accesorios->count())
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Accesorios</p>

        <div x-show="loading" x-cloak class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @for ($i = 0; $i < min(5, $accesorios->count()); $i++)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden animate-pulse">
                <div class="bg-gray-100 dark:bg-gray-700 h-24"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-3/4"></div>
                    <div class="h-7 bg-gray-200 dark:bg-gray-600 rounded mt-2"></div>
                </div>
            </div>
            @endfor
        </div>

        <div x-show="!loading" x-cloak class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($accesorios as $producto)
            @php
                $idPm      = $producto->productoModelo?->first()?->id_producto_modelo;
                $stockItem = $idPm && isset($inventario[$idPm]) ? $inventario[$idPm] : null;

                if ($esRol1 && $stockItem) {
                    $stockItem = is_iterable($stockItem) ? collect($stockItem)->first() : $stockItem;
                }

                $cantidad    = $stockItem?->cantidad    ?? 0;
                $stockMinimo = $stockItem?->stock_minimo ?? 3;

                // Card siempre gris — solo el badge conserva colores
                $badgeColor = $cantidad == 0
                    ? 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'
                    : ($cantidad < $stockMinimo
                        ? 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300');

                $badgeLabel = $cantidad == 0
                    ? 'Sin stock'
                    : ($cantidad < $stockMinimo ? $cantidad . ' — bajo' : $cantidad . ' uds.');
            @endphp

            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">

                {{-- Header — siempre gris --}}
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-5 text-center">
                    <div class="w-8 h-8 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 truncate px-2">{{ $producto->nombre_producto }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($producto->precio, 0) }}</p>
                </div>

                {{-- Body --}}
                <div class="bg-white dark:bg-gray-800 px-3 py-3 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-400">Stock</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeColor }}">
                            {{ $badgeLabel }}
                        </span>
                    </div>
                    <button @click.stop="abrirEditar(
                            '{{ $producto->id_producto }}',
                            '{{ addslashes($producto->nombre_producto) }}',
                            '{{ $producto->tipo }}',
                            '{{ $producto->precio }}'
                        )"
                        class="w-full text-[11px] text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition active:scale-95 mt-1">
                        Editar precio
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- EMPTY STATE --}}
    @if($bicicletas->isEmpty() && $accesorios->isEmpty())
    <div class="py-16 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">No tienes productos registrados.</p>
        <p class="text-xs text-gray-400 mt-1">Crea tu primer producto con el botón "+ Nuevo producto".</p>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         MODAL: CREAR
    ══════════════════════════════════════════ --}}
    <div x-show="crearModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
        @click.self="crearModal = false">
        <div x-show="crearModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>

            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo producto</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Elige el tipo para continuar</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <button type="button" @click="form.tipo = '1'; form.id_marca = ''; form.id_modelo = ''; form.id_voltaje = ''; modelosDisponibles = []; voltajesDisponibles = []"
                    :class="form.tipo === '1'
                        ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                        : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-400'"
                    class="flex flex-col items-center gap-2 border rounded-xl px-3 py-4 text-sm font-medium transition active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Accesorio
                </button>
                <button type="button" @click="form.tipo = '2'"
                    :class="form.tipo === '2'
                        ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                        : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-400'"
                    class="flex flex-col items-center gap-2 border rounded-xl px-3 py-4 text-sm font-medium transition active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Bicicleta
                </button>
            </div>

            {{-- FORM ACCESORIO --}}
            <form x-show="form.tipo === '1'" @submit.prevent="submitAccesorio()" class="space-y-4">
                @if($esRol1)
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sucursal</label>
                    <select x-model="form.id_usuario"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <option value="">Selecciona una sucursal</option>
                        @foreach($sucursales as $s)
                        <option value="{{ $s->id_usuario }}">{{ $s->nombre_usuario }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre del accesorio</label>
                    <input type="text" x-model="form.nombre_producto" placeholder="Ej. Impermeable talla M"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" x-model="form.precio" min="0" step="0.01" placeholder="0.00"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="crearModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="submitting || !accesorioValido"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!submitting">Guardar</span>
                        <span x-show="submitting" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>

            {{-- FORM BICICLETA --}}
            <form x-show="form.tipo === '2'" @submit.prevent="submitBicicleta()" class="space-y-4">
                @if($esRol1)
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Sucursal</label>
                    <select x-model="form.id_usuario"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <option value="">Selecciona una sucursal</option>
                        @foreach($sucursales as $s)
                        <option value="{{ $s->id_usuario }}">{{ $s->nombre_usuario }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Marca</label>
                    <select x-model="form.id_marca" @change="cargarModelos($event.target.value)"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <option value="">Selecciona una marca</option>
                        @foreach($marcas as $marca)
                        <option value="{{ $marca->id_marca }}">{{ $marca->nombre_marca }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                    <div x-show="loadingModelos" class="text-xs text-gray-400 py-2 px-1">Cargando modelos...</div>
                    <select x-show="!loadingModelos" x-model="form.id_modelo"
                        @change="cargarVoltajes($event.target.value)"
                        :disabled="modelosDisponibles.length === 0"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 disabled:opacity-50">
                        <option value="" x-text="!form.id_marca ? 'Primero selecciona una marca' : (modelosDisponibles.length === 0 ? 'Sin modelos disponibles' : 'Selecciona un modelo')"></option>
                        <template x-for="m in modelosDisponibles" :key="m.id_modelo">
                            <option :value="m.id_modelo" x-text="m.nombre_modelo"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                    <div x-show="loadingVoltajes" class="text-xs text-gray-400 py-2 px-1">Cargando voltajes...</div>
                    <select x-show="!loadingVoltajes" x-model="form.id_voltaje"
                        :disabled="voltajesDisponibles.length === 0"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 disabled:opacity-50">
                        <option value=""
                            x-text="!form.id_modelo ? 'Primero selecciona un modelo' : (voltajesDisponibles.length === 0 ? 'Todos los voltajes ya tienen precio' : 'Selecciona un voltaje')">
                        </option>
                        <template x-for="v in voltajesDisponibles" :key="v.id_voltaje">
                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                        </template>
                    </select>
                    <p x-show="form.id_modelo && !loadingVoltajes && voltajesDisponibles.length === 0"
                       class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                        Este modelo ya tiene precio para todos sus voltajes.
                    </p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" x-model="form.precio" min="0" step="0.01" placeholder="0.00"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="crearModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="submitting || !bicicletaValida"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!submitting">Guardar</span>
                        <span x-show="submitting" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: EDITAR
    ══════════════════════════════════════════ --}}
    <div x-show="editarModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
        @click.self="editarModal = false">
        <div x-show="editarModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>

            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar producto</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="'Editando: ' + editForm.nombre_producto"></p>
                    </div>
                </div>
                <button type="button"
                    @click="editarModal = false; abrirEliminar(editForm.id_producto, editForm.nombre_producto)"
                    class="w-8 h-8 flex items-center justify-center border border-red-200 dark:border-red-800 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition active:scale-95"
                    title="Eliminar producto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitEditar()" class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre</label>
                    <input type="text" x-model="editForm.nombre_producto" required
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo</label>
                    <div class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 text-sm text-gray-400"
                         x-text="editForm.tipo === '2' ? 'Bicicleta' : 'Accesorio'"></div>
                    <p class="text-[11px] text-gray-400 mt-1">El tipo no se puede cambiar.</p>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Precio</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input type="number" x-model="editForm.precio" required min="0" step="0.01"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="editarModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="submitting || !editFormModificado"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!submitting">Guardar cambios</span>
                        <span x-show="submitting" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: ELIMINAR
    ══════════════════════════════════════════ --}}
    <div x-show="eliminarModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
        @click.self="eliminarModal = false">
        <div x-show="eliminarModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>

            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar producto</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Se eliminará permanentemente:</p>
                    <div class="text-center mt-2">
                        <span class="inline-block px-3 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-200"
                              x-text="deleteTarget.nombre"></span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Esta acción no se puede deshacer.</p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="eliminarModal = false"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" @click="submitEliminar()" :disabled="submitting"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                    <span x-show="!submitting">Sí, eliminar</span>
                    <span x-show="submitting" class="inline-flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Eliminando...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         ALPINE JS
    ══════════════════════════════════════════ --}}
    <script>
    function productosPage() {
        return {
            loading:              true,
            crearModal:           false,
            editarModal:          false,
            eliminarModal:        false,
            submitting:           false,
            flashVisible:         false,
            flashMsg:             '',
            flashTipo:            'success',
            flashTimer:           null,
            loadingModelos:       false,
            loadingVoltajes:      false,
            modelosDisponibles:   [],
            voltajesDisponibles:  [],
            sucursalSeleccionada: '{{ $idSucursalFiltro ?? "" }}',

            form: {
                tipo: '1', id_usuario: '', nombre_producto: '',
                id_marca: '', id_modelo: '', id_voltaje: '', precio: '',
            },
                editFormOriginal: { nombre_producto: '', precio: '' },
                editForm: { id_producto: '', nombre_producto: '', tipo: '', precio: '' },
                deleteTarget: { id: '', nombre: '' },

            get accesorioValido() {
                const tieneNombre = this.form.nombre_producto.trim().length > 0;
                const tienePrecio = this.form.precio !== '' && Number(this.form.precio) >= 0;
                @if($esRol1)
                return tieneNombre && tienePrecio && this.form.id_usuario !== '';
                @else
                return tieneNombre && tienePrecio;
                @endif
            },

            get bicicletaValida() {
                const tieneMarca   = this.form.id_marca !== '';
                const tieneModelo  = this.form.id_modelo !== '';
                const tieneVoltaje = this.form.id_voltaje !== '';
                const tienePrecio  = this.form.precio !== '' && Number(this.form.precio) >= 0;
                @if($esRol1)
                return tieneMarca && tieneModelo && tieneVoltaje && tienePrecio && this.form.id_usuario !== '';
                @else
                return tieneMarca && tieneModelo && tieneVoltaje && tienePrecio;
                @endif
            },

            get editFormModificado() {
                const nombreCambio = this.editForm.nombre_producto.trim() !== this.editFormOriginal.nombre_producto.trim();
                const precioCambio = String(this.editForm.precio) !== String(this.editFormOriginal.precio);
                const nombreValido = this.editForm.nombre_producto.trim().length > 0;
                const precioValido = this.editForm.precio !== '' && Number(this.editForm.precio) >= 0;
                return (nombreCambio || precioCambio) && nombreValido && precioValido;
            },

            routes: {
                storeAccesorio:  '{{ $esRol1 ? route("admin.productos.storeAccesorio") : route("sucursal.productos.storeAccesorio") }}',
                storeBicicleta:  '{{ $esRol1 ? route("admin.productos.storeBicicleta") : route("sucursal.productos.storeBicicleta") }}',
                update:          (id) => `{{ $esRol1 ? url("/productos") : url("/sucursal/productos") }}/${id}`,
                destroy:         (id) => `{{ $esRol1 ? url("/productos") : url("/sucursal/productos") }}/${id}`,
                voltajes:        (id) => `{{ $esRol1 ? url("/productos/voltajes") : url("/sucursal/productos/voltajes") }}/${id}`,
                modelosPorMarca: (id) => `{{ $esRol1 ? url("/productos/modelos-por-marca") : url("/sucursal/productos/modelos-por-marca") }}/${id}`,
            },

            init() {
                const skeletonShown = sessionStorage.getItem('productPageSkeletonShown');
                if (skeletonShown === 'true') {
                    this.loading = false;
                } else {
                    setTimeout(() => {
                        this.loading = false;
                        sessionStorage.setItem('productPageSkeletonShown', 'true');
                    }, 300);
                }

                // ✅ Antes solo disparaba el evento Alpine (solo actualizaba números).
                // Ahora navega al servidor para que PHP filtre los productos correctos.
                this.$watch('sucursalSeleccionada', val => {
                    const url = new URL(window.location.href);
                    if (val) {
                        url.searchParams.set('sucursal', val);
                    } else {
                        url.searchParams.delete('sucursal');
                    }
                    // ✅ Resetear el skeleton para que se vea la transición
                    sessionStorage.removeItem('productPageSkeletonShown');
                    window.location.href = url.toString();
                });
            },

            flash(msg, tipo = 'success') {
                this.flashMsg     = msg;
                this.flashTipo    = tipo;
                this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
            },

            abrirCrear() {
                this.form = { tipo: '1', id_usuario: '', nombre_producto: '', id_marca: '', id_modelo: '', id_voltaje: '', precio: '' };
                this.modelosDisponibles  = [];
                this.voltajesDisponibles = [];
                this.crearModal = true;
            },

            abrirEditar(id, nombre, tipo, precio) {
                this.editForm         = { id_producto: id, nombre_producto: nombre, tipo, precio };
                this.editFormOriginal = { nombre_producto: nombre, precio };
                this.editarModal      = true;
            },

            abrirEliminar(id, nombre) {
                this.deleteTarget = { id, nombre };
                this.eliminarModal = true;
            },

            async cargarModelos(idMarca) {
                this.form.id_modelo = ''; this.form.id_voltaje = '';
                this.modelosDisponibles = []; this.voltajesDisponibles = [];
                if (!idMarca) return;
                this.loadingModelos = true;
                try {
                    const res = await fetch(this.routes.modelosPorMarca(idMarca), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error();
                    this.modelosDisponibles = await res.json();
                } catch { this.flash('Error cargando modelos.', 'error'); }
                finally { this.loadingModelos = false; }
            },

            async cargarVoltajes(idModelo) {
                this.form.id_voltaje = ''; this.voltajesDisponibles = [];
                if (!idModelo) return;
                this.loadingVoltajes = true;
                try {
                    const res = await fetch(this.routes.voltajes(idModelo), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error();
                    this.voltajesDisponibles = await res.json();
                } catch { this.flash('Error cargando voltajes.', 'error'); }
                finally { this.loadingVoltajes = false; }
            },

            async postForm(url, payload) {
                this.submitting = true;
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    let data = {};
                    try { data = await res.json(); } catch (_) {}
                    if (!res.ok) {
                        const err = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Error al guardar.');
                        this.flash(err, 'error');
                        return;
                    }
                    this.crearModal = false;
                    this.flash(data.message || 'Guardado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.submitting = false; }
            },

            async submitAccesorio() {
                @if($esRol1)
                if (!this.form.id_usuario)            { this.flash('Selecciona una sucursal.', 'error'); return; }
                @endif
                if (!this.form.nombre_producto.trim()) { this.flash('Ingresa el nombre.', 'error');      return; }
                if (this.form.precio === '')            { this.flash('Ingresa el precio.', 'error');      return; }
                await this.postForm(this.routes.storeAccesorio, {
                    id_usuario:      this.form.id_usuario,
                    nombre_producto: this.form.nombre_producto,
                    precio:          this.form.precio,
                });
            },

            async submitBicicleta() {
                @if($esRol1)
                if (!this.form.id_usuario) { this.flash('Selecciona una sucursal.', 'error'); return; }
                @endif
                if (!this.form.id_marca)   { this.flash('Selecciona una marca.', 'error');   return; }
                if (!this.form.id_modelo)  { this.flash('Selecciona un modelo.', 'error');   return; }
                if (!this.form.id_voltaje) { this.flash('Selecciona un voltaje.', 'error');  return; }
                if (this.form.precio === '') { this.flash('Ingresa el precio.', 'error');     return; }
                await this.postForm(this.routes.storeBicicleta, {
                    id_usuario: this.form.id_usuario,
                    id_modelo:  this.form.id_modelo,
                    id_voltaje: this.form.id_voltaje,
                    precio:     this.form.precio,
                });
            },

            async submitEditar() {
                this.submitting = true;
                try {
                    const res = await fetch(this.routes.update(this.editForm.id_producto), {
                        method: 'PUT',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify(this.editForm),
                    });
                    let data = {};
                    try { data = await res.json(); } catch (_) {}
                    if (!res.ok) { this.flash(data.message || 'Error al actualizar.', 'error'); return; }
                    this.editarModal = false;
                    this.flash(data.message || 'Actualizado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.submitting = false; }
            },

            async submitEliminar() {
                this.submitting = true;
                try {
                    const res = await fetch(this.routes.destroy(this.deleteTarget.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                    });
                    let data = {};
                    try { data = await res.json(); } catch (_) {}
                    if (!res.ok) { this.flash(data.message || 'Error al eliminar.', 'error'); return; }
                    this.eliminarModal = false;
                    this.flash(data.message || 'Eliminado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.submitting = false; }
            },
        }
    }

    // Card principal del modelo: badge de stock siempre gris (sin alerta de color)
    function modeloStock(esAdmin, stockData, cantidadInicial, stockMinimoInicial) {
        return {
            esAdmin,
            stockData,
            cantidad:    cantidadInicial,
            stockMinimo: stockMinimoInicial,
            colorBadge:  '',
            stockLabel:  '',

            init() {
                if (this.esAdmin) {
                    window.addEventListener('sucursal-cambio', (e) => this.aplicarFiltro(e.detail));
                }
                this.calcular();
            },

            aplicarFiltro(idUsuario) {
                if (!idUsuario) {
                    this.cantidad    = this.stockData.reduce((acc, s) => acc + s.cantidad, 0);
                    this.stockMinimo = this.stockData[0]?.stock_minimo ?? 3;
                } else {
                    const rows = this.stockData.filter(s => s.id_usuario === idUsuario);
                    this.cantidad    = rows.reduce((acc, s) => acc + s.cantidad, 0);
                    this.stockMinimo = rows[0]?.stock_minimo ?? 3;
                }
                this.calcular();
            },

            calcular() {
                this.colorBadge = 'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400';
                this.stockLabel = this.cantidad === 0 ? 'Sin stock' : this.cantidad + ' uds.';
            },
        }
    }

    // Filas de variante desplegable: badge con colores según stock
    function varianteStock(esAdmin, stockData, cantidadInicial, stockMinimoInicial) {
        return {
            esAdmin,
            stockData,
            cantidad:    cantidadInicial,
            stockMinimo: stockMinimoInicial,
            colorBadge:  '',
            stockLabel:  '',

            init() {
                if (this.esAdmin) {
                    window.addEventListener('sucursal-cambio', (e) => this.aplicarFiltro(e.detail));
                }
                this.calcular();
            },

            aplicarFiltro(idUsuario) {
                if (!idUsuario) {
                    this.cantidad    = this.stockData.reduce((acc, s) => acc + s.cantidad, 0);
                    this.stockMinimo = this.stockData[0]?.stock_minimo ?? 3;
                } else {
                    const row        = this.stockData.find(s => s.id_usuario === idUsuario);
                    this.cantidad    = row?.cantidad     ?? 0;
                    this.stockMinimo = row?.stock_minimo ?? 3;
                }
                this.calcular();
            },

            calcular() {
                if (this.cantidad === 0) {
                    this.colorBadge = 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300';
                    this.stockLabel = 'Sin stock';
                } else {
                    this.colorBadge = 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                    this.stockLabel = this.cantidad + ' uds.';
                }
            },
        }
    }
    </script>

</div>
</x-app-layout> 