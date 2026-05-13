<x-app-layout>
    <div x-data="metodosPagoManager()" x-init="init()" class="max-w-2xl mx-auto space-y-6">

        {{-- ══ NOTIFICACIÓN FLOTANTE ══ --}}
        <div x-show="notif.msg" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
            style="min-width:300px;max-width:400px;">
            <div class="flex items-start gap-3 rounded-xl px-4 py-3 border shadow-sm" :class="{
                 'bg-green-50  dark:bg-green-950/40  border-green-100  dark:border-green-900/50':  notif.tipo === 'ok',
                 'bg-yellow-50 dark:bg-yellow-950/40 border-yellow-100 dark:border-yellow-900/50': notif.tipo === 'warning',
                 'bg-red-50    dark:bg-red-950/40    border-red-100    dark:border-red-900/50':    notif.tipo === 'error',
             }">
                <template x-if="notif.tipo === 'ok'">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </template>
                <template x-if="notif.tipo === 'warning'">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </template>
                <template x-if="notif.tipo === 'error'">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </template>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold leading-snug" :class="{
                       'text-green-800  dark:text-green-300':  notif.tipo === 'ok',
                       'text-yellow-800 dark:text-yellow-300': notif.tipo === 'warning',
                       'text-red-800    dark:text-red-300':    notif.tipo === 'error',
                   }" x-text="notif.msg"></p>
                    <p x-show="notif.sub" class="text-xs font-normal opacity-75 mt-0.5" :class="{
                       'text-green-700 dark:text-green-400':   notif.tipo === 'ok',
                       'text-yellow-700 dark:text-yellow-400': notif.tipo === 'warning',
                       'text-red-700   dark:text-red-400':     notif.tipo === 'error',
                   }" x-text="notif.sub"></p>
                </div>
            </div>
        </div>

        {{-- ══ MODAL CONFIRMACIÓN TOGGLE ══ --}}
        <template x-teleport="body">
            <div x-show="confirm.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-end="opacity-0">

                <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="cerrarConfirm()">
                </div>

                <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.stop>

                    <div class="flex flex-col items-center px-6 pt-8 pb-5 text-center">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4" :class="confirm.accion === 'desactivar'
                             ? 'bg-yellow-50 dark:bg-yellow-900/30'
                             : 'bg-green-50  dark:bg-green-900/30'">
                            <template x-if="confirm.accion === 'desactivar'">
                                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </template>
                            <template x-if="confirm.accion === 'activar'">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="confirm.titulo"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed"
                            x-text="confirm.cuerpo"></p>
                    </div>

                    <div class="flex items-center gap-2 px-6 pb-6">
                        <button type="button" @click="cerrarConfirm()" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium
                                   border border-gray-200 dark:border-gray-600
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button type="button" @click="ejecutarConfirm()"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold transition active:scale-[.98]"
                            :class="confirm.accion === 'desactivar'
                                ? 'bg-yellow-500 hover:bg-yellow-600 text-white'
                                : 'bg-green-600  hover:bg-green-700  text-white'"
                            x-text="confirm.accion === 'desactivar' ? 'Desactivar' : 'Activar'">
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ══ MODAL FORMULARIO (crear / editar) ══ --}}
        <template x-teleport="body">
            <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-end="opacity-0">

                <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="cerrarModal()">
                </div>

                <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.stop>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 border-b dark:border-gray-700">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white"
                                x-text="modal.modo === 'crear' ? 'Nuevo método de pago' : 'Editar método de pago'"></h3>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="modal.modo === 'crear'
                               ? 'Elige el tipo y configura el nombre.'
                               : 'Actualiza el nombre o las propiedades.'"></p>
                        </div>
                        <button @click="cerrarModal()" class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-5">

                        {{-- Selector de tipo — exclusivo --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                Tipo de método <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2">

                                {{-- Efectivo --}}
                                <button type="button" @click="seleccionarTipo('efectivo')"
                                    :disabled="modal.modo === 'crear' && yaHayEfectivo" class="relative flex flex-col items-center gap-2 p-3.5 rounded-xl border-2 transition text-center
                                           disabled:opacity-40 disabled:cursor-not-allowed"
                                    :class="modal.tipo === 'efectivo'
                                        ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'">
                                    <svg class="w-6 h-6 transition"
                                        :class="modal.tipo === 'efectivo' ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="1.6" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.6" />
                                        <path d="M6 9v0m12 6v0" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    <span class="text-xs font-semibold leading-tight"
                                        :class="modal.tipo === 'efectivo' ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'">
                                        Efectivo
                                    </span>
                                    <template x-if="modal.modo === 'crear' && yaHayEfectivo">
                                        <span
                                            class="absolute -top-1.5 -right-1.5 text-[9px] font-bold px-1 py-0.5 rounded-full
                                                 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 leading-tight">
                                            1 máx
                                        </span>
                                    </template>
                                </button>

                                {{-- Transferencia --}}
                                <button type="button" @click="seleccionarTipo('transferencia')"
                                    class="flex flex-col items-center gap-2 p-3.5 rounded-xl border-2 transition text-center"
                                    :class="modal.tipo === 'transferencia'
                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'">
                                    <svg class="w-6 h-6 transition"
                                        :class="modal.tipo === 'transferencia' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    <span class="text-xs font-semibold leading-tight"
                                        :class="modal.tipo === 'transferencia' ? 'text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400'">
                                        Transferencia
                                    </span>
                                </button>

                                {{-- Tarjeta --}}
                                <button type="button" @click="seleccionarTipo('tarjeta')"
                                    class="flex flex-col items-center gap-2 p-3.5 rounded-xl border-2 transition text-center"
                                    :class="modal.tipo === 'tarjeta'
                                        ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-800'">
                                    <svg class="w-6 h-6 transition"
                                        :class="modal.tipo === 'tarjeta' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.6" />
                                        <path d="M2 10h20" stroke-width="1.6" />
                                        <rect x="5" y="14" width="4" height="2" rx="0.5" stroke-width="1.4" />
                                    </svg>
                                    <span class="text-xs font-semibold leading-tight"
                                        :class="modal.tipo === 'tarjeta' ? 'text-purple-700 dark:text-purple-400' : 'text-gray-600 dark:text-gray-400'">
                                        Tarjeta
                                    </span>
                                </button>
                            </div>

                            {{-- Descripción dinámica del tipo seleccionado --}}
                            <p class="text-xs text-gray-400 mt-2 min-h-[1rem]" x-text="modal.tipo === 'efectivo'
                               ? 'Activa el campo de monto recibido y cálculo de cambio automático.'
                               : modal.tipo === 'transferencia'
                                   ? 'Solicita folio o referencia al cobrar (SPEI, depósito, etc.).'
                                   : modal.tipo === 'tarjeta'
                                       ? 'Pago con tarjeta débito o crédito, sin referencia obligatoria.'
                                       : ''">
                            </p>

                            {{-- Aviso efectivo ya existe --}}
                            <template x-if="modal.modo === 'crear' && yaHayEfectivo">
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Solo puede haber un método de efectivo activo.
                                </p>
                            </template>
                        </div>

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                                Nombre <span class="text-red-400">*</span>
                            </label>
                            <input type="text" x-model="modal.nombre" @keyup.enter="guardar()"
                                placeholder="Ej: Efectivo, BBVA SPEI, Tarjeta débito…" class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                      px-4 py-2.5 text-sm bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-gray-400
                                      placeholder:text-gray-300 dark:placeholder:text-gray-600 transition">
                            <template x-if="modal.errores.nombre">
                                <p class="text-xs text-red-400 mt-1" x-text="modal.errores.nombre"></p>
                            </template>
                        </div>

                        {{-- Requiere referencia (solo visible en tarjeta; en transferencia es automático) --}}
                        <template x-if="modal.tipo === 'tarjeta'">
                            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border transition"
                                :class="modal.requiereRef
                                   ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/50'
                                   : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'"
                                @click="modal.requiereRef = !modal.requiereRef">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center shrink-0 mt-0.5 transition"
                                    :class="modal.requiereRef
                                     ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white'
                                     : 'border-gray-300 dark:border-gray-600'">
                                    <svg x-show="modal.requiereRef" class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">Requiere referencia</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Pide últimos 4 dígitos u otro texto al
                                        cobrar.</p>
                                </div>
                            </label>
                        </template>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4
                            bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700">
                        <button type="button" @click="cerrarModal()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300
                                   hover:text-gray-900 dark:hover:text-white transition">
                            Cancelar
                        </button>
                        <button type="button" @click="guardar()"
                            :disabled="guardando || !modal.nombre.trim() || !modal.tipo" class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold
                                   bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                   hover:opacity-90 active:scale-[.98] transition
                                   disabled:opacity-30 disabled:cursor-not-allowed">
                            <template x-if="guardando">
                                <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                            </template>
                            <span
                                x-text="guardando ? 'Guardando…' : (modal.modo === 'crear' ? 'Crear método' : 'Guardar cambios')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        {{-- ══ HEADER ══ --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Métodos de pago</h2>
                <p class="text-xs text-gray-400 mt-0.5">Configura cómo aceptas pagos en tus sucursales.</p>
            </div>
            <button @click="abrirCrear()" class="flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900
                       text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo método
            </button>
        </div>

        {{-- ══ LISTA ══ --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Métodos configurados</h3>
                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full"
                    x-text="metodos.length + ' total'"></span>
            </div>

            {{-- Empty state --}}
            <template x-if="metodos.length === 0">
                <div class="px-5 py-16 text-center">
                    <div
                        class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="1.5" />
                            <circle cx="12" cy="12" r="3" stroke-width="1.5" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No hay métodos configurados</p>
                    <p class="text-xs text-gray-400 mt-1">Crea el primer método con el botón de arriba.</p>
                </div>
            </template>

            <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                <template x-for="m in metodos" :key="m.id_metodo">
                    <div
                        class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">

                        {{-- Ícono por tipo --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition" :class="{
                             'bg-green-100  dark:bg-green-900/30':   m.es_efectivo && m.activo,
                             'bg-blue-100   dark:bg-blue-900/30':    !m.es_efectivo && m.requiere_referencia && m.activo,
                             'bg-purple-100 dark:bg-purple-900/30':  !m.es_efectivo && !m.requiere_referencia && m.activo,
                             'bg-gray-100   dark:bg-gray-700':       !m.activo,
                         }">

                            {{-- Efectivo: billete --}}
                            <template x-if="m.es_efectivo">
                                <svg class="w-5 h-5"
                                    :class="m.activo ? 'text-green-600 dark:text-green-400' : 'text-gray-400'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="1.6" />
                                    <circle cx="12" cy="12" r="3" stroke-width="1.6" />
                                    <path d="M6 9v0m12 6v0" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </template>

                            {{-- Transferencia: flechas arriba/abajo --}}
                            <template x-if="!m.es_efectivo && m.requiere_referencia">
                                <svg class="w-5 h-5"
                                    :class="m.activo ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                        d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </template>

                            {{-- Tarjeta: chip --}}
                            <template x-if="!m.es_efectivo && !m.requiere_referencia">
                                <svg class="w-5 h-5"
                                    :class="m.activo ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.6" />
                                    <path d="M2 10h20" stroke-width="1.6" />
                                    <rect x="5" y="14" width="4" height="2" rx="0.5" stroke-width="1.4" />
                                </svg>
                            </template>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white truncate"
                                    x-text="m.nombre" :class="!m.activo && 'line-through text-gray-400'"></p>
                                <template x-if="m.es_efectivo">
                                    <span
                                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 uppercase tracking-wide">
                                        Efectivo
                                    </span>
                                </template>
                                <template x-if="!m.es_efectivo && m.requiere_referencia">
                                    <span
                                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 uppercase tracking-wide">
                                        Transferencia
                                    </span>
                                </template>
                                <template x-if="!m.es_efectivo && !m.requiere_referencia">
                                    <span
                                        class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                                        Tarjeta
                                    </span>
                                </template>
                                <template x-if="!m.activo">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-gray-100 dark:bg-gray-700 text-gray-400 uppercase tracking-wide">
                                        Inactivo
                                    </span>
                                </template>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="m.es_efectivo
                               ? 'Calcula cambio automáticamente'
                               : m.requiere_referencia
                                   ? 'Solicita folio o referencia al cobrar'
                                   : 'Sin referencia requerida'">
                            </p>
                        </div>

                        {{-- Acciones hover --}}
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
                            <button @click="abrirEditar(m)" class="w-8 h-8 flex items-center justify-center rounded-lg
                                       text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Editar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="eliminarMetodo(m)"
        class="w-8 h-8 flex items-center justify-center rounded-lg
               text-gray-300 hover:text-red-500 dark:hover:text-red-400
               hover:bg-red-50 dark:hover:bg-red-900/20 transition"
        title="Eliminar">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"/>
    </svg>
</button>
                            <button @click="pedirConfirmToggle(m)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition"
                                :class="m.activo
                                    ? 'text-gray-300 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20'
                                    : 'text-gray-300 hover:text-green-500 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'"
                                :title="m.activo ? 'Desactivar' : 'Activar'">
                                <template x-if="m.activo">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </template>
                                <template x-if="!m.activo">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <script>
        // ── Helpers ───────────────────────────────────────────────────
        // Infiere tipo visual desde los flags del modelo
        function inferirTipo(m) {
            if (m.es_efectivo) return 'efectivo';
            if (m.requiere_referencia) return 'transferencia';
            return 'tarjeta';
        }

        // Convierte tipo visual → flags booleanos para el payload
        function tipoAFlags(tipo) {
            return {
                es_efectivo: tipo === 'efectivo' ? '1' : '0',
                requiere_referencia: tipo === 'transferencia' ? '1' : '0',
            };
        }

        function metodosPagoManager() {
            return {
                // ── Estado ────────────────────────────────────────────────────
                metodos: @json($metodos),

                guardando: false,
                notif: { msg: '', sub: '', tipo: 'ok', _t: null },

                modal: {
                    open: false, modo: 'crear', id: null,
                    nombre: '', tipo: null, requiereRef: false,
                    errores: {},
                },

                confirm: {
                    open: false, accion: 'desactivar',
                    titulo: '', cuerpo: '', _m: null,
                },

                // ── Computed ──────────────────────────────────────────────────
                get yaHayEfectivo() {
                    // Si estamos editando el único efectivo existente, no lo bloqueamos
                    if (this.modal.modo === 'editar' && this.modal.tipo === 'efectivo') return false;
                    return this.metodos.some(m =>
                        m.es_efectivo && m.activo && m.id_metodo !== this.modal.id
                    );
                },

                init() { },

                // ── Modal formulario ──────────────────────────────────────────
                abrirCrear() {
                    const hayEfectivo = this.metodos.some(m => m.es_efectivo && m.activo);
                    this.modal = {
                        open: true, modo: 'crear', id: null,
                        nombre: '',
                        tipo: hayEfectivo ? 'tarjeta' : 'efectivo',
                        requiereRef: false,
                        errores: {},
                    };
                },

                abrirEditar(m) {
                    this.modal = {
                        open: true,
                        modo: 'editar',
                        id: m.id_metodo,
                        nombre: m.nombre,
                        tipo: inferirTipo(m),
                        requiereRef: m.requiere_referencia,
                        errores: {},
                    };
                },

                cerrarModal() { this.modal.open = false; },

                seleccionarTipo(tipo) {
                    // Bloquea efectivo solo en creación cuando ya hay uno
                    if (tipo === 'efectivo' && this.modal.modo === 'crear' && this.metodos.some(m => m.es_efectivo && m.activo)) return;
                    this.modal.tipo = tipo;
                    // Sincroniza requiereRef según tipo
                    if (tipo === 'transferencia') this.modal.requiereRef = true;
                    if (tipo === 'tarjeta') this.modal.requiereRef = false;
                    if (tipo === 'efectivo') this.modal.requiereRef = false;
                },

                // ── Modal confirmación ────────────────────────────────────────
                pedirConfirmToggle(m) {
                    const desactivar = m.activo;
                    this.confirm = {
                        open: true,
                        accion: desactivar ? 'desactivar' : 'activar',
                        titulo: desactivar ? `¿Desactivar "${m.nombre}"?` : `¿Activar "${m.nombre}"?`,
                        cuerpo: desactivar
                            ? 'Este método no estará disponible en el punto de venta.'
                            : 'Volverá a estar disponible para usar en ventas.',
                        _m: m,
                    };
                },
                cerrarConfirm() { this.confirm.open = false; },
                ejecutarConfirm() {
                    if (this.confirm._m) this.toggleMetodo(this.confirm._m);
                    this.cerrarConfirm();
                },

                // ── CRUD ──────────────────────────────────────────────────────
                async guardar() {
                    this.modal.errores = {};

                    if (!this.modal.nombre.trim()) {
                        this.modal.errores.nombre = 'El nombre es obligatorio.';
                        return;
                    }

                    this.guardando = true;

                    const esCrear = this.modal.modo === 'crear';
                    const nombreGuardado = this.modal.nombre.trim();
                    const flags = tipoAFlags(this.modal.tipo);

                    if (this.modal.tipo === 'tarjeta') {
                        flags.requiere_referencia = this.modal.requiereRef ? '1' : '0';
                    }

                    try {

                        const url = esCrear
                            ? '{{ route("admin.metodos_pago.store") }}'
                            : '{{ url("admin/metodos-pago") }}/' + this.modal.id;

                        const body = new URLSearchParams();

                        body.append('_token', '{{ csrf_token() }}');

                        if (!esCrear) {
                            body.append('_method', 'PUT');
                        }

                        body.append('nombre', nombreGuardado);
                        body.append('es_efectivo', flags.es_efectivo);
                        body.append('requiere_referencia', flags.requiere_referencia);

                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body,
                        });

                        // 👇 LEEMOS COMO TEXTO PRIMERO
                        const text = await res.text();

                        console.group('🔥 RESPUESTA DEL SERVIDOR');
                        console.log('STATUS:', res.status);
                        console.log('URL:', url);
                        console.log('RAW RESPONSE:');
                        console.log(text);
                        console.groupEnd();

                        // 👇 intentamos convertir a JSON
                        let data = {};

                        try {
                            data = JSON.parse(text);
                        } catch (e) {

                            console.error('❌ La respuesta NO es JSON');

                            this.mostrarNotif(
                                'Laravel devolvió HTML en vez de JSON.',
                                'error',
                                'Revisa la consola.'
                            );

                            return;
                        }

                        if (!res.ok) {

                            if (data.errors) {

                                Object.entries(data.errors).forEach(([k, v]) => {
                                    this.modal.errores[k] = v[0];
                                });

                            } else {

                                this.mostrarNotif(
                                    data.message || 'Error al guardar.',
                                    'error'
                                );
                            }

                            return;
                        }

                        if (esCrear) {

                            this.metodos.push(data.metodo);

                        } else {

                            const idx = this.metodos.findIndex(
                                m => m.id_metodo === this.modal.id
                            );

                            if (idx >= 0) {
                                this.metodos[idx] = data.metodo;
                            }
                        }

                        this.cerrarModal();

                        this.mostrarNotif(
                            esCrear
                                ? `Método "${nombreGuardado}" creado.`
                                : `Método "${nombreGuardado}" actualizado.`,
                            'ok',
                            esCrear
                                ? 'Ya disponible en el punto de venta.'
                                : ''
                        );

                    } catch (error) {

                        console.group('🔥 CATCH ERROR');

                        console.error('Error completo:', error);
                        console.error('Mensaje:', error?.message);
                        console.error('Stack:', error?.stack);

                        console.groupEnd();

                        this.mostrarNotif(
                            error?.message || 'Error desconocido.',
                            'error',
                            'Revisa la consola.'
                        );

                    } finally {

                        this.guardando = false;
                    }
                },

                async toggleMetodo(m) {
                    const estabaActivo = m.activo;
                    try {
                        const res = await fetch(`{{ url('admin/metodos-pago') }}/${m.id_metodo}`, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json' },
                            body: new URLSearchParams({
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE',
                            }),
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            this.mostrarNotif(data.message || 'Error al cambiar estado.', 'error');
                            return;
                        }

                        const item = this.metodos.find(x => x.id_metodo === m.id_metodo);
                        if (item) item.activo = !item.activo;

                        this.mostrarNotif(
                            estabaActivo ? `"${m.nombre}" desactivado.` : `"${m.nombre}" activado.`,
                            estabaActivo ? 'warning' : 'ok',
                            estabaActivo
                                ? 'Ya no aparecerá en el punto de venta.'
                                : 'Disponible en el punto de venta.'
                        );

                    } catch (error) {

                        console.group('🔥 CATCH ERROR');

                        console.error('Error completo:', error);

                        console.error('Mensaje:', error?.message);

                        console.error('Stack:', error?.stack);

                        console.groupEnd();

                        this.mostrarNotif(
                            error?.message || 'Error desconocido.',
                            'error',
                            'Revisa la consola.'
                        );
                    }
                },

                async eliminarMetodo(m) {

    if (!confirm(`¿Eliminar "${m.nombre}"?`)) {
        return;
    }

    try {

        const res = await fetch(
            `{{ url('admin/metodos-pago') }}/${m.id_metodo}`,
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE',
                    eliminar: '1',
                }),
            }
        );

        const data = await res.json();

        if (!res.ok) {

            this.mostrarNotif(
                data.message || 'Error al eliminar.',
                'error'
            );

            return;
        }

        this.metodos = this.metodos.filter(
            x => x.id_metodo !== m.id_metodo
        );

        this.mostrarNotif(
            `"${m.nombre}" eliminado.`,
            'warning'
        );

    } catch (error) {

        console.error(error);

        this.mostrarNotif(
            'Error de conexión.',
            'error'
        );
    }
},

                mostrarNotif(msg, tipo = 'ok', sub = '') {
                    if (this.notif._t) clearTimeout(this.notif._t);
                    this.notif = { msg, sub, tipo, _t: setTimeout(() => { this.notif.msg = ''; }, 3500) };
                },
            };
        }
    </script>
</x-app-layout>