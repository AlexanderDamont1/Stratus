<x-app-layout>
{{-- El div con x-data envuelve TODO: tabla + los 3 modales --}}
<div class="mx-auto space-y-5" x-data="stockIndex()" x-init="init()">

    {{-- ── Header ── --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Piezas y stock</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                <span x-text="stats.total"></span> piezas ·
                <span x-text="stats.bajo_stock"></span> con stock bajo
            </p>
        </div>
        <button @click="abrirModalCrear()"
                class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900
                       text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90
                       transition active:scale-[.98] shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva pieza
        </button>
    </div>

    {{-- ── Flash ── --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo==='error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <svg x-show="flashTipo==='success'" class="h-4 w-4 text-green-600 dark:text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg x-show="flashTipo==='error'" class="h-4 w-4 text-red-600 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    {{-- ── Filtros ── --}}
    <div class="flex flex-wrap gap-2">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="busqueda" @input.debounce.400ms="cargar()"
                   placeholder="Buscar por nombre o clave..."
                   class="w-full pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-xl
                          text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                          focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>
        <select x-model="categoriaFiltro" @change="cargar()"
                class="border border-gray-200 dark:border-gray-600 rounded-xl px-3 py-2 text-sm
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-1 focus:ring-gray-400">
            <option value="">Todas las categorías</option>
            <template x-for="cat in categorias" :key="cat">
                <option :value="cat" x-text="cat"></option>
            </template>
        </select>
        <button @click="soloStockBajo = !soloStockBajo; cargar()"
                :class="soloStockBajo
                    ? 'bg-red-100 dark:bg-red-800/30 border-red-300 dark:border-red-700 text-red-700 dark:text-red-400'
                    : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400'"
                class="flex items-center gap-1.5 border rounded-xl px-3.5 py-2 text-sm font-medium transition">
            <span class="w-2 h-2 rounded-full bg-red-400 shrink-0"></span>
            Stock bajo
        </button>
    </div>

    {{-- ── Tabla ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

        {{-- Skeleton --}}
        <div x-show="cargando" class="p-4 space-y-3">
            <template x-for="i in 5" :key="i">
                <div class="h-12 bg-gray-100 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </template>
        </div>

        <template x-if="!cargando">
            <div>
                {{-- Sin resultados --}}
                <div x-show="items.length === 0"
                     class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin piezas</p>
                    <p class="text-xs text-gray-400 mt-1">Agrega tu primera pieza al catálogo</p>
                </div>

                {{-- Filas --}}
                <div x-show="items.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <template x-for="p in items" :key="p.id_pieza">
                        <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">

                            {{-- Info principal --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                                          x-text="p.nombre"></span>
                                    <span class="text-[10px] font-mono bg-gray-100 dark:bg-gray-700
                                                 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 rounded"
                                          x-text="p.clave"></span>
                                    <span x-show="p.categoria"
                                          class="text-[10px] bg-blue-50 dark:bg-blue-900/20 text-blue-600
                                                 dark:text-blue-400 px-1.5 py-0.5 rounded"
                                          x-text="p.categoria"></span>
                                </div>
                                <p x-show="p.marca_pieza"
                                   class="text-xs text-gray-400 mt-0.5" x-text="p.marca_pieza"></p>
                            </div>

                            {{-- Stock --}}
                            <div class="text-center shrink-0 w-20">
                                <p class="text-lg font-bold"
                                   :class="p.stock_actual <= p.stock_minimo
                                       ? 'text-red-600 dark:text-red-400'
                                       : 'text-gray-800 dark:text-gray-200'"
                                   x-text="p.stock_actual"></p>
                                <p class="text-[10px] text-gray-400">
                                    mín <span x-text="p.stock_minimo"></span>
                                </p>
                            </div>

                            {{-- Precio venta --}}
                            <div class="text-right shrink-0 w-24 hidden sm:block">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                                   x-text="'$'+Number(p.precio_venta).toLocaleString('es-MX')"></p>
                                <p class="text-[10px] text-gray-400">venta</p>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button @click="abrirVenta(p)"
                                        :disabled="p.stock_actual <= 0"
                                        title="Vender pieza suelta"
                                        class="p-2 rounded-lg border border-gray-200 dark:border-gray-600
                                               text-gray-500 dark:text-gray-400 hover:bg-blue-50
                                               dark:hover:bg-blue-900/20 hover:border-blue-300
                                               dark:hover:border-blue-700 hover:text-blue-600
                                               dark:hover:text-blue-400 transition disabled:opacity-30
                                               disabled:pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                                    </svg>
                                </button>
                                <button @click="abrirEntrada(p)"
                                        title="Registrar entrada"
                                        class="p-2 rounded-lg border border-gray-200 dark:border-gray-600
                                               text-gray-500 dark:text-gray-400 hover:bg-green-50
                                               dark:hover:bg-green-900/20 hover:border-green-300
                                               dark:hover:border-green-700 hover:text-green-600
                                               dark:hover:text-green-400 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                                <button @click="abrirHistorial(p)"
                                        title="Ver historial"
                                        class="p-2 rounded-lg border border-gray-200 dark:border-gray-600
                                               text-gray-500 dark:text-gray-400 hover:bg-gray-50
                                               dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <button @click="abrirModalEditar(p)"
                                        title="Editar"
                                        class="p-2 rounded-lg border border-gray-200 dark:border-gray-600
                                               text-gray-500 dark:text-gray-400 hover:bg-gray-50
                                               dark:hover:bg-gray-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Paginación --}}
                <div x-show="lastPage > 1"
                     class="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                    <button @click="cambiarPagina(pagina - 1)" :disabled="pagina === 1"
                            class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                                   text-gray-500 disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        ← Anterior
                    </button>
                    <span class="text-xs text-gray-400">
                        <span x-text="pagina"></span> / <span x-text="lastPage"></span>
                    </span>
                    <button @click="cambiarPagina(pagina + 1)" :disabled="pagina === lastPage"
                            class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                                   text-gray-500 disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Siguiente →
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Crear / Editar pieza
    ══════════════════════════════════════════ --}}
    <div x-show="modalForm" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalForm = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white"
                    x-text="modoEditar ? 'Editar pieza' : 'Nueva pieza'"></h3>
                <button @click="modalForm = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">

                {{-- Nombre + Clave --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" x-model="form.nombre" placeholder="Ej: Motor 350W"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Clave interna <span class="text-red-400">*</span>
                        </label>
                        <input type="text" x-model="form.clave" placeholder="Ej: MOT-350W-36V"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                    </div>
                </div>

                {{-- Categoría + Marca --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Categoría</label>
                        <input type="text" x-model="form.categoria" placeholder="Ej: Motor, Batería, Frenos"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Marca de la pieza</label>
                        <input type="text" x-model="form.marca_pieza" placeholder="Ej: Bafang"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                </div>

                {{-- Voltaje compatible --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Voltaje compatible
                        <span class="font-normal text-gray-400">(dejar vacío si no aplica)</span>
                    </label>
                    <input type="text" x-model="form.voltaje_compatible" placeholder="Ej: 36V, 48V"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                {{-- Modelos compatibles --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Modelos compatibles
                        <span class="font-normal text-gray-400">(vacío = universal)</span>
                    </label>
                    <div x-show="modelosDisponibles.length === 0"
                         class="text-xs text-gray-400 italic">Cargando modelos…</div>
                    <div x-show="modelosDisponibles.length > 0"
                         class="grid grid-cols-2 gap-1.5 max-h-36 overflow-y-auto pr-1">
                        <template x-for="m in modelosDisponibles" :key="m.id_modelo">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       :value="m.id_modelo"
                                       :checked="form.modelos_compatibles.includes(m.id_modelo)"
                                       @change="toggleModelo(m.id_modelo)"
                                       class="w-3.5 h-3.5 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate"
                                      x-text="m.nombre_modelo"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Precios --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Precio costo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" x-model="form.precio_costo" min="0" step="0.01" placeholder="0.00"
                                   class="w-full pl-6 pr-3 py-2.5 text-sm border border-gray-200 dark:border-gray-600
                                          rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Precio venta</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm text-gray-400">$</span>
                            <input type="number" x-model="form.precio_venta" min="0" step="0.01" placeholder="0.00"
                                   class="w-full pl-6 pr-3 py-2.5 text-sm border border-gray-200 dark:border-gray-600
                                          rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                        </div>
                    </div>
                </div>

                {{-- Stock mínimo + Stock inicial (solo al crear) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Stock mínimo</label>
                        <input type="number" x-model="form.stock_minimo" min="0" placeholder="0"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                    </div>
                    <div x-show="!modoEditar">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Stock inicial
                            <span class="font-normal text-gray-400">(opcional)</span>
                        </label>
                        <input type="number" x-model="form.stock_inicial" min="0" placeholder="0"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                    </div>
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Descripción</label>
                    <textarea x-model="form.descripcion" rows="2"
                              placeholder="Notas técnicas, referencias, etc."
                              class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                     bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                     focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2">
                <button @click="modalForm = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button @click="guardarPieza()"
                        :disabled="guardando || !form.nombre.trim() || !form.clave.trim()"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40">
                    <span x-text="guardando ? 'Guardando...' : (modoEditar ? 'Guardar cambios' : 'Crear pieza')"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Entrada de stock
    ══════════════════════════════════════════ --}}
    <div x-show="modalEntrada" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalEntrada = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar entrada</h3>
            <p class="text-xs text-gray-400 mb-4" x-text="piezaSeleccionada?.nombre"></p>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Cantidad <span class="text-red-400">*</span>
                    </label>
                    <input type="number" x-model="formEntrada.cantidad" min="1" placeholder="0"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Nota <span class="font-normal text-gray-400">(opcional)</span>
                    </label>
                    <input type="text" x-model="formEntrada.nota" placeholder="Ej: Compra a proveedor, pedido #123"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50
                            border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Stock actual:</span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                          x-text="piezaSeleccionada?.stock_actual ?? '—'"></span>
                    <span x-show="formEntrada.cantidad > 0" class="text-xs text-green-600 dark:text-green-400">
                        → <span x-text="(piezaSeleccionada?.stock_actual ?? 0) + parseInt(formEntrada.cantidad || 0)"></span>
                    </span>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button @click="modalEntrada = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button @click="confirmarEntrada()"
                        :disabled="registrandoEntrada || !(formEntrada.cantidad > 0)"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40">
                    <span x-text="registrandoEntrada ? 'Registrando...' : 'Confirmar entrada'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Vender pieza suelta (sin OT)
    ══════════════════════════════════════════ --}}
    <div x-show="modalVenta" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalVenta = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Vender pieza suelta</h3>
                <p class="text-xs text-gray-400 mt-0.5" x-text="piezaSeleccionada?.nombre"></p>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Cantidad <span class="text-red-400">*</span>
                        </label>
                        <input type="number" x-model="formVenta.cantidad" min="1"
                               :max="piezaSeleccionada?.stock_actual" @input="autocompletarPagoVenta()"
                               class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                    </div>
                    <div class="flex flex-col justify-end">
                        <p class="text-xs text-gray-400 mb-1.5">Total</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white" x-text="fmtVenta(totalVenta)"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                        Cliente <span class="font-normal text-gray-400">(opcional)</span>
                    </label>
                    <input type="text" x-model="formVenta.cliente_nombre" placeholder="Nombre"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 mb-2
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <input type="text" x-model="formVenta.cliente_telefono" placeholder="Teléfono"
                           class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Pago</label>
                        <button type="button" @click="agregarPagoVenta()"
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800
                                       dark:hover:text-gray-200 transition">
                            + Dividir pago
                        </button>
                    </div>

                    <template x-for="(pago, i) in pagosVenta" :key="i">
                        <div class="space-y-1.5 mb-2">
                            <div class="flex items-center gap-2">
                                <select x-model="pago.id_metodo" @change="onMetodoChangeVenta(i)"
                                        class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                               px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                    <option value="">— Método —</option>
                                    @foreach($metodos as $m)
                                    <option value="{{ $m['value'] }}"
                                            data-efectivo="{{ $m['es_efectivo'] ? '1' : '0' }}"
                                            data-ref="{{ $m['requiere_referencia'] ? '1' : '0' }}">
                                        {{ $m['label'] }}
                                    </option>
                                    @endforeach
                                </select>
                                <template x-if="pagosVenta.length > 1">
                                    <div class="relative w-24 shrink-0">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">$</span>
                                        <input type="number" step="0.01" min="0" x-model="pago.monto" @input="recalcularPagosVenta(i)"
                                               class="w-full pl-5 pr-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                                      text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition tabular-nums">
                                    </div>
                                </template>
                                <button type="button" @click="quitarPagoVenta(i)"
                                        x-show="pagosVenta.length > 1 && !(pago.es_efectivo && pagosVenta.filter(p => p.es_efectivo).length === 1)"
                                        class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="pago.requiere_referencia" class="text-[11px] text-gray-400">
                                El folio de este pago se pedirá en el detalle de la venta.
                            </p>
                        </div>
                    </template>

                    <p class="text-xs" :class="pagosCubreVenta ? 'text-green-600 dark:text-green-400' : 'text-gray-400'"
                       x-text="pagosCubreVenta ? '✓ Pago completo' : 'Pendiente de asignar pago'"></p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-2">
                <button @click="modalVenta = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button @click="confirmarVenta()"
                        :disabled="vendiendo || !(formVenta.cantidad > 0) || !pagosCubreVenta || !pagosVenta.every(p => p.id_metodo)"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40">
                    <span x-text="vendiendo ? 'Procesando...' : 'Vender'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Historial de movimientos
    ══════════════════════════════════════════ --}}
    <div x-show="modalHistorial" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modalHistorial = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col"
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition duration-100" x-transition:leave-end="opacity-0 scale-95">

            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white"
                        x-text="'Historial: ' + (piezaSeleccionada?.nombre ?? '')"></h3>
                    <p class="text-xs text-gray-400 mt-0.5"
                       x-text="'Stock actual: ' + (piezaSeleccionada?.stock_actual ?? '—')"></p>
                </div>
                <button @click="modalHistorial = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-4">
                <div x-show="cargandoHistorial" class="space-y-3">
                    <template x-for="i in 4" :key="i">
                        <div class="h-10 bg-gray-100 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    </template>
                </div>

                <div x-show="!cargandoHistorial && historial.length === 0"
                     class="text-center py-10">
                    <p class="text-sm text-gray-400">Sin movimientos registrados</p>
                </div>

                <div x-show="!cargandoHistorial && historial.length > 0" class="space-y-2">
                    <template x-for="(mov, idx) in historial" :key="mov.id_movimiento ?? idx">
                        <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl px-4 py-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                 :class="mov.tipo === 'entrada'
                                     ? 'bg-green-100 dark:bg-green-900/30'
                                     : 'bg-red-100 dark:bg-red-900/30'">
                                <span class="text-sm" x-text="mov.tipo === 'entrada' ? '↑' : '↓'"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-semibold capitalize"
                                          :class="mov.tipo === 'entrada'
                                              ? 'text-green-700 dark:text-green-400'
                                              : 'text-red-700 dark:text-red-400'"
                                          x-text="mov.tipo + ' × ' + mov.cantidad"></span>
                                    <span class="text-[10px] font-mono text-gray-400"
                                          x-text="mov.stock_antes + ' → ' + mov.stock_despues"></span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"
                                   x-text="mov.usuario?.nombre_usuario ?? '—'"></p>
                                <p x-show="mov.nota" class="text-xs text-gray-400 italic mt-0.5"
                                   x-text="mov.nota"></p>
                                <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5"
                                   x-text="mov.id_reparacion ? 'OT: ' + mov.id_reparacion : ''"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Paginación historial --}}
            <div x-show="historialLastPage > 1"
                 class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between shrink-0">
                <button @click="cambiarPaginaHistorial(historialPagina - 1)" :disabled="historialPagina === 1"
                        class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                               text-gray-500 disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    ← Anterior
                </button>
                <span class="text-xs text-gray-400">
                    <span x-text="historialPagina"></span> / <span x-text="historialLastPage"></span>
                </span>
                <button @click="cambiarPaginaHistorial(historialPagina + 1)" :disabled="historialPagina === historialLastPage"
                        class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                               text-gray-500 disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>

</div>{{-- ← cierre del x-data="stockIndex()" --}}

<script>
function stockIndex() {
    return {
        items: [], categorias: [], modelosDisponibles: [],
        cargando: true, guardando: false,
        registrandoEntrada: false, cargandoHistorial: false,
        busqueda: '', categoriaFiltro: '', soloStockBajo: false,
        pagina: 1, lastPage: 1,
        stats: { total: 0, bajo_stock: 0 },

        // Modales
        modalForm: false, modalEntrada: false, modalHistorial: false, modalVenta: false,
        modoEditar: false,
        piezaSeleccionada: null,

        // Historial
        historial: [], historialPagina: 1, historialLastPage: 1,

        form: {
            nombre: '', clave: '', categoria: '', marca_pieza: '',
            modelos_compatibles: [], voltaje_compatible: '',
            descripcion: '', precio_costo: '', precio_venta: '',
            stock_inicial: '', stock_minimo: '',
        },
        formEntrada: { cantidad: '', nota: '' },

        // Venta de pieza suelta
        formVenta: { cantidad: '', cliente_nombre: '', cliente_telefono: '' },
        pagosVenta: [],
        vendiendo: false,

        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

        async init() {
            await this.cargar();
            await this.cargarModelos();
        },

        async cargar() {
            this.cargando = true;
            try {
                const p = new URLSearchParams({ page: this.pagina });
                if (this.busqueda)        p.set('q', this.busqueda);
                if (this.categoriaFiltro) p.set('categoria', this.categoriaFiltro);
                if (this.soloStockBajo)   p.set('stock_bajo', '1');

                const res = await fetch(`{{ route('stock_piezas.index') }}?${p}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.ok) return;

                this.items            = data.data.data ?? [];
                this.lastPage         = data.data.last_page ?? 1;
                this.categorias       = data.categorias ?? [];
                this.stats.total      = data.data.total ?? 0;
                this.stats.bajo_stock = this.items.filter(p => p.stock_actual <= p.stock_minimo).length;
            } catch (e) {
                console.error('fetch error:', e);
                this.flash('Error cargando piezas', 'error');
            } finally {
                this.cargando = false;
            }
        },

        async cargarModelos() {
            try {
                const res  = await fetch(`{{ route('stock_piezas.modelos') }}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.ok) this.modelosDisponibles = data.data;
            } catch {}
        },

        cambiarPagina(n) {
            if (n < 1 || n > this.lastPage) return;
            this.pagina = n;
            this.cargar();
        },

        toggleModelo(idModelo) {
            const idx = this.form.modelos_compatibles.indexOf(idModelo);
            if (idx === -1) this.form.modelos_compatibles.push(idModelo);
            else this.form.modelos_compatibles.splice(idx, 1);
        },

        // ── Modales form ──────────────────────────────────────────────────────

        abrirModalCrear() {
            this.modoEditar = false;
            this.piezaSeleccionada = null;
            this.form = {
                nombre: '', clave: '', categoria: '', marca_pieza: '',
                modelos_compatibles: [], voltaje_compatible: '',
                descripcion: '', precio_costo: '', precio_venta: '',
                stock_inicial: '', stock_minimo: '',
            };
            this.modalForm = true;
        },

        abrirModalEditar(p) {
            this.modoEditar = true;
            this.piezaSeleccionada = p;
            this.form = {
                nombre:              p.nombre,
                clave:               p.clave,
                categoria:           p.categoria ?? '',
                marca_pieza:         p.marca_pieza ?? '',
                modelos_compatibles: p.modelos_compatibles ?? [],
                voltaje_compatible:  p.voltaje_compatible ?? '',
                descripcion:         p.descripcion ?? '',
                precio_costo:        p.precio_costo,
                precio_venta:        p.precio_venta,
                stock_minimo:        p.stock_minimo,
            };
            this.modalForm = true;
        },

        async guardarPieza() {
            if (!this.form.nombre.trim() || !this.form.clave.trim()) return;
            this.guardando = true;
            try {
                const url = this.modoEditar
                    ? `{{ url('sucursal/stock/piezas') }}/${this.piezaSeleccionada.id_pieza}`
                    : `{{ route('stock_piezas.store') }}`;
                const method = this.modoEditar ? 'PUT' : 'POST';

                const res  = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        ...this.form,
                        modelos_compatibles: this.form.modelos_compatibles.length
                            ? this.form.modelos_compatibles
                            : null,
                        precio_costo:  parseFloat(this.form.precio_costo)  || 0,
                        precio_venta:  parseFloat(this.form.precio_venta)  || 0,
                        stock_minimo:  parseInt(this.form.stock_minimo)    || 0,
                        stock_inicial: parseInt(this.form.stock_inicial)   || 0,
                    }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash(data.mensaje);
                this.modalForm = false;
                await this.cargar();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.guardando = false; }
        },

        // ── Entrada ───────────────────────────────────────────────────────────

        abrirEntrada(p) {
            this.piezaSeleccionada = p;
            this.formEntrada       = { cantidad: '', nota: '' };
            this.modalEntrada      = true;
        },

        async confirmarEntrada() {
            if (!(this.formEntrada.cantidad > 0)) return;
            this.registrandoEntrada = true;
            try {
                const res  = await fetch(`{{ url('sucursal/stock/piezas') }}/${this.piezaSeleccionada.id_pieza}/entrada`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        cantidad: parseInt(this.formEntrada.cantidad),
                        nota:     this.formEntrada.nota || null,
                    }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error', 'error'); return; }
                this.flash(data.mensaje);
                this.modalEntrada = false;
                await this.cargar();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.registrandoEntrada = false; }
        },

        // ── Venta de pieza suelta ────────────────────────────────────────────

        get totalVenta() {
            const cant = parseInt(this.formVenta.cantidad) || 0;
            return cant * (parseFloat(this.piezaSeleccionada?.precio_venta) || 0);
        },
        get sumaPagosVenta() {
            return this.pagosVenta.reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
        },
        get pagosCubreVenta() {
            if (!(this.totalVenta > 0)) return false;
            if (this.pagosVenta.length === 1) return true;
            return Math.round(this.sumaPagosVenta * 100) >= Math.round(this.totalVenta * 100);
        },

        _nuevoPagoVenta(esEfectivo = false) {
            return { id_metodo: esEfectivo ? 'efectivo' : '', monto: '', referencia: '', es_efectivo: esEfectivo, requiere_referencia: false };
        },

        abrirVenta(p) {
            this.piezaSeleccionada = p;
            this.formVenta         = { cantidad: 1, cliente_nombre: '', cliente_telefono: '' };
            this.pagosVenta        = [this._nuevoPagoVenta(true)];
            this.autocompletarPagoVenta();
            this.modalVenta        = true;
        },

        autocompletarPagoVenta() {
            if (this.pagosVenta.length === 1) this.pagosVenta[0].monto = this.totalVenta.toFixed(2);
        },

        agregarPagoVenta() { this.pagosVenta.push(this._nuevoPagoVenta()); },
        quitarPagoVenta(idx) {
            if (this.pagosVenta.length <= 1) return;
            if (this.pagosVenta[idx].es_efectivo && this.pagosVenta.filter(p => p.es_efectivo).length === 1) return;
            this.pagosVenta.splice(idx, 1);
            this.recalcularPagosVenta();
        },
        onMetodoChangeVenta(idx) {
            const select = document.querySelectorAll('[x-model="pago.id_metodo"]')[idx];
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;
            this.pagosVenta[idx].es_efectivo         = opt.dataset.efectivo === '1';
            this.pagosVenta[idx].requiere_referencia = opt.dataset.ref === '1';
        },
        recalcularPagosVenta(idxEditado) {
            if (this.pagosVenta.length < 2) return;
            const destino = idxEditado === 0 ? this.pagosVenta.length - 1 : 0;
            const ocupado = this.pagosVenta.reduce((s, p, j) => (j !== destino) ? s + (parseFloat(p.monto) || 0) : s, 0);
            const resta = Math.round((this.totalVenta - ocupado) * 100) / 100;
            this.pagosVenta[destino].monto = Math.max(0, resta).toFixed(2);
        },
        fmtVenta(n) { return '$' + Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },

        async confirmarVenta() {
            if (!(this.formVenta.cantidad > 0) || !this.pagosCubreVenta) return;
            this.vendiendo = true;
            try {
                const pagosEnviar = this.pagosVenta.length === 1
                    ? [{ ...this.pagosVenta[0], monto: this.totalVenta.toFixed(2) }]
                    : this.pagosVenta;

                const res = await fetch(`{{ url('sucursal/stock/piezas') }}/${this.piezaSeleccionada.id_pieza}/vender`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        cantidad:          parseInt(this.formVenta.cantidad),
                        cliente_nombre:    this.formVenta.cliente_nombre || null,
                        cliente_telefono:  this.formVenta.cliente_telefono || null,
                        pagos: pagosEnviar.map(p => ({ metodo: p.id_metodo, monto: parseFloat(p.monto || 0), referencia: p.referencia || null })),
                    }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error al vender', 'error'); return; }
                this.modalVenta = false;

                // Si algún pago requiere comprobante (tarjeta/transferencia) y no
                // se capturó la referencia aquí, se termina en el detalle de la
                // venta — ahí un modal obligatorio la pide antes de continuar.
                const faltaReferencia = pagosEnviar.some(p => p.requiere_referencia && !p.referencia);
                if (data.id_venta && faltaReferencia) {
                    window.location.href = `{{ route('ventas.show', ':id') }}`.replace(':id', data.id_venta);
                    return;
                }

                this.flash(data.mensaje);
                if (data.id_venta) {
                    window.open(`{{ route('ventas.ticket', ':id') }}`.replace(':id', data.id_venta), '_blank');
                }
                await this.cargar();
            } catch { this.flash('Error de conexión', 'error'); }
            finally  { this.vendiendo = false; }
        },

        // ── Historial ─────────────────────────────────────────────────────────

        async abrirHistorial(p) {
            this.piezaSeleccionada = p;
            this.historial         = [];
            this.historialPagina   = 1;
            this.historialLastPage = 1;
            this.modalHistorial    = true;
            await this.cargarHistorial();
        },

        async cargarHistorial() {
            this.cargandoHistorial = true;
            try {
                const res  = await fetch(`{{ url('sucursal/stock/piezas') }}/${this.piezaSeleccionada.id_pieza}/historial?page=${this.historialPagina}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.ok) {
                    this.historial         = data.data.data ?? [];
                    this.historialLastPage = data.data.last_page ?? 1;
                }
            } catch {}
            finally { this.cargandoHistorial = false; }
        },

        async cambiarPaginaHistorial(n) {
            if (n < 1 || n > this.historialLastPage) return;
            this.historialPagina = n;
            await this.cargarHistorial();
        },

        // ── Flash ─────────────────────────────────────────────────────────────

        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
        },
    }
}
</script>

</x-app-layout>