<div
    x-data="{ open: false }"
    class="flex min-h-screen bg-gray-50 dark:bg-gray-900"
>
    {{-- Overlay móvil --}}
    <div
        x-show="open"
        x-cloak
        @click="open = false"
        class="fixed inset-0 bg-black/60 dark:bg-black/80 z-30 lg:hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- ═══════════════════════════════
         SIDEBAR
    ═══════════════════════════════ --}}
    <aside
        class="fixed lg:static inset-y-0 left-0 z-40
               w-64 sm:w-72 lg:w-64
               bg-white dark:bg-gray-800
               border-r border-gray-200 dark:border-gray-700
               flex flex-col
               transition-transform duration-300 ease-in-out
               shadow-xl lg:shadow-none"
        :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <x-application-logo class="h-8 w-8 text-gray-900 dark:text-white flex-shrink-0" />
                <span class="text-base font-semibold text-gray-900 dark:text-white truncate">
                    ArrowK
                </span>
            </div>
            <button @click="open = false" class="lg:hidden p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
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
               @click="open = false"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                </svg>
                <span class="truncate">Inicio</span>
            </a>

            {{-- Panel Root: solo visible para id_rol = 0 --}}
            @if(Auth::user()->id_rol === 0)
                <a href="{{ route('root.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('root.*') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                    <span class="truncate">Panel Root</span>
                </a>
            @endif

            {{-- Admin normal --}}
            @if(auth()->user()->id_rol === 1)
                <a href="{{ route('admin.vendedores.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.vendedores.*') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="truncate">Sucursales</span>
                </a>
            @endif



            {{-- Gestor --}}
            @if(auth()->user()->id_rol === 5)
                <a href="{{ route('gestor.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('gestor.dashboard') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="truncate">Gestor</span>
                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('#') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="truncate">Clientes</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('#') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="truncate">Stock</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('#') 
                             ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                             : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   @click="open = false"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="truncate">Pedidos</span>
                </a>


            @endif

            {{-- Perfil --}}
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                      {{ request()->routeIs('profile.edit') 
                         ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' 
                         : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="truncate">Perfil</span>
            </a>
        </nav>

        {{-- Usuario --}}
        <div class="border-t dark:border-gray-700 p-4">
            <div class="flex items-center gap-3 mb-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
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
                <button class="flex items-center gap-3 w-full px-3 py-2.5 text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="truncate">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════
         CONTENIDO
    ═══════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden h-16 flex items-center px-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shrink-0 sticky top-0 z-20">
            <button
                @click="open = !open"
                class="p-2 -ml-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
                aria-label="Menú"
            >
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

    {{-- ═══════════════════════════════
         MODAL SETUP (ROL 44)
    ═══════════════════════════════ --}}
    @if(auth()->user()->id_rol === 44 && auth()->user()->negocio)
        @php
            $max = auth()->user()->negocio->max_users ?? 1;
        @endphp

        <div class="fixed inset-0 z-50 bg-black/70 dark:bg-black/90 backdrop-blur-sm overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
                <div class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-5 sm:p-8 my-4">
                    
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Configuración inicial
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Debes registrar <span class="font-semibold">{{ $max }}</span> {{ $max == 1 ? 'vendedor' : 'vendedores' }} para activar el sistema.
                    </p>

                    <form method="POST" action="{{ route('admin.setup.completar') }}" class="space-y-6">
                        @csrf

                        @for($i = 0; $i < $max; $i++)
                            <div class="pb-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4 text-sm sm:text-base">
                                    Vendedor {{ $i + 1 }}
                                </h4>

                                <div class="grid gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Nombre de sucursal</label>
                                        <input type="text"
                                               name="vendedores[{{ $i }}][nombre]"
                                               placeholder="Ej: Sucursal Centro"
                                               value="{{ old('vendedores.' . $i . '.nombre') }}"
                                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                                               required>
                                        @error('vendedores.' . $i . '.nombre')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Correo electrónico</label>
                                        <input type="email"
                                               name="vendedores[{{ $i }}][correo]"
                                               placeholder="ejemplo@correo.com"
                                               value="{{ old('vendedores.' . $i . '.correo') }}"
                                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                                               required>
                                        @error('vendedores.' . $i . '.correo')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Contraseña</label>
                                        <input type="password"
                                               name="vendedores[{{ $i }}][password]"
                                               placeholder="••••••••"
                                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 dark:focus:ring-white focus:border-transparent outline-none transition"
                                               required>
                                        @error('vendedores.' . $i . '.password')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endfor

                        <div class="pt-2">
                            <button class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-black dark:hover:bg-gray-100 text-white py-3 rounded-lg text-sm font-semibold transition active:scale-[0.98]">
                                Activar sistema
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
                        @csrf
                        <button class="text-xs text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 underline transition">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>