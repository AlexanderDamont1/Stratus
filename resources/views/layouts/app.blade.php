<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @auth
            @if(auth()->user()->requiereSesionUnica())
                <meta name="session-token" content="{{ session('session_token') }}">
            @endif
        @endauth

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

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900" style="zoom: 0.9;">
            @include('layouts.navigation')

            <main>
                @yield('content')
            </main>
        </div>

        @auth
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    window.__userId       = @json(auth()->user()->id_usuario);
                    window.__sessionToken = @json(session('session_token'));

                    function startListener(retries = 10, delay = 300) {
                        if (typeof Echo === 'undefined' || !Echo || !window.__userId) {
                            if (retries > 0) {
                                setTimeout(() => startListener(retries - 1, delay), delay);
                            } else {
                                console.warn('Echo no disponible.');
                            }
                            return;
                        }

                        const canal = Echo.private(`user.${window.__userId}`);

                        canal.subscribed(() => {
                            console.log('✅ Suscrito a user.' + window.__userId);
                        });

                        canal.error((err) => {
                            console.error('❌ Error canal:', err);
                        });

                        // ✅ Logout manual desde cualquier pestaña
                        canal.listen('.sesion.cerrada', () => {
                            setTimeout(() => window.location.href = '/login', 300);
                        });

                        // ✅ Sesión desplazada por nuevo login en otro dispositivo
                        canal.listen('.session.updated', (e) => {
                            if (window.__sessionToken && e.token !== window.__sessionToken) {
                                setTimeout(() => window.location.href = '/login', 300);
                            }
                        });

                        window.__sesionCanal = canal;
                    }

                    startListener();
                });

                
            </script>
        @endauth
        
        @stack('scripts')
    </body>
</html>
