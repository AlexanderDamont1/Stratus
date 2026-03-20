<x-app-layout>
<div
    x-data="{
        createModal: false,
        editModal: false,
        editId: '',
        editNombre: '',
        deleteModal: false,
        deleteId: null,
        deleteNombre: '',
        deleteAction: '',
        openEdit(id, nombre) {
            this.editId     = id;
            this.editNombre = nombre;
            this.editModal  = true;
        },
        openDelete(id, nombre, action) {
            this.deleteId     = id;
            this.deleteNombre = nombre;
            this.deleteAction = action;
            this.deleteModal  = true;
        }
    }"
    class="space-y-6"
>
    {{-- ===== MENSAJE FLASH ===== --}}
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Marcas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona las marcas de tu negocio</p>
        </div>
        <button
            @click="createModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Crear marca
        </button>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total marcas</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $marcas->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $marcas->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $marcas->currentPage() }}
                <span class="text-sm font-normal text-gray-400 ml-1">/{{ $marcas->lastPage() }}</span>
            </p>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Marcas registradas</h3>
            <span class="text-xs text-gray-400">{{ $marcas->total() }} total</span>
        </div>

        {{-- Vista PC --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Marca</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Modelos</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Creada</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($marcas as $marca)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $marca->nombre_marca }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ $marca->modelos()->count() }} modelos
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ $marca->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="openEdit('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}')"
                                        class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-semibold"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="openDelete('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}', '{{ route('admin.catalogo.marcas.destroy', $marca) }}')"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 text-xs font-semibold"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay marcas registradas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista móvil --}}
        <div class="block md:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Marca</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($marcas as $marca)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $marca->nombre_marca }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $marca->modelos()->count() }} modelos</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <button
                                            @click="openEdit('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}')"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="openDelete('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}', '{{ route('admin.catalogo.marcas.destroy', $marca) }}')"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 text-xs"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-xs">No hay marcas</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($marcas->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $marcas->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: CREAR MARCA ===== --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="createModal = false"
    >
        <div
            x-show="createModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva marca</h3>
                </div>
                <button
                    @click="createModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.catalogo.marcas.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre de la marca <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre_marca"
                        value="{{ old('nombre_marca') }}"
                        placeholder="Ej: Trek, Giant, Specialized"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                        required
                    >
                    @error('nombre_marca')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="createModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                    >
                        Guardar marca
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: EDITAR MARCA ===== --}}
    <div
        x-show="editModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="editModal = false"
    >
        <div
            x-show="editModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-yellow-500 dark:bg-yellow-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar marca</h3>
                </div>
                <button
                    @click="editModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" :action="`/admin/catalogo/marcas/${editId}`">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre de la marca <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre_marca"
                        x-model="editNombre"
                        placeholder="Ej: Trek, Giant, Specialized"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition"
                        required
                    >
                    @error('nombre_marca')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="editModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                    >
                        Actualizar marca
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR ELIMINACIÓN ===== --}}
    <x-delete-modal />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($errors->any())
                const el = document.querySelector('[x-data]');
                if (el) {
                    const data = Alpine.$data(el);
                    if (data) data.createModal = true;
                }
            @endif
        });
    </script>

</div>
</x-app-layout>