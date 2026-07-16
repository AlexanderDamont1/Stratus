@props(['steps' => [], 'clave' => '', 'triggerEvent' => null])

@if(!empty($steps) && auth()->check() && !auth()->user()->haVistoOnboarding($clave))
<div
    x-data="onboardingTour(@js($steps), @js($clave), {{ $triggerEvent ? 'false' : 'true' }})"
    x-init="init()"
    @if($triggerEvent)
    x-on:{{ $triggerEvent }}.window="iniciar()"
    @endif
    x-show="activo"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9998]"
>
    {{-- Oscurece toda la pantalla y resalta (recorta un hueco sobre) el
         elemento del paso actual, si lo tiene. --}}
    <div
        class="fixed rounded-xl transition-all duration-300 ease-out"
        :style="spotlightStyle"
    ></div>

    {{-- Botón omitir: siempre visible mientras el tour está activo --}}
    <button
        type="button"
        @click="omitir()"
        class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[10000] inline-flex items-center gap-1.5 text-xs font-medium text-white/90 hover:text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-lg transition-colors"
    >
        Omitir introducción
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    {{-- Tarjeta explicativa: se posiciona cerca del elemento del paso actual,
         o centrada si ese paso no tiene ancla (o no está visible ahora). --}}
    <div
        x-show="pasoActual"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        :style="cardStyle"
        class="fixed w-[calc(100%-2rem)] max-w-sm z-[9999] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 transition-[top,left] duration-300 ease-out"
    >
        <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide"
              x-text="`Paso ${index + 1} de ${steps.length}`"></span>

        <h3 class="text-[16px] font-semibold text-gray-900 dark:text-gray-100 mt-2 mb-2"
            x-text="pasoActual?.titulo"></h3>

        <p class="text-[13.5px] text-gray-500 dark:text-gray-400 leading-relaxed"
           x-text="pasoActual?.texto"></p>

        <div class="flex items-center justify-between mt-5">
            <div class="flex items-center gap-1">
                <template x-for="(s, i) in steps" :key="i">
                    <span class="w-1.5 h-1.5 rounded-full"
                          :class="i === index ? 'bg-gray-900 dark:bg-white' : 'bg-gray-200 dark:bg-gray-600'"></span>
                </template>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" x-show="index > 0" @click="anterior()"
                        class="text-[12px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-3 py-1.5 rounded-lg transition-colors">
                    Anterior
                </button>
                <button type="button" @click="siguiente()"
                        class="text-[12px] font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 px-4 py-1.5 rounded-lg hover:opacity-90 transition-opacity">
                    <span x-text="esUltimoPaso ? 'Entendido' : 'Siguiente'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
