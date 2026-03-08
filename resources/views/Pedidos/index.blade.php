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
        },

        detailModal: false,
        detailPedido: null,
        openDetail(pedido) {
            this.detailPedido = pedido;
            this.detailModal  = true;
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
        @if(auth()->user()->rol == 1)
            <a href="{{ route('pedidos.create') }}"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
                + Nuevo Pedido
            </a>
        @endif
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
                        @php
                            $deleteRoute = route('pedidos.destroy', $pedido->id_pedido);
                            $pedidoJson  = json_encode([
                                'id_pedido'   => $pedido->id_pedido,
                                'negocio'     => $pedido->negocio->nombre_negocio ?? '—',
                                'usuario'     => $pedido->usuario->nombre_usuario ?? '—',
                                'status'      => $pedido->status_label,
                                'status_num'  => $pedido->status,
                                'notas'       => $pedido->notas ?? '',
                                'fecha'       => $pedido->created_at->format('d/m/Y H:i'),
                                'items'       => $pedido->items->map(fn($i) => [
                                    'modelo'   => $i->modelo->nombre_modelo  ?? '—',
                                    'voltaje'  => $i->voltaje->voltaje ?? '—',
                                    'color'    => $i->color->color     ?? '—',
                                    'cantidad' => $i->cantidad,
                                ]),
                            ]);
                        @endphp
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer"
                            @click="openDetail({{ $pedidoJson }})"
                        >
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs">{{ $pedido->id_pedido }}</td>
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
                            <td class="px-4 py-3 text-center" @click.stop>
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
                        @php
                            $deleteRoute = route('pedidos.destroy', $pedido->id_pedido);
                            $pedidoJson  = json_encode([
                                'id_pedido'  => $pedido->id_pedido,
                                'negocio'    => $pedido->negocio->nombre_negocio ?? '—',
                                'usuario'    => $pedido->usuario->nombre_usuario ?? '—',
                                'status'     => $pedido->status_label,
                                'status_num' => $pedido->status,
                                'notas'      => $pedido->notas ?? '',
                                'fecha'      => $pedido->created_at->format('d/m/Y H:i'),
                                'items'      => $pedido->items->map(fn($i) => [
                                    'modelo'  => $i->modelo->nombre_modelo  ?? '—',
                                    'voltaje' => $i->voltaje->voltaje ?? '—',
                                    'color'   => $i->color->color     ?? '_',
                                    'cantidad'=> $i->cantidad,
                                ]),
                            ]);
                        @endphp
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer"
                            @click="openDetail({{ $pedidoJson }})"
                        >
                            <td class="px-3 py-3">
                                <div class="text-ms font-medium text-gray-900 dark:text-white ">{{$pedido->negocio->nombre_negocio ?? '—'}}</div>
                                <div class="text-xs text-gray-400">{{$pedido->id_pedido}}</div>
                               
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
                            <td class="px-3 py-3 text-center" @click.stop>
                                <div class="flex flex-col items-center gap-1">
                                   
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

    {{-- ===== MODAL: DETALLE DEL PEDIDO ===== --}}
    
    <div
        x-show="detailModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="detailModal = false"
    >
        <div
            x-show="detailModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl"
            @click.stop
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="detailPedido?.id_pedido"></h3>
                        <p class="text-xs text-gray-400" x-text="detailPedido?.fecha"></p>
                    </div>
                </div>
                <button @click="detailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Info general --}}
            <div class="px-4 sm:px-6 py-4 grid grid-cols-2 gap-4 border-b dark:border-gray-700">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Negocio</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="detailPedido?.negocio"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Solicitado por</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="detailPedido?.usuario"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    <span
                        class="text-xs font-semibold px-2.5 py-1 rounded-full"
                        :class="{
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': detailPedido?.status_num == 1,
                            'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': detailPedido?.status_num == 2,
                            'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400': detailPedido?.status_num == 3,
                        }"
                        x-text="detailPedido?.status"
                    ></span>
                </div>
                <div x-show="detailPedido?.notas">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Notas</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="detailPedido?.notas"></p>
                </div>
            </div>

            {{-- Tabla de items con scroll horizontal en mobile --}}
            <div class="px-4 sm:px-6 py-4 max-h-64 overflow-y-auto">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Artículos</p>
                <div class="overflow-x-auto -mx-4 sm:-mx-6 px-4 sm:px-6">
                    <table class="w-full text-sm min-w-[400px] md:min-w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="pb-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap pr-3">Modelo</th>
                                <th class="pb-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap pr-3">Color</th>
                                <th class="pb-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap pr-3">Voltaje</th>
                                <th class="pb-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase whitespace-nowrap">Cant.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(items, i) in detailPedido?.items" :key="i">
                                <tr>
                                    <td class="py-2 text-gray-900 dark:text-white text-xs whitespace-nowrap pr-3" x-text="items.modelo"></td>
                                    <td class="py-2 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap pr-3" x-text="items.color"></td>
                                    <td class="py-2 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap pr-3" x-text="items.voltaje"></td>
                                    <td class="py-2 text-center font-semibold text-gray-900 dark:text-white text-xs whitespace-nowrap" x-text="items.cantidad"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-4 sm:px-6 py-4 border-t dark:border-gray-700 flex justify-end gap-2">
                <button
                    @click="detailModal = false"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    Cerrar
                </button>
                <a
                    :href="`/Pedidos/${detailPedido?.id_pedido}`"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                >
                    Ver completo
                </a>
            </div>
        </div>
    </div>

    <x-delete-modal />

</div>
</x-app-layout>