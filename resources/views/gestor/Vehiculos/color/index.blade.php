<x-app-layout>
<div
    x-data="{
        createModal: false,
        deleteModal: false,
        deleteId: null,
        deleteNombre: '',
        deleteAction: '',
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
    @if(session('success'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 animate-fade-in" 
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-xl ring-1 ring-gray-200 min-w-[300px] max-w-md">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
            </div>
            <button @click="show = false" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 animate-fade-in" 
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-xl ring-1 ring-gray-200 min-w-[300px] max-w-md">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
            </div>
            <button @click="show = false" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Colores</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los colores disponibles por modelo</p>
        </div>
        <button
            @click="createModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Crear color
        </button>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total colores</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $colores->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $colores->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $colores->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $colores->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== TABLA DE COLORES ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Colores registrados</h3>
            <span class="text-xs text-gray-400">{{ $colores->total() }} total</span>
        </div>
        
        <!-- Vista en PC: tabla completa -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Color
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Modelo
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($colores as $color)
                        @php
                            $deleteRoute = route('gestor.vehiculos.colores.destroy', $color);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            {{-- Color --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full" style="background-color: {{ $color->color }}; border: 1px solid #ddd;"></div>
                                    <span>{{ $color->color }}</span>
                                </div>
                            </td>
                            {{-- Modelo --}}
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $color->modelo->nombre_modelo ?? '—' }}
                            </td>
                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('gestor.vehiculos.colores.edit', $color) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-semibold">
                                        Editar
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <button
                                        type="button"
                                        @click="openDelete(
                                            '{{ $color->id_color }}',
                                            '{{ $color->color }}',
                                            '{{ $deleteRoute }}'
                                        )"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay colores registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vista en móvil: tabla compacta -->
        <div class="block md:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($colores as $color)
                            @php
                                $deleteRoute = route('gestor.vehiculos.colores.destroy', $color);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 rounded-full mr-2" style="background-color: {{ $color->color }}; border: 1px solid #ddd;"></div>
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                            {{ Str::limit($color->color, 15) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $color->modelo->nombre_modelo ?? '—' }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <a href="{{ route('gestor.vehiculos.colores.edit', $color) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 text-xs">
                                            Editar
                                        </a>
                                        <button
                                            type="button"
                                            @click="openDelete({{ $color->id_color }}, '{{ $color->color }}', '{{ $deleteRoute }}')"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 text-xs"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs">No hay colores</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($colores->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $colores->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: CREAR COLOR ===== --}}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo color</h3>
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

            <form method="POST" action="{{ route('gestor.vehiculos.colores.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="color"
                        value="{{ old('color') }}"
                        placeholder="Ej: Rojo, #FF0000"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                        required
                    >
                    @error('color')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_modelo"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                        required
                    >
                        <option value="">Seleccione un modelo</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id_modelo }}" {{ old('id_modelo') == $modelo->id_modelo ? 'selected' : '' }}>
                                {{ $modelo->nombre_modelo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_modelo')
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
                        Guardar color
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR ELIMINACIÓN ===== --}}
    <div
        x-show="deleteModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="deleteModal = false"
    >
        <div
            x-show="deleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar color</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vas a eliminar el color
                        <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="deleteNombre"></span>.
                        Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <form method="POST" :action="deleteAction">
                @csrf
                @method('DELETE')

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition active:scale-[.98]"
                    >
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>