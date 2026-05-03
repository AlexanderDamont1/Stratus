<x-app-layout>
<div class="space-y-6">

{{-- ===== ENCABEZADO (botón a la derecha en la misma línea que el título) ===== --}}
<div>
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-0.5">Sistema</p>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Logs de eliminación</h2>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('root.dashboard') }}"
               class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-600 transition whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al dashboard
            </a>
        </div>
    </div>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reportes generados por el job de borrado de negocios</p>
</div>


    {{-- ===== TABLA CON SCROLL HORIZONTAL Y NOMBRE TRUNCADO EN MÓVIL ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Archivos generados</h3>
            <span class="text-xs text-gray-400">{{ count($archivos) }} total</span>
        </div>

        @if($archivos->isEmpty())
            <div class="px-4 py-8 text-center text-gray-400 text-sm">No hay logs generados aún.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Archivo</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Tamaño</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Estado</th>
                            <th class="px-4 py-2 text-right text-xs text-gray-500 dark:text-gray-400 font-medium">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivos as $archivo)
                        <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            {{-- Celda de archivo: truncado en móvil, completo en desktop --}}
                            <td class="px-4 py-3 max-w-[200px] sm:max-w-none">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg {{ $archivo['es_error'] ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }} flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 {{ $archivo['es_error'] ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-mono text-xs text-gray-700 dark:text-gray-300 truncate">
                                        {{ $archivo['nombre'] }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::createFromTimestamp($archivo['fecha'])->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ number_format($archivo['tamaño'] / 1024, 1) }} KB
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($archivo['es_error'])
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">Contiene errores</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800">Normal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('root.logs.ver', ['archivo' => $archivo['nombre']]) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-xs font-medium transition">
                                    Ver detalles →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
</x-app-layout>