<x-app-layout>
    <div
        x-data="pedidoRapido({{ Js::from($modelos->map(fn($m) => ['id' => $m->id_modelo, 'nombre' => $m->nombre_modelo])) }})"
        class="max-w-7xl mx-auto"
        :class="isMobile ? 'px-4 py-5 pb-28' : 'px-6 py-8'">

        {{-- ===========================
             HEADER
             =========================== --}}
        <div class="animate-fade-up mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">Emisión Rápida</h1>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Genera formulario de emisión sin registro en sistema
                        </p>
                    </div>
                </div>
                
                {{-- Contador --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300" 
                          x-text="items.reduce((s, i) => s + i.series.length, 0)"></span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">unidades</span>
                </div>
            </div>
            <hr class="border-t border-gray-200 dark:border-gray-700 mt-5">
        </div>

        <form id="formRapido" method="POST" action="{{ route('pedidos.rapido.pdf') }}" target="_blank">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                
                {{-- ===========================
                     COLUMNA PRINCIPAL
                     =========================== --}}
                <div class="space-y-6">
                    
                    {{-- Datos generales --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5 animate-fade-up delay-1">
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-5">
                            Información General
                        </span>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Fecha</label>
                                <input type="text" name="fecha" x-model="fechaHoy"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800/50  focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" readonly>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Cliente</label>
                                <input type="text" name="cliente" placeholder="Nombre"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Distancia</label>
                                <input type="text" name="distancia" placeholder="—"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Transporte</label>
                                <input type="text" name="transporte" placeholder="—"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Costo</label>
                                <input type="text" name="costo_envio" placeholder="$"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Nombre deChofer</label>
                                <input type="text" name="Nchofer" placeholder="Fredy"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs text-gray-500 dark:text-gray-400">Telefono Chofer</label>
                                <input type="text" name="Tchofer" placeholder="987654321"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30" autocomplete="off">                            </div>
                            
                        </div>
                    </div>

                    {{-- Scanner Desktop --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5 animate-fade-up delay-2 hidden lg:block">
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-5">
                            Registrar Bicicleta
                        </span>

                        <div class="space-y-4">
                            {{-- Número de serie --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Número de serie <span class="text-gray-400 ml-2">(17 caracteres)</span>
                                </label>
                                <div class="relative">
                                    <input
                                        x-ref="serieInput"
                                        type="text"
                                        x-model="numSerie"
                                        @input="onSerieInput()"
                                        @keydown.enter.prevent="agregarItem()"
                                        maxlength="17"
                                        placeholder="Ingresa el código de 17 caracteres"
                                        autocomplete="off"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-4 pr-20 py-3  tracking-wide text-base focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                        <span class="text-xs  text-gray-500 dark:text-gray-400"
                                              x-text="numSerie.length + '/17'"></span>
                                        <button type="button" @click="limpiarSerie()"
                                            x-show="numSerie.length > 0"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- Feedback --}}
                                <div x-show="modeloDetectado || errorSerie" x-cloak class="mt-2">
                                    <template x-if="modeloDetectado">
                                        <div class="text-xs text-gray-600 dark:text-gray-300">
                                            Modelo: <span class="font-medium text-gray-800 dark:text-gray-200" x-text="modeloDetectado"></span>
                                        </div>
                                    </template>
                                    <template x-if="errorSerie">
                                        <div class="text-xs text-red-600 dark:text-red-400" x-text="errorSerie"></div>
                                    </template>
                                </div>
                            </div>

                            {{-- Lote --}}
                            <div class="max-w-md">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Lote de batería <span class="text-gray-400">(opcional)</span>
                                </label>
                                <input type="text" x-model="lote" placeholder="Ej: L-2024-09"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                            </div>

                            {{-- Voltaje y Color --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Voltaje</label>
                                    <select x-model="form.id_voltaje" :disabled="!form.voltajes.length"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">— Seleccionar —</option>
                                        <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Color</label>
                                    <select x-model="form.id_color" :disabled="!form.colores.length"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">— Seleccionar —</option>
                                        <template x-for="c in form.colores" :key="c.id_color">
                                            <option :value="c.id_color" x-text="c.color"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <button type="button" @click="agregarItem()"
                                        :disabled="numSerie.length !== 17 || !form.id_modelo || !form.id_voltaje || !form.id_color"
                                        class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 h-[42px]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Agregar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla con scroll horizontal --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden animate-fade-up delay-3">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Artículos Registrados
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600" 
                                  x-text="items.reduce((s, i) => s + i.series.length, 0) + ' unidades'"></span>
                        </div>

                        <div x-show="items.length === 0" x-cloak class="py-12 text-center">
                            <div class="w-14 h-14 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No hay artículos registrados</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Escanea una bicicleta para comenzar</p>
                        </div>

                        <div x-show="items.length > 0" x-cloak class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Voltaje</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cant.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Números de Serie</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lote</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <input type="hidden" :name="`items[${index}][id_modelo]`" :value="item.id_modelo">
                                            <input type="hidden" :name="`items[${index}][id_voltaje]`" :value="item.id_voltaje">
                                            <input type="hidden" :name="`items[${index}][id_color]`" :value="item.id_color">
                                            <input type="hidden" :name="`items[${index}][cantidad]`" :value="item.series.length">
                                            <input type="hidden" :name="`items[${index}][lote]`" :value="item.lote">
                                            <template x-for="(serie, si) in item.series">
                                                <input type="hidden" :name="`items[${index}][series][${si}]`" :value="serie">
                                            </template>

                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-sm" x-text="item.modelo_nombre"></td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-sm" x-text="item.voltaje_nombre"></td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-sm" x-text="item.color_nombre"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
                                                      x-text="item.series.length"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1 max-w-xs">
                                                    <template x-for="(serie, si) in item.series.slice(0, 3)" :key="si">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs  text-gray-700 dark:text-gray-300">
                                                            <span x-text="serie"></span>
                                                            <button type="button" @click="quitarSerie(index, si)"
                                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    </template>
                                                    <span x-show="item.series.length > 3"
                                                          class="text-xs text-gray-400"
                                                          x-text="'+' + (item.series.length - 3)"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" x-model="item.lote" placeholder="—"
                                                       class="w-28 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 dark:text-white  focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="quitarItem(index)"
                                                    class="text-xs text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 font-medium transition">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Indicador de scroll en mobile --}}
                        <div x-show="items.length > 0" class="lg:hidden text-xs text-gray-400 text-center py-2 border-t border-gray-200 dark:border-gray-700">
                            <span>← Desliza para ver más columnas →</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ===========================
             MOBILE: Bottom Sheet Scanner
             =========================== --}}
        <div x-show="showScannerMobile" x-cloak
             class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-2xl shadow-2xl z-50 transform transition-transform duration-300 ease-out lg:hidden"
             :class="showScannerMobile ? 'translate-y-0' : 'translate-y-full'"
             @click.self="showScannerMobile = false">
            <div class="w-12 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto my-3"></div>
            
            <div class="px-5 pb-6 pt-2">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Registrar Bicicleta</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ingresa el número de serie</p>
                    </div>
                    <button type="button" @click="showScannerMobile = false"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Número de serie --}}
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">
                            Número de serie <span class="text-gray-400">(17 caracteres)</span>
                        </label>
                        <div class="relative">
                            <input
                                x-ref="serieInputMobile"
                                type="text"
                                x-model="numSerie"
                                @input="onSerieInput()"
                                @keydown.enter.prevent="agregarItem()"
                                maxlength="17"
                                placeholder="Ingresa el código"
                                autocomplete="off"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-4 pr-16 py-4  tracking-wide text-base focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs  text-gray-500 dark:text-gray-400"
                                      x-text="numSerie.length + '/17'"></span>
                            </div>
                        </div>
                        
                        <div x-show="modeloDetectado || errorSerie" x-cloak class="mt-2">
                            <template x-if="modeloDetectado">
                                <div class="text-xs text-gray-600 dark:text-gray-300">
                                    Modelo: <span class="font-medium" x-text="modeloDetectado"></span>
                                </div>
                            </template>
                            <template x-if="errorSerie">
                                <div class="text-xs text-red-600 dark:text-red-400" x-text="errorSerie"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Lote --}}
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">
                            Lote <span class="text-gray-400">(opcional)</span>
                        </label>
                        <input type="text" x-model="lote" placeholder="Ej: L-2024-09"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    </div>

                    {{-- Voltaje y Color --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Voltaje</label>
                            <select x-model="form.id_voltaje" :disabled="!form.voltajes.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">—</option>
                                <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                    <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Color</label>
                            <select x-model="form.id_color" :disabled="!form.colores.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">—</option>
                                <template x-for="c in form.colores" :key="c.id_color">
                                    <option :value="c.id_color" x-text="c.color"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Botón Agregar --}}
                    <button type="button" @click="agregarItem()"
                        :disabled="numSerie.length !== 17 || !form.id_modelo || !form.id_voltaje || !form.id_color"
                        class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-4 rounded-lg text-base font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Agregar Bicicleta</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ===========================
             FAB Mobile
             =========================== --}}
        <button type="button" @click="showScannerMobile = true"
            class="fixed bottom-24 right-4 lg:hidden w-14 h-14 rounded-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white flex items-center justify-center shadow-lg hover:scale-110 transition-transform z-40"
            title="Registrar bicicleta">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        {{-- ===========================
             Botón Generar PDF
             =========================== --}}
        <div class="fixed bottom-0 left-0 right-0 p-3 lg:static lg:p-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 lg:bg-transparent lg:border-0 lg:mt-6 lg:flex lg:justify-end animate-fade-up"
             style="z-index: 30;">
            <button type="button" @click="generarPdf()"
                :disabled="items.length === 0"
                class="w-full lg:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-lg text-base font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2.5 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-text="items.length === 0 ? 'Sin artículos' : 'Generar PDF (' + items.reduce((s, i) => s + i.series.length, 0) + ' un.)'"></span>
            </button>
        </div>

        <style>
            [x-cloak] { display: none !important; }
            
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animate-fade-up {
                animation: fadeInUp 0.3s ease forwards;
            }
            
            .delay-1 { animation-delay: 0.05s; }
            .delay-2 { animation-delay: 0.1s; }
            .delay-3 { animation-delay: 0.15s; }
        </style>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pedidoRapido', (catalogoModelos) => ({

                catalogoModelos: catalogoModelos,
                items: [],
                numSerie: '',
                lote: '',
                modeloDetectado: '',
                errorSerie: '',
                isMobile: window.innerWidth < 1024,
                showScannerMobile: false,

                form: {
                    id_modelo: '',
                    id_voltaje: '',
                    id_color: '',
                    voltajes: [],
                    colores: [],
                },

                fechaHoy: new Date().toLocaleDateString('es-MX', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                }).replace(/\//g, '/'),

                mapaModelos: {
                    '14': 'Zeus', '05': 'Galaxy', '03': 'Primavera', '19': 'Reina',
                    '09': 'VmpS5', '06': 'Rayo', '11': 'Polar', '24': 'Urbex',
                    '18': 'Eclipce', '07': 'Aguila', '08': 'Sol', '16': 'Sol Pro',
                },

                init() {
                    window.addEventListener('resize', () => {
                        this.isMobile = window.innerWidth < 1024;
                    });
                },

                limpiarSerie() {
                    this.numSerie = '';
                    this.modeloDetectado = '';
                    this.errorSerie = '';
                    this.form.id_modelo = '';
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];
                    this.focusInput();
                },

                focusInput() {
                    this.$nextTick(() => {
                        if (this.isMobile && this.$refs.serieInputMobile) {
                            this.$refs.serieInputMobile.focus();
                        } else if (this.$refs.serieInput) {
                            this.$refs.serieInput.focus();
                        }
                    });
                },

                onSerieInput() {
                    this.numSerie = this.numSerie.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    this.errorSerie = '';
                    this.modeloDetectado = '';

                    if (this.numSerie.length === 17) {
                        const codigo = this.numSerie.substring(11, 13);
                        const nombreModelo = this.mapaModelos[codigo] ?? null;

                        if (nombreModelo) {
                            this.modeloDetectado = nombreModelo;
                            this.detectarModelo(nombreModelo);
                        } else {
                            this.errorSerie = `Código no reconocido: ${codigo}`;
                            this.form.id_modelo = '';
                            this.form.voltajes = [];
                            this.form.colores = [];
                        }
                    } else {
                        this.form.id_modelo = '';
                        this.form.voltajes = [];
                        this.form.colores = [];
                    }
                },

                async detectarModelo(nombreModelo) {
                    const modeloEncontrado = this.catalogoModelos.find(m =>
                        m.nombre.trim().toLowerCase() === nombreModelo.trim().toLowerCase()
                    );

                    if (!modeloEncontrado) {
                        this.errorSerie = `Modelo "${nombreModelo}" no encontrado`;
                        return;
                    }

                    this.form.id_modelo = modeloEncontrado.id;
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];

                    try {
                        const [voltajes, colores] = await Promise.all([
                            fetch(`/voltaje-por-modelo/${modeloEncontrado.id}`).then(r => r.json()),
                            fetch(`/colores-por-modelo/${modeloEncontrado.id}`).then(r => r.json()),
                        ]);
                        
                        this.form.voltajes = voltajes || [];
                        this.form.colores = colores || [];

                        if (this.form.voltajes.length === 1) {
                            this.form.id_voltaje = this.form.voltajes[0].id_voltaje;
                        }
                        if (this.form.colores.length === 1) {
                            this.form.id_color = this.form.colores[0].id_color;
                        }
                    } catch (e) {
                        console.error(e);
                        this.errorSerie = 'Error cargando opciones';
                    }
                },

                agregarItem() {
                    this.errorSerie = '';

                    if (this.numSerie.length !== 17) {
                        this.errorSerie = 'Debe tener 17 caracteres';
                        return;
                    }
                    if (!this.form.id_modelo || !this.form.id_voltaje || !this.form.id_color) {
                        this.errorSerie = 'Selecciona voltaje y color';
                        return;
                    }

                    const serieExiste = this.items.some(i => i.series.includes(this.numSerie));
                    if (serieExiste) {
                        this.errorSerie = 'Esta serie ya fue agregada';
                        return;
                    }

                    const voltajeObj = this.form.voltajes.find(v => v.id_voltaje == this.form.id_voltaje);
                    const colorObj = this.form.colores.find(c => c.id_color == this.form.id_color);

                    // Buscar grupo existente con MISMO modelo, voltaje, color Y MISMO lote
                    const existing = this.items.find(i =>
                        i.id_modelo == this.form.id_modelo &&
                        i.id_voltaje == this.form.id_voltaje &&
                        i.id_color == this.form.id_color &&
                        i.lote == this.lote // ← Agregamos esta condición
                    );

                    const serie = this.numSerie;

                    if (existing) {
                        // Si existe grupo con las mismas características Y mismo lote, agregar serie ahí
                        existing.series.push(serie);
                        this.items = [...this.items];
                    } else {
                        // Si no existe, crear nuevo grupo
                        this.items.push({
                            id_modelo: this.form.id_modelo,
                            id_voltaje: this.form.id_voltaje,
                            id_color: this.form.id_color,
                            modelo_nombre: this.modeloDetectado,
                            voltaje_nombre: voltajeObj?.voltaje ?? '',
                            color_nombre: colorObj?.color ?? '',
                            lote: this.lote,
                            series: [serie],
                        });
                    }

                    this.numSerie = '';
                    this.lote = ''; // Limpiamos el lote después de agregar
                    this.modeloDetectado = '';
                    this.errorSerie = '';
                    this.form.id_modelo = '';
                    this.form.id_voltaje = '';
                    this.form.id_color = '';
                    this.form.voltajes = [];
                    this.form.colores = [];
                    
                    if (this.isMobile) {
                        this.showScannerMobile = false;
                    }
                    
                    this.focusInput();
                },

                quitarSerie(itemIndex, serieIndex) {
                    this.items[itemIndex].series.splice(serieIndex, 1);
                    if (this.items[itemIndex].series.length === 0) {
                        this.items.splice(itemIndex, 1);
                    }
                    this.items = [...this.items];
                },

                quitarItem(index) {
                    this.items.splice(index, 1);
                    this.items = [...this.items];
                },

                generarPdf() {
                    if (this.items.length === 0) return;
                    document.getElementById('formRapido').submit();
                },

            }));
        });
    </script>
    @endpush
</x-app-layout>