<x-app-layout>
<div
    x-data="{
        createModal: false,
        deleteModal: false,
        deleteToken: '',
        deleteAction: '',
        openDelete(token, action) {
            this.deleteToken  = token;
            this.deleteAction = action;
            this.deleteModal  = true;
        }
    }"
    class="space-y-6"
>
    {{-- ===== MENSAJE FLASH ===== --}}
    @if(session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-md bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== ENCABEZADO ===== --}}
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

    {{-- ===== ESTADÍSTICAS ===== --}}
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

    {{-- ===== TABLA DE LINKS ===== --}}
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
                        $deleteRoute = route('root.links.destroy', $link);
                    @endphp
                    <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        {{-- URL + Botón copiar --}}
                        <td class="px-4 py-3">
                            @if($disponible)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[180px] sm:max-w-[220px] font-mono">
                                        {{ $url }}
                                    </span>
                                    <button
                                        type="button"
                                        x-data="{ copied: false }"
                                        @click="
                                            const url = '{{ $url }}';
                                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                                navigator.clipboard.writeText(url).then(() => {
                                                    copied = true;
                                                    setTimeout(() => copied = false, 1800);
                                                });
                                            } else {
                                                const el = document.createElement('textarea');
                                                el.value = url;
                                                el.setAttribute('readonly', '');
                                                el.style.position = 'absolute';
                                                el.style.left = '-9999px';
                                                document.body.appendChild(el);
                                                el.select();
                                                el.setSelectionRange(0, 99999);
                                                document.execCommand('copy');
                                                document.body.removeChild(el);
                                                copied = true;
                                                setTimeout(() => copied = false, 1800);
                                            }
                                        "
                                        class="shrink-0 transition"
                                        :class="copied ? 'text-green-500' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                        title="Copiar URL"
                                    >
                                        <span x-show="!copied">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </span>
                                        <span x-show="copied" x-cloak>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 font-mono">
                                    {{ Str::limit($link->token, 24) }}
                                </span>
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
                            <button
                                type="button"
                                @click="openDelete('{{ Str::limit($link->token, 16) }}…', '{{ $deleteRoute }}')"
                                class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs transition"
                            >
                                Eliminar
                            </button>
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

    {{-- ===== TABLA DE NEGOCIOS ===== --}}
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

    {{-- ===== MODAL: CREAR LINK ===== --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="createModal = false"
    >
        <div
            x-show="createModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo link de registro</h3>
                </div>
                <button
                    @click="createModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('root.links.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Límite de vendedores
                    </label>
                    <input
                        type="number"
                        name="max_users"
                        min="1"
                        max="100"
                        value="{{ old('max_users', 1) }}"
                        placeholder="ej. 5"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition"
                        required
                        autofocus
                    >
                    @error('max_users')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Expira en 24h y se destruye al usarse.
                    </p>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="createModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition"
                    >
                        Crear link
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR ELIMINACIÓN ===== --}}
    <div
        x-show="deleteModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="deleteModal = false"
    >
        <div
            x-show="deleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar link</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vas a eliminar el link
                        <span class="font-mono text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="deleteToken"></span>.
                        Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <form method="POST" :action="deleteAction">
                @csrf
                @method('DELETE')

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition active:scale-[.98]"
                    >
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>