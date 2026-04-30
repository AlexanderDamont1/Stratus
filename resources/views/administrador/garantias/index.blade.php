{{-- resources/views/admin/garantias/index.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-7" x-data="garantiasAdmin()">

    {{-- Flash --}}
    <x-flash-messages />


    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Garantías</h2>
            <p class="text-xs text-gray-400 mt-0.5">Configura las garantías por marca y gestiona reclamos</p>
        </div>
        <div class="flex gap-2">
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
            <a href="{{ route('admin.garantias.marcas') }}"
               class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md
                      text-sm hover:opacity-90 transition flex items-center gap-2 hover:scale-105
                      transform duration-200 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Configurar marcas
            </a>
        </div>
    </div>

    {{-- Política de reemplazo del negocio --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                    Política de reemplazo de componentes
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Define qué garantía recibe un componente reemplazado
                </p>
            </div>
            <button @click="politicaModal = true"
                    class="text-xs border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-lg
                           text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700
                           transition shrink-0">
                Editar
            </button>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-3">
            @php
                $politica = $config_negocio?->politica_reemplazo ?? 'mini';
                $dias     = $config_negocio?->mini_garantia_dias ?? 7;
            @endphp

            @foreach([
                ['key' => 'heredar', 'label' => 'Heredar',  'desc' => 'Tiempo restante original'],
                ['key' => 'nueva',   'label' => 'Nueva',    'desc' => 'Duración completa del componente'],
                ['key' => 'mini',    'label' => 'Mini',     'desc' => $dias . ' días (configurable)'],
            ] as $op)
            <div class="rounded-lg border p-3 text-center transition
                {{ $politica === $op['key']
                    ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white'
                    : 'border-gray-200 dark:border-gray-600' }}">
                <p class="text-sm font-semibold
                    {{ $politica === $op['key'] ? 'text-white dark:text-gray-900' : 'text-gray-700 dark:text-gray-300' }}">
                    {{ $op['label'] }}
                </p>
                <p class="text-[10px] mt-0.5
                    {{ $politica === $op['key'] ? 'text-gray-300 dark:text-gray-600' : 'text-gray-400' }}">
                    {{ $op['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Marcas y estado de garantía --}}
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
                $cfg        = $configs[$marca->id_marca] ?? null;
                $activa     = $cfg?->activa ?? false;
                $estado     = $cfg?->estado_procesamiento ?? 'sin_pdf';
                $numDefs    = $cfg?->componenteDefs->where('excluido', false)->count() ?? 0;

                $estadoLabel = match($estado) {
                    'sin_pdf'    => 'Sin póliza',
                    'pendiente'  => 'Procesando...',
                    'procesando' => 'Procesando...',
                    'completado' => $numDefs . ' componentes',
                    'error'      => 'Error al procesar',
                    default      => '—',
                };
                $estadoColor = match($estado) {
                    'completado' => 'text-green-600 dark:text-green-400',
                    'error'      => 'text-red-600 dark:text-red-400',
                    'pendiente',
                    'procesando' => 'text-yellow-600 dark:text-yellow-400',
                    default      => 'text-gray-400',
                };
            @endphp

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                        rounded-xl p-4 hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">

                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ $marca->nombre_marca }}
                        </p>
                        <p class="text-xs {{ $estadoColor }} mt-0.5">{{ $estadoLabel }}</p>
                    </div>

                    {{-- Toggle activa --}}
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

    {{-- ── MODAL: Política de reemplazo ── --}}
    <div x-show="politicaModal" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[2px] flex items-center justify-center z-50 px-4"
         @click.self="politicaModal = false">

        <div x-show="politicaModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>

            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                Política de reemplazo
            </h3>

            <div class="space-y-2 mb-4">
                <template x-for="op in politicaOpciones" :key="op.key">
                    <button type="button" @click="politicaSeleccionada = op.key"
                            :class="politicaSeleccionada === op.key
                                ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700'
                                : 'border-gray-200 dark:border-gray-600'"
                            class="w-full text-left border rounded-lg px-4 py-3 transition">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="op.label"></p>
                        <p class="text-xs text-gray-400" x-text="op.desc"></p>
                    </button>
                </template>
            </div>

            {{-- Mini días --}}
            <div x-show="politicaSeleccionada === 'mini'" class="mb-4">
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Días de garantía mini
                </label>
                <input type="number" x-model.number="miniDias" min="1" max="90"
                       class="w-full border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                              focus:outline-none focus:ring-1 focus:ring-gray-400">
            </div>

            <div class="flex justify-end gap-2">
                <button @click="politicaModal = false"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400
                               rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    Cancelar
                </button>
                <button @click="guardarPolitica()"
                        :disabled="guardandoPolitica"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                               rounded-lg text-sm font-semibold hover:opacity-90 transition
                               disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                    <span x-show="!guardandoPolitica">Guardar</span>
                    <span x-show="guardandoPolitica">Guardando...</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    function garantiasAdmin() {
        return {
            politicaModal:        false,
            guardandoPolitica:    false,
            politicaSeleccionada: '{{ $config_negocio?->politica_reemplazo ?? "mini" }}',
            miniDias:             {{ $config_negocio?->mini_garantia_dias ?? 7 }},
            flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

            politicaOpciones: [
                { key: 'heredar', label: 'Heredar',  desc: 'El componente nuevo hereda el tiempo restante de garantía' },
                { key: 'nueva',   label: 'Nueva',    desc: 'El componente nuevo recibe la duración completa original' },
                { key: 'mini',    label: 'Mini',     desc: 'Garantía corta configurable (ideal para reemplazos en garantía)' },
            ],

            flash(msg, tipo = 'success') {
                this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
                clearTimeout(this.flashTimer);
                this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
            },

            async toggleActiva(idMarca, btn) {
                const activa = btn.dataset.activa === '1';
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
                    if (!data.ok) { this.flash('Error al cambiar estado.', 'error'); return; }

                    // Actualizar UI sin reload
                    btn.dataset.activa = data.activa ? '1' : '0';
                    btn.classList.toggle('bg-gray-900', data.activa);
                    btn.classList.toggle('dark:bg-white', data.activa);
                    btn.classList.toggle('bg-gray-200', !data.activa);
                    btn.classList.toggle('dark:bg-gray-600', !data.activa);
                    btn.querySelector('span').classList.toggle('translate-x-4', data.activa);
                    btn.querySelector('span').classList.toggle('translate-x-0', !data.activa);

                    this.flash(data.activa ? 'Garantía activada.' : 'Garantía desactivada.');
                } catch { this.flash('Error de conexión.', 'error'); }
            },

            async guardarPolitica() {
                this.guardandoPolitica = true;
                try {
                    const res = await fetch('{{ route("admin.garantias.politica") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type':     'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':           'application/json',
                        },
                        body: JSON.stringify({
                            politica_reemplazo: this.politicaSeleccionada,
                            mini_garantia_dias: this.miniDias,
                        }),
                    });
                    const data = await res.json();
                    if (!data.ok) { this.flash(data.mensaje ?? 'Error.', 'error'); return; }
                    this.politicaModal = false;
                    this.flash('Política guardada.');
                    setTimeout(() => window.location.reload(), 1000);
                } catch { this.flash('Error de conexión.', 'error'); }
                finally { this.guardandoPolitica = false; }
            },
        }
    }
    </script>
</div>
</x-app-layout>