<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro · Nuevo negocio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.google-analytics')
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 py-16">

    <div class="w-full max-w-md">

        {{-- Cabecera --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200
                        dark:border-green-800 text-green-700 dark:text-green-400 text-xs px-3 py-1.5 rounded-full mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                Link válido · {{ $link->max_users }} {{ $link->max_users === 1 ? 'vendedor' : 'vendedores' }}
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Crear tu negocio</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Completa los datos para activar tu cuenta de administrador.
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">

            {{-- Errores --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200
                            dark:border-red-800 rounded-lg">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-600 dark:text-red-400 text-xs flex items-start gap-2">
                                <span class="shrink-0 mt-0.5">✕</span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Botón Google --}}
            <a href="{{ route('google.registro.redirect', $link->token) }}"
               class="flex items-center justify-center gap-3 w-full border border-gray-300
                      dark:border-gray-600 rounded-lg px-4 py-3 text-sm font-medium
                      text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50
                      transition-all active:scale-[.98] mb-6">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                          d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04
                             2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853"
                          d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71
                             1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05"
                          d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18
                             C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335"
                          d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12
                             1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Registrarse con Google
            </a>

            {{-- Divider --}}
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white dark:bg-gray-800 px-3 text-gray-400">
                        o regístrate con correo
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('registro.store', $link->token) }}" novalidate>
                @csrf

                {{-- Negocio --}}
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-4">
                    Datos del negocio
                </p>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre del negocio
                    </label>
                    <input
                        type="text"
                        name="nombre_negocio"
                        value="{{ old('nombre_negocio') }}"
                        placeholder="ej. Bicicletas del Norte"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                               dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none
                               focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <hr class="border-gray-100 dark:border-gray-700 my-5">

                {{-- Admin --}}
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-4">
                    Tu cuenta de administrador
                </p>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Nombre completo
                    </label>
                    <input
                        type="text"
                        name="nombre_usuario"
                        value="{{ old('nombre_usuario') }}"
                        placeholder="ej. Juan Pérez"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                               dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none
                               focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Correo electrónico
                    </label>
                    <input
                        type="email"
                        name="correo"
                        value="{{ old('correo') }}"
                        placeholder="correo@ejemplo.com"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                               dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none
                               focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white rounded-md px-3 py-2 pr-10 text-sm focus:outline-none
                                   focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                            required
                        >
                        <button type="button" id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400
                                       hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542
                                         7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                                         a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
                                         l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59
                                         3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025
                                         10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repite la contraseña"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white rounded-md px-3 py-2 pr-10 text-sm focus:outline-none
                                   focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                            required
                        >
                        <button type="button" id="toggleConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400
                                       hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg id="eyeIconConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542
                                         7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeOffIconConfirm" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                                         a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
                                         l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59
                                         3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025
                                         10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Indicador de coincidencia --}}
                    <p id="matchMsg" class="text-xs mt-1.5 hidden"></p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                           py-2.5 rounded-md text-sm font-semibold hover:opacity-90
                           transition active:scale-[.98]"
                >
                    Crear negocio y cuenta
                </button>

            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-5">
            Este link expira
            {{ $link->expires_at ? $link->expires_at->diffForHumans() : 'sin fecha límite' }}
            y solo puede usarse una vez.
        </p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Toggle password
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput  = document.getElementById('password');
            const eyeIcon        = document.getElementById('eyeIcon');
            const eyeOffIcon     = document.getElementById('eyeOffIcon');

            togglePassword?.addEventListener('click', () => {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            });

            // Toggle confirm password
            const toggleConfirm     = document.getElementById('toggleConfirm');
            const confirmInput      = document.getElementById('password_confirmation');
            const eyeIconConfirm    = document.getElementById('eyeIconConfirm');
            const eyeOffIconConfirm = document.getElementById('eyeOffIconConfirm');

            toggleConfirm?.addEventListener('click', () => {
                const type = confirmInput.type === 'password' ? 'text' : 'password';
                confirmInput.type = type;
                eyeIconConfirm.classList.toggle('hidden');
                eyeOffIconConfirm.classList.toggle('hidden');
            });

            // Indicador de coincidencia de contraseñas
            const matchMsg = document.getElementById('matchMsg');

            function checkMatch() {
                if (!confirmInput.value) {
                    matchMsg.classList.add('hidden');
                    return;
                }
                matchMsg.classList.remove('hidden');
                if (passwordInput.value === confirmInput.value) {
                    matchMsg.textContent = '✓ Las contraseñas coinciden';
                    matchMsg.className   = 'text-xs mt-1.5 text-green-600 dark:text-green-400';
                } else {
                    matchMsg.textContent = '✕ Las contraseñas no coinciden';
                    matchMsg.className   = 'text-xs mt-1.5 text-red-500 dark:text-red-400';
                }
            }

            passwordInput?.addEventListener('input', checkMatch);
            confirmInput?.addEventListener('input', checkMatch);
        });
    </script>

</body>
</html>