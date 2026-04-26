<x-guest-layout>
    <x-auth-session-status class="mb-6 text-sm text-gray-600 dark:text-gray-400 animate-fade-in" :status="session('status')" />

    {{-- Flash de error no verificado --}}
    @if(session('no_verificado'))
        <div class="mb-4 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800
                    text-xs text-amber-700 dark:text-amber-400 text-center animate-fade-in">
            Verifica tu correo <span class="font-semibold">{{ session('no_verificado') }}</span> antes de iniciar sesión.
        </div>
    @endif

    <div class="w-full max-w-sm mx-auto px-2 sm:px-0">
        <div class="text-center mb-10">
            <div class="mb-6">
                <img src="{{ asset('favicon.svg') }}" alt="CloudLabs"
                     class="mx-auto object-contain w-40 h-20 sm:w-48 sm:h-24" />
                <span class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                    CloudLabs
                </span>
            </div>
            <h2 class="font-medium text-gray-900 dark:text-white opacity-0 animate-fade-in text-lg sm:text-xl"
                style="animation-delay: 0.1s">
                Acceso al sistema
            </h2>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
            @csrf

            <div class="opacity-0 animate-slide-up" style="animation-delay: 0.3s">
                <div class="relative group">
                    <x-text-input id="correo"
                        class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800
                               dark:text-white rounded-lg px-4 py-3.5 text-sm transition-all duration-200
                               focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                        type="email" name="correo" :value="old('correo')"
                        required autofocus placeholder="nombre@cloudlabs.com" />
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-500
                                group-hover:w-full transition-all duration-300"></div>
                </div>
                <x-input-error :messages="$errors->get('correo')" class="mt-2 text-xs animate-fade-in" />
            </div>

            <div class="opacity-0 animate-slide-up" style="animation-delay: 0.4s">
                <div class="relative group">
                    <div class="relative">
                        <x-text-input id="password"
                            class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800
                                   dark:text-white rounded-lg px-4 py-3.5 text-sm pr-12 transition-all duration-200
                                   focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                            type="password" name="password" required placeholder="Contraseña" />
                        <button type="button" id="togglePassword"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 p-1
                                       hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                         -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                                         a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
                                         l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                                         m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025
                                         0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs animate-fade-in" />
            </div>

            <div class="opacity-0 animate-slide-up flex justify-between items-center text-sm"
                 style="animation-delay: 0.5s">
                <label class="flex items-center text-gray-600 dark:text-gray-400 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember"
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

            <div class="opacity-0 animate-slide-up pt-2" style="animation-delay: 0.6s">
                <button type="submit" id="submitBtn"
                        class="relative w-full bg-gray-900 dark:bg-white text-white dark:text-black
                               py-3.5 text-sm font-medium rounded-lg hover:bg-gray-800
                               dark:hover:bg-gray-100 transition-all duration-200
                               hover:-translate-y-0.5 active:translate-y-0
                               focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    <span id="btnText" class="inline-flex items-center justify-center gap-2">
                        Acceder al sistema
                    </span>
                    <span id="btnSpinner" class="hidden absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                    </span>
                </button>
            </div>
        </form>

        {{-- Divider --}}
        <div class="relative my-6 opacity-0 animate-fade-in" style="animation-delay: 0.7s">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="bg-white dark:bg-gray-900 px-3 text-gray-400">
                    o continúa con
                </span>
            </div>
        </div>

        {{-- Google --}}
        <div class="opacity-0 animate-fade-in" style="animation-delay: 0.75s">
            <a href="{{ route('google.login') }}"
               class="flex items-center justify-center gap-3 w-full border border-gray-300
                      dark:border-gray-600 rounded-lg px-4 py-3 text-sm font-medium
                      text-gray-700 dark:text-gray-300 hover:bg-gray-50
                      dark:hover:bg-gray-800 transition-all duration-200 hover:scale-[1.01] active:scale-100">
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
                Continuar con Google
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800
                    opacity-0 animate-fade-in" style="animation-delay: 0.8s">
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
                CloudLabs Enterprise © {{ date('Y') }}
            </p>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in  { animation: fadeIn 0.5s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.5s ease-out forwards; }

        .opacity-0 { opacity: 0; }
        [style*="animation-delay"] { animation-fill-mode: forwards; }

        /* Transiciones generales */
        * {
            transition-property: background-color, border-color, color, fill, stroke,
                                  opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Mejora el feedback visual de inputs */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(209, 213, 219, 0.3);
        }

        /* Línea animada en el hover del campo */
        .group:hover .group-hover\:w-full { width: 100%; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Password toggle (sin animaciones extra)
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput  = document.getElementById('password');
            const eyeIcon        = document.getElementById('eyeIcon');
            const eyeOffIcon     = document.getElementById('eyeOffIcon');

            if (togglePassword) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    eyeIcon.classList.toggle('hidden');
                    eyeOffIcon.classList.toggle('hidden');
                });
            }

            // Spinner en el envío (solo feedback, sin bloquear completamente)
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            if (form) {
                form.addEventListener('submit', function() {
                    if (submitBtn.disabled) return;
                    submitBtn.disabled = true;
                    btnText.classList.add('opacity-0');
                    btnSpinner.classList.remove('hidden');
                });
            }
        });
    </script>
</x-guest-layout>