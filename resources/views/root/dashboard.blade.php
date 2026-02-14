<x-app-layout>

<div 
    x-data="{ 
        createModal: false
    }"
    class="space-y-6"
>

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Panel Root
        </h2>

        <button 
            @click="createModal = true"
            class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90"
        >
            Crear Link
        </button>
    </div>

    {{-- LINKS --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Token</th>
                    <th class="px-4 py-2 text-left">Máx. Vendedores</th>
                    <th class="px-4 py-2 text-left">Usado</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                <tr class="border-t dark:border-gray-700">
                    <td class="px-4 py-2 text-xs">
                        {{ url('/registro/'.$link->token) }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $link->max_users }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $link->usado ? 'Sí' : 'No' }}
                    </td>
                    <td class="px-4 py-2 text-right">
                        <form method="POST"
                              action="{{ route('root.links.destroy', $link) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-xs">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                        No hay links generados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- NEGOCIOS --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-left">Max Users</th>
                </tr>
            </thead>
            <tbody>
                @forelse($negocios as $negocio)
                <tr class="border-t dark:border-gray-700">
                    <td class="px-4 py-2 text-xs">
                        {{ $negocio->id_negocio }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $negocio->nombre_negocio }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $negocio->max_users }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                        No hay negocios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CREAR LINK --}}
    <div 
        x-show="createModal" 
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Crear Link</h3>

            <form method="POST" action="{{ route('root.links.store') }}">
                @csrf

                <div class="space-y-3">
                    <input type="number" name="max_users"
                        placeholder="Máximo de vendedores"
                        class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-700">
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button"
                        @click="createModal = false"
                        class="px-4 py-2 text-sm">
                        Cancelar
                    </button>

                    <button 
                        class="bg-gray-900 text-white px-4 py-2 rounded text-sm">
                        Crear
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

</x-app-layout>
