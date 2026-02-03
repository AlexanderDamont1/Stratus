<div
    x-data="{ open: false }"
    class="flex min-h-screen bg-gray-50 dark:bg-gray-900"
>
    <div
        x-show="open"
        x-cloak
        @click="open = false"
        class="fixed inset-0 bg-black/40 z-30 sm:hidden"
        x-transition:opacity
    ></div>

    <aside
        class="fixed sm:static inset-y-0 left-0 z-40
               w-64 bg-white dark:bg-gray-800
               border-r border-gray-200 dark:border-gray-700
               flex flex-col
               transition-transform duration-300 ease-in-out"
        :class="open ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
    >
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

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md transition
               {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               @click="open = false"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />                </svg>
                <span>Inicio</span>
            </a>

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

        <div class="border-t dark:border-gray-700 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-gray-300 shrink-0"></div>
                <div class="truncate">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 w-full px-3 py-2 text-sm text-gray-500 hover:text-red-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">

        <header class="sm:hidden h-16 flex items-center px-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shrink-0">
            <button 
                @click="open = !open" 
                class="p-2 -ml-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition"
            >
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <span class="ml-4 font-semibold text-gray-900 dark:text-white">StratusV1</span>
        </header>

        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

<!-- Agrega Alpine.js justo antes del cierre de body -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
