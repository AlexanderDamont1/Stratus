<x-guest-layout>
    <div class="space-y-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Un último paso
            </h2>
            <p class="text-sm text-gray-400 mt-0.5">
                Hola <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ session('google_registro.nombre') }}
                </span>, ¿cómo se llama tu negocio?
            </p>
        </div>

        <form method="POST" action="{{ route('registro.google.negocio.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Nombre del negocio
                </label>
                <input
                    type="text"
                    name="nombre_negocio"
                    value="{{ old('nombre_negocio') }}"
                    autofocus
                    class="w-full border border-gray-300 dark:border-gray-600
                           dark:bg-gray-700 dark:text-white rounded-lg
                           px-3.5 py-2.5 text-sm focus:outline-none
                           focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                    placeholder="Ej. Bicicletas García"
                >
                @error('nombre_negocio')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                       px-4 py-2.5 rounded-lg text-sm font-semibold
                       hover:opacity-90 transition active:scale-[.98]">
                Crear mi cuenta
            </button>
        </form>
    </div>
</x-guest-layout>