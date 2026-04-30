<x-app-layout>
<div
     x-data="cargaMasiva()"
     x-init="init()"
     class="max-w-4xl mx-auto space-y-5 pb-10"
     >

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex items-center gap-3 pt-1">
        <a href="{{ route('bicicletas.index') }}"
           class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-150 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">Registrar bicicletas</h2>
            <p class="text-xs text-gray-400">Carga masiva con número de serie</p>
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
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-7 w-full max-w-sm border border-gray-100 dark:border-gray-800" @click.stop>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Sin marcas en el catálogo</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-5">
                Necesitas al menos una marca creada antes de poder registrar bicicletas.
            </p>
            <a href="{{ route('admin.catalogo.index') }}"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:opacity-90 transition inline-flex items-center justify-center gap-2">
                Ir al catálogo
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ===== FLASH ===== --}}
    <x-flash-messages />


    {{-- ===== PASOS ===== --}}
    <div class="flex items-center">
        <template x-for="(step, i) in [{label:'Modelo'},{label:'N° Series'},{label:'Guardar'}]" :key="i">
            <div class="flex items-center" :class="i < 2 ? 'flex-1' : ''">
                <div class="flex flex-col items-center gap-1">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all duration-300"
                         :class="(paso - 1) > i
                            ? 'bg-emerald-500 text-white'
                            : (paso - 1) === i
                                ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-sm'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-400 border border-gray-200 dark:border-gray-700'">
                        <template x-if="(paso - 1) > i">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="(paso - 1) <= i">
                            <span x-text="i + 1"></span>
                        </template>
                    </div>
                    <span class="text-[10px] font-medium whitespace-nowrap transition-colors duration-300"
                          :class="(paso - 1) >= i ? 'text-gray-600 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600'"
                          x-text="step.label"></span>
                </div>
                <template x-if="i < 2">
                    <div class="flex-1 h-px mx-3 mb-4 transition-colors duration-500"
                         :class="(paso - 1) > i ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
                </template>
            </div>
        </template>
    </div>

    <form method="POST" action="{{ route('admin.bicicletas.storeMasivo') }}"
          @submit.prevent="enviar($el)" class="space-y-4">
        @csrf

        {{-- ===== PASO 1 ===== --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border transition-all duration-300"
             :class="plantillaCompleta
                ? 'border-emerald-300 dark:border-emerald-700 shadow-sm'
                : 'border-gray-200 dark:border-gray-800'">

            {{-- Header --}}
            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 text-[9px] font-black shrink-0">1</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Configuración base</span>
                    <span class="hidden sm:inline text-xs text-gray-400">— aplica a todas</span>
                </div>
                <div x-show="plantillaCompleta"
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                     class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Listo
                </div>
            </div>

            <div class="p-5 space-y-5">

                {{-- ── PICKER DE MODELOS ── --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Modelo</label>
                        <button type="button" x-show="plantilla.id_modelo" @click="limpiarModelo()"
                            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            class="text-[11px] text-gray-400 hover:text-red-500 dark:hover:text-red-400 font-medium flex items-center gap-1 transition duration-150">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cambiar
                        </button>
                    </div>

                    {{-- Modelo seleccionado --}}
                    <div x-show="plantilla.id_modelo"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100"
                         class="flex items-center gap-3 px-4 py-3 bg-gray-900 dark:bg-white rounded-xl cursor-pointer" @click="limpiarModelo()">
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

                    {{-- Picker: filtro + grid --}}
                    <div x-show="!plantilla.id_modelo"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-2">
                        {{-- Filtro --}}
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
                                class="w-full border border-gray-200 dark:border-gray-700 rounded-lg pl-9 pr-8 py-2 text-xs bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 focus:bg-white dark:focus:bg-gray-800 transition">
                            <button type="button" x-show="filtroModelo.length > 0" @click="filtroModelo = ''"
                                class="absolute inset-y-0 right-2.5 flex items-center text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Grid de modelos --}}
                        <div class="max-h-52 overflow-y-auto rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/30 p-2.5 space-y-3">
                            <template x-if="modelosFiltrados.length === 0">
                                <div class="py-6 text-center">
                                    <p class="text-xs text-gray-400">Sin resultados para "<span class="font-medium" x-text="filtroModelo"></span>"</p>
                                </div>
                            </template>
                            <template x-for="grupo in modelosFiltrados" :key="grupo.id_marca">
                                <div class="space-y-1.5">
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest px-1"
                                       x-text="grupo.nombre_marca"></p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                        <template x-for="mo in grupo.modelos" :key="mo.id_modelo">
                                            <button type="button"
                                                @click="seleccionarModelo(grupo, mo)"
                                                class="group text-left px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-900 dark:hover:border-white hover:bg-gray-900 dark:hover:bg-white transition-all duration-150 active:scale-95">
                                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 group-hover:text-white dark:group-hover:text-gray-900 truncate block leading-tight" x-text="mo.nombre_modelo"></span>
                                                <span class="text-[10px] text-gray-400 group-hover:text-white/60 dark:group-hover:text-gray-900/60 block mt-0.5 transition-colors duration-150">
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

                {{-- ── Color y Voltaje ── --}}
                <div class="grid grid-cols-2 gap-3" x-show="plantilla.id_modelo"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Color</label>
                        <select x-model="plantilla.id_color" :disabled="!coloresDisponibles.length"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <option value="">— Elige color —</option>
                            <template x-for="c in coloresDisponibles" :key="c.id_color">
                                <option :value="String(c.id_color)" x-text="c.color"></option>
                            </template>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Voltaje</label>
                        <select x-model="plantilla.id_voltaje" :disabled="!voltajesDisponibles.length"
                            class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-3.5 py-2.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <option value="">— Elige voltaje —</option>
                            <template x-for="v in voltajesDisponibles" :key="v.id_voltaje">
                                <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                            </template>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== PASO 2 ===== --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800">

            <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 text-[9px] font-black shrink-0">2</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Números de serie</span>
                    <span x-show="filas.length > 0"
                          x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                          class="text-[10px] font-black bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-1.5 py-0.5 rounded-full" x-text="filas.length"></span>
                </div>
                <button type="button" @click="agregarFila()" :disabled="!plantillaCompleta"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:opacity-80 active:scale-95 transition-all disabled:opacity-25 disabled:cursor-not-allowed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Agregar
                </button>
            </div>

            {{-- Vacío --}}
            <div x-show="filas.length === 0" class="px-5 py-10 text-center">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    <template x-if="!plantillaCompleta"><span>Completa la configuración base primero</span></template>
                    <template x-if="plantillaCompleta"><span>Pulsa <strong class="text-gray-700 dark:text-gray-200">Agregar</strong> para ingresar series</span></template>
                </p>
                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">Series de exactamente 17 caracteres</p>
            </div>

            {{-- ===== DESKTOP ===== --}}
            <div x-show="filas.length > 0" class="hidden md:block">
                <div class="overflow-x-auto max-h-[460px] overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800/80 backdrop-blur">
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10">#</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Serie</th>
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Configuración</th>
                                <th class="px-4 py-2.5 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/80">
                            <template x-for="(fila, idx) in filas" :key="fila._uid">
                                <tr class="align-top group hover:bg-gray-50/70 dark:hover:bg-gray-800/30 transition-colors duration-100">

                                    <td class="px-4 py-3.5 text-xs text-gray-300 dark:text-gray-600 w-10 pt-4" x-text="idx + 1"></td>

                                    <td class="px-4 py-3.5 w-64">
                                        <input type="hidden" :name="`bicicletas[${idx}][num_serie]`"  :value="fila.num_serie">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="fila.id_modelo">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="fila.id_color">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="fila.id_voltaje">

                                        <div class="space-y-1.5">
                                            <input type="text"
                                                x-model="fila.num_serie"
                                                @input="fila.num_serie = $event.target.value.toUpperCase(); validarSerie(idx)"
                                                maxlength="17"
                                                placeholder="ABC12345678901234"
                                                class="w-full border rounded-lg px-3 py-2 text-xs tracking-widest font-mono focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700"
                                                :class="{
                                                    'border-emerald-300 focus:ring-emerald-300 dark:border-emerald-700': fila.num_serie.length === 17 && !fila.error,
                                                    'border-red-300 focus:ring-red-300 dark:border-red-700': fila.error,
                                                    'border-amber-300 focus:ring-amber-300 dark:border-amber-700': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                                    'border-gray-200 dark:border-gray-700 focus:ring-gray-200 dark:focus:ring-gray-600': fila.num_serie.length === 0 && !fila.error
                                                }">
                                            <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300"
                                                     :style="`width:${(fila.num_serie.length/17)*100}%`"
                                                     :class="{
                                                        'bg-emerald-400': fila.num_serie.length===17 && !fila.error,
                                                        'bg-red-400': fila.error,
                                                        'bg-amber-400': fila.num_serie.length>0 && fila.num_serie.length<17 && !fila.error
                                                     }"></div>
                                            </div>
                                            <div x-show="fila.error"
                                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 class="flex items-center gap-1 text-[11px] text-red-500">
                                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <span x-text="fila.error"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <div class="space-y-2">
                                            <div class="flex flex-wrap gap-1">
                                                <span class="inline-flex items-center text-[11px] bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/50 px-2 py-0.5 rounded-md font-medium" x-text="nombreMarca(fila)"></span>
                                                <span class="inline-flex items-center text-[11px] bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-700 px-2 py-0.5 rounded-md" x-text="nombreModelo(fila)"></span>
                                                <span class="inline-flex items-center text-[11px] bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-500 border border-gray-100 dark:border-gray-700 px-2 py-0.5 rounded-md" x-text="nombreColor(fila)"></span>
                                                <span class="inline-flex items-center text-[11px] bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-500 border border-gray-100 dark:border-gray-700 px-2 py-0.5 rounded-md" x-text="nombreVoltaje(fila)"></span>
                                            </div>
                                            <button type="button" @click="fila.expanded = !fila.expanded"
                                                class="text-[11px] text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 font-medium flex items-center gap-0.5 transition duration-150">
                                                <svg class="w-3 h-3 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                <span x-text="fila.expanded ? 'Cerrar' : 'Config individual'"></span>
                                            </button>
                                            <div x-show="fila.expanded"
                                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                                                 class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                                                <p class="text-[10px] font-bold text-gray-300 dark:text-gray-600 uppercase tracking-widest">Override individual</p>
                                                <select x-model="fila.id_marca" @change="onFilaMarca(idx)"
                                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition">
                                                    <option value="">Marca</option>
                                                    <template x-for="m in catalogo" :key="m.id_marca">
                                                        <option :value="String(m.id_marca)" x-text="m.nombre_marca"></option>
                                                    </template>
                                                </select>
                                                <select x-model="fila.id_modelo" @change="onFilaModelo(idx)" :disabled="!filaModelos(idx).length"
                                                    class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                    <option value="">Modelo</option>
                                                    <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                                        <option :value="String(mo.id_modelo)" x-text="mo.nombre_modelo"></option>
                                                    </template>
                                                </select>
                                                <div class="grid grid-cols-2 gap-1.5">
                                                    <select x-model="fila.id_color" :disabled="!filaColores(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                        <option value="">Color</option>
                                                        <template x-for="c in filaColores(idx)" :key="c.id_color">
                                                            <option :value="String(c.id_color)" x-text="c.color"></option>
                                                        </template>
                                                    </select>
                                                    <select x-model="fila.id_voltaje" :disabled="!filaVoltajes(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                        <option value="">Voltaje</option>
                                                        <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                                            <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 text-right w-10 pt-4">
                                        <button type="button" @click="quitarFila(idx)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition duration-150 opacity-0 group-hover:opacity-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== MÓVIL ===== --}}
            <div x-show="filas.length > 0" class="block md:hidden max-h-96 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800">
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
                                    @input="fila.num_serie = $event.target.value.toUpperCase(); validarSerie(idx)"
                                    maxlength="17" placeholder="ABC12345678901234"
                                    class="w-full border rounded-lg px-3 py-2 text-xs tracking-widest font-mono focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-700"
                                    :class="{
                                        'border-emerald-300 focus:ring-emerald-300 dark:border-emerald-700': fila.num_serie.length === 17 && !fila.error,
                                        'border-red-300 focus:ring-red-300 dark:border-red-700': fila.error,
                                        'border-amber-300 focus:ring-amber-300 dark:border-amber-700': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                        'border-gray-200 dark:border-gray-700 focus:ring-gray-200': fila.num_serie.length === 0 && !fila.error
                                    }">
                                <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
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
                                <div class="flex flex-wrap gap-1 pt-0.5">
                                    <span class="text-[10px] bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/50 px-1.5 py-0.5 rounded font-medium" x-text="nombreMarca(fila)"></span>
                                    <span class="text-[10px] bg-gray-50 dark:bg-gray-800 text-gray-500 border border-gray-100 dark:border-gray-700 px-1.5 py-0.5 rounded" x-text="nombreModelo(fila)"></span>
                                    <span class="text-[10px] bg-gray-50 dark:bg-gray-800 text-gray-400 border border-gray-100 dark:border-gray-700 px-1.5 py-0.5 rounded" x-text="nombreColor(fila)"></span>
                                    <span class="text-[10px] bg-gray-50 dark:bg-gray-800 text-gray-400 border border-gray-100 dark:border-gray-700 px-1.5 py-0.5 rounded" x-text="nombreVoltaje(fila)"></span>
                                </div>
                                <button type="button" @click="fila.expanded = !fila.expanded"
                                    class="text-[11px] text-gray-400 hover:text-blue-500 font-medium flex items-center gap-0.5 transition">
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span x-text="fila.expanded ? 'Cerrar' : 'Config individual'"></span>
                                </button>
                                <div x-show="fila.expanded"
                                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                                     class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                                    <select x-model="fila.id_marca" @change="onFilaMarca(idx)"
                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition">
                                        <option value="">Marca</option>
                                        <template x-for="m in catalogo" :key="m.id_marca">
                                            <option :value="String(m.id_marca)" x-text="m.nombre_marca"></option>
                                        </template>
                                    </select>
                                    <select x-model="fila.id_modelo" @change="onFilaModelo(idx)" :disabled="!filaModelos(idx).length"
                                        class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                        <option value="">Modelo</option>
                                        <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                            <option :value="String(mo.id_modelo)" x-text="mo.nombre_modelo"></option>
                                        </template>
                                    </select>
                                    <div class="grid grid-cols-2 gap-1.5">
                                        <select x-model="fila.id_color" :disabled="!filaColores(idx).length"
                                            class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                            <option value="">Color</option>
                                            <template x-for="c in filaColores(idx)" :key="c.id_color">
                                                <option :value="String(c.id_color)" x-text="c.color"></option>
                                            </template>
                                        </select>
                                        <select x-model="fila.id_voltaje" :disabled="!filaVoltajes(idx).length"
                                            class="w-full border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                            <option value="">Voltaje</option>
                                            <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                                <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                                            </template>
                                        </select>
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

            {{-- Footer progreso --}}
            <div x-show="filas.length > 0" class="px-5 py-2.5 border-t border-gray-50 dark:border-gray-800 flex items-center gap-3">
                <span class="text-[11px] text-gray-400 whitespace-nowrap tabular-nums">
                    <span class="text-emerald-500 font-semibold" x-text="filas.filter(f => f.num_serie.length === 17 && !f.error).length"></span>/<span x-text="filas.length"></span> válidas
                </span>
                <div class="flex-1 h-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-400 transition-all duration-500"
                         :style="`width:${filas.length > 0 ? (filas.filter(f => f.num_serie.length === 17 && !f.error).length / filas.length) * 100 : 0}%`"></div>
                </div>
            </div>
        </div>

        {{-- ===== ERRORES LARAVEL ===== --}}
        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/50 rounded-2xl p-4">
            <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-2">Errores al guardar</p>
            <ul class="text-xs text-red-500 dark:text-red-400 space-y-1 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- ===== PASO 3 ===== --}}
        <div class="space-y-3">
            <div x-show="puedeGuardar"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl px-4 py-3">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-emerald-700 dark:text-emerald-400">
                    <strong x-text="filas.length + ' bicicleta' + (filas.length !== 1 ? 's' : '')"></strong> listas para guardar.
                </p>
            </div>
            <button type="submit" :disabled="submitting || !puedeGuardar"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3.5 rounded-2xl text-sm font-semibold hover:opacity-90 active:scale-[.99] transition-all duration-150 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <template x-if="!submitting">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="`Guardar ${filas.length} bicicleta${filas.length !== 1 ? 's' : ''}`"></span>
                    </span>
                </template>
                <template x-if="submitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Guardando…
                    </span>
                </template>
            </button>
        </div>

    </form>
</div>

<script>
function cargaMasiva() {
    return {
        catalogo:            @json(json_decode($catalogoJson)),
        plantilla:           { id_marca: '', id_modelo: '', id_color: '', id_voltaje: '' },
        filas:               [],
        submitting:          false,
        flashVisible:        false,
        flashMsg:            '',
        flashTipo:           'success',
        _uid:                0,
        mostrarModalSinMarcas: false,
        filtroModelo:        '',

        // ─── GETTERS ───────────────────────────────────
        get paso() {
            if (!this.plantillaCompleta) return 1;
            if (this.filas.length === 0)  return 2;
            return 3;
        },
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
        // Catálogo agrupado por marca, filtrado por texto
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

        // ─── PICKER ────────────────────────────────────
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

        // ─── HELPERS NOMBRE ────────────────────────────
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
            return c ? c.color : '—';
        },
        nombreVoltaje(fila) {
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return '—';
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            if (!mo) return '—';
            const v = mo.voltajes.find(v => String(v.id_voltaje) === fila.id_voltaje);
            return v ? v.voltaje : '—';
        },

        // ─── PLANTILLA ─────────────────────────────────
        onMarca()  { this.plantilla.id_modelo = ''; this.plantilla.id_color = ''; this.plantilla.id_voltaje = ''; },
        onModelo() { this.plantilla.id_color  = ''; this.plantilla.id_voltaje = ''; },

        // ─── INIT ──────────────────────────────────────
        init() {
            if (this.catalogo.length === 0) this.mostrarModalSinMarcas = true;
        },

        // ─── FILAS ─────────────────────────────────────
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

        // ─── VALIDACIÓN ────────────────────────────────
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

        // ─── FLASH + ENVÍO ─────────────────────────────
        mostrarFlash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            setTimeout(() => this.flashVisible = false, 3500);
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