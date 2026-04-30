<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<div x-data="{ open: false }" class="flex overflow-hidden bg-gray-50 dark:bg-gray-950"
    style="height: calc(100vh / 0.9);">

    {{-- Overlay móvil --}}
    <div x-show="open" x-cloak @click="open = false"
         class="fixed inset-0 bg-black/60 dark:bg-black/80 z-30 lg:hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- SIDEBAR --}}
    <aside x-cloak class="fixed lg:static inset-y-0 left-0 z-40
               w-64 sm:w-72 lg:w-64
               bg-white dark:bg-gray-900
               border-r border-gray-200 dark:border-gray-700
               flex flex-col
               transition-transform duration-300 ease-in-out
               shadow-xl lg:shadow-none"
           :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <x-application-logo class="h-8 w-8 text-gray-900 dark:text-white flex-shrink-0" />
                <span class="text-base font-semibold text-gray-900 dark:text-white truncate">
                    ArrowK
                </span>
            </div>
            <button @click="open = false"
                class="lg:hidden p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- NAV --}}
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto">
            {{-- Inicio --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('dashboard')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                </svg>
                <span class="truncate">Welcome</span>
            </a>

            {{-- Panel Root --}}
            @if(Auth::user()->id_rol === 0)
            <a href="{{ route('root.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('root.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                <span class="truncate">Panel Root</span>
            </a>
            @endif

            {{-- Admin rol 1 --}}
            @if(auth()->user()->id_rol === 1)

            <a href="{{ route('administrador.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('administrador.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                </svg>
                <span class="truncate">Inicio</span>
            </a>

            <a href="{{ route('admin.garantias.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('admin.garantias.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span class="truncate">Garantías</span>
            </a>

            <a href="{{ route('admin.cupones.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('admin.cupones.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>    
                <span class="truncate">Cupones</span>
            </a>

            {{-- Inventario --}}
            @php
            $inventarioActivo = request()->routeIs('bicicletas.index')
                || request()->routeIs('admin.productos.index')
                || request()->routeIs('gestor.vehiculos.modelos.*');
            @endphp

            <div x-data="{ InventarioOpen: {{ $inventarioActivo ? 'true' : 'false' }} }">
                <button @click="InventarioOpen = !InventarioOpen"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                               {{ $inventarioActivo
                                   ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                    </svg>
                    <span class="truncate flex-1 text-left">Inventario</span>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                         :class="InventarioOpen ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="InventarioOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 dark:border-gray-600 space-y-1">

                    <a href="#"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                       @click="open = false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor"
                             class="bi bi-ui-checks" viewBox="0 0 16 16">
                            <path d="M7 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zM2 1a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm0 8a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2zm.854-3.646a.5.5 0 0 1-.708 0l-1-1a.5.5 0 1 1 .708-.708l.646.647 1.646-1.647a.5.5 0 1 1 .708.708zm0 8a.5.5 0 0 1-.708 0l-1-1a.5.5 0 0 1 .708-.708l.646.647 1.646-1.647a.5.5 0 0 1 .708.708zM7 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 8a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                        </svg>
                        <span class="truncate">Inventario</span>
                    </a>

                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('admin.productos.index')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <span class="truncate">Precios</span>
                    </a>

                    <a href="{{ route('bicicletas.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('bicicletas.index')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        <span class="truncate">Stock</span>
                    </a>

                    @modulo('tracking')
                    <a href="{{ route('admin.movimientos.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('admin.movimientos.*')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span class="truncate">Tracking</span>
                    </a>
                    @endmodulo
                </div>
            </div>

            {{-- Catálogo --}}
            @php $catalogoActivo = request()->routeIs('admin.catalogo.*'); @endphp

            <div x-data="{ catalogoOpen: {{ $catalogoActivo ? 'true' : 'false' }} }">
                <button @click="catalogoOpen = !catalogoOpen"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                               {{ $catalogoActivo
                                   ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="truncate flex-1 text-left">Catálogo</span>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                         :class="catalogoOpen ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="catalogoOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 dark:border-gray-600 space-y-1">

                    <a href="{{ route('admin.catalogo.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('admin.catalogo.index')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        Catalogo
                    </a>

                    <a href="{{ route('admin.catalogo.voltajes.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('admin.catalogo.voltajes.*')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Voltajes
                    </a>
                </div>
            </div>
            @endif

            {{-- Vendedor rol 2 --}}
            @if(auth()->user()->id_rol === 2)
            <a href="{{ route('stock.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('stock.index')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                <span class="truncate">Stock</span>
            </a>

            <a href="{{ route('ventas.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('ventas.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span class="truncate">Ventas</span>
            </a>

            <a href="{{ route('garantias.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('garantias.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span class="truncate">Garantías</span>
            </a>

            <a href="{{ route('sucursal.productos.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('sucursal.productos.*')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span class="truncate">Precios</span>
            </a>
            @endif

            {{-- Gestor rol 5 --}}
            @if(auth()->user()->id_rol === 5)
            <a href="{{ route('gestor.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition active:scale-95
                      {{ request()->routeIs('gestor.dashboard')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="truncate">Clientes</span>
            </a>

            @php
            $stockActivo = request()->routeIs('bicicletas.index')
                || request()->routeIs('gestor.vehiculos.modelos.*')
                || request()->routeIs('gestor.vehiculos.colores.*')
                || request()->routeIs('gestor.vehiculos.voltajes.*')
                || request()->routeIs('modelo-voltaje');
            @endphp

            <div x-data="{ stockOpen: {{ $stockActivo ? 'true' : 'false' }} }">
                <button @click="stockOpen = !stockOpen"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                               {{ $stockActivo
                                   ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="truncate flex-1 text-left">Catalogo</span>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200"
                         :class="stockOpen ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="stockOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-1 ml-4 pl-3 border-l-2 border-gray-200 dark:border-gray-600 space-y-1">

                    <a href="{{ route('bicicletas.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('bicicletas.index')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
                        </svg>
                        Stock
                    </a>

                    <a href="{{ route('gestor.vehiculos.modelos.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('gestor.vehiculos.modelos.*')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Modelos
                    </a>

                    <a href="{{ route('gestor.vehiculos.colores.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('gestor.vehiculos.colores.*')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                        Colores
                    </a>

                    <a href="{{ route('gestor.vehiculos.voltajes.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('gestor.vehiculos.voltajes.*')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                       @click="open = false">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Voltajes
                    </a>

                    <a href="{{ route('modelo-voltaje') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition
                              {{ request()->routeIs('modelo-voltaje')
                                  ? 'bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white'
                                  : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Definir Voltaje
                    </a>
                </div>
            </div>
            @endif

            @if(auth()->user()->id_rol === 1)
                @modulo('pedidos')
                    <a href="{{ route('pedidos.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                            {{ request()->routeIs('pedidos.*')
                                ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    @click="open = false">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="truncate">Pedidos</span>
                    </a>
                @endmodulo
            @endif

            @if(auth()->user()->id_rol === 5)
                <a href="{{ route('pedidos.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('pedidos.*')
                            ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                @click="open = false">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="truncate">Pedidos</span>
                </a>
            @endif

             @if(auth()->user()->id_rol === 1)

            <a href="{{ route('admin.config.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                        {{ request()->routeIs('admin.config.*')
                            ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                @click="open = false">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="truncate">Configuración</span>
            </a>
            @endif

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('profile.edit')
                          ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">Mi Cuenta</span>
            </a>
        </nav>

        {{-- Usuario --}}
        <div class="border-t dark:border-gray-700 p-4">
            <div class="flex items-center gap-3 mb-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700
                            flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ Auth::user()->nombre_usuario }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ Auth::user()->correo }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 w-full px-3 py-2.5 text-sm
                               text-gray-500 dark:text-gray-400
                               hover:text-red-600 dark:hover:text-red-400
                               hover:bg-red-50 dark:hover:bg-red-900/20
                               rounded-lg transition">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="truncate">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- CONTENIDO --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden h-16 flex items-center px-4 bg-white dark:bg-gray-800
                       border-b dark:border-gray-700 shrink-0 sticky top-0 z-20">
            <button @click="open = !open"
                    class="p-2 -ml-2 text-gray-600 dark:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
                    aria-label="Menú">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="flex-1 text-center font-semibold text-gray-900 dark:text-white pr-8">
                ArrowK
            </span>
        </header>

        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

</div>
{{-- ── MODAL SETUP (ROL 44) — FUERA del div con overflow-hidden ── --}}
@if(auth()->user()->id_rol === 44 && auth()->user()->negocio)
@php $max = auth()->user()->negocio->max_users ?? 1; @endphp

<div x-data="{ step: {{ (Auth::user()->welcome_pending ?? true) ? 1 : 2 }} }"
     class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>

    <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

    <div class="flex min-h-full items-center justify-center p-4">

        {{-- PASO 1: BIENVENIDA --}}
        <div x-show="step === 1"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative w-full max-w-md bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-xl rounded-2xl p-8">

            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800
                            rounded-full flex items-center justify-center mb-4">
                    <x-application-logo class="h-8 w-8" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">¡Bienvenido a ArrowK!</h2>
                <p class="text-sm text-gray-500 mt-2">Estamos listos para configurar tu ecosistema de movilidad.</p>
            </div>

            <button @click="step = 2"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                           py-3 rounded-xl font-bold hover:opacity-90 transition shadow-lg">
                Comenzar configuración
            </button>
        </div>

        {{-- PASO 2: SETUP DE VENDEDORES --}}
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="relative w-full max-w-2xl bg-white dark:bg-gray-900 border
                    border-gray-200 dark:border-gray-800 shadow-2xl rounded-2xl p-8">

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configuración inicial</h2>
                    <p class="text-sm text-gray-500">Registra a tus sucursales para activar el panel.</p>
                </div>
                <button @click="step = 1" class="text-xs text-gray-400 hover:text-gray-600">← Volver</button>
            </div>

            <form method="POST" action="{{ route('admin.setup.completar') }}" class="space-y-6">
                @csrf

                <div class="max-h-[50vh] overflow-y-auto pr-2 space-y-6">
                    @for($i = 0; $i < $max; $i++)
                    <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800
                                bg-gray-50/50 dark:bg-gray-800/50">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">
                            Vendedor {{ $i + 1 }}
                        </h4>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <input type="text" name="vendedores[{{ $i }}][nombre]"
                                       placeholder="Nombre Sucursal"
                                       class="w-full rounded-lg border-gray-300 dark:bg-gray-700
                                              dark:text-white focus:ring-gray-900 text-sm sm:text-base"
                                       required>
                            </div>
                            <input type="email" name="vendedores[{{ $i }}][correo]"
                                   placeholder="Email de acceso"
                                   class="w-full rounded-lg border-gray-300 dark:bg-gray-700
                                          dark:text-white focus:ring-gray-900 text-sm sm:text-base"
                                   required>

                            <div class="relative" x-data="{ showPassword: false }">
                                <input :type="showPassword ? 'text' : 'password'"
                                       name="vendedores[{{ $i }}][password]"
                                       placeholder="Contraseña"
                                       class="w-full rounded-lg border-gray-300 dark:bg-gray-700
                                              dark:text-white focus:ring-gray-900 text-sm sm:text-base pr-10"
                                       required>
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center px-3
                                               text-gray-600 dark:text-gray-400
                                               hover:text-gray-900 dark:hover:text-white">
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" class="h-5 w-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="pt-4 border-t dark:border-gray-800 flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                                   py-4 rounded-xl font-bold shadow-xl hover:scale-[1.01] transition-transform">
                        Finalizar y Activar Cuenta
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>