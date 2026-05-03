{{-- resources/views/root/partials/modal-eliminar-negocio.blade.php --}}
<div x-show="eliminarModal" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
     @click.self="eliminarModal = false">
    <div x-show="eliminarModal"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <div class="flex items-start gap-4 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eliminar negocio</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Esta acción eliminará permanentemente <strong>{{ $negocio->nombre_negocio }}</strong> y todos sus datos.
                </p>
            </div>
        </div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            Escribe <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded">ELIMINAR</span> para confirmar
        </label>
        <input type="text" x-model="confirmacion" autocomplete="off" placeholder="ELIMINAR"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 mb-5">
        <div class="flex justify-center gap-3">
            <button @click="eliminarModal = false; confirmacion = ''"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button @click="if (confirmacion === 'ELIMINAR') {
                        fetch('{{ route('root.negocios.destroy', $negocio->id_negocio) }}', {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        }).then(r => r.json()).then(d => { if (d.ok || d.success) location.reload(); else alert('Error'); });
                    }"
                    :disabled="confirmacion !== 'ELIMINAR'"
                    :class="confirmacion === 'ELIMINAR' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                Eliminar definitivamente
            </button>
        </div>
    </div>
</div>