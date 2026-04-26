<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar cuenta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">

    <div class="w-full max-w-sm text-center space-y-6">

        <div class="flex justify-center">
            <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30
                        flex items-center justify-center">
                <svg class="w-7 h-7 text-green-500" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Confirma tu cuenta
            </h2>
            <p class="text-sm text-gray-400 mt-1">
                Haz clic en el botón para activar tu cuenta.
            </p>
        </div>

        <form method="POST" action="{{ route('verificar.email.confirmar', $token) }}">
            @csrf
            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                       px-4 py-2.5 rounded-lg text-sm font-semibold
                       hover:opacity-90 transition active:scale-[.98]">
                Verificar mi cuenta
            </button>
        </form>

        <a href="{{ route('login') }}"
           class="block text-xs text-gray-400 hover:text-gray-600 transition underline">
            Volver al login
        </a>

    </div>

</body>
</html>