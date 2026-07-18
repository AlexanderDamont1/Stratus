<x-app-layout>
<div class="mx-auto space-y-6" x-data="historialApp()" x-init="init()">

    {{-- ===== ENCABEZADO ===== --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('admin.movimientos.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                      flex items-center gap-1 mb-2 transition w-fit">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al tracking
            </a>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Historial de vehículos</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                <span x-text="total"></span> vehículo<span x-text="total === 1 ? '' : 's'"></span> registrado<span x-text="total === 1 ? '' : 's'"></span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form @submit.prevent="buscar()" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" x-model="busqueda"
                           placeholder="Serie, marca o modelo…"
                           class="border border-gray-200 dark:border-gray-600 rounded-lg pl-8 pr-3 py-2 text-sm w-52
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-xs text-gray-400">Del</label>
                    <input type="date" x-model="desde" @change="buscar()"
                           class="border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-2 text-sm
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <label class="text-xs text-gray-400">al</label>
                    <input type="date" x-model="hasta" @change="buscar()"
                           class="border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-2 text-sm
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                  focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                <select x-model="sucursal" @change="buscar()"
                        class="border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-sm
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <option value="">Todas las sucursales</option>
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id_usuario }}">{{ $s->nombre_usuario }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2
                               rounded-lg text-sm font-medium hover:opacity-90 transition active:scale-95">
                    Buscar
                </button>
                <button type="button" @click="limpiar()" x-show="busqueda || desde || hasta || sucursal"
                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    Limpiar
                </button>
            </form>

            <a :href="pdfHref()" x-show="desde || hasta || sucursal"
               class="inline-flex items-center gap-1.5 border border-gray-200 dark:border-gray-600
                      text-gray-600 dark:text-gray-300 px-3.5 py-2 rounded-lg text-sm font-medium
                      hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </a>
        </div>
    </div>

    {{-- ===== KPIs ===== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3.5">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Total registrados</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="stats.total"></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3.5">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">En fábrica</p>
            <p class="text-2xl font-semibold text-gray-500 dark:text-gray-400" x-text="stats.en_fabrica"></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3.5">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">En sucursal</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400" x-text="stats.en_sucursal"></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3.5">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Vendidas</p>
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400" x-text="stats.vendidas"></p>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div x-show="cargando" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-10 text-center">
        <p class="text-sm text-gray-400">Cargando…</p>
    </div>

    <div x-show="!cargando && items.length === 0" x-cloak
         class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-10 text-center">
        <p class="text-sm text-gray-400">
            <span x-show="busqueda || desde || hasta">No hay vehículos que coincidan con los filtros.</span>
            <span x-show="!busqueda && !desde && !hasta">Aún no hay vehículos registrados.</span>
        </p>
    </div>

    <div x-show="!cargando && items.length > 0" x-cloak
         class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

        {{-- ===== DESKTOP ===== --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">N° de serie</th>
                        <th class="px-2 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Marca</th>
                        <th class="px-2 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Modelo</th>
                        <th class="px-2 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">Distinción</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">En fábrica</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">Ingreso a sucursal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide whitespace-nowrap">Vendida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <template x-for="bici in items" :key="bici.num_serie">
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition-colors duration-100">
                            <td class="px-4 py-3 font-mono text-xs font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap" x-text="bici.num_serie"></td>
                            <td class="px-2 py-3 text-xs text-gray-600 dark:text-gray-300 max-w-[90px] truncate" :title="bici.marca ?? '—'" x-text="bici.marca ?? '—'"></td>
                            <td class="px-2 py-3 text-xs text-gray-600 dark:text-gray-300 max-w-[100px] truncate" :title="bici.modelo ?? '—'" x-text="bici.modelo ?? '—'"></td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span :title="'Color: ' + parsearColor(bici.color).nombre" class="cursor-default">
                                        <template x-if="parsearColor(bici.color).hexes.length >= 2">
                                            <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                                <span class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(bici.color).hexes[0]"></span>
                                                <span class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(bici.color).hexes[1]"></span>
                                            </span>
                                        </template>
                                        <template x-if="parsearColor(bici.color).hexes.length < 2">
                                            <span class="w-3.5 h-3.5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                                  :style="'background:' + parsearColor(bici.color).hexes[0]"></span>
                                        </template>
                                    </span>
                                    <span :title="'Voltaje: ' + (bici.voltaje ?? '—')"
                                          class="flex items-center gap-0.5 text-xs text-gray-600 dark:text-gray-300 cursor-default">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                        </svg>
                                        <span x-text="bici.voltaje ?? '—'"></span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                <p class="text-gray-700 dark:text-gray-300" x-text="fmtFecha(bici.created_at)?.fecha ?? '—'"></p>
                                <p class="text-gray-400" x-text="fmtFecha(bici.created_at)?.hora ?? ''"></p>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                <template x-if="bici.fecha_ingreso_sucursal">
                                    <div>
                                        <p class="text-gray-700 dark:text-gray-300" x-text="fmtFecha(bici.fecha_ingreso_sucursal).fecha"></p>
                                        <p class="text-gray-400" x-text="fmtFecha(bici.fecha_ingreso_sucursal).hora"></p>
                                    </div>
                                </template>
                                <span x-show="!bici.fecha_ingreso_sucursal" class="text-gray-300 dark:text-gray-600">—</span>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                <template x-if="bici.fecha_vendida">
                                    <div>
                                        <p class="text-emerald-600 dark:text-emerald-400 font-medium" x-text="fmtFecha(bici.fecha_vendida).fecha"></p>
                                        <p class="text-gray-400" x-text="fmtFecha(bici.fecha_vendida).hora"></p>
                                    </div>
                                </template>
                                <span x-show="!bici.fecha_vendida" class="text-gray-300 dark:text-gray-600">—</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- ===== MÓVIL ===== --}}
        <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-700/60">
            <template x-for="bici in items" :key="bici.num_serie">
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-mono text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="bici.num_serie"></p>
                            <p class="text-xs text-gray-400 truncate">
                                <span x-text="bici.marca ?? '—'"></span> · <span x-text="bici.modelo ?? '—'"></span>
                            </p>
                        </div>

                        {{-- Distinción con tooltip táctil (tap para ver qué es) --}}
                        <div class="flex items-center gap-2.5 shrink-0">
                            <div x-data="{ showTooltip: false, timeout: null }"
                                 @click="if(timeout) clearTimeout(timeout); showTooltip = true; timeout = setTimeout(() => showTooltip = false, 1500)"
                                 class="relative cursor-pointer">
                                <template x-if="parsearColor(bici.color).hexes.length >= 2">
                                    <span class="w-5 h-5 rounded-sm border border-black/10 dark:border-white/10 overflow-hidden relative inline-flex shrink-0">
                                        <span class="absolute left-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(bici.color).hexes[0]"></span>
                                        <span class="absolute right-0 top-0 w-1/2 h-full" :style="'background:' + parsearColor(bici.color).hexes[1]"></span>
                                    </span>
                                </template>
                                <template x-if="parsearColor(bici.color).hexes.length < 2">
                                    <span class="w-5 h-5 rounded-sm border border-black/10 dark:border-white/10 shrink-0 inline-block"
                                          :style="'background:' + parsearColor(bici.color).hexes[0]"></span>
                                </template>
                                <div x-show="showTooltip" x-cloak
                                     x-transition.opacity.duration.200ms
                                     class="absolute z-10 bottom-full right-0 mb-1 px-2 py-1 text-[10px] bg-black/80 text-white rounded whitespace-nowrap pointer-events-none"
                                     x-text="parsearColor(bici.color).nombre">
                                </div>
                            </div>
                            <span class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300">
                                <svg class="w-3 h-3 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M11.983 1.907a.75.75 0 00-1.292-.657L4.5 9.75a.75.75 0 00.6 1.207h4.043l-1.556 6.222a.75.75 0 001.32.638l6.5-8.5a.75.75 0 00-.598-1.207h-3.858l1.032-4.203a.75.75 0 00-.001-.001z"/>
                                </svg>
                                <span x-text="bici.voltaje ?? '—'"></span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-[11px]">
                        <div>
                            <p class="text-gray-400">Fábrica</p>
                            <p class="text-gray-700 dark:text-gray-300" x-text="fmtFecha(bici.created_at)?.fecha ?? '—'"></p>
                        </div>
                        <div>
                            <p class="text-gray-400">Sucursal</p>
                            <p class="text-gray-700 dark:text-gray-300" x-text="fmtFecha(bici.fecha_ingreso_sucursal)?.fecha ?? '—'"></p>
                        </div>
                        <div>
                            <p class="text-gray-400">Vendida</p>
                            <p :class="bici.fecha_vendida ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-gray-300 dark:text-gray-600'"
                               x-text="fmtFecha(bici.fecha_vendida)?.fecha ?? '—'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- ===== PAGINACIÓN ===== --}}
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <button @click="cambiarPagina(paginaActual - 1)" :disabled="paginaActual === 1"
                    class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                           text-gray-500 dark:text-gray-400 disabled:opacity-40
                           hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                ← Anterior
            </button>
            <span class="text-xs text-gray-400">
                Página <span x-text="paginaActual"></span> / <span x-text="lastPage"></span>
            </span>
            <button @click="cambiarPagina(paginaActual + 1)" :disabled="paginaActual === lastPage"
                    class="text-xs px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg
                           text-gray-500 dark:text-gray-400 disabled:opacity-40
                           hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Siguiente →
            </button>
        </div>
    </div>

</div>

<script>
function historialApp() {
    return {
        items: [],
        stats: { total: 0, en_fabrica: 0, en_sucursal: 0, vendidas: 0 },
        cargando: true,
        busqueda: '', desde: '', hasta: '', sucursal: '',
        paginaActual: 1, lastPage: 1, total: 0,

        init() {
            const params = new URLSearchParams(window.location.search);
            this.busqueda    = params.get('q') || '';
            this.desde       = params.get('desde') || '';
            this.hasta       = params.get('hasta') || '';
            this.sucursal    = params.get('sucursal') || '';
            this.paginaActual = parseInt(params.get('page') || '1', 10);
            this.cargar();
        },

        async cargar() {
            this.cargando = true;
            try {
                const p = new URLSearchParams({ page: this.paginaActual });
                if (this.busqueda) p.set('q', this.busqueda);
                if (this.desde)    p.set('desde', this.desde);
                if (this.hasta)    p.set('hasta', this.hasta);
                if (this.sucursal) p.set('sucursal', this.sucursal);

                const res = await fetch(`{{ route('admin.movimientos.tabla') }}?${p}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!data.ok) return;

                this.items    = data.items;
                this.stats    = data.stats;
                this.lastPage = data.last_page;
                this.total    = data.total;
            } catch { /* deja el listado anterior visible si falla */ }
            finally { this.cargando = false; }
        },

        buscar() {
            this.paginaActual = 1;
            this.cargar();
        },

        limpiar() {
            this.busqueda = ''; this.desde = ''; this.hasta = ''; this.sucursal = '';
            this.paginaActual = 1;
            this.cargar();
        },

        cambiarPagina(n) {
            if (n < 1 || n > this.lastPage) return;
            this.paginaActual = n;
            this.cargar();
        },

        pdfHref() {
            const p = new URLSearchParams();
            if (this.busqueda) p.set('q', this.busqueda);
            if (this.desde)    p.set('desde', this.desde);
            if (this.hasta)    p.set('hasta', this.hasta);
            if (this.sucursal) p.set('sucursal', this.sucursal);
            return `{{ route('admin.movimientos.tabla.pdf') }}?${p}`;
        },

        parsearColor(colorStr) {
            const [nombre, hexParte] = (colorStr || '').split('|');
            const hexes = hexParte ? hexParte.split('/') : ['#cccccc'];
            return { nombre: nombre?.trim() || colorStr || '—', hexes };
        },

        fmtFecha(iso) {
            if (!iso) return null;
            const d = new Date(iso);
            return {
                fecha: d.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'America/Mexico_City' }),
                hora:  d.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Mexico_City' }),
            };
        },
    };
}
</script>
</x-app-layout>
