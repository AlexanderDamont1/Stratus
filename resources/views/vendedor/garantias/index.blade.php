{{-- resources/views/vendedor/garantias/index.blade.php --}}
<x-app-layout>
<div class="mx-auto max-w-lg" x-data="buscadorGarantia()">

    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Garantías</h2>
        <p class="text-xs text-gray-400 mt-0.5">Busca una bicicleta por número de serie</p>
    </div>

    {{-- Buscador --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
            Número de serie
        </label>

        <div class="flex gap-2">
            <input
                type="text"
                x-model="numSerie"
                @keydown.enter="buscar()"
                placeholder="Ej. TEST000000000001"
                maxlength="60"
                class="flex-1 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-1 focus:ring-gray-400 font-mono tracking-wide uppercase"
                @input="numSerie = $event.target.value.toUpperCase()">

            <button
                @click="buscar()"
                :disabled="buscando || !numSerie.trim()"
                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-5 py-2.5 rounded-lg
                       text-sm font-medium hover:opacity-90 transition disabled:opacity-40
                       disabled:cursor-not-allowed active:scale-95 flex items-center gap-2">
                <svg x-show="!buscando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <svg x-show="buscando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="buscando ? 'Buscando...' : 'Buscar'"></span>
            </button>
        </div>

        {{-- Error --}}
        <div x-show="error" x-cloak
             class="mt-3 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span x-text="error"></span>
        </div>
    </div>

    <script>
    function buscadorGarantia() {
        return {
            numSerie: '',
            buscando: false,
            error:    '',

            async buscar() {
                this.error = '';
                const serie = this.numSerie.trim();
                if (!serie) return;

                this.buscando = true;
                try {
                    const res = await fetch(`{{ route('garantias.buscar') }}?num_serie=${encodeURIComponent(serie)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();

                    if (!data.ok) {
                        this.error = data.mensaje;
                        return;
                    }

                    window.location.href = data.redirect;

                } catch {
                    this.error = 'Error de conexión.';
                } finally {
                    this.buscando = false;
                }
            }
        }
    }
    </script>
</div>
</x-app-layout>