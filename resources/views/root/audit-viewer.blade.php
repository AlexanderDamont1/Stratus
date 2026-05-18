{{-- resources/views/root/audit-viewer.blade.php --}}
<x-app-layout>
<div class="mx-auto space-y-7" x-data="auditViewer()" x-init="init()">

    <x-flash-messages />

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Auditoría</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                Registro de actividad en tiempo real ·
                <span class="font-mono" x-text="'audit-' + date + '.log'"></span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- Indicador Reverb --}}
            <span
                class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-md border transition-colors"
                :class="live
                    ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400'
                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-400'"
            >
                <span class="w-1.5 h-1.5 rounded-full"
                      :class="live ? 'bg-green-500 animate-pulse' : 'bg-gray-300 dark:bg-gray-600'"></span>
                <span x-text="live ? 'Reverb conectado' : 'Sin conexión'"></span>
            </span>

            {{-- Selector de fecha --}}
            <input
                type="date"
                x-model="date"
                @change="loadDay()"
                class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                       text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5
                       focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-500"
            />
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <template x-for="stat in stats" :key="stat.label">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3">
                <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider mb-1"
                   x-text="stat.label"></p>
                <p class="text-2xl font-semibold" :class="stat.color" x-text="stat.value"></p>
            </div>
        </template>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-wrap items-center gap-2">
        <input
            x-model="filters.search"
            type="text"
            placeholder="Buscar usuario, IP, negocio…"
            class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                   text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 w-56
                   focus:outline-none focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-500
                   placeholder-gray-400 dark:placeholder-gray-500"
        />
        <select
            x-model="filters.nivel"
            class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                   text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5
                   focus:outline-none focus:ring-1 focus:ring-gray-400"
        >
            <option value="">Todos los niveles</option>
            <option>DANGER</option>
            <option>CRITICAL</option>
            <option>WARNING</option>
            <option>INFO</option>
        </select>
        <select
            x-model="filters.cat"
            class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                   text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5
                   focus:outline-none focus:ring-1 focus:ring-gray-400"
        >
            <option value="">Todas las categorías</option>
            <option>AUTH</option>
            <option>ACCESS</option>
            <option>SECURITY</option>
            <option>DATA</option>
            <option>EXPORT</option>
            <option>CONFIG</option>
            <option>BILLING</option>
            <option>ADMIN</option>
        </select>

        <span class="ml-auto text-xs text-gray-400" x-text="filtered.length + ' eventos'"></span>
    </div>

    {{-- Tabla --}}
    <div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-3">Eventos</p>

        {{-- Vacío / cargando --}}
        <template x-if="filtered.length === 0">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span x-show="loading">Cargando log…</span>
                    <span x-show="!loading">Sin eventos para los filtros actuales.</span>
                </p>
            </div>
        </template>

        <template x-if="filtered.length > 0">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

                {{-- Cabecera --}}
                <div class="grid border-b border-gray-100 dark:border-gray-700
                            text-[10px] font-medium text-gray-400 uppercase tracking-wider
                            bg-gray-50 dark:bg-gray-800/60"
                     style="grid-template-columns: 148px 80px 82px 76px 116px 1fr">
                    <div class="px-4 py-2">Timestamp</div>
                    <div class="px-3 py-2">Nivel</div>
                    <div class="px-3 py-2">Categoría</div>
                    <div class="px-3 py-2">Negocio</div>
                    <div class="px-3 py-2">Usuario</div>
                    <div class="px-3 py-2">Detalle</div>
                </div>

                {{-- Filas --}}
                <div class="overflow-y-auto max-h-[560px] divide-y divide-gray-100 dark:divide-gray-700/60"
                     x-ref="logScroll">

                    <template x-for="(row, idx) in filtered" :key="row.ts + idx">
                        <div>
                            <div
                                @click="toggleDetail(idx)"
                                class="grid cursor-pointer transition-colors duration-100
                                       hover:bg-gray-50 dark:hover:bg-gray-700/40"
                                :class="{
                                    'bg-red-50  dark:bg-red-900/10':  row.nivel === 'DANGER',
                                    'bg-pink-50 dark:bg-pink-900/10': row.nivel === 'CRITICAL',
                                    '!bg-green-50/70 dark:!bg-green-900/10': row._new
                                }"
                                style="grid-template-columns: 148px 80px 82px 76px 116px 1fr"
                            >
                                <div class="px-4 py-2.5 font-mono text-[11px] text-gray-400 dark:text-gray-500 whitespace-nowrap flex items-center"
                                     x-text="row.ts"></div>
                                <div class="px-3 py-2.5 flex items-center">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium"
                                          :class="nivelClass(row.nivel)" x-text="row.nivel"></span>
                                </div>
                                <div class="px-3 py-2.5 flex items-center">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium"
                                          :class="catClass(row.cat)" x-text="row.cat || '—'"></span>
                                </div>
                                <div class="px-3 py-2.5 text-[11px] text-gray-500 dark:text-gray-400 flex items-center"
                                     x-text="row.neg"></div>
                                <div class="px-3 py-2.5 text-[11px] font-mono text-gray-600 dark:text-gray-300 truncate flex items-center"
                                     x-text="row.usr"></div>
                                <div class="px-3 py-2.5 text-xs text-gray-700 dark:text-gray-200 truncate flex items-center"
                                     x-text="row.detail"></div>
                            </div>

                            {{-- Raw expandido --}}
                            <div
                                x-show="openIdx === idx"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="px-4 py-3 border-t border-gray-100 dark:border-gray-700
                                       bg-gray-50 dark:bg-gray-900/40 font-mono text-[10px]
                                       text-gray-500 dark:text-gray-400 leading-relaxed
                                       whitespace-pre-wrap break-all"
                                x-text="row.raw"
                            ></div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <p class="text-[10px] text-gray-400 mt-2 text-right">
            Los eventos más recientes aparecen primero · Haz clic en una fila para ver el raw completo
        </p>
    </div>

</div>

@push('scripts')
<script>
function auditViewer() {
    return {
        date:    new Date().toISOString().slice(0, 10),
        logs:    [],
        openIdx: null,
        live:    false,
        loading: false,
        filters: { search: '', nivel: '', cat: '' },

        get filtered() {
            return this.logs
                .filter(r => {
                    if (this.filters.nivel  && r.nivel !== this.filters.nivel)  return false
                    if (this.filters.cat    && r.cat   !== this.filters.cat)    return false
                    if (this.filters.search && !r.raw.toLowerCase().includes(this.filters.search.toLowerCase())) return false
                    return true
                })
                .slice()
                .reverse()
        },

        get stats() {
            const l     = this.logs
            const users = new Set(l.map(r => r.usr.split('|')[0]).filter(Boolean)).size
            const biz   = new Set(l.map(r => r.neg).filter(n => /^\d+$/.test(n))).size
            return [
                { label: 'Total',    value: l.length,                                    color: 'text-gray-800 dark:text-gray-200' },
                { label: 'Danger',   value: l.filter(r => r.nivel === 'DANGER').length,   color: 'text-red-600 dark:text-red-400' },
                { label: 'Critical', value: l.filter(r => r.nivel === 'CRITICAL').length, color: 'text-pink-600 dark:text-pink-400' },
                { label: 'Warning',  value: l.filter(r => r.nivel === 'WARNING').length,  color: 'text-yellow-600 dark:text-yellow-400' },
                { label: 'Usuarios', value: users,                                         color: 'text-blue-600 dark:text-blue-400' },
                { label: 'Negocios', value: biz,                                           color: 'text-green-600 dark:text-green-400' },
            ]
        },

        init() {
            this.loadDay()
            this.connectReverb()
        },

        async loadDay() {
            this.logs    = []
            this.openIdx = null
            this.loading = true
            try {
                const res  = await fetch(`/root/audit-log?date=${this.date}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                const data = await res.json()
                this.logs  = data.map(line => this.parse(line))
            } catch (e) {
                console.error('Error cargando log:', e)
            } finally {
                this.loading = false
            }
        },

        connectReverb() {
            if (typeof window.Echo === 'undefined') return

            window.Echo.private('audit.root')
                .listen('.audit.line', (e) => {
                    // Solo inyectar si estamos viendo el día de hoy
                    if (this.date !== new Date().toISOString().slice(0, 10)) return

                    const parsed = this.parse(e.line)
                    parsed._new  = true
                    this.logs.push(parsed)
                    this.live = true

                    setTimeout(() => { parsed._new = false }, 3000)

                    this.$nextTick(() => {
                        const el = this.$refs.logScroll
                        if (el) el.scrollTop = 0
                    })
                })

            window.Echo.connector.pusher.connection.bind('disconnected', () => { this.live = false })
            window.Echo.connector.pusher.connection.bind('connected',    () => { this.live = true  })
        },

        parse(line) {
            return {
                raw:    line,
                ts:     (line.match(/^\[([^\]]+)\]/)                    || [])[1] || '',
                nivel:  ((line.match(/\] \[([A-Z]+)\s*\]/)             || [])[1] || '').trim(),
                cat:    ((line.match(/\]\s\[([A-Z]+)\s*\]\s\[NEGOCIO/) || [])[1] || '').trim(),
                neg:    (line.match(/\[NEGOCIO:([^\]]+)\]/)             || [])[1] || '',
                usr:    (line.match(/\[USR:([^\]]+)\]/)                 || [])[1] || '',
                ip:     (line.match(/\[IP:([^\]]+)\]/)                  || [])[1] || '',
                detail: (line.match(/\]\s([^\[].+)$/)                   || [])[1] || line,
                _new:   false,
            }
        },

        toggleDetail(idx) {
            this.openIdx = this.openIdx === idx ? null : idx
        },

        nivelClass(n) {
            return {
                'DANGER':   'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300',
                'CRITICAL': 'bg-pink-100 dark:bg-pink-900/40 text-pink-700 dark:text-pink-300',
                'WARNING':  'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
                'INFO':     'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            }[n] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
        },

        catClass(c) {
            return {
                'AUTH':     'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'ACCESS':   'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300',
                'SECURITY': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                'DATA':     'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300',
                'EXPORT':   'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
                'CONFIG':   'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                'BILLING':  'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300',
                'ADMIN':    'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300',
            }[c] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-500'
        },
    }
}
</script>
@endpush

</x-app-layout>