@extends('layouts.publico')

@section('layout', 'vertical')

@section('titulo', 'Sucursales — ArrowK')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-gray-100 text-gray-700
                 dark:bg-gray-700 dark:text-gray-300">
        Directorio de sucursales
    </span>
@endsection

@section('heading')
    Encuentra tu<br>sucursal más cercana
@endsection

@section('sub')
    Todas nuestras sucursales con ubicación, horario y contacto directo.
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </div>
    <div class="min-w-0 text-left">
        <p class="text-xs text-gray-400">Sucursales registradas</p>
        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
            {{ count($sucursales) }}
        </p>
    </div>
@endsection

@section('contenido')
    <div x-data="catalogo()" x-init="init()">

        {{-- Buscador --}}
        <div class="px-6 py-6 space-y-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Buscar</p>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="busqueda"
                       placeholder="Buscar por nombre o dirección…"
                       class="w-full text-sm border border-gray-200 dark:border-gray-600 rounded-xl pl-9 pr-3 py-2.5
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                              focus:outline-none focus:ring-1 focus:ring-gray-400">
            </div>
            <p class="text-xs text-gray-400" x-show="busqueda.trim()">
                <span x-text="filtradas.length"></span>
                <span x-text="filtradas.length === 1 ? ' resultado' : ' resultados'"></span>
            </p>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700"></div>

        {{-- Lista de sucursales --}}
        <div class="px-6 py-6">
            <div x-show="filtradas.length === 0" class="text-center py-6">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin resultados</p>
                <p class="text-xs text-gray-400 mt-1">No encontramos sucursales para "<span x-text="busqueda"></span>"</p>
            </div>

            <div class="space-y-4 max-h-[560px] overflow-y-auto pr-0.5" x-show="filtradas.length > 0">
                <template x-for="(s, i) in filtradas" :key="s.id_usuario">
                    <div class="rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                        {{-- Previa del mapa --}}
                        <div class="relative h-36 bg-gray-100 dark:bg-gray-700">
                            <div class="absolute inset-0 flex items-center justify-center"
                                 x-show="!cargados[s.id_usuario]">
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <iframe :src="'https://maps.google.com/maps?q=' + s.lat + ',' + s.lng + '&z=15&output=embed&hl=es'"
                                    loading="lazy" tabindex="-1" referrerpolicy="no-referrer-when-downgrade"
                                    @load="cargados[s.id_usuario] = true"
                                    class="absolute inset-0 w-full h-full border-0"
                                    :title="'Mapa de ' + (s.nombre_sucursal || s.nombre_usuario)"></iframe>
                            <div class="absolute inset-0" @click="abrirMapa(s)"></div>
                            <span class="absolute top-2 left-2 text-[10px] font-semibold text-white bg-gray-900/80
                                         rounded-full px-2 py-0.5" x-text="(i + 1) + ' / ' + filtradas.length"></span>
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"
                               x-text="s.nombre_sucursal || s.nombre_usuario"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="s.direccion"></p>

                            <div class="flex flex-wrap gap-1.5 mt-3">
                                <template x-if="s.telefono">
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50
                                                 border border-gray-100 dark:border-gray-700 rounded-full px-2 py-0.5"
                                          x-text="s.telefono"></span>
                                </template>
                                <template x-if="s.horario_apertura && s.horario_cierre">
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50
                                                 border border-gray-100 dark:border-gray-700 rounded-full px-2 py-0.5"
                                          x-text="s.horario_apertura + ' – ' + s.horario_cierre"></span>
                                </template>
                                <span class="text-[11px] font-mono text-gray-400 bg-gray-50 dark:bg-gray-700/50
                                             border border-gray-100 dark:border-gray-700 rounded-full px-2 py-0.5"
                                      x-text="parseFloat(s.lat).toFixed(4) + ', ' + parseFloat(s.lng).toFixed(4)"></span>
                            </div>

                            <div class="flex gap-2 mt-3">
                                <a :href="'https://maps.google.com/?q=' + s.lat + ',' + s.lng" target="_blank"
                                   class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold
                                          bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                          rounded-lg py-2 hover:opacity-90 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Cómo llegar
                                </a>
                                <template x-if="s.telefono">
                                    <a :href="'tel:' + s.telefono"
                                       class="flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-300
                                              border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2
                                              hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        Llamar
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                    px-6 py-4 flex items-start gap-2">
            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs text-gray-400 leading-relaxed">
                ¿No encuentras tu sucursal? Contáctanos directamente.
            </span>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        const SUCURSALES_DATA = @json($sucursales);

        function catalogo() {
            return {
                sucursales: SUCURSALES_DATA,
                busqueda: '',
                cargados: {},

                init: function () {},

                get filtradas() {
                    var q = this.busqueda.toLowerCase().trim();
                    if (!q) return this.sucursales;
                    return this.sucursales.filter(function (s) {
                        return (s.nombre_sucursal || s.nombre_usuario || '').toLowerCase().includes(q)
                            || (s.direccion || '').toLowerCase().includes(q);
                    });
                },

                abrirMapa: function (s) {
                    window.open('https://maps.google.com/?q=' + s.lat + ',' + s.lng, '_blank');
                },
            };
        }
    </script>
@endsection
