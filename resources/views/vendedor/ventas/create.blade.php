<x-app-layout>

    @php
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
        x-data="ventaCreate()"
        x-init="init()"
        class="space-y-6 pb-24 lg:pb-10"
        data-vendedor-id="{{ auth()->user()->id_usuario }}"
        data-negocio-id="{{ auth()->user()->id_negocio }}"
        data-buscar-url="{{ route('ventas.buscar-serie') }}"
        data-cupon-url="{{ route('cupones.validar') }}">

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('ventas.index') }}"
                    class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300
                           flex items-center gap-1 mb-2 transition w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Volver a ventas
                </a>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Nueva venta</h2>
                <p class="text-xs text-gray-400 mt-0.5">Escanea el QR o escribe el N° de serie de cada bicicleta</p>
            </div>

            {{-- Total --}}
            <div class="text-right shrink-0">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Total</p>
                <p class="text-4xl font-semibold text-gray-900 dark:text-white tabular-nums leading-none"
                   x-text="fmt(totalConDescuento)">$0.00</p>
                <template x-if="descuento > 0 || tieneGratisEnCarrito">
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1 tabular-nums"
                       x-text="'− ' + fmt(descuento + descuentoGratis) + ' con cupón'"></p>
                </template>
            </div>
        </div>


        {{-- ===== TOAST ===== --}}
        <div x-show="flash.msg" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
            <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-900 px-4 py-2.5
                        shadow-lg ring-1 pointer-events-auto"
                :class="{
                    'ring-gray-200 dark:ring-gray-700': flash.tipo === 'ok',
                    'ring-red-200 dark:ring-red-800':   flash.tipo === 'error',
                    'ring-yellow-200 dark:ring-yellow-700': flash.tipo === 'warn'
                }">
                <span class="w-1.5 h-1.5 rounded-full shrink-0"
                    :class="{
                        'bg-green-500': flash.tipo === 'ok',
                        'bg-red-500':   flash.tipo === 'error',
                        'bg-yellow-400': flash.tipo === 'warn'
                    }"></span>
                <p class="text-sm text-gray-800 dark:text-white" x-text="flash.msg"></p>
                <button @click="flash.msg = ''" class="ml-2 text-gray-300 hover:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Error de sesión --}}
        @if(session('error'))
        <div class="flex items-center gap-2 rounded-lg border border-red-200 dark:border-red-800
                    bg-red-50 dark:bg-red-900/20 px-4 py-3 text-red-700 dark:text-red-400 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- ===== GRID PRINCIPAL ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

            {{-- ======================== COLUMNA IZQUIERDA (3/5) ======================== --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- ── BICICLETA ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-4 font-medium">Bicicleta</p>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text"
                                x-model="serieInput"
                                @keyup.enter="buscarSerie()"
                                @input="serieInput = serieInput.toUpperCase()"
                                maxlength="17"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600
                                       dark:bg-gray-700 dark:text-white text-sm font-mono uppercase tracking-widest
                                       focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30
                                       transition pr-14
                                       placeholder:normal-case placeholder:tracking-normal placeholder:font-sans
                                       placeholder:text-gray-300 dark:placeholder:text-gray-600"
                                :class="serieInput.length === 17
                                       ? 'border-green-400 dark:border-green-600'
                                       : 'border-gray-300 dark:border-gray-600'"
                                placeholder="HE0EA2A00SA963753"
                                :disabled="buscando"
                                x-ref="serieInputRef">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-mono tabular-nums pointer-events-none"
                                :class="serieInput.length === 17 ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'"
                                x-text="serieInput.length + '/17'"></span>
                        </div>
                        <button type="button" @click="buscarSerie()"
                            :disabled="buscando || serieInput.length !== 17"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   px-5 py-2 rounded-lg text-sm font-semibold
                                   hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed
                                   flex items-center gap-2 shrink-0">
                            <template x-if="!buscando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </template>
                            <template x-if="buscando">
                                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            <span x-text="buscando ? 'Buscando…' : 'Buscar'"></span>
                        </button>
                    </div>

                    {{-- Vista previa --}}
                    <div x-show="bikePreview" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-3">
                        <template x-if="bikePreview">
                            <div class="border border-green-200 dark:border-green-800 rounded-lg p-4
                                        bg-green-50 dark:bg-green-900/20 flex items-start gap-4">
                                <div class="shrink-0 mt-0.5">
                                    <template x-if="bikePreview.bici.color_hexes.length === 0">
                                        <div class="w-10 h-10 rounded-md border border-gray-200 dark:border-gray-600
                                                    bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-sm font-bold text-gray-400"
                                                x-text="(bikePreview.bici.color_nombre||'?').charAt(0).toUpperCase()"></span>
                                        </div>
                                    </template>
                                    <template x-if="bikePreview.bici.color_hexes.length === 1">
                                        <div class="w-10 h-10 rounded-md border border-black/10 dark:border-white/10"
                                            :style="'background:'+bikePreview.bici.color_hexes[0]"></div>
                                    </template>
                                    <template x-if="bikePreview.bici.color_hexes.length >= 2">
                                        <div class="w-10 h-10 rounded-md border border-black/10 dark:border-white/10 overflow-hidden relative">
                                            <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:'+bikePreview.bici.color_hexes[0]"></div>
                                            <div class="absolute right-0 top-0 w-1/2 h-full" :style="'background:'+bikePreview.bici.color_hexes[1]"></div>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white"
                                        x-text="bikePreview.bici.marca + ' ' + bikePreview.bici.modelo"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-400 font-mono mt-0.5"
                                        x-text="bikePreview.bici.num_serie"></p>
                                    <p class="text-xs text-gray-400 mt-1"
                                        x-text="bikePreview.bici.voltaje + ' · ' + bikePreview.bici.color_nombre"></p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white mt-1.5"
                                        x-text="fmt(bikePreview.producto.precio)"></p>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-2">
                                    <button type="button" @click="agregarBicicleta()"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5
                                               rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Agregar
                                    </button>
                                    <button type="button"
                                        @click="bikePreview = null; serieInput = ''; $refs.serieInputRef.focus()"
                                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── ACCESORIOS ── --}}
                @if($accesorios->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-4 font-medium">Accesorios</p>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($accesorios as $a)
                        <div class="flex items-center gap-3 py-2.5"
                             x-data="{}"
                             x-bind:class="stockAccesorios['{{ $a->id_producto }}'] === 0 ? 'opacity-50' : ''">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white truncate">{{ $a->nombre_producto }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-xs text-gray-400 tabular-nums">${{ number_format($a->precio, 2) }}</p>
                                    {{-- Badge de stock reactivo --}}
                                    <span class="text-[10px] font-medium tabular-nums px-1.5 py-0.5 rounded-full"
                                        :class="(stockAccesorios['{{ $a->id_producto }}'] ?? {{ $a->stock_disponible }}) > 0
                                            ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                            : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'"
                                        x-text="(stockAccesorios['{{ $a->id_producto }}'] ?? {{ $a->stock_disponible }}) > 0
                                            ? (stockAccesorios['{{ $a->id_producto }}'] ?? {{ $a->stock_disponible }}) + ' en stock'
                                            : 'Sin stock'">
                                    </span>
                                </div>
                            </div>
                            <button type="button"
                                @click="agregarAccesorio({
                                    id_producto: '{{ $a->id_producto }}',
                                    nombre:      '{{ addslashes($a->nombre_producto) }}',
                                    precio:      {{ $a->precio }}
                                })"
                                :disabled="stockDisponibleAccesorio('{{ $a->id_producto }}', {{ $a->stock_disponible }}) <= 0"
                                class="shrink-0 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                       px-3 py-1.5 rounded-md text-xs font-semibold hover:opacity-90 transition active:scale-95
                                       disabled:opacity-40 disabled:cursor-not-allowed">
                                + Agregar
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── DATOS DEL CLIENTE ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Cliente</p>
                        <template x-if="clienteIncompleto && carrito.length > 0">
                            <span class="text-[10px] font-semibold text-red-500 bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full">
                                Campos requeridos
                            </span>
                        </template>
                    </div>
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Nombre <span class="text-red-400">*</span></label>
                                <input type="text" x-model="cliente.nombre"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    :class="clienteTocado.nombre && !cliente.nombre.trim() ? 'border-red-300 dark:border-red-700' : ''"
                                    @blur="clienteTocado.nombre = true"
                                    placeholder="Juan">
                                <p x-show="clienteTocado.nombre && !cliente.nombre.trim()"
                                    class="text-red-400 text-xs mt-1">Requerido</p>
                                @error('nombre_cliente')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Primer apellido <span class="text-red-400">*</span></label>
                                <input type="text" x-model="cliente.apellido1"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    :class="clienteTocado.apellido1 && !cliente.apellido1.trim() ? 'border-red-300 dark:border-red-700' : ''"
                                    @blur="clienteTocado.apellido1 = true"
                                    placeholder="García">
                                <p x-show="clienteTocado.apellido1 && !cliente.apellido1.trim()"
                                    class="text-red-400 text-xs mt-1">Requerido</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Segundo apellido</label>
                                <input type="text" x-model="cliente.apellido2"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    placeholder="López">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Teléfono <span class="text-red-400">*</span></label>
                                <input type="text" x-model="cliente.telefono"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    :class="clienteTocado.telefono && !cliente.telefono.trim() ? 'border-red-300 dark:border-red-700' : ''"
                                    @blur="clienteTocado.telefono = true"
                                    placeholder="5512345678">
                                <p x-show="clienteTocado.telefono && !cliente.telefono.trim()"
                                    class="text-red-400 text-xs mt-1">Requerido</p>
                                @error('telefono')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Correo</label>
                                <input type="email" x-model="cliente.correo"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    placeholder="cliente@email.com">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-900 dark:text-white mb-1">Dirección</label>
                                <input type="text" x-model="cliente.direccion"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                    placeholder="Calle, núm., colonia">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── VENDEDOR ── --}}
                @if(isset($personal) && $personal->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-4 font-medium">Vendedor</p>
                    <select x-model="idPersonal"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        <option value="">— Sin especificar —</option>
                        @foreach($personal as $p)
                        <option value="{{ $p->id_personal }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

            </div>

            {{-- ======================== COLUMNA DERECHA (2/5) ======================== --}}
            <div class="lg:col-span-2 space-y-6 lg:sticky lg:top-6">

                {{-- ── CARRITO ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Carrito</h3>
                            <template x-if="carrito.length > 0">
                                <span class="text-[10px] font-semibold tabular-nums
                                             bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                             px-1.5 py-0.5 rounded-full" x-text="totalItems"></span>
                            </template>
                        </div>
                        <button type="button" x-show="carrito.length > 0" @click="vaciarCarrito()"
                            class="text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">
                            Vaciar
                        </button>
                    </div>

                    <div class="px-6 py-4">
                        <template x-if="carrito.length === 0">
                            <p class="text-sm text-gray-400 py-2">El carrito está vacío</p>
                        </template>

                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(item, idx) in carrito" :key="item.key">
                                <div class="flex items-center gap-3 py-2.5"
                                    :class="item.es_gratis ? 'bg-green-50/40 dark:bg-green-900/10 -mx-2 px-2 rounded' : ''">

                                    {{-- Swatch --}}
                                    <template x-if="item.tipo === '2' && item.color_hexes.length === 0">
                                        <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700
                                                    bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-gray-400"
                                                x-text="(item.color_nombre||'?').charAt(0).toUpperCase()"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.tipo === '2' && item.color_hexes.length === 1">
                                        <div class="w-7 h-7 rounded border border-black/10 dark:border-white/10 shrink-0"
                                            :style="'background:'+item.color_hexes[0]"></div>
                                    </template>
                                    <template x-if="item.tipo === '2' && item.color_hexes.length >= 2">
                                        <div class="w-7 h-7 rounded border border-black/10 dark:border-white/10 overflow-hidden relative shrink-0">
                                            <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:'+item.color_hexes[0]"></div>
                                            <div class="absolute right-0 top-0 w-1/2 h-full" :style="'background:'+item.color_hexes[1]"></div>
                                        </div>
                                    </template>
                                    <template x-if="item.tipo !== '2'">
                                        <div class="w-7 h-7 rounded border shrink-0 flex items-center justify-center"
                                            :class="item.es_gratis
                                                ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30'
                                                : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800'">
                                            <svg class="w-3.5 h-3.5"
                                                :class="item.es_gratis ? 'text-green-600 dark:text-green-400' : 'text-gray-400'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    </template>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-sm text-gray-900 dark:text-white truncate" x-text="item.nombre"></p>
                                            <template x-if="item.es_gratis">
                                                <span class="shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-full
                                                             bg-green-100 dark:bg-green-900/40
                                                             text-green-700 dark:text-green-400 uppercase tracking-wide">
                                                    Gratis
                                                </span>
                                            </template>
                                        </div>
                                        <p class="text-[10px] text-gray-400 font-mono tracking-wider"
                                            x-show="item.num_serie" x-text="item.num_serie"></p>
                                        <template x-if="!item.es_gratis">
                                            <p class="text-xs text-gray-400 tabular-nums"
                                                x-text="fmt(item.precio_unitario) + (item.cantidad > 1 ? ' × ' + item.cantidad : '')"></p>
                                        </template>
                                        <template x-if="item.es_gratis">
                                            <p class="text-xs">
                                                <span class="line-through text-gray-300" x-text="fmt(item.precio_unitario)"></span>
                                                <span class="ml-1 text-green-600 dark:text-green-400 font-semibold">$0.00</span>
                                            </p>
                                        </template>
                                    </div>

                                    {{-- Controles cantidad --}}
                                    <template x-if="item.tipo !== '2' && !item.es_gratis">
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" @click="decrementar(idx)"
                                                class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition text-xs font-bold text-gray-600 dark:text-gray-300">−</button>
                                            <span class="w-4 text-center text-xs font-medium text-gray-700 dark:text-gray-300 tabular-nums"
                                                x-text="item.cantidad"></span>
                                            <button type="button" @click="incrementar(idx)"
                                                :disabled="stockDisponibleAccesorio(item.id_producto, 0) <= item.cantidad"
                                                class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition text-xs font-bold text-gray-600 dark:text-gray-300 disabled:opacity-30 disabled:cursor-not-allowed">+</button>
                                        </div>
                                    </template>

                                    <button type="button" @click="quitar(idx)"
                                        :disabled="item.es_gratis && item.origen_cupon"
                                        class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400
                                               transition disabled:opacity-20 disabled:cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            {{-- Producto gratis extra --}}
                            <template x-if="productoGratisExtra">
                                <div class="flex items-center gap-3 py-2.5 text-gray-400">
                                    <div class="w-7 h-7 rounded border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate"
                                            x-text="productoGratisExtra.nombre_producto"></p>
                                        <p class="text-[10px] text-green-600 dark:text-green-400">Se añadirá gratis con el cupón</p>
                                    </div>
                                    <span class="text-xs font-semibold text-green-600 dark:text-green-400 shrink-0">$0.00</span>
                                </div>
                            </template>
                        </div>

                        {{-- Desglose descuentos --}}
                        <template x-if="carrito.length > 0 && (descuento > 0 || tieneGratisEnCarrito)">
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1">
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Subtotal</span>
                                    <span class="tabular-nums" x-text="fmt(total)"></span>
                                </div>
                                <template x-if="descuento > 0">
                                    <div class="flex justify-between text-xs text-green-600 dark:text-green-400">
                                        <span>Descuento cupón</span>
                                        <span class="tabular-nums" x-text="'− ' + fmt(descuento)"></span>
                                    </div>
                                </template>
                                <template x-if="tieneGratisEnCarrito && descuentoGratis > 0">
                                    <div class="flex justify-between text-xs text-green-600 dark:text-green-400">
                                        <span>Regalo</span>
                                        <span class="tabular-nums" x-text="'− ' + fmt(descuentoGratis)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── CUPÓN ── --}}
                <template x-if="carrito.length > 0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-4 font-medium">Cupón</p>
                        <template x-if="!cupon">
                            <div class="flex gap-2">
                                <input type="text"
                                    x-model="cuponInput"
                                    @keyup.enter="aplicarCupon()"
                                    @input="cuponInput = cuponInput.toUpperCase()"
                                    :disabled="validandoCupon"
                                    placeholder="CÓDIGO"
                                    class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 font-mono uppercase tracking-widest placeholder:normal-case placeholder:tracking-normal placeholder:font-sans placeholder:text-gray-300 dark:placeholder:text-gray-600 transition">
                                <button type="button" @click="aplicarCupon()"
                                    :disabled="validandoCupon || !cuponInput.trim()"
                                    class="px-4 py-2 rounded-lg bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                           text-sm font-semibold hover:opacity-90 transition
                                           disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1.5">
                                    <template x-if="validandoCupon">
                                        <svg class="animate-spin w-3 h-3" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </template>
                                    <span x-text="validandoCupon ? 'Validando…' : 'Aplicar'"></span>
                                </button>
                            </div>
                        </template>
                        <template x-if="cupon">
                            <div class="flex items-center justify-between border border-green-200 dark:border-green-800
                                        rounded-lg px-3 py-2 bg-green-50 dark:bg-green-900/20">
                                <div>
                                    <p class="text-xs font-semibold text-green-700 dark:text-green-400 font-mono tracking-wider"
                                        x-text="cuponInput"></p>
                                    <p class="text-[10px] text-green-600 dark:text-green-500" x-text="cupon.nombre"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-green-700 dark:text-green-400 tabular-nums"
                                        x-text="descuento > 0 ? '− '+fmt(descuento) : ''"></span>
                                    <button type="button" @click="quitarCupon()"
                                        class="text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- ── MÉTODOS DE PAGO ── --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pago</h3>
                        <button type="button" @click="agregarPago()"
                            :disabled="!puedeAgregarPago"
                            class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800
                                   dark:hover:text-gray-200
                                   transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Dividir pago
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-3">
                        <template x-for="(pago, i) in pagos" :key="i">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <select x-model="pago.id_metodo"
                                        @change="onMetodoChange(i)"
                                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                               px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                        <option value="">— Método —</option>
                                        @foreach($metodos as $m)
                                        <option value="{{ $m['value'] }}"
                                            data-efectivo="{{ $m['es_efectivo'] ? '1' : '0' }}"
                                            data-ref="{{ $m['requiere_referencia'] ? '1' : '0' }}">
                                            {{ $m['label'] }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <template x-if="pagos.length > 1">
                                        <div class="relative w-28 shrink-0">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">$</span>
                                            <input type="number" step="0.01" min="0"
                                                x-model="pago.monto"
                                                @input="recalcularPagos(i)"
                                                class="w-full pl-6 pr-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                                       text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition tabular-nums">
                                        </div>
                                    </template>

                                    <button type="button" @click="quitarPago(i)"
                                        x-show="pagos.length > 1 && !(pago.es_efectivo && pagos.filter(p => p.es_efectivo).length === 1)"
                                        class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div x-show="pago.requiere_referencia"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0">
                                    <input type="text" x-model="pago.referencia"
                                        placeholder="Folio / últimos 4 dígitos…"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                               px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30
                                               transition placeholder:text-gray-300 dark:placeholder:text-gray-600">
                                </div>
                            </div>
                        </template>

                        {{-- Progreso --}}
                        <div class="pt-1 space-y-1.5" x-show="carrito.length > 0">
                            <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500"
                                    :class="pagosCubreTotal ? 'bg-green-500' : 'bg-gray-400'"
                                    :style="'width:' + Math.min(100, totalConDescuento > 0 ? (sumaPagos / totalConDescuento * 100) : 0) + '%'">
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs"
                                :class="pagosCubreTotal ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">
                                <span x-text="pagosCubreTotal ? '✓ Pago completo' : 'Pendiente'"></span>
                                <template x-if="!pagosCubreTotal && totalConDescuento > 0">
                                    <span class="tabular-nums"
                                        x-text="fmt(Math.max(0, totalConDescuento - sumaPagos)) + ' por asignar'"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── BOTÓN REGISTRAR (desktop) ── --}}
                <button type="button" @click="submitVenta()"
                    :disabled="!puedeRegistrar"
                    class="hidden lg:flex w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                           py-3 rounded-lg text-sm font-semibold items-center justify-center gap-2
                           hover:opacity-90 active:scale-[.99] transition
                           disabled:opacity-40 disabled:cursor-not-allowed">
                    <template x-if="!enviando">
                        <span x-text="btnLabel"></span>
                    </template>
                    <template x-if="enviando">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Registrando venta…
                        </span>
                    </template>
                </button>

                {{-- Inputs ocultos --}}
                <form id="form-venta" method="POST" action="{{ route('ventas.store') }}" style="display:none">
                    @csrf
                    <div id="carrito-inputs"></div>
                </form>

            </div>
        </div>
    </div>

    {{-- Botón registrar fijo en móvil --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40
                bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800
                px-4 py-3 safe-area-pb">
        <button type="button"
            @click="$dispatch('submit-venta')"
            class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                   py-3 rounded-lg text-sm font-semibold flex items-center justify-center gap-2
                   hover:opacity-90 active:scale-[.99] transition">
            Registrar venta
        </button>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .safe-area-pb { padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
    </style>

    <script>
        function round2(n) { return Math.round(n * 100) / 100; }

        function ventaCreate() {
            return {
                serieInput: '',
                buscando: false,
                enviando: false,
                bikePreview: null,
                carrito: [],
                flash: { msg: '', tipo: 'ok', _t: null },
                buscarUrl: '',
                cuponUrl: '',
                csrfToken: '{{ csrf_token() }}',

                cliente: { nombre: '', apellido1: '', apellido2: '', telefono: '', correo: '', direccion: '' },
                clienteTocado: { nombre: false, apellido1: false, telefono: false },

                cuponInput: '',
                validandoCupon: false,
                cupon: null,
                descuento: 0,
                productoGratisExtra: null,

                idPersonal: '',
                pagos: [],

                // ── Stock de accesorios reactivo ──────────────────────────────
                // Mapa { id_producto: stock } — se actualiza via Reverb
                stockAccesorios: {},

                _nuevoPago(esEfectivo = false) {
                    return {
                        id_metodo: esEfectivo ? 'efectivo' : '',
                        monto: '',
                        referencia: '',
                        es_efectivo: esEfectivo,
                        requiere_referencia: false,
                    };
                },

                init() {
                    this.buscarUrl  = document.querySelector('[data-buscar-url]')?.dataset?.buscarUrl ?? '';
                    this.cuponUrl   = document.querySelector('[data-cupon-url]')?.dataset?.cuponUrl   ?? '';
                    this.pagos      = [this._nuevoPago(true)];
                    this.$nextTick(() => this.$refs.serieInputRef?.focus());
                    window.addEventListener('submit-venta', () => this.submitVenta());

                    // ── Escuchar actualizaciones de stock via Reverb ───────────
                    const negocioId = document.querySelector('[data-negocio-id]')?.dataset?.negocioId;
                    const usuarioId = document.querySelector('[data-vendedor-id]')?.dataset?.vendedorId;

                    if (negocioId && window.Echo) {
                        window.Echo.private(`negocio.${negocioId}`)
                            .listen('.stock.actualizado', (e) => {
                                // Solo procesar si es para este usuario
                                if (e.id_usuario !== usuarioId) return;

                                e.accesorios.forEach(item => {
                                    // Actualizar el mapa de stock reactivo
                                    this.stockAccesorios[item.id_producto] = item.stock;

                                    // Si hay ítems en carrito que ahora superan el stock, ajustar
                                    const enCarrito = this.carrito.find(
                                        c => c.id_producto === item.id_producto && !c.es_gratis
                                    );
                                    if (enCarrito && enCarrito.cantidad > item.stock) {
                                        if (item.stock <= 0) {
                                            // Quitar del carrito y avisar
                                            this.carrito = this.carrito.filter(
                                                c => !(c.id_producto === item.id_producto && !c.es_gratis)
                                            );
                                            this.mostrarFlash(
                                                `"${enCarrito.nombre}" se agotó y fue removido del carrito.`,
                                                'warn'
                                            );
                                        } else {
                                            // Reducir cantidad al máximo disponible
                                            enCarrito.cantidad = item.stock;
                                            this.mostrarFlash(
                                                `Stock de "${enCarrito.nombre}" actualizado a ${item.stock} unidad(es).`,
                                                'warn'
                                            );
                                        }
                                        this._autoCompletarPago();
                                        if (this.cupon) this.recalcularDescuento();
                                    }
                                });
                            });
                    }
                },

                // Devuelve el stock real considerando el mapa reactivo y lo que hay en carrito
                stockDisponibleAccesorio(idProducto, stockInicial) {
                    const stockActual = this.stockAccesorios[idProducto] ?? stockInicial;
                    const enCarrito   = this.carrito
                        .filter(c => c.id_producto === idProducto && !c.es_gratis)
                        .reduce((s, c) => s + c.cantidad, 0);
                    return Math.max(0, stockActual - enCarrito);
                },

                get clienteIncompleto() {
                    return !this.cliente.nombre.trim() || !this.cliente.apellido1.trim() || !this.cliente.telefono.trim();
                },
                get tieneGratisEnCarrito() {
                    return this.carrito.some(i => i.es_gratis && i.origen_cupon);
                },
                get descuentoGratis() {
                    return this.carrito.filter(i => i.es_gratis && i.origen_cupon)
                        .reduce((s, i) => s + i.precio_unitario * i.cantidad, 0);
                },
                get total() {
                    return this.carrito.reduce((s, i) => s + i.precio_unitario * i.cantidad, 0);
                },
                get totalConDescuento() {
                    return Math.max(0, this.total - this.descuento - this.descuentoGratis);
                },
                get totalItems() {
                    return this.carrito.reduce((s, i) => s + i.cantidad, 0);
                },
                get sumaPagos() {
                    return this.pagos.reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
                },
                get pagosCubreTotal() {
                    if (this.carrito.length === 0) return true;
                    if (this.pagos.length === 1) return true;
                    return round2(this.sumaPagos) >= round2(this.totalConDescuento);
                },
                get puedeAgregarPago() {
                    return this.carrito.length > 0 && this.totalConDescuento > 0;
                },
                get puedeRegistrar() {
                    return this.carrito.length > 0 && !this.enviando && this.pagosCubreTotal && !this.clienteIncompleto;
                },
                get btnLabel() {
                    if (this.carrito.length === 0) return 'Agrega productos al carrito';
                    if (this.clienteIncompleto) return 'Completa los datos del cliente';
                    if (!this.pagosCubreTotal) return 'Completa el pago para continuar';
                    return 'Registrar venta · ' + this.fmt(this.totalConDescuento);
                },

                async buscarSerie() {
                    const serie = this.serieInput.trim().toUpperCase();
                    if (!serie || this.buscando) return;
                    if (this.carrito.find(i => i.num_serie === serie)) {
                        this.mostrarFlash('Esta bicicleta ya está en el carrito.', 'warn'); return;
                    }
                    this.buscando = true; this.bikePreview = null;
                    try {
                        const res = await fetch(this.buscarUrl + '?num_serie=' + encodeURIComponent(serie), {
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (!data.ok) { this.mostrarFlash(data.mensaje ?? 'No encontrada.', 'error'); return; }
                        this.bikePreview = data;
                        this.agregarBicicleta();
                    } catch { this.mostrarFlash('Error de conexión.', 'error'); }
                    finally { this.buscando = false; }
                },

                agregarBicicleta() {
                    if (!this.bikePreview) return;
                    const { bici, producto } = this.bikePreview;
                    this.carrito.push({
                        key: bici.num_serie, tipo: '2',
                        id_producto: producto.id_producto,
                        num_serie: bici.num_serie,
                        nombre: bici.marca + ' ' + bici.modelo,
                        precio_unitario: producto.precio, cantidad: 1,
                        color_hexes: bici.color_hexes, color_nombre: bici.color_nombre,
                        es_gratis: false, origen_cupon: false,
                    });
                    this.bikePreview = null; this.serieInput = '';
                    this.$nextTick(() => this.$refs.serieInputRef?.focus());
                    this.mostrarFlash('Bicicleta agregada al carrito.', 'ok');
                    this._autoCompletarPago();
                    if (this.cupon) this.recalcularDescuento();
                },

                agregarAccesorio(acc) {
                    // Validar stock antes de agregar
                    const stockActual = this.stockAccesorios[acc.id_producto] ?? null;
                    const enCarrito   = this.carrito
                        .filter(c => c.id_producto === acc.id_producto && !c.es_gratis)
                        .reduce((s, c) => s + c.cantidad, 0);

                    // Si tenemos stock en el mapa reactivo, usarlo; si no, confiar en el servidor
                    if (stockActual !== null && (enCarrito + 1) > stockActual) {
                        this.mostrarFlash(`Sin stock disponible para "${acc.nombre}".`, 'error');
                        return;
                    }

                    const existente = this.carrito.find(i => i.id_producto === acc.id_producto && !i.es_gratis);
                    if (existente) {
                        existente.cantidad++;
                        this.mostrarFlash('Unidad adicional agregada.', 'ok');
                    } else {
                        this.carrito.push({
                            key: acc.id_producto, tipo: '1', id_producto: acc.id_producto,
                            num_serie: '', nombre: acc.nombre,
                            precio_unitario: parseFloat(acc.precio), cantidad: 1,
                            color_hexes: [], color_nombre: '', es_gratis: false, origen_cupon: false,
                        });
                        this.mostrarFlash('Accesorio agregado.', 'ok');
                    }
                    this._autoCompletarPago();
                    if (this.cupon) this.recalcularDescuento();
                },

                incrementar(idx) {
                    const item = this.carrito[idx];
                    if (item.es_gratis) return;
                    if (item.tipo === '1') {
                        // Verificar stock al incrementar accesorio
                        const disponible = this.stockDisponibleAccesorio(item.id_producto, 0);
                        if (disponible <= item.cantidad) {
                            this.mostrarFlash('No hay más unidades en stock.', 'warn');
                            return;
                        }
                    }
                    item.cantidad++;
                    this._autoCompletarPago();
                    if (this.cupon) this.recalcularDescuento();
                },
                decrementar(idx) {
                    if (this.carrito[idx].es_gratis) return;
                    if (this.carrito[idx].cantidad <= 1) { this.quitar(idx); return; }
                    this.carrito[idx].cantidad--;
                    this._autoCompletarPago();
                    if (this.cupon) this.recalcularDescuento();
                },
                quitar(idx) {
                    if (this.carrito[idx].es_gratis && this.carrito[idx].origen_cupon) return;
                    this.carrito.splice(idx, 1);
                    this._autoCompletarPago();
                    if (this.cupon) this.recalcularDescuento();
                },
                vaciarCarrito() {
                    if (!confirm('¿Vaciar todo el carrito?')) return;
                    this.carrito = []; this.cupon = null; this.descuento = 0;
                    this.cuponInput = ''; this.productoGratisExtra = null;
                    this.pagos = [this._nuevoPago(true)];
                },

                agregarPago() { this.pagos.push(this._nuevoPago()); },
                quitarPago(idx) {
                    if (this.pagos.length <= 1) return;
                    if (this.pagos[idx].es_efectivo && this.pagos.filter(p => p.es_efectivo).length === 1) return;
                    this.pagos.splice(idx, 1); this.recalcularPagos();
                },
                onMetodoChange(idx) {
                    const selects = document.querySelectorAll('[x-model="pago.id_metodo"]');
                    const select  = selects[idx];
                    if (!select) return;
                    const opt = select.options[select.selectedIndex];
                    if (!opt) return;
                    this.pagos[idx].es_efectivo          = opt.dataset.efectivo === '1';
                    this.pagos[idx].requiere_referencia  = opt.dataset.ref      === '1';
                    this._autoCompletarPago(idx);
                },
                recalcularPagos(idxEditado) {
                    if (this.pagos.length < 2) return;
                    const destino = idxEditado === 0 ? this.pagos.length - 1 : 0;
                    const ocupado = this.pagos.reduce((s, p, j) =>
                        (j !== destino) ? s + (parseFloat(p.monto) || 0) : s, 0);
                    const resta = round2(this.totalConDescuento - ocupado);
                    this.pagos[destino].monto = Math.max(0, resta).toFixed(2);
                },
                _autoCompletarPago(idx = null) {
                    if (this.pagos.length === 1) {
                        this.pagos[0].monto = this.totalConDescuento.toFixed(2);
                        return;
                    }
                    const i    = idx !== null ? idx : this.pagos.length - 1;
                    const pago = this.pagos[i];
                    if (!pago) return;
                    const ocupado = this.pagos.reduce((s, p, j) => j !== i ? s + (parseFloat(p.monto) || 0) : s, 0);
                    const resta   = round2(this.totalConDescuento - ocupado);
                    if (resta > 0) pago.monto = resta.toFixed(2);
                },

                async aplicarCupon() {
                    if (!this.cuponInput.trim() || this.validandoCupon || this.carrito.length === 0) return;
                    this.validandoCupon = true;
                    try {
                        const data = await this._llamarValidar();
                        if (!data.valido) { this.mostrarFlash(data.mensaje || 'Cupón no válido.', 'error'); return; }
                        this.cupon = data.cupon; this.descuento = data.descuento;
                        this._aplicarProductoGratis(data); this._autoCompletarPago();
                        this.mostrarFlash(data.mensaje, 'ok');
                    } catch { this.mostrarFlash('Error al validar el cupón.', 'error'); }
                    finally { this.validandoCupon = false; }
                },
                quitarCupon() {
                    const gratisEnCarrito = this.carrito.filter(i => i.origen_cupon);
                    gratisEnCarrito.forEach(item => {
                        const existente = this.carrito.find(i => i.id_producto === item.id_producto && !i.origen_cupon);
                        if (existente) existente.cantidad += 1;
                        else this.carrito.push({ ...item, key: item.id_producto, es_gratis: false, origen_cupon: false });
                    });
                    this.carrito = this.carrito.filter(i => !i.origen_cupon);
                    this.cupon = null; this.descuento = 0; this.cuponInput = '';
                    this.productoGratisExtra = null; this._autoCompletarPago();
                },
                async recalcularDescuento() {
                    if (!this.cupon || this.carrito.length === 0) {
                        this.descuento = 0; this.productoGratisExtra = null;
                        this.carrito = this.carrito.filter(i => !i.origen_cupon);
                        this._autoCompletarPago(); return;
                    }
                    try {
                        const data = await this._llamarValidar();
                        if (data.valido) { this.descuento = data.descuento; this._aplicarProductoGratis(data); }
                        else { this.mostrarFlash(data.mensaje || 'El cupón ya no aplica.', 'warn'); this.quitarCupon(); }
                        this._autoCompletarPago();
                    } catch { /* silencioso */ }
                },
                _aplicarProductoGratis(data) {
                    const gratisAnterior = this.carrito.filter(i => i.origen_cupon);
                    gratisAnterior.forEach(item => {
                        const existente = this.carrito.find(i => i.id_producto === item.id_producto && !i.origen_cupon);
                        if (existente) existente.cantidad += 1;
                        else this.carrito.push({ ...item, key: item.id_producto, es_gratis: false, origen_cupon: false });
                    });
                    this.carrito = this.carrito.filter(i => !i.origen_cupon);
                    this.productoGratisExtra = null;
                    if (!data.producto_gratis) return;
                    const pg = data.producto_gratis;
                    if (data.gratis_ya_en_carrito) {
                        const itemExistente = this.carrito.find(i => i.id_producto === pg.id_producto && !i.origen_cupon);
                        if (itemExistente) {
                            if (itemExistente.cantidad > 1) itemExistente.cantidad -= 1;
                            else this.carrito = this.carrito.filter(i => !(i.id_producto === pg.id_producto && !i.origen_cupon));
                            this.carrito.push({
                                key: pg.id_producto + '_gratis', tipo: '1', id_producto: pg.id_producto,
                                num_serie: '', nombre: pg.nombre_producto, precio_unitario: pg.precio, cantidad: 1,
                                color_hexes: [], color_nombre: '', es_gratis: true, origen_cupon: true,
                            });
                        }
                    } else { this.productoGratisExtra = pg; }
                },
                async _llamarValidar() {
                    const itemsReales = this.carrito.filter(i => !i.origen_cupon)
                        .map(i => ({ id_producto: i.id_producto, cantidad: i.cantidad }));
                    const res = await fetch(this.cuponUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ codigo: this.cuponInput.trim().toUpperCase(), items: itemsReales }),
                    });
                    return res.json();
                },

                submitVenta() {
                    if (!this.puedeRegistrar) {
                        this.clienteTocado.nombre    = true;
                        this.clienteTocado.apellido1 = true;
                        this.clienteTocado.telefono  = true;
                        if (this.clienteIncompleto) this.mostrarFlash('Completa los datos del cliente antes de continuar.', 'error');
                        return;
                    }
                    const cont = document.getElementById('carrito-inputs');
                    cont.innerHTML = '';
                    const mk = (name, val) => {
                        const inp = document.createElement('input');
                        inp.type = 'hidden'; inp.name = name; inp.value = val ?? ''; cont.appendChild(inp);
                    };
                    mk('nombre_cliente', this.cliente.nombre.trim());
                    mk('apellido1',      this.cliente.apellido1.trim());
                    mk('apellido2',      this.cliente.apellido2.trim());
                    mk('telefono',       this.cliente.telefono.trim());
                    mk('correo',         this.cliente.correo.trim());
                    mk('direccion',      this.cliente.direccion.trim());
                    this.carrito.forEach((item, i) => {
                        mk(`items[${i}][id_producto]`, item.id_producto);
                        mk(`items[${i}][num_serie]`,   item.num_serie);
                        mk(`items[${i}][cantidad]`,    item.cantidad);
                        mk(`items[${i}][es_gratis]`,   item.es_gratis ? '1' : '0');
                    });
                    if (this.cupon)      mk('codigo_cupon', this.cuponInput.trim().toUpperCase());
                    if (this.idPersonal) mk('id_personal',  this.idPersonal);

                    const pagosEnviar = this.pagos.length === 1
                        ? [{ ...this.pagos[0], monto: this.totalConDescuento.toFixed(2) }]
                        : this.pagos;

                    pagosEnviar.forEach((pago, i) => {
                        mk(`pagos[${i}][metodo]`,     pago.id_metodo);
                        mk(`pagos[${i}][monto]`,      parseFloat(pago.monto || 0).toFixed(2));
                        mk(`pagos[${i}][referencia]`, pago.referencia || '');
                    });
                    this.enviando = true;
                    document.getElementById('form-venta').submit();
                },

                fmt(n) {
                    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n);
                },
                mostrarFlash(msg, tipo = 'ok') {
                    if (this.flash._t) clearTimeout(this.flash._t);
                    this.flash.msg  = msg;
                    this.flash.tipo = tipo;
                    this.flash._t   = setTimeout(() => { this.flash.msg = ''; }, 3000);
                },
            };
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const idVendedor = document.querySelector('[data-vendedor-id]')?.dataset?.vendedorId;
            if (!idVendedor || !window.Echo) return;
            window.Echo.private(`user.${idVendedor}`).listen('.venta.realizada', (e) => {
                window.dispatchEvent(new CustomEvent('venta-realizada', { detail: e }));
            });
        });
    </script>

</x-app-layout>