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
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">

    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Voltajes
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">
            Gestiona los voltajes del catálogo de bicicletas
        </p>
    </div>

    <div class="flex gap-2">

        

        {{-- Crear voltaje --}}
        <button
            @click="createModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Crear Voltaje
        </button>

    </div>

</div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total voltajes</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $voltajes->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $voltajes->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $voltajes->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $voltajes->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== TABLA DE VOLTAJES ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Voltajes registrados</h3>
            <span class="text-xs text-gray-400">{{ $voltajes->total() }} total</span>
        </div>
        
        <!-- Vista en PC: tabla completa -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            ID
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Voltaje
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($voltajes as $voltaje)
                        @php
                            $deleteRoute = route('gestor.vehiculos.voltajes.destroy', $voltaje);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            {{-- ID --}}
                            <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                                #{{ $voltaje->id_voltaje }}
                            </td>
                            {{-- Voltaje --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <span>{{ $voltaje->voltaje }}</span>
                                </div>
                            </td>
                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('gestor.vehiculos.voltajes.edit', $voltaje) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-semibold">
                                        Editar
                                    </a>
                                    <span class="text-gray-300">|</span>
                                    <button
                                        type="button"
                                        @click="openDelete(
                                            '{{ $voltaje->id_voltaje }}',
                                            '{{ $voltaje->voltaje }}',
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
                                No hay voltajes registrados aún.
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
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Voltaje</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($voltajes as $voltaje)
                            @php
                                $deleteRoute = route('gestor.vehiculos.voltajes.destroy', $voltaje);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-7 w-7 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-2">
                                            <svg class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($voltaje->voltaje, 20) }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                ID: #{{ $voltaje->id_voltaje }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <a href="{{ route('gestor.vehiculos.voltajes.edit', $voltaje) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 text-xs">
                                            Editar
                                        </a>
                                        <button
                                            type="button"
                                            @click="openDelete({{ $voltaje->id_voltaje }}, '{{ $voltaje->voltaje }}', '{{ $deleteRoute }}')"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 text-xs"
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <p class="text-xs">No hay voltajes</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($voltajes->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $voltajes->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: CREAR VOLTAJE ===== --}}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo voltaje</h3>
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

            <form method="POST" action="{{ route('gestor.vehiculos.voltajes.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Voltaje <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="voltaje"
                        value="{{ old('voltaje') }}"
                        placeholder="Ej: 12V, 24V, 36V"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                        required
                        autofocus
                    >
                    @error('voltaje')
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
                        Guardar voltaje
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR ELIMINACIÓN ===== --}}
    <x-delete-modal />
</div>
</x-app-layout>