<x-app-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-4">
        <div class="max-w-sm w-full text-center space-y-6">

            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800
                            flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                    </svg>
                </div>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-2">
                    Periodo de prueba finalizado
                </p>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Gracias por participar
                </h1>
                <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">
                    {{ $negocio->nombre_negocio }}
                </p>
            </div>

            <div class="border border-gray-100 dark:border-gray-700 rounded-xl p-4 text-left space-y-2">
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Tu periodo de prueba de 14 días ha concluido. Si deseas continuar
                    usando el sistema, ponte en contacto con nosotros.
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-xs text-gray-400 hover:text-gray-600
                           dark:hover:text-gray-200 transition underline">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-app-layout>