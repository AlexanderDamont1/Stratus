<x-app-layout>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Ventas</h2>
                <p class="text-xs text-gray-400 mt-0.5">Historial de ventas registradas</p>
            </div>
            <a href="{{ route('ventas.create') }}"
                class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva venta
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

        {{-- LISTA --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">

            {{-- Barra superior --}}
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
                <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Registro</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">
                    {{ $ventas->total() }} ventas
                </span>
            </div>

            {{-- Vista escritorio --}}
            <div class="hidden md:flex flex-col divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($ventas as $venta)
                    @php
                        $iniciales = mb_strtoupper(
                            mb_substr($venta->cliente->nombre_cliente, 0, 1) .
                            mb_substr($venta->cliente->apellido1, 0, 1)
                        );
                        $tieneBici   = $venta->detalles->contains(fn($d) => $d->bicicleta);
                        $tienePoliza = $venta->detalles->contains(fn($d) => $d->bicicleta && $d->poliza);
                    @endphp

                    <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition group">

                        {{-- Avatar iniciales --}}
                        <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400 shrink-0 select-none">
                            {{ $iniciales }}
                        </div>

                        {{-- Nombre + teléfono + ID + productos --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $venta->cliente->nombre_cliente }} {{ $venta->cliente->apellido1 }}
                                </span>
                                <span class="font-mono text-[10px] text-gray-400 dark:text-gray-500 shrink-0">
                                    {{ $venta->id_venta }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $venta->cliente->telefono }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600 inline-block"></span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $venta->detalles->count() }} {{ $venta->detalles->count() === 1 ? 'producto' : 'productos' }}</span>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="text-right shrink-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $venta->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $venta->created_at->format('H:i') }}</p>
                        </div>

                        {{-- Separador --}}
                        <div class="w-px h-7 bg-gray-200 dark:bg-gray-600 shrink-0"></div>

                        {{-- Total --}}
                        <div class="text-right shrink-0 min-w-[90px]">
                            <span class="text-base font-semibold text-gray-900 dark:text-white tracking-tight">
                                ${{ number_format($venta->total, 2) }}
                            </span>
                        </div>

                        {{-- Separador --}}
                        <div class="w-px h-7 bg-gray-200 dark:bg-gray-600 shrink-0"></div>

                        {{-- Acciones --}}
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <a href="{{ route('ventas.show', $venta->id_venta) }}"
                                class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white border border-gray-200 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-400 px-2.5 py-1 rounded-md transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver
                            </a>

                            @if($tieneBici)
                                <button
                                    onclick="openPreview('{{ $tienePoliza ? route('ventas.poliza', $venta->id_venta) : route('ventas.ticket', $venta->id_venta) }}')"
                                    class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 px-2.5 py-1 rounded-md transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $tienePoliza ? 'Póliza' : 'Ticket' }}
                                </button>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="px-5 py-16 text-center">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-gray-400">No hay ventas registradas aún.</p>
                        <a href="{{ route('ventas.create') }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            Registrar primera venta
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Vista móvil --}}
            <div class="flex md:hidden flex-col divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($ventas as $venta)
                    @php
                        $iniciales = mb_strtoupper(
                            mb_substr($venta->cliente->nombre_cliente, 0, 1) .
                            mb_substr($venta->cliente->apellido1, 0, 1)
                        );
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-4">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400 shrink-0 select-none">
                            {{ $iniciales }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $venta->cliente->nombre_cliente }} {{ $venta->cliente->apellido1 }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $venta->created_at->format('d/m/Y') }} · {{ $venta->detalles->count() }} prod.
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($venta->total, 2) }}</p>
                            <a href="{{ route('ventas.show', $venta->id_venta) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                Ver →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-gray-400">
                        No hay ventas registradas.
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            @if($ventas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $ventas->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- Modal preview --}}
    <div id="previewModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="relative w-[95%] md:w-[80%] lg:w-[65%] h-[90%] bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center px-4 py-2 border-b bg-gray-100">
                <span class="text-sm font-semibold">Vista previa</span>
                <button onclick="closePreview()" class="text-gray-500 hover:text-black text-lg leading-none">&times;</button>
            </div>
            <iframe id="previewFrame" class="w-full h-full"></iframe>
        </div>
    </div>

    <script>
        function openPreview(url) {
            const modal = document.getElementById('previewModal');
            const frame = document.getElementById('previewFrame');
            frame.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            const frame = document.getElementById('previewFrame');
            frame.src = '';
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('previewModal');
            if (e.target === modal) closePreview();
        });
    </script>
</x-app-layout>