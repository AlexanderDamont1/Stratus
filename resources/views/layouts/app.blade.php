<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <!-- Scripts (Vite compila app.js y app.css) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased [zoom:0.9]">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            {{-- aquí va el contenido de cada vista --}}
            <main>
                @yield('content')
            </main>
        </div>

        {{-- Script global para cerrar sesión en tiempo real --}}
        @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // Valores seguros inyectados desde PHP
                window.userId = @json(auth()->user()->id_usuario);
                window.sessionToken = @json(session('session_token'));

                // Comprobar que Echo esté disponible (Vite puede tardar en inicializar)
                function startListener(retries = 10, delay = 300) {
                    if (typeof Echo !== 'undefined' && Echo && window.userId) {

                        Echo.private(`user.${window.userId}`)
                            .listen('.session.updated', (e) => {

                                // Si el token local no coincide con el token enviado → cerrar sesión
                                if (window.sessionToken !== e.token) {

                                    fetch('/logout', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document
                                                .querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({})
                                    }).finally(() => {
                                        // redirigir al login (evita problemas si fetch falla)
                                        window.location.href = "/login";
                                    });
                                }

                            });

                        return;
                    }

                    if (retries > 0) {
                        setTimeout(() => startListener(retries - 1, delay), delay);
                    } else {
                        console.warn('Echo no disponible: no fue posible inicializar el listener de sesión.');
                    }
                }

                startListener();
            });
        </script>
        @endauth

        {{-- espacio para que otras vistas apilen scripts --}}
        @stack('scripts')
    </body>
</html>
