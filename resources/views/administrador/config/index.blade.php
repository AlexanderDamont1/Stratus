<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Flash --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-6 left-1/2 -translate-x-1/2 z-50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="opacity-0 translate-y-2">
            <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800
                        p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 min-w-[300px]">
                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ session('success') }}
                </p>
            </div>
        </div>
        @endif

        {{-- Header --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Configuración
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">
                Ajustes generales para todas las sucursales.
            </p>
        </div>

        {{-- Card: Comprobante de venta --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200
                    dark:border-gray-700 p-6">
            <div class="mb-5">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                    Entrega de comprobante
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Define cómo se entrega el comprobante al cliente después de cada venta.
                    Aplica para todas las sucursales.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.config.update') }}"
                  x-data="{ modo: '{{ $config->entrega_comprobante }}' }">
                @csrf

                <div class="grid grid-cols-2 gap-3 mb-6">
                    {{-- Opción Ticket --}}
                    <label
                        class="relative flex flex-col gap-2 p-4 rounded-xl border-2 cursor-pointer transition"
                        :class="modo === 'ticket'
                            ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/50'
                            : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                    >
                        <input type="radio" name="entrega_comprobante" value="ticket"
                               x-model="modo" class="sr-only">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                 :class="modo === 'ticket'
                                     ? 'bg-gray-900 dark:bg-white'
                                     : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-4 h-4"
                                     :class="modo === 'ticket'
                                         ? 'text-white dark:text-gray-900'
                                         : 'text-gray-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                Ticket PDF
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            El vendedor descarga el ticket y lo imprime o comparte manualmente.
                        </p>
                        {{-- Check indicator --}}
                        <div x-show="modo === 'ticket'"
                             class="absolute top-3 right-3 w-4 h-4 rounded-full bg-gray-900
                                    dark:bg-white flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </label>

                    {{-- Opción Correo --}}
                    <label
                        class="relative flex flex-col gap-2 p-4 rounded-xl border-2 cursor-pointer transition"
                        :class="modo === 'correo'
                            ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-700/50'
                            : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'"
                    >
                        <input type="radio" name="entrega_comprobante" value="correo"
                               x-model="modo" class="sr-only">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                 :class="modo === 'correo'
                                     ? 'bg-gray-900 dark:bg-white'
                                     : 'bg-gray-100 dark:bg-gray-700'">
                                <svg class="w-4 h-4"
                                     :class="modo === 'correo'
                                         ? 'text-white dark:text-gray-900'
                                         : 'text-gray-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                Correo electrónico
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            El comprobante se envía automáticamente al correo del cliente.
                        </p>
                        <div x-show="modo === 'correo'"
                             class="absolute top-3 right-3 w-4 h-4 rounded-full bg-gray-900
                                    dark:bg-white flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white dark:text-gray-900"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   px-5 py-2.5 rounded-lg text-sm font-semibold
                                   hover:opacity-90 transition active:scale-[.98]">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>