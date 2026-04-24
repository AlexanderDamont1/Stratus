<x-app-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-4">
        <div class="max-w-sm w-full text-center space-y-6">

            <div class="flex justify-center">
                <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-900/20
                            flex items-center justify-center">
                    <svg class="w-7 h-7 text-amber-400" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>

            <div>
                <p class="text-xs font-medium text-amber-500 uppercase tracking-widest mb-2">
                    Suscripción vencida
                </p>
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Tu suscripción ha terminado
                </h1>
                <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">
                    {{ $negocio->nombre_negocio }}
                </p>
            </div>

            <div class="border border-amber-100 dark:border-amber-900/30
                        bg-amber-50 dark:bg-amber-900/10 rounded-xl p-4 text-left">
                <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                    Tu suscripción venció el
                    <span class="font-semibold">
                        {{ $negocio->subscribed_until?->format('d/m/Y') }}
                    </span>.
                    Para renovar y recuperar el acceso completo, contáctanos.
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