{{-- resources/views/administrador/catalogo/_marca_card.blade.php --}}
{{-- Recibe: $marca (con modelos, colores, voltajes cargados) --}}
@php
    $totalModelos  = $marca->modelos->count();
    $limiteModelos = 20;
    $pct           = $limiteModelos > 0 ? round(($totalModelos / $limiteModelos) * 100) : 0;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
     data-marca-id="{{ $marca->id_marca }}"
     id="marca-card-{{ $marca->id_marca }}"
     data-limit="{{ $limiteModelos }}"
     x-data="{ abierto: false }"
     @abrir-acordeon="abierto = true">

    {{-- Header marca --}}
    <div class="px-5 py-3.5 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center justify-between">

        <button type="button" @click="abierto = !abierto"
            class="flex items-center gap-2.5 flex-1 min-w-0 text-left">
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                :class="abierto ? 'rotate-90' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>
            <span class="text-sm font-semibold text-gray-800 dark:text-white truncate marca-nombre">{{ $marca->nombre_marca }}</span>
            <span class="text-xs text-gray-400 shrink-0 modelos-count">{{ $totalModelos }} {{ Str::plural('modelo', $totalModelos) }}</span>
        </button>

        <div class="flex items-center gap-2 shrink-0 ml-3">
            <button @click.stop="abrirEditMarca('{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}')"
                class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 transition
                    hover:border-yellow-500 dark:hover:border-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20">
                Editar
            </button>
            @if($totalModelos < $limiteModelos)
            <button @click.stop="abrirModeloModal('{{ $marca->id_marca }}', '{{ $marca->nombre_marca }}')"
                class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 transition
                    hover:border-green-500 dark:hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 btn-add-modelo">
                + Modelo
            </button>
            @else
            <button class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 rounded-md px-3 py-1.5 opacity-50 cursor-not-allowed btn-add-modelo" disabled>
                + Modelo
            </button>
            @endif
            <button @click.stop="abrirDeleteModal('marca', '{{ $marca->id_marca }}', '{{ addslashes($marca->nombre_marca) }}', '{{ route('admin.catalogo.marcas.destroy', $marca->id_marca) }}')"
                class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 rounded-md px-3 py-1.5 transition">
                Eliminar
            </button>
        </div>
    </div>

    {{-- Contenido colapsable --}}
    <div x-show="abierto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1">

        <div class="overflow-x-auto overflow-y-visible">
            <div class="grid grid-cols-3 border-b dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 sticky top-0 z-10">
                <div class="px-5 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400 border-r dark:border-gray-700">Modelo</div>
                <div class="px-4 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400 border-r dark:border-gray-700">Colores</div>
                <div class="px-4 py-2.5 text-[11px] font-medium uppercase tracking-wider text-gray-400">Voltajes</div>
            </div>

            <div class="max-h-[320px] overflow-y-auto">
                <div class="modelos-tbody">
                @forelse($marca->modelos as $modelo)
                <div class="grid grid-cols-3 border-b dark:border-gray-700 last:border-b-0"
                     data-modelo-id="{{ $modelo->id_modelo }}">

                    <div class="px-5 py-3 border-r dark:border-gray-700 flex items-center justify-between group">
                        <span class="text-sm text-gray-800 dark:text-gray-200 modelo-nombre">{{ $modelo->nombre_modelo }}</span>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="abrirEditModelo('{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ $modelo->id_marca }}')"
                                class="p-1 text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="abrirDeleteModal('modelo', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ route('admin.catalogo.modelos.destroy', $modelo->id_modelo) }}')"
                                class="p-1 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-4 py-3 border-r dark:border-gray-700">
                        <div class="flex items-center gap-1.5 flex-wrap colores-container">
                            @foreach($modelo->colores as $color)
                            @php
                                $hexes  = colorHexes($color->color);
                                $nombre = colorNombre($color->color);
                                $esComb = colorEsCombinado($color->color);
                            @endphp
                            <div class="relative group/chip w-7 h-7 rounded-md border border-black/10 dark:border-white/10 overflow-hidden cursor-pointer flex-shrink-0"
                                title="{{ $nombre }}"
                                data-color-id="{{ $color->id_color }}">
                                @if($esComb)
                                    <div class="absolute left-0 top-0 w-1/2 h-full" style="background:{{ $hexes[0] }}"></div>
                                    <div class="absolute right-0 top-0 w-1/2 h-full" style="background:{{ $hexes[1] ?? $hexes[0] }}"></div>
                                @else
                                    <div class="w-full h-full" style="background:{{ $hexes[0] }}"></div>
                                @endif
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover/chip:opacity-100 transition flex items-center justify-center gap-0.5">
                                    <button @click="abrirEditColor('{{ $color->id_color }}', '{{ addslashes($color->color) }}', '{{ $modelo->id_modelo }}')"
                                        class="text-white text-[9px] p-0.5 hover:text-yellow-300 transition">✎</button>
                                    <button @click="abrirDeleteModal('color', '{{ $color->id_color }}', '{{ addslashes($nombre) }}', '{{ route('admin.catalogo.colores.destroy', $color->id_color) }}')"
                                        class="text-white text-[9px] p-0.5 hover:text-red-300 transition">✕</button>
                                </div>
                            </div>
                            @endforeach
                            <button @click="abrirColorModal('{{ $marca->id_marca }}', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ addslashes($marca->nombre_marca) }}')"
                                class="w-7 h-7 rounded-md border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:border-gray-400 transition text-sm">
                                +
                            </button>
                        </div>
                    </div>

                    <div class="px-4 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap voltajes-container">
                            @foreach($modelo->voltajes as $voltaje)
                            <span class="group/pill inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full px-2.5 py-0.5 text-xs text-gray-700 dark:text-gray-300"
                                  data-mvoltaje-id="{{ $voltaje->pivot->id_mvoltaje }}">
                                {{ $voltaje->voltaje }}
                                <button @click="abrirDeleteModal('voltaje', '{{ $voltaje->pivot->id_mvoltaje }}', '{{ $voltaje->voltaje }}', '{{ route('admin.catalogo.modelo-voltaje.destroy', $voltaje->pivot->id_mvoltaje) }}')"
                                    class="text-gray-400 hover:text-red-500 transition opacity-0 group-hover/pill:opacity-100 text-[10px] leading-none">✕</button>
                            </span>
                            @endforeach
                            <button @click="abrirVoltajeModal('{{ $marca->id_marca }}', '{{ $modelo->id_modelo }}', '{{ addslashes($modelo->nombre_modelo) }}', '{{ addslashes($marca->nombre_marca) }}')"
                                class="inline-flex items-center gap-1 border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2.5 py-0.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:border-gray-400 transition">
                                + voltaje
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500 sin-modelos">
                    Sin modelos. Agrega el primero con el botón "+ Modelo".
                </div>
                @endforelse
                </div>
            </div>
        </div>

        <div class="px-5 py-2.5 border-t dark:border-gray-700 flex items-center gap-3 bg-gray-50/50 dark:bg-gray-800/40">
            <span class="text-[11px] text-gray-400 whitespace-nowrap progress-label">{{ $totalModelos }} / {{ $limiteModelos }} modelos</span>
            <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all progress-fill {{ $pct >= 80 ? 'bg-amber-400' : 'bg-emerald-500' }}"
                    style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>
</div>