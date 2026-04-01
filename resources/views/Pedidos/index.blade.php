<x-app-layout>

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
    'updated_at' => $pedido->updated_at->format('d/m/Y H:i'),
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

    $canalesVendedorJson = json_encode($canalesVendedor ?? []);
    @endphp

    <div
        x-data="{
            loading: true,
            ...pedidosIndex({{ $modelosJson }}, {{ Js::from($pedidosData) }}, {{ $pedidos->total() }}, {{ $pedidos->currentPage() }}, {{ $pedidos->lastPage() }}, {{ $canalesVendedorJson }})
        }"
        x-init="loading = false"
        class="space-y-6">

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
            @if(auth()->user()->id_rol == 5)
            <a href="{{ route('pedidos.rapido.crear') }}"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
                + Nuevo Pedido
            </a>
            @endif
        </div>

        <div id="notifications" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

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
                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="N° Pedido o Negocio"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                        <option value="">Todos</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Solicitado</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Verificando Pago</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Listo para Entregar</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Entregado</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('pedidos.index') }}"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
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
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
                            @if(auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5)
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        <template x-for="pedido in pedidos" :key="pedido.id_pedido">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer"
                                @click="openDetail(pedido)">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs" x-text="pedido.id_pedido"></td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white" x-text="pedido.negocio"></td>
                                <td class="px-4 py-3 text-center text-gray-900 dark:text-white"
                                    x-text="pedido.items.reduce((sum, item) => sum + item.cantidad, 0)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': pedido.status_num == 1,
                                            'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': pedido.status_num == 2,
                                            'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400': pedido.status_num == 3,
                                            'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': pedido.status_num == 4
                                        }"
                                        x-text="pedido.status">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white text-xs"
                                    x-text="pedido.fecha.split(' ')[0]"></td>

                                @if(auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5)
                                <td class="px-4 py-3 text-center" @click.stop>
                                    <div class="flex items-center justify-center gap-3">

                                        {{-- ROL 1: eliminar si está en status 1 --}}
                                        <template x-if="'{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1">
                                            <button type="button"
                                                @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold">
                                                Eliminar
                                            </button>
                                        </template>

                                        {{-- ROL 5: realizar pedido en status 1 o 2 --}}
                                        <template x-if="'{{ auth()->user()->id_rol }}' == 5 && (pedido.status_num == 1 || pedido.status_num == 2)">
                                            <a :href="`/pedidos/${pedido.id_pedido}/realizar`"
                                                class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-semibold">
                                                Realizar pedido
                                            </a>
                                        </template>

                                        {{-- ROL 5: completar entrega en status 3 --}}
                                        <template x-if="'{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 3">
                                            <button type="button"
                                                @click="openCompletar(pedido.id_pedido)"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-semibold">
                                                Completar Pedido
                                            </button>
                                        </template>

                                        {{-- Sin acción --}}
                                        <template x-if="
                                            !('{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1) &&
                                            !('{{ auth()->user()->id_rol }}' == 5 && (pedido.status_num == 1 || pedido.status_num == 2)) &&
                                            !('{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 3)
                                        ">
                                            <span class="text-xs text-gray-400">—</span>
                                        </template>

                                    </div>
                                </td>
                                @endif
                            </tr>
                        </template>

                        <tr x-show="pedidos.length === 0">
                            <td colspan="{{ auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5 ? 6 : 5 }}"
                                class="px-6 py-10 text-center text-gray-900 dark:text-white">
                                No se encontraron pedidos.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Vista móvil --}}
           <div class="block md:hidden overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-900 dark:text-white uppercase">Pedido</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-900 dark:text-white uppercase">Cantidad</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-900 dark:text-white uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-900 dark:text-white uppercase">Fecha</th>
                            @if(auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5)
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-900 dark:text-white uppercase">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="pedido in pedidos" :key="pedido.id_pedido">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer"
                                @click="openDetail(pedido)">
                                <td class="px-3 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="pedido.negocio"></div>
                                    <div class="text-xs text-gray-400" x-text="pedido.id_pedido"></div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-900 dark:text-white"
                                    x-text="pedido.items.reduce((sum, item) => sum + item.cantidad, 0)"></td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                     <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': pedido.status_num == 1,
                                            'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': pedido.status_num == 2,
                                            'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400': pedido.status_num == 3,
                                            'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': pedido.status_num == 4
                                        }"
                                        x-text="pedido.status">
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-white text-xs"
                                    x-text="pedido.fecha.split(' ')[0]"></td>

                                @if(auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5)
                                <td class="px-3 py-3 text-center" @click.stop>

                                    {{-- ROL 1: eliminar --}}
                                    <template x-if="'{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1">
                                        <button type="button"
                                            @click="openDelete(pedido.id_pedido, `{{ route('pedidos.destroy', 'REEMPLAZAR_ID') }}`.replace('REEMPLAZAR_ID', pedido.id_pedido))"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-semibold  whitespace-nowrap">
                                            Eliminar
                                        </button>
                                    </template>

                                    {{-- ROL 5: realizar pedido en status 1 o 2 --}}
                                    <template x-if="'{{ auth()->user()->id_rol }}' == 5 && (pedido.status_num == 1 || pedido.status_num == 2)">
                                        <a :href="`/pedidos/${pedido.id_pedido}/realizar`"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 text-xs font-semibold  whitespace-nowrap">
                                            Realizar pedido
                                        </a>
                                    </template>

                                    {{-- ROL 5: completar entrega en status 3 --}}
                                    <template x-if="'{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 3">
                                        <button type="button"
                                            @click="openCompletar(pedido.id_pedido)"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-semibold  whitespace-nowrap">
                                            Completar Pedido
                                        </button>
                                    </template>

                                    {{-- Sin acción --}}
                                    <template x-if="
                                        !('{{ auth()->user()->id_rol }}' == 1 && pedido.status_num == 1) &&
                                        !('{{ auth()->user()->id_rol }}' == 5 && (pedido.status_num == 1 || pedido.status_num == 2)) &&
                                        !('{{ auth()->user()->id_rol }}' == 5 && pedido.status_num == 3)
                                    ">
                                        <span class="text-xs text-gray-400">—</span>
                                    </template>

                                </td>
                                @endif
                            </tr>
                        </template>

                        <tr x-show="pedidos.length === 0">
                            <td colspan="{{ auth()->user()->id_rol == 1 || auth()->user()->id_rol == 5 ? 3 : 2 }}"
                                class="px-3 py-8 text-center text-gray-900 dark:text-white text-xs">
                                No hay pedidos
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $pedidos->withQueryString()->links() }}
            </div>
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
            class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[1px] justify-center z-50 px-4"
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
                        <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-gray-100 dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white"
                                x-text="'Pedido #' + (detailPedido?.id_pedido || '')"></h3>
                            <div class="flex items-center gap-2 text-xs mt-0.5">
                                <div class="flex items-center gap-1 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="detailPedido?.fecha"></span>
                                </div>
                                <template x-if="detailPedido?.updated_at && detailPedido?.updated_at !== detailPedido?.fecha">
                                    <div class="flex items-center gap-1">
                                        <span class="text-gray-300 dark:text-gray-600">-</span>
                                        <div class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            <span x-text="detailPedido?.updated_at"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <button @click="detailModal = false; detailToken = null; detailTokenError = ''"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
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
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                            :class="{
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400': detailPedido?.status_num == 1,
                                'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-400': detailPedido?.status_num == 2,
                                'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400': detailPedido?.status_num == 3,
                                'bg-purple-100 text-purple-800 dark:bg-purple-800/30 dark:text-purple-400': detailPedido?.status_num == 4,
                            }"
                            x-text="detailPedido?.status || '—'">
                        </span>
                    </div>
                    <div x-show="detailPedido?.notas">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Notas</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="detailPedido?.notas"></p>
                    </div>
                </div>

                {{-- Token de entrega — solo rol 1, solo status 3 --}}
                @if(auth()->user()->id_rol == 1)
                    <div x-show="detailPedido?.status_num == 3" x-cloak
                        class="px-4 sm:px-6 py-4 border-b dark:border-gray-700 bg-green-50 dark:bg-green-900/10">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2 text-center">Token de Entrega</p> {{-- Centrado --}}

                        <div x-show="!detailToken && !detailTokenCargando" class="flex items-center justify-center gap-2"> {{-- Centrado --}}
                            <button @click="cargarToken(detailPedido.id_pedido)"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Token
                            </button>
                            <p class="text-xs text-gray-400">Haz clic para revelar el token de entrega</p>
                        </div>

                        <div x-show="detailTokenCargando" class="flex items-center justify-center gap-2"> {{-- Centrado --}}
                            <svg class="w-4 h-4 animate-spin text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="text-xs text-gray-400">Cargando...</span>
                        </div>

                        <div x-show="detailToken" class="flex items-center justify-center gap-3 flex-wrap"> {{-- Centrado y con wrap para móvil --}}
                            <span class=" text-lg font-bold tracking-widest text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-4 py-2 rounded-lg"
                                x-text="detailToken"></span>
                            <button @click="copiarToken()"
                                class="flex items-center gap-1 px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span x-text="tokenCopiado ? '¡Copiado!' : 'Copiar'"></span>
                            </button>
                            <button @click="detailToken = null"
                                class="text-xs text-gray-400 hover:text-gray-600 transition">
                                Ocultar
                            </button>
                        </div>

                        <p x-show="detailTokenError" x-text="detailTokenError"
                            class="text-xs text-red-500 mt-1 text-center"></p> {{-- Centrado --}}
                    </div>
                @endif

                {{-- Tabla de items --}}
                <div class="px-4 sm:px-6 py-4 max-h-64 overflow-y-auto">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Artículos</p>
                    <div class="overflow-x-auto -mx-4 sm:-mx-6 px-4 sm:px-6">
                        <table class="w-full text-sm min-w-[400px]">
                            <thead>
                                <tr class="border-b dark:border-gray-700">
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Modelo</th>
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Color</th>
                                    <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Voltaje</th>
                                    <th class="pb-2 text-center text-xs font-semibold text-gray-900 dark:text-white uppercase">Cant.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="(item, i) in detailPedido?.items" :key="i">
                                    <tr>
                                        <td class="py-2 text-gray-900 dark:text-white text-xs pr-3" x-text="item.modelo"></td>
                                        <td class="py-2 text-gray-900 dark:text-white text-xs pr-3" x-text="item.color"></td>
                                        <td class="py-2 text-gray-900 dark:text-white text-xs pr-3" x-text="item.voltaje"></td>
                                        <td class="py-2 text-center font-semibold text-gray-900 dark:text-white text-xs" x-text="item.cantidad"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-3 items-center gap-4">
                        
                        {{-- IZQUIERDA: PDF o Token --}}
                        <div class="flex justify-start">
                            @if(auth()->user()->id_rol == 5)
                            {{-- PDF para status 3 o 4 --}}
                            <template x-if="detailPedido?.status_num == 3 || detailPedido?.status_num == 4">
                                <a :href="`/pedidos/${detailPedido?.id_pedido}/pdf`" target="_blank"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="hidden sm:inline">Descargar PDF</span>
                                    <span class="sm:hidden">PDF</span>
                                </a>
                            </template>

                            
                            @endif
                        </div>

                        
                        <div class="flex justify-center">
                            @if(auth()->user()->id_rol == 5)        
                                <template x-if="detailPedido?.status_num == 3">
                                    <button
                                        @click="openCompletar(detailPedido?.id_pedido)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        <span class="hidden sm:inline">Ingresar Token</span>
                                        <span class="sm:hidden">Ingresar Token</span>
                                    </button>
                                </template>
                            @endif        
                        </div>
                        

                        
                        <div class="flex justify-end items-center gap-2">
                            <button @click="detailModal = false; detailToken = null; detailTokenError = ''"
                                class="px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap">
                                Cerrar
                            </button>
                            
                            @if(auth()->user()->id_rol == 1)
                            <button
                                x-show="detailPedido?.status_num == 1"
                                x-cloak
                                @click="openEdit(detailPedido)"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="hidden sm:inline">Editar</span>
                                <span class="sm:hidden">Editar</span>
                            </button>
                            @endif

                            @if(auth()->user()->id_rol == 5)
                            <template x-if="detailPedido?.status_num == 1 || detailPedido?.status_num == 2">
                                <a :href="`/pedidos/${detailPedido?.id_pedido}/realizar`"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="hidden sm:inline">Realizar</span>
                                    <span class="sm:hidden">Realizar</span>
                                </a>
                            </template>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->id_rol == 1)    

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
                class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[1px] justify-center z-50 px-4"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white"
                                    x-text="'Editar Pedido #' + (editPedido?.id_pedido || '')"></h3>
                                <p class="text-xs text-gray-400" x-text="editPedido?.fecha"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400">
                                Solicitado
                            </span>
                            <button @click="editModal = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form x-ref="editForm" method="POST" :action="`/pedidos/${editPedido?.id_pedido}`">
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
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Modelo</label>
                                    <select x-model="editForm.id_modelo" @change="onModeloChange()"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                        <option value="">Seleccionar</option>
                                        @foreach($modelos as $modelo)
                                        <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre_modelo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Voltaje</label>
                                    <select x-model="editForm.id_voltaje" :disabled="!editForm.voltajes.length"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">— voltaje —</option>
                                        <template x-for="v in editForm.voltajes" :key="v.id_voltaje">
                                            <option :value="v.id_voltaje" x-text="v.voltaje"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Color</label>
                                    <select x-model="editForm.id_color" :disabled="!editForm.colores.length"
                                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">— color —</option>
                                        <template x-for="c in editForm.colores" :key="c.id_color">
                                            <option :value="c.id_color" x-text="c.color"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-900 dark:text-white mb-1">Cantidad</label>
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
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Artículos</p>
                                <span class="text-xs text-gray-500"
                                    x-text="editItems.length ? `${editItems.length} línea${editItems.length > 1 ? 's' : ''}` : ''"></span>
                            </div>

                            <div x-show="editItems.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-2">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-900 dark:text-white">No hay artículos en el pedido</p>
                            </div>

                            <div x-show="editItems.length > 0" x-cloak class="overflow-x-auto -mx-4 sm:-mx-6 px-4 sm:px-6">
                                <table class="w-full text-sm min-w-[400px]">
                                    <thead>
                                        <tr class="border-b dark:border-gray-700">
                                            <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">#</th>
                                            <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Modelo</th>
                                            <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Color</th>
                                            <th class="pb-2 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Voltaje</th>
                                            <th class="pb-2 text-center text-xs font-semibold text-gray-900 dark:text-white uppercase pr-3">Cantidad</th>
                                            <th class="pb-2 text-center text-xs font-semibold text-gray-900 dark:text-white uppercase">Acción</th>
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
                                                <td class="py-2.5 pr-3 text-gray-900 dark:text-white text-xs" x-text="item.color_nombre"></td>
                                                <td class="py-2.5 pr-3 text-gray-900 dark:text-white text-xs" x-text="item.voltaje_nombre"></td>
                                                <td class="py-2.5 pr-3">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" @click="decrementQuantity(index)"
                                                            class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition">
                                                            <svg class="w-3 h-3 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                            </svg>
                                                        </button>
                                                        <input type="number" x-model="item.cantidad"
                                                            @input="updateItemQuantity(index, $event.target.value)"
                                                            min="1" max="999"
                                                            class="w-16 text-center border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-1 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                                                        <button type="button" @click="incrementQuantity(index)"
                                                            class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition">
                                                            <svg class="w-3 h-3 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="py-2.5 text-center">
                                                    <button type="button" @click="removeEditItem(index)"
                                                        class="text-xs text-red-500 hover:text-red-700 dark:hover:text-red-400 font-medium px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                        Quitar
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot x-show="editItems.length > 0" class="border-t dark:border-gray-700">
                                        <tr>
                                            <td colspan="4" class="pt-3 text-right text-xs font-medium text-gray-600 dark:text-gray-400">Total artículos:</td>
                                            <td class="pt-3 text-center"
                                                x-text="editItems.reduce((sum, item) => sum + Number(item.cantidad), 0)"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="px-4 sm:px-6 py-4 flex justify-end gap-2">
                            <button type="button" @click="editModal = false; detailModal = true"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                ← Volver
                            </button>
                            <button type="button" @click="submitEdit()" :disabled="editItems.length === 0"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif


        @if(auth()->user()->id_rol == 5)   
            {{-- MODAL: COMPLETAR ENTREGA --}}
            <div
                x-show="completarModal"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/50 flex items-center backdrop-blur-[1px] justify-center z-50 px-4"
                @click.self="completarModal = false">
                <div
                    x-show="completarModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
                    @click.stop>

                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Completar Entrega</h3>
                            <p class="text-xs text-gray-400" x-text="'Pedido #' + (completarPedidoId ?? '')"></p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">
                            Token de entrega
                        </label>
                        <input
                            type="text"
                            x-model="completarToken"
                            @input="completarToken = $event.target.value.toUpperCase()"
                            maxlength="10"
                            placeholder="Ej: ABCD1234WS"
                            autocomplete="off"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm  tracking-widest focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                        <p class="text-xs text-gray-400 mt-1" x-text="completarToken.length + '/10 caracteres'"></p>
                    </div>

                    <p x-show="completarError" x-text="completarError"
                        class="text-xs text-red-500 mb-3 font-medium"></p>

                    <div class="flex gap-2">
                        <button type="button" @click="completarModal = false; completarToken = ''; completarError = ''"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                        <button type="button"
                            @click="submitCompletar()"
                            :disabled="completarToken.length !== 10 || completarGuardando"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
                            <template x-if="!completarGuardando">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="completarGuardando">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </template>
                            <span x-text="completarGuardando ? 'Procesando...' : 'Confirmar Entrega'"></span>
                        </button>
                    </div>
                </div>
            </div>
        @endif    

    </div>

    @push('scripts')

    <script>
        window.NUEVO_PEDIDO_ID = @json(session('nuevo_pedido_id') ?? null);
    </script>

    <script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('pedidosIndex', (modelos, initialPedidos, initialTotal, initialCurrentPage, initialLastPage, canalesVendedor) => ({

            modelos: modelos,
            pedidos: initialPedidos,
            totalPedidos: initialTotal,
            currentPage: initialCurrentPage,
            lastPage: initialLastPage,
            canalesVendedor: canalesVendedor,

            _justCreatedId: null,

            // Modales
            detailModal: false,
            detailPedido: null,
            detailToken: null,
            detailTokenCargando: false,
            detailTokenError: '',
            tokenCopiado: false,

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

            completarModal: false,
            completarPedidoId: null,
            completarToken: '',
            completarError: '',
            completarGuardando: false,

            init() {
                const self = this;

                // 1. Verificar si venimos de una redirección de creación
                if (window.NUEVO_PEDIDO_ID) {
                    fetch(`/api/pedidos/${window.NUEVO_PEDIDO_ID}`)
                        .then(r => r.ok ? r.json() : Promise.reject())
                        .then(pedido => {
                            self.addPedido(pedido);
                            self.totalPedidos++;
                            self.showNotification('created', pedido.id_pedido);
                            self._justCreatedId = pedido.id_pedido;
                        })
                        .catch(err => {
                            console.error("Error cargando nuevo pedido:", err);
                        });
                }

                // 2. Configurar Echo (WebSockets)
                if (!window.Echo) {
                    console.error("Echo no disponible");
                    return;
                }

                canalesVendedor.forEach(idVendedor => {
                    window.Echo.private(`vendedor.${idVendedor}`)
                        .listen('.pedido.updated', (e) => {
                            try {
                                const action = e.action;
                                const pedido = e.pedido;

                                if (action === 'created') {
                                    // Evitar duplicar si nosotros mismos lo creamos
                                    if (pedido && pedido.id_pedido === self._justCreatedId) {
                                        self._justCreatedId = null;
                                        return;
                                    }
                                    if (pedido) {
                                        self.addPedido(pedido);
                                        self.totalPedidos++;
                                        self.showNotification('created', pedido.id_pedido);
                                    }
                                } 
                                else if (action === 'updated') {
                                    if (pedido) {
                                        self.updatePedido(pedido);
                                        self.showNotification('updated', pedido.id_pedido);
                                    }
                                } 
                                else if (action === 'deleted') {
                                    // REVISIÓN: Capturamos el ID directamente del evento o del objeto
                                    const idABorrar = e.id_pedido || (pedido ? pedido.id_pedido : null);
                                    if (idABorrar) {
                                        self.removePedido(idABorrar);
                                        self.showNotification('deleted', idABorrar);
                                    }
                                }
                            } catch (err) {
                                console.error("Error en evento Echo:", err);
                            }
                        });
                });
            },

            // --- GESTIÓN REACTIVA DE LA LISTA ---

            addPedido(newPedido) {
                const exists = this.pedidos.some(p => p.id_pedido === newPedido.id_pedido);
                if (!exists) {
                    // Reasignar el array para que Alpine detecte el cambio (Reactividad)
                    this.pedidos = [newPedido, ...this.pedidos];
                    this.sortPedidos();
                }
            },

            updatePedido(updatedPedido) {
                const index = this.pedidos.findIndex(p => p.id_pedido === updatedPedido.id_pedido);
                if (index !== -1) {
                    // Actualizamos la referencia del objeto y el array completo
                    this.pedidos[index] = { ...updatedPedido };
                    this.pedidos = [...this.pedidos];
                    
                    // Actualizar modal si está abierto
                    if (this.detailPedido && this.detailPedido.id_pedido === updatedPedido.id_pedido) {
                        this.detailPedido = { ...updatedPedido };
                    }
                }
            },

            removePedido(id) {
                // Filtramos y reasignamos para forzar el redibujado del DOM
                const countBefore = this.pedidos.length;
                this.pedidos = this.pedidos.filter(p => p.id_pedido != id);
                
                if (this.pedidos.length < countBefore) {
                    this.totalPedidos--;
                }

                // Cerrar modales si el pedido borrado estaba en pantalla
                if (this.detailPedido && this.detailPedido.id_pedido == id) this.detailModal = false;
                if (this.editPedido && this.editPedido.id_pedido == id) this.editModal = false;
            },

            sortPedidos() {
                this.pedidos.sort((a, b) => {
                    const parseDate = (fecha) => {
                        if (!fecha) return new Date(0);
                        const [date, time] = fecha.split(' ');
                        const [d, m, y] = date.split('/');
                        return new Date(`${y}-${m}-${d}T${time}`);
                    };
                    return parseDate(b.fecha) - parseDate(a.fecha);
                });
            },

            // --- NOTIFICACIONES ---

            showNotification(action, pedidoId) {
                const container = document.getElementById('notifications');
                if (!container) return;

                const config = {
                    created: { msg: `Nuevo pedido #${pedidoId}`, style: "bg-white text-gray-800 border-gray-200", icon: "bg-green-100 text-green-600", svg: "M5 13l4 4L19 7" },
                    updated: { msg: `Pedido #${pedidoId} actualizado`, style: "bg-yellow-50 text-yellow-800 border-yellow-300", icon: "bg-yellow-200 text-yellow-700", svg: "M12 9v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" },
                    deleted: { msg: `Pedido #${pedidoId} eliminado`, style: "bg-red-50 text-red-800 border-red-300", icon: "bg-red-200 text-red-700", svg: "M6 18L18 6M6 6l12 12" }
                };

                const c = config[action];
                const notification = document.createElement('div');
                notification.className = `px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 border animate-fade-in-up ${c.style}`;
                notification.innerHTML = `
                    <div class="flex items-center justify-center w-6 h-6 rounded-full ${c.icon}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${c.svg}"/></svg>
                    </div>
                    <span class="text-sm font-medium">${c.msg}</span>
                `;
                
                container.appendChild(notification);
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transition = 'opacity 0.5s';
                    setTimeout(() => notification.remove(), 500);
                }, 3500);
            },

                openDetail(pedido) {
                    this.detailPedido = pedido;
                    this.detailToken = null;
                    this.detailTokenError = '';
                    this.tokenCopiado = false;
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

                async cargarToken(pedidoId) {
                    this.detailTokenCargando = true;
                    this.detailTokenError = '';
                    try {
                        const resp = await fetch(`/ver/${pedidoId}/token`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });
                        const data = await resp.json();
                        if (!resp.ok || !data.ok) {
                            this.detailTokenError = data.mensaje ?? 'No se pudo cargar el token.';
                            return;
                        }
                        this.detailToken = data.token;
                    } catch (e) {
                        this.detailTokenError = 'Error de conexión.';
                    } finally {
                        this.detailTokenCargando = false;
                    }
                },

                async copiarToken() {
                    if (!this.detailToken) return;
                    try {
                        await navigator.clipboard.writeText(this.detailToken);
                        this.tokenCopiado = true;
                        setTimeout(() => { this.tokenCopiado = false; }, 2000);
                    } catch (e) {
                        console.error('Error al copiar', e);
                    }
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
                    if (!this.editForm.id_modelo || !this.editForm.id_voltaje || !this.editForm.id_color || this.editForm.cantidad < 1) return;
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

                removeEditItem(index) { this.editItems.splice(index, 1); },

                updateItemQuantity(index, value) {
                    const newValue = parseInt(value) || 1;
                    this.editItems[index].cantidad = Math.min(999, Math.max(1, newValue));
                    this.editItems = [...this.editItems];
                },

                incrementQuantity(index) {
                    if (this.editItems[index].cantidad < 999) {
                        this.editItems[index].cantidad++;
                        this.editItems = [...this.editItems];
                    }
                },

                decrementQuantity(index) {
                    if (this.editItems[index].cantidad > 1) {
                        this.editItems[index].cantidad--;
                        this.editItems = [...this.editItems];
                    }
                },

                submitEdit() {
                    if (this.editItems.length === 0) { alert('Debe agregar al menos un artículo'); return; }
                    this.$refs.editForm.submit();
                },

                openDelete(nombre, action) {
                    this.deleteNombre = nombre;
                    this.deleteAction = action;
                    this.deleteModal = true;
                },

                openCompletar(pedidoId) {
                    this.completarPedidoId = pedidoId;
                    this.completarToken = '';
                    this.completarError = '';
                    this.completarModal = true;
                },

                async submitCompletar() {
                    if (this.completarGuardando) return;
                    this.completarGuardando = true;
                    this.completarError = '';

                    try {
                        const resp = await fetch(`/pedidos/${this.completarPedidoId}/completar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ token: this.completarToken })
                        });

                        const data = await resp.json();

                        if (!resp.ok || !data.ok) {
                            this.completarError = data.mensaje ?? 'Token inválido.';
                            return;
                        }

                        this.completarModal = false;
                        this.completarToken = '';

                    } catch (e) {
                        this.completarError = 'Error de conexión.';
                    } finally {
                        this.completarGuardando = false;
                    }
                },

            }));
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px) }
            to { opacity: 1; transform: translateY(0) }
        }
        .animate-fade-in-up { animation: fadeInUp .2s ease-out }

        .loader { display: flex; align-items: center; }
        .bar {
            display: inline-block; width: 4px; height: 20px;
            background-color: rgba(0,0,0,.2); border-radius: 10px;
            animation: scale-up4 1s linear infinite;
        }
        .bar:nth-child(2) { height: 35px; margin: 0 6px; animation-delay: .25s; }
        .bar:nth-child(3) { animation-delay: .5s; }

        @keyframes scale-up4 {
            20% { background-color: #000000; transform: scaleY(1.5); }
            40% { transform: scaleY(1); }
        }
    </style>

    @endpush
</x-app-layout>