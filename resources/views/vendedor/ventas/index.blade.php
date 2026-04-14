<x-app-layout>
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Ventas</h2>
            <p class="text-xs text-gray-400">Historial de ventas registradas</p>
        </div>
        <a href="{{ route('ventas.create') }}"
           class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
            + Nueva venta
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- TABLA --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Registro de ventas</h3>
            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                {{ $ventas->total() }} total
            </span>
        </div>

        {{-- Vista PC --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cliente</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Productos</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fecha</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($ventas as $venta)
                    @php
                        $total = $venta->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-400 dark:text-gray-500">
                            {{ $venta->id_venta }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $venta->cliente->nombre_cliente }} {{ $venta->cliente->apellido1 }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $venta->cliente->telefono }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                {{ $venta->detalles->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                            ${{ number_format($total, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                            {{ $venta->created_at->format('d/m/Y') }}
                            <span class="block text-gray-400">{{ $venta->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('ventas.show', $venta->id_venta) }}"
                                   class="text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                                    Ver detalle
                                </a>
                                @if($venta->detalles->filter(fn($d) => $d->bicicleta)->isNotEmpty())
                                <a href="{{ route('ventas.poliza', $venta->id_venta) }}" target="_blank"
                                   class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Póliza
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm text-gray-400">No hay ventas registradas aún.</p>
                            <a href="{{ route('ventas.create') }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                Registrar primera venta
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Vista móvil --}}
        <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($ventas as $venta)
            @php
                $total = $venta->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
            @endphp
            <div class="px-4 py-4 flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ $venta->cliente->nombre_cliente }} {{ $venta->cliente->apellido1 }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $venta->created_at->format('d/m/Y') }} · {{ $venta->detalles->count() }} producto(s)
                    </p>
                </div>
                <div class="text-right shrink-0 space-y-1">
                    <p class="font-bold text-gray-900 dark:text-white text-sm">${{ number_format($total, 2) }}</p>
                    <a href="{{ route('ventas.show', $venta->id_venta) }}"
                       class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                        Ver detalle →
                    </a>
                </div>
            </div>
            @empty
            <div class="px-4 py-10 text-center text-sm text-gray-400">
                No hay ventas registradas.
            </div>
            @endforelse
        </div>

        @if($ventas->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $ventas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
</x-app-layout>