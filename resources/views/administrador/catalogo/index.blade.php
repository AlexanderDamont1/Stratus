<x-app-layout>
<div class="mx-auto space-y-7" x-data="catalogoPage()" data-negocio-id="{{ $idNegocio }}" x-init="init()">

{{-- ===== ENCABEZADO ===== --}}
<div class="flex flex-wrap items-start justify-between gap-4 sm:gap-2">
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Catálogo de productos</h2>
        <p class="text-xs text-gray-400 mt-0.5">Gestiona marcas, modelos, colores y voltajes</p>
    </div>
    <div class="flex flex-col items-end gap-2 sm:flex-row-reverse sm:items-center">
        @if($totalMarcas < $limiteMarcas)
            <button @click="marcaModal = true"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap hover:scale-105 transform duration-200">
                + Nueva marca
            </button>
        @else
            <span class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-3 py-1.5 rounded-md whitespace-nowrap">
                Límite de marcas alcanzado
            </span>
        @endif
        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-600 whitespace-nowrap">
            {{ $totalMarcas }} / {{ $limiteMarcas }} marcas
        </span>
    </div>
</div>

{{-- ===== FLASH ALPINE (dinámico para AJAX) ===== --}}
<div x-show="flashVisible" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
    <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl min-w-[300px]"
         :class="flashTipo === 'error'
            ? 'ring-1 ring-red-200 dark:ring-red-800'
            : 'ring-1 ring-gray-200 dark:ring-gray-700'">
        <svg x-show="flashTipo === 'success'" class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <svg x-show="flashTipo === 'error'" class="h-5 w-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
    </div>
</div>

{{-- ===== FLASH SESSION (para recargas de otros roles) ===== --}}
@if(session('success'))
<div class="fixed top-6 left-1/2 -translate-x-1/2 z-50"
     x-data="{ show: true }" x-show="show"
     x-init="setTimeout(() => show = false, 3000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-end="opacity-0 translate-y-2">
    <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 min-w-[300px]">
        <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-6 left-1/2 -translate-x-1/2 z-50"
     x-data="{ show: true }" x-show="show"
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2">
    <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-red-200 dark:ring-red-800 min-w-[300px]">
        <svg class="h-5 w-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ session('error') }}</p>
    </div>
</div>
@endif

{{-- ===== MARCAS ===== --}}
    @if($marcas->count())
        {{-- Skeleton (solo se muestra mientras loading = true) --}}
        <div x-show="loading" x-cloak class="space-y-4">
            @for ($i = 0; $i < min(3, $marcas->count()); $i++)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden animate-pulse">
                <div class="px-5 py-3.5 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="w-32 h-5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="w-16 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-16 h-7 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                        <div class="w-16 h-7 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                        <div class="w-16 h-7 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <div class="grid grid-cols-[minmax(180px,auto)_minmax(160px,auto)_minmax(140px,auto)] gap-4">
                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Contenido real (se muestra cuando loading = false) --}}
        <div x-show="!loading" x-cloak>
            @foreach($marcas as $marca)
                @php
                    $totalModelos  = $marca->modelos->count();
                    $limiteModelos = 20;
                    $pct           = $limiteModelos > 0 ? round(($totalModelos / $limiteModelos) * 100) : 0;
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden transition-all duration-300 hover:shadow-md"
                    data-marca-id="{{ $marca->id_marca }}"
                    id="marca-card-{{ $marca->id_marca }}"
                    data-limit="{{ $limiteModelos }}"
                    x-data="{ abierto: false }"
                    @abrir-acordeon="abierto = true">

                    {{-- Header marca (igual que antes) --}}
                    <div class="px-5 py-3.5 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">
                        <button type="button" @click="abierto = !abierto"
                            class="flex items-center gap-2.5 flex-1 min-w-0 text-left">
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-300 shrink-0"
                                :class="abierto ? 'rotate-90' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-white truncate marca-nombre">{{ $marca->nombre_marca }}</span>
                            <span class="text-xs text-gray-400 shrink-0 modelos-count">{{ $totalModelos }} {{ Str::plural('modelo', $totalModelos) }}</span>
                        </button>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <button @click.stop="abrirEditMarca('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}')"
                                class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 transition hover:border-yellow-500 dark:hover:border-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:scale-105 transform duration-200">
                                Editar
                            </button>
                            @if($totalModelos < $limiteModelos)
                            <button @click.stop="abrirModeloModal('{{ $marca->id_marca }}', '{{ $marca->nombre_marca }}')"
                                class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 transition hover:border-green-500 dark:hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 btn-add-modelo hover:scale-105 transform duration-200">
                                + Modelo
                            </button>
                            @else
                            <button class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 opacity-50 cursor-not-allowed btn-add-modelo" disabled>
                                + Modelo
                            </button>
                            @endif
                            <button @click.stop="abrirDeleteModal('marca', '{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}', '{{ route('admin.catalogo.marcas.destroy', $marca->id_marca) }}')"
                                class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 rounded-md px-3 py-1.5 transition hover:scale-105 transform duration-200">
                                Eliminar
                            </button>
                        </div>
                    </div>

                    {{-- Contenido colapsable (igual que antes) --}}
                    <div x-show="abierto"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1">

                        <div class="overflow-x-auto overflow-y-visible">
                            {{-- columnas header --}}
                            <div class="grid grid-cols-[minmax(180px,auto)_minmax(160px,auto)_minmax(140px,auto)] sm:grid-cols-3 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 sticky top-0 z-10">
                                <div class="px-5 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400 border-r dark:border-gray-700">Modelo</div>
                                <div class="px-4 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400 border-r dark:border-gray-700">Colores</div>
                                <div class="px-4 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400">Voltajes</div>
                            </div>

                            <div class="max-h-[320px] overflow-y-auto">
                                <div class="modelos-tbody">
                                    @forelse($marca->modelos as $modelo)
                                    <div class="grid grid-cols-[minmax(180px,auto)_minmax(160px,auto)_minmax(140px,auto)] sm:grid-cols-3 border-b dark:border-gray-700 last:border-b-0" data-modelo-id="{{ $modelo->id_modelo }}">
                                        {{-- Nombre modelo --}}
                                        <div class="px-5 py-3 border-r dark:border-gray-700 flex items-center justify-between group min-w-0">
                                            <span class="text-sm text-gray-800 dark:text-gray-200 modelo-nombre break-words" title="{{ $modelo->nombre_modelo }}">{{ $modelo->nombre_modelo }}</span>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="abrirEditModelo('{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ $modelo->id_marca }}')"
                                                    class="p-1 text-gray-400 hover:text-gray-700 dark:hover:text-white transition hover:scale-110 transform duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="abrirDeleteModal('modelo', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ route('admin.catalogo.modelos.destroy', $modelo->id_modelo) }}')"
                                                    class="p-1 text-gray-400 hover:text-red-500 transition hover:scale-110 transform duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Colores --}}
                                        <div class="px-4 py-3 border-r dark:border-gray-700">
                                            <div class="flex items-center gap-1.5 flex-wrap colores-container">
                                                @foreach($modelo->colores as $color)
                                                @php
                                                    $hexes  = colorHexes($color->color);
                                                    $nombre = colorNombre($color->color);
                                                    $esComb = colorEsCombinado($color->color);
                                                @endphp
                                                <div class="relative group/chip w-7 h-7 rounded-md border border-black/10 dark:border-white/10 overflow-hidden cursor-pointer flex-shrink-0 transition-all duration-200 hover:scale-110" title="{{ $nombre }}" data-color-id="{{ $color->id_color }}">
                                                    @if($esComb)
                                                        <div class="absolute left-0 top-0 w-1/2 h-full" style="background:{{ $hexes[0] }}"></div>
                                                        <div class="absolute right-0 top-0 w-1/2 h-full" style="background:{{ $hexes[1] ?? $hexes[0] }}"></div>
                                                    @else
                                                        <div class="w-full h-full" style="background:{{ $hexes[0] }}"></div>
                                                    @endif
                                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/chip:opacity-100 transition flex items-center justify-center gap-0.5">
                                                        <button @click="abrirEditColor('{{ $color->id_color }}', '{{ addslashes($color->color) }}', '{{ $modelo->id_modelo }}')" class="text-white text-[9px] p-0.5 hover:text-yellow-300 transition">✎</button>
                                                        <button @click="abrirDeleteModal('color', '{{ $color->id_color }}', '{{ addslashes($nombre) }}', '{{ route('admin.catalogo.colores.destroy', $color->id_color) }}')" class="text-white text-[9px] p-0.5 hover:text-red-300 transition">✕</button>
                                                    </div>
                                                </div>
                                                @endforeach
                                                <button @click="abrirColorModal('{{ $marca->id_marca }}', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ addslashes($marca->nombre_marca) }}')" class="w-7 h-7 rounded-md border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:border-gray-400 transition text-sm hover:scale-110 transform duration-200">+</button>
                                            </div>
                                        </div>

                                        {{-- Voltajes --}}
                                        <div class="px-4 py-3">
                                            <div class="flex items-center gap-1.5 flex-wrap voltajes-container">
                                                @foreach($modelo->voltajes as $voltaje)
                                                <span class="group/pill inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full px-2.5 py-0.5 text-xs text-gray-700 dark:text-gray-300 transition-all duration-200 hover:scale-105" data-mvoltaje-id="{{ $voltaje->pivot->id_mvoltaje }}">
                                                    {{ $voltaje->voltaje }}
                                                    <button @click="abrirDeleteModal('voltaje', '{{ $voltaje->pivot->id_mvoltaje }}', '{{ $voltaje->voltaje }}', '{{ route('admin.catalogo.modelo-voltaje.destroy', $voltaje->pivot->id_mvoltaje) }}')" class="text-gray-400 hover:text-red-500 transition opacity-0 group-hover/pill:opacity-100 text-[10px] leading-none">✕</button>
                                                </span>
                                                @endforeach
                                                <button @click="abrirVoltajeModal('{{ $marca->id_marca }}', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ addslashes($marca->nombre_marca) }}')" class="inline-flex items-center gap-1 border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2.5 py-0.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:border-gray-400 transition hover:scale-105 transform duration-200">+ voltaje</button>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500 sin-modelos">Sin modelos. Agrega el primero con el botón "+ Modelo".</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Barra límite modelos --}}
                        <div class="px-5 py-2.5 border-t dark:border-gray-700 flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/40">
                            <span class="text-[11px] text-gray-400 whitespace-nowrap progress-label">{{ $totalModelos }} / {{ $limiteModelos }} modelos</span>
                            <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 progress-fill {{ $pct >= 80 ? 'bg-amber-400' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-12 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">No tienes marcas registradas.</p>
            <p class="text-xs text-gray-400 mt-1">Crea tu primera marca con el botón "+ Nueva marca".</p>
        </div>
    
@endif


{{-- ===================================================
     MODALES (sin cambios, se mantienen igual)
===================================================== --}}

{{-- ── MODAL: Nueva marca ── --}}
<div x-show="marcaModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="marcaModal = false">
    <div x-show="marcaModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Nueva marca</h3>
        <form @submit.prevent="submitMarca($el.querySelector('[name=nombre_marca]').value)">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre de la marca</label>
            <input type="text" name="nombre_marca" required autofocus
                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 mb-4"
                placeholder="Ej. Yadea">
            <div class="flex justify-end gap-2">
                <button type="button" @click="marcaModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
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

{{-- ── MODAL: Nuevo modelo ── --}}
<div x-show="modeloModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-[9999] px-4"
    @click.self="modeloModal = false">
    <div x-show="modeloModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo Modelo</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                   x-text="marcaActual ? 'Marca: ' + marcaActual.nombre : ''"></p>
            </div>
        </div>
        <form @submit.prevent="submitModelo(nombreModelo, marcaActual?.id)" x-data="{ nombreModelo: '' }">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre del modelo</label>
            <input type="text" x-model="nombreModelo" required autofocus
                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 mb-4"
                placeholder="Ej. Tauro">
            <div class="flex justify-end gap-2">
                <button type="button" @click="modeloModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="!nombreModelo.trim() || submitting"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
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

{{-- ── MODAL: Nuevo color ── --}}
<div x-show="colorModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="colorModal = false">
    <div x-show="colorModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agregar Color</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                    x-text="modeloActual && marcaActual ? 'Modelo: ' + modeloActual.nombre + ' ~ ' + marcaActual.nombre : ''"></p>
            </div>
        </div>
        <form @submit.prevent="submitColor(modeloActual?.id)">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                    @click="$refs.picker1.click()">
                    <div class="absolute inset-0" :style="'background:'+colorHex1"></div>
                    <div x-show="sugirendoHex1" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                </div>
                <input type="color" x-ref="picker1" class="sr-only" x-model="colorHex1">
                <input type="text" x-model="colorNombre1" @input="onNombre1($event.target.value)" required
                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                    placeholder="Ej. Rojo">
            </div>
            <div x-show="colorCombinado" x-transition>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color 2 <span class="text-gray-400">(combinado)</span></label>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                        @click="$refs.picker2.click()">
                        <div class="absolute inset-0" :style="'background:'+colorHex2"></div>
                        <div x-show="sugirendoHex2" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                    </div>
                    <input type="color" x-ref="picker2" class="sr-only" x-model="colorHex2">
                    <input type="text" x-model="colorNombre2" @input="onNombre2($event.target.value)"
                        class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                        placeholder="Ej. Azul">
                </div>
            </div>
            <p x-show="colorError" x-text="colorError" class="text-xs text-red-500 mb-2"></p>
            <button type="button" @click="colorCombinado = !colorCombinado; colorNombre2 = ''"
                :class="colorCombinado ? 'border-red-200 dark:border-red-800 text-red-500 dark:text-red-400' : 'border-dashed border-gray-300 dark:border-gray-600 text-gray-400'"
                class="w-full flex items-center justify-center gap-2 border rounded-lg px-3 py-2 text-xs mb-3 hover:opacity-80 transition">
                <span x-text="colorCombinado ? '✕ Quitar combinación' : '+ Agregar combinación de color'"></span>
            </button>
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2.5 mb-4">
                <div class="w-8 h-8 rounded-md border border-black/10 dark:border-white/10 overflow-hidden flex-shrink-0 relative">
                    <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:'+colorHex1"></div>
                    <div class="absolute right-0 top-0 w-1/2 h-full" :style="colorCombinado ? 'background:'+colorHex2 : 'background:'+colorHex1"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Se guardará como:
                    <span class="font-medium text-gray-800 dark:text-gray-200"
                        x-text="colorCombinado && colorNombre2 ? colorNombre1+'/'+colorNombre2 : colorNombre1 || '-'"></span>
                </p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="colorModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting || !colorNombre1.trim() || !!colorError || sugirendoHex1 || sugirendoHex2"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-show="!sugirendoHex1 && !sugirendoHex2 && !submitting">Guardar</span>
                    <span x-show="submitting" class="inline-flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Guardando...
                    </span>
                    <span x-show="!submitting && (sugirendoHex1 || sugirendoHex2)" x-cloak>Sugiriendo color...</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: Agregar voltaje ── --}}
<div x-show="voltajeModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="voltajeModal = false">
    <div x-show="voltajeModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agregar Voltaje</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                    x-text="modeloActual && marcaActual ? 'Modelo: ' + modeloActual.nombre + ' — ' + marcaActual.nombre : ''"></p>
            </div>
        </div>
        <form @submit.prevent="submitVoltaje(modeloActual?.id, $el.querySelector('[name=id_voltaje]').value)">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje disponible</label>
            <div x-data="{
                voltajesNegocio: {{ Js::from(\App\Models\Voltaje::where('id_negocio', auth()->user()->id_negocio)->orderBy('voltaje')->get(['id_voltaje','voltaje'])) }},
                selectedVoltaje: '',
                get disponibles() {
                    const idModelo = modeloActual?.id;
                    if (!idModelo) return this.voltajesNegocio;
                    const yaAsignados = {{ Js::from(\App\Models\ModeloVoltaje::where('id_negocio', auth()->user()->id_negocio)->get(['id_modelo','id_voltaje'])->groupBy('id_modelo')->map(fn($rows) => $rows->pluck('id_voltaje')->toArray())) }};
                    const asignadosAlModelo = yaAsignados[idModelo] ?? [];
                    return this.voltajesNegocio.filter(v => !asignadosAlModelo.includes(v.id_voltaje));
                }
            }">
                <template x-if="disponibles.length === 0">
                    <p class="text-sm text-gray-400 dark:text-gray-500 py-3 text-center">Todos los voltajes ya están asignados.</p>
                </template>
                <template x-if="disponibles.length > 0">
                    <select name="id_voltaje" required x-model="selectedVoltaje"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 mb-4">
                        <option value="">Selecciona un voltaje</option>
                        <template x-for="v in disponibles" :key="v.id_voltaje">
                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                        </template>
                    </select>
                </template>
                <p class="text-xs text-gray-400 mt-1 mb-4">
                    ¿No ves el voltaje?
                    <a href="{{ route('admin.catalogo.voltajes.index') }}"
                        class="text-gray-600 dark:text-gray-300 underline underline-offset-2 hover:text-gray-900 dark:hover:text-white transition">
                        Créalo aquí
                    </a>
                </p>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="voltajeModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="submitting || disponibles.length === 0 || !selectedVoltaje"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <span x-show="!submitting">Asignar voltaje</span>
                        <span x-show="submitting" class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Asignando...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: Editar marca ── --}}
<div x-show="editMarcaModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="editMarcaModal = false">
    <div x-show="editMarcaModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Marca</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="editMarcaData.nombre ? 'Nombre actual: ' + editMarcaData.nombre : ''"></p>
            </div>
        </div>
        <form @submit.prevent="submitEditMarca()">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre de la marca</label>
            <input type="text" name="nombre_marca" required
                :value="editMarcaData.nombre"
                @input="editMarcaData.nombre = $event.target.value"
                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" @click="editMarcaModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
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

{{-- ── MODAL: Editar modelo ── --}}
<div x-show="editModeloModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="editModeloModal = false">
    <div x-show="editModeloModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Modelo</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="editModeloData.nombre ? 'Modelo: ' + editModeloData.nombre : ''"></p>
            </div>
        </div>
        <form @submit.prevent="submitEditModelo()">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre del modelo</label>
            <input type="text" name="nombre_modelo" required
                :value="editModeloData.nombre"
                @input="editModeloData.nombre = $event.target.value"
                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" @click="editModeloModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
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

{{-- ── MODAL: Editar color ── --}}
<div x-show="editColorModal" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="editColorModal = false">
    <div x-show="editColorModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar color</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                   x-text="editColorData.combinado && editColorData.nombre2 ? editColorData.nombre1+'/'+editColorData.nombre2 : editColorData.nombre1 || 'Color'"></p>
            </div>
        </div>
        <form @submit.prevent="submitEditColor()">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                    @click="$refs.editPicker1.click()">
                    <div class="absolute inset-0" :style="'background:'+editColorData.hex1"></div>
                    <div x-show="editColorData.sugiriendo1" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                </div>
                <input type="color" x-ref="editPicker1" class="sr-only" x-model="editColorData.hex1">
                <input type="text" required
                    :value="editColorData.nombre1"
                    @input="editColorData.nombre1 = $event.target.value; onEditNombre1($event.target.value)"
                    class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                    placeholder="Ej. Rojo">
            </div>
            <div x-show="editColorData.combinado" x-transition>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color 2 <span class="text-gray-400">(combinado)</span></label>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                        @click="$refs.editPicker2.click()">
                        <div class="absolute inset-0" :style="'background:'+editColorData.hex2"></div>
                        <div x-show="editColorData.sugiriendo2" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                    </div>
                    <input type="color" x-ref="editPicker2" class="sr-only" x-model="editColorData.hex2">
                    <input type="text"
                        :value="editColorData.nombre2"
                        @input="editColorData.nombre2 = $event.target.value; onEditNombre2($event.target.value)"
                        class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400"
                        placeholder="Ej. Azul">
                </div>
            </div>
            <p x-show="editColorData.error" x-text="editColorData.error" class="text-xs text-red-500 mb-2"></p>
            <button type="button" @click="editColorData.combinado = !editColorData.combinado; editColorData.nombre2 = ''"
                :class="editColorData.combinado ? 'border-red-200 dark:border-red-800 text-red-500 dark:text-red-400' : 'border-dashed border-gray-300 dark:border-gray-600 text-gray-400'"
                class="w-full flex items-center justify-center gap-2 border rounded-lg px-3 py-2 text-xs mb-3 hover:opacity-80 transition">
                <span x-text="editColorData.combinado ? '✕ Quitar combinación' : '+ Agregar combinación de color'"></span>
            </button>
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2.5 mb-4">
                <div class="w-8 h-8 rounded-md border border-black/10 dark:border-white/10 overflow-hidden flex-shrink-0 relative">
                    <div class="absolute left-0 top-0 w-1/2 h-full" :style="'background:'+editColorData.hex1"></div>
                    <div class="absolute right-0 top-0 w-1/2 h-full"
                        :style="editColorData.combinado ? 'background:'+editColorData.hex2 : 'background:'+editColorData.hex1"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Se guardará como:
                    <span class="font-medium text-gray-800 dark:text-gray-200"
                        x-text="editColorData.combinado && editColorData.nombre2 ? editColorData.nombre1+'/'+editColorData.nombre2 : editColorData.nombre1 || '—'"></span>
                </p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="editColorModal = false"
                    class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting || !editColorData.nombre1.trim() || editColorData.sugiriendo1 || editColorData.sugiriendo2"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-show="!submitting && !editColorData.sugiriendo1 && !editColorData.sugiriendo2">Guardar cambios</span>
                    <span x-show="submitting" class="inline-flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Guardando...
                    </span>
                    <span x-show="!submitting && (editColorData.sugiriendo1 || editColorData.sugiriendo2)" x-cloak>Sugiriendo color...</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODALES: Confirmar eliminación (genérico) ── --}}
@foreach([
    ['marca',   'deleteMarcaModal'],
    ['modelo',  'deleteModeloModal'],
    ['color',   'deleteColorModal'],
    ['voltaje', 'deleteVoltajeModal'],
] as [$tipo, $modalVar])
<div x-show="{{ $modalVar }}" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[2px] justify-center z-50 px-4"
    @click.self="{{ $modalVar }} = false">
    <div x-show="{{ $modalVar }}"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center  justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar {{ $tipo }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Se eliminará permanentemente</p>
                <div class="text-center mt-1">
                    <span class="inline-block px-3 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-200"
                          x-text="deleteTarget.nombre"></span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                    Esta acción no se puede deshacer.
                    @if($tipo === 'marca') Si tiene modelos asociados no podrá eliminarse. @endif
                    @if($tipo === 'modelo') Si tiene bicicletas asociadas no podrá eliminarse. @endif
                </p>
            </div>
        </div>
        <form @submit.prevent="submitDelete()">
            <div class="flex justify-end gap-2">
                <button type="button" @click="{{ $modalVar }} = false"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="submitting"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
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
        </form>
    </div>
</div>
@endforeach


{{-- ===== WEBSOCKET: Reverb listener ===== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const idNegocio = document.querySelector('[data-negocio-id]')?.dataset?.negocioId;
    if (!idNegocio || !window.Echo) return;

    window.Echo.private(`catalogo.${idNegocio}`)
        .listen('.catalogo.actualizado', async (e) => {
            const idMarca = e.id_marca;
            if (!idMarca) return;

            const card = document.getElementById(`marca-card-${idMarca}`);

            try {
                const res = await fetch(`/admin/catalogo/marca-card/${idMarca}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    }
                });

                if (!res.ok) {
                    if (res.status === 404 && card) {
                        card.remove();
                    }
                    return;
                }

                const html = await res.text();

                if (card) {
                    card.outerHTML = html;
                } else {
                    const contenedor = document.querySelector('[data-negocio-id]');
                    const primerModal = contenedor.querySelector('[x-cloak]');
                    if (primerModal) {
                        primerModal.insertAdjacentHTML('beforebegin', html);
                    } else {
                        contenedor.insertAdjacentHTML('beforeend', html);
                    }
                }

                Alpine.initTree(document.getElementById(`marca-card-${idMarca}`));

            } catch (err) {
                console.error('Error recargando card:', err);
            }
        });
});
</script>

</div>
</x-app-layout>