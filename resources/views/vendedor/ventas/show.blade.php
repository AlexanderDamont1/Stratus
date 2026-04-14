<x-app-layout>
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('ventas.index') }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detalle de venta</h2>
                <p class="text-xs text-gray-400 font-mono">{{ $venta->id_venta }}</p>
            </div>
        </div>

        @if($venta->detalles->filter(fn($d) => $d->bicicleta)->isNotEmpty())
        <a href="{{ route('ventas.poliza', $venta->id_venta) }}" target="_blank"
           class="flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Póliza de garantía
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- CLIENTE --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cliente</h3>
        </div>
        <div class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Nombre completo</p>
                <p class="font-medium text-gray-800 dark:text-white">
                    {{ $venta->cliente->nombre_cliente }}
                    {{ $venta->cliente->apellido1 }}
                    {{ $venta->cliente->apellido2 }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Teléfono</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $venta->cliente->telefono }}</p>
            </div>
            @if($venta->cliente->correo)
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Correo</p>
                <p class="font-medium text-gray-800 dark:text-white">{{ $venta->cliente->correo }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Fecha de venta</p>
                <p class="font-medium text-gray-800 dark:text-white">
                    {{ $venta->created_at->format('d/m/Y') }}
                    <span class="text-gray-400 text-xs">{{ $venta->created_at->format('H:i') }}</span>
                </p>
            </div>
        </div>
    </div>

    {{-- PRODUCTOS --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Productos vendidos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">N° Serie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Detalle</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cant.</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Precio unit.</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $total = 0; @endphp
                    @foreach($venta->detalles as $detalle)
                    @php
                        $subtotal = $detalle->precio_unitario * $detalle->cantidad;
                        $total   += $subtotal;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 dark:text-white">
                                {{ $detalle->producto->nombre_producto ?? '—' }}
                            </p>
                            <span class="inline-block mt-0.5 px-1.5 py-0.5 text-xs rounded-full
                                {{ ($detalle->producto->tipo ?? '') === '2'
                                    ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-800/30 dark:text-indigo-400'
                                    : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ ($detalle->producto->tipo ?? '') === '2' ? 'Bicicleta' : 'Accesorio' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 font-mono text-xs">
                            {{ $detalle->num_serie ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            @if($detalle->bicicleta)
                                {{ $detalle->bicicleta->modelo->nombre_modelo ?? '—' }}
                                · {{ $detalle->bicicleta->color->color ?? '—' }}
                                · {{ $detalle->bicicleta->voltaje->voltaje ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 font-medium">
                            {{ $detalle->cantidad }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">
                            ${{ number_format($detalle->precio_unitario, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($subtotal, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/30 border-t-2 border-gray-200 dark:border-gray-600">
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Total de la venta
                        </td>
                        <td class="px-4 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">
                            ${{ number_format($total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</x-app-layout>