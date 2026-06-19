<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900
             @if($wide ?? false) {{ $wideBodyClass ?? 'min-h-[100svh] flex items-center justify-center' }} @endif">

    @if($wide ?? false)
        {{ $slot }}
    @else
        <div class="min-h-[100svh] flex items-center justify-center px-4">
            <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg flex flex-col justify-center">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-6 py-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @endif

    @stack('scripts')
</body>
</html>