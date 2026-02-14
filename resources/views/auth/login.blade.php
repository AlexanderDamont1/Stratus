<x-guest-layout>
    <x-auth-session-status class="mb-6 text-sm text-gray-600 dark:text-gray-400 animate-fade-in" :status="session('status')" />

    <div class="w-full max-w-sm mx-auto px-2 sm:px-0">
        <div class="text-center mb-10">
            <div class="mb-6">
                <img src="{{ asset('favicon.svg') }}" alt="CloudLabs" class="mx-auto object-contain w-40 h-20 sm:w-48 sm:h-24 " />
                <span class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                    CloudLabs
                </span>
            </div>
            <h2 class="font-medium text-gray-900 dark:text-white opacity-0 animate-fade-in text-lg sm:text-xl" style="animation-delay: 0.1s">
                Acceso al sistema
            </h2>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="opacity-0 animate-slide-up" style="animation-delay: 0.3s">
                <div class="relative group">
                    <x-text-input id="correo" 
                        class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-3.5 text-sm transition-all focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                        type="email" name="correo" :value="old('correo')" required autofocus placeholder="nombre@cloudlabs.com" />
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-500 group-hover:w-full transition-all duration-300"></div>
                </div>
                <x-input-error :messages="$errors->get('correo')" class="mt-2 text-xs animate-fade-in" />
            </div>

            <div class="opacity-0 animate-slide-up" style="animation-delay: 0.4s">
                <div class="relative group">
                    <div class="relative">
                        <x-text-input id="password" 
                            class="block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-3.5 text-sm pr-12 transition-all focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-200"
                            type="password" name="password" required placeholder="Contraseña" />
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 p-1">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="opacity-0 animate-slide-up flex justify-between items-center text-sm" style="animation-delay: 0.5s">
                <label class="flex items-center text-gray-600 dark:text-gray-400 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 shadow-sm focus:ring-gray-500" />
                    <span class="ml-3">Recordar sesión</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
                        ¿Olvidó contraseña?
                    </a>
                @endif
            </div>

            <div class="opacity-0 animate-slide-up pt-2" style="animation-delay: 0.6s">
                <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-black py-3.5 text-sm font-medium rounded-lg hover:bg-gray-800 transition-all">
                    Acceder al sistema
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 opacity-0 animate-fade-in" style="animation-delay: 0.8s">
            <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
                CloudLabs Enterprise © {{ date('Y') }}
            </p>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    eyeIcon.classList.toggle('hidden');
                    eyeOffIcon.classList.toggle('hidden');
                });
            }

            // Add focus effects to inputs
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-gray-200');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-gray-200');
                });
                
                // Validation feedback
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.remove('border-red-300');
                        this.classList.add('border-green-500');
                        setTimeout(() => {
                            this.classList.remove('border-green-500');
                            this.classList.add('border-gray-300');
                        }, 1500);
                    }
                });
            });

            // Add click animation to checkbox
            const rememberCheckbox = document.getElementById('remember_me');
            if (rememberCheckbox) {
                rememberCheckbox.addEventListener('change', function() {
                    const checkDiv = this.nextElementSibling;
                    if (this.checked) {
                        checkDiv.classList.add('animate-pulse-subtle');
                        setTimeout(() => {
                            checkDiv.classList.remove('animate-pulse-subtle');
                        }, 300);
                    }
                });
            }
        });
    </script>

    <style>
        /* Animaciones personalizadas solo para formulario */
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(5px);
            }
            to { 
                opacity: 1; 
                transform: translateY(0);
            }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(15px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1); 
            }
            50% { 
                transform: scale(1.02); 
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
        }
        
        .animate-pulse-subtle {
            animation: pulse 0.3s ease-in-out;
        }
        
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
        
        /* Custom focus styles */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(209, 213, 219, 0.3);
        }
        
        /* Submit button hover effect */
        button[type="submit"]:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Input underline animation */
        .group:hover .group-hover\:w-full {
            width: 100%;
        }
        
        /* Checkbox animation */
        .sr-only:checked + div {
            animation: pulse 0.3s ease-in-out;
        }
        
        /* Responsive adjustments for larger logo */
        @media (max-width: 640px) {
            .w-48 {
                width: 12rem;
            }
            .h-24 {
                height: 6rem;
            }
        }
        
        /* Loading animation for form elements */
        .opacity-0 {
            opacity: 0;
        }
        
        /* Smooth loading sequence */
        [style*="animation-delay"] {
            animation-fill-mode: forwards;
        }
    </style>
</x-guest-layout>