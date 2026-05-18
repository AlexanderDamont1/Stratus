
<div
    x-show="confirmModal"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
    @click.self="confirmModal = false"
>
    <div
        x-show="confirmModal"
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
            <div class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Eliminar registro</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    ¿Seguro que deseas eliminar
                    <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="'«' + confirmNombre + '»'"></span>?
                    Esta acción no se puede deshacer.
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button
                type="button"
                @click="confirmModal = false"
                :disabled="confirmando"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white
                       transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                       disabled:opacity-40 disabled:cursor-not-allowed"
            >
                Cancelar
            </button>
            <button
                type="button"
                @click="ejecutarConfirm()"
                :disabled="confirmando"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold
                       transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2"
            >
                <svg x-show="confirmando" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="confirmando ? 'Eliminando...' : 'Sí, eliminar'"></span>
            </button>
        </div>
    </div>
</div>