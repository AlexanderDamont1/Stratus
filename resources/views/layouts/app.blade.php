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
        <link rel="icon" href="{{ asset('arrowk/favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">

        {{-- ── Contenido principal con zoom ─────────────────────── --}}
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900" style="zoom: 0.87;">
            @include('layouts.navigation')

            <main>
                @yield('content')
            </main>
        </div>

        {{-- ── Todo lo que necesita cubrir el viewport real ──────── --}}
        {{-- (fuera del zoom para que fixed inset-0 funcione bien)   --}}

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

                        canal.error((err) => {
                            console.error('❌ Error canal:', err);
                        });

                        canal.listen('.sesion.cerrada', () => {
                            setTimeout(() => window.location.href = '/login', 300);
                        });

                        canal.listen('.session.updated', (e) => {
                            if (window.__sessionToken && e.token !== window.__sessionToken) {
                                setTimeout(() => window.location.href = '/login', 300);
                            }
                        });

                        window.__sesionCanal = canal;

                        @if(auth()->user()->id_negocio && auth()->user()->id_rol !== 0)
                            const canalNegocio = Echo.channel(
                                `negocio.{{ auth()->user()->id_negocio }}`
                            );

                            canalNegocio.listen('.negocio.expirado', (e) => {
                                const overlay = document.getElementById('trial-expired-overlay');
                                if (overlay) {
                                    overlay._x_dataStack?.[0]
                                        ? (overlay._x_dataStack[0].visible = true,
                                           overlay._x_dataStack[0].redirect = e.redirect)
                                        : null;
                                    setTimeout(() => window.location.href = e.redirect, 3000);
                                } else {
                                    setTimeout(() => window.location.href = e.redirect, 1500);
                                }
                            });
                        @endif
                    }

                    startListener();
                });
            </script>

            {{-- Overlay de expiración de trial/suscripción --}}
            @if(auth()->user()->id_negocio && auth()->user()->id_rol !== 0)
            <div
                id="trial-expired-overlay"
                x-data="{ visible: false, redirect: '' }"
                x-show="visible"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]"
            >
                <div
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6
                           max-w-sm w-full text-center space-y-4 mx-4"
                >
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-900/30
                                flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6 text-amber-500" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                        </svg>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            Tu periodo ha finalizado
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Serás redirigido en unos segundos...
                        </p>
                    </div>

                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-0.5 overflow-hidden">
                        <div
                            x-show="visible"
                            x-transition:enter="transition-none"
                            style="width: 100%; transition: width 3s linear;"
                            x-init="$watch('visible', v => {
                                if (v) $nextTick(() => $el.style.width = '0%')
                            })"
                            class="bg-amber-400 h-0.5 rounded-full"
                        ></div>
                    </div>
                </div>
            </div>
            @endif
        @endauth

        @stack('scripts')
    </body>
</html>