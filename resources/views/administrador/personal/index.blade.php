<x-app-layout>
<div
    x-data="personalManager()"
    x-init="init()"
    class="space-y-6 max-w-4xl mx-auto"
>

    {{-- ══ NOTIFICATION CARD ══ --}}
    <div
        x-show="notif.msg"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
        class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
        style="min-width:300px;max-width:400px;"
    >
        <div class="flex items-start gap-3 rounded-xl px-4 py-3 border shadow-sm"
             :class="{
                 'bg-green-50  dark:bg-green-950/40  border-green-100  dark:border-green-900/50':  notif.tipo === 'ok',
                 'bg-yellow-50 dark:bg-yellow-950/40 border-yellow-100 dark:border-yellow-900/50': notif.tipo === 'warning',
                 'bg-red-50    dark:bg-red-950/40    border-red-100    dark:border-red-900/50':    notif.tipo === 'error',
                 'bg-blue-50   dark:bg-blue-950/40   border-blue-100   dark:border-blue-900/50':   notif.tipo === 'info',
             }">
            <template x-if="notif.tipo === 'ok'">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </template>
            <template x-if="notif.tipo === 'warning'">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-yellow-500 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </template>
            <template x-if="notif.tipo === 'error'">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </template>
            <template x-if="notif.tipo === 'info'">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </template>

            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold leading-snug"
                   :class="{
                       'text-green-800  dark:text-green-300':  notif.tipo === 'ok',
                       'text-yellow-800 dark:text-yellow-300': notif.tipo === 'warning',
                       'text-red-800    dark:text-red-300':    notif.tipo === 'error',
                       'text-blue-800   dark:text-blue-300':   notif.tipo === 'info',
                   }"
                   x-text="notif.msg"></p>
                <p x-show="notif.sub"
                   class="text-xs font-normal opacity-75 mt-0.5 leading-snug"
                   :class="{
                       'text-green-700  dark:text-green-400':  notif.tipo === 'ok',
                       'text-yellow-700 dark:text-yellow-400': notif.tipo === 'warning',
                       'text-red-700    dark:text-red-400':    notif.tipo === 'error',
                       'text-blue-700   dark:text-blue-400':   notif.tipo === 'info',
                   }"
                   x-text="notif.sub"></p>
            </div>
        </div>
    </div>

    {{-- ══ MODAL FORMULARIO (crear / editar) ══ --}}
    <template x-teleport="body">
        <div x-show="modal.open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
                 @click="cerrarModal()"></div>

            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 @click.stop>

                <div class="flex items-center justify-between px-6 py-5 border-b dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white"
                            x-text="modal.modo === 'crear' ? 'Nuevo vendedor' : 'Editar vendedor'"></h3>
                        <p class="text-xs text-gray-400 mt-0.5"
                           x-text="modal.modo === 'crear'
                               ? 'Asigna el vendedor a una o más sucursales'
                               : 'Actualiza el nombre o las sucursales asignadas'"></p>
                    </div>
                    <button @click="cerrarModal()"
                            class="w-8 h-8 flex items-center justify-center rounded-lg
                                   text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                   hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5">
                    {{-- Nombre --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Nombre completo <span class="text-red-400">*</span>
                        </label>
                        <input type="text"
                               x-model="modal.nombre"
                               @keyup.enter="guardar()"
                               placeholder="Ej: Carlos Hernández"
                               class="w-full rounded-xl border border-gray-300 dark:border-gray-600
                                      px-4 py-2.5 text-sm bg-white dark:bg-gray-900
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-gray-400
                                      placeholder:text-gray-300 dark:placeholder:text-gray-600 transition">
                        <template x-if="modal.errores.nombre">
                            <p class="text-xs text-red-400 mt-1" x-text="modal.errores.nombre"></p>
                        </template>
                    </div>

                    {{-- Sucursales --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                            Sucursales asignadas <span class="text-red-400">*</span>
                        </label>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            @foreach($sucursales as $s)
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                                   :class="modal.sucursales.includes('{{ $s->id_usuario }}')
                                       ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/50'
                                       : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="checkbox"
                                       value="{{ $s->id_usuario }}"
                                       :checked="modal.sucursales.includes('{{ $s->id_usuario }}')"
                                       @change="toggleSucursal('{{ $s->id_usuario }}')"
                                       class="sr-only">
                                <div class="w-4 h-4 rounded border-2 flex items-center justify-center shrink-0 transition"
                                     :class="modal.sucursales.includes('{{ $s->id_usuario }}')
                                         ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white'
                                         : 'border-gray-300 dark:border-gray-600'">
                                    <svg x-show="modal.sucursales.includes('{{ $s->id_usuario }}')"
                                         class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $s->nombre_usuario }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $s->correo }}</p>
                                </div>
                                <template x-if="conteoPersonalPorSucursal['{{ $s->id_usuario }}']">
                                    <span class="shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full
                                                 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400"
                                          x-text="conteoPersonalPorSucursal['{{ $s->id_usuario }}'] + ' vendedor(es)'"></span>
                                </template>
                            </label>
                            @endforeach
                        </div>
                        <template x-if="modal.errores.sucursales">
                            <p class="text-xs text-red-400 mt-1.5" x-text="modal.errores.sucursales"></p>
                        </template>
                    </div>

                    {{-- Activo (solo edición) --}}
                    <template x-if="modal.modo === 'editar'">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative w-9 h-5 shrink-0">
                                <input type="checkbox" x-model="modal.activo" class="sr-only peer">
                                <div class="w-9 h-5 rounded-full transition peer-checked:bg-gray-900 dark:peer-checked:bg-white
                                            bg-gray-200 dark:bg-gray-600 cursor-pointer"
                                     @click="modal.activo = !modal.activo"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white dark:bg-gray-900
                                            shadow transition-transform pointer-events-none"
                                     :class="modal.activo ? 'translate-x-4' : 'translate-x-0'"></div>
                            </div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Vendedor activo</span>
                        </label>
                    </template>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4
                            bg-gray-50 dark:bg-gray-700/30 border-t dark:border-gray-700">
                    <button type="button" @click="cerrarModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300
                                   hover:text-gray-900 dark:hover:text-white transition">
                        Cancelar
                    </button>
                    <button type="button" @click="guardar()"
                            :disabled="guardando || !modal.nombre.trim() || modal.sucursales.length === 0"
                            class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold
                                   bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                   hover:opacity-90 active:scale-[.98] transition
                                   disabled:opacity-30 disabled:cursor-not-allowed">
                        <template x-if="guardando">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </template>
                        <span x-text="guardando ? 'Guardando…' : (modal.modo === 'crear' ? 'Crear vendedor' : 'Guardar cambios')"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ MODAL DE CONFIRMACIÓN (toggle activo/inactivo) ══ --}}
    <template x-teleport="body">
        <div x-show="confirm.open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0">

            <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
                 @click="cerrarConfirm()"></div>

            <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                        ring-1 ring-black/5 dark:ring-white/10 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 @click.stop>

                <div class="flex flex-col items-center px-6 pt-8 pb-5 text-center">
                    {{-- Icono dinámico según acción --}}
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4"
                         :class="confirm.accion === 'desactivar'
                             ? 'bg-yellow-50 dark:bg-yellow-900/30'
                             : 'bg-green-50  dark:bg-green-900/30'">
                        <template x-if="confirm.accion === 'desactivar'">
                            <svg class="w-6 h-6 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </template>
                        <template x-if="confirm.accion === 'activar'">
                            <svg class="w-6 h-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                    </div>

                    <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="confirm.titulo"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed" x-text="confirm.cuerpo"></p>
                </div>

                <div class="flex items-center gap-2 px-6 pb-6">
                    <button type="button" @click="cerrarConfirm()"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium
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

    {{-- ══ HEADER ══ --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personal</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los vendedores de tus sucursales.</p>
        </div>
        <button @click="abrirCrear()"
                class="flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900
                       text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo vendedor
        </button>
    </div>

    {{-- ══ FILTRO POR SUCURSAL ══ --}}
    @if($sucursales->count() > 1)
    <div class="flex items-center gap-2 flex-wrap">
        <button @click="filtroSucursal = ''"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                :class="filtroSucursal === ''
                    ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'">
            Todas las sucursales
        </button>
        @foreach($sucursales as $s)
        <button @click="filtroSucursal = '{{ $s->id_usuario }}'"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                :class="filtroSucursal === '{{ $s->id_usuario }}'
                    ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'">
            {{ $s->nombre_usuario }}
        </button>
        @endforeach
    </div>
    @endif

    {{-- ══ TABLA DE PERSONAL ══ --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Vendedores registrados</h3>
            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full"
                  x-text="personalFiltrado.length + ' resultado(s)'"></span>
        </div>

        {{-- Empty state --}}
        <template x-if="personalFiltrado.length === 0">
            <div class="px-6 py-16 text-center">
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No hay vendedores</p>
                <p class="text-xs text-gray-400 mt-1">
                    <template x-if="filtroSucursal !== ''">
                        <span>No hay personal en esta sucursal.</span>
                    </template>
                    <template x-if="filtroSucursal === ''">
                        <span>Crea el primer vendedor con el botón de arriba.</span>
                    </template>
                </p>
            </div>
        </template>

        <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
            <template x-for="p in personalFiltrado" :key="p.id_personal">
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">

                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm"
                         :class="p.activo
                             ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                             : 'bg-gray-200 dark:bg-gray-700 text-gray-400'">
                        <span x-text="p.nombre.charAt(0).toUpperCase()"></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate"
                               x-text="p.nombre"
                               :class="!p.activo && 'line-through text-gray-400'"></p>
                            <template x-if="!p.activo">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full
                                             bg-gray-100 dark:bg-gray-700 text-gray-400 uppercase tracking-wide">
                                    Inactivo
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                            <template x-for="suc in p.sucursales" :key="suc.id_usuario">
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-600 dark:text-blue-400
                                             border border-blue-200 dark:border-blue-800"
                                      x-text="suc.nombre_usuario"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
                        <button @click="abrirEditar(p)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg
                                       text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                title="Editar">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="pedirConfirmToggle(p)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition"
                                :class="p.activo
                                    ? 'text-gray-300 hover:text-yellow-500 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20'
                                    : 'text-gray-300 hover:text-green-500 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'"
                                :title="p.activo ? 'Desactivar' : 'Activar'">
                            <template x-if="p.activo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            </template>
                            <template x-if="!p.activo">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
function personalManager() {
    return {
        personal:              @json($personal),
        sucursalesDisponibles: @json($sucursalesJs),

        filtroSucursal: '',
        guardando:      false,

        notif:   { msg: '', sub: '', tipo: 'ok', _t: null },

        // Modal formulario
        modal: {
            open: false, modo: 'crear', id: null,
            nombre: '', sucursales: [], activo: true, errores: {},
        },

        // Modal confirmación toggle
        confirm: {
            open: false, accion: 'desactivar',
            titulo: '', cuerpo: '', _p: null,
        },

        // ── Computed ──────────────────────────────────────────────────
        get conteoPersonalPorSucursal() {
            const conteo = {};
            this.personal.forEach(p => {
                if (!p.activo) return;
                p.sucursales.forEach(s => {
                    conteo[s.id_usuario] = (conteo[s.id_usuario] || 0) + 1;
                });
            });
            return conteo;
        },

        get personalFiltrado() {
            if (!this.filtroSucursal) return this.personal;
            return this.personal.filter(p =>
                p.sucursales.some(s => s.id_usuario === this.filtroSucursal)
            );
        },

        init() {},

        // ── Modal formulario ──────────────────────────────────────────
        abrirCrear() {
            this.modal = {
                open: true, modo: 'crear', id: null,
                nombre: '', sucursales: [], activo: true, errores: {},
            };
        },
        abrirEditar(p) {
            this.modal = {
                open:       true,
                modo:       'editar',
                id:         p.id_personal,
                nombre:     p.nombre,
                sucursales: p.sucursales.map(s => s.id_usuario),
                activo:     p.activo,
                errores:    {},
            };
        },
        cerrarModal() { this.modal.open = false; },
        toggleSucursal(id) {
            const idx = this.modal.sucursales.indexOf(id);
            if (idx >= 0) this.modal.sucursales.splice(idx, 1);
            else          this.modal.sucursales.push(id);
        },

        // ── Modal confirmación ────────────────────────────────────────
        pedirConfirmToggle(p) {
            const desactivar = p.activo;
            this.confirm = {
                open:   true,
                accion: desactivar ? 'desactivar' : 'activar',
                titulo: desactivar ? `¿Desactivar a ${p.nombre}?` : `¿Reactivar a ${p.nombre}?`,
                cuerpo: desactivar
                    ? 'No podrá ser asignado a nuevas ventas mientras esté inactivo.'
                    : 'Volverá a estar disponible para asignar en ventas.',
                _p: p,
            };
        },
        cerrarConfirm() { this.confirm.open = false; },
        ejecutarConfirm() {
            if (this.confirm._p) this.toggleVendedor(this.confirm._p);
            this.cerrarConfirm();
        },

        // ── CRUD ──────────────────────────────────────────────────────
        async guardar() {
            this.modal.errores = {};
            if (!this.modal.nombre.trim()) {
                this.modal.errores.nombre = 'El nombre es obligatorio.'; return;
            }
            if (this.modal.sucursales.length === 0) {
                this.modal.errores.sucursales = 'Selecciona al menos una sucursal.'; return;
            }

            this.guardando = true;
            const esCrear        = this.modal.modo === 'crear';
            const nombreGuardado = this.modal.nombre.trim();

            try {
                const url  = esCrear
                    ? '{{ route("admin.personal.store") }}'
                    : '{{ url("admin/personal") }}/' + this.modal.id;

                const body = new URLSearchParams();
                body.append('_token', '{{ csrf_token() }}');
                if (!esCrear) body.append('_method', 'PUT');
                body.append('nombre', nombreGuardado);
                body.append('activo', this.modal.activo ? '1' : '0');
                this.modal.sucursales.forEach(id => body.append('sucursales[]', id));

                const res  = await fetch(url, {
                    method: 'POST', headers: { 'Accept': 'application/json' }, body,
                });
                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => {
                            this.modal.errores[k] = v[0];
                        });
                    } else {
                        this.mostrarNotif(data.message || 'Error al guardar.', 'error');
                    }
                    return;
                }

                if (esCrear) {
                    this.personal.push(data.personal);
                } else {
                    const idx = this.personal.findIndex(p => p.id_personal === this.modal.id);
                    if (idx >= 0) this.personal[idx] = data.personal;
                }

                this.cerrarModal();
                this.mostrarNotif(
                    esCrear
                        ? `Vendedor "${nombreGuardado}" creado correctamente.`
                        : `Datos de "${nombreGuardado}" actualizados.`,
                    'ok',
                    esCrear ? 'Ya está disponible para asignar en ventas.' : ''
                );

            } catch {
                this.mostrarNotif('Error de conexión.', 'error', 'Revisa tu conexión e intenta de nuevo.');
            } finally {
                this.guardando = false;
            }
        },

        async toggleVendedor(p) {
            const estabaActivo = p.activo;
            try {
                const res  = await fetch(`{{ url('admin/personal') }}/${p.id_personal}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new URLSearchParams({
                        _token: '{{ csrf_token() }}', _method: 'DELETE',
                    }),
                });
                const data = await res.json();
                if (!res.ok) { this.mostrarNotif(data.message || 'Error.', 'error'); return; }

                const item = this.personal.find(x => x.id_personal === p.id_personal);
                if (item) item.activo = !item.activo;

                this.mostrarNotif(
                    estabaActivo
                        ? `${p.nombre} fue desactivado.`
                        : `${p.nombre} fue reactivado.`,
                    estabaActivo ? 'warning' : 'ok',
                    estabaActivo
                        ? 'No aparecerá disponible en nuevas ventas.'
                        : 'Vuelve a estar disponible en el punto de venta.'
                );

            } catch {
                this.mostrarNotif('Error de conexión.', 'error', 'Revisa tu conexión e intenta de nuevo.');
            }
        },

        // ── Notificación ──────────────────────────────────────────────
        mostrarNotif(msg, tipo = 'ok', sub = '') {
            if (this.notif._t) clearTimeout(this.notif._t);
            this.notif.msg  = msg;
            this.notif.sub  = sub;
            this.notif.tipo = tipo;
            this.notif._t   = setTimeout(() => { this.notif.msg = ''; }, 3500);
        },
    };
}
</script>
</x-app-layout>