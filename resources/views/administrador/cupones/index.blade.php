<x-app-layout>
    <div class="mx-auto space-y-7" x-data="cuponesPage()" x-init="init()">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Cupones</h2>
                <p class="text-xs text-gray-400 mt-0.5">Crea y gestiona descuentos para tus sucursales</p>
            </div>
            <button @click="abrirCrear()"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition active:scale-95">
                + Nuevo cupón
            </button>
        </div>

        {{-- ===== FLASH ===== --}}

        <x-flash-messages />


        {{-- ===== LISTA DE CUPONES ===== --}}
        @if($cupones->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay cupones creados.</p>
            <p class="text-xs text-gray-400 mt-1">Crea tu primer cupón con el botón de arriba.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cupones as $cupon)
            @php
            $vigente = $cupon->estaVigente();
            @endphp
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition hover:shadow-md"
                x-data="{ toggling: false, eliminando: false }">

                {{-- Header tarjeta --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm font-bold text-gray-900 dark:text-white tracking-wider">
                                {{ $cupon->codigo }}
                            </span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                            {{ $vigente
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                {{ $vigente ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $cupon->nombre }}</p>
                    </div>

                    {{-- Toggle activo --}}
                    <button
                        @click="
                        toggling = true;
                        fetch('{{ route('admin.cupones.toggle', $cupon->id_cupon) }}', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            }
                        })
                        .then(r => r.json())
                        .then(() => { toggling = false; window.location.reload(); })
                        .catch(() => toggling = false);
                    "
                        :disabled="toggling"
                        class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-600 text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-40"
                        title="{{ $cupon->activo ? 'Desactivar' : 'Activar' }}">
                        <svg x-show="!toggling" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($cupon->activo)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                        <svg x-show="toggling" class="animate-spin w-4 h-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </button>
                </div>

                {{-- Cuerpo --}}
                <div class="px-5 py-4 space-y-3">

                    {{-- Descuento --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Descuento</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            @if($cupon->tipo_descuento === 'porcentaje')
                            {{ $cupon->valor_descuento }}%
                            @else
                            ${{ number_format($cupon->valor_descuento, 2) }}
                            @endif
                            <span class="text-[10px] font-normal text-gray-400 ml-1">
                                {{ $cupon->aplica_a === 'total' ? 'al total' : 'por producto' }}
                            </span>
                        </span>
                    </div>

                    {{-- Accesorio gratis --}}
                    @if($cupon->id_producto_gratis && $cupon->productoGratis)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Accesorio gratis</span>
                        <span class="text-xs font-medium text-green-600 dark:text-green-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            {{ $cupon->productoGratis->nombre_producto }}
                        </span>
                    </div>
                    @endif

                    {{-- Usos --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Usos</span>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ $cupon->usos_actuales }}
                            @if($cupon->usos_maximos)
                            / {{ $cupon->usos_maximos }}
                            @else
                            <span class="text-gray-400">/ ilimitado</span>
                            @endif
                        </span>
                    </div>

                    {{-- Vigencia --}}
                    @if($cupon->fecha_inicio || $cupon->fecha_fin)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Vigencia</span>
                        <span class="text-xs text-gray-600 dark:text-gray-300">
                            {{ $cupon->fecha_inicio?->format('d/m/Y') ?? '—' }}
                            →
                            {{ $cupon->fecha_fin?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    @endif

                    {{-- Reglas --}}
                    @if($cupon->reglas->isNotEmpty())
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider mb-2">Condiciones</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($cupon->reglas as $regla)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                @switch($regla->tipo)
                                @case('modelo') Modelo @break
                                @case('marca') Marca @break
                                @case('cantidad_minima') Mín. {{ $regla->valor }} uds. @break
                                @case('sucursal') Sucursal @break
                                @endswitch
                                @if($regla->valor && $regla->tipo !== 'cantidad_minima')
                                {{-- Mostrar nombre legible en lugar del ID --}}
                                @if($regla->tipo === 'sucursal')
                                : {{ $sucursales->firstWhere('id_usuario', $regla->valor)?->nombre_usuario ?? $regla->valor }}
                                @elseif($regla->tipo === 'marca')
                                : {{ $marcas->firstWhere('id_marca', $regla->valor)?->nombre_marca ?? $regla->valor }}
                                @elseif($regla->tipo === 'modelo')
                                : {{ $modelos->firstWhere('id_modelo', $regla->valor)?->nombre_modelo ?? $regla->valor }}
                                @else
                                : {{ $regla->valor }}
                                @endif
                                @endif
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <button
                        @click="abrirEditar({
                        id_cupon:            '{{ $cupon->id_cupon }}',
                        nombre:              '{{ addslashes($cupon->nombre) }}',
                        codigo:              '{{ $cupon->codigo }}',
                        tipo_descuento:      '{{ $cupon->tipo_descuento }}',
                        valor_descuento:     {{ $cupon->valor_descuento }},
                        aplica_a:            '{{ $cupon->aplica_a }}',
                        id_producto_gratis:  '{{ $cupon->id_producto_gratis ?? '' }}',
                        usos_maximos:        {{ $cupon->usos_maximos ?? 'null' }},
                        fecha_inicio_raw:    '{{ $cupon->fecha_inicio?->format('Y-m-d') ?? '' }}',
                        fecha_fin_raw:       '{{ $cupon->fecha_fin?->format('Y-m-d') ?? '' }}',
                        reglas: @json($cupon->reglas->map(fn($r) => ['tipo' => $r->tipo, 'valor' => $r->valor])),
                    })"
                        class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 transition">
                        Editar
                    </button>

                    <button
                        @click="
                        if (!confirm('¿Eliminar este cupón? Esta acción no se puede deshacer.')) return;
                        eliminando = true;
                        fetch('{{ route('admin.cupones.destroy', $cupon->id_cupon) }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            }
                        })
                        .then(r => r.json())
                        .then(() => window.location.reload())
                        .catch(() => eliminando = false);
                    "
                        :disabled="eliminando"
                        class="text-xs text-red-400 hover:text-red-600 dark:hover:text-red-400 transition disabled:opacity-40">
                        <span x-show="!eliminando">Eliminar</span>
                        <span x-show="eliminando" class="flex items-center gap-1">
                            <svg class="animate-spin w-3 h-3" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Eliminando...
                        </span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ===== MODAL CREAR / EDITAR (versión modificada) ===== --}}
        <div x-show="crearModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4 py-6 overflow-y-auto"
            @click.self="crearModal = false">
            <div x-show="crearModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg my-auto" @click.stop>

                {{-- Header modal --}}
                <div class="flex items-center gap-3 px-6 py-5 border-b dark:border-gray-700">
                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white"
                            x-text="modoEdicion ? 'Editar cupón' : 'Nuevo cupón'"></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                            x-text="modoEdicion ? 'Modifica los datos del cupón' : 'Configura el descuento y sus condiciones'"></p>
                    </div>
                </div>

                {{-- Body modal --}}
                <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">

                    {{-- Nombre y código --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre del cupón</label>
                            <input type="text" x-model="form.nombre" placeholder="Ej. Promoción de verano"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Código
                                <button type="button" @click="generarCodigo()"
                                    class="inline-flex items-center gap-1 ml-1 text-[10px] text-blue-600 dark:text-blue-400 
                                hover:text-blue-800 dark:hover:text-blue-300 underline transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Generar
                                </button>
                            </label>
                            <input type="text" x-model="form.codigo" placeholder="VERANO20"
                                @input="form.codigo = form.codigo.toUpperCase()"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono uppercase">
                        </div>
                    </div>

                    {{-- ACCESORIO GRATIS (movido al principio) --}}
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                            Accesorio gratis
                            <span class="text-gray-400 font-normal">(opcional — se agrega automáticamente a la venta sin costo)</span>
                        </label>
                        <select x-model="form.id_producto_gratis"
                            @change="onProductoGratisChange()"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="">Sin accesorio gratis</option>
                            <template x-for="a in __accesorios" :key="a.id">
                                <option :value="a.id" x-text="a.nombre"></option>
                            </template>
                        </select>
                        <template x-if="form.id_producto_gratis">
                            <p class="text-[10px] text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Este accesorio se añadirá gratis al carrito al usar el cupón.
                            </p>
                        </template>
                    </div>

                    {{-- SECCIÓN DE DESCUENTO (visible solo si NO hay accesorio gratis) --}}
                    <template x-if="!form.id_producto_gratis">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo de descuento</label>
                                    <select x-model="form.tipo_descuento"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                        <option value="porcentaje">Porcentaje (%)</option>
                                        <option value="monto_fijo">Monto fijo ($)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        Valor
                                        <span x-text="form.tipo_descuento === 'porcentaje' ? '(%)' : '($)'"></span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                            x-text="form.tipo_descuento === 'porcentaje' ? '%' : '$'"></span>
                                        <input type="number" x-model="form.valor_descuento" min="0" step="0.01"
                                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Si hay accesorio gratis, mostramos un mensaje opcional --}}
                    <template x-if="form.id_producto_gratis">
                        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3 text-xs text-blue-700 dark:text-blue-300 flex items-start gap-2">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Este cupón solo regalará el accesorio seleccionado, no aplica descuento adicional.</span>
                        </div>
                    </template>

                    {{-- Usos y fechas --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                                Usos máximos
                                <span class="text-gray-400">(vacío = ilimitado)</span>
                            </label>
                            <input type="number" x-model="form.usos_maximos" min="1" placeholder="∞"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Fecha inicio</label>
                            <input type="date" x-model="form.fecha_inicio"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Fecha fin</label>
                            <input type="date" x-model="form.fecha_fin"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                    </div>

                    {{-- Reglas / condiciones (sin cambios, pero asegurar que 'aplica_a' ya no se usa en el envío) --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Condiciones</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">La sucursal es obligatoria. Puedes agregar más condiciones.</p>
                            </div>
                            <button type="button" @click="agregarRegla()"
                                class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95">
                                + Condición
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(regla, idx) in form.reglas" :key="idx">
                                <div class="flex items-start gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                                    <!-- Sucursal fija -->
                                    <template x-if="regla.tipo === 'sucursal'">
                                        <div class="flex items-center gap-2 flex-1">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 shrink-0 w-20">Sucursal</span>
                                            <select x-model="regla.valor"
                                                class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <option value="">Todas las sucursales</option>
                                                <template x-for="s in __sucursales" :key="s.id">
                                                    <option :value="s.id" x-text="s.nombre"></option>
                                                </template>
                                            </select>
                                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </template>

                                    <!-- Otras reglas -->
                                    <template x-if="regla.tipo !== 'sucursal'">
                                        <div class="flex items-start gap-2 flex-1">
                                            <select x-model="regla.tipo"
                                                @change="regla.valor = ''; regla.valor_modelo = ''; if (regla.tipo !== 'marca') modelosFiltrados = []"
                                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 shrink-0">
                                                <option value="marca">Marca</option>
                                                <option value="modelo">Modelo</option>
                                                <option value="cantidad_minima">Cantidad mínima</option>
                                            </select>

                                            <!-- Marca con modelos encadenados -->
                                            <template x-if="regla.tipo === 'marca'">
                                                <div class="flex flex-col gap-1.5 flex-1">
                                                    <select x-model="regla.valor" @change="onMarcaChange(regla)"
                                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                        <option value="">Elige una marca…</option>
                                                        @foreach($marcas as $m)
                                                        <option value="{{ $m->id_marca }}">{{ $m->nombre_marca }}</option>
                                                        @endforeach
                                                    </select>

                                                    <template x-if="regla.valor && modelosCargando">
                                                        <div class="flex items-center gap-2 text-xs text-gray-400 py-1">
                                                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                                            </svg>
                                                            Cargando modelos...
                                                        </div>
                                                    </template>

                                                    <template x-if="regla.valor && !modelosCargando && modelosFiltrados.length > 0">
                                                        <select x-model="regla.valor_modelo"
                                                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                            <option value="">Todos los modelos de la marca</option>
                                                            <template x-for="mo in modelosFiltrados" :key="mo.id">
                                                                <option :value="mo.id" x-text="mo.nombre"></option>
                                                            </template>
                                                        </select>
                                                    </template>

                                                    <template x-if="regla.valor && !modelosCargando && modelosFiltrados.length === 0">
                                                        <p class="text-[10px] text-gray-400 py-1">No hay modelos para esta marca.</p>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- Modelo directo -->
                                            <template x-if="regla.tipo === 'modelo'">
                                                <select x-model="regla.valor"
                                                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <option value="">Cualquier modelo</option>
                                                    @foreach($modelos as $m)
                                                    <option value="{{ $m->id_modelo }}">{{ $m->nombre_modelo }}</option>
                                                    @endforeach
                                                </select>
                                            </template>

                                            <!-- Cantidad mínima -->
                                            <template x-if="regla.tipo === 'cantidad_minima'">
                                                <input type="number" x-model="regla.valor" min="1" placeholder="Ej. 2"
                                                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                            </template>

                                            <button type="button" @click="quitarRegla(idx)"
                                                class="text-gray-400 hover:text-red-500 transition mt-1 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>{{-- /body modal --}}

                {{-- Footer modal --}}
                <div class="flex justify-end gap-2 px-6 py-4 border-t dark:border-gray-700">
                    <button type="button" @click="crearModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        Cancelar
                    </button>
                    <button type="button" @click="submitCrear()" :disabled="submitting || !formValido"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!submitting" x-text="modoEdicion ? 'Guardar cambios' : 'Crear cupón'"></span>
                        <span x-show="submitting" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== DATOS PARA ALPINE ===== --}}
        <script>
            const __accesorios = @json($accesorios -> map(fn($a) => ['id' => $a -> id_producto, 'nombre' => $a -> nombre_producto]));
            const __sucursales = @json($sucursales -> map(fn($s) => ['id' => $s -> id_usuario, 'nombre' => $s -> nombre_usuario]));
            const __modelosPorMarcaUrl = '{{ url("admin/cupones/modelos-por-marca") }}';
            const __updateUrl = (id) => `{{ url('admin/cupones') }}/${id}`;
            const __storeUrl = '{{ route("admin.cupones.store") }}';
        </script>

        {{-- ===== ALPINE JS ===== --}}
        <script>
            function cuponesPage() {
                return {
                    crearModal: false,
                    modoEdicion: false,
                    editandoId: null,
                    submitting: false,
                    flashVisible: false,
                    flashMsg: '',
                    flashTipo: 'success',
                    flashTimer: null,
                    modelosFiltrados: [],
                    modelosCargando: false,

                    form: {
                        nombre: '',
                        codigo: '',
                        tipo_descuento: 'porcentaje',
                        valor_descuento: '',
                        id_producto_gratis: '',
                        usos_maximos: '',
                        fecha_inicio: '',
                        fecha_fin: '',
                        reglas: [],
                    },

                    // ── Validación ───────────────────────────────────────────────────
                    get formValido() {
                        const tieneSucursal = this.form.reglas.some(r => r.tipo === 'sucursal');
                        if (!tieneSucursal) return false;

                        // Si hay producto gratis, no requerimos descuento
                        if (this.form.id_producto_gratis) {
                            return this.form.nombre.trim() !== '' && this.form.codigo.trim() !== '';
                        }

                        // Sin producto gratis, sí requiere descuento
                        return this.form.nombre.trim() !== '' &&
                            this.form.codigo.trim() !== '' &&
                            this.form.valor_descuento !== '' &&
                            Number(this.form.valor_descuento) > 0;
                    },

                    // ── Se ejecuta al cambiar el accesorio gratis ─────────────────────
                    onProductoGratisChange() {
                        if (this.form.id_producto_gratis) {
                            this.form.tipo_descuento = 'porcentaje';
                            this.form.valor_descuento = '';
                        }
                    },

                    init() {},

                    // ── Flash ────────────────────────────────────────────────────────
                    flash(msg, tipo = 'success') {
                        this.flashMsg = msg;
                        this.flashTipo = tipo;
                        this.flashVisible = true;
                        clearTimeout(this.flashTimer);
                        this.flashTimer = setTimeout(
                            () => this.flashVisible = false,
                            tipo === 'error' ? 4000 : 3000
                        );
                    },

                    // ── Abrir modal CREAR ────────────────────────────────────────────
                    abrirCrear() {
                        this.modoEdicion = false;
                        this.editandoId = null;
                        this.modelosFiltrados = [];
                        this.form = {
                            nombre: '',
                            codigo: '',
                            tipo_descuento: 'porcentaje',
                            valor_descuento: '',
                            id_producto_gratis: '',
                            usos_maximos: '',
                            fecha_inicio: '',
                            fecha_fin: '',
                            reglas: [{
                                tipo: 'sucursal',
                                valor: ''
                            }],
                        };
                        this.crearModal = true;
                    },

                    // ── Abrir modal EDITAR ───────────────────────────────────────────
                    async abrirEditar(cupon) {
                        this.modoEdicion = true;
                        this.editandoId = cupon.id_cupon;
                        this.modelosFiltrados = [];

                        // Garantizar regla de sucursal
                        const reglas = [...(cupon.reglas ?? [])];
                        if (!reglas.some(r => r.tipo === 'sucursal')) {
                            reglas.unshift({
                                tipo: 'sucursal',
                                valor: ''
                            });
                        }

                        this.form = {
                            nombre: cupon.nombre,
                            codigo: cupon.codigo,
                            tipo_descuento: cupon.tipo_descuento,
                            valor_descuento: cupon.valor_descuento,
                            id_producto_gratis: cupon.id_producto_gratis ?? '',
                            usos_maximos: cupon.usos_maximos ?? '',
                            fecha_inicio: cupon.fecha_inicio_raw ?? '',
                            fecha_fin: cupon.fecha_fin_raw ?? '',
                            reglas: reglas.map(r => ({
                                tipo: r.tipo,
                                valor: r.valor ?? '',
                                valor_modelo: '',
                            })),
                        };

                        // Si había regla de marca, cargar sus modelos
                        const regMarca = this.form.reglas.find(r => r.tipo === 'marca');
                        if (regMarca?.valor) {
                            await this.filtrarModelos(regMarca.valor);
                            const regModelo = cupon.reglas.find(r => r.tipo === 'modelo');
                            if (regModelo) regMarca.valor_modelo = regModelo.valor ?? '';
                        }

                        this.crearModal = true;
                    },

                    // ── Generar código aleatorio ─────────────────────────────────────
                    generarCodigo() {
                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        this.form.codigo = Array.from({
                                length: 8
                            }, () =>
                            chars[Math.floor(Math.random() * chars.length)]
                        ).join('');
                    },

                    // ── Gestión de reglas ────────────────────────────────────────────
                    agregarRegla() {
                        this.form.reglas.push({
                            tipo: 'marca',
                            valor: '',
                            valor_modelo: ''
                        });
                    },

                    quitarRegla(idx) {
                        if (this.form.reglas[idx].tipo === 'sucursal') return; // protegida
                        this.form.reglas.splice(idx, 1);
                    },

                    // ── Modelos por marca (AJAX) ─────────────────────────────────────
                    async filtrarModelos(idMarca) {
                        if (!idMarca) {
                            this.modelosFiltrados = [];
                            return;
                        }
                        this.modelosCargando = true;
                        try {
                            const res = await fetch(`${__modelosPorMarcaUrl}/${idMarca}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                            });
                            this.modelosFiltrados = await res.json();
                        } catch {
                            this.modelosFiltrados = [];
                        } finally {
                            this.modelosCargando = false;
                        }
                    },

                    async onMarcaChange(regla) {
                        regla.valor_modelo = '';
                        await this.filtrarModelos(regla.valor);
                    },

                    // ── Submit (crear o editar) ──────────────────────────────────────
                    async submitCrear() {
                        if (!this.formValido || this.submitting) return;
                        this.submitting = true;

                        const url = this.modoEdicion ? __updateUrl(this.editandoId) : __storeUrl;
                        const method = this.modoEdicion ? 'PUT' : 'POST';

                        // Normalizar reglas: marca con modelo encadenado → 2 reglas separadas
                        const reglas = [];
                        for (const r of this.form.reglas) {
                            if (r.tipo === 'marca') {
                                reglas.push({
                                    tipo: 'marca',
                                    valor: r.valor || null
                                });
                                if (r.valor_modelo) {
                                    reglas.push({
                                        tipo: 'modelo',
                                        valor: r.valor_modelo
                                    });
                                }
                            } else {
                                reglas.push({
                                    tipo: r.tipo,
                                    valor: r.valor || null
                                });
                            }
                        }

                        // Construir payload
                        let payload = {
                            nombre: this.form.nombre,
                            codigo: this.form.codigo,
                            id_producto_gratis: this.form.id_producto_gratis || null,
                            usos_maximos: this.form.usos_maximos || null,
                            fecha_inicio: this.form.fecha_inicio || null,
                            fecha_fin: this.form.fecha_fin || null,
                            reglas: reglas,
                        };

                        if (this.form.id_producto_gratis) {
                            // Cupón solo de regalo: sin descuento
                            payload.tipo_descuento = null;
                            payload.valor_descuento = null;
                            payload.aplica_a = 'total';
                        } else {
                            payload.tipo_descuento = this.form.tipo_descuento;
                            payload.valor_descuento = this.form.valor_descuento;
                            payload.aplica_a = 'total';
                        }

                        try {
                            const res = await fetch(url, {
                                method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify(payload),
                            });

                            const data = await res.json();

                            if (!res.ok) {
                                const err = data.errors ?
                                    Object.values(data.errors).flat().join(' ') :
                                    (data.message || 'Error al guardar.');
                                this.flash(err, 'error');
                                return;
                            }

                            this.crearModal = false;
                            this.flash(data.mensaje || (this.modoEdicion ? 'Cupón actualizado.' : 'Cupón creado.'));
                            setTimeout(() => window.location.reload(), 800);

                        } catch {
                            this.flash('Error de conexión.', 'error');
                        } finally {
                            this.submitting = false;
                        }
                    },
                };
            }
        </script>

    </div>
</x-app-layout>