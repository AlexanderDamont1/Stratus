<x-app-layout>
<div
    x-data="{
        createModal: false,
        editModal: false,
        deleteModal: false,
        editDef: null,
        deleteId: null,
        deleteNombre: '',

        tipoActual: 'radio',
        opcionesJson: '',
        opcionesEditar: '[]',

        openEdit(def) {
            this.editDef      = def;
            this.tipoActual   = def.tipo;
            this.opcionesEditar = def.opciones ? JSON.stringify(def.opciones) : '[]';
            this.editModal    = true;
        },

        openDelete(id, nombre) {
            this.deleteId     = id;
            this.deleteNombre = nombre;
            this.deleteModal  = true;
        },

        tipoLabel(tipo) {
            const map = {
                radio:          'Radio',
                checkbox_multi: 'Checkbox múltiple',
                toggle:         'Toggle',
                texto:          'Texto',
                numero:         'Número',
            };
            return map[tipo] ?? tipo;
        },

        tipoClass(tipo) {
            const map = {
                radio:          'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                checkbox_multi: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                toggle:         'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border-green-200 dark:border-green-800',
                texto:          'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                numero:         'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-600',
            };
            return map[tipo] ?? 'bg-gray-100 text-gray-400';
        },
    }"
    class="space-y-6"
>

    {{-- Flash --}}
    @if(session('success'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 min-w-[300px] max-w-md">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4 sm:gap-2">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Configuraciones del sistema</h2>
            <p class="text-xs text-gray-400 mt-0.5">Define las opciones que verán y configurarán todos los negocios.</p>
        </div>
        <button @click="createModal = true"
                class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:opacity-90 transition whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva configuración
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $definiciones->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Activas</p>
            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $definiciones->where('activo', true)->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Grupos</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $definiciones->pluck('grupo')->unique()->count() }}</p>
        </div>
        <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Inactivas</p>
            <p class="text-2xl font-semibold text-gray-400">{{ $definiciones->where('activo', false)->count() }}</p>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Definiciones</h3>
            <span class="text-xs text-gray-400">{{ $definiciones->count() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Clave</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Grupo</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Default</th>
                        <th class="px-4 py-2 text-center text-xs text-gray-500 dark:text-gray-400 font-medium">Activo</th>
                        <th class="px-4 py-2 text-right text-xs text-gray-500 dark:text-gray-400 font-medium">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($definiciones as $def)
                    <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-gray-600 dark:text-gray-300">{{ $def->clave }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $def->nombre }}</p>
                            @if($def->descripcion)
                            <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $def->descripcion }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $def->grupo }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span x-data="{ tipo: '{{ $def->tipo }}' }"
                                  :class="tipoClass(tipo)"
                                  class="inline-flex items-center text-xs px-2 py-0.5 rounded-full border"
                                  x-text="tipoLabel(tipo)">
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $def->valor_default }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                x-data="{ activo: {{ $def->activo ? 'true' : 'false' }} }"
                                @click="
                                    fetch('/root/config/{{ $def->getKey() }}/toggle', {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        }
                                    })
                                    .then(r => r.json())
                                    .then(d => { activo = d.activo; })
                                "
                                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none"
                                :class="activo ? 'bg-gray-900 dark:bg-white' : 'bg-gray-300 dark:bg-gray-600'">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white dark:bg-gray-900 transition-transform duration-200 shadow"
                                      :class="activo ? 'translate-x-4' : 'translate-x-1'"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button"
                                        @click="openEdit({{ $def->toJson() }})"
                                        class="text-xs px-2.5 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
                                    Editar
                                </button>
                                <button type="button"
                                        @click="openDelete('{{ $def->getKey() }}', '{{ addslashes($def->nombre) }}')"
                                        class="text-xs px-2.5 py-1.5 rounded-lg text-red-400/80 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 border border-transparent hover:border-red-200 dark:hover:border-red-800/40 transition font-medium">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">
                            No hay configuraciones definidas aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: CREAR --}}
    <div x-show="createModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
         @click.self="createModal = false">
        <div x-show="createModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva configuración</h3>
                </div>
                <button @click="createModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="/root/config" class="px-6 py-5 space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Clave <span class="text-red-400">*</span></label>
                        <input type="text" name="clave" placeholder="ej. entrega_comprobante"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition font-mono">
                        <p class="text-[10px] text-gray-400 mt-1">Solo letras, números y guión bajo.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Grupo <span class="text-red-400">*</span></label>
                        <input type="text" name="grupo" placeholder="ej. ventas"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre visible <span class="text-red-400">*</span></label>
                    <input type="text" name="nombre" placeholder="ej. Entrega de comprobante"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                    <input type="text" name="descripcion" placeholder="Breve explicación para el admin del negocio"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo <span class="text-red-400">*</span></label>
                        <select name="tipo" x-model="tipoActual"
                                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                            <option value="radio">Radio</option>
                            <option value="checkbox_multi">Checkbox múltiple</option>
                            <option value="toggle">Toggle</option>
                            <option value="texto">Texto</option>
                            <option value="numero">Número</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Valor default <span class="text-red-400">*</span></label>
                        <input type="text" name="valor_default" placeholder="ej. ticket"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Icono (SVG path)</label>
                    <input type="text" name="icono" placeholder="M9 12h6m-6 4h6m2 5H7..."
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Pega el valor del atributo <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">d=""</code> de un path SVG de Heroicons.</p>
                </div>

                <div x-show="tipoActual === 'radio' || tipoActual === 'checkbox_multi'">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Opciones <span class="text-red-400">*</span>
                    </label>
                    <textarea name="opciones" rows="5" x-model="opcionesJson"
                              placeholder='[{"value":"ticket","label":"Ticket PDF","descripcion":"Descripción opcional"},{"value":"correo","label":"Correo electrónico"}]'
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">JSON array. Cada opción: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">{"value":"...","label":"...","descripcion":"..."}</code></p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Orden</label>
                    <input type="number" name="orden" value="0" min="0"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t dark:border-gray-700">
                    <button type="button" @click="createModal = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                        Crear configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDITAR --}}
    <div x-show="editModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
         @click.self="editModal = false">
        <div x-show="editModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar configuración</h3>
                        <p class="text-xs text-gray-400 font-mono" x-text="editDef?.clave"></p>
                    </div>
                </div>
                <button @click="editModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <template x-if="editDef">
                <form method="POST" :action="`/root/config/${editDef.id_ncf}`" class="px-6 py-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Clave</label>
                            <input type="text" :value="editDef.clave" disabled
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400 rounded-lg px-3 py-2 text-sm font-mono cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Grupo <span class="text-red-400">*</span></label>
                            <input type="text" name="grupo" :value="editDef.grupo"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre visible <span class="text-red-400">*</span></label>
                        <input type="text" name="nombre" :value="editDef.nombre"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                        <input type="text" name="descripcion" :value="editDef.descripcion ?? ''"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipo</label>
                            <input type="text" :value="tipoLabel(editDef.tipo)" disabled
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400 rounded-lg px-3 py-2 text-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Valor default <span class="text-red-400">*</span></label>
                            <input type="text" name="valor_default" :value="editDef.valor_default"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Icono (SVG path)</label>
                        <input type="text" name="icono" :value="editDef.icono ?? ''"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>

                    <div x-show="editDef.tipo === 'radio' || editDef.tipo === 'checkbox_multi'">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opciones</label>
                        <textarea name="opciones" rows="5" x-model="opcionesEditar"
                                  class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Orden</label>
                        <input type="number" name="orden" :value="editDef.orden" min="0"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t dark:border-gray-700">
                        <button type="button" @click="editModal = false"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- MODAL: ELIMINAR --}}
    <div x-show="deleteModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
         @click.self="deleteModal = false">
        <div x-show="deleteModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
             @click.stop>

            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar configuración</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vas a eliminar <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="deleteNombre"></span>.
                        Los valores guardados por los negocios también se eliminarán.
                    </p>
                </div>
            </div>

            <form method="POST" :action="`/root/config/${deleteId}`">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" @click="deleteModal = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition active:scale-[.98]">
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>