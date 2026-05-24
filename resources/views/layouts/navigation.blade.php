<style>
    [x-cloak] { display: none !important; }

    .nav-group-label {
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 10px 12px 4px;
    }
    .dark .nav-group-label { color: #6b7280; }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        font-size: 13px;
        text-decoration: none;
        position: relative;
    }
    .nav-item-active {
        background: #111827;
        color: #fff;
    }
    .dark .nav-item-active {
        background: #fff;
        color: #111827;
    }
    .nav-item-inactive {
        color: #6b7280;
    }
    .nav-item-inactive:hover {
        background: #f3f4f6;
        color: #111827;
    }
    .dark .nav-item-inactive { color: #9ca3af; }
    .dark .nav-item-inactive:hover {
        background: #1f2937;
        color: #f3f4f6;
    }

    .nav-sub-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        font-size: 12.5px;
        text-decoration: none;
        color: #6b7280;
    }
    .nav-sub-item:hover { background: #f3f4f6; color: #111827; }
    .dark .nav-sub-item { color: #9ca3af; }
    .dark .nav-sub-item:hover { background: #1f2937; color: #f3f4f6; }

    .nav-sub-item-active {
        color: #111827;
        font-weight: 500;
        background: #f3f4f6;
    }
    .dark .nav-sub-item-active {
        color: #f3f4f6;
        background: #1f2937;
    }

    .nav-divider {
        height: 0.5px;
        background: #e5e7eb;
        margin: 4px 0;
    }
    .dark .nav-divider { background: #374151; }

    .sub-border {
        border-left: 1.5px solid #e5e7eb;
        margin-left: 18px;
        padding-left: 8px;
    }
    .dark .sub-border { border-left-color: #374151; }

    .chevron-icon {
        margin-left: auto;
        width: 14px;
        height: 14px;
        transition: transform 0.2s;
        flex-shrink: 0;
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
    <aside x-cloak
        class="fixed lg:static inset-y-0 left-0 z-40
               w-64 sm:w-72 lg:w-64
               bg-white dark:bg-gray-900
               border-r border-gray-200 dark:border-gray-700
               flex flex-col transition-transform duration-300 ease-in-out
               shadow-xl lg:shadow-none"
        :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Header --}}

        <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <x-application-logo class="h-8 w-8 text-gray-900 dark:text-white flex-shrink-0" />
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate leading-tight">ArrowK</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate leading-tight">
                        @if(Auth::user()->id_rol === 0) Root
                        @elseif(Auth::user()->id_rol === 1) Administrador
                        @elseif(Auth::user()->id_rol === 2) Sucursal
                        @elseif(Auth::user()->id_rol === 5) Gestor
                        @else Usuario
                        @endif
                    </p>
                </div>
            </div>
            <button @click="open = false" class="lg:hidden p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        

        {{-- Navegación --}}
        <nav class="flex-1 px-2 py-3 overflow-y-auto space-y-0.5">

            {{-- Dashboard (todos los roles) --}}
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'nav-item-active' : 'nav-item-inactive' }}"
               @click="open = false">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- ============================================================
                 ROL ROOT (id_rol === 0)
            ============================================================ --}}
            @if(Auth::user()->id_rol === 0)
                <div class="nav-divider"></div>
               <a href="{{ route('root.dashboard') }}"
                    class="nav-item {{
                            request()->routeIs('root.*') && !request()->routeIs('root.config.*')&& !request()->routeIs('root.audit.*')
                            ? 'nav-item-active'
                            : 'nav-item-inactive'
                    }}"
                    @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                    <span>Panel Root</span>
                </a>


                 <a href="{{ route('root.audit.index') }}"
                     class="nav-item {{ request()->routeIs('root.audit.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                    @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                   
                    <span>Logs de Sistema</span>
                </a>

                <a href="{{ route('root.config.index') }}"
                   class="nav-item {{ request()->routeIs('root.config.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="flex-1 truncate">Configuración</span>
                </a>
            @endif

            {{-- ============================================================
                 ROL ADMINISTRADOR (id_rol === 1)
            ============================================================ --}}
            @if(Auth::user()->id_rol === 1)

                <div class="nav-divider"></div>
                <p class="nav-group-label">Operaciones</p>

                {{-- Sucursales: agrupa Personal + Stock + Tracking --}}
                @php
                    $sucursalesActivo = request()->routeIs('admin.personal.*')
                        || request()->routeIs('bicicletas.index')
                        || request()->routeIs('admin.movimientos.*');
                @endphp
                <div x-data="{ sucOpen: {{ $sucursalesActivo ? 'true' : 'false' }} }">
                    <button @click="sucOpen = !sucOpen"
                        class="nav-item nav-item-inactive w-full text-left"
                        :class="sucOpen ? 'text-gray-900 dark:text-white' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                        </svg>
                        <span class="flex-1 truncate">Sucursales</span>
                        <svg class="chevron-icon" :style="sucOpen ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="sucOpen" x-cloak class="mt-1 mb-1 sub-border space-y-0.5">
                        <a href="{{ route('admin.personal.index') }}"
                           class="nav-sub-item {{ request()->routeIs('admin.personal.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Personal
                        </a>
                        <a href="{{ route('bicicletas.index') }}"
                           class="nav-sub-item {{ request()->routeIs('bicicletas.index') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Stock por sucursal
                        </a>
                        @modulo('tracking')
                        <a href="{{ route('admin.movimientos.index') }}"
                           class="nav-sub-item {{ request()->routeIs('admin.movimientos.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Tracking
                        </a>
                        @endmodulo
                    </div>
                </div>

                {{-- Inventario: Precios --}}
                @php
                    $inventarioActivo = request()->routeIs('admin.productos.index');
                @endphp
                <div x-data="{ invOpen: {{ $inventarioActivo ? 'true' : 'false' }} }">
                    <button @click="invOpen = !invOpen"
                        class="nav-item nav-item-inactive w-full text-left"
                        :class="invOpen ? 'text-gray-900 dark:text-white' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>
                        <span class="flex-1 truncate">Inventario</span>
                        <svg class="chevron-icon" :style="invOpen ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="invOpen" x-cloak class="mt-1 mb-1 sub-border space-y-0.5">
                        <a href="{{ route('admin.productos.index') }}"
                           class="nav-sub-item {{ request()->routeIs('admin.productos.index') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Precios
                        </a>
                    </div>
                </div>

                {{-- Catálogo: Modelos + Voltajes --}}
                @php $catalogoActivo = request()->routeIs('admin.catalogo.*'); @endphp
                <div x-data="{ catOpen: {{ $catalogoActivo ? 'true' : 'false' }} }">
                    <button @click="catOpen = !catOpen"
                        class="nav-item nav-item-inactive w-full text-left"
                        :class="catOpen ? 'text-gray-900 dark:text-white' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="flex-1 truncate">Catálogo</span>
                        <svg class="chevron-icon" :style="catOpen ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="catOpen" x-cloak class="mt-1 mb-1 sub-border space-y-0.5">
                        <a href="{{ route('admin.catalogo.index') }}"
                           class="nav-sub-item {{ request()->routeIs('admin.catalogo.index') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Modelos
                        </a>
                        <a href="{{ route('admin.catalogo.voltajes.index') }}"
                           class="nav-sub-item {{ request()->routeIs('admin.catalogo.voltajes.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Voltajes
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.garantias.index') }}"
                   class="nav-item {{ request()->routeIs('admin.garantias.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span class="flex-1 truncate">Garantías</span>
                </a>

                @modulo('pedidos')
                <a href="{{ route('pedidos.index') }}"
                   class="nav-item {{ request()->routeIs('pedidos.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75" />
                    </svg>
                    <span class="flex-1 truncate">Pedidos</span>
                </a>
                @endmodulo

                <div class="nav-divider"></div>

                
                <div class="nav-group-label">Finanzas</div>
 
                <a href="{{ route('admin.cajas.index') }}"
                class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors
                        {{ request()->routeIs('admin.cajas.*') ? 'bg-white/8 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Cajas</span>
                    {{-- Indicador si hay sesiones abiertas --}}
                    @php
                        $cajasAbiertas = \App\Models\CajaSesion::whereHas('caja', fn($q) => $q->where('id_negocio', auth()->user()->id_negocio))
                            ->where('estado', 'abierta')->count();
                    @endphp
                    @if($cajasAbiertas > 0)
                        <span class="ml-auto text-xs bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded-full">
                            {{ $cajasAbiertas }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.cupones.index') }}"
                   class="nav-item {{ request()->routeIs('admin.cupones.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    <span class="flex-1 truncate">Cupones</span>
                </a>

                <div class="nav-divider"></div>

                <a href="{{ route('admin.config.index') }}"
                   class="nav-item {{ request()->routeIs('admin.config.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="flex-1 truncate">Configuración</span>
                </a>

            @endif

            {{-- ============================================================
                 ROL VENDEDOR / SUCURSAL (id_rol === 2)
            ============================================================ --}}
            @if(Auth::user()->id_rol === 2)

                <div class="nav-divider"></div>
                <p class="nav-group-label">Mis operaciones</p>

                <a href="{{ route('stock.index') }}"
                   class="nav-item {{ request()->routeIs('stock.index') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    <span class="flex-1 truncate">Stock disponible</span>
                </a>

                {{-- Grupo: Mi turno --}}
<div class="nav-group-label">Mi turno</div>
 
<a href="{{ route('caja.index') }}"
   class="nav-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm transition-colors
          {{ request()->routeIs('caja.*') ? 'bg-white/8 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <span>Mi caja</span>
    {{-- Badge de estado en tiempo real --}}
    @php
        $cajaVendedor = \App\Models\Caja::where('id_usuario', auth()->id())->where('id_negocio', auth()->user()->id_negocio)->first();
        $sesionVendedor = $cajaVendedor ? \App\Models\CajaSesion::where('id_caja', $cajaVendedor->id_caja)->where('estado', 'abierta')->first() : null;
    @endphp
    @if($sesionVendedor)
        <span class="ml-auto w-2 h-2 rounded-full bg-emerald-400"></span>
    @endif
</a>

                <a href="{{ route('ventas.index') }}"
                   class="nav-item {{ request()->routeIs('ventas.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <span class="flex-1 truncate">Ventas</span>
                </a>

                <a href="{{ route('garantias.index') }}"
                   class="nav-item {{ request()->routeIs('garantias.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    <span class="flex-1 truncate">Garantías</span>
                </a>

                <a href="{{ route('sucursal.productos.index') }}"
                   class="nav-item {{ request()->routeIs('sucursal.productos.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="flex-1 truncate">Precios</span>
                </a>

            @endif

            {{-- ============================================================
                 ROL GESTOR (id_rol === 5)
            ============================================================ --}}
            @if(Auth::user()->id_rol === 5)

                <div class="nav-divider"></div>
                <p class="nav-group-label">Gestión</p>

                <a href="{{ route('gestor.dashboard') }}"
                   class="nav-item {{ request()->routeIs('gestor.dashboard') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="flex-1 truncate">Clientes</span>
                </a>

                <a href="{{ route('pedidos.index') }}"
                   class="nav-item {{ request()->routeIs('pedidos.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
                   @click="open = false">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75" />
                    </svg>
                    <span class="flex-1 truncate">Pedidos</span>
                </a>

                {{-- Catálogo: agrupa todo lo de vehículos --}}
                @php
                    $catalogoGestorActivo = request()->routeIs('bicicletas.index')
                        || request()->routeIs('gestor.vehiculos.modelos.*')
                        || request()->routeIs('gestor.vehiculos.colores.*')
                        || request()->routeIs('gestor.vehiculos.voltajes.*')
                        || request()->routeIs('modelo-voltaje');
                @endphp
                <div x-data="{ catGOpen: {{ $catalogoGestorActivo ? 'true' : 'false' }} }">
                    <button @click="catGOpen = !catGOpen"
                        class="nav-item nav-item-inactive w-full text-left"
                        :class="catGOpen ? 'text-gray-900 dark:text-white' : ''">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="flex-1 truncate">Catálogo</span>
                        <svg class="chevron-icon" :style="catGOpen ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="catGOpen" x-cloak class="mt-1 mb-1 sub-border space-y-0.5">
                        <a href="{{ route('bicicletas.index') }}"
                           class="nav-sub-item {{ request()->routeIs('bicicletas.index') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Stock
                        </a>
                        <a href="{{ route('gestor.vehiculos.modelos.index') }}"
                           class="nav-sub-item {{ request()->routeIs('gestor.vehiculos.modelos.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Modelos
                        </a>
                        <a href="{{ route('gestor.vehiculos.colores.index') }}"
                           class="nav-sub-item {{ request()->routeIs('gestor.vehiculos.colores.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                            Colores
                        </a>
                        <a href="{{ route('gestor.vehiculos.voltajes.index') }}"
                           class="nav-sub-item {{ request()->routeIs('gestor.vehiculos.voltajes.*') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Voltajes
                        </a>
                        <a href="{{ route('modelo-voltaje') }}"
                           class="nav-sub-item {{ request()->routeIs('modelo-voltaje') ? 'nav-sub-item-active' : '' }}"
                           @click="open = false">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Definir voltaje
                        </a>
                    </div>
                </div>

            @endif

            {{-- Mi cuenta (todos los roles) --}}
            <div class="nav-divider"></div>
            <a href="{{ route('profile.edit') }}"
               class="nav-item {{ request()->routeIs('profile.*') ? 'nav-item-active' : 'nav-item-inactive' }}"
               @click="open = false">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="flex-1 truncate">Mi cuenta</span>
            </a>

        </nav>

      <x-sidebar-novedades />

        {{-- Footer: usuario + logout --}}
        <div class="border-t border-gray-200 dark:border-gray-700 p-3">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg cursor-default mb-1">
                <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate leading-tight">
                        {{ Auth::user()->nombre_usuario }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate leading-tight">
                        {{ Auth::user()->correo }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2.5 w-full px-2 py-2 text-xs rounded-lg
                           text-red-500 dark:text-red-400
                           hover:bg-red-50 dark:hover:bg-red-900/20
                           transition-all duration-200">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar móvil --}}
        <header class="lg:hidden h-14 flex items-center px-4
                        bg-white dark:bg-gray-900
                        border-b border-gray-200 dark:border-gray-700
                        shrink-0 sticky top-0 z-20">
            <button @click="open = !open"
                class="p-2 -ml-1 text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700
                       rounded-lg transition"
                aria-label="Abrir menú">
                <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="flex-1 text-center text-sm font-medium text-gray-900 dark:text-white pr-8">
                ArrowK
            </span>
        </header>

        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- MODAL DE CONFIGURACIÓN INICIAL (sin cambios de lógica) --}}
@if(auth()->user()->id_rol === 44 && auth()->user()->negocio)
    @php $max = auth()->user()->negocio->max_users ?? 1; @endphp

    <div x-data="{ step: {{ (Auth::user()->welcome_pending ?? true) ? 1 : 2 }} }"
        class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4">

            <div x-show="step === 1"
                class="relative w-full max-w-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-2xl p-8">
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <x-application-logo class="h-8 w-8" />
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">¡Bienvenido a ArrowK!</h2>
                    <p class="text-sm text-gray-500 mt-2">Estamos listos para configurar tu ecosistema de movilidad.</p>
                </div>
                <button @click="step = 2"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    Comenzar configuración
                </button>
            </div>

            <div x-show="step === 2"
                class="relative w-full max-w-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-2xl rounded-2xl p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Configuración inicial</h2>
                        <p class="text-sm text-gray-500">Registra a tus sucursales para activar el panel.</p>
                    </div>
                    <button @click="step = 1"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        ← Volver
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.setup.completar') }}" class="space-y-6"
                    x-data="{
                        vendedores: [],
                        submitting: false,
                        init() {
                            @for($i = 0; $i < $max; $i++)
                                this.vendedores.push({
                                    nombre: '{{ old("vendedores.$i.nombre") }}',
                                    correo: '{{ old("vendedores.$i.correo") }}',
                                    password: '',
                                    password_confirmation: ''
                                });
                            @endfor
                        },
                        get todosValidos() {
                            return this.vendedores.every(v =>
                                v.nombre.trim() !== '' &&
                                v.correo.trim() !== '' &&
                                v.password !== '' &&
                                v.password_confirmation !== '' &&
                                v.password === v.password_confirmation
                            );
                        }
                    }"
                    @submit.prevent="if(todosValidos) { submitting = true; $el.submit() }">

                    @csrf
                    <div class="max-h-[50vh] overflow-y-auto pr-2 space-y-6">
                        @for($i = 0; $i < $max; $i++)
                            <div class="p-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 shadow-sm">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-400 mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-gray-900 dark:bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-100 dark:text-gray-900">
                                        {{ $i + 1 }}
                                    </span>
                                    Sucursal
                                </h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-1">Nombre de la sucursal</label>
                                        <input type="text"
                                            x-model="vendedores[{{ $i }}].nombre"
                                            name="vendedores[{{ $i }}][nombre]"
                                            placeholder="Ej. Sucursal Centro"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 text-sm">
                                        @error("vendedores.$i.nombre")
                                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-1">Correo electrónico</label>
                                        <input type="email"
                                            x-model="vendedores[{{ $i }}].correo"
                                            name="vendedores[{{ $i }}][correo]"
                                            placeholder="sucursal@ejemplo.com"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 text-sm">
                                        @error("vendedores.$i.correo")
                                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-1">Contraseña</label>
                                            <div class="relative" x-data="{ show: false }">
                                                <input :type="show ? 'text' : 'password'"
                                                    x-model="vendedores[{{ $i }}].password"
                                                    name="vendedores[{{ $i }}][password]"
                                                    placeholder="Mínimo 8 caracteres"
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 text-sm pr-10">
                                                <button type="button" @click="show = !show"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-1">Confirmar contraseña</label>
                                            <div class="relative" x-data="{ show: false }">
                                                <input :type="show ? 'text' : 'password'"
                                                    x-model="vendedores[{{ $i }}].password_confirmation"
                                                    name="vendedores[{{ $i }}][password_confirmation]"
                                                    placeholder="Repite la contraseña"
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gray-900 text-sm pr-10">
                                                <button type="button" @click="show = !show"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                </button>
                                            </div>
                                            <template x-if="vendedores[{{ $i }}].password_confirmation && vendedores[{{ $i }}].password !== vendedores[{{ $i }}].password_confirmation">
                                                <p class="text-[10px] text-red-500 mt-1">✕ Las contraseñas no coinciden</p>
                                            </template>
                                            <template x-if="vendedores[{{ $i }}].password_confirmation && vendedores[{{ $i }}].password === vendedores[{{ $i }}].password_confirmation && vendedores[{{ $i }}].password !== ''">
                                                <p class="text-[10px] text-green-600 dark:text-green-400 mt-1">✓ Coinciden</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="pt-4 border-t dark:border-gray-800">
                        <button type="submit"
                            :disabled="!todosValidos || submitting"
                            :class="{ 'opacity-50 cursor-not-allowed': !todosValidos || submitting }"
                            class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-4 rounded-xl font-bold shadow-xl hover:shadow-2xl transition-all duration-200 disabled:shadow-none flex items-center justify-center gap-2">
                            <span x-show="!submitting">Finalizar y activar cuenta</span>
                            <span x-show="submitting" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>