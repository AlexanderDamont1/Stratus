<x-app-layout>

    {{-- ===== NOTIFICACIONES FLASH ===== --}}
    @if(session('success'))
    <div class="fixed top-6 left-1/2 -translate-x-1/2 z-[60]"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3500)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3">
        <div class="flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 px-4 py-3 shadow-2xl ring-1 ring-green-200 dark:ring-green-800 min-w-[320px] max-w-md">
            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-6 left-1/2 -translate-x-1/2 z-[60]"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3">
        <div class="flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 px-4 py-3 shadow-2xl ring-1 ring-red-200 dark:ring-red-800 min-w-[320px] max-w-md">
            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center shrink-0">
                <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1">{{ session('error') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Notificación JS --}}
    <div x-data="{ show: false, msg: '', tipo: 'success' }"
         x-show="show"
         x-on:notify-cupon.window="show = true; msg = $event.detail.msg; tipo = $event.detail.tipo; setTimeout(() => show = false, 3500)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-[60]">
        <div class="flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 px-4 py-3 shadow-2xl min-w-[320px] max-w-md"
             :class="tipo === 'success' ? 'ring-1 ring-green-200 dark:ring-green-800' : 'ring-1 ring-red-200 dark:ring-red-800'">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                 :class="tipo === 'success' ? 'bg-green-100 dark:bg-green-900/40' : 'bg-red-100 dark:bg-red-900/40'">
                <template x-if="tipo === 'success'">
                    <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="tipo !== 'success'">
                    <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </template>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1" x-text="msg"></p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <style>
        /* ── Barra de usos ── */
        .uso-bar {
            height: 2px;
            border-radius: 2px;
            overflow: hidden;
            background: #e5e7eb;
        }
        .dark .uso-bar { background: #374151; }
        .uso-bar-fill {
            height: 2px;
            border-radius: 2px;
        }

        /* ── Toggle switch ── */
        .cupon-toggle {
            width: 28px;
            height: 16px;
            border-radius: 999px;
            position: relative;
            flex-shrink: 0;
            transition: background 0.2s;
            cursor: pointer;
            border: none;
            padding: 0;
        }
        .cupon-toggle::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            top: 2px;
            transition: left 0.2s;
        }
        .cupon-toggle.on::after  { left: auto; right: 2px; }
        .cupon-toggle.off::after { left: 2px; }
        .cupon-toggle.on  { background: #16a34a; }
        .cupon-toggle.off { background: #d1d5db; }
        .dark .cupon-toggle.off { background: #4b5563; }
    </style>

    <x-onboarding
        :steps="config('onboarding')['admin.cupones.modal-crear'] ?? []"
        clave="admin.cupones.modal-crear"
        trigger-event="cupon-modal-abierto"
    />

    <div class="mx-auto space-y-7" x-data="cuponesPage()" x-init="init()">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Cupones</h2>
                <p class="text-xs text-gray-900 dark:text-white mt-0.5">Crea y gestiona descuentos para tus sucursales</p>
            </div>
            <button @click="abrirCrear()"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition active:scale-95 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo cupón
            </button>
        </div>

        {{-- ===== LISTA ===== --}}
        @if($cupones->isEmpty())
        <div class="py-16 text-center border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white">No hay cupones creados.</p>
            <p class="text-xs text-gray-900 dark:text-white mt-1">Crea tu primer cupón con el botón de arriba.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($cupones as $cupon)
            @php
                $vigente = $cupon->estaVigente();
                $activo  = $cupon->activo;

                // Color del acento izquierdo según tipo
                $accentColor = match($cupon->tipo_cupon) {
                    '1' => '#639922',
                    '2' => '#534AB7',
                    '3' => '#BA7517',
                    default => '#6b7280',
                };

                // Badges de tipo
                $badgeTipo = match($cupon->tipo_cupon) {
                    '1' => 'bg-[#EAF3DE] text-[#3B6D11] dark:bg-green-900/30 dark:text-green-400',
                    '2' => 'bg-[#EEEDFE] text-[#3C3489] dark:bg-purple-900/30 dark:text-purple-400',
                    '3' => 'bg-[#FAEEDA] text-[#854F0B] dark:bg-orange-900/30 dark:text-orange-400',
                    default => 'bg-gray-100 text-gray-600',
                };
                $tipoLabel = match($cupon->tipo_cupon) {
                    '1' => 'Descuento',
                    '2' => 'Accesorio',
                    '3' => 'Mantenimiento',
                    default => 'Cupón',
                };

                // Color barra de usos
                $barColor = match($cupon->tipo_cupon) {
                    '1' => '#639922',
                    '2' => '#534AB7',
                    '3' => '#BA7517',
                    default => '#6b7280',
                };

                // Estado badge
                $estadoBadge = $vigente
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                    : ($activo
                        ? 'bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-white'
                        : 'bg-gray-100 text-gray-400 dark:bg-gray-700/50 dark:text-gray-500');
                $estadoLabel = $vigente ? 'Vigente' : ($activo ? 'Activo' : 'Inactivo');

                $pct = ($cupon->usos_maximos && $cupon->usos_maximos > 0)
                    ? round(($cupon->usos_actuales / $cupon->usos_maximos) * 100)
                    : 0;

                // Color del toggle
                $toggleBg = $activo ? '#16a34a' : '';
            @endphp

            <div class="flex flex-col rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:shadow-md hover:-translate-y-0.5 transition duration-200"
                 style="border-left: 3px solid {{ $accentColor }};"
                 x-data="{ toggling: false }">

                {{-- ── CUERPO ── --}}
                <div class="px-4 pt-4 pb-3 flex flex-col gap-2.5 flex-1">

                    {{-- Fila 1: código + badges --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class=" text-sm font-medium tracking-[0.12em] text-gray-900 dark:text-white uppercase block">
                                {{ $cupon->codigo }}
                            </span>
                            <span class="text-xs text-gray-900 dark:text-white block mt-0.5 truncate">
                                {{ $cupon->nombre }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 flex-wrap justify-end">
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md {{ $badgeTipo }}">
                                {{ $tipoLabel }}
                            </span>
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md {{ $estadoBadge }}">
                                {{ $estadoLabel }}
                            </span>
                        </div>
                    </div>

                    {{-- Valor principal (más grande para todos los tipos) --}}
                    <div class="flex items-baseline gap-2">
                        @if($cupon->tipo_cupon === '1' && $cupon->tipo_descuento)
                            <span class="text-3xl font-medium leading-none text-gray-900 dark:text-white">
                                @if($cupon->tipo_descuento === 'porcentaje')
                                    {{ $cupon->valor_descuento }}%
                                @else
                                    ${{ number_format($cupon->valor_descuento, 0) }}
                                @endif
                            </span>
                            <span class="text-xs text-gray-900 dark:text-white">
                                {{ $cupon->aplica_a === 'total' ? 'descuento · total' : 'descuento · producto' }}
                            </span>
                        @elseif($cupon->tipo_cupon === '2' && $cupon->productoGratis)
                            <span class="text-xl font-medium text-gray-900 dark:text-white">
                                {{ $cupon->productoGratis->nombre_producto }}
                            </span>
                            <span class="text-xs text-gray-900 dark:text-white">se añade al carrito</span>
                        @elseif($cupon->tipo_cupon === '3')
                            @if($cupon->tipo_descuento === 'porcentaje' && $cupon->valor_descuento)
                                <span class="text-3xl font-medium leading-none text-gray-900 dark:text-white">{{ $cupon->valor_descuento }}%</span>
                                <span class="text-xs text-gray-900 dark:text-white">en mantenimiento</span>
                            @elseif($cupon->tipo_descuento === 'monto_fijo' && $cupon->valor_descuento)
                                <span class="text-3xl font-medium leading-none text-gray-900 dark:text-white">${{ number_format($cupon->valor_descuento, 0) }}</span>
                                <span class="text-xs text-gray-900 dark:text-white">en mantenimiento</span>
                            @else
                                <span class="text-3xl font-medium leading-none text-gray-900 dark:text-white">Gratis</span>
                                <span class="text-xs text-gray-900 dark:text-white">mantenimiento</span>
                            @endif
                        @endif
                    </div>

                    {{-- Meta: usos y fechas --}}
                    <div class="flex items-center gap-4 text-xs text-gray-900 dark:text-white">
                        <span>
                            Usos
                            <span class="font-medium text-gray-900 dark:text-white ml-1">
                                {{ $cupon->usos_actuales }}
                                @if($cupon->usos_maximos)
                                    / {{ $cupon->usos_maximos }}
                                    <span class="text-gray-900 dark:text-white font-normal">({{ $pct }}%)</span>
                                @else
                                    / ∞
                                @endif
                            </span>
                        </span>
                        @if($cupon->fecha_fin)
                        <span>
                            Hasta
                            <span class="font-medium text-gray-900 dark:text-white ml-1">
                                {{ $cupon->fecha_fin->format('d/m/Y') }}
                            </span>
                        </span>
                        @endif
                        @if($cupon->monto_minimo)
                        <span>
                            Mín.
                            <span class="font-medium text-gray-900 dark:text-white ml-1">
                                ${{ number_format($cupon->monto_minimo, 0) }}
                            </span>
                        </span>
                        @endif
                    </div>

                    {{-- Barra de usos --}}
                    @if($cupon->usos_maximos)
                    <div class="uso-bar">
                        <div class="uso-bar-fill" style="width: {{ min($pct, 100) }}%; background: {{ $barColor }};"></div>
                    </div>
                    @endif

                    {{-- Tags de reglas --}}
                    @if($cupon->reglas->isNotEmpty())
                    <div class="flex flex-wrap gap-1 pt-1">
                        @foreach($cupon->reglas as $regla)
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700/80 text-gray-900 dark:text-white font-medium flex items-center gap-1">
                            @switch($regla->tipo)
                                @case('sucursal')
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $regla->valor ? ($sucursales->firstWhere('id_usuario', $regla->valor)?->nombre_usuario ?? $regla->valor) : 'Todas' }}
                                    @break
                                @case('marca')
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $regla->valor ? ($marcas->firstWhere('id_marca', $regla->valor)?->nombre_marca ?? $regla->valor) : 'Toda marca' }}
                                    @break
                                @case('modelo')
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    {{ $regla->valor ? ($modelos->firstWhere('id_modelo', $regla->valor)?->nombre_modelo ?? $regla->valor) : 'Todo modelo' }}
                                    @break
                                @case('voltaje')
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    {{ $regla->valor ? ($voltajes->firstWhere('id_voltaje', $regla->valor)?->voltaje ?? $regla->valor) : 'Todo voltaje' }}
                                    @break
                                @case('monto_minimo')
                                    <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Mín. ${{ number_format($regla->valor, 2) }}
                                    @break
                            @endswitch
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- ── FOOTER ── --}}
                <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
                    <div class="flex items-center gap-1.5">
                        <button
                            @click="abrirEditar('{{ $cupon->id_cupon }}')"
                            class="text-xs text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 font-medium transition flex items-center gap-1 border border-gray-200 dark:border-gray-600 rounded-md px-2.5 py-1 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                        <button
                            @click="abrirDeleteModal('{{ $cupon->id_cupon }}', '{{ $cupon->codigo }}')"
                            class="text-xs text-red-400 hover:text-red-600 transition flex items-center gap-1 border border-gray-200 dark:border-gray-600 rounded-md px-2.5 py-1 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-200 dark:hover:border-red-700/50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </div>

                    {{-- Toggle activo/inactivo --}}
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
                        title="{{ $cupon->activo ? 'Desactivar' : 'Activar' }}"
                        class="cupon-toggle {{ $cupon->activo ? 'on' : 'off' }} disabled:opacity-40">
                        <span x-show="toggling" class="absolute inset-0 flex items-center justify-center">
                            <svg class="animate-spin w-3 h-3 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ===== MODAL CREAR/EDITAR ===== --}}
        <div x-show="crearModal" x-cloak
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4 py-6 overflow-y-auto"
            @click.self="crearModal = false">
            <div x-show="crearModal"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl my-auto" @click.stop>

                {{-- Header modal --}}
                <div class="flex items-center justify-between gap-3 px-6 py-5 border-b dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-gray-100 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white"
                                x-text="modoEdicion ? 'Editar cupón' : 'Nuevo cupón'"></h3>
                            <p class="text-xs text-gray-900 dark:text-white mt-0.5"
                                x-text="modoEdicion ? 'Modifica los datos del cupón' : 'Configura el descuento y sus condiciones'"></p>
                        </div>
                    </div>
                    <button @click="crearModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-600 text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body modal --}}
                <div class="px-6 py-5 space-y-5 max-h-[75vh] overflow-y-auto">

                    {{-- Tipo de cupón --}}
                    <div data-onboarding="cupon-tipo">
                        <label class="block text-xs font-medium text-gray-900 dark:text-white mb-2">Tipo de cupón</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="form.tipo_cupon = '1'; resetBeneficio()"
                                class="flex flex-col items-center gap-1.5 px-3 py-4 rounded-xl border text-xs font-medium transition"
                                :class="form.tipo_cupon === '1'
                                    ? 'border-green-400 bg-[#EAF3DE] dark:bg-green-900/20 text-[#27500A] dark:text-green-400'
                                    : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Descuento
                            </button>
                            <button type="button" @click="form.tipo_cupon = '2'; resetBeneficio()"
                                class="flex flex-col items-center gap-1.5 px-3 py-4 rounded-xl border text-xs font-medium transition"
                                :class="form.tipo_cupon === '2'
                                    ? 'border-purple-400 bg-[#EEEDFE] dark:bg-purple-900/20 text-[#26215C] dark:text-purple-400'
                                    : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                Accesorio gratis
                            </button>
                            <button type="button" @click="form.tipo_cupon = '3'; resetBeneficio()"
                                class="flex flex-col items-center gap-1.5 px-3 py-4 rounded-xl border text-xs font-medium transition"
                                :class="form.tipo_cupon === '3'
                                    ? 'border-orange-400 bg-[#FAEEDA] dark:bg-orange-900/20 text-[#412402] dark:text-orange-400'
                                    : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Mantenimiento
                            </button>
                        </div>
                    </div>

                    {{-- Nombre y código --}}
                    <div data-onboarding="cupon-nombre" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-900 dark:text-white mb-1">Nombre del cupón</label>
                            <input type="text" x-model="form.nombre" placeholder="Ej. Promoción de verano"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-900 dark:text-white mb-1">
                                Código
                                <button type="button" @click="generarCodigo()"
                                    class="inline-flex items-center gap-1 ml-1 text-[10px] text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Generar
                                </button>
                            </label>
                            <input type="text" x-model="form.codigo" placeholder="VERANO20"
                                @input="form.codigo = form.codigo.toUpperCase()"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400  uppercase">
                        </div>
                    </div>

                    {{-- Beneficio tipo 1 --}}
                    <template x-if="form.tipo_cupon === '1'">
                        <div data-onboarding="cupon-descuento" class="space-y-3 p-4 bg-[#EAF3DE] dark:bg-green-900/10 rounded-xl border border-[#C0DD97] dark:border-green-900/30">
                            <p class="text-xs font-medium text-[#27500A] dark:text-green-400">Configurar descuento</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Tipo</label>
                                    <select x-model="form.tipo_descuento"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                        <option value="porcentaje">Porcentaje (%)</option>
                                        <option value="monto_fijo">Monto fijo ($)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">
                                        Valor <span x-text="form.tipo_descuento === 'porcentaje' ? '(%)' : '($)'"></span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-900 dark:text-white text-sm"
                                            x-text="form.tipo_descuento === 'porcentaje' ? '%' : '$'"></span>
                                        <input type="number" x-model="form.valor_descuento" min="0" step="0.01"
                                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Aplica a</label>
                                    <div class="flex gap-2 mt-1">
                                        <button type="button" @click="form.aplica_a = 'total'"
                                            class="flex-1 px-2 py-2 rounded-lg border text-xs font-medium transition"
                                            :class="form.aplica_a === 'total'
                                                ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                                                : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                            Total
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Beneficio tipo 2 --}}
                    <template x-if="form.tipo_cupon === '2'">
                        <div data-onboarding="cupon-descuento" class="space-y-3 p-4 bg-[#EEEDFE] dark:bg-purple-900/10 rounded-xl border border-[#CECBF6] dark:border-purple-900/30">
                            <p class="text-xs font-medium text-[#26215C] dark:text-purple-400">Seleccionar accesorio gratis</p>
                            <select x-model="form.id_producto_gratis"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                <option value="">— Elige un accesorio —</option>
                                <template x-for="a in __accesorios" :key="a.id">
                                    <option :value="a.id" x-text="a.nombre"></option>
                                </template>
                            </select>
                        </div>
                    </template>

                    {{-- Beneficio tipo 3 --}}
                    <template x-if="form.tipo_cupon === '3'">
                        <div data-onboarding="cupon-descuento" class="space-y-3 p-4 bg-[#FAEEDA] dark:bg-orange-900/10 rounded-xl border border-[#FAC775] dark:border-orange-900/30">
                            <p class="text-xs font-medium text-[#412402] dark:text-orange-400">Configurar beneficio de mantenimiento</p>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="form.mantenimiento_tipo = 'gratis'; form.tipo_descuento = null; form.valor_descuento = ''"
                                    class="px-3 py-2 rounded-lg border text-xs font-medium transition"
                                    :class="form.mantenimiento_tipo === 'gratis'
                                        ? 'border-orange-400 bg-[#FAEEDA] dark:bg-orange-900/30 text-[#412402] dark:text-orange-400'
                                        : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                    Gratis
                                </button>
                                <button type="button" @click="form.mantenimiento_tipo = 'porcentaje'; form.tipo_descuento = 'porcentaje'"
                                    class="px-3 py-2 rounded-lg border text-xs font-medium transition"
                                    :class="form.mantenimiento_tipo === 'porcentaje'
                                        ? 'border-orange-400 bg-[#FAEEDA] dark:bg-orange-900/30 text-[#412402] dark:text-orange-400'
                                        : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                    % Descuento
                                </button>
                                <button type="button" @click="form.mantenimiento_tipo = 'monto_fijo'; form.tipo_descuento = 'monto_fijo'"
                                    class="px-3 py-2 rounded-lg border text-xs font-medium transition"
                                    :class="form.mantenimiento_tipo === 'monto_fijo'
                                        ? 'border-orange-400 bg-[#FAEEDA] dark:bg-orange-900/30 text-[#412402] dark:text-orange-400'
                                        : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-white hover:border-gray-400'">
                                    $ Fijo
                                </button>
                            </div>
                            <template x-if="form.mantenimiento_tipo !== 'gratis'">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-900 dark:text-white text-sm"
                                        x-text="form.mantenimiento_tipo === 'porcentaje' ? '%' : '$'"></span>
                                    <input type="number" x-model="form.valor_descuento" min="0" step="0.01"
                                        :placeholder="form.mantenimiento_tipo === 'porcentaje' ? 'Ej. 20' : 'Ej. 150'"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-7 pr-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Usos y fechas --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-900 dark:text-white mb-1">
                                Usos máximos <span class="text-gray-900 dark:text-white">(vacío = ∞)</span>
                            </label>
                            <input type="number" x-model="form.usos_maximos" min="1" placeholder="∞"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-900 dark:text-white mb-1">Fecha inicio</label>
                            <input type="date" x-model="form.fecha_inicio"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-900 dark:text-white mb-1">Fecha fin</label>
                            <input type="date" x-model="form.fecha_fin"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                    </div>

                    {{-- Condiciones --}}
                    <div data-onboarding="cupon-condiciones">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Condiciones</p>
                                <p class="text-[10px] text-gray-900 dark:text-white mt-0.5">La sucursal es obligatoria. Agrega más si necesitas.</p>
                            </div>
                            <button type="button" @click="agregarRegla()"
                                class="text-xs text-gray-500 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-95 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Condición
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(regla, idx) in form.reglas" :key="idx">
                                <div class="flex items-start gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">

                                    <template x-if="regla.tipo === 'sucursal'">
                                        <div class="flex items-center gap-2 flex-1">
                                            <span class="text-xs font-medium text-gray-900 dark:text-white shrink-0 w-20">Sucursal</span>
                                            <select x-model="regla.valor"
                                                class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <option value="">Todas las sucursales</option>
                                                <template x-for="s in __sucursales" :key="s.id">
                                                    <option :value="s.id" x-text="s.nombre"></option>
                                                </template>
                                            </select>
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    </template>

                                    <template x-if="regla.tipo !== 'sucursal'">
                                        <div class="flex items-start gap-2 flex-1">
                                            <select x-model="regla.tipo"
                                                @change="regla.valor = ''; regla.valor_modelo = ''; if (regla.tipo !== 'marca') modelosFiltrados = []"
                                                class="border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 shrink-0">
                                                <option value="marca">Marca</option>
                                                <option value="modelo">Modelo</option>
                                                <option value="voltaje">Voltaje</option>
                                                <option value="monto_minimo">Monto mínimo</option>
                                            </select>

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
                                                        <div class="flex items-center gap-2 text-xs text-gray-900 dark:text-white py-1">
                                                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
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
                                                </div>
                                            </template>

                                            <template x-if="regla.tipo === 'modelo'">
                                                <select x-model="regla.valor"
                                                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <option value="">Cualquier modelo</option>
                                                    @foreach($modelos as $m)
                                                    <option value="{{ $m->id_modelo }}">{{ $m->nombre_modelo }}</option>
                                                    @endforeach
                                                </select>
                                            </template>

                                            <template x-if="regla.tipo === 'voltaje'">
                                                <select x-model="regla.valor"
                                                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <option value="">Cualquier voltaje</option>
                                                    @foreach($voltajes as $v)
                                                    <option value="{{ $v->id_voltaje }}">{{ $v->voltaje }}</option>
                                                    @endforeach
                                                </select>
                                            </template>

                                            <template x-if="regla.tipo === 'monto_minimo'">
                                                <div class="relative flex-1">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-900 dark:text-white text-xs pointer-events-none">$</span>
                                                    <input type="number" x-model="regla.valor" min="0" step="0.01" placeholder="Ej. 5000"
                                                        class="w-full pl-6 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                </div>
                                            </template>

                                            <button type="button" @click="quitarRegla(idx)"
                                                class="text-gray-400 hover:text-red-500 transition mt-1 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Footer modal --}}
                <div class="flex justify-end gap-2 px-6 py-4 border-t dark:border-gray-700">
                    <button type="button" @click="crearModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        Cancelar
                    </button>
                    <button type="button" @click="submitCrear()" :disabled="submitting || !formValido"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!submitting" x-text="modoEdicion ? 'Guardar cambios' : 'Crear cupón'"></span>
                        <span x-show="submitting" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL ELIMINAR CUPÓN ===== --}}
            <div x-show="deleteModal" x-cloak
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
                @click.self="deleteModal = false">
                <div x-show="deleteModal"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar cupón</h3>
                            <p class="text-sm text-gray-500 dark:text-white mt-1">Se eliminará permanentemente</p>
                            <div class="text-center mt-1">
                                <span class="inline-block px-3 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-sm  font-semibold text-gray-800 dark:text-gray-200"
                                    x-text="deleteCuponCode"></span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-white mt-3">
                                Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="deleteModal = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="button" @click="confirmDelete" :disabled="deleteLoading"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!deleteLoading">Sí, eliminar</span>
                            <span x-show="deleteLoading" class="inline-flex items-center gap-1">
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

        {{-- ===== DATOS PARA ALPINE ===== --}}
        @php
            $cuponesJs = $cupones->mapWithKeys(fn($c) => [
                $c->id_cupon => [
                    'id_cupon'           => $c->id_cupon,
                    'tipo_cupon'         => $c->tipo_cupon,
                    'nombre'             => $c->nombre,
                    'codigo'             => $c->codigo,
                    'tipo_descuento'     => $c->tipo_descuento,
                    'valor_descuento'    => $c->valor_descuento,
                    'aplica_a'           => $c->aplica_a,
                    'monto_minimo'       => $c->monto_minimo,
                    'id_producto_gratis' => $c->id_producto_gratis,
                    'usos_maximos'       => $c->usos_maximos,
                    'fecha_inicio_raw'   => $c->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin_raw'      => $c->fecha_fin?->format('Y-m-d'),
                    'mensaje_vendedor'   => $c->mensaje_vendedor ?? '',
                    'reglas'             => $c->reglas->map(fn($r) => [
                        'tipo'  => $r->tipo,
                        'valor' => $r->valor,
                    ])->values()->all(),
                ]
            ])->all();
        @endphp

        <script>
            const __cupones            = @json($cuponesJs);
            const __accesorios         = @json($accesorios->map(fn($a) => ['id' => $a->id_producto, 'nombre' => $a->nombre_producto])->values()->all());
            const __sucursales         = @json($sucursales->map(fn($s) => ['id' => $s->id_usuario, 'nombre' => $s->nombre_usuario])->values()->all());
            const __modelosPorMarcaUrl = '{{ url("admin/cupones/modelos-por-marca") }}';
            const __updateUrl          = (id) => `{{ url('admin/cupones') }}/${id}`;
            const __storeUrl           = '{{ route("admin.cupones.store") }}';
        </script>

        <script>
        function cuponesPage() {
            return {
                crearModal:       false,
                modoEdicion:      false,
                editandoId:       null,
                submitting:       false,
                modelosFiltrados: [],
                modelosCargando:  false,
                // Delete modal
                deleteModal:      false,
                deleteId:         null,
                deleteCuponCode:  '',
                deleteLoading:    false,

                form: {
                    tipo_cupon:         '1',
                    nombre:             '',
                    codigo:             '',
                    mensaje_vendedor:   '',
                    tipo_descuento:     'porcentaje',
                    valor_descuento:    '',
                    aplica_a:           'total',
                    id_producto_gratis: '',
                    mantenimiento_tipo: 'gratis',
                    monto_minimo:       '',
                    usos_maximos:       '',
                    fecha_inicio:       '',
                    fecha_fin:          '',
                    reglas:             [],
                },

                get formValido() {
                    if (!this.form.nombre.trim() || !this.form.codigo.trim()) return false;
                    if (!this.form.reglas.some(r => r.tipo === 'sucursal'))   return false;
                    if (this.form.tipo_cupon === '1') {
                        return !!this.form.tipo_descuento
                            && this.form.valor_descuento !== ''
                            && Number(this.form.valor_descuento) > 0;
                    }
                    if (this.form.tipo_cupon === '2') return !!this.form.id_producto_gratis;
                    if (this.form.tipo_cupon === '3') {
                        if (this.form.mantenimiento_tipo === 'gratis') return true;
                        return this.form.valor_descuento !== '' && Number(this.form.valor_descuento) > 0;
                    }
                    return false;
                },

                init() {},

                resetBeneficio() {
                    this.form.tipo_descuento     = 'porcentaje';
                    this.form.valor_descuento    = '';
                    this.form.aplica_a           = 'total';
                    this.form.id_producto_gratis = '';
                    this.form.mantenimiento_tipo = 'gratis';
                },

                async abrirEditar(idCupon) {
                    const c = __cupones[idCupon];
                    if (!c) return;

                    this.modoEdicion      = true;
                    this.editandoId       = idCupon;
                    this.modelosFiltrados = [];

                    const reglas = [...(c.reglas ?? [])];
                    if (!reglas.some(r => r.tipo === 'sucursal')) {
                        reglas.unshift({ tipo: 'sucursal', valor: '' });
                    }

                    let mantTipo = 'gratis';
                    if (c.tipo_cupon === '3' && c.tipo_descuento) mantTipo = c.tipo_descuento;

                    this.form = {
                        tipo_cupon:         c.tipo_cupon  ?? '1',
                        nombre:             c.nombre,
                        codigo:             c.codigo,
                        mensaje_vendedor:   c.mensaje_vendedor ?? '',
                        tipo_descuento:     c.tipo_descuento   ?? 'porcentaje',
                        valor_descuento:    c.valor_descuento  ?? '',
                        aplica_a:           c.aplica_a         ?? 'total',
                        id_producto_gratis: c.id_producto_gratis ?? '',
                        mantenimiento_tipo: mantTipo,
                        monto_minimo:       c.monto_minimo     ?? '',
                        usos_maximos:       c.usos_maximos     ?? '',
                        fecha_inicio:       c.fecha_inicio_raw ?? '',
                        fecha_fin:          c.fecha_fin_raw    ?? '',
                        reglas: reglas.map(r => ({
                            tipo:         r.tipo,
                            valor:        r.valor ?? '',
                            valor_modelo: '',
                        })),
                    };

                    const regMarca = this.form.reglas.find(r => r.tipo === 'marca');
                    if (regMarca?.valor) {
                        await this.filtrarModelos(regMarca.valor);
                        const regModelo = c.reglas.find(r => r.tipo === 'modelo');
                        if (regModelo) regMarca.valor_modelo = regModelo.valor ?? '';
                    }

                    this.crearModal = true;
                    window.dispatchEvent(new CustomEvent('cupon-modal-abierto'));
                },

                abrirCrear() {
                    this.modoEdicion      = false;
                    this.editandoId       = null;
                    this.modelosFiltrados = [];
                    this.form = {
                        tipo_cupon:         '1',
                        nombre:             '',
                        codigo:             '',
                        mensaje_vendedor:   '',
                        tipo_descuento:     'porcentaje',
                        valor_descuento:    '',
                        aplica_a:           'total',
                        id_producto_gratis: '',
                        mantenimiento_tipo: 'gratis',
                        monto_minimo:       '',
                        usos_maximos:       '',
                        fecha_inicio:       '',
                        fecha_fin:          '',
                        reglas: [{ tipo: 'sucursal', valor: '' }],
                    };
                    this.crearModal = true;
                    window.dispatchEvent(new CustomEvent('cupon-modal-abierto'));
                },

                generarCodigo() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    this.form.codigo = Array.from({ length: 8 }, () =>
                        chars[Math.floor(Math.random() * chars.length)]
                    ).join('');
                },

                agregarRegla() {
                    this.form.reglas.push({ tipo: 'marca', valor: '', valor_modelo: '' });
                },

                quitarRegla(idx) {
                    if (this.form.reglas[idx].tipo === 'sucursal') return;
                    this.form.reglas.splice(idx, 1);
                },

                async filtrarModelos(idMarca) {
                    if (!idMarca) { this.modelosFiltrados = []; return; }
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

                async submitCrear() {
                    if (!this.formValido || this.submitting) return;
                    this.submitting = true;

                    const url    = this.modoEdicion ? __updateUrl(this.editandoId) : __storeUrl;
                    const method = this.modoEdicion ? 'PUT' : 'POST';

                    const reglas = [];
                    for (const r of this.form.reglas) {
                        if (r.tipo === 'marca') {
                            reglas.push({ tipo: 'marca', valor: r.valor || null });
                            if (r.valor_modelo) reglas.push({ tipo: 'modelo', valor: r.valor_modelo });
                        } else {
                            reglas.push({ tipo: r.tipo, valor: r.valor || null });
                        }
                    }

                    let tipo_descuento     = null;
                    let valor_descuento    = null;
                    let aplica_a           = 'total';
                    let id_producto_gratis = null;

                    if (this.form.tipo_cupon === '1') {
                        tipo_descuento  = this.form.tipo_descuento;
                        valor_descuento = this.form.valor_descuento;
                        aplica_a        = this.form.aplica_a;
                    } else if (this.form.tipo_cupon === '2') {
                        id_producto_gratis = this.form.id_producto_gratis || null;
                    } else if (this.form.tipo_cupon === '3') {
                        if (this.form.mantenimiento_tipo !== 'gratis') {
                            tipo_descuento  = this.form.mantenimiento_tipo;
                            valor_descuento = this.form.valor_descuento;
                        }
                    }

                    const payload = {
                        tipo_cupon:         this.form.tipo_cupon,
                        nombre:             this.form.nombre,
                        codigo:             this.form.codigo,
                        mensaje_vendedor:   this.form.mensaje_vendedor  || null,
                        monto_minimo:       this.form.monto_minimo      || null,
                        usos_maximos:       this.form.usos_maximos      || null,
                        fecha_inicio:       this.form.fecha_inicio      || null,
                        fecha_fin:          this.form.fecha_fin         || null,
                        tipo_descuento,
                        valor_descuento,
                        aplica_a,
                        id_producto_gratis,
                        reglas,
                    };

                    try {
                        const res  = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type':     'application/json',
                                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept':           'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            const err = data.errors
                                ? Object.values(data.errors).flat().join(' ')
                                : (data.message || 'Error al guardar.');
                            window.dispatchEvent(new CustomEvent('notify-cupon', { detail: { msg: err, tipo: 'error' } }));
                            return;
                        }

                        this.crearModal = false;
                        window.dispatchEvent(new CustomEvent('notify-cupon', {
                            detail: {
                                msg: this.modoEdicion ? 'Cupón actualizado correctamente.' : 'Cupón creado correctamente.',
                                tipo: 'success',
                            }
                        }));
                        setTimeout(() => window.location.reload(), 1200);

                    } catch {
                        window.dispatchEvent(new CustomEvent('notify-cupon', { detail: { msg: 'Error de conexión.', tipo: 'error' } }));
                    } finally {
                        this.submitting = false;
                    }
                },

                // Delete modal methods
                abrirDeleteModal(id, code) {
                    this.deleteId = id;
                    this.deleteCuponCode = code;
                    this.deleteModal = true;
                },

                async confirmDelete() {
                    if (this.deleteLoading) return;
                    this.deleteLoading = true;
                    try {
                        const res = await fetch(`{{ url('admin/cupones') }}/${this.deleteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            }
                        });
                        const data = await res.json();
                        if (res.ok) {
                            window.dispatchEvent(new CustomEvent('notify-cupon', {
                                detail: { msg: 'Cupón eliminado correctamente.', tipo: 'success' }
                            }));
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            window.dispatchEvent(new CustomEvent('notify-cupon', {
                                detail: { msg: data.message || 'Error al eliminar.', tipo: 'error' }
                            }));
                            this.deleteModal = false;
                        }
                    } catch {
                        window.dispatchEvent(new CustomEvent('notify-cupon', {
                            detail: { msg: 'Error de conexión.', tipo: 'error' }
                        }));
                        this.deleteModal = false;
                    } finally {
                        this.deleteLoading = false;
                    }
                }
            };
        }
        </script>

    </div>
</x-app-layout>