<x-app-layout>
<div 
     x-data="cargaMasiva()"
     x-init="init()"
     class="max-w-5xl mx-auto space-y-6"
     >

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('bicicletas.index') }}"
               class="w-9 h-9 rounded-full flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:scale-105 transform transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Registrar Bicicletas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Agrega varias bicicletas a la vez con su número de serie</p>
            </div>
        </div>
    </div>

    {{-- ===== MODAL OBLIGATORIO: SIN MARCAS ===== --}}
    <div x-show="mostrarModalSinMarcas" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 px-4"
        @click.self="/* no se cierra */">
        <div x-show="mostrarModalSinMarcas"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">¡No tan rápido!</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Primero debes crear marcas</p>
                </div>
            </div>
            <div class="space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Para registrar bicicletas, necesitas tener al menos una <strong>marca</strong> en tu catálogo.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Dirígete a la sección de catálogo y crea tu primera marca, luego podrás volver aquí.
                </p>
            </div>
            <div class="mt-5 flex justify-end">
                <a href="{{ route('admin.catalogo.index') }}"
                    class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition hover:scale-105 transform duration-200 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Ir a catálogo
                </a>
            </div>
        </div>
    </div>

    {{-- ===== FLASH ALPINE ===== --}}
    <div x-show="flashVisible" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
        <div class="flex items-center gap-3 rounded-xl bg-white dark:bg-gray-800 px-5 py-4 shadow-xl min-w-[300px]"
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

    {{-- ===== PASOS VISUALES ===== --}}
    <div class="flex items-center gap-1 px-1">
        <div class="flex flex-col items-center gap-1.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300 shrink-0"
                 :class="paso > 1 ? 'bg-emerald-500 text-white' : paso === 1 ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border border-gray-200 dark:border-gray-600'">
                <template x-if="paso > 1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></template>
                <template x-if="paso <= 1"><span>1</span></template>
            </div>
            <span class="text-[10px] text-gray-400 whitespace-nowrap">Configura</span>
        </div>
        <div class="flex-1 h-px mx-2 mb-5 transition-colors duration-500" :class="paso > 1 ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
        <div class="flex flex-col items-center gap-1.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300 shrink-0"
                 :class="paso > 2 ? 'bg-emerald-500 text-white' : paso === 2 ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border border-gray-200 dark:border-gray-600'">
                <template x-if="paso > 2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></template>
                <template x-if="paso <= 2"><span>2</span></template>
            </div>
            <span class="text-[10px] text-gray-400 whitespace-nowrap">N° Series</span>
        </div>
        <div class="flex-1 h-px mx-2 mb-5 transition-colors duration-500" :class="paso > 2 ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
        <div class="flex flex-col items-center gap-1.5">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300 shrink-0"
                 :class="paso === 3 ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border border-gray-200 dark:border-gray-600'">
                <span>3</span>
            </div>
            <span class="text-[10px] text-gray-400 whitespace-nowrap">Guardar</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.bicicletas.storeMasivo') }}"
          @submit.prevent="enviar($el)" class="space-y-8">
        @csrf

        {{-- ===== PASO 1 ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden transition-all duration-300 hover:shadow-md"
             :class="plantillaCompleta ? 'ring-1 ring-emerald-400 dark:ring-emerald-600' : ''">
            <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 text-[10px] font-bold shrink-0">1</div>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Configuración base</span>
                    <p class="hidden sm:block text-xs text-gray-400">— se aplicará a todas las bicicletas</p>
                </div>
                <div x-show="plantillaCompleta" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                     class="flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-medium px-3 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-800 whitespace-nowrap">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    Listo
                </div>
            </div>
            <div class="px-6 py-5 space-y-5">
                <p class="text-xs text-gray-400 -mt-1">Elige marca, modelo, color y voltaje. Puedes cambiar la configuración individual por bici en el paso 2.</p>
                <div class="space-y-1.5">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Marca</label>
                    <select x-model="plantilla.id_marca" @change="onMarca()"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition">
                        <option value="">— Selecciona marca —</option>
                        <template x-for="m in catalogo" :key="m.id_marca">
                            <option :value="String(m.id_marca)" x-text="m.nombre_marca"></option>
                        </template>
                    </select>
                </div>
                <div class="space-y-1.5" x-show="plantilla.id_marca"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Modelo</label>
                    <select x-model="plantilla.id_modelo" @change="onModelo()" :disabled="!modelosDisponibles.length"
                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="">— Selecciona modelo —</option>
                        <template x-for="mo in modelosDisponibles" :key="mo.id_modelo">
                            <option :value="String(mo.id_modelo)" x-text="mo.nombre_modelo"></option>
                        </template>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4" x-show="plantilla.id_modelo"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Color</label>
                        <select x-model="plantilla.id_color" :disabled="!coloresDisponibles.length"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Color</option>
                            <template x-for="c in coloresDisponibles" :key="c.id_color">
                                <option :value="String(c.id_color)" x-text="c.color"></option>
                            </template>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Voltaje</label>
                        <select x-model="plantilla.id_voltaje" :disabled="!voltajesDisponibles.length"
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Voltaje</option>
                            <template x-for="v in voltajesDisponibles" :key="v.id_voltaje">
                                <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== PASO 2 ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden transition-all duration-300 hover:shadow-md">
            <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900 text-[10px] font-bold shrink-0">2</div>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">Números de serie</span>
                    <span x-show="filas.length > 0" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
                          class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-bold px-2 py-0.5 rounded-full" x-text="filas.length"></span>
                </div>
                <button type="button" @click="agregarFila()" :disabled="!plantillaCompleta"
                    class="text-xs font-semibold bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-3.5 py-1.5 rounded-lg hover:opacity-90 active:scale-95 transition-all disabled:opacity-30 disabled:cursor-not-allowed flex items-center gap-1.5 hover:scale-105 transform duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Agregar
                </button>
            </div>

            {{-- Estado vacío --}}
            <div x-show="filas.length === 0" class="px-6 py-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        <template x-if="!plantillaCompleta"><span>Primero completa la configuración base</span></template>
                        <template x-if="plantillaCompleta"><span>Pulsa <strong>Agregar</strong> para ingresar series</span></template>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Cada bicicleta necesita un número de serie único de 17 caracteres</p>
                </div>
            </div>

            {{-- 
                ===== VISTA ESCRITORIO =====
                FIX CLAVE: La config expandida va DENTRO de la misma celda (td) de configuración,
                NO en un <tr> separado. Así Alpine x-for genera solo un <tr> por bici
                y el navegador no rompe el DOM de la tabla.
            --}}
            <div x-show="filas.length > 0" class="hidden md:block">
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 sticky top-0 z-10">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase w-10">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Número de serie</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Configuración</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(fila, idx) in filas" :key="fila._uid">
                                <tr class="align-top hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">

                                    {{-- Columna # --}}
                                    <td class="px-4 py-4 text-xs text-gray-400 w-10" x-text="idx + 1"></td>

                                    {{-- Columna serie + inputs ocultos --}}
                                    <td class="px-4 py-4">
                                        <input type="hidden" :name="`bicicletas[${idx}][num_serie]`"  :value="fila.num_serie">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="fila.id_modelo">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="fila.id_color">
                                        <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="fila.id_voltaje">

                                        <div class="space-y-1.5">
                                            <input type="text"
                                                x-model="fila.num_serie"
                                                @input="fila.num_serie = $event.target.value.toUpperCase(); validarSerie(idx)"
                                                maxlength="17"
                                                placeholder="Ej: ABC12345678901234"
                                                class="w-full border rounded-lg px-3.5 py-2.5 text-sm tracking-wider focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-700 dark:text-white"
                                                :class="{
                                                    'border-emerald-400 focus:ring-emerald-400 dark:border-emerald-600': fila.num_serie.length === 17 && !fila.error,
                                                    'border-red-400 focus:ring-red-400 dark:border-red-600': fila.error,
                                                    'border-amber-400 focus:ring-amber-400 dark:border-amber-600': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                                    'border-gray-200 dark:border-gray-600 focus:ring-gray-400': fila.num_serie.length === 0 && !fila.error
                                                }">
                                            <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-200"
                                                     :style="`width: ${(fila.num_serie.length / 17) * 100}%`"
                                                     :class="{
                                                         'bg-emerald-500': fila.num_serie.length === 17 && !fila.error,
                                                         'bg-red-400': fila.error,
                                                         'bg-amber-400': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error
                                                     }"></div>
                                            </div>
                                            <div x-show="fila.error"
                                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="flex items-center gap-1.5 text-xs text-red-500">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                                </svg>
                                                <span x-text="fila.error"></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Columna configuración: badges + toggle + form expandido --}}
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col gap-2">
                                            {{-- Badges --}}
                                            <div class="flex flex-wrap gap-1">
                                                <span class="text-[11px] bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 px-2 py-0.5 rounded-md font-medium" x-text="nombreMarca(fila)"></span>
                                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreModelo(fila)"></span>
                                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreColor(fila)"></span>
                                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreVoltaje(fila)"></span>
                                            </div>
                                            {{-- Botón toggle --}}
                                            <button type="button" @click="fila.expanded = !fila.expanded"
                                                class="text-[11px] text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 font-medium flex items-center gap-0.5 w-fit transition hover:scale-105 transform duration-200">
                                                <svg class="w-3 h-3 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                <span x-text="fila.expanded ? 'Cerrar' : 'Cambiar config'"></span>
                                            </button>
                                            {{-- Form expandido DENTRO de la misma celda --}}
                                            <div x-show="fila.expanded"
                                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 -translate-y-1"
                                                 class="mt-1 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-2">
                                                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Config individual para esta bici</p>
                                                <select x-model="fila.id_marca" @change="onFilaMarca(idx)"
                                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition">
                                                    <option value="">Selecciona marca</option>
                                                    <template x-for="m in catalogo" :key="m.id_marca">
                                                        <option :value="String(m.id_marca)" x-text="m.nombre_marca"></option>
                                                    </template>
                                                </select>
                                                <select x-model="fila.id_modelo" @change="onFilaModelo(idx)" :disabled="!filaModelos(idx).length"
                                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                    <option value="">Selecciona modelo</option>
                                                    <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                                        <option :value="String(mo.id_modelo)" x-text="mo.nombre_modelo"></option>
                                                    </template>
                                                </select>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <select x-model="fila.id_color" :disabled="!filaColores(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                        <option value="">Color</option>
                                                        <template x-for="c in filaColores(idx)" :key="c.id_color">
                                                            <option :value="String(c.id_color)" x-text="c.color"></option>
                                                        </template>
                                                    </select>
                                                    <select x-model="fila.id_voltaje" :disabled="!filaVoltajes(idx).length"
                                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                                        <option value="">Voltaje</option>
                                                        <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                                            <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Eliminar --}}
                                    <td class="px-4 py-4 text-right w-10">
                                        <button type="button" @click="quitarFila(idx)"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-200 dark:hover:border-red-800 transition hover:scale-105 transform duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            {{-- ===== VISTA MÓVIL ===== --}}
            <div x-show="filas.length > 0" class="block md:hidden max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/50">
                <template x-for="(fila, idx) in filas" :key="fila._uid">
                    <div>
                        <input type="hidden" :name="`bicicletas[${idx}][num_serie]`"  :value="fila.num_serie">
                        <input type="hidden" :name="`bicicletas[${idx}][id_modelo]`"  :value="fila.id_modelo">
                        <input type="hidden" :name="`bicicletas[${idx}][id_color]`"   :value="fila.id_color">
                        <input type="hidden" :name="`bicicletas[${idx}][id_voltaje]`" :value="fila.id_voltaje">

                        <div class="grid grid-cols-[36px_1fr_auto] items-start px-4 py-4 gap-2">
                            <div class="flex items-center justify-center pt-2.5">
                                <span class="text-xs text-gray-400" x-text="idx + 1"></span>
                            </div>
                            <div class="space-y-1.5">
                                <input type="text"
                                    x-model="fila.num_serie"
                                    @input="fila.num_serie = $event.target.value.toUpperCase(); validarSerie(idx)"
                                    maxlength="17" placeholder="Ej: ABC12345678901234"
                                    class="w-full border rounded-lg px-3.5 py-2.5 text-sm tracking-wider focus:outline-none focus:ring-1 transition bg-white dark:bg-gray-700 dark:text-white"
                                    :class="{
                                        'border-emerald-400 focus:ring-emerald-400 dark:border-emerald-600': fila.num_serie.length === 17 && !fila.error,
                                        'border-red-400 focus:ring-red-400 dark:border-red-600': fila.error,
                                        'border-amber-400 focus:ring-amber-400 dark:border-amber-600': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error,
                                        'border-gray-200 dark:border-gray-600 focus:ring-gray-400': fila.num_serie.length === 0 && !fila.error
                                    }">
                                <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-200"
                                         :style="`width: ${(fila.num_serie.length / 17) * 100}%`"
                                         :class="{
                                             'bg-emerald-500': fila.num_serie.length === 17 && !fila.error,
                                             'bg-red-400': fila.error,
                                             'bg-amber-400': fila.num_serie.length > 0 && fila.num_serie.length < 17 && !fila.error
                                         }"></div>
                                </div>
                                <div x-show="fila.error" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                     class="flex items-center gap-1.5 text-xs text-red-500">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    <span x-text="fila.error"></span>
                                </div>
                            </div>
                            <div class="flex items-start pt-2 justify-end">
                                <button type="button" @click="quitarFila(idx)"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-200 dark:hover:border-red-800 transition hover:scale-105 transform duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between ml-[36px] pr-4 pb-3 flex-wrap gap-2">
                            <div class="flex flex-wrap gap-1">
                                <span class="text-[11px] bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 px-2 py-0.5 rounded-md font-medium" x-text="nombreMarca(fila)"></span>
                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreModelo(fila)"></span>
                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreColor(fila)"></span>
                                <span class="text-[11px] bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md" x-text="nombreVoltaje(fila)"></span>
                            </div>
                            <button type="button" @click="fila.expanded = !fila.expanded"
                                class="text-[11px] text-blue-500 hover:text-blue-700 font-medium flex items-center gap-0.5 transition">
                                <svg class="w-3 h-3 transition-transform duration-200" :class="fila.expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <span x-text="fila.expanded ? 'Cerrar' : 'Cambiar config'"></span>
                            </button>
                        </div>

                        <div x-show="fila.expanded"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 -translate-y-1"
                             class="ml-[36px] mr-4 pb-5 pt-3 border-t border-gray-100 dark:border-gray-700/60 space-y-2.5">
                            <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Config individual para esta bici</p>
                            <select x-model="fila.id_marca" @change="onFilaMarca(idx)"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition">
                                <option value="">Selecciona marca</option>
                                <template x-for="m in catalogo" :key="m.id_marca">
                                    <option :value="String(m.id_marca)" x-text="m.nombre_marca"></option>
                                </template>
                            </select>
                            <select x-model="fila.id_modelo" @change="onFilaModelo(idx)" :disabled="!filaModelos(idx).length"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <option value="">Selecciona modelo</option>
                                <template x-for="mo in filaModelos(idx)" :key="mo.id_modelo">
                                    <option :value="String(mo.id_modelo)" x-text="mo.nombre_modelo"></option>
                                </template>
                            </select>
                            <div class="grid grid-cols-2 gap-2.5">
                                <select x-model="fila.id_color" :disabled="!filaColores(idx).length"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <option value="">Color</option>
                                    <template x-for="c in filaColores(idx)" :key="c.id_color">
                                        <option :value="String(c.id_color)" x-text="c.color"></option>
                                    </template>
                                </select>
                                <select x-model="fila.id_voltaje" :disabled="!filaVoltajes(idx).length"
                                    class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-xs bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <option value="">Voltaje</option>
                                    <template x-for="v in filaVoltajes(idx)" :key="v.id_voltaje">
                                        <option :value="String(v.id_voltaje)" x-text="v.voltaje"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer progreso --}}
            <div x-show="filas.length > 0" class="px-6 py-2.5 border-t dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 flex items-center gap-3">
                <span class="text-[11px] text-gray-400 whitespace-nowrap">
                    <span class="text-emerald-500 font-medium" x-text="filas.filter(f => f.num_serie.length === 17 && !f.error).length"></span>
                    / <span x-text="filas.length"></span> válidas
                </span>
                <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                         :style="`width: ${filas.length > 0 ? (filas.filter(f => f.num_serie.length === 17 && !f.error).length / filas.length) * 100 : 0}%`"></div>
                </div>
            </div>
        </div>

        {{-- ===== ERRORES LARAVEL ===== --}}
        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <p class="text-xs font-semibold text-red-600 dark:text-red-400">Errores al guardar</p>
            </div>
            <ul class="text-xs text-red-600 dark:text-red-400 space-y-1.5 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- ===== PASO 3: GUARDAR ===== --}}
        <div class="space-y-4">
            <div x-show="puedeGuardar" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3.5">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-emerald-700 dark:text-emerald-400">
                    Listo para guardar <strong x-text="filas.length + ' bicicleta' + (filas.length !== 1 ? 's' : '')"></strong> — todas con serie válida y configuración completa.
                </p>
            </div>
            <button type="submit" :disabled="submitting || !puedeGuardar"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3.5 rounded-xl text-sm font-semibold hover:opacity-90 active:scale-[.99] transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 hover:scale-[1.01] transform duration-200">
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
                        Guardando...
                    </span>
                </template>
            </button>
        </div>

    </form>
</div>

<script>
function cargaMasiva() {
    return {
        catalogo:     @json(json_decode($catalogoJson)),
        plantilla:    { id_marca: '', id_modelo: '', id_color: '', id_voltaje: '' },
        filas:        [],
        submitting:   false,
        flashVisible: false,
        flashMsg:     '',
        flashTipo:    'success',
        _uid:         0,
        mostrarModalSinMarcas: false,

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

        onMarca()  { this.plantilla.id_modelo = ''; this.plantilla.id_color = ''; this.plantilla.id_voltaje = ''; },
        onModelo() { this.plantilla.id_color = ''; this.plantilla.id_voltaje = ''; },

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
            const fila = this.filas[idx];
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            return m ? m.modelos : [];
        },
        filaColores(idx) {
            const fila = this.filas[idx];
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return [];
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            return mo ? mo.colores : [];
        },
        filaVoltajes(idx) {
            const fila = this.filas[idx];
            const m = this.catalogo.find(m => String(m.id_marca) === fila.id_marca);
            if (!m) return [];
            const mo = m.modelos.find(mo => String(mo.id_modelo) === fila.id_modelo);
            return mo ? mo.voltajes : [];
        },

        onFilaMarca(idx) { this.filas[idx].id_modelo = ''; this.filas[idx].id_color = ''; this.filas[idx].id_voltaje = ''; },
        onFilaModelo(idx) { this.filas[idx].id_color = ''; this.filas[idx].id_voltaje = ''; },

        validarSerie(idx) {
            const val = this.filas[idx].num_serie.trim();
            if (val.length > 0 && val.length < 17) {
                const falt = 17 - val.length;
                this.filas[idx].error = `Faltan ${falt} carácter${falt !== 1 ? 'es' : ''}`;
            } else if (val.length === 17) {
                const dup = this.filas.some((f, i) => i !== idx && f.num_serie.trim() === val);
                this.filas[idx].error = dup ? 'N° de serie repetido en este listado' : '';
            } else {
                this.filas[idx].error = '';
            }
            this.revalidarTodas(idx);
        },
        revalidarTodas(exceptIdx = -1) {
            this.filas.forEach((fila, idx) => {
                if (idx === exceptIdx || fila.num_serie.length !== 17) return;
                const dup = this.filas.some((f, i) => i !== idx && f.num_serie.trim() === fila.num_serie.trim());
                fila.error = dup ? 'N° de serie repetido en este listado' : '';
            });
        },

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