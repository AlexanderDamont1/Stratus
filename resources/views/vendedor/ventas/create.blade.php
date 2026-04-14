<x-app-layout>
<div
    x-data="ventaCreate()"
    x-init="init()"
    class="space-y-6 max-w-6xl mx-auto"
    data-vendedor-id="{{ auth()->user()->id_usuario }}"
    data-buscar-url="{{ route('ventas.buscar-serie') }}"
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

    {{-- Flash Alpine --}}
    <div x-show="flash.msg" x-cloak x-transition.opacity.duration.200ms
        :class="{
            'bg-yellow-50 border-yellow-200 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-700 dark:text-yellow-400': flash.tipo === 'warn',
            'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-700 dark:text-red-400': flash.tipo === 'error',
            'bg-green-50 border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-700 dark:text-green-400': flash.tipo === 'ok',
        }"
        class="border text-sm px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  x-bind:d="flash.tipo === 'ok'
                    ? 'M5 13l4 4L19 7'
                    : flash.tipo === 'error'
                    ? 'M6 18L18 6M6 6l12 12'
                    : 'M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z'"/>
        </svg>
        <span x-text="flash.msg"></span>
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
                                {{-- Color --}}
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
                                {{-- Info --}}
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
                                {{-- Acción --}}
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
                            <button type="button" @click="carrito = []"
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

                    <template x-for="(item, idx) in carrito" :key="item.key">
                        <div class="flex items-center gap-3 px-5 py-3">
                            {{-- Icono/color --}}
                            <div class="shrink-0">
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
                                <template x-if="item.tipo !== '2'">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 dark:text-white truncate" x-text="item.nombre"></p>
                                <p class="text-xs text-gray-400 font-mono truncate" x-show="item.num_serie" x-text="item.num_serie"></p>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300"
                                   x-text="fmt(item.precio_unitario) + (item.cantidad > 1 ? ' × ' + item.cantidad : '')"></p>
                            </div>

                            {{-- Cantidad (solo accesorios) --}}
                            <template x-if="item.tipo !== '2'">
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

                            {{-- Quitar --}}
                            <button type="button" @click="quitar(idx)"
                                class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Total --}}
                <template x-if="carrito.length > 0">
                    <div class="px-5 py-3 border-t dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 rounded-b-xl">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="fmt(total)"></span>
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

                <button type="button" @click="submitVenta()"
                    :disabled="carrito.length === 0 || enviando"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3 rounded-xl text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <template x-if="!enviando">
                        <span>Registrar venta</span>
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
        serieInput:  '',
        buscando:    false,
        enviando:    false,
        bikePreview: null,
        carrito:     [],
        flash:       { msg: '', tipo: 'warn', _t: null },
        buscarUrl:   '',
        csrfToken:   '{{ csrf_token() }}',

        init() {
            this.buscarUrl = document.querySelector('[data-buscar-url]')?.dataset?.buscarUrl ?? '';
            this.$nextTick(() => this.$refs.serieInputRef?.focus());
        },

        /* ─── Buscar bicicleta ─── */
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

                // Auto-agregar si ya teníamos el input lleno (scan QR)
                this.bikePreview = data;
                this.agregarBicicleta();

            } catch (e) {
                this.mostrarFlash('Error de conexión.', 'error');
            } finally {
                this.buscando = false;
            }
        },

        /* ─── Agregar bicicleta ─── */
        agregarBicicleta() {
            if (!this.bikePreview) return;
            const { bici, producto } = this.bikePreview;

            this.carrito.push({
                key:           bici.num_serie,
                tipo:          '2',
                id_producto:   producto.id_producto,
                num_serie:     bici.num_serie,
                nombre:        bici.marca + ' ' + bici.modelo,
                precio_unitario: producto.precio,
                cantidad:      1,
                color_hexes:   bici.color_hexes,
                color_nombre:  bici.color_nombre,
            });

            this.bikePreview = null;
            this.serieInput  = '';
            this.$nextTick(() => this.$refs.serieInputRef?.focus());
            this.mostrarFlash('Bicicleta agregada al carrito.', 'ok');
        },

        /* ─── Agregar accesorio ─── */
        agregarAccesorio(acc) {
            const existente = this.carrito.find(i => i.id_producto === acc.id_producto);
            if (existente) {
                existente.cantidad++;
                this.mostrarFlash('Unidad adicional agregada.', 'ok');
                return;
            }
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
            });
            this.mostrarFlash('Accesorio agregado al carrito.', 'ok');
        },

        /* ─── Cantidad accesorios ─── */
        incrementar(idx) {
            this.carrito[idx].cantidad++;
        },

        decrementar(idx) {
            if (this.carrito[idx].cantidad <= 1) {
                this.quitar(idx);
                return;
            }
            this.carrito[idx].cantidad--;
        },

        quitar(idx) {
            this.carrito.splice(idx, 1);
        },

        /* ─── Totales ─── */
        get total() {
            return this.carrito.reduce((s, i) => s + (i.precio_unitario * i.cantidad), 0);
        },

        get totalItems() {
            return this.carrito.reduce((s, i) => s + i.cantidad, 0);
        },

        /* ─── Formato ─── */
        fmt(n) {
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n);
        },

        /* ─── Submit ─── */
        submitVenta() {
            if (this.carrito.length === 0 || this.enviando) return;

            const cont = document.getElementById('carrito-inputs');
            cont.innerHTML = '';

            this.carrito.forEach((item, i) => {
                const mk = (name, val) => {
                    const inp = document.createElement('input');
                    inp.type  = 'hidden';
                    inp.name  = name;
                    inp.value = val ?? '';
                    cont.appendChild(inp);
                };
                mk(`items[${i}][id_producto]`, item.id_producto);
                mk(`items[${i}][num_serie]`,   item.num_serie);
                mk(`items[${i}][cantidad]`,     item.cantidad);
            });

            this.enviando = true;
            document.getElementById('form-venta').submit();
        },

        /* ─── Flash ─── */
        mostrarFlash(msg, tipo = 'warn') {
            if (this.flash._t) clearTimeout(this.flash._t);
            this.flash.msg  = msg;
            this.flash.tipo = tipo;
            this.flash._t   = setTimeout(() => { this.flash.msg = ''; }, 3500);
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
</script>

<style>[x-cloak] { display: none !important; }</style>
</x-app-layout>