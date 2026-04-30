<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar cuenta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">

    <div class="w-full max-w-sm text-center space-y-8"> {{-- space-y-6 → space-y-8 --}}

        <div class="flex justify-center">
            <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30
                        flex items-center justify-center">
                <svg class="w-7 h-7 text-green-500" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Confirma tu cuenta
            </h2>
            <p class="text-sm text-gray-400 mt-2"> {{-- mt-1 → mt-2 --}}
                Haz clic en el botón para activar tu cuenta.
            </p>
        </div>

        <form method="POST" action="{{ route('verificar.email.confirmar', $token) }}" id="verifyForm">
            @csrf
            <button type="submit" id="submitBtn"
                class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                       px-5 py-3 rounded-lg text-base font-semibold {{-- px-4 py-2.5 → px-5 py-3, text-sm → text-base --}}
                       hover:opacity-90 transition active:scale-[.98]
                       flex items-center justify-center gap-2">
                <span id="btnText">Verificar mi cuenta</span>
                <svg id="btnIcon" class="w-4 h-4 text-white/70 dark:text-gray-900/70 group-hover:translate-x-0.5 transition-transform" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg id="btnSpinner" class="animate-spin h-4 w-4 text-current hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <a href="{{ route('login') }}"
           class="block text-xs text-gray-400 hover:text-gray-600 transition underline mt-2"> {{-- agregado mt-2 para separar --}}
            Volver al login
        </a>

    </div>

    <script>
        // Prevenir envíos múltiples y mostrar spinner
        (function() {
            const form = document.getElementById('verifyForm');
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const btnSpinner = document.getElementById('btnSpinner');

            if (form && btn) {
                form.addEventListener('submit', function(e) {
                    if (btn.disabled) {
                        e.preventDefault();
                        return;
                    }
                    btn.disabled = true;
                    btnText.classList.add('opacity-0');
                    btnIcon.classList.add('hidden');
                    btnSpinner.classList.remove('hidden');
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                });
            }
        })();
    </script>

</body>
</html>