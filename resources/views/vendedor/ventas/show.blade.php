<x-app-layout>
    <div class="mx-auto space-y-5">

        {{-- ===== ENCABEZADO ===== --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('ventas.index') }}"
                class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                        flex items-center gap-1 mb-2 transition w-fit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver a ventas
                </a>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detalle de venta</h2>
                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $venta->id_venta }}</p>
            </div>

            <div class="flex flex-col items-end gap-2 sm:flex-row-reverse sm:items-center">
                <button onclick="openPreview('{{ route('ventas.ticket', $venta->id_venta) }}')"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                    Ver ticket
                </button>

                @if($tieneGarantia)
                    <button onclick="openPreview('{{ route('ventas.poliza', $venta->id_venta) }}')"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition whitespace-nowrap flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver póliza
                    </button>
                @endif
            </div>
        </div>

        {{-- ===== FLASH MESSAGE ===== --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-cloak
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
                <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4
                            shadow-xl ring-1 ring-gray-200 dark:ring-gray-700">
                    <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                              clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- ===== MÉTRICAS RÁPIDAS ===== --}}
        @php
            $totalVenta    = $venta->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
            $cantArticulos = $venta->detalles->sum('cantidad');
            $pagos         = $venta->pagos ?? collect();

            // Resumen de métodos de pago
            $resumenPagos = $pagos->map(fn($p) => ($p->label ?? $p->metodo))->implode(' + ');
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">Artículos</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $cantArticulos }}</p>
            </div>
            <div class="bg-green-100 dark:bg-green-800/30 border border-green-100 dark:border-green-800/30 rounded-xl p-4">
                <p class="text-[11px] uppercase tracking-wider text-green-400 mb-1">Total</p>
                <p class="text-2xl font-semibold text-green-800 dark:text-green-400">
                    ${{ number_format($totalVenta, 2) }}
                </p>
            </div>
            <div class="bg-yellow-100 dark:bg-yellow-800/30 border border-yellow-100 dark:border-yellow-800/30 rounded-xl p-4">
                <p class="text-[11px] uppercase tracking-wider text-yellow-400 mb-1">Método de pago</p>
                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 leading-tight mt-1">
                    {{ $resumenPagos ?: '—' }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <p class="text-[11px] uppercase tracking-wider text-gray-400 mb-1">Fecha</p>
                <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight mt-1">
                    {{ $venta->created_at->format('d/m/Y') }}
                    <span class="text-xs text-gray-400 font-normal">{{ $venta->created_at->format('H:i') }}</span>
                </p>
            </div>
        </div>

        {{-- ===== CLIENTE ===== --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-700/30">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Cliente</p>
            </div>
            <div class="p-5">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Nombre completo</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">
                            {{ $venta->cliente->nombre_cliente }}
                            {{ $venta->cliente->apellido1 }}
                            {{ $venta->cliente->apellido2 }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Teléfono</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $venta->cliente->telefono }}</dd>
                    </div>
                    @if($venta->cliente->correo)
                        <div>
                            <dt class="text-xs text-gray-500 mb-0.5">Correo</dt>
                            <dd class="font-medium text-gray-900 dark:text-white text-xs">{{ $venta->cliente->correo }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">Vendedor</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-sky-100 dark:bg-sky-800/30 text-sky-700 dark:text-sky-300">
                                {{ $venta->personal?->nombre ?? '—' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ===== PAGOS ===== --}}
        @if($pagos->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-700/30">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Pagos</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($pagos as $pago)
                <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        {{-- Ícono según tipo --}}
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $pago->label ?? $pago->metodo }}
                            </p>
                            @if($pago->requiere_referencia && $pago->referencia)
                                <p class="text-xs text-gray-400 font-mono mt-0.5">
                                    Ref: {{ $pago->referencia }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">
                            ${{ number_format($pago->monto, 2) }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Total pagado vs total venta --}}
            @if($pagos->count() > 1 || $venta->descuento_total > 0)
            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700
                        flex flex-wrap items-center justify-between gap-2">
                @if($venta->descuento_total > 0)
                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                        Descuento aplicado: −${{ number_format($venta->descuento_total, 2) }}
                    </span>
                @else
                    <span></span>
                @endif
                <span class="text-xs text-gray-500 tabular-nums">
                    Total pagado:
                    <span class="font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($pagos->sum('monto'), 2) }}
                    </span>
                </span>
            </div>
            @endif
        </div>
        @endif

        {{-- ===== PRODUCTOS ===== --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
            <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100 dark:border-gray-700
                        bg-gray-50 dark:bg-gray-700/30">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Productos vendidos</p>
            </div>

            @if($tieneGarantia)
                <div class="flex items-center gap-2 px-5 py-2.5 bg-green-50 dark:bg-green-900/20
                            border-b border-green-100 dark:border-green-800/30">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04
                                 A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622
                                 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <p class="text-xs font-medium text-green-700 dark:text-green-400">
                        Garantía activa — esta venta incluye póliza de garantía
                    </p>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/30">
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">N° serie</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Detalle</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cant.</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Precio unit.</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($venta->detalles as $detalle)
                            @php
                                $subtotal  = $detalle->precio_unitario * $detalle->cantidad;
                                $colorRaw  = $detalle->bicicleta->color->color ?? '';
                                $colorInfo = parsearColor($colorRaw);
                                $esBici    = ($detalle->producto->tipo ?? '') === '2';
                                $esPieza   = !is_null($detalle->id_pieza);
                                $esServicio = !$detalle->id_producto && !$detalle->id_pieza;
                                $esGratis  = $detalle->precio_unitario == 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $detalle->nombre }}
                                        </p>
                                        @if($esGratis && !$esBici)
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-full
                                                         bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 uppercase">
                                                Gratis
                                            </span>
                                        @endif
                                    </div>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full
                                        {{ match(true) {
                                            $esBici     => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-800/30 dark:text-indigo-400',
                                            $esPieza    => 'bg-teal-100 text-teal-700 dark:bg-teal-800/30 dark:text-teal-400',
                                            $esServicio => 'bg-purple-100 text-purple-700 dark:bg-purple-800/30 dark:text-purple-400',
                                            default     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        } }}">
                                        {{ match(true) {
                                            $esBici     => 'Bicicleta',
                                            $esPieza    => 'Pieza',
                                            $esServicio => 'Servicio',
                                            default     => 'Accesorio',
                                        } }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs align-top">
                                    {{ $detalle->num_serie ?? '—' }}
                                </td>

                                <td class="px-4 py-3 align-top whitespace-nowrap">
                                    @if($detalle->bicicleta)
                                        <p class="text-xs text-gray-700 dark:text-gray-300 mb-1">
                                            {{ $detalle->bicicleta->modelo->nombre_modelo ?? '—' }}
                                            · {{ $detalle->bicicleta->voltaje->voltaje ?? '—' }}
                                        </p>
                                        <div class="flex items-center gap-1.5">
                                            @if(count($colorInfo['hexes']) >= 2)
                                                <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10
                                                             overflow-hidden relative inline-flex shrink-0">
                                                    <span class="absolute left-0 top-0 w-1/2 h-full"
                                                          style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                                    <span class="absolute right-0 top-0 w-1/2 h-full"
                                                          style="background:{{ $colorInfo['hexes'][1] }}"></span>
                                                </span>
                                            @else
                                                <span class="w-4 h-4 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                                      style="background:{{ $colorInfo['hexes'][0] }}"></span>
                                            @endif
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $colorInfo['nombre'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center text-gray-800 dark:text-gray-200 font-medium align-top">
                                    {{ $detalle->cantidad }}
                                </td>

                                <td class="px-4 py-3 text-right align-top">
                                    @if($esGratis)
                                        <span class="line-through text-gray-300 text-xs mr-1">
                                            {{-- precio original no disponible en detalle, mostrar $0.00 --}}
                                        </span>
                                        <span class="text-green-600 dark:text-green-400 font-semibold">$0.00</span>
                                    @else
                                        <span class="text-gray-700 dark:text-gray-300">
                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white align-top">
                                    ${{ number_format($subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/30 border-t-2 border-gray-200 dark:border-gray-600">
                        @if($venta->descuento_total > 0)
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-right text-xs text-green-600 dark:text-green-400">
                                Descuento cupón
                            </td>
                            <td class="px-4 py-2 text-right text-xs font-semibold text-green-600 dark:text-green-400">
                                −${{ number_format($venta->descuento_total, 2) }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Total de la venta
                            </td>
                            <td class="px-4 py-4 text-right text-xl font-bold text-gray-900 dark:text-white">
                                ${{ number_format($totalVenta - $venta->descuento_total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- ===== MODAL IFRAME ===== --}}
    <div id="previewModal"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="relative w-full max-w-5xl h-[90vh] bg-white dark:bg-gray-900
                    rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center px-5 py-3
                        border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                <span class="text-sm font-semibold text-gray-800 dark:text-white">Vista previa</span>
                <button onclick="closePreview()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <iframe id="previewFrame" class="w-full h-[calc(100%-56px)]"></iframe>
        </div>
    </div>

    @if($autoTicket)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    window.open('{{ route('ventas.ticket', $venta->id_venta) }}', '_blank');
                }, 800);
            });
        </script>
    @endif

    <script>
        function openPreview(url) {
            const modal = document.getElementById('previewModal');
            document.getElementById('previewFrame').src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            document.getElementById('previewFrame').src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', function (e) {
            if (e.target === document.getElementById('previewModal')) closePreview();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closePreview();
        });
    </script>

</x-app-layout>

@php
    function parsearColor(string $colorStr): array {
        if (!$colorStr) return ['nombre' => '—', 'hexes' => ['#cccccc']];
        [$nombre, $hexPart] = array_pad(explode('|', $colorStr, 2), 2, '');
        return [
            'nombre' => trim($nombre),
            'hexes'  => $hexPart ? explode('/', $hexPart) : ['#cccccc'],
        ];
    }
@endphp