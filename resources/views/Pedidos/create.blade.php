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
           class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
            ← Volver
        </a>
    </div>

    {{-- ===== ERRORES ===== --}}
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 rounded-lg">
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-red-600 dark:text-red-400 text-sm flex items-center gap-1">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5 space-y-4 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3">
                Información del Pedido
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Negocio <span class="text-red-500">*</span>
                    </label>
                    <select name="id_negocio" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccione un negocio</option>
                        @foreach($negocios as $negocio)
                            <option value="{{ $negocio->id_negocio }}" {{ old('id_negocio') == $negocio->id_negocio ? 'selected' : '' }}>
                                {{ $negocio->nombre_negocio }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Notas
                    </label>
                    <textarea name="notas" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Observaciones opcionales...">{{ old('notas') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===== AGREGAR BICICLETA ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4">
                Agregar Bicicleta
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                {{-- Modelo --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                    <select x-model="form.id_modelo" @change="onModeloChange()"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Voltaje --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                    <select x-model="form.id_voltaje"
                            :disabled="!form.voltajes.length"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-40">
                        <option value="">— voltaje —</option>
                        <template x-for="v in form.voltajes" :key="v.id_voltaje">
                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                        </template>
                    </select>
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
                    <select x-model="form.id_color"
                            :disabled="!form.colores.length"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-40">
                        <option value="">— color —</option>
                        <template x-for="c in form.colores" :key="c.id_color">
                            <option :value="c.id_color" x-text="c.color"></option>
                        </template>
                    </select>
                </div>

                {{-- Cantidad --}}
                <div>
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cantidad</label>
                    <input type="number" x-model="form.cantidad"
                           min="1" max="999"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Botón agregar --}}
                <div>
                    <button type="button" @click="addItem()"
                            :disabled="!form.id_modelo || !form.id_voltaje || !form.id_color || form.cantidad < 1"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition">
                        + Agregar
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== TABLA DE ITEMS ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-5 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-3 mb-4">
                Bicicletas del Pedido
                <span class="ml-2 text-xs font-normal text-gray-400"
                      x-text="items.length ? `(${items.length} línea${items.length > 1 ? 's' : ''})` : ''"></span>
            </h3>

            {{-- Estado vacío --}}
            <div x-show="items.length === 0"
                 class="text-center py-10 text-gray-400 dark:text-gray-500 text-sm">
                <svg class="mx-auto mb-2 w-8 h-8 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z"/>
                </svg>
                Todavía no hay bicicletas en el pedido. Usa el formulario de arriba para agregar.
            </div>

            {{-- Tabla --}}
            <div x-show="items.length > 0" x-cloak>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-2 pr-4">#</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-2 pr-4">Modelo</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-2 pr-4">Voltaje</th>
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-2 pr-4">Color</th>
                            <th class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-2 pr-4">Cant.</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                {{-- inputs ocultos para enviar con el form --}}
                                <input type="hidden" :name="`items[${index}][id_modelo]`"   :value="item.id_modelo">
                                <input type="hidden" :name="`items[${index}][id_voltaje]`"  :value="item.id_voltaje">
                                <input type="hidden" :name="`items[${index}][id_color]`"    :value="item.id_color">
                                <input type="hidden" :name="`items[${index}][cantidad]`"    :value="item.cantidad">

                                <td class="py-2.5 pr-4 text-gray-400 text-xs" x-text="index + 1"></td>
                                <td class="py-2.5 pr-4 font-medium text-gray-800 dark:text-gray-200" x-text="item.modelo_nombre"></td>
                                <td class="py-2.5 pr-4 text-gray-600 dark:text-gray-400" x-text="item.voltaje_nombre"></td>
                                <td class="py-2.5 pr-4 text-gray-600 dark:text-gray-400" x-text="item.color_nombre"></td>
                                <td class="py-2.5 pr-4 text-center font-semibold text-gray-800 dark:text-gray-200" x-text="item.cantidad"></td>
                                <td class="py-2.5 text-right">
                                    <button type="button" @click="removeItem(index)"
                                            class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        Quitar
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== ACCIONES ===== --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('pedidos.index') }}"
               class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                Cancelar
            </a>
            <button type="submit"
                    :disabled="items.length === 0"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg text-sm font-semibold transition">
                Crear Pedido
            </button>
        </div>

    </form>
</div>
</x-app-layout>