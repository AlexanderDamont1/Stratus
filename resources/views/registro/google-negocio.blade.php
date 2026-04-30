<x-guest-layout>
    {{-- Línea decorativa superior negra --}}
    <div class="h-1.5 bg-gray-900 dark:bg-white rounded-t-lg -mt-6 -mx-6 mb-6"></div> {{-- Aumentado mb-4 a mb-6 --}}

    <div class="text-center">
        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mx-auto mb-3"> {{-- mb-2 a mb-3 --}}
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Un último paso</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"> {{-- mt-0.5 a mt-1 --}}
            Hola <span class="font-medium text-gray-700 dark:text-gray-200">
                {{ session('google_registro.nombre') }}
            </span>, ¿cómo se llama tu negocio?
        </p>
    </div>

    <form method="POST" action="{{ route('registro.google.negocio.store') }}" class="mt-6 space-y-4" id="registerForm"> {{-- mt-4 a mt-6, space-y-3 a space-y-4 --}}
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1"> {{-- mb-0.5 a mb-1 --}}
                Nombre del negocio
            </label>
            <input type="text" name="nombre_negocio" value="{{ old('nombre_negocio') }}" autofocus
                placeholder="Ej. Bicicletas García"
                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 {{-- py-1.5 a py-2 --}}
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                       focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('nombre_negocio')
                <p class="text-[10px] text-red-500 mt-1 flex items-center gap-1"> {{-- mt-0.5 a mt-1 --}}
                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit" id="submitBtn"
            class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold {{-- text-xs a text-sm --}}
                   rounded-lg py-2.5 px-4 hover:opacity-90 transition active:scale-[0.97] 
                   flex items-center justify-center gap-2">
            <span id="btnText">Crear mi cuenta</span>
            <svg id="btnIcon" class="w-4 h-4 text-white/70 dark:text-gray-900/70 group-hover:translate-x-0.5 transition-transform" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg id="btnSpinner" class="animate-spin h-4 w-4 text-current hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </button>
    </form>

    <p class="text-[10px] text-gray-400 dark:text-gray-500 text-center mt-5"> {{-- mt-4 a mt-5 --}}
        Puedes cambiarlo más tarde
    </p>
</x-guest-layout>

<script>
    // Prevenir envíos múltiples y mostrar spinner
    (function() {
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const btnSpinner = document.getElementById('btnSpinner');

        if (form && btn) {
            form.addEventListener('submit', function(e) {
                // Deshabilitar botón para evitar doble clic
                if (btn.disabled) {
                    e.preventDefault();
                    return;
                }
                btn.disabled = true;
                // Ocultar texto e ícono, mostrar spinner
                btnText.classList.add('opacity-0');
                btnIcon.classList.add('hidden');
                btnSpinner.classList.remove('hidden');
                // Opcional: cambiar un poco el estilo visual
                btn.classList.add('opacity-70', 'cursor-not-allowed');
            });
        }
    })();
</script>