<x-app-layout>
<div class="max-w-lg mx-auto space-y-5" x-data="cobrarOt()" x-init="init()">

    {{-- ── Header ── --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('reparaciones.index') }}"
           class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-1.5 -ml-1.5 rounded-lg
                  hover:bg-gray-100 dark:hover:bg-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Cobrar orden de trabajo</h2>
            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $rep->id_reparacion }}</p>
        </div>
    </div>

    {{-- ── Flash ── --}}
    <div x-show="flashVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
        <div class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl min-w-[280px] pointer-events-auto"
             :class="flashTipo==='error'
                 ? 'bg-red-100 dark:bg-red-800/30 ring-1 ring-red-200 dark:ring-red-700'
                 : 'bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'">
            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="flashMsg"></p>
        </div>
    </div>

    {{-- ── Resumen de la OT ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">
                    {{ $rep->cliente_nombre ?? $rep->cliente?->nombre_cliente ?? 'Cliente' }}
                </p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">
                    {{ $rep->num_serie ?? $rep->unidad_descripcion ?? '—' }}
                </p>
            </div>
            <span class="text-[11px] font-medium px-2.5 py-1 rounded-full shrink-0
                {{ $rep->tipo === 'garantia' ? 'bg-purple-100 dark:bg-purple-800/30 text-purple-800 dark:text-purple-400' : '' }}
                {{ $rep->tipo === 'mantenimiento' ? 'bg-blue-100 dark:bg-blue-800/30 text-blue-800 dark:text-blue-400' : '' }}
                {{ $rep->tipo === 'reparacion' ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                {{ ucfirst($rep->tipo) }}
            </span>
        </div>

        @if($rep->piezas->count() > 0)
        <div class="space-y-1.5 border-t border-gray-100 dark:border-gray-700 pt-3">
            @foreach($rep->piezas as $p)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600 dark:text-gray-300 flex items-center gap-1.5">
                    {{ ($p->pieza?->nombre ?? $p->descripcion ?? '—') }} × {{ $p->cantidad }}
                    @if($p->es_garantia)
                        <span class="text-[9px] font-medium px-1.5 py-0.5 rounded-full
                                     bg-purple-100 dark:bg-purple-800/30 text-purple-700 dark:text-purple-400">
                            Garantía
                        </span>
                    @endif
                </span>
                <span class="font-medium text-gray-700 dark:text-gray-300">${{ number_format($p->subtotal, 2) }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <div class="space-y-1 border-t border-gray-100 dark:border-gray-700 pt-3">
            @if($rep->costo_mano_obra > 0)
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400">Mano de obra</span>
                <span class="text-gray-600 dark:text-gray-300 font-medium">${{ number_format($rep->costo_mano_obra, 2) }}</span>
            </div>
            @endif
            @if($rep->costo_reparacion > 0 && $rep->tipo !== 'garantia')
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ $rep->tipo === 'mantenimiento' ? 'Costo de mantenimiento' : 'Costo base' }}</span>
                <span class="text-gray-600 dark:text-gray-300 font-medium">${{ number_format($rep->costo_reparacion, 2) }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between text-base pt-2 mt-1 border-t border-gray-100 dark:border-gray-700">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Total a cobrar</span>
                <span class="font-bold text-gray-900 dark:text-white">${{ number_format($rep->costo_total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── Pago ── --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pago</h3>
            <button type="button" @click="agregarPago()" :disabled="!puedeAgregarPago"
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800
                           dark:hover:text-gray-200 transition flex items-center gap-1 disabled:opacity-40">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Dividir pago
            </button>
        </div>

        <div class="px-5 py-4 space-y-3">
            <template x-for="(pago, i) in pagos" :key="i">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <select x-model="pago.id_metodo" @change="onMetodoChange(i)"
                                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                       px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition">
                            <option value="">— Método —</option>
                            @foreach($metodos as $m)
                            <option value="{{ $m['value'] }}"
                                    data-efectivo="{{ $m['es_efectivo'] ? '1' : '0' }}"
                                    data-ref="{{ $m['requiere_referencia'] ? '1' : '0' }}">
                                {{ $m['label'] }}
                            </option>
                            @endforeach
                        </select>

                        <template x-if="pagos.length > 1">
                            <div class="relative w-28 shrink-0">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">$</span>
                                <input type="number" step="0.01" min="0" x-model="pago.monto" @input="recalcularPagos(i)"
                                       class="w-full pl-6 pr-2 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                              text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30 transition tabular-nums">
                            </div>
                        </template>

                        <button type="button" @click="quitarPago(i)"
                                x-show="pagos.length > 1 && !(pago.es_efectivo && pagos.filter(p => p.es_efectivo).length === 1)"
                                class="shrink-0 text-gray-300 hover:text-red-500 dark:hover:text-red-400 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div x-show="pago.requiere_referencia"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1">
                        <input type="text" x-model="pago.referencia" placeholder="Folio / últimos 4 dígitos…"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white
                                      px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30
                                      transition placeholder:text-gray-300 dark:placeholder:text-gray-600">
                    </div>
                </div>
            </template>

            <div class="pt-1 space-y-1.5">
                <div class="h-0.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         :class="pagosCubreTotal ? 'bg-green-500' : 'bg-gray-400'"
                         :style="'width:' + Math.min(100, total > 0 ? (sumaPagos / total * 100) : 0) + '%'"></div>
                </div>
                <div class="flex items-center justify-between text-xs"
                     :class="pagosCubreTotal ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">
                    <span x-text="pagosCubreTotal ? '✓ Pago completo' : 'Pendiente'"></span>
                    <template x-if="!pagosCubreTotal">
                        <span class="tabular-nums" x-text="fmt(Math.max(0, total - sumaPagos)) + ' por asignar'"></span>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <button type="button" @click="submitCobro()" :disabled="!puedeRegistrar"
            class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white py-3 rounded-xl text-sm font-semibold
                   flex items-center justify-center gap-2 hover:opacity-90 active:scale-[.99] transition
                   disabled:opacity-40 disabled:cursor-not-allowed">
        <template x-if="!enviando">
            <span x-text="btnLabel"></span>
        </template>
        <template x-if="enviando">
            <span class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Procesando…
            </span>
        </template>
    </button>

</div>

<script>
function cobrarOt() {
    return {
        total: {{ (float) $rep->costo_total }},
        pagos: [],
        enviando: false,
        flashVisible: false, flashMsg: '', flashTipo: 'success', flashTimer: null,

        _nuevoPago(esEfectivo = false) {
            return { id_metodo: esEfectivo ? 'efectivo' : '', monto: '', referencia: '', es_efectivo: esEfectivo, requiere_referencia: false };
        },

        init() {
            this.pagos = [this._nuevoPago(true)];
            this.pagos[0].monto = this.total.toFixed(2);
        },

        get sumaPagos() {
            return this.pagos.reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
        },
        get pagosCubreTotal() {
            if (this.pagos.length === 1) return true;
            return Math.round(this.sumaPagos * 100) >= Math.round(this.total * 100);
        },
        get puedeAgregarPago() { return this.total > 0; },
        get puedeRegistrar() { return !this.enviando && this.pagosCubreTotal && this.pagos.every(p => p.id_metodo); },
        get btnLabel() {
            if (!this.pagos.every(p => p.id_metodo)) return 'Selecciona un método de pago';
            if (!this.pagosCubreTotal) return 'Completa el pago para continuar';
            return 'Cobrar y entregar · ' + this.fmt(this.total);
        },

        agregarPago() { this.pagos.push(this._nuevoPago()); },
        quitarPago(idx) {
            if (this.pagos.length <= 1) return;
            if (this.pagos[idx].es_efectivo && this.pagos.filter(p => p.es_efectivo).length === 1) return;
            this.pagos.splice(idx, 1);
            this.recalcularPagos();
        },
        onMetodoChange(idx) {
            const selects = document.querySelectorAll('[x-model="pago.id_metodo"]');
            const select  = selects[idx];
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;
            this.pagos[idx].es_efectivo         = opt.dataset.efectivo === '1';
            this.pagos[idx].requiere_referencia = opt.dataset.ref === '1';
        },
        recalcularPagos(idxEditado) {
            if (this.pagos.length < 2) return;
            const destino = idxEditado === 0 ? this.pagos.length - 1 : 0;
            const ocupado = this.pagos.reduce((s, p, j) => (j !== destino) ? s + (parseFloat(p.monto) || 0) : s, 0);
            const resta = Math.round((this.total - ocupado) * 100) / 100;
            this.pagos[destino].monto = Math.max(0, resta).toFixed(2);
        },

        fmt(n) { return '$' + Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        flash(msg, tipo = 'success') {
            this.flashMsg = msg; this.flashTipo = tipo; this.flashVisible = true;
            clearTimeout(this.flashTimer);
            this.flashTimer = setTimeout(() => this.flashVisible = false, tipo === 'error' ? 4500 : 3000);
        },

        async submitCobro() {
            if (!this.puedeRegistrar) return;
            this.enviando = true;
            try {
                const pagosEnviar = this.pagos.length === 1
                    ? [{ ...this.pagos[0], monto: this.total.toFixed(2) }]
                    : this.pagos;

                const res = await fetch(`{{ route('reparaciones.cobrar', $rep->id_reparacion) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        pagos: pagosEnviar.map(p => ({ metodo: p.id_metodo, monto: parseFloat(p.monto || 0), referencia: p.referencia || null })),
                    }),
                });
                const data = await res.json();
                if (!data.ok) { this.flash(data.mensaje ?? 'Error al cobrar', 'error'); return; }

                if (data.id_venta) {
                    window.open(`{{ route('ventas.ticket', ':id') }}`.replace(':id', data.id_venta), '_blank');
                }

                window.location.href = "{{ route('reparaciones.index') }}?cobrado=1";
            } catch { this.flash('Error de conexión', 'error'); }
            finally { this.enviando = false; }
        },
    }
}
</script>
</x-app-layout>
