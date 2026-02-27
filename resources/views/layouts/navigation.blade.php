<div
    x-data="{ open: false }"
    class="flex min-h-screen bg-gray-50 dark:bg-gray-900"
>
    {{-- Overlay móvil --}}
    <div
        x-show="open"
        x-cloak
        @click="open = false"
        class="fixed inset-0 bg-black/40 z-30 sm:hidden"
        x-transition:opacity
    ></div>

    {{-- ═══════════════════════════════
         SIDEBAR
    ═══════════════════════════════ --}}
    <aside
        class="fixed sm:static inset-y-0 left-0 z-40
               w-64 bg-white dark:bg-gray-800
               border-r border-gray-200 dark:border-gray-700
               flex flex-col
               transition-transform duration-300 ease-in-out"
        :class="open ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
    >
        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 border-b dark:border-gray-700 shrink-0">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-8 w-8 text-gray-900 dark:text-gray-100" />
                <span class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    CloudLabs
                </span>
            </div>
            <button @click="open = false" class="sm:hidden text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- NAV --}}
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto">

            {{-- Inicio --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md transition
               {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                </svg>
                <span>Inicio</span>
            </a>

            {{-- Panel Root: solo visible para id_rol = 0 --}}
            @if(Auth::user()->id_rol === 0)
                <a href="{{ route('root.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md transition
                   {{ request()->routeIs('root.*') ? 'bg-gray-900 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span>Panel Root</span>
                </a>
            @endif

            {{-- Admin normal --}}
            @if(auth()->user()->id_rol === 1)
                <a href="{{ route('admin.vendedores.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md transition
                   {{ request()->routeIs('admin.vendedores.*') ? 'bg-gray-900 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span>Vendedores</span>
                </a>
            @endif

            {{-- Perfil --}}
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md transition
               {{ request()->routeIs('profile.edit') ? 'bg-gray-900 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Perfil</span>
            </a>

        </nav>

        {{-- Usuario --}}
        <div class="border-t dark:border-gray-700 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                    {{-- Inicial del nombre --}}
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}
                    </span>
                </div>
                <div class="truncate">
                    {{-- CORRECTO: nombre_usuario y correo, no name ni email --}}
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ Auth::user()->nombre_usuario }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ Auth::user()->correo }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════
         CONTENIDO
    ═══════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0">

        <header class="sm:hidden h-16 flex items-center px-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shrink-0">
            <button
                @click="open = !open"
                class="p-2 -ml-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
            >
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="ml-4 font-semibold text-gray-900 dark:text-white">CloudLabs</span>
        </header>

        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    {{-- ═══════════════════════════════
     MODAL SETUP OBLIGATORIO
     (ROL 44)
═══════════════════════════════ --}}
@if(auth()->user()->id_rol === 44)
    @php
        $max = auth()->user()->negocio->max_users ?? 1;
    @endphp

    <div class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm">

        {{-- Contenedor scrollable --}}
        <div class="fixed inset-0 overflow-y-auto">

            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">

                {{-- Card --}}
                <div
                    class="w-full
                           max-w-2xl
                           bg-white dark:bg-gray-900
                           rounded-2xl
                           shadow-2xl
                           p-6 sm:p-8
                           max-h-[95vh]
                           overflow-y-auto"
                >

                    <h2 class="text-lg sm:text-xl font-semibold mb-2">
                        Configuración inicial
                    </h2>

                    <p class="text-sm text-gray-500 mb-6">
                        Debes registrar {{ $max }} vendedor(es) para activar el sistema.
                    </p>

                    <form method="POST" action="{{ route('admin.setup.completar') }}">
                        @csrf

                        @for($i = 0; $i < $max; $i++)
                            <div class="mb-6 pb-6 border-b last:border-b-0">
                                <h4 class="font-semibold mb-3 text-sm sm:text-base">
                                    Vendedor {{ $i + 1 }}
                                </h4>

                                <div class="grid gap-3">

                                    <input type="text"
                                           name="vendedores[{{ $i }}][nombre]"
                                           placeholder="Nombre de Sucursal"
                                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 outline-none">

                                    <input type="email"
                                           name="vendedores[{{ $i }}][correo]"
                                           placeholder="Correo"
                                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 outline-none">

                                    <input type="text"
                                           name="vendedores[{{ $i }}][username]"
                                           placeholder="Username"
                                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 outline-none">

                                    <input type="password"
                                           name="vendedores[{{ $i }}][password]"
                                           placeholder="Password"
                                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-900 outline-none">

                                </div>
                            </div>
                        @endfor

                        <button
                            class="w-full
                                   bg-gray-900
                                   hover:bg-black
                                   text-white
                                   py-2.5
                                   rounded-lg
                                   transition"
                        >
                            Activar sistema
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
                        @csrf
                        <button class="text-xs text-gray-400 hover:text-gray-500 underline">
                            Cerrar sesión
                        </button>
                    </form>

                </div>

            </div>
        </div>
    </div>
@endif
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>