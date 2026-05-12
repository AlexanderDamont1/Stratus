{{-- resources/views/admin/garantias/index.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-7" x-data="garantiasAdmin()">

    <x-flash-messages />

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Garantías</h2>
            <p class="text-xs text-gray-400 mt-0.5">Configura las garantías por marca y gestiona reclamos</p>
        </div>
        <a href="{{ route('admin.garantias.reclamos') }}"
           class="border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300
                  px-4 py-2 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition
                  flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver reclamos
        </a>
    </div>

    {{-- Marcas --}}
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Estado por marca</p>

        @if($marcas->isEmpty())
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay marcas registradas para este negocio.</p>
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($marcas as $marca)
            @php
                $cfg      = $configs[$marca->id_marca] ?? null;
                $activa   = $cfg?->activa ?? false;
                $estado   = $cfg?->estado_procesamiento ?? 'sin_pdf';
                $numDefs  = $cfg?->componenteDefs->where('excluido', false)->count() ?? 0;
                $politica = $cfg?->politica_reemplazo ?? 'mini';
                $dias     = $cfg?->mini_garantia_dias ?? 7;

                $estadoLabel = match($estado) {
                    'sin_pdf'    => 'Sin póliza',
                    'pendiente',
                    'procesando' => 'Procesando...',
                    'completado' => $numDefs . ' componentes',
                    'error'      => 'Error al procesar',
                    default      => '—',
                };
                $estadoColor = match($estado) {
                    'completado'            => 'text-green-600 dark:text-green-400',
                    'error'                 => 'text-red-600 dark:text-red-400',
                    'pendiente','procesando'=> 'text-yellow-600 dark:text-yellow-400',
                    default                 => 'text-gray-400',
                };
                $politicaLabel = match($politica) {
                    'heredar' => 'Reemplazo: heredar',
                    'nueva'   => 'Reemplazo: nueva',
                    'mini'    => "Reemplazo: mini ({$dias}d)",
                    default   => '—',
                };
            @endphp

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                        rounded-xl p-4 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">

                {{-- Nombre + toggle --}}
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $marca->nombre_marca }}
                        </p>
                        <p class="text-xs {{ $estadoColor }} mt-0.5">{{ $estadoLabel }}</p>
                        @if($cfg)
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $politicaLabel }}</p>
                        @endif
                    </div>

                    <button
                        @click="toggleActiva('{{ $marca->id_marca }}', $el)"
                        data-activa="{{ $activa ? '1' : '0' }}"
                        :disabled="{{ $cfg ? 'false' : 'true' }}"
                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2
                               border-transparent transition-colors duration-200 ease-in-out
                               focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed
                               {{ $activa ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-600' }}"
                        title="{{ $cfg ? 'Activar/desactivar garantía' : 'Primero configura la póliza' }}">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full
                                     bg-white dark:bg-gray-900 shadow ring-0 transition duration-200 ease-in-out
                                     {{ $activa ? 'translate-x-4' : 'translate-x-0' }}">
                        </span>
                    </button>
                </div>

                {{-- PDF badge + botón configurar --}}
                <div class="flex items-center justify-between">
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                        {{ $cfg?->tienePdf()
                            ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                            : 'bg-gray-100 dark:bg-gray-700 text-gray-400' }}">
                        {{ $cfg?->tienePdf() ? '📄 ' . $cfg->pdf_nombre_original : 'Sin PDF' }}
                    </span>

                    <a href="{{ route('admin.garantias.marcas.editar', $marca->id_marca) }}"
                       class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                              border border-gray-200 dark:border-gray-600 px-2.5 py-1 rounded-lg
                              hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Configurar
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <script>
    function garantiasAdmin() {
        return {
            async toggleActiva(idMarca, btn) {
                try {
                    const res = await fetch(`/admin/garantias/marcas/${idMarca}/activar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                    });
                    const data = await res.json();
                    if (!data.ok) return;

                    btn.dataset.activa = data.activa ? '1' : '0';
                    btn.classList.toggle('bg-gray-900',    data.activa);
                    btn.classList.toggle('dark:bg-white',  data.activa);
                    btn.classList.toggle('bg-gray-200',   !data.activa);
                    btn.classList.toggle('dark:bg-gray-600', !data.activa);
                    btn.querySelector('span').classList.toggle('translate-x-4', data.activa);
                    btn.querySelector('span').classList.toggle('translate-x-0', !data.activa);
                } catch { /* silencioso */ }
            },
        }
    }
    </script>
</div>
</x-app-layout>