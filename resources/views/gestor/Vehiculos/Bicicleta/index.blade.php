<x-app-layout>
<div
    x-data="{
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
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-xl ring-1 ring-gray-200 min-w-[300px] max-w-md">
            <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-gray-900 flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)">
        <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-xl ring-1 ring-red-200 min-w-[300px] max-w-md">
            <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium text-gray-900 flex-1">{{ session('error') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600">
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
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Bicicletas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona el inventario de bicicletas</p>
        </div>
        <a href="{{ route('gestor.vehiculos.bicicletas.create') }}"
           class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
            + Nueva Bicicleta
        </a>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $bicicletas->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $bicicletas->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $bicicletas->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $bicicletas->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== FILTROS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
        <form method="GET" action="{{ route('gestor.vehiculos.bicicletas.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="N° Serie o Status"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                    Filtrar
                </button>
                <a href="{{ route('gestor.vehiculos.bicicletas.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Bicicletas registradas</h3>
            <span class="text-xs text-gray-400">{{ $bicicletas->total() }} total</span>
        </div>

        {{-- Vista PC --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">N° Serie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Modelo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Voltaje</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Color</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($bicicletas as $bicicleta)
                        @php $deleteRoute = route('gestor.vehiculos.bicicletas.destroy', $bicicleta->num_serie); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->voltaje->voltaje ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $bicicleta->color->color ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($bicicleta->status == 'disponible') bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400
                                    @elseif($bicicleta->status == 'en_mantenimiento') bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400
                                    @elseif($bicicleta->status == 'prestado') bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400
                                    @else bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bicicleta->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-3">
                                   
                                    <a href="{{ route('gestor.vehiculos.bicicletas.edit', $bicicleta->num_serie) }}"
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-semibold">Editar</a>
                                    <button type="button"
                                        @click="openDelete('{{ $bicicleta->num_serie }}', '{{ $bicicleta->num_serie }}', '{{ $deleteRoute }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron bicicletas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista móvil --}}
        <div class="block md:hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serie</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($bicicletas as $bicicleta)
                        @php $deleteRoute = route('gestor.vehiculos.bicicletas.destroy', $bicicleta->num_serie); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-3 py-3">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $bicicleta->num_serie }}</div>
                                <div class="text-xs text-gray-400">{{ $bicicleta->modelo->nombre_modelo ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($bicicleta->status == 'disponible') bg-green-100 text-green-800
                                    @elseif($bicicleta->status == 'en_mantenimiento') bg-yellow-100 text-yellow-800
                                    @elseif($bicicleta->status == 'prestado') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bicicleta->status)) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <a href="{{ route('gestor.vehiculos.bicicletas.edit', $bicicleta->num_serie) }}" class="text-yellow-600 text-xs">Editar</a>
                                    <button type="button"
                                        @click="openDelete('{{ $bicicleta->num_serie }}', '{{ $bicicleta->num_serie }}', '{{ $deleteRoute }}')"
                                        class="text-red-600 text-xs">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-8 text-center text-gray-500 text-xs">No hay bicicletas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bicicletas->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $bicicletas->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL ELIMINAR ===== --}}
    <div x-show="deleteModal" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="deleteModal = false">
        <div x-show="deleteModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar bicicleta</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vas a eliminar la bicicleta <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="deleteNombre"></span>. Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>
            <form method="POST" :action="deleteAction">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" @click="deleteModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>