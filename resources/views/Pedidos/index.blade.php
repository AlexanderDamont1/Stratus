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
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Pedidos</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los pedidos de bicicletas</p>
        </div>
        <a href="{{ route('pedidos.create') }}"
           class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
            + Nuevo Pedido
        </a>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $pedidos->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $pedidos->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $pedidos->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $pedidos->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== FILTROS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
        <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="N° Pedido o Negocio"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Solicitado</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Preparado</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Entregado</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                    Filtrar
                </button>
                <a href="{{ route('pedidos.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pedidos registrados</h3>
            <span class="text-xs text-gray-400">{{ $pedidos->total() }} total</span>
        </div>

        {{-- Vista PC --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">N° Pedido</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Negocio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Solicitado por</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Items</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($pedidos as $pedido)
                        @php $deleteRoute = route('pedidos.destroy', $pedido->id_pedido); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white font-mono text-xs">{{ $pedido->id_pedido }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $pedido->negocio->nombre_negocio ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $pedido->usuario->nombre_usuario ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $pedido->items->sum('cantidad') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($pedido->status == 1) bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400
                                    @elseif($pedido->status == 2) bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400
                                    @else bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400
                                    @endif">
                                    {{ $pedido->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('pedidos.show', $pedido->id_pedido) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-semibold">Ver</a>
                                    @if($pedido->status == 1)
                                    <button type="button"
                                        @click="openDelete('{{ $pedido->id_pedido }}', '{{ $pedido->id_pedido }}', '{{ $deleteRoute }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold">
                                        Eliminar
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron pedidos.
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
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pedido</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pedidos as $pedido)
                        @php $deleteRoute = route('pedidos.destroy', $pedido->id_pedido); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-3 py-3">
                                <div class="text-xs font-medium text-gray-900 dark:text-white font-mono">{{ $pedido->id_pedido }}</div>
                                <div class="text-xs text-gray-400">{{ $pedido->negocio->nombre_negocio ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $pedido->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($pedido->status == 1) bg-yellow-100 text-yellow-800
                                    @elseif($pedido->status == 2) bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $pedido->status_label }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <a href="{{ route('pedidos.show', $pedido->id_pedido) }}" class="text-blue-600 text-xs">Ver</a>
                                    @if($pedido->status == 1)
                                    <button type="button"
                                        @click="openDelete('{{ $pedido->id_pedido }}', '{{ $pedido->id_pedido }}', '{{ $deleteRoute }}')"
                                        class="text-red-600 text-xs">Eliminar</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-8 text-center text-gray-500 text-xs">No hay pedidos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidos->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $pedidos->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <x-delete-modal />

</div>
</x-app-layout>