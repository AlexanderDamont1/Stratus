<x-app-layout>
<div
    x-data="{
        tokenModal: false,
        desenlazarModal: false,
        desenlazarId: null,
        desenlazarNombre: '',
        openDesenlazar(id, nombre) {
            this.desenlazarId     = id;
            this.desenlazarNombre = nombre;
            this.desenlazarModal  = true;
        }
    }"
    class="space-y-6"
>
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Dashboard</h2>
            <p class="text-xs text-gray-400 mt-0.5">Bienvenido, {{ auth()->user()->nombre_usuario }}</p>
        </div>
        <button
            @click="tokenModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Ingresar token
        </button>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total enlaces</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $enlaces->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Activos</p>
            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $enlaces->where('estado', 'activo')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $enlaces->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $enlaces->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== TABLA DE ENLACES ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Administradores enlazados</h3>
            <span class="text-xs text-gray-400">{{ $enlaces->total() }} total</span>
        </div>

        {{-- PC --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Administrador</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Enlazado el</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($enlaces as $enlace)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                                        <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $enlace->usuarioAdmin->nombre_usuario }}</p>
                                        <p class="text-xs text-gray-400">{{ $enlace->usuarioAdmin->correo }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'text-xs font-medium px-2.5 py-1 rounded-full',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' => $enlace->estado === 'pendiente',
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'  => $enlace->estado === 'activo',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'          => $enlace->estado === 'cancelado',
                                ])>
                                    {{ ucfirst($enlace->estado) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                {{ $enlace->updated_at?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($enlace->estado === 'activo')
                                    <button
                                        type="button"
                                        @click="openDesenlazar('{{ $enlace->id_enlace }}', '{{ $enlace->usuarioAdmin->nombre_usuario }}')"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 font-semibold text-sm"
                                    >
                                        Desenlazar
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No tienes ningún enlace aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MÓVIL --}}
        <div class="block md:hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Administrador</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($enlaces as $enlace)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 h-7 w-7 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-900 dark:text-white">{{ Str::limit($enlace->usuarioAdmin->nombre_usuario, 18) }}</div>
                                        <div class="text-xs text-gray-400">{{ Str::limit($enlace->usuarioAdmin->correo, 20) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span @class([
                                    'text-xs font-medium px-2 py-0.5 rounded-full',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' => $enlace->estado === 'pendiente',
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'  => $enlace->estado === 'activo',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'          => $enlace->estado === 'cancelado',
                                ])>
                                    {{ ucfirst($enlace->estado) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if($enlace->estado === 'activo')
                                    <button
                                        type="button"
                                        @click="openDesenlazar('{{ $enlace->id_enlace }}', '{{ $enlace->usuarioAdmin->nombre_usuario }}')"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 text-xs"
                                    >
                                        Desenlazar
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    <p class="text-xs">No hay enlaces</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enlaces->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $enlaces->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: INGRESAR TOKEN ===== --}}
    <div
        x-show="tokenModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="tokenModal = false"
    >
        <div
            x-show="tokenModal"
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
                    <div class="w-9 h-9 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ingresar token de enlace</h3>
                </div>
                <button
                    @click="tokenModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('enlaces.aceptar') }}">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Token <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="token_enlace"
                        value="{{ old('token_enlace') }}"
                        placeholder="Pega aquí el token del administrador"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition font-mono"
                        required
                        autofocus
                    >
                    @error('token_enlace')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="tokenModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                    >
                        Enlazar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR DESENLAZAR ===== --}}
    <div
        x-show="desenlazarModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="desenlazarModal = false"
    >
        <div
            x-show="desenlazarModal"
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
                    <div class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Desenlazar administrador</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                ¿Deseas desenlazarte de <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="desenlazarNombre"></span>? Ya no podrás ver sus pedidos.
            </p>
            <form method="POST" :action="`/enlaces/${desenlazarId}/cancelar`">
                @csrf
                @method('PATCH')
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="desenlazarModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        No, mantener
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                    >
                        Sí, desenlazar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>