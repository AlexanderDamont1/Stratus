<x-app-layout>

    <!-- Pantalla de carga -->
    <div
        x-data="{ loading:true }"
        x-init="window.addEventListener('load', () => loading=false)">

        <div
            x-show="loading"
            x-cloak
            class="fixed inset-0 bg-white flex items-center justify-center z-[999]">

            <div class="flex flex-col items-center gap-6">

                <div class="loader">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>

                <p class="text-gray-600 font-semibold text-sm">
                    Cargando pedidos...
                </p>

            </div>

        </div>

    </div>

    @php
    $modelosJson = json_encode($modelos->map(fn($m) => [
    'id_modelo' => $m->id_modelo,
    'nombre_modelo' => $m->nombre_modelo,
    ]));

    $pedidosData = $pedidos->map(fn($pedido) => [
    'id_pedido' => $pedido->id_pedido,
    'negocio' => $pedido->negocio->nombre_negocio ?? '—',
    'usuario' => $pedido->usuario->nombre_usuario ?? '—',
    'status' => $pedido->status_label,
    'status_num' => $pedido->status,
    'notas' => $pedido->notas ?? '',
    'fecha' => $pedido->created_at->format('d/m/Y H:i'),
    'items' => $pedido->items->map(fn($i) => [
    'id_modelo' => $i->id_modelo,
    'id_voltaje' => $i->id_voltaje,
    'id_color' => $i->id_color,
    'modelo' => $i->modelo->nombre_modelo ?? '—',
    'voltaje' => $i->voltaje->voltaje ?? '—',
    'color' => $i->color->color ?? '—',
    'cantidad' => $i->cantidad,
    ]),
    ])->values();
    @endphp

    <div
        x-data="{
        loading:true,
        ...pedidosIndex({{ $modelosJson }}, {{ Js::from($pedidosData) }}, {{ $pedidos->total() }}, {{ $pedidos->currentPage() }}, {{ $pedidos->lastPage() }})
    }"
        x-init="
        loading = false;

        @if(session('nuevo_pedido_id'))
            fetch('{{ route('pedidos.api.get', session('nuevo_pedido_id')) }}')
                .then(response => response.json())
                .then(pedido => {
                    this.addPedido(pedido);
                    this.showNotification('created', pedido.id_pedido);
                })
                .catch(error => {
                    console.error('Error cargando nuevo pedido:', error);
                    setTimeout(() => window.location.reload(), 1000);
                });
        @endif
    "
        class="space-y-6">
        <x-flash-messages />

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Pedidos</h2>
                <p class="text-xs text-gray-400 mt-0.5">Gestiona los pedidos de bicicletas</p>
            </div>
            @if(auth()->user()->id_rol == 1)
            <a href="{{ route('pedidos.create') }}"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
                + Nuevo Pedido
            </a>
            @endif
        </div>

        <div id="notifications"
            class="fixed top-4 right-4 z-50 flex flex-col gap-2">
        </div>

        {{-- ===== ESTADÍSTICAS ===== --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="totalPedidos"></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400" x-text="pedidos.length"></p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    <span x-text="currentPage"></span>
                    <span class="text-sm font-normal text-gray-400 ml-1">/<span x-text="lastPage"></span></span>
                </p>
            </div>
        </div>

        {{-- ===== FILTROS ===== --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4">
            <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="N° Pedido o Negocio"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
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
                <span class="text-xs text-gray-400" x-text="totalPedidos + ' total'"></span>
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
                            @if(auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($pedido) { return $pedido->status == 2; })))
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        <template x-for="pedido in pedidos" :key="pedido.id_pedido">
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer"
                                @click="openDetail(pedido)">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs" x-text="pedido.id_pedido"></td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400" x-text="pedido.negocio"></td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400" x-text="pedido.usuario"></td>
                                <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400" x-text="pedido.items.reduce((sum, item) => sum + item.cantidad, 0)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                          'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': pedido.status_num == 1,
                                          'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': pedido.status_num == 2,
                                          'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400': pedido.status_num == 3
                                      }"
                                        x-text="pedido.status">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs" x-text="pedido.fecha"></td>

                                @if(auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($p) { return $p->status == 2; })))
                                <td class="px-4 py-3 text-center" @click.stop>
                                    <div class="flex items-center justify-center gap-3">
                                        <template x-if="'{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1">
                                            <button type="button"
                                                @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold">
                                                Eliminar
                                            </button>
                                        </template>

                                        <template x-if="'{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 2">
                                            <button type="button"
                                                @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                                class="text-green-600 hover:text-green-800 text-xs font-semibold">
                                                Realizar pedido
                                            </button>
                                        </template>

                                        <template x-if="!('{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1) && !('{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 2)">
                                            <span class="text-xs text-gray-400">—</span>
                                        </template>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        </template>

                        <tr x-show="pedidos.length === 0">
                            <td :colspan="{{ (auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($p) { return $p->status == 2; }))) ? 7 : 6 }}"
                                class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron pedidos.
                            </td>
                        </tr>
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
                            @if(auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($pedido) { return $pedido->status == 2; })))
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="pedido in pedidos" :key="pedido.id_pedido">
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer"
                                @click="openDetail(pedido)">
                                <td class="px-3 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="pedido.negocio"></div>
                                    <div class="text-xs text-gray-400" x-text="pedido.id_pedido"></div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                          'bg-yellow-100 text-yellow-800': pedido.status_num == 1,
                                          'bg-blue-100 text-blue-800': pedido.status_num == 2,
                                          'bg-green-100 text-green-800': pedido.status_num == 3
                                      }"
                                        x-text="pedido.status">
                                    </span>
                                </td>

                                @if(auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($p) { return $p->status == 2; })))
                                <td class="px-3 py-3 text-center" @click.stop>
                                    <template x-if="'{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1">
                                        <button type="button"
                                            @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                            class="text-red-600 text-xs font-semibold">
                                            Eliminar
                                        </button>
                                    </template>

                                    <template x-if="'{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 2">
                                        <button type="button"
                                            @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                            class="text-green-600 text-xs font-semibold">
                                            Realizar pedido
                                        </button>
                                    </template>

                                    <template x-if="!('{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1) && !('{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 2)">
                                        <span class="text-xs text-gray-400">—</span>
                                    </template>
                                </td>
                                @endif
                            </tr>
                        </template>

                        <tr x-show="pedidos.length === 0">
                            <td :colspan="{{ (auth()->user()->id_rol == 1 || (auth()->user()->id_rol == 5 && $pedidos->contains(function($p) { return $p->status == 2; }))) ? 3 : 2 }}"
                                class="px-3 py-8 text-center text-gray-500 text-xs">
                                No hay pedidos
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($pedidos->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $pedidos->withQueryString()->links() }}
            </div>
            @endif
        </div>

        {{-- MODAL: DETALLE DEL PEDIDO --}}
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
            @click.self="detailModal = false">
            <div
                x-show="detailModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl"
                @click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="'Pedido #' + (detailPedido?.id_pedido || '')"></h3>
                            <p class="text-xs text-gray-400" x-text="detailPedido?.fecha"></p>
                        </div>
                    </div>
                    <button @click="detailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Info general --}}
                <div class="px-4 sm:px-6 py-4 grid grid-cols-2 gap-4 border-b dark:border-gray-700">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Negocio</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="detailPedido?.negocio || '—'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Solicitado por</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="detailPedido?.usuario || '—'"></p>
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
                            x-text="detailPedido?.status || '—'"></span>
                    </div>
                    <div x-show="detailPedido?.notas">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Notas</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="detailPedido?.notas"></p>
                    </div>
                </div>

                {{-- Tabla de items --}}
                <div class="px-4 sm:px-6 py-4 max-h-64 overflow-y-auto">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Artículos</p>
                    <div class="overflow-x-auto -mx-4 sm:-mx-6 px-4 sm:px-6">
                        <table class="w-full text-sm min-w-[400px]">
                            <thead>
                                <tr class="border-b dark:border-gray-700">
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Modelo</th>
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Color</th>
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Voltaje</th>
                                    <th class="pb-2 text-center text-xs font-semibold text-gray-500 uppercase">Cant.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(item, i) in detailPedido?.items" :key="i">
                                    <tr>
                                        <td class="py-2 text-gray-900 dark:text-white text-xs pr-3" x-text="item.modelo"></td>
                                        <td class="py-2 text-gray-500 dark:text-gray-400 text-xs pr-3" x-text="item.color"></td>
                                        <td class="py-2 text-gray-500 dark:text-gray-400 text-xs pr-3" x-text="item.voltaje"></td>
                                        <td class="py-2 text-center font-semibold text-gray-900 dark:text-white text-xs" x-text="item.cantidad"></td>
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
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        Cerrar
                    </button>
                    @if(auth()->user()->id_rol == 1)
                    <button
                        x-show="detailPedido?.status_num == 1"
                        x-cloak
                        @click="openEdit(detailPedido)"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- MODAL: EDITAR PEDIDO --}}
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
            @click.self="editModal = false">
            <div
                x-show="editModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
                @click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white" x-text="'Editar Pedido #' + (editPedido?.id_pedido || '')"></h3>
                            <p class="text-xs text-gray-400" x-text="editPedido?.fecha"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400">
                            Solicitado
                        </span>
                        <button @click="editModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form
                    x-ref="editForm"
                    method="POST"
                    :action="`/pedidos/${editPedido?.id_pedido}`">
                    @csrf
                    @method('PUT')

                    {{-- Notas --}}
                    <div class="px-4 sm:px-6 py-4 border-b dark:border-gray-700">
                        <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5 font-medium">Notas</label>
                        <textarea name="notas" rows="2" x-model="editNotas"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                            placeholder="Observaciones opcionales..."></textarea>
                    </div>

                    {{-- Agregar artículo --}}
                    <div class="px-4 sm:px-6 py-4 border-b dark:border-gray-700">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-3 font-medium">Agregar artículo</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Modelo</label>
                                <select x-model="editForm.id_modelo" @change="onModeloChange()"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                    <option value="">Seleccionar</option>
                                    @foreach($modelos as $modelo)
                                    <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Voltaje</label>
                                <select x-model="editForm.id_voltaje"
                                    :disabled="!editForm.voltajes.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">— voltaje —</option>
                                    <template x-for="v in editForm.voltajes" :key="v.id_voltaje">
                                        <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Color</label>
                                <select x-model="editForm.id_color"
                                    :disabled="!editForm.colores.length"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">— color —</option>
                                    <template x-for="c in editForm.colores" :key="c.id_color">
                                        <option :value="c.id_color" x-text="c.color"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cantidad</label>
                                <input type="number" x-model="editForm.cantidad" min="1" max="999"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                            </div>
                            <div>
                                <button type="button" @click="addEditItem()"
                                    :disabled="!editForm.id_modelo || !editForm.id_voltaje || !editForm.id_color || editForm.cantidad < 1"
                                    class="w-full px-4 py-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-sm font-semibold rounded-lg hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de items editables --}}
                    <div class="px-4 sm:px-6 py-4 border-b dark:border-gray-700">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-3 font-medium flex items-center justify-between">
                            Artículos
                            <span x-text="editItems.length ? `${editItems.length} línea${editItems.length > 1 ? 's' : ''}` : ''"></span>
                        </p>

                        <div x-show="editItems.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No hay artículos en el pedido</p>
                        </div>

                        <div x-show="editItems.length > 0" x-cloak class="overflow-x-auto -mx-4 sm:-mx-6 px-4 sm:px-6">
                            <table class="w-full text-sm min-w-[400px]">
                                <thead>
                                    <tr class="border-b dark:border-gray-700">
                                        <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">#</th>
                                        <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Modelo</th>
                                        <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Color</th>
                                        <th class="pb-2 text-left text-xs font-semibold text-gray-500 uppercase pr-3">Voltaje</th>
                                        <th class="pb-2 text-center text-xs font-semibold text-gray-500 uppercase pr-3">Cant.</th>
                                        <th class="pb-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="(item, index) in editItems" :key="index">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <input type="hidden" :name="`items[${index}][id_modelo]`" :value="item.id_modelo">
                                            <input type="hidden" :name="`items[${index}][id_voltaje]`" :value="item.id_voltaje">
                                            <input type="hidden" :name="`items[${index}][id_color]`" :value="item.id_color">
                                            <input type="hidden" :name="`items[${index}][cantidad]`" :value="item.cantidad">

                                            <td class="py-2.5 pr-3 text-gray-400 text-xs" x-text="index + 1"></td>
                                            <td class="py-2.5 pr-3 font-medium text-gray-900 dark:text-white text-xs" x-text="item.modelo_nombre"></td>
                                            <td class="py-2.5 pr-3 text-gray-500 dark:text-gray-400 text-xs" x-text="item.color_nombre"></td>
                                            <td class="py-2.5 pr-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    <span x-text="item.voltaje_nombre"></span>
                                                </span>
                                            </td>
                                            <td class="py-2.5 pr-3 text-center">
                                                <span class="inline-flex items-center justify-center w-7 h-7 text-xs font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full border border-blue-200 dark:border-blue-800"
                                                    x-text="item.cantidad"></span>
                                            </td>
                                            <td class="py-2.5 text-right">
                                                <button type="button" @click="removeEditItem(index)"
                                                    class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 font-medium px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                    Quitar
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 sm:px-6 py-4 flex justify-end gap-2">
                        <button
                            type="button"
                            @click="editModal = false; detailModal = true"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            ← Volver
                        </button>
                        <button
                            type="button"
                            @click="submitEdit()"
                            :disabled="editItems.length === 0"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Guardar cambios
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- MODAL: ELIMINAR --}}
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
            @click.self="deleteModal = false">
            <div
                x-show="deleteModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
                @click.stop>
                <div class="flex items-start gap-4 mb-5">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar pedido</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Vas a eliminar el pedido
                            <span class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="deleteNombre"></span>.
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
                <form method="POST" :action="deleteAction">
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

    @push('scripts')

    <script>
        window.NUEVO_PEDIDO_ID = @json(session('nuevo_pedido_id') ?? null);
    </script>

    <script>
        // @ts-nocheck
        document.addEventListener('alpine:init', () => {

            Alpine.data('pedidosIndex', (modelos, initialPedidos, initialTotal, initialCurrentPage, initialLastPage) => ({

                modelos: modelos,
                pedidos: initialPedidos,
                totalPedidos: initialTotal,
                currentPage: initialCurrentPage,
                lastPage: initialLastPage,

                detailModal: false,
                detailPedido: null,

                editModal: false,
                editPedido: null,
                editNotas: '',
                editItems: [],
                editForm: {
                    id_modelo: '',
                    id_voltaje: '',
                    id_color: '',
                    cantidad: 1,
                    voltajes: [],
                    colores: []
                },

                deleteModal: false,
                deleteAction: '',
                deleteNombre: '',


                init() {

                    const self = this;

                    console.log("Echo realtime activo");


                    // ─────────── cargar nuevo pedido si se acaba de crear ───────────
                    if (window.NUEVO_PEDIDO_ID) {

                        fetch(`/pedidos/api/${window.NUEVO_PEDIDO_ID}`)
                            .then(r => {
                                if (!r.ok) throw new Error('no ok');
                                return r.json();
                            })
                            .then(pedido => {

                                self.addPedido(pedido);
                                self.totalPedidos++;

                                self.showNotification('created', pedido.id_pedido);

                            })
                            .catch(err => {

                                console.error("Error cargando nuevo pedido:", err);

                                setTimeout(() => location.reload(), 1000);

                            });

                    }



                    // ─────────── verificar Echo ───────────

                    if (!window.Echo) {

                        console.error("Echo no disponible");

                        return;

                    }



                    // ─────────── websocket pedidos ───────────

                    window.Echo.channel('pedidos')

                        .listen('.pedido.updated', (e) => {

                            try {

                                const action = e.action ?? null;
                                const pedido = e.pedido ?? null;

                                if (action === 'created') {

                                    if (pedido) {
                                        self.addPedido(pedido);
                                        self.totalPedidos++;
                                    }

                                } else if (action === 'updated') {

                                    if (pedido) {
                                        self.updatePedido(pedido);
                                    }


                                    const id = e.id_pedido ?? pedido?.id_pedido;

                                    if (id) {

                                        self.removePedido(id);

                                        if (self.totalPedidos > 0) {
                                            self.totalPedidos--;
                                        }

                                    }

                                }

                                self.showNotification(action, pedido?.id_pedido ?? e.id_pedido);

                            } catch (err) {

                                console.error("Error evento Echo:", err);

                            }

                        });

                },



                // ─────────── realtime handlers ───────────

                addPedido(newPedido) {

                    const exists = this.pedidos.some(p => p.id_pedido === newPedido.id_pedido);

                    if (!exists) {

                        this.pedidos.unshift(newPedido);

                        this.pedidos.sort((a, b) => {

                            const dateA = new Date(a.fecha.split('/').reverse().join('-'));
                            const dateB = new Date(b.fecha.split('/').reverse().join('-'));

                            return dateB - dateA;

                        });

                    }

                },



                updatePedido(updatedPedido) {

                    const index = this.pedidos.findIndex(p => p.id_pedido === updatedPedido.id_pedido);

                    if (index !== -1) {

                        this.pedidos[index] = updatedPedido;

                        if (this.detailPedido && this.detailPedido.id_pedido === updatedPedido.id_pedido) {
                            this.detailPedido = updatedPedido;
                        }

                        if (this.editPedido && this.editPedido.id_pedido === updatedPedido.id_pedido) {
                            this.editModal = false;
                        }

                    }

                },



                removePedido(id) {

                    const index = this.pedidos.findIndex(p => p.id_pedido === id);

                    if (index !== -1) {

                        this.pedidos.splice(index, 1);

                        if (this.detailPedido && this.detailPedido.id_pedido === id)
                            this.detailModal = false;

                        if (this.editPedido && this.editPedido.id_pedido === id)
                            this.editModal = false;

                    }

                },



                // ─────────── notificaciones ───────────

                showNotification(action, pedidoId) {

                    const container = document.getElementById('notifications');
                    if (!container) return;

                    const messages = {
                        created: 'Nuevo pedido #' + pedidoId,
                        updated: 'Pedido #' + pedidoId + ' actualizado',
                        deleted: 'Pedido #' + pedidoId + ' eliminado'
                    };

                    const styles = {
                        created: "bg-white text-gray-800 border-gray-200",
                        updated: "bg-yellow-50 text-yellow-800 border-yellow-300",
                        deleted: "bg-red-50 text-red-800 border-red-300"
                    };

                    const icons = {

                        created: `
        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>`,

                        updated: `
        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-yellow-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-7.4 12.8A1 1 0 004 18h16a1 1 0 00.87-1.34l-7.4-12.8a1 1 0 00-1.74 0z"/>
            </svg>
        </div>`,

                        deleted: `
        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-red-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6l-12 12"/>
            </svg>
        </div>`
                    };

                    const notification = document.createElement('div');

                    notification.className =
                        `px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 border ${styles[action]}`;

                    notification.innerHTML = `
        ${icons[action]}
        <span class="text-sm font-medium">${messages[action]}</span>
    `;

                    container.appendChild(notification);

                    setTimeout(() => notification.remove(), 3000);
                },



                // ─────────── UI helpers ───────────

                openDetail(pedido) {

                    this.detailPedido = pedido;

                    this.detailModal = true;

                },


                openEdit(pedido) {

                    this.editPedido = pedido;

                    this.editNotas = pedido.notas ?? '';

                    this.editItems = pedido.items.map(i => ({

                        id_modelo: i.id_modelo,
                        id_voltaje: i.id_voltaje,
                        id_color: i.id_color,
                        cantidad: i.cantidad,
                        modelo_nombre: i.modelo,
                        voltaje_nombre: i.voltaje,
                        color_nombre: i.color,

                    }));

                    this.editForm = {
                        id_modelo: '',
                        id_voltaje: '',
                        id_color: '',
                        cantidad: 1,
                        voltajes: [],
                        colores: []
                    };

                    this.detailModal = false;

                    this.editModal = true;

                },



                async onModeloChange() {

                    const modeloId = this.editForm.id_modelo;

                    this.editForm.id_voltaje = '';
                    this.editForm.id_color = '';
                    this.editForm.voltajes = [];
                    this.editForm.colores = [];

                    if (!modeloId) return;

                    try {

                        const [voltajes, colores] = await Promise.all([

                            fetch(`/voltaje-por-modelo/${modeloId}`).then(r => r.json()),
                            fetch(`/colores-por-modelo/${modeloId}`).then(r => r.json())

                        ]);

                        this.editForm.voltajes = voltajes;
                        this.editForm.colores = colores;

                    } catch (e) {

                        console.error("Error cargando datos", e);

                    }

                },



                addEditItem() {

                    if (!this.editForm.id_modelo || !this.editForm.id_voltaje || !this.editForm.id_color || this.editForm.cantidad < 1)
                        return;

                    const existing = this.editItems.find(i =>

                        i.id_modelo == this.editForm.id_modelo &&
                        i.id_voltaje == this.editForm.id_voltaje &&
                        i.id_color == this.editForm.id_color

                    );

                    if (existing) {

                        existing.cantidad = Number(existing.cantidad) + Number(this.editForm.cantidad);

                    } else {

                        const modelo = this.modelos.find(m => m.id_modelo == this.editForm.id_modelo);
                        const voltaje = this.editForm.voltajes.find(v => v.id_voltaje == this.editForm.id_voltaje);
                        const color = this.editForm.colores.find(c => c.id_color == this.editForm.id_color);

                        this.editItems.push({

                            id_modelo: this.editForm.id_modelo,
                            id_voltaje: this.editForm.id_voltaje,
                            id_color: this.editForm.id_color,
                            cantidad: Number(this.editForm.cantidad),
                            modelo_nombre: modelo ? modelo.nombre_modelo : this.editForm.id_modelo,
                            voltaje_nombre: voltaje ? voltaje.voltaje : this.editForm.id_voltaje,
                            color_nombre: color ? color.color : this.editForm.id_color

                        });

                    }

                    this.editForm.id_voltaje = '';
                    this.editForm.id_color = '';
                    this.editForm.cantidad = 1;

                },



                removeEditItem(index) {

                    this.editItems.splice(index, 1);

                },



                submitEdit() {

                    if (this.editItems.length === 0) return;

                    this.$refs.editForm.submit();

                },



                openDelete(nombre, action) {

                    this.deleteNombre = nombre;

                    this.deleteAction = action;

                    this.deleteModal = true;

                }

            }));

        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp .2s ease-out
        }

        .loader {
            display: flex;
            align-items: center;
        }

        .bar {
            display: inline-block;
            width: 4px;
            height: 20px;
            background-color: rgba(0, 0, 0, .2);
            border-radius: 10px;
            animation: scale-up4 1s linear infinite;
        }

        .bar:nth-child(2) {
            height: 35px;
            margin: 0 6px;
            animation-delay: .25s;
        }

        .bar:nth-child(3) {
            animation-delay: .5s;
        }

        @keyframes scale-up4 {
            20% {
                background-color: #000000;
                transform: scaleY(1.5);
            }

            40% {
                transform: scaleY(1);
            }
        }
    </style>

    @endpush
</x-app-layout>