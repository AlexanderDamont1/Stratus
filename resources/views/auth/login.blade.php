<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso al sistema</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900
             h-full overflow-hidden flex items-center justify-center px-6 py-6">

    <div class="w-full max-w-5xl mx-auto my-auto">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center gap-y-8">

            {{-- ── Columna izquierda (oculta en mobile) ── --}}
            <div class="hidden lg:block text-center lg:text-left space-y-5">

                <div class="flex items-center gap-3">
                <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                     class="h-14 w-auto object-contain dark:hidden" />
                <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK"
                     class="h-14 w-auto object-contain hidden dark:block" />

                <span class="inline-block px-4 py-1.5 text-xs font-semibold rounded-full
                             bg-sky-100 text-sky-800
                             dark:bg-sky-800/30 dark:text-sky-400">
                    Acceso seguro
                </span>
            </div>
                

                <div class="space-y-2">
                    <h1 class="text-3xl font-light text-gray-900 dark:text-white leading-tight">
                        Bienvenido de nuevo
                    </h1>
                    <p class="text-base text-gray-400 leading-relaxed">
                        Ingresa tus credenciales para continuar.
                    </p>
                </div>

                {{-- Mensajes de sesión o error --}}
                @if (session('status'))
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('no_verificado'))
                    <div class="text-sm text-amber-600 dark:text-amber-400">
                        Verifica tu correo <span class="font-semibold">{{ session('no_verificado') }}</span> antes de iniciar sesión.
                    </div>
                @endif

                <div class="flex items-center gap-4 justify-center lg:justify-start">
                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700
                                flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                        </svg>
                    </div>
                    <div class="min-w-0 text-left">
                        <p class="text-xs text-gray-400">Accede con tu cuenta</p>
                        <p class="text-base font-medium text-gray-800 dark:text-gray-200 truncate">
                            ArrowK Enterprise
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Columna derecha (form, único bloque visible en mobile) ── --}}
            <div x-data="loginForm()">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                            dark:border-gray-700 shadow-sm overflow-hidden">

                    {{-- Formulario --}}
                    <div class="px-6 py-6 sm:px-8 lg:px-10 lg:py-10 space-y-5 lg:space-y-7">

                        {{-- Logo: solo visible en mobile, arriba de "Iniciar sesión" --}}
                        <div class="flex justify-center lg:hidden">
                            <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                                 class="h-10 w-auto object-contain dark:hidden" />
                            <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK"
                                 class="h-10 w-auto object-contain hidden dark:block" />
                        </div>

                        <div class="text-center">
                            <h2 class="text-xl font-medium text-gray-900 dark:text-white">
                                Iniciar sesión
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Ingresa tus datos para acceder
                            </p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" @submit="handleSubmit" class="space-y-4 lg:space-y-6">
                            @csrf

                            {{-- Correo --}}
                            <div class="space-y-1.5">
                                <label for="correo" class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Correo electrónico
                                </label>
                                <input id="correo"
                                       class="block w-full border border-gray-300 dark:border-gray-700
                                              dark:bg-gray-800 dark:text-white rounded-lg px-5 py-3 lg:py-3.5
                                              text-sm transition-all duration-200
                                              focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                                       type="email" name="correo" value="{{ old('correo') }}"
                                       required autofocus placeholder="nombre@cloudlabs.com" />
                                @error('correo')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Contraseña --}}
                            <div class="space-y-1.5">
                                <label for="password" class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <input id="password"
                                           class="block w-full border border-gray-300 dark:border-gray-700
                                                  dark:bg-gray-800 dark:text-white rounded-lg px-5 py-3 lg:py-3.5
                                                  text-sm pr-12 transition-all duration-200
                                                  focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                                           :type="showPassword ? 'text' : 'password'"
                                           name="password" required placeholder="Ingresa tu contraseña" />
                                    <button type="button" @click="togglePassword"
                                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 p-1
                                                   hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Recordar y olvidó --}}
                            <div class="flex justify-between items-center text-sm">
                                <label for="remember" class="flex items-center text-gray-600 dark:text-gray-400 cursor-pointer group">
                                    <input id="remember" type="checkbox" name="remember"
                                           class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700
                                                  text-gray-900 shadow-sm focus:ring-gray-500" />
                                    <span class="ml-3 transition-colors group-hover:text-gray-800 dark:group-hover:text-white">
                                        Recordar sesión
                                    </span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="text-gray-500 dark:text-gray-400 hover:text-gray-700
                                              dark:hover:text-white transition-colors hover:underline underline-offset-2">
                                        ¿Olvidó contraseña?
                                    </a>
                                @endif
                            </div>

                            {{-- Botón submit --}}
                            <button type="submit" :disabled="cargando"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 lg:py-3.5
                                           bg-gray-900 dark:bg-white text-white dark:text-black
                                           hover:bg-gray-800 dark:hover:bg-gray-100
                                           text-sm font-medium rounded-xl transition-all duration-150
                                           disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                                <svg x-show="cargando" x-cloak
                                     class="w-4 h-4 animate-spin text-white/70 dark:text-black/70 shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                <span x-text="cargando ? 'Accediendo...' : 'Acceder al sistema'"></span>
                            </button>
                        </form>

                        {{-- Separador con "o continúa con" --}}
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="bg-white dark:bg-gray-800 px-4 text-gray-400">
                                    o continúa con
                                </span>
                            </div>
                        </div>

                        {{-- Botón Google --}}
                        <a href="{{ route('google.login') }}"
                           class="flex items-center justify-center gap-3 w-full border border-gray-300
                                  dark:border-gray-600 rounded-lg px-4 py-3 lg:py-3.5 text-sm font-medium
                                  text-gray-700 dark:text-gray-300 hover:bg-gray-50
                                  dark:hover:bg-gray-800 transition-all duration-200 hover:scale-[1.01] active:scale-100">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continuar con Google
                        </a>
                    </div>

                    {{-- Footer de la tarjeta --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                                px-6 py-4 sm:px-8 lg:px-10 lg:py-5 flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="text-xs text-gray-400 leading-relaxed">
                            Tus credenciales están protegidas con cifrado de extremo a extremo.
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('loginForm', function () {
                return {
                    showPassword: false,
                    cargando: false,
                    togglePassword() {
                        this.showPassword = !this.showPassword;
                    },
                    handleSubmit() {
                        // No se previene el submit: dejamos que el navegador envíe el form
                        // de forma nativa (full page load), solo activamos el estado visual.
                        this.cargando = true;
                    }
                };
            });
        });
    </script>
</body>
</html>