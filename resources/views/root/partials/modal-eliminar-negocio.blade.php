{{-- resources/views/root/partials/modal-eliminar-negocio.blade.php --}}
<div x-show="eliminarModal" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-4"
     @click.self="eliminarModal = false"
     @keydown.escape.window="eliminarModal = false">
    <div x-show="eliminarModal"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl overflow-hidden" @click.stop>

        {{-- Header --}}
        <div class="bg-red-950/30 border-b border-red-900/30 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-950/60 border border-red-800/40 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Eliminar negocio permanentemente</h3>
                    <p class="text-xs text-red-400/80 mt-0.5">Esta acción no se puede deshacer</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5">
            <p class="text-sm text-gray-400 mb-4">
                Estás a punto de eliminar
                <span class="font-semibold text-white">{{ $negocio->nombre_negocio }}</span>
                y <strong class="whitespace-nowrap text-red-400">todos sus datos</strong>: usuarios, ventas, bicicletas, inventario, garantías y más.
            </p>

            <div class="bg-gray-950/60 border border-gray-800 rounded-xl p-4 mb-5">
                <p class="text-xs text-gray-500 mb-1">El job correrá en segundo plano. Reporte disponible en:</p>
                <p class="text-xs font-mono text-indigo-400">storage/logs/negocios/</p>
            </div>

            <form action="{{ route('root.negocios.destroy', $negocio->id_negocio) }}" method="POST"
                  @submit.prevent="confirmacion === 'ELIMINAR' ? $el.submit() : null">
                @csrf
                @method('DELETE')

                <label class="block mb-4">
                    <p class="text-xs font-medium text-gray-400 mb-2">
                        Escribe <span class="font-mono font-bold text-red-400">ELIMINAR</span> para confirmar
                    </p>
                    <input x-model="confirmacion" type="text" name="confirmacion" autocomplete="off"
                           placeholder="ELIMINAR"
                           class="w-full bg-gray-950 border rounded-xl px-4 py-2.5 text-sm font-mono text-white placeholder-gray-700 outline-none transition-colors focus:ring-2 focus:ring-red-500/30"
                           :class="confirmacion && confirmacion !== 'ELIMINAR'
                               ? 'border-red-800/60 focus:border-red-700'
                               : 'border-gray-700 focus:border-gray-600'">
                </label>

                <div class="flex gap-2">
                    <button type="button" @click="eliminarModal = false; confirmacion = ''"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-400 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        :disabled="confirmacion !== 'ELIMINAR'"
                        class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl transition-all duration-150
                               disabled:opacity-30 disabled:cursor-not-allowed
                               bg-red-600 hover:bg-red-500 text-white border border-red-500
                               disabled:bg-gray-800 disabled:border-gray-700 disabled:text-gray-500">
                        Eliminar negocio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>