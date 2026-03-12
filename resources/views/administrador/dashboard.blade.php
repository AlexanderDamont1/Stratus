<x-app-layout>
<div
    x-data="{
        tokenModal: false,
        cancelModal: false,
        cancelId: null,
        enlaceEstado: '{{ $enlace->estado ?? '' }}',
        enlaceCancelado: false,
        enlaceGestor: '{{ $enlace->usuarioDestino->nombre_usuario ?? '' }}',
        enlaceGestorCorreo: '{{ $enlace->usuarioDestino->correo ?? '' }}',
        enlaceToken: '{{ $enlace->token_enlace ?? '' }}',
        enlaceId: '{{ $enlace->id_enlace ?? '' }}',
        openCancel(id) {
            this.cancelId = id;
            this.cancelModal = true;
        },
        init() {
            if (!window.Echo) return;

            window.Echo.private(`enlace-vendedor.{{ auth()->user()->id_usuario }}`)
                .listen('.enlace.updated', (e) => {
                    const action = e.action ?? null;
                    const enlace = e.enlace ?? null;

                    if (!enlace) return;

                    if (action === 'aceptado') {
                        this.enlaceCancelado = false;
                        this.enlaceEstado = 'activo';
                        this.enlaceGestor = enlace.gestor;
                        this.enlaceGestorCorreo = enlace.gestor_correo;
                        this.enlaceId = enlace.id_enlace;
                    } else if (action === 'cancelado') {
                        this.enlaceCancelado = true;
                        this.enlaceEstado = '';
                        this.enlaceGestor = '';
                        this.enlaceGestorCorreo = '';
                        this.enlaceToken = '';
                        this.enlaceId = '';
                    }
                });
        }
    }"
    class="space-y-6"
>
    {{-- ===== MENSAJE FLASH ===== --}}
    @if(session('success'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 animate-fade-in"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-xl ring-1 ring-gray-200 min-w-[300px] max-w-md">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Inicio</h2>
            <p class="text-xs text-gray-400 mt-0.5">Bienvenido, {{ auth()->user()->nombre_usuario }}</p>
        </div>
        {{-- Botón solo visible si no hay enlace Y no fue cancelado por el gestor --}}
        <button
            x-show="!enlaceEstado && !enlaceCancelado"
            @click="tokenModal = true"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Generar token de enlace
        </button>
    </div>

    {{-- ===== TARJETA DE ENLACE ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Estado de enlace</h3>
            <template x-if="enlaceEstado">
                <span
                    :class="{
                        'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800': enlaceEstado === 'pendiente',
                        'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border-green-200 dark:border-green-800': enlaceEstado === 'activo',
                    }"
                    class="inline-flex items-center text-xs px-2.5 py-1 rounded-full border">
                    <span x-show="enlaceEstado === 'activo'" class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block mr-1.5"></span>
                    <span x-text="enlaceEstado.charAt(0).toUpperCase() + enlaceEstado.slice(1)"></span>
                </span>
            </template>
            <template x-if="enlaceCancelado">
                <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full border bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 border-red-200 dark:border-red-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block mr-1.5"></span>
                    Desconectado
                </span>
            </template>
        </div>

        <div class="px-6 py-6">

            {{-- Gestor terminó la conexión --}}
            <template x-if="enlaceCancelado">
                <div class="flex flex-col items-center justify-center py-8 text-center space-y-3">
                    <div class="h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            El gestor terminó la conexión
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Por favor comunícate con tu Gestor de ventas.
                        </p>
                    </div>
                </div>
            </template>

            {{-- Sin enlace (estado limpio) --}}
            <template x-if="!enlaceEstado && !enlaceCancelado">
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No tienes ningún enlace activo.</p>
                    <p class="text-xs text-gray-400 mt-1">Genera un token para conectarte con un gestor.</p>
                </div>
            </template>

            {{-- Token pendiente --}}
            <template x-if="enlaceEstado === 'pendiente'">
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Comparte este token con el gestor para completar el enlace.
                    </p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 sm:px-4 py-2.5 sm:py-3 font-mono text-[11px] sm:text-sm text-gray-800 dark:text-gray-200 tracking-widest select-all border border-gray-200 dark:border-gray-600 break-all"
                            x-text="enlaceToken">
                        </div>
                        <button
                            type="button"
                            x-data="{ copied: false }"
                            @click="
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(enlaceToken).then(() => {
                                        copied = true;
                                        setTimeout(() => copied = false, 1800);
                                    });
                                } else {
                                    const el = document.createElement('textarea');
                                    el.value = enlaceToken;
                                    el.setAttribute('readonly', '');
                                    el.style.position = 'absolute';
                                    el.style.left = '-9999px';
                                    document.body.appendChild(el);
                                    el.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(el);
                                    copied = true;
                                    setTimeout(() => copied = false, 1800);
                                }
                            "
                            class="shrink-0 transition p-2 sm:p-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg"
                            :class="copied ? 'text-green-500 border-green-200 dark:border-green-800' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                        >
                            <span x-show="!copied">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span x-show="copied" x-cloak>
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="openCancel(enlaceId)"
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs transition"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </template>

            {{-- Enlace activo --}}
            <template x-if="enlaceEstado === 'activo'">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0 border border-green-200 dark:border-green-800">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="enlaceGestor"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="enlaceGestorCorreo"></p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="openCancel(enlaceId)"
                            class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-xs transition"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

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
                    <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Generar token de enlace</h3>
                </div>
                <button @click="tokenModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-400 mb-5 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Se generará un token único que deberás compartir con el gestor para completar el enlace.
            </p>
            <form method="POST" action="{{ route('enlaces.generar') }}">
                @csrf
                <div class="flex justify-end gap-2">
                    <button type="button" @click="tokenModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
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
            <div class="flex items-start gap-4 mb-5">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Cancelar enlace</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        ¿Estás seguro de que deseas cancelar este enlace? El gestor ya no podrá ver tus pedidos.
                    </p>
                </div>
            </div>
            <form method="POST" :action="`/enlaces/${cancelId}/cancelar`">
                @csrf
                @method('PATCH')
                <div class="flex justify-end gap-2">
                    <button type="button" @click="cancelModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        No, mantener
                    </button>
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition active:scale-[.98]">
                        Sí, cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>