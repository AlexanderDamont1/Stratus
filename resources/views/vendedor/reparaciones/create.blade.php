<x-app-layout>
<div class="mx-auto max-w-2xl space-y-6" x-data="repCreate()" x-init="init()">

    {{-- ── Header ── --}}
    <div>
        <a href="{{ route('reparaciones.index') }}"
           class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                  flex items-center gap-1 mb-2 transition w-fit">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a órdenes
        </a>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Nueva orden de trabajo</h2>
        <p class="text-xs text-gray-400 mt-0.5">
            Busca por número de serie para autocompletar los datos del cliente y ver garantías activas
        </p>
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

    {{-- ══════════════════════════════════════════
         PASO 1 — Unidad
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Unidad</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Busca por número de serie o descríbela manualmente si no está registrada
            </p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Buscador --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Número de serie
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text"
                               x-model="numSerie"
                               @keydown.enter.prevent="buscarBicicleta()"
                               placeholder="Ej: REVI-48V-00123"
                               :disabled="biciEncontrada"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5
                                      text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400
                                      disabled:opacity-50 transition font-mono pr-10">
                        <svg x-show="buscando"
                             class="animate-spin w-4 h-4 text-gray-400 absolute right-3 top-2.5"
                             fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <button @click="buscarBicicleta()"
                            :disabled="buscando || !numSerie.trim() || biciEncontrada"
                            class="px-4 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   rounded-xl text-sm font-semibold hover:opacity-90 transition
                                   disabled:opacity-40 active:scale-[.98]">
                        Buscar
                    </button>
                    <button x-show="biciEncontrada || biciNoEncontrada"
                            @click="limpiarBici()"
                            class="px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl
                                   text-xs text-gray-500 dark:text-gray-400 hover:bg-gray-50
                                   dark:hover:bg-gray-700 transition">
                        Limpiar
                    </button>
                </div>
            </div>

            {{-- ── Bici encontrada ── --}}
            <template x-if="biciEncontrada && bici">
                <div class="space-y-3"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1">

                    {{-- Info de la unidad --}}
                    <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20
                                border border-green-200 dark:border-green-800 rounded-xl p-3.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-green-800 dark:text-green-400">
                                Unidad encontrada en el sistema
                            </p>
                            <p class="text-xs text-green-700 dark:text-green-500 mt-0.5"
                               x-text="[bici.marca, bici.modelo, bici.color, bici.voltaje].filter(Boolean).join(' · ')"></p>
                        </div>
                    </div>

                    {{-- Garantías activas --}}
                    <template x-if="garantiasActivas.length > 0">
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2.5">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                <p class="text-xs font-semibold text-purple-800 dark:text-purple-400">
                                    Componentes con garantía activa
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <template x-for="g in garantiasActivas" :key="g.clave">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400 shrink-0"></span>
                                            <span class="text-xs text-purple-700 dark:text-purple-300"
                                                  x-text="g.nombre"></span>
                                        </div>
                                        <span class="text-[10px] text-purple-500 dark:text-purple-400 font-mono"
                                              x-text="'Hasta ' + g.expira_at"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Sin garantías --}}
                    <template x-if="garantiasActivas.length === 0">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50
                                    border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Esta unidad no tiene componentes con garantía activa
                            </p>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ── Bici no encontrada → descripción manual ── --}}
            <template x-if="biciNoEncontrada">
                <div class="space-y-3"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1">
                    <div class="flex items-start gap-2.5 bg-amber-50 dark:bg-amber-900/20
                                border border-amber-200 dark:border-amber-700 rounded-xl px-3.5 py-3">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs text-amber-800 dark:text-amber-400">
                            Número de serie no registrado.
                            <span class="font-semibold">Describe la unidad manualmente.</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                            Descripción de la unidad <span class="text-red-400">*</span>
                        </label>
                        <input type="text" x-model="unidadDescripcion"
                               placeholder="Ej: Bicicleta eléctrica negra 36V sin marca"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5
                                      text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         PASO 2 — Cliente
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Cliente</p>
            <p class="text-xs text-gray-400 mt-0.5">
                <template x-if="clienteAutocompletado">
                    <span class="text-green-600 dark:text-green-400 font-medium">
                        ✓ Autocompletado desde el sistema ·
                    </span>
                </template>
                El cliente debe presentar identificación oficial al entregar la unidad
            </p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Banner autocomplete --}}
            <div x-show="clienteAutocompletado"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20
                        border border-blue-200 dark:border-blue-700 rounded-xl px-3.5 py-2.5">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs text-blue-800 dark:text-blue-400">
                    Cliente vinculado al número de serie en el sistema
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Nombre <span class="text-red-400">*</span>
                    </label>
                    <input type="text" x-model="clienteNombre"
                           placeholder="Nombre completo"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Teléfono
                        <span class="font-normal text-gray-400">(referencia — no se guarda en la orden)</span>
                    </label>
                    <input type="tel" x-model="clienteTelefonoDisplay"
                           placeholder="55 1234 5678"
                           readonly
                           :class="clienteTelefonoDisplay ? '' : 'opacity-50'"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                        Correo electrónico
                        <span class="font-normal text-gray-400">(para recibir cotización y aviso de entrega)</span>
                    </label>
                    <input type="email" x-model="clienteEmail"
                           placeholder="cliente@correo.com"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <p x-show="!clienteEmail.trim()"
                       class="text-[11px] text-amber-600 dark:text-amber-400 mt-1">
                        ⚠ Sin correo no se podrá enviar la cotización por email
                    </p>
                </div>
            </div>

            {{-- Verificación ID --}}
            <label class="flex items-start gap-3 cursor-pointer select-none">
                <div class="relative mt-0.5 shrink-0">
                    <input type="checkbox" x-model="idVerificada" class="sr-only">
                    <div @click="idVerificada = !idVerificada"
                         :class="idVerificada
                             ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white'
                             : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600'"
                         class="w-5 h-5 rounded-md border-2 transition-colors flex items-center justify-center cursor-pointer">
                        <svg x-show="idVerificada" class="w-3 h-3 text-white dark:text-gray-900"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                        Identificación verificada
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        El cliente presentó una identificación oficial al entregar la unidad
                    </p>
                </div>
            </label>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         PASO 3 — Detalle de la orden
    ══════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Detalle de la orden</p>
        </div>
        <div class="p-5 space-y-4">

            {{-- Tipo --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">
                    Tipo de OT
                </label>
                <div class="grid grid-cols-3 gap-2">
                    {{-- Reparación — siempre disponible --}}
                    <button type="button" @click="tipo = 'reparacion'"
                            :class="tipo==='reparacion'
                                ? 'border-gray-900 dark:border-gray-300 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-900 dark:ring-gray-300'
                                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                            class="border rounded-xl px-3 py-3 text-center transition-all duration-150">
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Reparación</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Falla o daño</p>
                    </button>

                    {{-- Mantenimiento — siempre disponible --}}
                    <button type="button" @click="tipo = 'mantenimiento'"
                            :class="tipo==='mantenimiento'
                                ? 'border-gray-900 dark:border-gray-300 bg-gray-50 dark:bg-gray-700 ring-1 ring-gray-900 dark:ring-gray-300'
                                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                            class="border rounded-xl px-3 py-3 text-center transition-all duration-150">
                        <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Mantenimiento</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Servicio preventivo</p>
                    </button>

                    {{-- Garantía — solo si el cliente está en el sistema (tiene id_cliente) --}}
                    <template x-if="idCliente">
                        <a href="{{ url('sucursal/garantias') }}"
                           class="border rounded-xl px-3 py-3 text-center transition-all duration-150
                                  border-purple-200 dark:border-purple-700 bg-purple-50 dark:bg-purple-900/20
                                  hover:border-purple-400 dark:hover:border-purple-500 cursor-pointer
                                  flex flex-col items-center justify-center gap-0.5">
                            <p class="text-xs font-semibold text-purple-800 dark:text-purple-300">Garantía</p>
                            <p class="text-[10px] text-purple-500 dark:text-purple-400">Ver en garantías →</p>
                        </a>
                    </template>
                    <template x-if="!idCliente">
                        <div class="border rounded-xl px-3 py-3 text-center
                                    border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50
                                    opacity-50 cursor-not-allowed"
                             title="Solo disponible para clientes registrados en el sistema">
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500">Garantía</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Solo clientes registrados</p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Costo de mantenimiento (solo si tipo = mantenimiento) --}}
            <div x-show="tipo === 'mantenimiento'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Costo base de la reparación
                    <span class="font-normal text-gray-400">(opcional — acuerdo interno)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-sm text-gray-400">$</span>
                    <input type="number" x-model="costoReparacion"
                           min="0" step="0.01" placeholder="0.00"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl pl-7 pr-4 py-2.5
                                  text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono">
                </div>
            </div>

            {{-- Problema reportado --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Problema reportado <span class="text-red-400">*</span>
                </label>
                <textarea x-model="problemaReportado" rows="3"
                          placeholder="Describe el problema que reporta el cliente..."
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3
                                 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
            </div>

            {{-- Notas internas --}}
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                    Notas internas
                    <span class="font-normal text-gray-400">(solo visible para el equipo)</span>
                </label>
                <textarea x-model="notasInternas" rows="2"
                          placeholder="Estado físico al recibir, accesorios incluidos, observaciones..."
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3
                                 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
            </div>

        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="flex items-center justify-between gap-3 pb-8">
        <a href="{{ route('reparaciones.index') }}"
           class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700
                  dark:hover:text-gray-200 transition">
            Cancelar
        </a>
        <button @click="crearOt()"
                :disabled="creando || !puedeCrear()"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-6 py-2.5
                       rounded-xl text-sm font-semibold hover:opacity-90 transition
                       disabled:opacity-40 disabled:cursor-not-allowed active:scale-[.98]
                       flex items-center gap-2 shadow-sm">
            <svg x-show="creando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="creando ? 'Creando...' : 'Crear orden de trabajo'"></span>
        </button>
    </div>

</div>

<script>
function repCreate() {
    return {
        // Unidad
        numSerie: '', bici: null, unidadDescripcion: '',
        biciEncontrada: false, biciNoEncontrada: false, buscando: false,
        garantiasActivas: [],

        // Cliente
        idCliente: null, clienteNombre: '', clienteEmail: '', clienteTelefonoDisplay: '',
        idVerificada: false, clienteAutocompletado: false,

        // OT
        tipo: 'reparacion', costoReparacion: '', problemaReportado: '', notasInternas: '',

        // UI
        creando: false,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

        init() {},

        // ── Buscar bicicleta ────────────────────────────────────────────────

        async buscarBicicleta() {
            if (!this.numSerie.trim() || this.buscando) return;
            this.buscando         = true;
            this.biciEncontrada   = false;
            this.biciNoEncontrada = false;
            this.garantiasActivas = [];
            this.limpiarCliente();

            try {
                const res  = await fetch('{{ route("reparaciones.buscar-bicicleta") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ num_serie: this.numSerie.trim() }),
                });
                const data = await res.json();

                if (data.ok && data.data) {
                    this.bici             = data.data.bicicleta;
                    this.biciEncontrada   = true;
                    this.garantiasActivas = data.data.garantias_activas ?? [];

                    if (data.data.cliente) {
                        const c = data.data.cliente;
                        this.idCliente             = c.id_cliente;
                        this.clienteNombre         = [c.nombre_cliente, c.apellido1, c.apellido2].filter(Boolean).join(' ');
                        this.clienteTelefonoDisplay = c.telefono ?? '';
                        this.clienteEmail          = c.correo ?? '';
                        this.clienteAutocompletado = true;
                    }
                } else {
                    this.biciNoEncontrada = true;
                    // Cliente nuevo: garantía no disponible, forzar tipo a reparacion o mantenimiento
                    if (this.tipo === 'garantia') this.tipo = 'reparacion';
                }
            } catch {
                this.flash('Error al buscar la unidad', 'error');
            } finally {
                this.buscando = false;
            }
        },

        limpiarBici() {
            this.numSerie         = '';
            this.bici             = null;
            this.unidadDescripcion= '';
            this.biciEncontrada   = false;
            this.biciNoEncontrada = false;
            this.garantiasActivas = [];
            this.limpiarCliente();
            if (this.tipo === 'garantia') this.tipo = 'reparacion';
        },

        limpiarCliente() {
            this.idCliente             = null;
            this.clienteNombre         = '';
            this.clienteTelefonoDisplay = '';
            this.clienteEmail          = '';
            this.idVerificada          = false;
            this.clienteAutocompletado = false;
        },

        // ── Validación del botón Crear ──────────────────────────────────────

        puedeCrear() {
            if (!this.problemaReportado.trim()) return false;
            // Debe tener serie o descripción
            if (!this.numSerie.trim() && !this.unidadDescripcion.trim()) return false;
            // Si es mantenimiento debe tener costo
            // costo_reparacion no es obligatorio al crear — se puede cotizar después
            return true;
            return true;
        },

        // ── Crear OT ───────────────────────────────────────────────────────

        async crearOt() {
            if (!this.puedeCrear()) return;
            this.creando = true;

            try {
                const res  = await fetch('{{ route("reparaciones.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({
                        // num_serie solo si fue encontrada en el sistema
                        num_serie:           this.biciEncontrada ? (this.numSerie.trim() || null) : null,
                        unidad_descripcion:  !this.biciEncontrada ? (this.unidadDescripcion.trim() || null) : null,
                        id_cliente:          this.idCliente,
                        cliente_nombre:      this.clienteNombre.trim() || null,
                        // cliente_telefono no existe en tabla reparaciones — no se envía
                        cliente_email:       this.clienteEmail.trim() || null,
                        id_verificada:       this.idVerificada,
                        tipo:                this.tipo,
                        // costo_reparacion solo aplica a tipo=reparacion y se persiste en BD
                        // para mantenimiento el costo base es acuerdo interno, no se guarda
                        costo_reparacion:    this.tipo === 'reparacion'
                                                ? (parseFloat(this.costoReparacion) || 0)
                                                : 0,
                        problema_reportado:  this.problemaReportado.trim(),
                        notas_internas:      this.notasInternas.trim() || null,
                    }),
                });
                const data = await res.json();

                if (!data.ok) {
                    this.flash(data.mensaje ?? 'Error al crear la OT', 'error');
                    return;
                }

                this.flash(data.mensaje);
                setTimeout(() => {
                    window.location.href = '{{ route("reparaciones.index") }}';
                }, 1200);

            } catch {
                this.flash('Error de conexión', 'error');
            } finally {
                this.creando = false;
            }
        },

        // ── Flash ──────────────────────────────────────────────────────────

        flash(msg, tipo = 'success') {
            this.flashMsg  = msg;
            this.flashTipo = tipo;
            this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(
                () => this.flashVisible = false,
                tipo === 'error' ? 4500 : 3000
            );
        },
    }
}
</script>
</x-app-layout>