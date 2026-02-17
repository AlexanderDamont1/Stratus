<x-app-layout>

<div
    x-data="{ createModal: false }"
    class="space-y-6"
>

    {{-- ===== FLASH ===== --}}
    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== HEADER ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Panel Root</h2>
            <p class="text-xs text-gray-400 mt-0.5">Los links expiran en 24h y mueren al usarse.</p>
        </div>
        <button
            @click="createModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Crear link
        </button>
    </div>

    {{-- ===== STATS ===== --}}
    @php
        $totalLinks    = $links->total();
        $disponibles   = \App\Models\RegistroLink::disponibles()->count();
        $totalNegocios = $negocios->total();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Links totales</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalLinks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Disponibles</p>
            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $disponibles }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Negocios</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalNegocios }}</p>
        </div>
    </div>

    {{-- ===== TABLA LINKS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
        <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Links de registro</h3>
            <span class="text-xs text-gray-400">{{ $links->total() }} total</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">URL</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Vendedores</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Expira</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Estado</th>
                    <th class="px-4 py-2 text-right text-xs text-gray-500 dark:text-gray-400 font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                    @php
                        $expirado   = $link->expires_at && now()->greaterThan($link->expires_at);
                        $disponible = ! $link->usado && ! $expirado;
                        $url        = route('registro.show', $link->token);
                    @endphp
                    <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3">
                            @if($disponible)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[220px] font-mono">{{ $url }}</span>
                                    <button
                                        onclick="copiar('{{ $url }}', this)"
                                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white text-xs transition shrink-0"
                                        title="Copiar URL"
                                    >⎘</button>
                                </div>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 font-mono">{{ Str::limit($link->token, 24) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $link->max_users }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $link->expires_at ? $link->expires_at->diffForHumans() : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($link->usado)
                                <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">
                                    Usado
                                </span>
                            @elseif($expirado)
                                <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800">
                                    Expirado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                    Activo
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('root.links.destroy', $link) }}" onsubmit="return confirm('¿Eliminar este link?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs transition">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                            No hay links generados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($links->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700">
                {{ $links->links() }}
            </div>
        @endif
    </div>

    {{-- ===== TABLA NEGOCIOS ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
        <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Negocios registrados</h3>
            <span class="text-xs text-gray-400">{{ $negocios->total() }} total</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">ID</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Negocio</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Admin</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Límite vendedores</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 dark:text-gray-400 font-medium">Creado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($negocios as $negocio)
                    <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <td class="px-4 py-3 text-xs font-mono text-gray-400">{{ $negocio->id_negocio }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $negocio->nombre_negocio }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $negocio->admin?->correo ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $negocio->max_users }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ $negocio->created_at->format('d/m/y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Ningún negocio registrado aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($negocios->hasPages())
            <div class="px-4 py-3 border-t dark:border-gray-700">
                {{ $negocios->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL CREAR LINK ===== --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        @click.self="createModal = false"
    >
        <div
            x-show="createModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-sm mx-4"
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo link de registro</h3>
                <button @click="createModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('root.links.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1.5">
                        Límite de vendedores
                    </label>
                    <input
                        type="number"
                        name="max_users"
                        min="1"
                        max="100"
                        value="{{ old('max_users', 1) }}"
                        placeholder="ej. 5"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                        autofocus
                    >
                    @error('max_users')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1.5">El link expira en 24h y muere al ser usado.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="createModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
                    >
                        Crear link
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function copiar(url, btn) {
        navigator.clipboard.writeText(url).then(() => {
            const original = btn.textContent;
            btn.textContent = '✓';
            btn.classList.add('text-green-500');
            setTimeout(() => {
                btn.textContent = original;
                btn.classList.remove('text-green-500');
            }, 1500);
        });
    }
</script>

</x-app-layout>