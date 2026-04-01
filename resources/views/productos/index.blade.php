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
        <button @click="abrirCrear()"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap hover:scale-105 transform duration-200 active:scale-95">
            + Nuevo producto
        </button>
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
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($bicicletas as $idModelo => $variantes)
            @php
                $primera = $variantes->first();
                $pm      = $primera->productoModelo?->first();
                $precios = $variantes->pluck('precio');
                $min     = $precios->min();
                $max     = $precios->max();
            @endphp

            {{-- Tarjeta con efecto de elevación y sombra --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-3.5 flex flex-col gap-2.5 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                 x-data="{ variantesOpen: false }">

                {{-- TOP: badge + contador variantes (mejorado) --}}
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-purple-100/80 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-purple-200/50 dark:border-purple-700/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Bicicleta
                    </span>
                    <span class="text-[8px] sm:text-[10px] font-medium bg-emerald-100/80 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 px-1.5 sm:px-2 py-0.5 rounded-full">
                        {{ $variantes->count() }} {{ $variantes->count() === 1 ? 'variante' : 'variantes' }}
                    </span>
                </div>

                {{-- NOMBRE MODELO --}}
                <p class="text-sm font-semibold text-gray-800 dark:text-white leading-snug">
                    
                    {{$pm?->modelo->marca->nombre_marca ?? 'Error' }}
                    <span class="text-sm text-gray-400">~</span>
                    {{ $pm?->modelo->nombre_modelo ?? $primera->nombre_producto }}

                </p>

                {{-- RANGO DE PRECIOS con indicador "Desde" --}}
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if($min == $max)
                        ${{ number_format($min, 2) }}
                    @else
                        
                        ${{ number_format($min, 2) }}
                        <span class="text-sm text-gray-400">~</span>
                        ${{ number_format($max, 2) }}
                    @endif
                </p>

                {{-- BOTTOM: variantes (con texto dinámico) --}}
                <div class="flex items-center gap-2 mt-auto">
                    <button @click="variantesOpen = !variantesOpen"
                        class="flex items-center gap-1.5 flex-1 text-[11px] font-medium text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition active:scale-95">
                        <span x-text="variantesOpen ? 'Ocultar variantes' : 'Variantes'"></span>
                        <svg class="w-3.5 h-3.5 ml-auto transition-transform duration-200 flex-shrink-0"
                            :class="variantesOpen ? 'rotate-90' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- VARIANTES DESPLEGABLES (con animación más suave) --}}
                <div x-show="variantesOpen" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                    class="border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden">

                    @foreach($variantes as $v)
                    @php $vpm = $v->productoModelo?->first(); @endphp
                    <div class="flex items-center gap-2 px-2.5 py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <span class="text-[10px] font-medium bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-0.5 rounded-full flex-shrink-0">
                            {{ $vpm?->voltaje?->voltaje ?? '—' }}
                        </span>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 flex-1">
                            ${{ number_format($v->precio, 2) }}
                        </span>
                        {{-- GEAR por variante (con efecto hover ampliado) --}}
                        <button @click.stop="abrirEditar(
                                '{{ $v->id_producto }}',
                                '{{ addslashes($v->nombre_producto) }}',
                                '{{ $v->tipo }}',
                                '{{ $v->precio }}'
                            )"
                            class="w-7 h-7 sm:w-6 sm:h-6 flex items-center justify-center rounded-md border border-gray-200 dark:border-gray-600 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition hover:scale-105 active:scale-95">
                            <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <circle cx="12" cy="12" r="3" stroke-width="2"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach

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
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            
            @foreach($accesorios as $producto)

            {{-- Tarjeta con efecto de elevación y sombra --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-3.5 flex flex-col gap-2.5 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5"
                 id="producto-row-{{ $producto->id_producto }}">

                {{-- TOP: badge mejorado --}}
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-amber-100/80 dark:bg-amber-900/40 text-amber-900 dark:text-amber-400 text-[11px] font-semibold px-2.5 py-1 rounded-full border border-amber-200/50 dark:border-amber-700/50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Accesorio
                    </span>
                </div>

                {{-- NOMBRE --}}
                <p class="text-sm font-semibold text-gray-800 dark:text-white leading-snug">
                    {{ $producto->nombre_producto }}
                </p>

                {{-- PRECIO (simple) --}}
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    ${{ number_format($producto->precio, 2) }}
                </p>

                {{-- GEAR con efecto hover y escalado --}}
                <div class="flex justify-end mt-auto">
                    <button @click.stop="abrirEditar(
                            '{{ $producto->id_producto }}',
                            '{{ addslashes($producto->nombre_producto) }}',
                            '{{ $producto->tipo }}',
                            '{{ $producto->precio }}'
                        )"
                        class="w-9 h-9 sm:w-8 sm:h-8 flex items-center justify-center border border-gray-200 dark:border-gray-600 rounded-lg text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-600 dark:hover:text-gray-300 transition hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
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
         MODAL: CREAR (sin cambios visuales)
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
                    <button type="submit"
                        :disabled="submitting || !accesorioValido"
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

                {{-- PASO 1: MARCA --}}
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

                {{-- PASO 2: MODELO --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                    <div x-show="loadingModelos" class="text-xs text-gray-400 py-2 px-1">Cargando modelos...</div>
                    <select x-show="!loadingModelos"
                        x-model="form.id_modelo"
                        @change="cargarVoltajes($event.target.value)"
                        :disabled="modelosDisponibles.length === 0"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 disabled:opacity-50">
                        <option value="" x-text="!form.id_marca ? 'Primero selecciona una marca' : (modelosDisponibles.length === 0 && form.id_marca ? 'Sin modelos disponibles' : 'Selecciona un modelo')"></option>
                        <template x-for="m in modelosDisponibles" :key="m.id_modelo">
                            <option :value="m.id_modelo" x-text="m.nombre_modelo"></option>
                        </template>
                    </select>
                </div>

                {{-- PASO 3: VOLTAJE --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                    <div x-show="loadingVoltajes" class="text-xs text-gray-400 py-2 px-1">Cargando voltajes...</div>
                    <select x-show="!loadingVoltajes"
                        x-model="form.id_voltaje"
                        :disabled="voltajesDisponibles.length === 0"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 disabled:opacity-50">
                        <option value=""
                            x-text="!form.id_modelo
                                ? 'Primero selecciona un modelo'
                                : (voltajesDisponibles.length === 0
                                    ? 'Todos los voltajes ya tienen precio asignado'
                                    : 'Selecciona un voltaje')">
                        </option>
                        <template x-for="v in voltajesDisponibles" :key="v.id_voltaje">
                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                        </template>
                    </select>
                    {{-- Aviso cuando no hay voltajes disponibles --}}
                    <p x-show="form.id_modelo && !loadingVoltajes && voltajesDisponibles.length === 0"
                       class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                        Este modelo ya tiene precio asignado para todos sus voltajes.
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
                    <button type="submit"
                        :disabled="submitting || !bicicletaValida"
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
         MODAL: EDITAR (sin cambios visuales)
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
                    class="w-8 h-8 flex items-center justify-center border border-red-200 dark:border-red-800 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition active:scale-95">
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
                    <button type="submit"
                        :disabled="submitting || !editFormModificado"
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
         MODAL: ELIMINAR (sin cambios visuales)
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
         ALPINE.JS (sin cambios)
    ══════════════════════════════════════════ --}}
    <script>
    function productosPage() {
        return {
            crearModal:    false,
            editarModal:   false,
            eliminarModal: false,

            submitting:           false,
            flashVisible:         false,
            flashMsg:             '',
            flashTipo:            'success',
            flashTimer:           null,
            loadingModelos:       false,
            loadingVoltajes:      false,
            modelosDisponibles:   [],
            voltajesDisponibles:  [],

            form: {
                tipo:            '1',
                id_usuario:      '',
                nombre_producto: '',
                id_marca:        '',
                id_modelo:       '',
                id_voltaje:      '',
                precio:          '',
            },

            // Snapshot del form al abrir editar (para detectar cambios)
            editFormOriginal: {
                nombre_producto: '',
                precio:          '',
            },

            editForm: {
                id_producto:     '',
                nombre_producto: '',
                tipo:            '',
                precio:          '',
            },

            deleteTarget: { id: '', nombre: '' },

            // ── Computed: botón Guardar accesorio activo sólo si hay datos reales ──
            get accesorioValido() {
                const tieneNombre = this.form.nombre_producto.trim().length > 0;
                const tienePrecio = this.form.precio !== '' && Number(this.form.precio) >= 0;
                @if($esRol1)
                return tieneNombre && tienePrecio && this.form.id_usuario !== '';
                @else
                return tieneNombre && tienePrecio;
                @endif
            },

            // ── Computed: botón Guardar bicicleta activo sólo si todo está seleccionado ──
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

            // ── Computed: botón Guardar cambios activo sólo si algo cambió ──
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

            init() {},

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
                this.editForm = { id_producto: id, nombre_producto: nombre, tipo: tipo, precio: precio };
                // Guardar snapshot para comparar cambios
                this.editFormOriginal = { nombre_producto: nombre, precio: precio };
                this.editarModal = true;
            },

            abrirEliminar(id, nombre) {
                this.deleteTarget = { id, nombre };
                this.eliminarModal = true;
            },

            // ── Cargar modelos al elegir marca ──
            async cargarModelos(idMarca) {
                this.form.id_modelo      = '';
                this.form.id_voltaje     = '';
                this.modelosDisponibles  = [];
                this.voltajesDisponibles = [];
                if (!idMarca) return;
                this.loadingModelos = true;
                try {
                    const res = await fetch(this.routes.modelosPorMarca(idMarca), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Error al cargar modelos');
                    this.modelosDisponibles = await res.json();
                } catch (e) {
                    this.flash('Error cargando modelos.', 'error');
                } finally {
                    this.loadingModelos = false;
                }
            },

            // ── Cargar voltajes disponibles al elegir modelo ──
            async cargarVoltajes(idModelo) {
                this.form.id_voltaje     = '';
                this.voltajesDisponibles = [];
                if (!idModelo) return;
                this.loadingVoltajes = true;
                try {
                    const res = await fetch(this.routes.voltajes(idModelo), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Error al cargar voltajes');
                    this.voltajesDisponibles = await res.json();
                } catch (e) {
                    this.flash('Error cargando voltajes.', 'error');
                } finally {
                    this.loadingVoltajes = false;
                }
            },

            async submitAccesorio() {
                @if($esRol1)
                if (!this.form.id_usuario)            { this.flash('Selecciona una sucursal.', 'error');          return; }
                @endif
                if (!this.form.nombre_producto.trim()) { this.flash('Ingresa el nombre del accesorio.', 'error'); return; }
                if (this.form.precio === '')            { this.flash('Ingresa el precio.', 'error');               return; }

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

            // ── Helper POST genérico ──
            // Cierra el modal ANTES de la recarga para no mostrar errores de red
            async postForm(url, payload) {
                this.submitting = true;
                try {
                    const res  = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    // Intentar parsear como JSON; si falla, es un error HTML del servidor
                    let data = {};
                    try { data = await res.json(); } catch (_) {}

                    if (!res.ok) {
                        const err = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Error al guardar.');
                        this.flash(err, 'error');
                        return; // Modal sigue abierto para que el usuario corrija
                    }

                    // Éxito: cerrar modal primero, luego mostrar flash y recargar
                    this.crearModal = false;
                    this.flash(data.message || 'Guardado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch (e) {
                    // Error de red real (no errores SQL expuestos)
                    this.flash('Error de conexión. Intenta de nuevo.', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            async submitEditar() {
                this.submitting = true;
                try {
                    const res  = await fetch(this.routes.update(this.editForm.id_producto), {
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

                    if (!res.ok) {
                        const err = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Error al actualizar.');
                        this.flash(err, 'error');
                        return; // Modal sigue abierto
                    }

                    this.editarModal = false;
                    this.flash(data.message || 'Actualizado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch (e) {
                    this.flash('Error de conexión. Intenta de nuevo.', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            async submitEliminar() {
                this.submitting = true;
                try {
                    const res  = await fetch(this.routes.destroy(this.deleteTarget.id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                    });

                    let data = {};
                    try { data = await res.json(); } catch (_) {}

                    if (!res.ok) {
                        this.flash(data.message || 'Error al eliminar.', 'error');
                        return; // Modal sigue abierto
                    }

                    this.eliminarModal = false;
                    this.flash(data.message || 'Eliminado correctamente.');
                    setTimeout(() => window.location.reload(), 800);
                } catch (e) {
                    this.flash('Error de conexión. Intenta de nuevo.', 'error');
                } finally {
                    this.submitting = false;
                }
            },
        }
    }
    </script>

</div>
</x-app-layout>