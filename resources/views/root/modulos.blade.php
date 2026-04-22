<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4">

        <div class="mb-6">
            <a href="{{ route('root.dashboard') }}" class="text-sm text-gray-500 hover:underline">
                ← Volver al dashboard
            </a>
            <h1 class="text-2xl font-bold mt-2">
                Módulos — {{ $negocio->nombre_negocio }}
            </h1>
        </div>

        @foreach ($estado as $item)
            <div class="bg-white rounded-xl shadow p-6 mb-4">
                <div class="mb-3">
                    <h2 class="text-lg font-semibold">{{ $item['modulo']->nombre }}</h2>
                    @if($item['modulo']->descripcion)
                        <p class="text-sm text-gray-500">{{ $item['modulo']->descripcion }}</p>
                    @endif
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Rol</th>
                            <th class="py-2">Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['roles'] as $idRol => $rol)
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-medium">{{ $rol['nombre'] }}</td>
                                <td class="py-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            class="sr-only peer toggle-modulo"
                                            {{ $rol['activo'] ? 'checked' : '' }}
                                            data-negocio="{{ $negocio->id_negocio }}"
                                            data-modulo="{{ $item['modulo']->id_modulo }}"
                                            data-rol="{{ $idRol }}"
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full
                                                    peer-focus:ring-2 peer-focus:ring-blue-300
                                                    after:content-[''] after:absolute after:top-0.5 after:left-[2px]
                                                    after:bg-white after:rounded-full after:h-5 after:w-5
                                                    after:transition-all peer-checked:after:translate-x-full">
                                        </div>
                                        <span class="ml-3 text-sm text-gray-600">
                                            {{ $rol['activo'] ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

    </div>

    <script>
    document.querySelectorAll('.toggle-modulo').forEach(checkbox => {
        checkbox.addEventListener('change', async function () {
            const label = this.closest('label').querySelector('span');

            try {
                const res = await fetch('{{ route('root.modulos.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_negocio: this.dataset.negocio,
                        id_modulo:  this.dataset.modulo,
                        id_rol:     parseInt(this.dataset.rol),
                        activo:     this.checked,
                    })
                });

                const data = await res.json();

                if (data.ok) {
                    label.textContent = this.checked ? 'Activo' : 'Inactivo';
                } else {
                    // Revertir si algo falló
                    this.checked = !this.checked;
                }

            } catch (e) {
                this.checked = !this.checked;
            }
        });
    });
    </script>
</x-app-layout>