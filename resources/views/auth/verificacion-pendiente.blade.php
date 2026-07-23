<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifica tu correo</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.google-analytics')
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 min-h-[100svh] flex items-center justify-center px-6">

    <div class="w-full max-w-4xl mx-auto py-10">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center space-y-8 lg:space-y-0">

            {{-- ── Columna izquierda ── --}}
            <div class="text-center lg:text-left space-y-6">

                <div class="flex justify-center lg:justify-start">
                    <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                         class="h-14 w-auto object-contain dark:hidden" />
                    <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK"
                         class="h-14 w-auto object-contain hidden dark:block" />
                </div>

                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                             bg-yellow-100 text-yellow-800
                             dark:bg-yellow-800/30 dark:text-yellow-400">
                    Verificación requerida
                </span>

                <div class="space-y-2">
                    <h1 class="text-3xl font-light text-gray-900 dark:text-white leading-tight">
                        Verifica tu<br>correo electrónico
                    </h1>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Te enviamos un enlace de activación.
                    </p>
                </div>

                <div class="flex items-center gap-3 justify-center lg:justify-start">
                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                                flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <div class="min-w-0 text-left">
                        <p class="text-xs text-gray-400">Enviado a</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                            {{ Auth::user()->correo }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xs text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>

            {{-- ── Columna derecha ── --}}
            <div x-data="vpSetup()">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                            dark:border-gray-700 shadow-sm overflow-hidden">

                    {{-- Pasos --}}
                    <div class="px-6 py-6 space-y-4">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">
                            Pasos a seguir
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30
                                             flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    Revisa tu bandeja de entrada (incluye spam)
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/30
                                             flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5l-7 7 7 7"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    Haz clic en el enlace de verificación
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30
                                             flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    Accede al panel automáticamente
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700"></div>

                    {{-- Reenviar --}}
                    <div class="px-6 py-6 space-y-4">

                        <div x-show="!enviado" class="space-y-3">
                            <p class="text-xs text-gray-400 text-center">¿No recibiste el correo?</p>
                            <form method="POST" action="{{ route('verificacion.reenviar') }}"
                                  @submit.prevent="reenviar($el.closest('form'))">
                                @csrf
                                <button type="submit" :disabled="cargando"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                               bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600
                                               text-white text-sm font-medium rounded-xl transition-all duration-150
                                               disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                                    <svg x-show="cargando" x-cloak
                                         class="w-4 h-4 animate-spin text-white/70 shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    <svg x-show="!cargando"
                                         class="w-4 h-4 text-white/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span x-text="cargando ? 'Enviando...' : 'Reenviar correo de verificación'"></span>
                                </button>
                            </form>
                        </div>

                        <div x-show="enviado" x-cloak class="space-y-2">
                            <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400
                                        bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/30
                                        rounded-xl px-4 py-2.5 justify-center">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>¡Reenviado! Revisa tu bandeja.</span>
                            </div>
                            <p class="text-xs text-gray-400 text-center">
                                Puedes reenviar en <span x-text="cuenta"></span>s
                            </p>
                        </div>

                        @if (session('error'))
                            <p class="text-xs text-red-500 text-center">{{ session('error') }}</p>
                        @endif
                    </div>

                    {{-- Footer nota --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                                px-6 py-4 flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs text-gray-400 leading-relaxed">
                            El enlace expira en
                            <strong class="font-medium text-gray-500 dark:text-gray-400">24 horas</strong>.
                            Si ya verificaste, recarga la página.
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('vpSetup', function () {
                return {
                    cargando: false,
                    enviado: false,
                    cuenta: 60,
                    _timer: null,
                    reenviar: function (form) {
                        var self = this;
                        if (self.cargando || self.enviado) return;
                        self.cargando = true;
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                        })
                        .then(function (r) {
                            if (r.status === 429) throw new Error('throttle');
                            return r;
                        })
                        .then(function () {
                            self.cargando = false;
                            self.enviado = true;
                            self.cuenta = 60;
                            self._timer = setInterval(function () {
                                self.cuenta--;
                                if (self.cuenta <= 0) {
                                    clearInterval(self._timer);
                                    self.enviado = false;
                                }
                            }, 1000);
                        })
                        .catch(function () {
                            self.cargando = false;
                        });
                    }
                };
            });
        });
    </script>

</body>
</html>