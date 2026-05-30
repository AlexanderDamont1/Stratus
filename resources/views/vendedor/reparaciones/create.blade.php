<x-app-layout>
<div class="mx-auto space-y-6" x-data="repCreate()" x-init="init()">


{{-- ══════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════ --}}
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

    {{-- ══════════════════════════════════════════
         MODAL — Reporte de robo
    ══════════════════════════════════════════ --}}
    <div x-show="modalRobo"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.45)"
         @keydown.escape.window="cerrarModalRobo()">

        <div x-show="modalRobo"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    rounded-xl shadow-xl w-full max-w-sm overflow-hidden"
             @click.stop>

            {{-- Franja roja --}}
            <div class="h-1 bg-red-500 w-full"></div>

            <div class="px-5 pt-5 pb-5">
                {{-- Ícono + título --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center
                                justify-center shrink-0 border border-red-100 dark:border-red-800">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Reporte de robo activo</p>
                        <p class="text-xs text-gray-400 mt-0.5">No es posible abrir una orden de trabajo</p>
                    </div>
                </div>

                {{-- Datos del reporte --}}
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800
                            rounded-xl overflow-hidden mb-4">
                    <div class="flex items-center justify-between px-3.5 py-2.5
                                border-b border-red-100 dark:border-red-800">
                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Número de serie</span>
                        <span class="text-xs font-mono font-semibold text-red-800 dark:text-red-300"
                              x-text="numSerie"></span>
                    </div>
                    <div class="flex items-center justify-between px-3.5 py-2.5"
                         :class="roboDatos.fecha ? 'border-b border-red-100 dark:border-red-800' : ''">
                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">Folio del reporte</span>
                        <span class="text-xs font-mono font-semibold text-red-800 dark:text-red-300"
                              x-text="roboDatos.folio ?? '—'"></span>
                    </div>
                    <template x-if="roboDatos.fecha">
                        <div class="flex items-center justify-between px-3.5 py-2.5">
                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">Fecha del reporte</span>
                            <span class="text-xs font-mono font-semibold text-red-800 dark:text-red-300"
                                  x-text="roboDatos.fecha"></span>
                        </div>
                    </template>
                </div>

                <p class="text-xs text-gray-400 leading-relaxed mb-4">
                    Esta unidad tiene un reporte de robo activo. Si crees que es un error,
                    comunícate con soporte antes de continuar.
                </p>

                <div class="flex gap-2">
                    <button @click="cerrarModalRobo()"
                            class="flex-1 border border-gray-200 dark:border-gray-600 rounded-xl py-2.5
                                   text-sm font-semibold text-gray-500 dark:text-gray-400
                                   hover:bg-gray-50 dark:hover:bg-gray-700 transition active:scale-[.98]">
                        Entendido
                    </button>
                    <button @click="cerrarModalRobo()"
                            class="flex-1 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   rounded-xl py-2.5 text-sm font-semibold hover:opacity-90
                                   transition active:scale-[.98]">
                        Nueva búsqueda
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FLASH
    ══════════════════════════════════════════ --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-40 pointer-events-none">
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

    

    {{-- ══════════════════════════════════════════
         GRID — 1/3 izq + 2/3 der
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ── Columna izquierda ── --}}
        <div class="space-y-6 lg:col-span-1">

            {{-- ── Unidad ── --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 pt-5 pb-4">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Unidad</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Busca por número de serie o descríbela si no está registrada
                    </p>
                </div>

                <div class="px-5 pb-5 space-y-3">

                    {{-- Buscador --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Número de serie
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="text"
                                       x-model="numSerie"
                                       @input="numSerie = $event.target.value.toUpperCase().slice(0,17)"
                                       @keydown.enter.prevent="buscarBicicleta()"
                                       placeholder="REVI-48V-00123"
                                       :disabled="biciEncontrada"
                                       maxlength="17"
                                       class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                              px-3.5 py-2 text-sm bg-white dark:bg-gray-700
                                              text-gray-900 dark:text-white font-mono uppercase pr-8
                                              focus:outline-none focus:ring-1 focus:ring-gray-400
                                              disabled:opacity-50 transition">
                                <svg x-show="buscando"
                                     class="animate-spin w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-2.5"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </div>
                            <button @click="buscarBicicleta()"
                                    :disabled="buscando || !numSerie.trim() || biciEncontrada"
                                    class="px-3.5 py-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                           rounded-xl text-sm font-semibold hover:opacity-90 transition
                                           disabled:opacity-40 active:scale-[.98]">
                                Buscar
                            </button>
                        </div>
                        <button x-show="biciEncontrada || biciNoEncontrada || biciNoVendida"
                                @click="limpiarBici()"
                                class="mt-1.5 text-[11px] text-gray-400 hover:text-gray-600
                                       dark:hover:text-gray-300 transition underline">
                            Limpiar búsqueda
                        </button>
                    </div>

                    {{-- Bici encontrada --}}
                    <template x-if="biciEncontrada && bici">
                        <div x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             class="space-y-2">

                            <div class="flex items-start gap-2.5 bg-green-50 dark:bg-green-900/20
                                        border border-green-200 dark:border-green-800 rounded-lg px-3 py-2.5">
                                <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-xs font-semibold text-green-800 dark:text-green-400">Unidad encontrada</p>
                                    <p class="text-xs text-green-700 dark:text-green-500 mt-0.5"
                                       x-text="[bici.marca, bici.modelo, bici.color, bici.voltaje].filter(Boolean).join(' · ')"></p>
                                </div>
                            </div>

                            {{-- Garantías activas --}}
                            <template x-if="garantiasActivas.length > 0">
                                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200
                                            dark:border-purple-800 rounded-lg px-3 py-2.5">
                                    <p class="text-[10px] font-semibold text-purple-800 dark:text-purple-400
                                               uppercase tracking-wide mb-1.5">Garantías activas</p>
                                    <div class="space-y-1">
                                        <template x-for="g in garantiasActivas" :key="g.clave">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-purple-700 dark:text-purple-300"
                                                      x-text="g.nombre"></span>
                                                <span class="text-[10px] font-mono text-purple-500 dark:text-purple-400"
                                                      x-text="'hasta ' + g.expira_at"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="garantiasActivas.length === 0">
                                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50
                                            border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sin garantías activas</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Bici no encontrada --}}
                    <template x-if="biciNoEncontrada">
                        <div x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             class="space-y-2">
                            <div class="flex items-start gap-2 bg-amber-50 dark:bg-amber-900/20
                                        border border-amber-200 dark:border-amber-700 rounded-lg px-3 py-2.5">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-xs text-amber-800 dark:text-amber-400">
                                    Serie no registrada.
                                    <span class="font-semibold">Describe la unidad.</span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                                    Descripción <span class="text-red-400">*</span>
                                </label>
                                <input type="text" x-model="unidadDescripcion"
                                       placeholder="Bicicleta eléctrica negra 36V"
                                       class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                              px-3.5 py-2 text-sm bg-white dark:bg-gray-700
                                              text-gray-900 dark:text-white
                                              focus:outline-none focus:ring-1 focus:ring-gray-400">
                            </div>
                        </div>
                    </template>


                    {{-- Bici no vendida --}}
                    <template x-if="biciNoVendida">
                        <div x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            class="flex items-start gap-2 bg-red-50 dark:bg-red-900/20
                                    border border-red-200 dark:border-red-700 rounded-lg px-3 py-2.5">
                            <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400 shrink-0 mt-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9
                                        9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <p class="text-xs text-red-800 dark:text-red-400">
                                Esta unidad <span class="font-semibold">no está vendida</span>.
                                Solo se pueden abrir órdenes de trabajo para unidades entregadas a un cliente.
                            </p>
                        </div>
                    </template>

                </div>
            </div>

            {{-- ── Cliente ── --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="px-5 pt-5 pb-4">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Cliente</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Presenta identificación oficial al entregar
                            </p>
                        </div>
                        <template x-if="clienteAutocompletado">
                            <span class="text-[10px] font-medium px-2 py-1 rounded-md
                                         bg-blue-100 dark:bg-blue-800/30 text-blue-800 dark:text-blue-400">
                                Autocompletado
                            </span>
                        </template>
                    </div>
                </div>

                <div class="px-5 pb-5 space-y-3">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" x-model="clienteNombre"
                               placeholder="Nombre completo"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                      px-3.5 py-2 text-sm bg-white dark:bg-gray-700
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Teléfono
                        </label>
                        <input type="tel" x-model="clienteTelefonoDisplay"
                               placeholder="5512345678"
                               :readonly="clienteAutocompletado"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                      px-3.5 py-2 text-sm bg-white dark:bg-gray-700
                                      text-gray-900 dark:text-white font-mono
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Correo
                            <span class="font-normal text-gray-400">(cotización y avisos)</span>
                        </label>
                        <input type="email" x-model="clienteEmail"
                               placeholder="cliente@correo.com"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                      px-3.5 py-2 text-sm bg-white dark:bg-gray-700
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <p x-show="!clienteEmail.trim()"
                           class="text-[11px] text-amber-600 dark:text-amber-400 mt-1.5">
                            ⚠ Sin correo no se enviará la cotización por email
                        </p>
                    </div>

                    {{-- Checkbox ID --}}
                    <label class="flex items-start gap-2.5 cursor-pointer select-none group">
                        <div class="relative mt-0.5 shrink-0">
                            <input type="checkbox" x-model="idVerificada" class="sr-only">
                            <div @click="idVerificada = !idVerificada"
                                 :class="idVerificada
                                     ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white'
                                     : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600'"
                                 class="w-4 h-4 rounded border-2 transition-colors flex items-center
                                        justify-center cursor-pointer">
                                <svg x-show="idVerificada"
                                     class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Identificación verificada</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">
                                El cliente presentó identificación oficial
                            </p>
                        </div>
                    </label>

                </div>
            </div>

        </div>

        {{-- ── Columna derecha — Detalle OT ── --}}
        <div class="lg:col-span-2 flex flex-col">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                        rounded-xl overflow-hidden flex flex-col flex-1">

                <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-4
                            border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Detalle de la orden</p>
                        <p class="text-xs text-gray-400 mt-0.5">Tipo, problema reportado y notas internas</p>
                    </div>
                </div>

                <div class="flex-1 px-5 py-5 space-y-5">

                    {{-- Tipo OT --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                            Tipo de OT
                        </label>
                        <div class="grid grid-cols-3 gap-2">

                            <button type="button" @click="tipo = 'reparacion'"
                                    :class="tipo === 'reparacion'
                                        ? 'border-gray-900 dark:border-gray-300 ring-1 ring-gray-900 dark:ring-gray-300 bg-gray-50 dark:bg-gray-700'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                                    class="border rounded-xl px-3 py-3 text-center transition-all duration-150">
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Reparación</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Falla o daño</p>
                            </button>

                            <button type="button" @click="tipo = 'mantenimiento'"
                                    :class="tipo === 'mantenimiento'
                                        ? 'border-gray-900 dark:border-gray-300 ring-1 ring-gray-900 dark:ring-gray-300 bg-gray-50 dark:bg-gray-700'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'"
                                    class="border rounded-xl px-3 py-3 text-center transition-all duration-150">
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Mantenimiento</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Servicio preventivo</p>
                            </button>

                            <template x-if="idCliente">
                                <a :href="`/sucursal/garantias/bici/${numSerie}`"
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
                                     title="Solo disponible para clientes registrados">
                                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500">Garantía</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Solo registrados</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Costo (solo mantenimiento) --}}
                    <div x-show="tipo === 'mantenimiento'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-end="opacity-0 -translate-y-1">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Costo base del mantenimiento
                            <span class="font-normal text-gray-400">(acuerdo interno)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2 text-sm text-gray-400">$</span>
                            <input type="number" x-model="costoReparacion"
                                   min="0" step="0.01" placeholder="0.00"
                                   class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                          pl-7 pr-4 py-2 text-sm bg-white dark:bg-gray-700
                                          text-gray-900 dark:text-white font-mono
                                          focus:outline-none focus:ring-1 focus:ring-gray-400">
                        </div>
                    </div>

                    {{-- Problema reportado --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Problema reportado <span class="text-red-400">*</span>
                        </label>
                        <textarea x-model="problemaReportado" rows="4"
                                  placeholder="Describe el problema que reporta el cliente..."
                                  class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                         px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700
                                         text-gray-900 dark:text-white
                                         focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
                    </div>

                    {{-- Notas internas --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Notas internas
                            <span class="font-normal text-gray-400">(solo visible para el equipo)</span>
                        </label>
                        <textarea x-model="notasInternas" rows="3"
                                  placeholder="Estado físico al recibir, accesorios incluidos, observaciones..."
                                  class="w-full border border-gray-200 dark:border-gray-600 rounded-xl
                                         px-3.5 py-2.5 text-sm bg-white dark:bg-gray-700
                                         text-gray-900 dark:text-white
                                         focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"></textarea>
                    </div>

                </div>

                {{-- Footer del card --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700
                            bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('reparaciones.index') }}"
                           class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            Cancelar
                        </a>
                        <button @click="crearOt()"
                                :disabled="creando || !puedeCrear()"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5
                                       rounded-xl text-sm font-semibold hover:opacity-90 transition
                                       disabled:opacity-40 disabled:cursor-not-allowed active:scale-[0.98]
                                       flex items-center gap-2 shadow-sm">
                            <svg x-show="creando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg x-show="!creando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span x-text="creando ? 'Creando...' : 'Crear orden de trabajo'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
function repCreate() {
    return {
        // Unidad
        numSerie: '', bici: null, unidadDescripcion: '',
        biciEncontrada: false, biciNoEncontrada: false, biciNoVendida: false, buscando: false,
        garantiasActivas: [],

        // Cliente
        idCliente: null, clienteNombre: '', clienteEmail: '',
        clienteTelefonoDisplay: '', idVerificada: false, clienteAutocompletado: false,

        // OT
        tipo: 'reparacion', costoReparacion: '', problemaReportado: '', notasInternas: '',

        // Modal robo
        modalRobo: false,
        roboDatos: { folio: null, fecha: null },

        // UI
        creando: false,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,
        searchTimer: null,

        init() {},

        debounceSearch() {
            clearTimeout(this.searchTimer);
            if (this.numSerie.trim().length < 5) return;
            this.searchTimer = setTimeout(() => {
                if (!this.biciEncontrada) this.buscarBicicleta();
            }, 600);
        },

        async buscarBicicleta() {
            if (!this.numSerie.trim() || this.buscando) return;
            this.buscando         = true;
            this.biciEncontrada   = false;
            this.biciNoEncontrada = false;
            this.garantiasActivas = [];
            this.limpiarCliente();

            try {
                // ── 1. Verificar reporte de robo ──────────────────────────────
                const roboRes  = await fetch(`/sucursal/reporte/robo/verificar/${encodeURIComponent(this.numSerie.trim())}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const roboData = await roboRes.json();

                if (roboData.reportada) {
                    this.roboDatos = { folio: roboData.folio ?? null, fecha: roboData.fecha ?? null };
                    this.modalRobo = true;
                    this.buscando  = false;
                    return;
                }

                // ── 2. Buscar bicicleta ───────────────────────────────────────
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
                        this.idCliente              = c.id_cliente;
                        this.clienteNombre          = [c.nombre_cliente, c.apellido1, c.apellido2].filter(Boolean).join(' ');
                        this.clienteTelefonoDisplay = c.telefono ?? '';
                        this.clienteEmail           = c.correo ?? '';
                        this.clienteAutocompletado  = true;
                    }
                }  else if (!data.ok && data.error === 'no_vendida') {
                    this.biciNoVendida = true;
                } else {
                    this.biciNoEncontrada = true;
                    if (this.tipo === 'garantia') this.tipo = 'reparacion';
                }

            } catch {
                this.flash('Error al buscar la unidad', 'error');
            } finally {
                this.buscando = false;
            }
        },

        cerrarModalRobo() {
            this.modalRobo = false;
            this.numSerie  = '';
            this.roboDatos = { folio: null, fecha: null };
        },

        limpiarBici() {
            this.numSerie          = '';
            this.bici              = null;
            this.unidadDescripcion = '';
            this.biciEncontrada    = false;
            this.biciNoEncontrada  = false;
            this.biciNoVendida     = false;
            this.garantiasActivas  = [];
            this.limpiarCliente();
            if (this.tipo === 'garantia') this.tipo = 'reparacion';
        },

        limpiarCliente() {
            this.idCliente              = null;
            this.clienteNombre          = '';
            this.clienteTelefonoDisplay = '';
            this.clienteEmail           = '';
            this.idVerificada           = false;
            this.clienteAutocompletado  = false;
        },

        puedeCrear() {
            if (this.biciNoVendida) return false;          // ← AÑADIR
            if (!this.problemaReportado.trim()) return false;
            if (!this.numSerie.trim() && !this.unidadDescripcion.trim()) return false;
            return true;
        },

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
                        num_serie:          this.biciEncontrada ? (this.numSerie.trim() || null) : null,
                        unidad_descripcion: !this.biciEncontrada ? (this.unidadDescripcion.trim() || null) : null,
                        id_cliente:         this.idCliente,
                        cliente_nombre:     this.clienteNombre.trim() || null,
                        cliente_telefono:   this.clienteTelefonoDisplay.trim() || null,
                        cliente_email:      this.clienteEmail.trim() || null,
                        id_verificada:      this.idVerificada,
                        tipo:               this.tipo,
                        costo_reparacion:   ['reparacion', 'mantenimiento'].includes(this.tipo)
                                                ? (parseFloat(this.costoReparacion) || 0) : 0,
                        problema_reportado: this.problemaReportado.trim(),
                        notas_internas:     this.notasInternas.trim() || null,
                    }),
                });
                const data = await res.json();

                if (!data.ok) { this.flash(data.mensaje ?? 'Error al crear la OT', 'error'); return; }

                this.flash(data.mensaje);
                setTimeout(() => { window.location.href = '{{ route("reparaciones.index") }}'; }, 1200);

            } catch {
                this.flash('Error de conexión', 'error');
            } finally {
                this.creando = false;
            }
        },

        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
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