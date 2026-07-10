<x-app-layout>

<div
    x-data="personalManager()"
    x-init="init()"
    class="space-y-6 mx-auto"
>

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personal</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los vendedores de tus sucursales.</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Comisiones de la semana del {{ \Carbon\Carbon::parse($comisiones['inicio'])->format('d/m') }}
                al {{ \Carbon\Carbon::parse($comisiones['fin'])->format('d/m') }}
            </p>
        </div>
        <button @click="abrirCrear()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-black dark:bg-white text-white dark:text-black shadow-md hover:opacity-80 active:scale-95 transition">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo vendedor
        </button>
    </div>

    {{-- ══ NOTIFICACIÓN (TOAST) ══ --}}
    <div
        x-show="notif.msg"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
        style="min-width:300px;max-width:420px;">
        <div class="flex items-start gap-2.5 px-3.5 py-2.5 rounded-xl border backdrop-blur-sm"
             :class="{
                'bg-green-50/90 dark:bg-green-900/30 border-green-200 dark:border-green-700': notif.tipo === 'ok',
                'bg-yellow-50/90 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-700': notif.tipo === 'warning',
                'bg-red-50/90 dark:bg-red-900/30 border-red-200 dark:border-red-700': notif.tipo === 'error'
             }">
            <div class="shrink-0 mt-0.5">
                <template x-if="notif.tipo === 'ok'">
                    <svg class="w-3.5 h-3.5 text-green-700 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="notif.tipo === 'warning'">
                    <svg class="w-3.5 h-3.5 text-yellow-700 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="notif.tipo === 'error'">
                    <svg class="w-3.5 h-3.5 text-red-700 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-semibold leading-snug"
                   :class="{
                       'text-green-700 dark:text-green-300': notif.tipo === 'ok',
                       'text-yellow-700 dark:text-yellow-300': notif.tipo === 'warning',
                       'text-red-700 dark:text-red-300': notif.tipo === 'error'
                   }"
                   x-text="notif.msg"></p>
                <p x-show="notif.sub" class="text-[12px] mt-0.5 opacity-80"
                   :class="{
                       'text-green-600 dark:text-green-400': notif.tipo === 'ok',
                       'text-yellow-600 dark:text-yellow-400': notif.tipo === 'warning',
                       'text-red-600 dark:text-red-400': notif.tipo === 'error'
                   }"
                   x-text="notif.sub"></p>
            </div>
        </div>
    </div>

    {{-- ══ SKELETON ══ --}}
    <div x-show="loading" x-cloak>
        <div class="{{ $sucursales->count() === 1 ? 'max-w-sm' : ($sucursales->count() === 2 ? 'grid grid-cols-1 sm:grid-cols-2 gap-5' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5') }}">
            @foreach($sucursales as $s)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
                <div class="p-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-start justify-between gap-2 border-l-2 border-gray-200 dark:border-gray-600">
                    <div class="space-y-2">
                        <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-3.5 w-28 rounded"></div>
                        <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-2.5 w-20 rounded"></div>
                    </div>
                </div>
                @for($i = 0; $i < 3; $i++)
                <div class="grid grid-cols-[28px_1fr_auto_auto] items-center border-b border-gray-100 dark:border-gray-700 min-h-[44px] px-1.5">
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-2.5 w-4 rounded"></div>
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-3 rounded w-2/3 mx-2"></div>
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-4 w-14 rounded-full"></div>
                    <div></div>
                </div>
                @endfor
                <div class="flex items-center justify-between p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-2.5 w-16 rounded"></div>
                    <div class="animate-pulse bg-gray-200 dark:bg-gray-700 h-2.5 w-14 rounded"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══ CONTENIDO REAL ══ --}}
    <div x-show="!loading" x-cloak>

        @if($sucursales->count() > 0)

            {{-- Empty state global --}}
            <template x-if="personal.length === 0">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm px-6 py-16 text-center">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center mx-auto mb-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-[14px] font-semibold text-gray-600 dark:text-gray-400">Sin vendedores aún</p>
                    <p class="text-[12px] mt-1 text-gray-400 dark:text-gray-500">Crea el primer vendedor con el botón de arriba.</p>
                </div>
            </template>

            {{-- Grid ──────────────────────────────── --}}
            <template x-if="personal.length > 0">
                <div class="{{ $sucursales->count() === 1 ? 'max-w-sm' : ($sucursales->count() === 2 ? 'grid grid-cols-1 sm:grid-cols-2 gap-5' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5') }}">
                    @foreach($sucursales as $s)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-transform duration-200 hover:-translate-y-1 flex flex-col">

                        {{-- Cabecera --}}
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-start justify-between gap-2 border-l-2 border-black dark:border-white">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $s->nombre_usuario }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400"
                                     x-text="
                                         personal.filter(p => p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}')).length + ' vendedores · ' +
                                         personal.filter(p => p.activo && p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}')).length + ' activos'
                                     "></div>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-green-400"></span>
                                <span class="text-[10px] font-medium text-green-500 dark:text-green-400">Activa</span>
                            </div>
                        </div>

                        {{-- Lista --}}
                        <div class="flex-1">

                            <template x-if="personal.filter(p => p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}')).length === 0">
                                <div class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin vendedores asignados</div>
                            </template>

                            <template x-for="(p, idx) in personal.filter(p => p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}'))"
                                      :key="p.id_personal + '-{{ $s->id_usuario }}'">
                                <div class="group grid grid-cols-[28px_1fr_auto_auto] items-center border-b border-gray-100 dark:border-gray-700 min-h-[44px] hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors px-1.5">

                                    {{-- Número --}}
                                    <span class="text-xs text-gray-300 dark:text-gray-600 text-center pl-1" x-text="idx + 1"></span>

                                    {{-- Info --}}
                                    <div class="p-2.5 pl-2 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                                             :class="{'line-through text-gray-400 dark:text-gray-500': !p.activo}"
                                             x-text="p.nombre"></div>
                                        <template x-if="p.sucursales.length > 1">
                                            <div class="text-xs text-gray-400 dark:text-gray-500 truncate"
                                                 x-text="'+ ' + p.sucursales.filter(su => su.id_usuario !== '{{ $s->id_usuario }}').map(su => su.nombre_usuario).join(', ')">
                                            </div>
                                        </template>
                                        <template x-if="(p.comision_semana || 0) > 0">
                                            <div class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 truncate"
                                                 x-text="'$' + Number(p.comision_semana).toLocaleString('es-MX', {minimumFractionDigits:2}) + ' en comisiones esta semana'">
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Badge estado --}}
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full whitespace-nowrap"
                                          :class="p.activo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-400 dark:bg-gray-700/30 dark:text-gray-500'"
                                          x-text="p.activo ? 'Activo' : 'Inactivo'"></span>

                                    {{-- Acciones --}}
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                        <button @click="abrirEditar(p)" class="w-6 h-6 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 transition" title="Editar">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="pedirConfirmToggle(p)"
                                                class="w-6 h-6 rounded-md flex items-center justify-center text-gray-300 dark:text-gray-600 transition"
                                                :class="p.activo ? 'hover:text-yellow-600 hover:bg-yellow-50 dark:hover:text-yellow-400 dark:hover:bg-yellow-900/20' : 'hover:text-emerald-600 hover:bg-emerald-50 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20'"
                                                :title="p.activo ? 'Desactivar' : 'Activar'">
                                            <template x-if="p.activo">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </template>
                                            <template x-if="!p.activo">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>

                                </div>
                            </template>
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 mt-auto">
                            <span class="text-xs text-gray-400 dark:text-gray-500"
                                  x-text="personal.filter(p => p.activo && p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}')).length + ' / ' + personal.filter(p => p.sucursales.some(su => su.id_usuario === '{{ $s->id_usuario }}')).length + ' activos'">
                            </span>
                            <button @click="abrirCrearEnSucursal('{{ $s->id_usuario }}')" class="flex items-center gap-1 text-xs font-medium text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 transition">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </template>

        @else
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm px-6 py-12 text-center">
                <p class="text-[13.5px] text-gray-400 dark:text-gray-500">No tienes sucursales configuradas aún.</p>
            </div>
        @endif

    </div>

    {{-- ══════════════════════════════════════════
         MODAL: CREAR / EDITAR VENDEDOR (estilo productos)
    ══════════════════════════════════════════ --}}
    <div x-show="modal.open" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
         @click.self="cerrarModal()">
        <div x-show="modal.open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>

            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-gray-100 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="modal.modo === 'crear' ? 'Nuevo vendedor' : 'Editar vendedor'"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="modal.modo === 'crear' ? 'Asigna a una o más sucursales' : 'Actualiza los datos del vendedor'"></p>
                </div>
            </div>

            <form @submit.prevent="guardar()" class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre completo <span class="text-red-400">*</span></label>
                    <input type="text" x-model="modal.nombre" placeholder="Ej: Carlos Hernández"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <template x-if="modal.errores.nombre">
                        <p class="text-[11px] text-red-500 mt-1" x-text="modal.errores.nombre"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Sucursales asignadas <span class="text-red-400">*</span></label>
                    <div class="space-y-2 max-h-52 overflow-y-auto pr-0.5">
                        @foreach($sucursales as $s)
                        <label class="flex items-center gap-3 p-3 rounded-lg border transition cursor-pointer"
                               :class="modal.sucursales.includes('{{ $s->id_usuario }}') ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/30' : 'border-gray-200 dark:border-gray-700'"
                               @click="toggleSucursal('{{ $s->id_usuario }}')">
                            <div class="w-4 h-4 rounded border flex items-center justify-center shrink-0"
                                 :class="modal.sucursales.includes('{{ $s->id_usuario }}') ? 'bg-gray-900 dark:bg-white border-gray-900 dark:border-white' : 'border-gray-300 dark:border-gray-500'">
                                <svg x-show="modal.sucursales.includes('{{ $s->id_usuario }}')" class="w-3 h-3 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $s->nombre_usuario }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $s->correo }}</p>
                            </div>
                            <template x-if="conteoPersonalPorSucursal['{{ $s->id_usuario }}']">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300"
                                      x-text="conteoPersonalPorSucursal['{{ $s->id_usuario }}'] + ' activos'"></span>
                            </template>
                        </label>
                        @endforeach
                    </div>
                    <template x-if="modal.errores.sucursales">
                        <p class="text-[11px] text-red-500 mt-1" x-text="modal.errores.sucursales"></p>
                    </template>
                </div>

                <template x-if="modal.modo === 'editar'">
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Estado del vendedor</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400" x-text="modal.activo ? 'Disponible en el punto de venta' : 'No aparece en nuevas ventas'"></p>
                        </div>
                        <button type="button" @click="modal.activo = !modal.activo"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                :class="modal.activo ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600'">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                  :class="modal.activo ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                    </div>
                </template>

                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="cerrarModal()"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="guardando || !modal.nombre.trim() || modal.sucursales.length === 0"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <span x-show="!guardando" x-text="modal.modo === 'crear' ? 'Crear vendedor' : 'Guardar cambios'"></span>
                        <span x-show="guardando" class="inline-flex items-center gap-1">
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

    {{-- ══════════════════════════════════════════
         MODAL: CONFIRMAR ACTIVAR / DESACTIVAR
    ══════════════════════════════════════════ --}}
    <div x-show="confirm.open" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
         @click.self="cerrarConfirm()">
        <div x-show="confirm.open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>

            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     :class="confirm.accion === 'desactivar' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-green-100 dark:bg-green-900/30'">
                    <template x-if="confirm.accion === 'desactivar'">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </template>
                    <template x-if="confirm.accion === 'activar'">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="confirm.titulo"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="confirm.cuerpo"></p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="cerrarConfirm()"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" @click="ejecutarConfirm()"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition active:scale-95"
                        :class="confirm.accion === 'desactivar' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-emerald-600 hover:bg-emerald-700'"
                        x-text="confirm.accion === 'desactivar' ? 'Desactivar' : 'Activar'">
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function personalManager() {
    return {
        loading: true,
        personal:              @json($personal),
        sucursalesDisponibles: @json($sucursalesJs),
        guardando: false,
        notif: { msg: '', sub: '', tipo: 'ok', _t: null },

        modal: {
            open: false, modo: 'crear', id: null,
            nombre: '', sucursales: [], activo: true, errores: {},
        },

        confirm: {
            open: false, accion: 'desactivar',
            titulo: '', cuerpo: '', _p: null,
        },

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

        init() {
            const visto = sessionStorage.getItem('personalSkeletonShown');
            if (visto === 'true') {
                this.loading = false;
            } else {
                setTimeout(() => {
                    this.loading = false;
                    sessionStorage.setItem('personalSkeletonShown', 'true');
                }, 300);
            }
        },

        abrirCrear() {
            this.modal = { open: true, modo: 'crear', id: null, nombre: '', sucursales: [], activo: true, errores: {} };
        },

        abrirCrearEnSucursal(idSucursal) {
            this.modal = { open: true, modo: 'crear', id: null, nombre: '', sucursales: [idSucursal], activo: true, errores: {} };
        },

        abrirEditar(p) {
            this.modal = {
                open: true, modo: 'editar', id: p.id_personal,
                nombre: p.nombre, sucursales: p.sucursales.map(s => s.id_usuario),
                activo: p.activo, errores: {},
            };
        },

        cerrarModal() { this.modal.open = false; },

        toggleSucursal(id) {
            const idx = this.modal.sucursales.indexOf(id);
            if (idx >= 0) this.modal.sucursales.splice(idx, 1);
            else          this.modal.sucursales.push(id);
        },

        pedirConfirmToggle(p) {
            const desactivar = p.activo;
            this.confirm = {
                open: true,
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

        async guardar() {
            this.modal.errores = {};
            if (!this.modal.nombre.trim()) { this.modal.errores.nombre = 'El nombre es obligatorio.'; return; }
            if (this.modal.sucursales.length === 0) { this.modal.errores.sucursales = 'Selecciona al menos una sucursal.'; return; }

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

                const res  = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json' }, body });
                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => { this.modal.errores[k] = v[0]; });
                    } else {
                        this.mostrarNotif(data.message || 'Error al guardar.', 'error');
                    }
                    return;
                }

                if (esCrear) {
                    this.personal.push(data.personal);
                    sessionStorage.removeItem('personalSkeletonShown');
                } else {
                    const idx = this.personal.findIndex(p => p.id_personal === this.modal.id);
                    if (idx >= 0) this.personal[idx] = data.personal;
                }

                this.cerrarModal();
                this.mostrarNotif(
                    esCrear ? `Vendedor "${nombreGuardado}" creado correctamente.` : `Datos de "${nombreGuardado}" actualizados.`,
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
                    body: new URLSearchParams({ _token: '{{ csrf_token() }}', _method: 'DELETE' }),
                });
                const data = await res.json();
                if (!res.ok) { this.mostrarNotif(data.message || 'Error.', 'error'); return; }

                const item = this.personal.find(x => x.id_personal === p.id_personal);
                if (item) item.activo = !item.activo;

                this.mostrarNotif(
                    estabaActivo ? `${p.nombre} fue desactivado.` : `${p.nombre} fue reactivado.`,
                    estabaActivo ? 'warning' : 'ok',
                    estabaActivo ? 'No aparecerá disponible en nuevas ventas.' : 'Vuelve a estar disponible en el punto de venta.'
                );

            } catch {
                this.mostrarNotif('Error de conexión.', 'error', 'Revisa tu conexión e intenta de nuevo.');
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