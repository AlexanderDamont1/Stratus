<x-app-layout>
<div
     x-data="cargaMasiva()"
     x-init="init()"
     class="mx-auto space-y-6"
     >

    {{-- ===== ENCABEZADO (estilo garantías) ===== --}}
    <div>
        <a href="{{ route('bicicletas.index') }}"
           class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                  flex items-center gap-1 mb-2 transition w-fit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a bicicletas
        </a>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Registrar bicicletas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Carga masiva con número de serie</p>
        </div>
    </div>

    {{-- ===== MODAL: SIN MARCAS ===== --}}
    <div x-show="mostrarModalSinMarcas" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 px-4">
        <div x-show="mostrarModalSinMarcas"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-7 w-full max-w-sm border border-gray-200 dark:border-gray-700" @click.stop>
            <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-800/30 border border-amber-200 dark:border-amber-700 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-amber-800 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Sin marcas en el catálogo</h3>
            <p class="text-xs text-gray-400 leading-relaxed mb-5">
                Necesitas al menos una marca creada antes de poder registrar bicicletas.
            </p>
            <a href="{{ route('admin.catalogo.index') }}"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 active:scale-[0.98] transition inline-flex items-center justify-center gap-2">
                Ir al catálogo
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ===== MODAL: ESCANEAR QR ===== --}}
    <div x-show="scanModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-[100] px-4">
        <div x-show="scanModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-gray-200 dark:border-gray-700">

            <div class="flex items-center justify-between px-5 pt-5 pb-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        <span x-text="scanModo === 'secuencial' ? 'Escanear números de serie' : 'Escanear número de serie'"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5" x-show="scanModo === 'secuencial'">
                        Se llenan en orden ·
                        <span x-text="filas.filter(f => !f.num_serie).length"></span>
                        <span x-text="filas.filter(f => !f.num_serie).length === 1 ? ' pendiente' : ' pendientes'"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5" x-show="scanModo === 'individual'">
                        Fila <span x-text="scanFilaIdx !== null ? scanFilaIdx + 1 : ''"></span>
                    </p>
                </div>
                <button type="button" @click="cerrarScanner()"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-5 pb-3">
                <div class="relative rounded-xl overflow-hidden bg-black aspect-square">
                    <div id="qr-reader-region" class="w-full h-full"></div>
                    <div x-show="scanRecienLeido" x-cloak
                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
                         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0"
                         class="absolute inset-0 bg-emerald-500/30 flex items-center justify-center pointer-events-none">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <div x-show="scanError" x-cloak
                     class="mt-3 flex items-center gap-2 text-xs text-red-500 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg px-3 py-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="scanError"></span>
                </div>

                <p class="text-xs text-gray-400 text-center mt-3">Apunta la cámara al código del vehículo</p>

                <button type="button" x-show="scanCamaras.length > 1" @click="cambiarCamara()"
                    class="w-full mt-3 flex items-center justify-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400
                           border border-gray-200 dark:border-gray-600 rounded-xl py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Cambiar de cámara — si se ve borroso o muy abierto
                </button>
            </div>

            <div class="px-5 pb-5" x-show="scanModo === 'secuencial'">
                <button type="button" @click="cerrarScanner()"
                    class="w-full text-xs font-medium text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-xl py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Terminar escaneo
                </button>
            </div>
        </div>
    </div>

    {{-- ===== FLASH (estilo garantías) ===== --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo === 'error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-800 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    <x-flash-messages />

    <form method="POST" action="{{ route('admin.bicicletas.storeMasivo') }}"
          @submit.prevent="enviar($el)" class="space-y-6">

        @csrf

        {{-- ===== GRID DE DOS COLUMNAS (estilo garantías) ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- COLUMNA IZQUIERDA: Configuración base + botón agregar --}}
            <div class="space-y-6 lg:col-span-1">

                {{-- ===== Configuración base ===== --}}
                <div class="bg-white dark:bg-gray-800 border rounded-xl transition-colors duration-300"
                     :class="plantillaCompleta
                        ? 'border-emerald-300 dark:border-emerald-700'
                        : 'border-gray-200 dark:border-gray-700'">

                    <div class="flex items-center justify-between px-5 pt-5 pb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Configuración base</p>
                            <p class="text-xs text-gray-400 mt-0.5">Aplica a todas las bicicletas que agregues</p>
                        </div>
                        <div x-show="plantillaCompleta"
                             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                             class="shrink-0 flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Listo
                        </div>
                    </div>

                    <div class="px-5 pb-5 space-y-4">

                        {{-- ── PICKER DE MODELOS ── --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-[10px] text-gray-400 font-medium uppercase tracking-wide">Modelo</label>
                                <button type="button" x-show="plantilla.id_modelo" @click="limpiarModelo()"
                                    x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    class="text-[11px] text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center gap-1 transition duration-150">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cambiar
                                </button>
                            </div>

                            <div x-show="plantilla.id_modelo"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100"
                                 class="flex items-center gap-3 px-3.5 py-3 bg-gray-900 dark:bg-white rounded-xl cursor-pointer" @click="limpiarModelo()">
                                <div class="w-8 h-8 rounded-lg bg-white/10 dark:bg-black/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white dark:text-gray-900 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-white dark:text-gray-900 truncate" x-text="nombreModeloById(plantilla.id_modelo)"></p>
                                    <p class="text-[11px] text-white/50 dark:text-gray-900/50 truncate" x-text="nombreMarcaById(plantilla.id_marca)"></p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-white/40 dark:text-gray-900/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>

                            <div x-show="!plantilla.id_modelo"
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 class="space-y-2">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                        <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text"
                                        x-model="filtroModelo"
                                        placeholder="Filtrar modelos…"
                                        autocomplete="off"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg pl-9 pr-8 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-400 transition">
                                    <button type="button" x-show="filtroModelo.length > 0" @click="filtroModelo = ''"
                                        class="absolute inset-y-0 right-2.5 flex items-center text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="max-h-52 overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-700/30 p-2.5 space-y-3">
                                    <template x-if="modelosFiltrados.length === 0">
                                        <div class="py-6 text-center">
                                            <p class="text-xs text-gray-400">Sin resultados para "<span class="font-medium" x-text="filtroModelo"></span>"</p>
                                        </div>
                                    </template>
                                    <template x-for="grupo in modelosFiltrados" :key="grupo.id_marca">
                                        <div class="space-y-1.5">
                                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest px-1"
                                               x-text="grupo.nombre_marca"></p>
                                            <div class="grid grid-cols-2 gap-1">
                                                <template x-for="mo in grupo.modelos" :key="mo.id_modelo">
                                                    <button type="button"
                                                        @click="seleccionarModelo(grupo, mo)"
                                                        class="text-left px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-150 active:scale-95">
                                                        <span class="text-[11px] font-semibold text-gray-800 dark:text-gray-200 truncate block leading-tight" x-text="mo.nombre_modelo"></span>
                                                        <span class="text-[9px] text-gray-400 block mt-0.5">
                                                            <span x-text="mo.colores.length"></span> col · <span x-text="mo.voltajes.length"></span> V
                                                        </span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- ── Color y Voltaje (PLANTILLA GLOBAL) ── --}}
                        <div class="grid grid-cols-2 gap-2.5" x-show="plantilla.id_modelo"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                            {{-- COLOR PICKER — plantilla global --}}
                            <div class="space-y-1.5" x-data="{ abiertoColor: false }" @click.outside="abiertoColor = false">
                                <label class="block text-[10px] text-gray-400 font-medium uppercase tracking-wide">Color</label>
                                <div class="relative">
                                    <button type="button"
                                        @click="abiertoColor = !abiertoColor"
                                        :disabled="!coloresDisponibles.length"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                        <template x-if="!plantilla.id_color">
                                            <span class="text-gray-400 flex-1 text-xs">Elige color</span>
                                        </template>
                                        <template x-if="plantilla.id_color">
                                            <span class="flex items-center gap-2 flex-1 min-w-0">
                                                <template x-if="parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).hexes.length >= 2">
                                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                                        <span class="absolute left-0 top-0 w-1/2 h-full"
                                                              :style="'background:' + parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).hexes[0]"></span>
                                                        <span class="absolute right-0 top-0 w-1/2 h-full"
                                                              :style="'background:' + parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).hexes[1]"></span>
                                                    </span>
                                                </template>
                                                <template x-if="parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).hexes.length < 2">
                                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                                          :style="'background:' + parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).hexes[0]"></span>
                                                </template>
                                                <span class="text-gray-900 dark:text-white truncate text-xs"
                                                      x-text="parsearColor((coloresDisponibles.find(c => String(c.id_color) === plantilla.id_color) || {color:''}).color).nombre"></span>
                                            </span>
                                        </template>
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-150" :class="abiertoColor ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="abiertoColor" x-cloak
                                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full min-w-[180px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                        <div class="max-h-48 overflow-y-auto p-1">
                                            <template x-for="c in coloresDisponibles" :key="c.id_color">
                                                <button type="button"
                                                    @click="plantilla.id_color = String(c.id_color); abiertoColor = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                    :class="String(c.id_color) === plantilla.id_color ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                    <template x-if="parsearColor(c.color).hexes.length >= 2">
                                                        <span class="w-5 h-5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                                            <span class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                            <span class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[1]"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="parsearColor(c.color).hexes.length < 2">
                                                        <span class="w-5 h-5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                                              :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                    </template>
                                                    <span class="text-sm text-gray-800 dark:text-gray-200 truncate" x-text="parsearColor(c.color).nombre"></span>
                                                    <template x-if="String(c.id_color) === plantilla.id_color">
                                                        <svg class="w-3.5 h-3.5 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- VOLTAJE --}}
                            <div class="space-y-1.5" x-data="{ abiertoVoltaje: false }" @click.outside="abiertoVoltaje = false">
                                <label class="block text-[10px] text-gray-400 font-medium uppercase tracking-wide">Voltaje</label>
                                <div class="relative">
                                    <button type="button"
                                        @click="abiertoVoltaje = !abiertoVoltaje"
                                        :disabled="!voltajesDisponibles.length"
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                        <template x-if="!plantilla.id_voltaje">
                                            <span class="text-gray-400 flex-1 text-xs">Elige voltaje</span>
                                        </template>
                                        <template x-if="plantilla.id_voltaje">
                                            <span class="flex items-center gap-1.5 flex-1 min-w-0">
                                                <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                                </svg>
                                                <span class="text-gray-900 dark:text-white truncate text-xs"
                                                    x-text="(voltajesDisponibles.find(v => String(v.id_voltaje) === plantilla.id_voltaje) || {}).voltaje || 'Voltaje'"></span>
                                            </span>
                                        </template>
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-150" :class="abiertoVoltaje ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="abiertoVoltaje" x-cloak
                                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                        class="absolute z-50 mt-1 w-full min-w-[180px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                        <div class="max-h-48 overflow-y-auto p-1">
                                            <template x-for="v in voltajesDisponibles" :key="v.id_voltaje">
                                                <button type="button"
                                                    @click="plantilla.id_voltaje = String(v.id_voltaje); abiertoVoltaje = false"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                    :class="String(v.id_voltaje) === plantilla.id_voltaje ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                    <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                                    </svg>
                                                    <span class="text-sm text-gray-800 dark:text-gray-200" x-text="v.voltaje"></span>
                                                    <template x-if="String(v.id_voltaje) === plantilla.id_voltaje">
                                                        <svg class="w-3.5 h-3.5 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ===== Botón agregar ===== --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-3">
                    <button type="button" @click="agregarFila()" :disabled="!plantillaCompleta"
                        class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 active:scale-[0.98] transition-all duration-150 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar número de serie
                    </button>

                    <p x-show="filas.length > 0" class="text-xs text-gray-400 text-center">
                        <span class="font-semibold text-gray-600 dark:text-gray-300" x-text="filas.filter(f => f.num_serie.length === 17 && !f.error).length"></span>/<span x-text="filas.length"></span> válidas
                    </p>
                </div>

            </div>

            {{-- COLUMNA DERECHA: Lista de números de serie --}}
            <div class="lg:col-span-2 flex flex-col">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl flex flex-col flex-1">

                    <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700 rounded-t-xl">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Números de serie</p>
                            <p class="text-xs text-gray-400 mt-0.5">Series de exactamente 17 caracteres</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" x-show="filas.length > 0" @click="abrirEscaneoSecuencial()"
                                :disabled="filas.filter(f => !f.num_serie).length === 0"
                                class="flex items-center gap-1.5 text-xs font-semibold bg-gray-900 dark:bg-white dark:text-gray-900
                                       text-white px-3 py-2 rounded-lg hover:opacity-90 active:scale-[0.98] transition
                                       disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-14h2a1 1 0 011 1v2m-3 11h2a1 1 0 001-1v-2M7 12h10"/>
                                </svg>
                                Escanear QR
                            </button>
                            <span x-show="filas.length > 0"
                                  x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                                  class="text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-lg" x-text="filas.length"></span>
                        </div>
                    </div>

                    {{-- Vacío --}}
                    <div x-show="filas.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <template x-if="!plantillaCompleta"><span>Completa la configuración base primero</span></template>
                            <template x-if="plantillaCompleta"><span>Pulsa <strong class="text-gray-700 dark:text-gray-200">Agregar</strong> para ingresar series</span></template>
                        </p>
                        <p class="text-xs text-gray-400 mt-1 max-w-[220px]">Cada fila representa una bicicleta individual con su número de serie único</p>
                    </div>

                    {{-- ===== DESKTOP ===== --}}
                    <div x-show="filas.length > 0" class="hidden md:block flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60" style="max-height: calc(100vh - 220px);">
                        <template x-for="(fila, idx) in filas" :key="fila._uid">
                            <div class="group px-4 py-2.5 hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition-colors duration-100">

                                <input type="hidden" :name="`bicicletas[${idx}][num_serie]`"  :value="fila.num_serie">
                                <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="fila.id_modelo">
                                <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="fila.id_color">
                                <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="fila.id_voltaje">

                                <div class="flex items-center gap-2.5">
                                    <span class="text-xs text-gray-300 dark:text-gray-600 w-4 shrink-0 text-right" x-text="idx + 1"></span>

                                    {{-- Input serie más grande --}}
                                    <div class="flex-1 min-w-0 max-w-sm">
                                        <input type="text"
                                            x-model="fila.num_serie"
                                            @input="fila.num_serie = $event.target.value.toUpperCase(); validarSerie(idx)"
                                            maxlength="17"
                                            placeholder="X3P9L2N8T4R6W7Q5Z"
                                            class="w-full border rounded-lg px-4 py-2 text-sm tracking-widest font-mono focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600"
                                            :class="{
                                                'border-emerald-300 focus:ring-emerald-300 dark:border-emerald-700': fila.num_serie.length === 17 && !fila.error,
                                                'border-red-300 focus:ring-red-300 dark:border-red-700': fila.error,
                                                'border-amber-300 focus:ring-amber-300 dark:border-amber-700': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                                'border-gray-200 dark:border-gray-600 focus:ring-gray-300 dark:focus:ring-gray-500': fila.num_serie.length === 0 && !fila.error
                                            }">
                                    </div>

                                    {{-- Escanear (solo si no tiene serie) / Borrar (solo si sí tiene) --}}
                                    <button type="button" x-show="fila.num_serie.length === 0" @click="abrirEscaneoFila(idx)"
                                        class="shrink-0 flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400
                                               border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1.5
                                               hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-14h2a1 1 0 011 1v2m-3 11h2a1 1 0 001-1v-2M7 12h10"/>
                                        </svg>
                                        Escanear
                                    </button>
                                    <button type="button" x-show="fila.num_serie.length > 0" @click="fila.num_serie = ''; validarSerie(idx)"
                                        title="Borrar número de serie"
                                        class="shrink-0 w-6 h-6 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600
                                               hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    {{-- Color (con anillo si está seleccionado) --}}
                                    <span class="w-4 h-4 rounded-sm shrink-0 inline-block transition duration-150"
                                        :class="{
                                            'border border-black/10 dark:border-white/10': fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color),
                                            'border-2 border-dashed border-gray-300 dark:border-gray-500': !fila.id_color || !filaColores(idx).find(c => String(c.id_color) === fila.id_color)
                                        }"
                                        :style="fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color) ? 'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0] : ''"
                                        :title="fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color) ? nombreColor(fila) : 'Sin color'">
                                    </span>

                                    {{-- Voltaje (con color ámbar si está seleccionado) --}}
                                    <i class="shrink-0 transition duration-150"
                                       :class="fila.id_voltaje ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'"
                                       :title="nombreVoltaje(fila)">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                        </svg>
                                    </i>

                                    <button type="button" @click="fila.expanded = !fila.expanded"
                                        class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 transition duration-150"
                                        :class="fila.expanded ? 'text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                                        title="Config individual">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <button type="button" @click="quitarFila(idx)"
                                        class="w-6 h-6 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition duration-150 shrink-0 opacity-0 group-hover:opacity-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    <span x-show="fila.num_serie.length === 17 && !fila.error"
                                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                                        class="text-emerald-500 shrink-0 w-5 h-5 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                </div>

                                <div x-show="fila.error"
                                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                     class="flex items-center gap-1 text-[11px] text-red-500 mt-1 ml-[26px]">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="fila.error"></span>
                                </div>

                                {{-- Override individual DESKTOP (pickers con teleport para escapar del overflow) --}}
                                <div x-show="fila.expanded"
                                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                                     class="mt-2 ml-[26px] pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div class="grid grid-cols-4 gap-1.5 max-w-xl">

                                        {{-- MARCA PICKER — override desktop (teleport) --}}
                                        <div x-data="{ abiertoMarca: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoMarca = false" class="relative">
                                            <button type="button"
                                                @click="
                                                    const r = $el.getBoundingClientRect();
                                                    coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                    abiertoMarca = !abiertoMarca
                                                "
                                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <span class="flex-1 truncate" :class="fila.id_marca ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
                                                      x-text="fila.id_marca ? ((catalogo.find(m => String(m.id_marca) === fila.id_marca) || {}).nombre_marca || 'Marca') : 'Marca'"></span>
                                                <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoMarca ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <template x-teleport="body">
                                                <div x-show="abiertoMarca" x-cloak
                                                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                     @click.outside="abiertoMarca = false"
                                                     :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                     class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                    <div class="max-h-48 overflow-y-auto p-1">
                                                        <template x-for="m in catalogo" :key="m.id_marca">
                                                            <button type="button"
                                                                @click="fila.id_marca = String(m.id_marca); onFilaMarca(idx); abiertoMarca = false"
                                                                class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                :class="String(m.id_marca) === fila.id_marca ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="m.nombre_marca"></span>
                                                                <template x-if="String(m.id_marca) === fila.id_marca">
                                                                    <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </template>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- MODELO PICKER — override desktop (teleport) --}}
                                        <div x-data="{ abiertoModelo: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoModelo = false" class="relative">
                                            <button type="button"
                                                @click="
                                                    if (!filaModelos(idx).length) return;
                                                    const r = $el.getBoundingClientRect();
                                                    coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                    abiertoModelo = !abiertoModelo
                                                "
                                                :disabled="!filaModelos(idx).length"
                                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <span class="flex-1 truncate" :class="fila.id_modelo ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
                                                      x-text="fila.id_modelo ? ((filaModelos(idx).find(mo => String(mo.id_modelo) === fila.id_modelo) || {}).nombre_modelo || 'Modelo') : 'Modelo'"></span>
                                                <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoModelo ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <template x-teleport="body">
                                                <div x-show="abiertoModelo" x-cloak
                                                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                     @click.outside="abiertoModelo = false"
                                                     :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                     class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                    <div class="max-h-48 overflow-y-auto p-1">
                                                        <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                                            <button type="button"
                                                                @click="fila.id_modelo = String(mo.id_modelo); onFilaModelo(idx); abiertoModelo = false"
                                                                class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                :class="String(mo.id_modelo) === fila.id_modelo ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="mo.nombre_modelo"></span>
                                                                <template x-if="String(mo.id_modelo) === fila.id_modelo">
                                                                    <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </template>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- COLOR PICKER — override desktop (teleport) --}}
                                        <div x-data="{ abiertoColor: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoColor = false" class="relative">
                                            <button type="button"
                                                @click="
                                                    if (!filaColores(idx).length) return;
                                                    const r = $el.getBoundingClientRect();
                                                    coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                    abiertoColor = !abiertoColor
                                                "
                                                :disabled="!filaColores(idx).length"
                                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <template x-if="!fila.id_color">
                                                    <span class="text-gray-400 flex-1">Color</span>
                                                </template>
                                                <template x-if="fila.id_color">
                                                    <span class="flex items-center gap-1.5 flex-1 min-w-0">
                                                        <template x-if="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes.length >= 2">
                                                            <span class="w-3.5 h-3.5 rounded-sm border border-black/10 overflow-hidden relative inline-flex shrink-0">
                                                                <span class="absolute left-0 top-0 w-1/2 h-full"
                                                                      :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0]"></span>
                                                                <span class="absolute right-0 top-0 w-1/2 h-full"
                                                                      :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[1]"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes.length < 2">
                                                            <span class="w-3.5 h-3.5 rounded-sm border border-black/10 shrink-0 inline-block"
                                                                  :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0]"></span>
                                                        </template>
                                                        <span class="truncate text-gray-800 dark:text-white"
                                                              x-text="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).nombre"></span>
                                                    </span>
                                                </template>
                                                <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoColor ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <template x-teleport="body">
                                                <div x-show="abiertoColor" x-cloak
                                                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                     @click.outside="abiertoColor = false"
                                                     :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                     class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                    <div class="max-h-40 overflow-y-auto p-1">
                                                        <template x-for="c in filaColores(idx)" :key="c.id_color">
                                                            <button type="button"
                                                                @click="fila.id_color = String(c.id_color); abiertoColor = false"
                                                                class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                :class="String(c.id_color) === fila.id_color ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                <template x-if="parsearColor(c.color).hexes.length >= 2">
                                                                    <span class="w-4 h-4 rounded-sm border border-black/10 overflow-hidden relative inline-flex shrink-0">
                                                                        <span class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                                        <span class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[1]"></span>
                                                                    </span>
                                                                </template>
                                                                <template x-if="parsearColor(c.color).hexes.length < 2">
                                                                    <span class="w-4 h-4 rounded-sm border border-black/10 shrink-0 inline-block"
                                                                          :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                                </template>
                                                                <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="parsearColor(c.color).nombre"></span>
                                                                <template x-if="String(c.id_color) === fila.id_color">
                                                                    <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </template>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- VOLTAJE PICKER — override desktop (teleport) --}}
                                        <div x-data="{ abiertoVoltaje: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoVoltaje = false" class="relative">
                                            <button type="button"
                                                @click="
                                                    if (!filaVoltajes(idx).length) return;
                                                    const r = $el.getBoundingClientRect();
                                                    coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                    abiertoVoltaje = !abiertoVoltaje
                                                "
                                                :disabled="!filaVoltajes(idx).length"
                                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                <template x-if="!fila.id_voltaje">
                                                    <span class="text-gray-400 flex-1">Voltaje</span>
                                                </template>
                                                <template x-if="fila.id_voltaje">
                                                    <span class="flex-1 text-gray-800 dark:text-white truncate"
                                                          x-text="(filaVoltajes(idx).find(v => String(v.id_voltaje) === fila.id_voltaje) || {}).voltaje || 'Voltaje'"></span>
                                                </template>
                                                <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoVoltaje ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <template x-teleport="body">
                                                <div x-show="abiertoVoltaje" x-cloak
                                                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                     @click.outside="abiertoVoltaje = false"
                                                     :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                     class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                    <div class="max-h-40 overflow-y-auto p-1">
                                                        <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                                            <button type="button"
                                                                @click="fila.id_voltaje = String(v.id_voltaje); abiertoVoltaje = false"
                                                                class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                :class="String(v.id_voltaje) === fila.id_voltaje ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                <span class="text-xs text-gray-800 dark:text-gray-200" x-text="v.voltaje"></span>
                                                                <template x-if="String(v.id_voltaje) === fila.id_voltaje">
                                                                    <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </template>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ===== MÓVIL ===== --}}
                    <div x-show="filas.length > 0" class="block md:hidden max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(fila, idx) in filas" :key="fila._uid">
                            <div class="px-4 py-4">
                                <input type="hidden" :name="`bicicletas[${idx}][num_serie]`"  :value="fila.num_serie">
                                <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="fila.id_modelo">
                                <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="fila.id_color">
                                <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="fila.id_voltaje">
                                <div class="flex items-start gap-3">
                                    <span class="text-xs text-gray-300 dark:text-gray-600 pt-2.5 w-4 shrink-0 text-right" x-text="idx + 1"></span>
                                    <div class="flex-1 space-y-1.5">
                                        <input type="text"
                                            x-model="fila.num_serie"
                                            @input="fila.num_serie = $event.target.value.toUpperCase().slice(0, 17);validarSerie(idx)"
                                            maxlength="17" placeholder="X3P9L2N8T4R6W7Q5Z"
                                            class="w-full border rounded-lg px-3 py-2 text-xs tracking-widest font-mono focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600"
                                            :class="{
                                                'border-emerald-300 focus:ring-emerald-300 dark:border-emerald-700': fila.num_serie.length === 17 && !fila.error,
                                                'border-red-300 focus:ring-red-300 dark:border-red-700': fila.error,
                                                'border-amber-300 focus:ring-amber-300 dark:border-amber-700': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                                'border-gray-200 dark:border-gray-600 focus:ring-gray-300': fila.num_serie.length === 0 && !fila.error
                                            }">
                                        <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300"
                                                 :style="`width:${(fila.num_serie.length/17)*100}%`"
                                                 :class="{
                                                    'bg-emerald-400': fila.num_serie.length===17 && !fila.error,
                                                    'bg-red-400': fila.error,
                                                    'bg-amber-400': fila.num_serie.length>0 && fila.num_serie.length<17 && !fila.error
                                                 }"></div>
                                        </div>
                                        <div x-show="fila.error" class="flex items-center gap-1 text-[11px] text-red-500">
                                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span x-text="fila.error"></span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-0.5">
                                            {{-- Escanear (solo si no tiene serie) / Borrar (solo si sí tiene) --}}
                                            <button type="button" x-show="fila.num_serie.length === 0" @click="abrirEscaneoFila(idx)"
                                                class="shrink-0 flex items-center gap-1 text-[10px] font-medium text-gray-500 dark:text-gray-400
                                                       border border-gray-200 dark:border-gray-600 rounded-lg px-1.5 py-1
                                                       hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 7V5a1 1 0 011-1h2M4 17v2a1 1 0 001 1h2m10-14h2a1 1 0 011 1v2m-3 11h2a1 1 0 001-1v-2M7 12h10"/>
                                                </svg>
                                                Escanear
                                            </button>
                                            <button type="button" x-show="fila.num_serie.length > 0" @click="fila.num_serie = ''; validarSerie(idx)"
                                                title="Borrar número de serie"
                                                class="shrink-0 w-5 h-5 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600
                                                       hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>

                                            {{-- Color --}}
                                            <span class="w-3.5 h-3.5 rounded-sm shrink-0 inline-block transition duration-150"
                                                :class="{
                                                    'border border-black/10 dark:border-white/10': fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color),
                                                    'border-2 border-dashed border-gray-300 dark:border-gray-500': !fila.id_color || !filaColores(idx).find(c => String(c.id_color) === fila.id_color)
                                                }"
                                                :style="fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color) ? 'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0] : ''"
                                                :title="fila.id_color && filaColores(idx).find(c => String(c.id_color) === fila.id_color) ? nombreColor(fila) : 'Sin color'">
                                            </span>
                                            {{-- Voltaje --}}
                                            <i class="shrink-0 transition duration-150"
                                               :class="fila.id_voltaje ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'"
                                               :title="nombreVoltaje(fila)">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                                </svg>
                                            </i>

                                            <button type="button" @click="fila.expanded = !fila.expanded"
                                                class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 transition duration-150 ml-1"
                                                :class="fila.expanded ? 'text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'"
                                                title="Config individual">
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                           
                                            <span x-show="fila.num_serie.length === 17 && !fila.error"
                                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                                                class="text-emerald-500 shrink-0 w-5 h-5 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                        </div>

                                        {{-- Override individual MÓVIL (pickers con teleport) --}}
                                        <div x-show="fila.expanded"
                                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                                             class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">

                                            {{-- MARCA PICKER — override móvil (teleport) --}}
                                            <div x-data="{ abiertoMarca: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoMarca = false" class="relative">
                                                <button type="button"
                                                    @click="
                                                        const r = $el.getBoundingClientRect();
                                                        coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                        abiertoMarca = !abiertoMarca
                                                    "
                                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <span class="flex-1 truncate" :class="fila.id_marca ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
                                                          x-text="fila.id_marca ? ((catalogo.find(m => String(m.id_marca) === fila.id_marca) || {}).nombre_marca || 'Marca') : 'Marca'"></span>
                                                    <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoMarca ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="abiertoMarca" x-cloak
                                                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                         x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                         @click.outside="abiertoMarca = false"
                                                         :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                         class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                        <div class="max-h-48 overflow-y-auto p-1">
                                                            <template x-for="m in catalogo" :key="m.id_marca">
                                                                <button type="button"
                                                                    @click="fila.id_marca = String(m.id_marca); onFilaMarca(idx); abiertoMarca = false"
                                                                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                    :class="String(m.id_marca) === fila.id_marca ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                    <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="m.nombre_marca"></span>
                                                                    <template x-if="String(m.id_marca) === fila.id_marca">
                                                                        <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                    </template>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- MODELO PICKER — override móvil (teleport) --}}
                                            <div x-data="{ abiertoModelo: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoModelo = false" class="relative">
                                                <button type="button"
                                                    @click="
                                                        if (!filaModelos(idx).length) return;
                                                        const r = $el.getBoundingClientRect();
                                                        coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                        abiertoModelo = !abiertoModelo
                                                    "
                                                    :disabled="!filaModelos(idx).length"
                                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                    <span class="flex-1 truncate" :class="fila.id_modelo ? 'text-gray-800 dark:text-white' : 'text-gray-400'"
                                                          x-text="fila.id_modelo ? ((filaModelos(idx).find(mo => String(mo.id_modelo) === fila.id_modelo) || {}).nombre_modelo || 'Modelo') : 'Modelo'"></span>
                                                    <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150 ml-auto" :class="abiertoModelo ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                <template x-teleport="body">
                                                    <div x-show="abiertoModelo" x-cloak
                                                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                         x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                         @click.outside="abiertoModelo = false"
                                                         :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                         class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                        <div class="max-h-48 overflow-y-auto p-1">
                                                            <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                                                <button type="button"
                                                                    @click="fila.id_modelo = String(mo.id_modelo); onFilaModelo(idx); abiertoModelo = false"
                                                                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                    :class="String(mo.id_modelo) === fila.id_modelo ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                    <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="mo.nombre_modelo"></span>
                                                                    <template x-if="String(mo.id_modelo) === fila.id_modelo">
                                                                        <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                        </svg>
                                                                    </template>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="grid grid-cols-2 gap-1.5">
                                                {{-- COLOR PICKER — override móvil (teleport) --}}
                                                <div x-data="{ abiertoColor: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoColor = false" class="relative">
                                                    <button type="button"
                                                        @click="
                                                            if (!filaColores(idx).length) return;
                                                            const r = $el.getBoundingClientRect();
                                                            coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                            abiertoColor = !abiertoColor
                                                        "
                                                        :disabled="!filaColores(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                        <template x-if="!fila.id_color">
                                                            <span class="text-gray-400 flex-1">Color</span>
                                                        </template>
                                                        <template x-if="fila.id_color">
                                                            <span class="flex items-center gap-1.5 flex-1 min-w-0">
                                                                <template x-if="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes.length >= 2">
                                                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 overflow-hidden relative inline-flex shrink-0">
                                                                        <span class="absolute left-0 top-0 w-1/2 h-full"
                                                                              :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0]"></span>
                                                                        <span class="absolute right-0 top-0 w-1/2 h-full"
                                                                              :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[1]"></span>
                                                                    </span>
                                                                </template>
                                                                <template x-if="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes.length < 2">
                                                                    <span class="w-3.5 h-3.5 rounded-sm border border-black/10 shrink-0 inline-block"
                                                                          :style="'background:' + parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).hexes[0]"></span>
                                                                </template>
                                                                <span class="truncate text-gray-800 dark:text-white"
                                                                      x-text="parsearColor((filaColores(idx).find(c => String(c.id_color) === fila.id_color) || {color:''}).color).nombre"></span>
                                                            </span>
                                                        </template>
                                                        <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150" :class="abiertoColor ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <template x-teleport="body">
                                                        <div x-show="abiertoColor" x-cloak
                                                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                             x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                             @click.outside="abiertoColor = false"
                                                             :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                             class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                            <div class="max-h-40 overflow-y-auto p-1">
                                                                <template x-for="c in filaColores(idx)" :key="c.id_color">
                                                                    <button type="button"
                                                                        @click="fila.id_color = String(c.id_color); abiertoColor = false"
                                                                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                        :class="String(c.id_color) === fila.id_color ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                        <template x-if="parsearColor(c.color).hexes.length >= 2">
                                                                            <span class="w-4 h-4 rounded-sm border border-black/10 overflow-hidden relative inline-flex shrink-0">
                                                                                <span class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                                                <span class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(c.color).hexes[1]"></span>
                                                                            </span>
                                                                        </template>
                                                                        <template x-if="parsearColor(c.color).hexes.length < 2">
                                                                            <span class="w-4 h-4 rounded-sm border border-black/10 shrink-0 inline-block"
                                                                                  :style="'background:' + parsearColor(c.color).hexes[0]"></span>
                                                                        </template>
                                                                        <span class="text-xs text-gray-800 dark:text-gray-200 truncate" x-text="parsearColor(c.color).nombre"></span>
                                                                        <template x-if="String(c.id_color) === fila.id_color">
                                                                            <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                            </svg>
                                                                        </template>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- VOLTAJE PICKER — override móvil (teleport) --}}
                                                <div x-data="{ abiertoVoltaje: false, coords: {top:0,left:0,width:0} }" @click.outside="abiertoVoltaje = false" class="relative">
                                                    <button type="button"
                                                        @click="
                                                            if (!filaVoltajes(idx).length) return;
                                                            const r = $el.getBoundingClientRect();
                                                            coords = { top: r.bottom + 4, left: r.left, width: r.width };
                                                            abiertoVoltaje = !abiertoVoltaje
                                                        "
                                                        :disabled="!filaVoltajes(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-700 text-left flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed transition focus:outline-none focus:ring-1 focus:ring-gray-400">
                                                        <template x-if="!fila.id_voltaje">
                                                            <span class="text-gray-400 flex-1">Voltaje</span>
                                                        </template>
                                                        <template x-if="fila.id_voltaje">
                                                            <span class="flex-1 text-gray-800 dark:text-white truncate"
                                                                  x-text="(filaVoltajes(idx).find(v => String(v.id_voltaje) === fila.id_voltaje) || {}).voltaje || 'Voltaje'"></span>
                                                        </template>
                                                        <svg class="w-3 h-3 text-gray-400 shrink-0 transition-transform duration-150" :class="abiertoVoltaje ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <template x-teleport="body">
                                                        <div x-show="abiertoVoltaje" x-cloak
                                                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                             x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                                                             @click.outside="abiertoVoltaje = false"
                                                             :style="`position: fixed; top:${coords.top}px; left:${coords.left}px; width:${Math.max(coords.width,160)}px;`"
                                                             class="z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden">
                                                            <div class="max-h-40 overflow-y-auto p-1">
                                                                <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                                                    <button type="button"
                                                                        @click="fila.id_voltaje = String(v.id_voltaje); abiertoVoltaje = false"
                                                                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-left"
                                                                        :class="String(v.id_voltaje) === fila.id_voltaje ? 'bg-gray-50 dark:bg-gray-700/50' : ''">
                                                                        <span class="text-xs text-gray-800 dark:text-gray-200" x-text="v.voltaje"></span>
                                                                        <template x-if="String(v.id_voltaje) === fila.id_voltaje">
                                                                            <svg class="w-3 h-3 text-gray-900 dark:text-white ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                            </svg>
                                                                        </template>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" @click="quitarFila(idx)"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition duration-150 shrink-0 mt-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ===== FOOTER: GUARDAR (estilo garantías) ===== --}}
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 rounded-b-xl">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs text-gray-400">
                                <span x-text="filas.filter(f => f.num_serie.length === 17 && !f.error).length"></span> válidas ·
                                <span x-text="filas.length"></span> total
                            </p>
                            <button type="submit" :disabled="submitting || !puedeGuardar"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5
                                       rounded-xl text-sm font-semibold hover:opacity-90 transition
                                       disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98]
                                       flex items-center gap-2 shadow-sm">
                                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="submitting ? 'Guardando...' : 'Guardar bicicletas'"></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== ERRORES LARAVEL ===== --}}
        @if($errors->any())
        <div class="bg-red-100 dark:bg-red-800/30 border border-red-200 dark:border-red-700 rounded-xl p-4">
            <p class="text-xs font-semibold text-red-800 dark:text-red-400 mb-2">Errores al guardar</p>
            <ul class="text-xs text-red-800 dark:text-red-400 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

    </form>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function cargaMasiva() {
    return {
        catalogo:              @json(json_decode($catalogoJson)),
        plantilla:             { id_marca: '', id_modelo: '', id_color: '', id_voltaje: '' },
        filas:                 [],
        submitting:            false,
        flashVisible:          false,
        flashMsg:              '',
        flashTipo:             'success',
        flashTimer:            null,
        _uid:                  0,
        mostrarModalSinMarcas: false,
        filtroModelo:          '',

        // ── Escaneo con cámara ──
        scanModal:       false,
        scanModo:        'secuencial', // 'secuencial' (FIFO, botón global) | 'individual' (una fila)
        scanFilaIdx:     null,
        scanError:       '',
        scanRecienLeido: false,
        scanCamaras:     [],   // [{ id, label }] — cámaras traseras disponibles
        scanCamaraIdx:   0,    // cuál de scanCamaras está activa ahora mismo
        _scanner:        null,
        _scanBusy:       false,

        get modelosDisponibles() {
            const m = this.catalogo.find(m => String(m.id_marca) === this.plantilla.id_marca);
            return m ? m.modelos : [];
        },
        get coloresDisponibles() {
            const mo = this.modelosDisponibles.find(mo => String(mo.id_modelo) === this.plantilla.id_modelo);
            return mo ? mo.colores : [];
        },
        get voltajesDisponibles() {
            const mo = this.modelosDisponibles.find(mo => String(mo.id_modelo) === this.plantilla.id_modelo);
            return mo ? mo.voltajes : [];
        },
        get plantillaCompleta() {
            return this.plantilla.id_marca && this.plantilla.id_modelo &&
                   this.plantilla.id_color && this.plantilla.id_voltaje;
        },
        get puedeGuardar() {
            return this.filas.length > 0 &&
                   this.filas.every(f => f.num_serie.length === 17 && !f.error && f.id_modelo && f.id_color && f.id_voltaje);
        },
        get modelosFiltrados() {
            const q = this.filtroModelo.toLowerCase().trim();
            return this.catalogo
                .map(marca => ({
                    id_marca:     marca.id_marca,
                    nombre_marca: marca.nombre_marca,
                    modelos: q
                        ? marca.modelos.filter(mo => mo.nombre_modelo.toLowerCase().includes(q))
                        : marca.modelos,
                }))
                .filter(g => g.modelos.length > 0);
        },

        seleccionarModelo(grupo, mo) {
            this.plantilla.id_marca   = String(grupo.id_marca);
            this.plantilla.id_modelo  = String(mo.id_modelo);
            this.plantilla.id_color   = '';
            this.plantilla.id_voltaje = '';
            this.filtroModelo         = '';
        },
        limpiarModelo() {
            this.plantilla.id_marca   = '';
            this.plantilla.id_modelo  = '';
            this.plantilla.id_color   = '';
            this.plantilla.id_voltaje = '';
            this.filtroModelo         = '';
        },

        parsearColor(colorStr) {
            const [nombre, hexParte] = (colorStr || '').split('|');
            const hexes = hexParte ? hexParte.split('/') : ['#cccccc'];
            return { nombre: nombre?.trim() || colorStr, hexes };
        },
        nombreMarcaById(id) {
            const m = this.catalogo.find(m => String(m.id_marca) === id);
            return m ? m.nombre_marca : '';
        },
        nombreModeloById(id) {
            for (const marca of this.catalogo) {
                const mo = marca.modelos.find(mo => String(mo.id_modelo) === id);
                if (mo) return mo.nombre_modelo;
            }
            return '';
        },
        nombreMarca(fila) {
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            return m ? m.nombre_marca : '—';
        },
        nombreModelo(fila) {
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return '—';
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            return mo ? mo.nombre_modelo : '—';
        },
        nombreColor(fila) {
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return '—';
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            if (!mo) return '—';
            const c = mo.colores.find(c => String(c.id_color) === fila.id_color);
            return c ? this.parsearColor(c.color).nombre : '—';
        },
        nombreVoltaje(fila) {
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return '—';
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            if (!mo) return '—';
            const v = mo.voltajes.find(v => String(v.id_voltaje) === fila.id_voltaje);
            return v ? v.voltaje : '—';
        },

        onMarca()  { this.plantilla.id_modelo = ''; this.plantilla.id_color = ''; this.plantilla.id_voltaje = ''; },
        onModelo() { this.plantilla.id_color  = ''; this.plantilla.id_voltaje = ''; },

        init() {
            if (this.catalogo.length === 0) this.mostrarModalSinMarcas = true;
        },

        agregarFila() {
            this.filas.push({
                _uid:       ++this._uid,
                num_serie:  '',
                id_marca:   this.plantilla.id_marca,
                id_modelo:  this.plantilla.id_modelo,
                id_color:   this.plantilla.id_color,
                id_voltaje: this.plantilla.id_voltaje,
                expanded:   false,
                error:      '',
            });
            this.$nextTick(() => {
                const inputs = this.$el.querySelectorAll('input[maxlength="17"]');
                if (inputs.length) inputs[inputs.length - 1].focus();
            });
        },
        quitarFila(idx) {
            this.filas.splice(idx, 1);
            this.revalidarTodas();
        },
        filaModelos(idx) {
            const m = this.catalogo.find(m => String(m.id_marca) === this.filas[idx].id_marca);
            return m ? m.modelos : [];
        },
        filaColores(idx) {
            const m = this.catalogo.find(m => String(m.id_marca) === this.filas[idx].id_marca);
            if (!m) return [];
            const mo = m.modelos.find(mo => String(mo.id_modelo) === this.filas[idx].id_modelo);
            return mo ? mo.colores : [];
        },
        filaVoltajes(idx) {
            const m = this.catalogo.find(m => String(m.id_marca) === this.filas[idx].id_marca);
            if (!m) return [];
            const mo = m.modelos.find(mo => String(mo.id_modelo) === this.filas[idx].id_modelo);
            return mo ? mo.voltajes : [];
        },
        onFilaMarca(idx)  { this.filas[idx].id_modelo = ''; this.filas[idx].id_color = ''; this.filas[idx].id_voltaje = ''; },
        onFilaModelo(idx) { this.filas[idx].id_color  = ''; this.filas[idx].id_voltaje = ''; },

        validarSerie(idx) {
            const val = this.filas[idx].num_serie.trim();
            if (val.length > 0 && val.length < 17) {
                const falt = 17 - val.length;
                this.filas[idx].error = `Faltan ${falt} carácter${falt !== 1 ? 'es' : ''}`;
            } else if (val.length === 17) {
                const dup = this.filas.some((f, i) => i !== idx && f.num_serie.trim() === val);
                this.filas[idx].error = dup ? 'N° de serie repetido' : '';
            } else {
                this.filas[idx].error = '';
            }
            this.revalidarTodas(idx);
        },
        revalidarTodas(exceptIdx = -1) {
            this.filas.forEach((fila, idx) => {
                if (idx === exceptIdx || fila.num_serie.length !== 17) return;
                const dup = this.filas.some((f, i) => i !== idx && f.num_serie.trim() === fila.num_serie.trim());
                fila.error = dup ? 'N° de serie repetido' : '';
            });
        },

        mostrarFlash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
        },

        // ── Escaneo con cámara ───────────────────────────────────────────────
        // Botón global: escanea en secuencia y llena, en orden (FIFO), la
        // primera fila que aún no tenga número de serie.
        abrirEscaneoSecuencial() {
            if (this.filas.filter(f => !f.num_serie).length === 0) return;
            this.scanModo    = 'secuencial';
            this.scanFilaIdx = null;
            this._abrirScanner();
        },
        // Botón por fila: solo aparece si esa fila no tiene serie, y un único
        // escaneo llena exclusivamente esa fila (no hay avance secuencial).
        abrirEscaneoFila(idx) {
            this.scanModo    = 'individual';
            this.scanFilaIdx = idx;
            this._abrirScanner();
        },
        async _abrirScanner() {
            this.scanError  = '';
            this.scanModal  = true;
            await this.$nextTick();

            if (typeof Html5Qrcode === 'undefined') {
                this.scanError = 'No se pudo cargar el lector de códigos.';
                return;
            }

            // Muchos celulares traen 2-3 lentes traseros (principal, gran
            // angular, telefoto). Pedir solo facingMode:'environment' deja
            // que el navegador elija cualquiera, y en varios Android termina
            // abriendo el gran angular — que enfoca mal de cerca y falla al
            // leer el código. Por eso se listan las cámaras y se elige a
            // propósito la que NO sea gran angular/telefoto.
            if (this.scanCamaras.length === 0) {
                try {
                    const camaras = await Html5Qrcode.getCameras();
                    this.scanCamaras   = camaras || [];
                    this.scanCamaraIdx = this._elegirCamaraTrasera(this.scanCamaras);
                } catch (e) {
                    this.scanCamaras = []; // sin permiso/soporte — cae a facingMode genérico
                }
            }

            this._iniciarCamara();
        },
        // Heurística para evitar el lente gran angular / telefoto: primero
        // busca por nombre (funciona en iOS Safari y varios Android con
        // labels descriptivos); si los nombres no dicen nada (ej. "camera2 0,
        // facing back"), los fabricantes casi siempre listan el lente
        // principal primero entre las traseras.
        _elegirCamaraTrasera(camaras) {
            if (!camaras || camaras.length <= 1) return 0;

            const evitar    = /ultra ?wide|gran ?angular|wide ?angle|tele ?photo|telefoto|macro/i;
            const esTrasera = c => /back|rear|trasera|environment/i.test(c.label || '');

            const traseras = camaras.filter(esTrasera);
            const base     = traseras.length ? traseras : camaras;

            const buenas = base.filter(c => !evitar.test(c.label || ''));
            const elegida = buenas[0] || base[0];

            return camaras.indexOf(elegida);
        },
        _iniciarCamara() {
            this._scanner = new Html5Qrcode('qr-reader-region');

            const camara = this.scanCamaras[this.scanCamaraIdx];
            const fuente = camara ? { deviceId: { exact: camara.id } } : { facingMode: 'environment' };

            this._scanner.start(
                fuente,
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (texto) => this._onScan(texto),
                () => {} // se dispara en cada frame sin lectura — se ignora
            ).catch(() => {
                this.scanError = 'No se pudo acceder a la cámara. Verifica los permisos del navegador.';
            });
        },
        // Botón manual "Cambiar de cámara" — por si la heurística automática
        // no acertó en algún modelo de celular en particular.
        async cambiarCamara() {
            if (this.scanCamaras.length < 2) return;
            this.scanCamaraIdx = (this.scanCamaraIdx + 1) % this.scanCamaras.length;

            const scanner = this._scanner;
            this._scanner = null;
            if (scanner) {
                try { await scanner.stop(); scanner.clear(); } catch (e) {}
            }
            this._iniciarCamara();
        },
        _onScan(textoLeido) {
            if (this._scanBusy) return;
            this._scanBusy = true;

            const texto = textoLeido.trim().toUpperCase();

            if (this.scanModo === 'individual') {
                this.filas[this.scanFilaIdx].num_serie = texto;
                this.validarSerie(this.scanFilaIdx);
                this._confirmarLectura();
                this.cerrarScanner();
                return;
            }

            // Secuencial: FIFO — llena la primera fila vacía, en el orden en
            // que se agregaron.
            const idx = this.filas.findIndex(f => !f.num_serie);
            if (idx === -1) { this.cerrarScanner(); return; }

            this.filas[idx].num_serie = texto;
            this.validarSerie(idx);
            this._confirmarLectura();

            if (this.filas.filter(f => !f.num_serie).length === 0) {
                this.mostrarFlash('Todas las series fueron escaneadas.');
                this.cerrarScanner();
                return;
            }

            // Pausa breve para no volver a leer el mismo código de inmediato,
            // luego reanuda para seguir con la siguiente fila vacía.
            if (this._scanner) {
                this._scanner.pause(true);
                setTimeout(() => {
                    if (this._scanner) { try { this._scanner.resume(); } catch (e) {} }
                    this._scanBusy = false;
                }, 700);
            } else {
                this._scanBusy = false;
            }
        },
        _confirmarLectura() {
            this.reproducirBeep();
            this.scanRecienLeido = true;
            setTimeout(() => this.scanRecienLeido = false, 400);
        },
        reproducirBeep() {
            try {
                const ctx  = new (window.AudioContext || window.webkitAudioContext)();
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
                osc.start();
                osc.stop(ctx.currentTime + 0.18);
            } catch (e) {}
        },
        cerrarScanner() {
            this._scanBusy = false;
            const scanner  = this._scanner;
            this._scanner  = null;
            this.scanModal = false;
            if (scanner) {
                scanner.stop().then(() => scanner.clear()).catch(() => {});
            }
        },

        enviar(form) {
            if (!this.puedeGuardar) return;
            this.submitting = true;
            form.submit();
        },
    };
}
</script>

</x-app-layout>