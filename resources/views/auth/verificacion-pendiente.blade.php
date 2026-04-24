<x-guest-layout>
    <div class="text-center space-y-5">
        <div class="flex justify-center">
            <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30
                        flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-500" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                Verifica tu correo
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Enviamos un enlace de verificación a
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ auth()->user()->correo }}
                </span>
            </p>
        </div>

        @if(session('success'))
            <p class="text-sm text-green-600 dark:text-green-400">
                {{ session('success') }}
            </p>
        @endif

        <form method="POST" action="{{ route('verificacion.reenviar') }}">
            @csrf
            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                       px-4 py-2.5 rounded-lg text-sm font-semibold
                       hover:opacity-90 transition active:scale-[.98]">
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-xs text-gray-400 hover:text-gray-600
                       dark:hover:text-gray-200 transition underline">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>