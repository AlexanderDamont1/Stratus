<x-app-layout>
    <div class="space-y-6" x-data="{
    /* ── estado del formulario de entrada ── */
    form: { id_modelo: '', id_voltaje: '', id_color: '', cantidad: 1, voltajes: [], colores: [] },

    /* ── tabla de líneas ya agregadas ── */
    items: [],

    /* ── helpers de nombres para mostrar en la tabla ── */
    modeloNombre(id) {
        const el = document.querySelector(`[data-modelos] [data-id='${id}']`);
        return el ? el.dataset.nombre : id;
    },
    voltajeNombre(id) {
        const v = this.form.voltajes.find(x => x.id_voltaje == id);
        return v ? v.voltaje : id;
    },
    colorNombre(id) {
        const c = this.form.colores.find(x => x.id_color == id);
        return c ? c.color : id;
    },

    async onModeloChange() {
        const modeloId = this.form.id_modelo;
        this.form.id_voltaje = '';
        this.form.id_color   = '';
        this.form.voltajes   = [];
        this.form.colores    = [];

        if (!modeloId) return;

        try {
            const [voltajes, colores] = await Promise.all([
                fetch(`/voltaje-por-modelo/${modeloId}`).then(r => r.json()),
                fetch(`/colores-por-modelo/${modeloId}`).then(r => r.json()),
            ]);
            this.form.voltajes = voltajes;
            this.form.colores  = colores;
        } catch(e) {
            console.error('Error cargando datos:', e);
        }
    },

    addItem() {
        if (!this.form.id_modelo || !this.form.id_voltaje || !this.form.id_color || this.form.cantidad < 1) return;

        this.items.push({
            id_modelo:      this.form.id_modelo,
            id_voltaje:     this.form.id_voltaje,
            id_color:       this.form.id_color,
            cantidad:       this.form.cantidad,
            modelo_nombre:  this.modeloNombre(this.form.id_modelo),
            voltaje_nombre: this.voltajeNombre(this.form.id_voltaje),
            color_nombre:   this.colorNombre(this.form.id_color),
        });

        /* reset del formulario de entrada (sin borrar listas cacheadas) */
        this.form.id_voltaje = '';
        this.form.id_color   = '';
        this.form.cantidad   = 1;
    },

    removeItem(index) {
        this.items.splice(index, 1);
    }
}">

        {{-- lookup oculto para nombres de modelos --}}
        <div data-modelos class="hidden">
            @foreach($modelos as $modelo)
            <span data-id="{{ $modelo->id_modelo }}" data-nombre="{{ $modelo->nombre_modelo }}"></span>
            @endforeach
        </div>

        <x-flash-messages />

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Nuevo Pedido</h2>
                <p class="text-xs text-gray-400 mt-0.5">Completa los datos del pedido</p>
            </div>
            <a href="{{ route('pedidos.index') }}"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>
        </div>

        {{-- ===== ERRORES ===== --}}
        @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h4 class="text-sm font-semibold text-red-700 dark:text-red-400">Se encontraron errores</h4>
            </div>
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-red-600 dark:text-red-400 text-xs flex items-center gap-1.5">
                    <span class="w-1 h-1 bg-red-500 rounded-full inline-block"></span>
                    {{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('pedidos.store') }}" method="POST">
            @csrf

            {{-- ===== INFO GENERAL ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Información del Pedido</h3>
                    <span class="text-xs text-gray-400">Datos generales</span>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                                Notas
                            </label>
                            <textarea name="notas" rows="2"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                                placeholder="Observaciones opcionales...">{{ old('notas') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== AGREGAR BICICLETA ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Agregar Bicicleta</h3>
                    <span class="text-xs text-gray-400">Nuevo artículo</span>
                </div>

                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        {{-- Modelo --}}
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5 font-medium">Modelo</label>
                            <select x-model="form.id_modelo" @change="onModeloChange()"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                <option value="">Seleccionar</option>
                                @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Voltaje --}}
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5 font-medium">Voltaje</label>
                            <select x-model="form.id_voltaje"
                                :disabled="!form.voltajes.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">— Seleccionar voltaje —</option>
                                <template x-for="v in form.voltajes" :key="v.id_voltaje">
                                    <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Color --}}
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5 font-medium">Color</label>
                            <select x-model="form.id_color"
                                :disabled="!form.colores.length"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">— Seleccionar color —</option>
                                <template x-for="c in form.colores" :key="c.id_color">
                                    <option :value="c.id_color" x-text="c.color"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Cantidad --}}
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5 font-medium">Cantidad</label>
                            <input type="number" x-model="form.cantidad"
                                min="1" max="999"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                        </div>

                        {{-- Botón agregar --}}
                        <div>
                            <button type="button" @click="addItem()"
                                :disabled="!form.id_modelo || !form.id_voltaje || !form.id_color || form.cantidad < 1"
                                class="w-full px-4 py-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-sm font-semibold rounded-lg hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TABLA DE ITEMS ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bicicletas del Pedido</h3>
                    <span class="text-xs text-gray-400" x-text="items.length ? `Total: ${items.length} artículo${items.length > 1 ? 's' : ''}` : 'Sin artículos'"></span>
                </div>

                <div class="px-6 py-5">
                    {{-- Estado vacío --}}
                    <div x-show="items.length === 0"
                        class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Todavía no hay bicicletas en el pedido</p>
                        <p class="text-xs text-gray-400 mt-1">Usa el formulario de arriba para agregar artículos</p>
                    </div>

                    {{-- Tabla --}}
                    <div x-show="items.length > 0" x-cloak class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[600px] md:min-w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Voltaje</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        {{-- inputs ocultos para enviar con el form --}}
                                        <input type="hidden" :name="`items[${index}][id_modelo]`" :value="item.id_modelo">
                                        <input type="hidden" :name="`items[${index}][id_color]`" :value="item.id_color">
                                        <input type="hidden" :name="`items[${index}][id_voltaje]`" :value="item.id_voltaje">
                                        <input type="hidden" :name="`items[${index}][cantidad]`" :value="item.cantidad">

                                        <td class="px-4 py-3 text-gray-400 text-xs" x-text="index + 1"></td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-sm" x-text="item.modelo_nombre"></td>

                                        <td class="px-4 py-3">
                                            <span class="text-xs text-gray-600 dark:text-gray-300" x-text="item.color_nombre"></span>
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                <span x-text="item.voltaje_nombre"></span>
                                            </span>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full border border-blue-200 dark:border-blue-800"
                                                x-text="item.cantidad"></span>
                                        </td>

                                        <td class="px-4 py-3 text-right">
                                            <button type="button" @click="removeItem(index)"
                                                class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 font-medium transition">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== ACCIONES ===== --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('pedidos.index') }}"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit"
                    :disabled="items.length === 0"
                    class="px-5 py-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Crear Pedido
                </button>
            </div>

        </form>
    </div>
</x-app-layout>