@extends('layouts.publico')

@section('titulo', 'Verificar cuenta')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-yellow-100 text-yellow-800
                 dark:bg-yellow-800/30 dark:text-yellow-400">
        Verificación requerida
    </span>
@endsection

@section('heading')
    Confirma tu<br>cuenta
@endsection

@section('sub')
    Haz clic en el botón para activar tu cuenta.
@endsection

@section('contenido')
    <div class="px-6 py-6">
        <form method="POST" action="{{ route('verificar.email.confirmar', $token) }}" id="verifyForm">
            @csrf
            <button type="submit" id="submitBtn"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                           bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600
                           text-white text-sm font-medium rounded-xl transition-all duration-150
                           disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white/70 shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg id="btnIcon" class="w-4 h-4 text-white/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="btnText">Verificar mi cuenta</span>
            </button>
        </form>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-center justify-center">
        <a href="{{ route('login') }}"
           class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
            Volver al inicio de sesión
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        // Prevenir envíos múltiples y mostrar spinner
        (function() {
            const form = document.getElementById('verifyForm');
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const btnSpinner = document.getElementById('btnSpinner');

            if (form && btn) {
                form.addEventListener('submit', function (e) {
                    if (btn.disabled) {
                        e.preventDefault();
                        return;
                    }
                    btn.disabled = true;
                    btnText.textContent = 'Verificando...';
                    btnIcon.classList.add('hidden');
                    btnSpinner.classList.remove('hidden');
                });
            }
        })();
    </script>
@endsection
