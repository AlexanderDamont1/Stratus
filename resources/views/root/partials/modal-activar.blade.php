{{-- resources/views/root/partials/modal-activar.blade.php --}}
<div x-show="activarModal" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
     @click.self="activarModal = false">
    <div x-show="activarModal"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm" @click.stop>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Activar suscripción</h3>
        <p class="text-xs text-gray-400 mb-4">{{ $negocio->nombre_negocio }}</p>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Días de suscripción</label>
        <input type="number" x-model.number="dias" min="1" max="365"
               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
        <div class="flex justify-end gap-2 mt-4">
            <button @click="activarModal = false"
                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button @click="
                fetch('{{ route('root.negocios.activar', $negocio->id_negocio) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ dias })
                }).then(r => r.json()).then(d => { if (d.ok) activarModal = false; })"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                Confirmar
            </button>
        </div>
    </div>
</div>