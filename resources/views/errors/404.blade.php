<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página no encontrada</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 min-h-[100svh] flex items-center justify-center px-6">

    <div class="w-full max-w-sm text-center space-y-6">
        <p class="text-6xl font-bold text-gray-900 dark:text-white tracking-tight">404</p>

        <div class="space-y-2">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Página no encontrada</h1>
            <p class="text-sm text-gray-400 leading-relaxed">
                La página que buscas no existe o fue movida.
            </p>
        </div>

        <a href="{{ url('/') }}"
           class="inline-flex items-center justify-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900
                  text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition active:scale-[.98]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Volver al inicio
        </a>
    </div>

</body>
</html>
