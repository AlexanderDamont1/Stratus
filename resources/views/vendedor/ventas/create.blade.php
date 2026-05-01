<x-app-layout>
<div
    x-data="ventaCreate()"
    x-init="init()"
    class="space-y-6 max-w-6xl mx-auto"
    data-vendedor-id="{{ auth()->user()->id_usuario }}"
    data-buscar-url="{{ route('ventas.buscar-serie') }}"
    data-cupon-url="{{ route('cupones.validar') }}"
>
    {{-- ══ HEADER ══ --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('ventas.index') }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Nueva venta</h2>
            <p class="text-xs text-gray-400">Escanea el QR o escribe el N° de serie de cada bicicleta</p>
        </div>
    </div>

    {{-- Flash sesión --}}
    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    {{-- ===== FLASH (toast flotante) ===== --}}
    <div x-show="flash.msg" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 w-auto min-w-[300px] max-w-md">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1"
            :class="{
                'ring-gray-200 dark:ring-gray-700': flash.tipo === 'ok',
                'ring-red-200 dark:ring-red-800':   flash.tipo === 'error',
                'ring-yellow-200 dark:ring-yellow-800': flash.tipo === 'warn'
            }">
            <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                :class="{
                    'text-green-500':  flash.tipo === 'ok',
                    'text-red-500':    flash.tipo === 'error',
                    'text-yellow-500': flash.tipo === 'warn'
                }">
                <path x-show="flash.tipo === 'ok'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                <path x-show="flash.tipo === 'error'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                <path x-show="flash.tipo === 'warn'" fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 flex-1" x-text="flash.msg"></p>
            <button @click="flash.msg = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ══ LAYOUT ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

        {{-- ─── Columna izquierda ─── --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Buscador de serie --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Agregar bicicleta</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Escanea el QR o escribe el número de serie</p>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            x-model="serieInput"
                            @keyup.enter="buscarSerie()"
                            @input="serieInput = serieInput.toUpperCase()"
                            :disabled="buscando"
                            maxlength="17"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 font-mono uppercase tracking-widest transition"
                            placeholder="HE0EA2A00SA963753"
                            autocomplete="off"
                            x-ref="serieInputRef"
                        >
                        <button type="button" @click="buscarSerie()"
                            :disabled="buscando || !serieInput.trim()"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2 shrink-0">
                            <template x-if="!buscando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </template>
                            <template x-if="buscando">
                                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <span x-text="buscando ? 'Buscando...' : 'Buscar'"></span>
                        </button>
                    </div>

                    {{-- Preview bicicleta --}}
                    <div x-show="bikePreview" x-transition.opacity.duration.150ms>
                        <template x-if="bikePreview">
                            <div class="border border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/20 flex items-start gap-4">
                                <div class="shrink-0 mt-0.5">
                                    <template x-if="bikePreview.bici.color_hexes.length === 0">
                                        <div class="w-10 h-10 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-sm font-bold text-gray-500"
                                                  x-text="(bikePreview.bici.color_nombre || '?').charAt(0).toUpperCase()"></span>
                                        </div>
                                    </template>
                                    <template x-if="bikePreview.bici.color_hexes.length === 1">
                                        <div class="w-10 h-10 rounded-full border-2 border-black/10 dark:border-white/10"
                                             :style="'background:' + bikePreview.bici.color_hexes[0]"
                                             :title="bikePreview.bici.color_nombre"></div>
                                    </template>
                                    <template x-if="bikePreview.bici.color_hexes.length >= 2">
                                        <div class="w-10 h-10 rounded-full border-2 border-black/10 dark:border-white/10 overflow-hidden relative"
                                             :title="bikePreview.bici.color_nombre">
                                            <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + bikePreview.bici.color_hexes[0]"></div>
                                            <div class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + bikePreview.bici.color_hexes[1]"></div>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white"
                                       x-text="bikePreview.bici.marca + ' ' + bikePreview.bici.modelo"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono"
                                       x-text="bikePreview.bici.num_serie"></p>
                                    <p class="text-xs text-gray-400 mt-0.5"
                                       x-text="bikePreview.bici.voltaje + ' · ' + bikePreview.bici.color_nombre"></p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1"
                                       x-text="fmt(bikePreview.producto.precio)"></p>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-2">
                                    <button type="button" @click="agregarBicicleta()"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Agregar
                                    </button>
                                    <button type="button" @click="bikePreview = null; serieInput = ''; $refs.serieInputRef.focus()"
                                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Accesorios --}}
            @if($accesorios->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Accesorios</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Puedes agregar múltiples unidades</p>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($accesorios as $a)
                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $a->nombre_producto }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($a->precio, 2) }} c/u</p>
                        </div>
                        <button type="button"
                            @click="agregarAccesorio({
                                id_producto: '{{ $a->id_producto }}',
                                nombre:      '{{ addslashes($a->nombre_producto) }}',
                                precio:      {{ $a->precio }},
                            })"
                            class="shrink-0 flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:opacity-80 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ─── Columna derecha ─── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Carrito --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Carrito</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400" x-text="totalItems + ' producto(s)'"></span>
                        <template x-if="carrito.length > 0">
                            <button type="button" @click="vaciarCarrito()"
                                class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-300 transition">
                                Vaciar
                            </button>
                        </template>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700 min-h-[80px]">
                    <template x-if="carrito.length === 0">
                        <div class="px-5 py-10 text-center">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-xs text-gray-400">El carrito está vacío</p>
                        </div>
                    </template>

                    {{-- Items del carrito --}}
                    <template x-for="(item, idx) in carrito" :key="item.key">
                        <div class="flex items-center gap-3 px-5 py-3"
                             :class="item.es_gratis ? 'bg-green-50/50 dark:bg-green-900/10' : ''">

                            {{-- Ícono / color --}}
                            <div class="shrink-0">
                                {{-- Bicicleta con color --}}
                                <template x-if="item.tipo === '2' && item.color_hexes.length === 0">
                                    <div class="w-7 h-7 rounded-full border border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-500" x-text="(item.color_nombre || '?').charAt(0).toUpperCase()"></span>
                                    </div>
                                </template>
                                <template x-if="item.tipo === '2' && item.color_hexes.length === 1">
                                    <div class="w-7 h-7 rounded-full border border-black/10 dark:border-white/10"
                                         :style="'background:' + item.color_hexes[0]"></div>
                                </template>
                                <template x-if="item.tipo === '2' && item.color_hexes.length >= 2">
                                    <div class="w-7 h-7 rounded-full border border-black/10 dark:border-white/10 overflow-hidden relative">
                                        <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + item.color_hexes[0]"></div>
                                        <div class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + item.color_hexes[1]"></div>
                                    </div>
                                </template>
                                {{-- Accesorio normal --}}
                                <template x-if="item.tipo !== '2' && !item.es_gratis">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                    </div>
                                </template>
                                {{-- Accesorio gratis --}}
                                <template x-if="item.tipo !== '2' && item.es_gratis">
                                    <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-medium text-gray-800 dark:text-white truncate" x-text="item.nombre"></p>
                                    <template x-if="item.es_gratis">
                                        <span class="shrink-0 text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 uppercase tracking-wide">
                                            Gratis
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-400 font-mono truncate" x-show="item.num_serie" x-text="item.num_serie"></p>
                                {{--
                                    Si es gratis: tachamos precio original y mostramos $0.00
                                    Si no:        precio normal × cantidad
                                --}}
                                <template x-if="!item.es_gratis">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300"
                                       x-text="fmt(item.precio_unitario) + (item.cantidad > 1 ? ' × ' + item.cantidad : '')"></p>
                                </template>
                                <template x-if="item.es_gratis">
                                    <p class="text-xs text-gray-400">
                                        <span class="line-through" x-text="fmt(item.precio_unitario)"></span>
                                        <span class="ml-1 font-bold text-green-600 dark:text-green-400">$0.00</span>
                                    </p>
                                </template>
                            </div>

                            {{-- Controles de cantidad — solo accesorios NO gratis --}}
                            <template x-if="item.tipo !== '2' && !item.es_gratis">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" @click="decrementar(idx)"
                                        class="w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-bold flex items-center justify-center">
                                        −
                                    </button>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-5 text-center" x-text="item.cantidad"></span>
                                    <button type="button" @click="incrementar(idx)"
                                        class="w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-bold flex items-center justify-center">
                                        +
                                    </button>
                                </div>
                            </template>

                            {{-- Quitar — no permitir quitar el producto gratis que vino del cupón --}}
                            <button type="button" @click="quitar(idx)"
                                :disabled="item.es_gratis && item.origen_cupon"
                                :title="item.es_gratis && item.origen_cupon ? 'Quita el cupón para eliminar este regalo' : ''"
                                class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition p-1 disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    {{--
                        Producto gratis que NO estaba en el carrito → ítem visual adicional.
                        (Si ya estaba en carrito, se muestra inline con badge "Gratis" arriba)
                    --}}
                    <template x-if="productoGratisExtra">
                        <div class="flex items-center gap-3 px-5 py-3 bg-green-50/50 dark:bg-green-900/10">
                            <div class="shrink-0">
                                <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 dark:text-white truncate" x-text="productoGratisExtra.nombre_producto"></p>
                                <p class="text-[10px] text-green-600 dark:text-green-400 font-medium">Se añadirá gratis con el cupón</p>
                            </div>
                            <span class="text-xs font-bold text-green-600 dark:text-green-400 shrink-0">$0.00</span>
                        </div>
                    </template>
                </div>

                {{-- Cupón --}}
                <template x-if="carrito.length > 0">
                    <div class="px-5 py-3 border-t dark:border-gray-700">
                        <template x-if="!cupon">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <input
                                        type="text"
                                        x-model="cuponInput"
                                        @keyup.enter="aplicarCupon()"
                                        @input="cuponInput = cuponInput.toUpperCase()"
                                        :disabled="validandoCupon"
                                        placeholder="CÓDIGO DE CUPÓN"
                                        class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-xs bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono uppercase tracking-widest transition">
                                </div>
                                <button type="button" @click="aplicarCupon()"
                                    :disabled="validandoCupon || !cuponInput.trim()"
                                    class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap flex items-center gap-1.5">
                                    <template x-if="validandoCupon">
                                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                    </template>
                                    <span x-text="validandoCupon ? 'Validando...' : 'Aplicar'"></span>
                                </button>
                            </div>
                        </template>

                        {{-- Cupón aplicado --}}
                        <template x-if="cupon">
                            <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-semibold text-green-700 dark:text-green-400" x-text="cuponInput"></p>
                                        <p class="text-[10px] text-green-600 dark:text-green-500" x-text="cupon.nombre"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-green-700 dark:text-green-400"
                                          x-text="descuento > 0 ? '−' + fmt(descuento) : ''"></span>
                                    <button type="button" @click="quitarCupon()"
                                        class="text-gray-400 hover:text-red-500 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Total --}}
                <template x-if="carrito.length > 0">
                    <div class="px-5 py-3 border-t dark:border-gray-700 rounded-b-xl bg-gray-50 dark:bg-gray-700/30 space-y-1">
                        <template x-if="descuento > 0 || tieneGratisEnCarrito">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">Subtotal</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400" x-text="fmt(total)"></span>
                            </div>
                        </template>
                        <template x-if="descuento > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-green-600 dark:text-green-400">Descuento</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400" x-text="'−' + fmt(descuento)"></span>
                            </div>
                        </template>
                        {{-- Descuento por producto gratis ya en carrito --}}
                        <template x-if="tieneGratisEnCarrito && descuentoGratis > 0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-green-600 dark:text-green-400">Regalo (cupón)</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400" x-text="'−' + fmt(descuentoGratis)"></span>
                            </div>
                        </template>
                        <div class="flex items-center justify-between pt-1 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="fmt(totalConDescuento)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Formulario cliente --}}
            <form id="form-venta" method="POST" action="{{ route('ventas.store') }}" class="space-y-5">
                @csrf
                <div id="carrito-inputs"></div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="px-5 py-4 border-b dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Datos del cliente</h3>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre_cliente" value="{{ old('nombre_cliente') }}"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                       placeholder="Juan" required>
                                @error('nombre_cliente')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Primer apellido <span class="text-red-500">*</span></label>
                                <input type="text" name="apellido1" value="{{ old('apellido1') }}"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                       placeholder="García" required>
                                @error('apellido1')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Segundo apellido</label>
                            <input type="text" name="apellido2" value="{{ old('apellido2') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                   placeholder="López">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Teléfono <span class="text-red-500">*</span></label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                   placeholder="5512345678" required>
                            @error('telefono')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Correo</label>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                   placeholder="cliente@email.com">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Dirección</label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                                   placeholder="Calle, número, colonia">
                        </div>
                    </div>
                </div>

                
{{-- ─── Card: Vendedor ─── --}}
@if($personal->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="px-5 py-4 border-b dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Vendedor</h3>
        <p class="text-xs text-gray-400 mt-0.5">¿Quién realiza esta venta?</p>
    </div>
    <div class="px-5 py-4">
        <select
            x-model="idPersonal"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm
                   bg-white dark:bg-gray-900 text-gray-900 dark:text-white
                   focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
            <option value="">— Sin especificar —</option>
            @foreach($personal as $p)
                <option value="{{ $p->id_personal }}">{{ $p->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif

{{-- ─── Card: Método de pago ─── --}}
<div x-show="carrito.length > 0"
     class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">

    <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Método de pago</h3>
            <p class="text-xs text-gray-400 mt-0.5">Puedes dividir en varios métodos</p>
        </div>
        <button type="button" @click="agregarPago()"
            :disabled="metodosPendientes.length === 0"
            class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline
                   disabled:opacity-30 disabled:no-underline disabled:cursor-not-allowed transition">
            + Agregar método
        </button>
    </div>

    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        <template x-for="(pago, i) in pagos" :key="i">
            <div class="px-5 py-3 space-y-2">
                <div class="flex items-center gap-2">
                    {{-- Selector de método --}}
                    <select x-model="pago.id_metodo"
                            @change="onMetodoChange(i)"
                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600
                                   px-3 py-2 text-sm bg-white dark:bg-gray-900
                                   text-gray-900 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        <option value="">— Método —</option>
                        @foreach($metodos as $m)
                            <option value="{{ $m->id_metodo }}"
                                    data-efectivo="{{ $m->es_efectivo ? '1' : '0' }}"
                                    data-ref="{{ $m->requiere_referencia ? '1' : '0' }}">
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Monto --}}
                    <div class="relative w-32 shrink-0">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">$</span>
                        <input type="number" step="0.01" min="0"
                               x-model="pago.monto"
                               @input="recalcularCambio()"
                               class="w-full pl-6 pr-2 py-2 rounded-lg border border-gray-300
                                      dark:border-gray-600 text-sm bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                    </div>

                    {{-- Quitar (solo si hay más de uno) --}}
                    <button type="button" @click="quitarPago(i)"
                        x-show="pagos.length > 1"
                        class="shrink-0 text-gray-300 hover:text-red-500 transition p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Referencia (si aplica) --}}
                <div x-show="pago.requiere_referencia" x-transition.opacity>
                    <input type="text" x-model="pago.referencia"
                           placeholder="Referencia (folio, últimos 4 dígitos, etc.)"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  px-3 py-2 text-sm bg-white dark:bg-gray-900
                                  text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                </div>

                {{-- Monto recibido + cambio (solo efectivo) --}}
                <div x-show="pago.es_efectivo" x-transition.opacity class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">$</span>
                            <input type="number" step="0.01" min="0"
                                   x-model="montoRecibido"
                                   @input="recalcularCambio()"
                                   placeholder="Monto recibido"
                                   class="w-full pl-6 pr-2 py-2 rounded-lg border border-gray-300
                                          dark:border-gray-600 text-sm bg-white dark:bg-gray-900
                                          text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        </div>
                        <div class="w-32 shrink-0">
                            <div class="rounded-lg px-3 py-2 text-sm font-semibold text-center"
                                 :class="cambio >= 0
                                     ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400'
                                     : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400'">
                                <span class="text-xs font-normal">Cambio </span>
                                <span x-text="fmt(Math.max(0, cambio))"></span>
                            </div>
                        </div>
                    </div>
                    <template x-if="cambio < 0">
                        <p class="text-xs text-red-500 dark:text-red-400">
                            Faltan <span x-text="fmt(Math.abs(cambio))"></span> por cubrir.
                        </p>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Resumen de pagos vs total --}}
    <div class="px-5 py-3 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 rounded-b-xl">
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
            <span>Total a cobrar</span>
            <span class="font-semibold" x-text="fmt(totalConDescuento)"></span>
        </div>
        <div class="flex items-center justify-between text-xs mb-2"
             :class="pagosCubreTotal
                 ? 'text-green-600 dark:text-green-400'
                 : 'text-red-500 dark:text-red-400'">
            <span>Total ingresado</span>
            <span class="font-semibold" x-text="fmt(sumaPagos)"></span>
        </div>

        {{-- Diferencia si no cubre --}}
        <template x-if="!pagosCubreTotal">
            <p class="text-xs text-red-500 dark:text-red-400 mb-2">
                Faltan <span x-text="fmt(totalConDescuento - sumaPagos)"></span> por asignar.
            </p>
        </template>
    </div>
</div>

{{-- ─── Botón final ─── --}}
<button type="button" @click="submitVenta()"
    :disabled="carrito.length === 0 || enviando || !pagosCubreTotal"
    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3
           rounded-xl text-sm font-semibold hover:opacity-90 transition
           disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
    <template x-if="!enviando">
        <span x-text="'Registrar venta · ' + fmt(totalConDescuento)"></span>
    </template>
    <template x-if="enviando">
        <span class="flex items-center gap-2">
            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Registrando...
        </span>
    </template>
</button>
            </form>
        </div>
    </div>
</div>

<script>
function ventaCreate() {
    return {
        serieInput:     '',
        buscando:       false,
        enviando:       false,
        bikePreview:    null,
        carrito:        [],
        flash:          { msg: '', tipo: 'warn', _t: null },
        buscarUrl:      '',
        cuponUrl:       '',
        csrfToken:      '{{ csrf_token() }}',

        // ── Cupón ─────────────────────────────────────────────────────────────
        cuponInput:          '',
        validandoCupon:      false,
        cupon:               null,   // datos del cupón validado
        descuento:           0,      // descuento en $ calculado por el backend
        /**
         * productoGratisExtra:
         *   Si el producto gratis NO estaba en el carrito → se muestra aquí como
         *   ítem visual adicional (el backend lo agrega al guardar la venta).
         *   Si YA estaba en el carrito → null aquí; el ítem del carrito lleva
         *   es_gratis=true y origen_cupon=true.
         */
        productoGratisExtra: null,

        //Pagos
        idPersonal:     '',    // id_personal seleccionado
        pagos:          [],    // array de { id_metodo, monto, referencia, es_efectivo, requiere_referencia }
        montoRecibido:  '',    // solo efectivo
        cambio:         0,

        init() {
            this.buscarUrl = document.querySelector('[data-buscar-url]')?.dataset?.buscarUrl ?? '';
            this.cuponUrl  = document.querySelector('[data-cupon-url]')?.dataset?.cuponUrl ?? '';
            this.$nextTick(() => this.$refs.serieInputRef?.focus());
            this.initPagos();
        },

        // ── Helpers de carrito ────────────────────────────────────────────────

        /**
         * ¿Hay algún ítem en el carrito marcado como gratis por el cupón?
         * Se usa para mostrar la fila de "Regalo (cupón)" en el resumen.
         */
        get tieneGratisEnCarrito() {
            return this.carrito.some(i => i.es_gratis && i.origen_cupon);
        },

        /**
         * Precio del producto que está marcado como gratis en el carrito.
         * Se descuenta del total para que el cliente pague $0 por él.
         */
        get descuentoGratis() {
            return this.carrito
                .filter(i => i.es_gratis && i.origen_cupon)
                .reduce((s, i) => s + i.precio_unitario * i.cantidad, 0);
        },

        get sumaPagos() {
            return this.pagos.reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
        },
 
        get pagosCubreTotal() {
            return this.pagos.length > 0
                && round2(this.sumaPagos) >= round2(this.totalConDescuento);
        },
 
        // Métodos que aún no están en pagos[] (para el botón "+ Agregar método")
        // Solo evita duplicar efectivo — los demás pueden repetirse (ej. 2 tarjetas distintas)
        get metodosPendientes() {
            const tieneEfectivo = this.pagos.some(p => p.es_efectivo);
            // Retornamos al menos un slot disponible salvo que todos sean únicos y ya estén
            // La lógica real de "si quedan métodos por agregar" la manejamos con:
            // si la diferencia entre total y sumaPagos > 0 → hay pendiente
            return this.totalConDescuento - this.sumaPagos > 0 ? [true] : [];
        },

        // Llamado en init() — agrega el primer pago vacío
        initPagos() {
            this.pagos = [{ id_metodo: '', monto: '', referencia: '', es_efectivo: false, requiere_referencia: false }];
        },
 
        agregarPago() {
            this.pagos.push({ id_metodo: '', monto: '', referencia: '', es_efectivo: false, requiere_referencia: false });
        },
 
        quitarPago(idx) {
            if (this.pagos.length <= 1) return;
            this.pagos.splice(idx, 1);
            this.recalcularCambio();
        },
 
        onMetodoChange(idx) {
            const select = document.querySelectorAll('[x-model="pago.id_metodo"]')[idx];
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;
 
            this.pagos[idx].es_efectivo         = opt.dataset.efectivo === '1';
            this.pagos[idx].requiere_referencia  = opt.dataset.ref      === '1';
 
            // Auto-rellenar monto con el restante si es el último pago
            const restante = round2(this.totalConDescuento - this.sumaPagos + (parseFloat(this.pagos[idx].monto) || 0));
            if (restante > 0) this.pagos[idx].monto = restante.toFixed(2);
 
            this.recalcularCambio();
        },
 
        recalcularCambio() {
            const tieneEfectivo = this.pagos.some(p => p.es_efectivo);
            if (!tieneEfectivo) { this.cambio = 0; return; }
 
            const recibido = parseFloat(this.montoRecibido) || 0;
            this.cambio    = round2(recibido - this.totalConDescuento);
        },

        // ── Bicicletas ────────────────────────────────────────────────────────
        async buscarSerie() {
            const serie = this.serieInput.trim().toUpperCase();
            if (!serie || this.buscando) return;

            if (this.carrito.find(i => i.num_serie === serie)) {
                this.mostrarFlash('Esta bicicleta ya está en el carrito.', 'warn');
                return;
            }

            this.buscando    = true;
            this.bikePreview = null;

            try {
                const res  = await fetch(this.buscarUrl + '?num_serie=' + encodeURIComponent(serie), {
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!data.ok) {
                    this.mostrarFlash(data.mensaje ?? 'No encontrada.', 'error');
                    return;
                }

                this.bikePreview = data;
                this.agregarBicicleta();

            } catch {
                this.mostrarFlash('Error de conexión.', 'error');
            } finally {
                this.buscando = false;
            }
        },

        agregarBicicleta() {
            if (!this.bikePreview) return;
            const { bici, producto } = this.bikePreview;

            this.carrito.push({
                key:             bici.num_serie,
                tipo:            '2',
                id_producto:     producto.id_producto,
                num_serie:       bici.num_serie,
                nombre:          bici.marca + ' ' + bici.modelo,
                precio_unitario: producto.precio,
                cantidad:        1,
                color_hexes:     bici.color_hexes,
                color_nombre:    bici.color_nombre,
                es_gratis:       false,
                origen_cupon:    false,
            });

            this.bikePreview = null;
            this.serieInput  = '';
            this.$nextTick(() => this.$refs.serieInputRef?.focus());
            this.mostrarFlash('Bicicleta agregada al carrito.', 'ok');

            if (this.cupon) this.recalcularDescuento();
        },

        // ── Accesorios ────────────────────────────────────────────────────────
        agregarAccesorio(acc) {
            const existente = this.carrito.find(
                i => i.id_producto === acc.id_producto && !i.es_gratis
            );
            if (existente) {
                existente.cantidad++;
                this.mostrarFlash('Unidad adicional agregada.', 'ok');
            } else {
                this.carrito.push({
                    key:             acc.id_producto,
                    tipo:            '1',
                    id_producto:     acc.id_producto,
                    num_serie:       '',
                    nombre:          acc.nombre,
                    precio_unitario: parseFloat(acc.precio),
                    cantidad:        1,
                    color_hexes:     [],
                    color_nombre:    '',
                    es_gratis:       false,
                    origen_cupon:    false,
                });
                this.mostrarFlash('Accesorio agregado al carrito.', 'ok');
            }

            if (this.cupon) this.recalcularDescuento();
        },

        incrementar(idx) {
            if (this.carrito[idx].es_gratis) return; // no tocar gratuito
            this.carrito[idx].cantidad++;
            if (this.cupon) this.recalcularDescuento();
        },

        decrementar(idx) {
            if (this.carrito[idx].es_gratis) return;
            if (this.carrito[idx].cantidad <= 1) {
                this.quitar(idx);
                return;
            }
            this.carrito[idx].cantidad--;
            if (this.cupon) this.recalcularDescuento();
        },

        quitar(idx) {
            if (this.carrito[idx].es_gratis && this.carrito[idx].origen_cupon) return;
            this.carrito.splice(idx, 1);
            if (this.cupon) this.recalcularDescuento();
        },

        vaciarCarrito() {
            this.carrito             = [];
            this.cupon               = null;
            this.descuento           = 0;
            this.cuponInput          = '';
            this.productoGratisExtra = null;
        },

        // ── Cupón ─────────────────────────────────────────────────────────────

        /**
         * Aplicar cupón por primera vez.
         */
        async aplicarCupon() {
            if (!this.cuponInput.trim() || this.validandoCupon || this.carrito.length === 0) return;
            this.validandoCupon = true;

            try {
                const data = await this._llamarValidar();

                if (!data.valido) {
                    this.mostrarFlash(data.mensaje || 'Cupón no válido.', 'error');
                    return;
                }

                this.cupon    = data.cupon;
                this.descuento = data.descuento;
                this._aplicarProductoGratis(data);
                this.mostrarFlash(data.mensaje, 'ok');

            } catch {
                this.mostrarFlash('Error al validar el cupón.', 'error');
            } finally {
                this.validandoCupon = false;
            }
        },

        quitarCupon() {
            // Revertir cualquier ítem marcado como gratis por el cupón
            this.carrito = this.carrito.filter(i => !i.origen_cupon);

            this.cupon               = null;
            this.descuento           = 0;
            this.cuponInput          = '';
            this.productoGratisExtra = null;
        },

        /**
         * Recalcular cuando cambia el carrito con cupón ya aplicado.
         */
        async recalcularDescuento() {
            if (!this.cupon || this.carrito.length === 0) {
                this.descuento           = 0;
                this.productoGratisExtra = null;
                // Limpiar ítems gratis del carrito
                this.carrito = this.carrito.filter(i => !i.origen_cupon);
                return;
            }

            try {
                const data = await this._llamarValidar();

                if (data.valido) {
                    this.descuento = data.descuento;
                    this._aplicarProductoGratis(data);
                } else {
                    // Cupón dejó de ser válido (ej. ya no cumple cantidad mínima)
                    this.mostrarFlash(data.mensaje || 'El cupón ya no aplica.', 'warn');
                    this.quitarCupon();
                }
            } catch { /* silencioso */ }
        },

        /**
         * Lógica central: ¿qué hacer con el producto gratis según si ya está en carrito?
         *
         * Caso A — gratis_ya_en_carrito = true:
         *   El producto ya está en el carrito. Marcamos UNA unidad como gratis
         *   (es_gratis + origen_cupon) para que se muestre con badge y precio tachado.
         *   El descuentoGratis computed lo resta del total.
         *
         * Caso B — gratis_ya_en_carrito = false:
         *   No está en el carrito. Lo mostramos en productoGratisExtra como ítem
         *   visual separado ("Se añadirá gratis con el cupón"). El backend lo agrega
         *   al guardar; el frontend no lo mete en el carrito para evitar dobles.
         */
        _aplicarProductoGratis(data) {
            if (!data.producto_gratis) {
                // Limpiar estado anterior si existía
                this.productoGratisExtra = null;
                this.carrito = this.carrito.filter(i => !i.origen_cupon);
                return;
            }

            const pg = data.producto_gratis;

            if (data.gratis_ya_en_carrito) {
                // ── Caso A: marcar ítem existente como gratis ─────────────────
                this.productoGratisExtra = null;

                // Desmarcar cualquier gratis anterior (por si cambió el cupón)
                this.carrito.forEach(i => {
                    if (i.origen_cupon) {
                        i.es_gratis    = false;
                        i.origen_cupon = false;
                    }
                });

                // Marcar la primera unidad del producto como gratis
                const item = this.carrito.find(i => i.id_producto === pg.id_producto && !i.es_gratis);
                if (item) {
                    item.es_gratis    = true;
                    item.origen_cupon = true;
                }
            } else {
                // ── Caso B: producto gratis fuera del carrito ─────────────────
                // Asegurarnos de limpiar cualquier marca anterior del carrito
                this.carrito.forEach(i => {
                    if (i.origen_cupon) {
                        i.es_gratis    = false;
                        i.origen_cupon = false;
                    }
                });
                this.productoGratisExtra = pg;
            }
        },

        /**
         * Llamada HTTP reutilizable al endpoint de validación.
         * Siempre envía los ítems del carrito que NO son de origen_cupon
         * (para no contaminar el cálculo con el gratis ya marcado).
         */
        async _llamarValidar() {
            const itemsReales = this.carrito
                .filter(i => !i.origen_cupon)
                .map(i => ({ id_producto: i.id_producto, cantidad: i.cantidad }));

            const res = await fetch(this.cuponUrl, {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept':       'application/json',
                },
                body: JSON.stringify({
                    codigo: this.cuponInput.trim().toUpperCase(),
                    items:  itemsReales,
                }),
            });
            return res.json();
        },

        // ── Totales ───────────────────────────────────────────────────────────

        /** Suma bruta de todos los ítems (incluido el marcado como gratis) */
        get total() {
            return this.carrito.reduce((s, i) => s + (i.precio_unitario * i.cantidad), 0);
        },

        /** Total a cobrar = total bruto − descuento de porcentaje/monto − precio del regalo */
        get totalConDescuento() {
            return Math.max(0, this.total - this.descuento - this.descuentoGratis);
        },

        get totalItems() {
            return this.carrito.reduce((s, i) => s + i.cantidad, 0);
        },

        fmt(n) {
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n);
        },

        // ── Submit ────────────────────────────────────────────────────────────
        submitVenta() {
            if (this.carrito.length === 0 || this.enviando || !this.pagosCubreTotal) return;
 
            const cont = document.getElementById('carrito-inputs');
            cont.innerHTML = '';
 
            const mk = (name, val) => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = name;
                inp.value = val ?? '';
                cont.appendChild(inp);
            };
 
            // Ítems del carrito (igual que antes)
            this.carrito.forEach((item, i) => {
                mk(`items[${i}][id_producto]`, item.id_producto);
                mk(`items[${i}][num_serie]`,   item.num_serie);
                mk(`items[${i}][cantidad]`,     item.cantidad);
                mk(`items[${i}][es_gratis]`,    item.es_gratis ? '1' : '0');
            });
 
            // Cupón
            if (this.cupon) mk('codigo_cupon', this.cuponInput.trim().toUpperCase());
 
            // Vendedor
            if (this.idPersonal) mk('id_personal', this.idPersonal);
 
            // Pagos
            this.pagos.forEach((pago, i) => {
                mk(`pagos[${i}][id_metodo]`,  pago.id_metodo);
                mk(`pagos[${i}][monto]`,       parseFloat(pago.monto).toFixed(2));
                mk(`pagos[${i}][referencia]`,  pago.referencia || '');
            });
 
            // Efectivo
            const tieneEfectivo = this.pagos.some(p => p.es_efectivo);
            if (tieneEfectivo) {
                mk('monto_recibido', parseFloat(this.montoRecibido || 0).toFixed(2));
                mk('cambio',         Math.max(0, this.cambio).toFixed(2));
            }
 
            this.enviando = true;
            document.getElementById('form-venta').submit();
        },

        // ── Flash ─────────────────────────────────────────────────────────────
        mostrarFlash(msg, tipo = 'warn') {
            if (this.flash._t) clearTimeout(this.flash._t);
            this.flash.msg  = msg;
            this.flash.tipo = tipo;
            this.flash._t   = setTimeout(() => { this.flash.msg = ''; }, 2000);
        },
    };
}
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const idVendedor = document.querySelector('[data-vendedor-id]')?.dataset?.vendedorId;
    if (!idVendedor || !window.Echo) return;

    window.Echo.private(`user.${idVendedor}`)
        .listen('.venta.realizada', (e) => {
            window.dispatchEvent(new CustomEvent('venta-realizada', { detail: e }));
        });
});

function round2(n) { return Math.round(n * 100) / 100; }
</script>

<style>[x-cloak] { display: none !important; }</style>
</x-app-layout>