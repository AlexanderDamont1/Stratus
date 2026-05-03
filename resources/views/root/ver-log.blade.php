<x-app-layout>
<div class="space-y-6">

   {{-- ===== ENCABEZADO (botón a la derecha en la misma línea que el título) ===== --}}
<div>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-0.5">Sistema</p>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detalle del log</h2>
        </div>
        <div>
            <a href="{{ route('root.logs.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-2 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a logs
            </a>
        </div>
    </div>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $nombre }}</p>
</div>

    {{-- ===== CONTENIDO DEL LOG (estilo tarjeta) ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-4 py-3 border-b dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Contenido del archivo</h3>
        </div>
        <div class="p-4">
            <pre class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-xs font-mono text-gray-700 dark:text-gray-300 leading-relaxed overflow-x-auto whitespace-pre-wrap">{{ $contenido }}</pre>
        </div>
    </div>

</div>
</x-app-layout>