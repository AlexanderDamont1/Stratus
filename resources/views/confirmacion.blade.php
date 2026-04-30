<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de robo</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 w-full max-w-md text-center">

        @if($exito)
        <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Robo confirmado</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ $mensaje }}</p>
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-left space-y-2">
            <div class="flex justify-between">
                <span class="text-xs text-gray-400">Folio</span>
                <span class="text-xs font-mono font-semibold text-gray-900 dark:text-white">{{ $folio }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs text-gray-400">N° de serie</span>
                <span class="text-xs font-mono font-semibold text-gray-900 dark:text-white">{{ $serie }}</span>
            </div>
        </div>
        @else
        <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Enlace inválido</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $mensaje }}</p>
        @endif

        <p class="text-xs text-gray-400 mt-8">ArrowX — CloudLabs</p>
    </div>
</body>
</html>