<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'ArrowK')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    @include('partials.google-analytics')
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 min-h-[100svh] flex items-center justify-center px-6">

    @php
        $modoVertical = trim($__env->yieldContent('layout')) === 'vertical';
    @endphp

    @if($modoVertical)
        {{-- ── Encabezado arriba, contenido debajo, todo a lo ancho ── --}}
        <div class="w-full max-w-4xl mx-auto py-10 space-y-8">

            <div class="text-center space-y-6">
                @yield('badge')

                <div class="space-y-2">
                    <h1 class="text-3xl font-light text-gray-900 dark:text-white leading-tight">
                        @yield('heading')
                    </h1>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        @yield('sub')
                    </p>
                </div>

                @hasSection('meta')
                    <div class="flex items-center gap-3 justify-center">
                        @yield('meta')
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                        dark:border-gray-700 shadow-sm overflow-hidden">
                @yield('contenido')
            </div>

        </div>
    @else
        {{-- ── Dos columnas lado a lado ── --}}
        <div class="w-full max-w-4xl mx-auto py-10">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center space-y-8 lg:space-y-0">

                {{-- ── Columna izquierda ── --}}
                <div class="text-center lg:text-left space-y-6">

                    @yield('badge')

                    <div class="space-y-2">
                        <h1 class="text-3xl font-light text-gray-900 dark:text-white leading-tight">
                            @yield('heading')
                        </h1>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            @yield('sub')
                        </p>
                    </div>

                    @hasSection('meta')
                        <div class="flex items-center gap-3 justify-center lg:justify-start">
                            @yield('meta')
                        </div>
                    @endif
                </div>

                {{-- ── Columna derecha ── --}}
                <div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                                dark:border-gray-700 shadow-sm overflow-hidden">
                        @yield('contenido')
                    </div>
                </div>

            </div>
        </div>
    @endif

    @yield('scripts')

</body>
</html>
