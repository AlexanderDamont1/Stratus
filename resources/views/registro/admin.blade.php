<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro · Nuevo negocio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 py-16">

    <div class="w-full max-w-md">

        {{-- Cabecera --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-xs px-3 py-1.5 rounded-full mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                Link válido · {{ $link->max_users }} {{ $link->max_users === 1 ? 'vendedor' : 'vendedores' }}
            </div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Crear tu negocio</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Completa los datos para activar tu cuenta de administrador.</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">

            {{-- Errores --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-600 dark:text-red-400 text-xs flex items-start gap-2">
                                <span class="shrink-0 mt-0.5">✕</span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('registro.store', $link->token) }}" novalidate>
                @csrf

                {{-- Negocio --}}
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-4">Datos del negocio</p>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">Nombre del negocio</label>
                    <input
                        type="text"
                        name="nombre_negocio"
                        value="{{ old('nombre_negocio') }}"
                        placeholder="ej. Bicicletas del Norte"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <hr class="border-gray-100 dark:border-gray-700 my-5">

                {{-- Admin --}}
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium mb-4">Tu cuenta de administrador</p>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">Nombre completo</label>
                    <input
                        type="text"
                        name="nombre_usuario"
                        value="{{ old('nombre_usuario') }}"
                        placeholder="ej. Juan Pérez"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">Correo electrónico</label>
                    <input
                        type="email"
                        name="correo"
                        value="{{ old('correo') }}"
                        placeholder="correo@ejemplo.com"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>


                <div class="mb-4">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">Contraseña</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1.5">Confirmar contraseña</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Repite la contraseña"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-2.5 rounded-md text-sm font-semibold hover:opacity-90 transition"
                >
                    Crear negocio y cuenta
                </button>

            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-5">
            Este link expira {{ $link->expires_at ? $link->expires_at->diffForHumans() : 'sin fecha límite' }}
            y solo puede usarse una vez.
        </p>

    </div>

</body>
</html>