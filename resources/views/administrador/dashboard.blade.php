<x-app-layout>
<div
    x-data="{
        tokenModal: false,
        cancelModal: false,
        cancelId: null,
        openCancel(id) {
            this.cancelId = id;
            this.cancelModal = true;
        }
    }"
    class="space-y-6"
>
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Inicio</h2>
            <p class="text-xs text-gray-400 mt-0.5">Bienvenido, {{ auth()->user()->nombre_usuario }}</p>
        </div>
        @if(! $enlace)
            <button
                @click="tokenModal = true"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
            >
                + Generar token de enlace
            </button>
        @endif
    </div>

    {{-- ===== TARJETA DE ENLACE ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Estado de enlace</h3>
            @if($enlace)
                <span @class([
                    'text-xs font-medium px-2.5 py-1 rounded-full',
                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' => $enlace->estado === 'pendiente',
                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'    => $enlace->estado === 'activo',
                ])>
                    {{ ucfirst($enlace->estado) }}
                </span>
            @endif
        </div>

        <div class="px-6 py-6">
            @if(! $enlace)
                {{-- Sin enlace --}}
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No tienes ningún enlace activo.</p>
                    <p class="text-xs text-gray-400 mt-1">Genera un token para conectarte con un gestor.</p>
                </div>

            @elseif($enlace->estado === 'pendiente')
                {{-- Token pendiente --}}
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Comparte este token con el gestor para completar el enlace.
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-3 font-mono text-sm text-gray-800 dark:text-gray-200 tracking-widest select-all">
                            {{ $enlace->token_enlace }}
                        </div>
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText('{{ $enlace->token_enlace }}')"
                            class="p-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                            title="Copiar token"
                        >
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="openCancel('{{ $enlace->id_enlace }}')"
                            class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 font-medium"
                        >
                            Cancelar enlace
                        </button>
                    </div>
                </div>

            @elseif($enlace->estado === 'activo')
                {{-- Enlace activo --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $enlace->usuarioDestino->nombre_usuario }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $enlace->usuarioDestino->correo }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="openCancel('{{ $enlace->id_enlace }}')"
                            class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 font-medium"
                        >
                            Cancelar enlace
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== AQUÍ VAN TUS OTRAS SECCIONES ===== --}}

    {{-- ===== MODAL: GENERAR TOKEN ===== --}}
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
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Generar token de enlace</h3>
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
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                Se generará un token único que deberás compartir con el gestor para completar el enlace.
            </p>
            <form method="POST" action="{{ route('enlaces.generar') }}">
                @csrf
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
                        Generar token
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CANCELAR ENLACE ===== --}}
    <div
        x-show="cancelModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="cancelModal = false"
    >
        <div
            x-show="cancelModal"
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
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Cancelar enlace</h3>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                ¿Estás seguro de que deseas cancelar este enlace? El gestor ya no podrá ver tus pedidos.
            </p>
            <form method="POST" :action="`/enlaces/${cancelId}/cancelar`">
                @csrf
                @method('PATCH')
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="cancelModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        No, mantener
                    </button>
                    <button
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                    >
                        Sí, cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>