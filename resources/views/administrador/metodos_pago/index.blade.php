<x-app-layout>
<div class="max-w-2xl mx-auto space-y-6">

    {{-- ══ NOTIFICATION CARD ══ --}}
    @if(session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3500)"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
         style="min-width: 300px; max-width: 400px;">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 border shadow-sm
                    bg-green-50 dark:bg-green-950/40
                    border-green-100 dark:border-green-900/50
                    text-green-800 dark:text-green-300">
            <svg class="w-4 h-4 shrink-0 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-xs font-semibold leading-snug">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3500)"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none"
         style="min-width: 300px; max-width: 400px;">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 border shadow-sm
                    bg-red-50 dark:bg-red-950/40
                    border-red-100 dark:border-red-900/50
                    text-red-800 dark:text-red-300">
            <svg class="w-4 h-4 shrink-0 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-xs font-semibold leading-snug">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Métodos de pago</h2>
            <p class="text-xs text-gray-400 mt-0.5">Configura cómo aceptas pagos en tus sucursales.</p>
        </div>
    </div>

    {{-- Lista de métodos --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Métodos configurados</h3>
            <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                {{ $metodos->count() }} total
            </span>
        </div>

        @if($metodos->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm text-gray-400">No hay métodos configurados.</p>
                <p class="text-xs text-gray-400 mt-1">Agrega uno abajo para empezar.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($metodos as $m)
                <div x-data="{ editando: false, nombre: '{{ addslashes($m->nombre) }}', esEfectivo: {{ $m->es_efectivo ? 'true' : 'false' }}, requiereRef: {{ $m->requiere_referencia ? 'true' : 'false' }}, activo: {{ $m->activo ? 'true' : 'false' }} }"
                     class="px-5 py-4">

                    {{-- Vista normal --}}
                    <div x-show="!editando" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                             :class="activo ? 'bg-gray-100 dark:bg-gray-700' : 'bg-gray-50 dark:bg-gray-800'">
                            <svg class="w-4 h-4" :class="activo ? 'text-gray-600 dark:text-gray-300' : 'text-gray-300 dark:text-gray-600'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-medium text-gray-800 dark:text-white"
                                   :class="!activo && 'line-through text-gray-400'">
                                    {{ $m->nombre }}
                                </p>
                                @if($m->es_efectivo)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 uppercase tracking-wide">
                                        Efectivo
                                    </span>
                                @endif
                                @if($m->requiere_referencia)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 uppercase tracking-wide">
                                        Ref. requerida
                                    </span>
                                @endif
                                @if(!$m->activo)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 uppercase tracking-wide">
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="editando = true"
                                class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.metodos_pago.destroy', $m->id_metodo) }}"
                                  onsubmit="return confirm('¿Desactivar este método?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition">
                                    Desactivar
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Modo edición inline --}}
                    <form x-show="editando" x-transition.opacity
                          method="POST" action="{{ route('admin.metodos_pago.update', $m->id_metodo) }}"
                          class="space-y-3">
                        @csrf @method('PUT')

                        <div class="flex items-center gap-2">
                            <input type="text" name="nombre" x-model="nombre"
                                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600
                                          px-3 py-2 text-sm bg-white dark:bg-gray-900
                                          text-gray-900 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                            <button type="submit"
                                class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                       px-4 py-2 rounded-lg text-xs font-semibold hover:opacity-90 transition">
                                Guardar
                            </button>
                            <button type="button" @click="editando = false"
                                class="text-xs text-gray-400 hover:text-gray-600 transition">
                                Cancelar
                            </button>
                        </div>

                        <div class="flex items-center gap-4 flex-wrap">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden"  name="es_efectivo" value="0">
                                <input type="checkbox" name="es_efectivo" value="1"
                                       x-model="esEfectivo"
                                       class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span class="text-xs text-gray-600 dark:text-gray-300">Es efectivo</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden"  name="requiere_referencia" value="0">
                                <input type="checkbox" name="requiere_referencia" value="1"
                                       x-model="requiereRef"
                                       class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span class="text-xs text-gray-600 dark:text-gray-300">Requiere referencia</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden"  name="activo" value="0">
                                <input type="checkbox" name="activo" value="1"
                                       x-model="activo"
                                       class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                <span class="text-xs text-gray-600 dark:text-gray-300">Activo</span>
                            </label>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Agregar nuevo método --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6"
         x-data="{ esEfectivo: false, requiereRef: false }">
        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-4">Nuevo método de pago</p>

        <form method="POST" action="{{ route('admin.metodos_pago.store') }}" class="space-y-4">
            @csrf

            @if($errors->any())
                <div class="text-xs text-red-500 space-y-1">
                    @foreach($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       placeholder="Ej: Efectivo, Tarjeta débito, Transferencia SPEI…"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              px-3 py-2 text-sm bg-white dark:bg-gray-900
                              text-gray-900 dark:text-white
                              focus:outline-none focus:ring-2 focus:ring-gray-400 transition"
                       required>
            </div>

            <div class="flex items-center gap-6 flex-wrap">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden"  name="es_efectivo" value="0">
                    <input type="checkbox" name="es_efectivo" value="1"
                           x-model="esEfectivo"
                           class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Es efectivo</span>
                </label>
                <p x-show="esEfectivo" x-transition.opacity
                   class="text-xs text-gray-400 -mt-2 w-full">
                    Habilitará el campo de monto recibido y cálculo de cambio en el punto de venta.
                </p>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden"  name="requiere_referencia" value="0">
                    <input type="checkbox" name="requiere_referencia" value="1"
                           x-model="requiereRef"
                           class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Requiere referencia</span>
                </label>
                <p x-show="requiereRef" x-transition.opacity
                   class="text-xs text-gray-400 -mt-2 w-full">
                    El cajero deberá ingresar un texto libre (folio, últimos 4 dígitos, etc.).
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                               px-5 py-2.5 rounded-lg text-sm font-semibold
                               hover:opacity-90 transition active:scale-[.98]">
                    Crear método
                </button>
            </div>
        </form>
    </div>

</div>
</x-app-layout>