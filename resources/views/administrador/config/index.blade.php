<x-app-layout>
<div class="space-y-6">

{{-- Header --}}
    <div>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Configuración general</h2>
        <p class="text-xs text-gray-400 mt-0.5">Estos ajustes afectarán a todas las sucursales y centros de venta.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 translate-y-2">
        <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 min-w-[300px] max-w-md">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-gray-900 dark:text-white flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    

    {{-- Grupos --}}
    @php $grupos = $definiciones->groupBy('grupo'); @endphp

    @foreach($grupos as $grupo => $items)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">

      
        @foreach($items as $def)
        @php
            $valorActual = $valores[$def->clave] ?? $def->valor_default;
            $opciones    = $def->opciones ?? [];
            $badge = match($def->tipo) {
                'radio'          => collect($opciones)->firstWhere('value', $valorActual)['label'] ?? $valorActual,
                'toggle'         => $valorActual ? 'Activado' : 'Desactivado',
                'checkbox_multi' => is_array($valorActual) ? count($valorActual) . ' seleccionadas' : '—',
                default          => $valorActual,
            };
            $iconPath = $def->icono ?? 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z';
        @endphp

        <div x-data="{ open: false }" class="border-b dark:border-gray-700 last:border-b-0">

            {{-- Fila --}}
            <button type="button" @click="open = !open"
                    class="w-full flex items-center gap-4 px-6 py-4 text-left
                           hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors duration-150">

                <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white dark:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $def->nombre }}</p>
                    @if($def->descripcion)
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $def->descripcion }}</p>
                    @endif
                </div>

                <span class="shrink-0 text-xs font-medium px-2.5 py-1 rounded-full
                             bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    {{ $badge }}
                </span>

                <svg class="shrink-0 w-4 h-4 text-gray-400 transition-transform duration-200"
                     :class="open ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Panel --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">

                <form method="POST" action="{{ route('admin.config.update') }}" class="px-6 py-5 space-y-4">
                    @csrf

                    {{-- RADIO --}}
                    @if($def->tipo === 'radio')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3"
                         x-data="{ modo: '{{ $valorActual }}' }">
                        @foreach($opciones as $opcion)
                        <label class="relative flex flex-col rounded-lg border-2 p-4 cursor-pointer transition-all duration-150
                                      {{ $valorActual === $opcion['value']
                                          ? 'border-gray-800 dark:border-white bg-white dark:bg-gray-700/40'
                                          : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/40 hover:border-gray-300 dark:hover:border-gray-500' }}">
                            <input type="radio" name="{{ $def->clave }}" value="{{ $opcion['value'] }}"
                                   x-model="modo" class="absolute opacity-0 w-full h-full cursor-pointer">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $opcion['label'] }}
                                    </span>
                                    @if(isset($opcion['descripcion']))
                                    <span class="text-xs text-gray-400 mt-0.5 leading-relaxed block">
                                        {{ $opcion['descripcion'] }}
                                    </span>
                                    @endif
                                </div>
                                <div x-show="modo === '{{ $opcion['value'] }}'"
                                     class="shrink-0 w-5 h-5 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-white dark:text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div x-show="modo !== '{{ $opcion['value'] }}'"
                                     class="shrink-0 w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600 mt-0.5">
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- TOGGLE --}}
                    @elseif($def->tipo === 'toggle')
                    <div x-data="{ activo: {{ $valorActual ? 'true' : 'false' }} }" class="flex items-center gap-3">
                        <input type="hidden" name="{{ $def->clave }}" :value="activo ? '1' : '0'">
                        <button type="button" @click="activo = !activo"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none"
                                :class="activo ? 'bg-gray-900 dark:bg-white' : 'bg-gray-300 dark:bg-gray-600'">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white dark:bg-gray-900 transition-transform duration-200 shadow"
                                  :class="activo ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                        <span class="text-sm text-gray-700 dark:text-gray-300"
                              x-text="activo ? 'Activado' : 'Desactivado'"></span>
                    </div>

                    {{-- CHECKBOX MULTI --}}
                    @elseif($def->tipo === 'checkbox_multi')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($opciones as $opcion)
                        @php $checked = is_array($valorActual) && in_array($opcion['value'], $valorActual); @endphp
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700
                                      hover:bg-white dark:hover:bg-gray-800 cursor-pointer transition-colors">
                            <input type="checkbox" name="{{ $def->clave }}[]" value="{{ $opcion['value'] }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 focus:ring-gray-500">
                            <div>
                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-200">{{ $opcion['label'] }}</span>
                                @if(isset($opcion['descripcion']))
                                <span class="text-xs text-gray-400">{{ $opcion['descripcion'] }}</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- TEXTO --}}
                    @elseif($def->tipo === 'texto')
                    <input type="text" name="{{ $def->clave }}" value="{{ $valorActual }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">

                    {{-- NUMERO --}}
                    @elseif($def->tipo === 'numero')
                    <input type="number" name="{{ $def->clave }}" value="{{ $valorActual }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                  px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                    @endif

                    {{-- Guardar --}}
                    <div class="flex justify-end pt-2 border-t dark:border-gray-700">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2
                                       bg-gray-900 dark:bg-white text-white dark:text-gray-900
                                       text-sm font-semibold rounded-md hover:opacity-90
                                       active:scale-[0.98] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

        </div>
        @endforeach
    </div>
    @endforeach

    <p class="text-center text-xs text-gray-400 dark:text-gray-500">
        Los cambios se aplicarán de inmediato para todas las sucursales.
    </p>

</div>
</x-app-layout>