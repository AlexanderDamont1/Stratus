<x-app-layout>
<div class="space-y-6">

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-0.5">Control de acceso</p>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $negocio->nombre_negocio }}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configura qué módulos puede usar cada rol</p>
        </div>
        <div>
            <a href="{{ route('root.dashboard') }}"
               class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-2 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al dashboard
            </a>
        </div>
    </div>

    {{-- ===== FLASH ===== --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl px-4 py-3 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== MÓDULOS ===== --}}
    <div class="space-y-3">
        @foreach ($estado as $item)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200 group">

            {{-- Cabecera del módulo --}}
            <button
                type="button"
                onclick="toggleModulo(this)"
                class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                data-open="false"
            >
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $item['modulo']->nombre }}</p>
                        @if($item['modulo']->descripcion)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item['modulo']->descripcion }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Pill con conteo de activos --}}
                    @php
                        $activos = collect($item['roles'])->where('activo', true)->count();
                        $total   = count($item['roles']);
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        {{ $activos === $total ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' :
                           ($activos === 0 ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600' :
                           'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800') }}">
                        {{ $activos }}/{{ $total }} activos
                    </span>
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            {{-- Roles (expandible) --}}
            <div class="roles-panel hidden border-t border-gray-100 dark:border-gray-700">
                @foreach ($item['roles'] as $idRol => $rol)
                <div class="flex items-center justify-between px-4 py-3
                            {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">{{ $idRol }}</span>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $rol['nombre'] }}</span>
                    </div>

                    <label class="relative inline-flex items-center gap-3 cursor-pointer select-none">
                        <span class="text-xs estado-label {{ $rol['activo'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-500' }}">
                            {{ $rol['activo'] ? 'Activo' : 'Inactivo' }}
                        </span>
                        <div class="relative">
                            <input
                                type="checkbox"
                                class="sr-only toggle-modulo"
                                {{ $rol['activo'] ? 'checked' : '' }}
                                data-negocio="{{ $negocio->id_negocio }}"
                                data-modulo="{{ $item['modulo']->id_modulo }}"
                                data-rol="{{ $idRol }}"
                            >
                            <div class="toggle-track w-10 h-5 rounded-full border transition-all duration-200
                                        {{ $rol['activo']
                                            ? 'bg-indigo-600 border-indigo-500'
                                            : 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                            </div>
                            <div class="toggle-thumb absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-all duration-200
                                        {{ $rol['activo'] ? 'translate-x-5' : 'translate-x-0' }}">
                            </div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    </div>

</div>

<script>
    // ── Accordion ──────────────────────────────────────────────
    function toggleModulo(btn) {
        const panel   = btn.nextElementSibling;
        const chevron = btn.querySelector('.chevron');
        const isOpen  = btn.dataset.open === 'true';

        if (isOpen) {
            panel.classList.add('hidden');
            chevron.style.transform = '';
            btn.dataset.open = 'false';
        } else {
            panel.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
            btn.dataset.open = 'true';
        }
    }

    // ── Toggles ────────────────────────────────────────────────
    document.querySelectorAll('.toggle-modulo').forEach(checkbox => {
        checkbox.addEventListener('change', async function () {
            const wrapper = this.closest('label');
            const track   = wrapper.querySelector('.toggle-track');
            const thumb   = wrapper.querySelector('.toggle-thumb');
            const label   = wrapper.querySelector('.estado-label');

            // Optimistic UI
            applyToggleUI(track, thumb, label, this.checked);

            try {
                const res = await fetch('{{ route('root.modulos.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_negocio: this.dataset.negocio,
                        id_modulo:  this.dataset.modulo,
                        id_rol:     parseInt(this.dataset.rol),
                        activo:     this.checked,
                    })
                });

                const data = await res.json();

                if (!data.ok) {
                    // Revertir
                    this.checked = !this.checked;
                    applyToggleUI(track, thumb, label, this.checked);
                } else {
                    // Actualizar pill del módulo
                    actualizarPill(this);
                }
            } catch {
                this.checked = !this.checked;
                applyToggleUI(track, thumb, label, this.checked);
            }
        });
    });

    function applyToggleUI(track, thumb, label, activo) {
        if (activo) {
            track.className = track.className.replace('bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600', 'bg-indigo-600 border-indigo-500');
            thumb.classList.remove('translate-x-0');
            thumb.classList.add('translate-x-5');
            label.className = label.className.replace('text-gray-500 dark:text-gray-500', 'text-emerald-600 dark:text-emerald-400');
            label.textContent = 'Activo';
        } else {
            track.className = track.className.replace('bg-indigo-600 border-indigo-500', 'bg-gray-200 dark:bg-gray-700 border-gray-300 dark:border-gray-600');
            thumb.classList.remove('translate-x-5');
            thumb.classList.add('translate-x-0');
            label.className = label.className.replace('text-emerald-600 dark:text-emerald-400', 'text-gray-500 dark:text-gray-500');
            label.textContent = 'Inactivo';
        }
    }

    function actualizarPill(checkbox) {
        const panel   = checkbox.closest('.roles-panel');
        const card    = panel.closest('.group');
        const pill    = card.querySelector('button span:first-of-type'); // el primer span es la pill
        const checks  = panel.querySelectorAll('.toggle-modulo');
        const activos = [...checks].filter(c => c.checked).length;
        const total   = checks.length;

        pill.textContent = `${activos}/${total} activos`;

        // Reset classes
        pill.className = 'text-xs px-2.5 py-1 rounded-full font-medium ';

        if (activos === total) {
            pill.className += 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800';
        } else if (activos === 0) {
            pill.className += 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600';
        } else {
            pill.className += 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800';
        }
    }
</script>
</x-app-layout>