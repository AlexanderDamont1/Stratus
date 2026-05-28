<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifica tu correo — ArrowK</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        @keyframes vp-ripple {
            0%   { transform: scale(1); opacity: .55; }
            100% { transform: scale(2.3); opacity: 0; }
        }
        .vp-pulse::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: #fbbf24;
            animation: vp-ripple 1.7s ease-out infinite;
        }
        .vp-steps li:not(:last-child)::after {
            content: '';
            display: block;
            width: 1px;
            height: 18px;
            background: #e5e7eb;
            margin-left: 14px;
            margin-top: 4px;
        }
        .dark .vp-steps li:not(:last-child)::after {
            background: #374151;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900">
    <div class="min-h-[100svh] flex items-center justify-center px-4 overflow-hidden">
        <div class="w-full max-w-4xl flex flex-col justify-center">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div x-data="vpSetup()">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        {{-- Columna izquierda (igual que antes) --}}
                        <div class="flex flex-col justify-between p-8">
                            <!-- Marca -->
                            <div class="text-center mb-6">
                                <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                                     class="mx-auto object-contain w-44 h-22 sm:w-52 sm:h-28"/>
                                <p class="text-xs text-gray-400 mt-2">Panel de administración</p>
                            </div>

                            <!-- Badge -->
                            <div class="inline-flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 text-xs font-medium px-3 py-1.5 rounded-full mb-5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                Verificación requerida
                            </div>

                            <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">Verifica tu correo electrónico</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                                Te enviamos un enlace de verificación. Ábrelo desde tu bandeja para activar tu cuenta y acceder al panel.
                            </p>

                            <!-- Steps -->
                            <ul class="vp-steps list-none space-y-0">
                                @foreach ([
                                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Revisa tu bandeja', 'desc' => 'Incluida la carpeta de spam'],
                                    ['icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.1-1.1m-.757-4.9a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'title' => 'Haz clic en el enlace', 'desc' => 'Confirma que eres tú'],
                                    ['icon' => 'M5 13l4 4L19 7', 'title' => 'Accede al panel', 'desc' => 'Tu cuenta se activa de inmediato'],
                                ] as $step)
                                <li class="flex items-start gap-3 mb-4">
                                    <div class="w-[29px] h-[29px] rounded-full shrink-0 mt-0.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                        <svg class="w-[13px] h-[13px] text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $step['title'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $step['desc'] }}</p>
                                    </div>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Usuario + logout -->
                            <div class="flex items-center gap-3 pt-5 mt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center shrink-0 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}{{ strtoupper(substr(Auth::user()->nombre_usuario, strrpos(Auth::user()->nombre_usuario, ' ') + 1, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()->nombre_usuario }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->correo }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">Salir</button>
                                </form>
                            </div>
                        </div>

                        {{-- Columna derecha --}}
                        <div class="bg-gray-50 dark:bg-gray-900 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700 flex flex-col">
                            <div class="flex-1 flex flex-col items-center justify-center p-6 text-center">
                                <!-- Sobre con badge pulsante -->
                                <div class="relative mb-6">
                                    <div class="w-[88px] h-[88px] rounded-[20px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8 M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="vp-pulse absolute -top-1.5 -right-1.5 w-[22px] h-[22px] rounded-full bg-amber-400 flex items-center justify-center">
                                        <span class="relative z-10 text-[10px] font-semibold text-white leading-none">1</span>
                                    </span>
                                </div>

                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correo enviado a</p>
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-mono bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2 mb-6 truncate max-w-full">
                                    {{ Auth::user()->correo }}
                                </div>

                                <div class="w-full h-px bg-gray-200 dark:bg-gray-700 mb-6"></div>

                                <div x-show="!enviado" class="w-full">
                                    <p class="text-xs text-gray-400 mb-3">¿No recibiste el correo?</p>
                                    <form method="POST" action="{{ route('verificacion.reenviar') }}" @submit.prevent="reenviar($el.closest('form'))">
                                        @csrf
                                        <button type="submit" :disabled="cargando" class="w-full flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium py-[10px] rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/60 transition disabled:opacity-50">
                                            <svg x-show="cargando" x-cloak class="w-4 h-4 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                            </svg>
                                            <svg x-show="!cargando" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9 m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span x-text="cargando ? 'Enviando...' : 'Reenviar correo de verificación'"></span>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="enviado" x-cloak class="w-full">
                                    <div class="flex items-center justify-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 text-sm font-medium px-4 py-[11px] rounded-xl">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        ¡Correo reenviado! Revisa tu bandeja.
                                    </div>
                                    <p class="text-xs text-gray-400 mt-3">Puedes reenviar en <span x-text="cuenta"></span>s</p>
                                </div>

                                @if (session('error'))
                                    <p class="mt-4 text-xs text-red-500">{{ session('error') }}</p>
                                @endif
                            </div>

                            <div class="px-5 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-start gap-2 text-left">
                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs text-gray-400 leading-relaxed">El enlace expira en 24 horas. Si ya verificaste, recarga la página.</span>
                            </div>
                        </div>
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
                    cargando : false,
                    enviado  : false,
                    cuenta   : 60,
                    _timer   : null,
                    reenviar: function (form) {
                        var self = this;
                        if (self.cargando || self.enviado) return;
                        self.cargando = true;
                        fetch(form.action, {
                            method  : 'POST',
                            headers : { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        })
                        .then(function (r) { if (r.status === 429) throw new Error('throttle'); return r; })
                        .then(function () {
                            self.cargando = false;
                            self.enviado  = true;
                            self.cuenta   = 60;
                            self._timer = setInterval(function () {
                                self.cuenta--;
                                if (self.cuenta <= 0) { clearInterval(self._timer); self.enviado = false; }
                            }, 1000);
                        })
                        .catch(function () { self.cargando = false; });
                    }
                };
            });
        });
    </script>
</body>
</html>