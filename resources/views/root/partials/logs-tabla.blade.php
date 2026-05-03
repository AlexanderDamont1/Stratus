@if($archivos->isEmpty())
    <div class="px-4 py-8 text-center text-gray-400 text-sm">No hay logs generados aún.</div>
@else
    {{-- Mobile --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-700 sm:hidden">
        @foreach($archivos as $archivo)
        <div class="px-4 py-4 space-y-2">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-lg {{ $archivo['es_error'] ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }} flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 {{ $archivo['es_error'] ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-mono text-gray-700 dark:text-gray-300 truncate">{{ $archivo['nombre'] }}</p>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::createFromTimestamp($archivo['fecha'])->diffForHumans() }}</span>
                            <span class="text-xs text-gray-400">·</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($archivo['tamaño'] / 1024, 1) }} KB</span>
                            @if($archivo['es_error'])
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">Error</span>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('root.logs.ver', ['archivo' => $archivo['nombre']]) }}"
                   class="shrink-0 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                    Ver →
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
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
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg {{ $archivo['es_error'] ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }} flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 {{ $archivo['es_error'] ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="font-mono text-xs text-gray-700 dark:text-gray-300">{{ $archivo['nombre'] }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::createFromTimestamp($archivo['fecha'])->diffForHumans() }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format($archivo['tamaño'] / 1024, 1) }} KB
                    </td>
                    <td class="px-4 py-3">
                        @if($archivo['es_error'])
                            <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">Contiene errores</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800">Normal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
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