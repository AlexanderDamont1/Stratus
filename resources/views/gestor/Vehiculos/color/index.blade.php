<x-app-layout>
<div
    x-data="{
        createModal: false,
        deleteModal: false,
        deleteId: null,
        deleteNombre: '',
        deleteAction: '',
        openDelete(id, nombre, action) {
            this.deleteId     = id;
            this.deleteNombre = nombre;
            this.deleteAction = action;
            this.deleteModal  = true;
        },

        // ── Color con picker + sugerencia de hex por IA ──
        colorNombre1: '',
        colorHex1: '#d5d5d5',
        colorNombre2: '',
        colorHex2: '#a12491',
        colorCombinado: false,
        sugirendoHex1: false,
        sugirendoHex2: false,
        colorError: '',
        get colorFinal() {
            const nombre = this.colorCombinado && this.colorNombre2
                ? this.colorNombre1 + '/' + this.colorNombre2
                : this.colorNombre1;
            const hex = this.colorCombinado && this.colorNombre2
                ? this.colorHex1 + '/' + this.colorHex2
                : this.colorHex1;
            return nombre ? nombre + '|' + hex : '';
        },
        validarNombreColor(val) {
            const bloqueadas = ['con','y','e','o','u','del','de','la','el','los','las'];
            const sufijos    = ['ito','ita','itos','itas','illo','illa','ote','ota'];
            const w = val.trim().toLowerCase();
            if (bloqueadas.includes(w)) { this.colorError = `"${w}" no es un color válido`; return false; }
            for (const s of sufijos) {
                if (w.endsWith(s) && w.length > s.length + 2) {
                    this.colorError = `"${w}" parece un diminutivo`; return false;
                }
            }
            this.colorError = '';
            return true;
        },
        async sugerirHexColor(nombre, campo) {
            if (!nombre || nombre.trim().length < 2) return;
            if (!this.validarNombreColor(nombre)) return;
            if (campo === 1) this.sugirendoHex1 = true;
            if (campo === 2) this.sugirendoHex2 = true;
            try {
                const url = {{ auth()->user()->id_rol === 1 ? "'" . route('admin.catalogo.sugerir-hex') . "'" : "'" . route('gestor.vehiculos.sugerir-hex') . "'" }};
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content },
                    body: JSON.stringify({ nombre: nombre.trim() }),
                });
                const data = await res.json();
                if (data.hex) {
                    if (campo === 1) this.colorHex1 = data.hex;
                    if (campo === 2) this.colorHex2 = data.hex;
                }
            } catch (e) { console.error(e); }
            finally {
                if (campo === 1) this.sugirendoHex1 = false;
                if (campo === 2) this.sugirendoHex2 = false;
            }
        },
        onColorNombre1(val) {
            if (!this.validarNombreColor(val)) return;
            clearTimeout(this._colorTimer1);
            this._colorTimer1 = setTimeout(() => this.sugerirHexColor(val, 1), 600);
        },
        onColorNombre2(val) {
            if (!this.validarNombreColor(val)) return;
            clearTimeout(this._colorTimer2);
            this._colorTimer2 = setTimeout(() => this.sugerirHexColor(val, 2), 600);
        },
    }"
    class="space-y-6"
>
    {{-- ===== MENSAJE FLASH ===== --}}
    <x-flash-messages />

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Colores</h2>
            <p class="text-xs text-gray-400 mt-0.5">Gestiona los colores disponibles por modelo</p>
        </div>
        <button
            @click="createModal = true; colorNombre1 = ''; colorHex1 = '#d5d5d5'; colorNombre2 = ''; colorHex2 = '#a12491'; colorCombinado = false; colorError = ''"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition"
        >
            + Crear color
        </button>
    </div>

    {{-- ===== ESTADÍSTICAS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total colores</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $colores->total() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Esta página</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $colores->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Página</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $colores->currentPage() }}<span class="text-sm font-normal text-gray-400 ml-1">/{{ $colores->lastPage() }}</span></p>
        </div>
    </div>

    {{-- ===== TABLA DE COLORES ===== --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Colores registrados</h3>
            <span class="text-xs text-gray-400">{{ $colores->total() }} total</span>
        </div>
        
        <!-- Vista en PC: tabla completa -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Color
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Modelo
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($colores as $color)
                        @php
                            $hexes  = colorHexes($color->color);
                            $nombre = colorNombre($color->color);
                            $esComb = colorEsCombinado($color->color);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            {{-- Color --}}
                            <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full overflow-hidden relative border border-black/10 dark:border-white/10 shrink-0">
                                        @if($esComb)
                                            <div class="absolute left-0 top-0 w-1/2 h-full" style="background:{{ $hexes[0] }}"></div>
                                            <div class="absolute right-0 top-0 w-1/2 h-full" style="background:{{ $hexes[1] ?? $hexes[0] }}"></div>
                                        @else
                                            <div class="w-full h-full" style="background:{{ $hexes[0] }}"></div>
                                        @endif
                                    </div>
                                    <span>{{ $nombre }}</span>
                                </div>
                            </td>
                            {{-- Modelo --}}
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $color->modelo->nombre_modelo ?? '—' }}
                            </td>
                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ auth()->user()->id_rol === 1 ? route('admin.catalogo.colores.edit', $color) : route('gestor.vehiculos.colores.edit', $color) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 text-xs font-semibold">
                                        Editar
                                    </a>
                                    
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No hay colores registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Vista en móvil: tabla compacta -->
        <div class="block md:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Color</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modelo</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($colores as $color)
                            @php
                                $hexesM  = colorHexes($color->color);
                                $nombreM = colorNombre($color->color);
                                $esCombM = colorEsCombinado($color->color);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 rounded-full mr-2 overflow-hidden relative border border-black/10 dark:border-white/10 shrink-0">
                                            @if($esCombM)
                                                <div class="absolute left-0 top-0 w-1/2 h-full" style="background:{{ $hexesM[0] }}"></div>
                                                <div class="absolute right-0 top-0 w-1/2 h-full" style="background:{{ $hexesM[1] ?? $hexesM[0] }}"></div>
                                            @else
                                                <div class="w-full h-full" style="background:{{ $hexesM[0] }}"></div>
                                            @endif
                                        </div>
                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                            {{ Str::limit($nombreM, 15) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $color->modelo->nombre_modelo ?? '—' }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <a href="{{ auth()->user()->id_rol === 1 ? route('admin.catalogo.colores.edit', $color) : route('gestor.vehiculos.colores.edit', $color) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 text-xs">
                                            Editar
                                        </a>
                                        
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs">No hay colores</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($colores->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $colores->links() }}
            </div>
        @endif
    </div>

    {{-- ===== MODAL: CREAR COLOR ===== --}}
    <div
        x-show="createModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        @click.self="createModal = false"
    >
        <div
            x-show="createModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-sm"
            @click.stop
        >
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-600 dark:bg-blue-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo color</h3>
                </div>
                <button
                    @click="createModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ auth()->user()->id_rol === 1 ? route('admin.catalogo.colores.store') : route('gestor.vehiculos.colores.store') }}"
                @submit="colorError ? $event.preventDefault() : null">
                @csrf
                <input type="hidden" name="color" :value="colorFinal">

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                            @click="$refs.gestorPicker1.click()">
                            <div class="absolute inset-0" :style="'background:'+colorHex1"></div>
                            <div x-show="sugirendoHex1" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </div>
                        </div>
                        <input type="color" x-ref="gestorPicker1" class="sr-only" x-model="colorHex1">
                        <input type="text" x-model="colorNombre1" @input="onColorNombre1($event.target.value)" required
                            class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                            placeholder="Ej: Rojo">
                    </div>

                    <div x-show="colorCombinado" x-transition>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden flex-shrink-0 cursor-pointer relative"
                                @click="$refs.gestorPicker2.click()">
                                <div class="absolute inset-0" :style="'background:'+colorHex2"></div>
                                <div x-show="sugirendoHex2" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                            </div>
                            <input type="color" x-ref="gestorPicker2" class="sr-only" x-model="colorHex2">
                            <input type="text" x-model="colorNombre2" @input="onColorNombre2($event.target.value)"
                                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                                placeholder="Ej: Azul">
                        </div>
                    </div>

                    <button type="button" @click="colorCombinado = !colorCombinado; colorNombre2 = ''"
                        :class="colorCombinado ? 'border-red-200 dark:border-red-800 text-red-500 dark:text-red-400' : 'border-dashed border-gray-300 dark:border-gray-600 text-gray-400'"
                        class="w-full flex items-center justify-center gap-2 border rounded-lg px-3 py-2 text-xs mb-2 hover:opacity-80 transition">
                        <span x-text="colorCombinado ? '✕ Quitar combinación' : '+ Agregar combinación de color'"></span>
                    </button>

                    <p x-show="colorError" x-text="colorError" class="text-xs text-red-500 mb-1.5"></p>
                    @error('color')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_modelo"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 transition"
                        required
                    >
                        <option value="">Seleccione un modelo</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id_modelo }}">
                                {{ $modelo->marca->nombre_marca ?? 'Sin marca' }} ~ {{ $modelo->nombre_modelo }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_modelo')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="createModal = false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="!colorNombre1.trim() || !!colorError || sugirendoHex1 || sugirendoHex2"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span x-show="!sugirendoHex1 && !sugirendoHex2">Guardar color</span>
                        <span x-show="sugirendoHex1 || sugirendoHex2" x-cloak>Sugiriendo color...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: CONFIRMAR ELIMINACIÓN ===== --}}
    <x-delete-modal />
</div>
</x-app-layout>