<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg"
                 x-data="reiniciarTutorial()">
                <div class="max-w-xl space-y-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Tutorial
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Vuelva a ver las guías de introducción en cada pantalla, como la primera vez que ingresó al sistema.
                        </p>
                    </header>

                    <button type="button"
                        @click="reiniciar()"
                        :disabled="cargando"
                        class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition disabled:opacity-40 disabled:cursor-not-allowed active:scale-95">
                        <svg x-show="cargando" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="cargando ? 'Reiniciando...' : 'Reiniciar todo el tutorial'"></span>
                    </button>
                </div>

                <x-flash-toast />
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function reiniciarTutorial() {
            return {
                cargando: false,
                flashVisible: false,
                flashMsg: '',
                flashTipo: 'success',
                flashTimer: null,

                flash(msg, tipo = 'success') {
                    this.flashMsg = msg;
                    this.flashTipo = tipo;
                    this.flashVisible = true;
                    clearTimeout(this.flashTimer);
                    this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4000 : 3000);
                },

                async reiniciar() {
                    if (!confirm('¿Desea reiniciar el tutorial? Las guías de introducción volverán a aparecer en cada pantalla.')) return;

                    this.cargando = true;
                    try {
                        const res = await fetch('{{ route('onboarding.reiniciar') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept':       'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        const data = await res.json();
                        if (!res.ok || !data.ok) {
                            this.flash('No se pudo reiniciar el tutorial.', 'error');
                            return;
                        }
                        this.flash(data.mensaje ?? 'Tutorial reiniciado.');
                    } catch (e) {
                        this.flash('Error de conexión.', 'error');
                    } finally {
                        this.cargando = false;
                    }
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
