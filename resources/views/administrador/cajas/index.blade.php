{{-- resources/views/administrador/cajas/index.blade.php --}}
<x-app-layout>

<div class="space-y-6" x-data="adminCajas()">

    {{-- ── Alertas ── --}}
    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400" role="alert">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke-width="2.5"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400" role="alert">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke-width="2"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Gestión de Cajas</h2>
            <p class="text-xs text-gray-400 mt-0.5">Vista consolidada — todas las sucursales</p>
        </div>
        <button type="button"
            @click="modal = 'crear'; targetId = ''; targetName = ''; modalDesc = 'Selecciona la sucursal para asignarle una caja.'"
            class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
            + Asignar caja
        </button>
    </div>

    {{-- ── Resumen global ── --}}
    @php
        $totalSistema  = $snapshots->sum(fn($s) => $s['totales']['total_sistema'] ?? 0);
        $totalVentas   = $snapshots->sum(fn($s) => $s['totales']['ingresos_ventas'] ?? 0);
        $cajasAbiertas = $snapshots->count();
        $sucSinCaja    = $sucSinCaja ?? 0;  
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total en cajas</p>
            <p class="text-2xl font-semibold text-green-600 dark:text-green-400">${{ number_format($totalSistema, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Ventas del día</p>
            <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($totalVentas, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Sesiones abiertas</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $cajasAbiertas }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-5 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Sin caja</p>
            <p class="text-2xl font-semibold {{ $sucSinCaja > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white' }}">{{ $sucSinCaja }}</p>
        </div>
    </div>

    {{-- ── Grid de sucursales ── --}}
    @if($sucursales->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-16 text-center">
        <p class="text-3xl mb-3">🏪</p>
        <p class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No hay sucursales registradas</p>
        <p class="text-xs text-gray-400">Crea vendedores desde <strong>Admin → Vendedores</strong> para ver sus cajas aquí.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sucursales as $suc)
        @php
            $caja    = $cajas[$suc->id_usuario] ?? null;
            $snap    = $snapshots[$suc->id_usuario] ?? null;
            $sesion  = $caja?->sesionActiva;
            $abierta = $sesion !== null;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col">

            {{-- Cabecera tarjeta --}}
            <div class="px-5 py-4 border-b dark:border-gray-700 flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $suc->nombre_usuario }}</p>
                    @if($caja)
                    <p class="text-xs text-gray-400 font-mono mt-0.5 truncate">{{ $caja->nombre }}</p>
                    @endif
                </div>
                @if(!$caja)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 shrink-0">Sin caja</span>
                @elseif($abierta)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-400 shrink-0 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Abierta
                    </span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 shrink-0">Cerrada</span>
                @endif
            </div>

            {{-- Cuerpo --}}
            <div class="px-5 py-4 flex-1">
                @if(!$caja)
                <div class="text-center py-4">
                    <p class="text-2xl mb-2">📭</p>
                    <p class="text-xs text-gray-400 mb-4">Esta sucursal no tiene caja asignada.</p>
                    <button type="button"
                        @click="abrirModalCrear('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-400 hover:opacity-80 transition">
                        + Asignar caja
                    </button>
                </div>

                @elseif($abierta && $snap)
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Total sistema</p>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">${{ number_format($snap['totales']['total_sistema'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Ventas</p>
                        <p class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($snap['totales']['ingresos_ventas'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5"># Ventas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $snap['ventas_count'] ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5">
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Fondo inicial</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($snap['sesion']['fondo_inicial'] ?? 0, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400 py-1 border-t dark:border-gray-700">
                    <span>Abierta desde</span>
                    <span class="font-mono font-medium text-gray-600 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($snap['sesion']['abierta_at'])->format('d/m H:i') }}
                    </span>
                </div>
                @if(($snap['totales']['retiros'] ?? 0) > 0)
                <div class="flex items-center justify-between text-xs py-1">
                    <span class="text-gray-400">Retiros</span>
                    <span class="font-mono font-medium text-red-600 dark:text-red-400">-${{ number_format($snap['totales']['retiros'], 2) }}</span>
                </div>
                @endif
                @if(($snap['totales']['ingresos_manuales'] ?? 0) > 0)
                <div class="flex items-center justify-between text-xs py-1">
                    <span class="text-gray-400">Ing. manuales</span>
                    <span class="font-mono font-medium text-blue-600 dark:text-blue-400">+${{ number_format($snap['totales']['ingresos_manuales'], 2) }}</span>
                </div>
                @endif

                @else
                <div class="text-center py-6">
                    <p class="text-2xl mb-2">🔒</p>
                    <p class="text-xs text-gray-400">Sesión cerrada</p>
                </div>
                @endif
            </div>

            {{-- Footer acciones --}}
            @if($caja)
            <div class="px-5 py-3 border-t dark:border-gray-700 flex flex-wrap gap-2">
                <a href="{{ route('admin.cajas.show', $suc->id_usuario) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Ver detalle
                </a>
                @if($abierta)
                <button type="button"
                    @click="abrirIngreso('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    + Ingreso
                </button>
                <button type="button"
                    @click="abrirRetiro('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    − Retiro
                </button>
                <button type="button"
                    @click="abrirCierre('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:opacity-80 transition">
                    Forzar cierre
                </button>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════
         MODAL: CREAR CAJA
    ══════════════════════════════════════════════ --}}
    <div x-show="modal === 'crear'" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Asignar caja a sucursal</h3>
            <p class="text-xs text-gray-400 mb-5" x-text="modalDesc"></p>
            <form method="POST" action="{{ route('admin.cajas.store') }}">
                @csrf
                <input type="hidden" name="id_usuario" x-model="targetId">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Nombre de la caja <span class="normal-case font-normal">(opcional)</span>
                    </label>
                    <input type="text" name="nombre" maxlength="80" placeholder="Caja principal"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    <p class="text-xs text-gray-400 mt-1">Si lo dejas vacío se usará "Caja principal".</p>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modal = ''"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                        Crear caja
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL: INGRESO
         Incluye metodo + es_efectivo que requiere CajaService::registrarIngreso()
    ══════════════════════════════════════════════ --}}
    <div x-show="modal === 'ingreso'" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-data="{ metodo: 'efectivo' }">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar ingreso</h3>
            <p class="text-xs text-gray-400 mb-5" x-text="modalDesc"></p>
            <form method="POST" :action="`/admin/cajas/${targetId}/ingreso`">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto</label>
                        <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                               placeholder="0.00" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                               x-init="$watch('modal', v => v === 'ingreso' && $nextTick(() => $el.focus()))">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Método de pago</label>
                        <select name="metodo" x-model="metodo"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="otro">Otro</option>
                        </select>
                        {{-- es_efectivo calculado del método seleccionado --}}
                        <input type="hidden" name="es_efectivo" :value="metodo === 'efectivo' ? '1' : '0'">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto</label>
                        <input type="text" name="concepto" maxlength="200" required
                               placeholder="Ej. Transferencia de fondo de reserva..."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Referencia <span class="normal-case font-normal">(opcional)</span>
                        </label>
                        <input type="text" name="referencia" maxlength="120" placeholder="Folio, número..."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modal = ''"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-gray-900 dark:bg-white dark:text-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition">
                        Registrar ingreso
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL: RETIRO
    ══════════════════════════════════════════════ --}}
    <div x-show="modal === 'retiro'" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Registrar retiro</h3>
            <p class="text-xs text-gray-400 mb-5" x-text="modalDesc"></p>
            <form method="POST" :action="`/admin/cajas/${targetId}/retiro`">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto a retirar</label>
                        <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                               placeholder="0.00" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"
                               x-init="$watch('modal', v => v === 'retiro' && $nextTick(() => $el.focus()))">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto</label>
                        <input type="text" name="concepto" maxlength="200" required
                               placeholder="Ej. Depósito a banco, gastos operativos..."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                            Referencia <span class="normal-case font-normal">(opcional)</span>
                        </label>
                        <input type="text" name="referencia" maxlength="120"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modal = ''"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:opacity-80 transition">
                        Registrar retiro
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         MODAL: CIERRE FORZADO
    ══════════════════════════════════════════════ --}}
    <div x-show="modal === 'cierre'" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-[1px] flex items-center justify-center z-50 px-4"
         @click.self="modal = ''">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">⚠ Forzar cierre de sesión</h3>
            <p class="text-xs text-gray-400 mb-5"
               x-text="'Se cerrará la sesión activa de ' + targetName + ' y se generará un corte de cierre con motivo «admin».'"></p>
            <form method="POST" :action="`/admin/cajas/${targetId}/cerrar-forzado`">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">
                        Notas <span class="normal-case font-normal">(opcional)</span>
                    </label>
                    <textarea name="notas" rows="2" maxlength="500"
                              placeholder="Razón del cierre forzado..."
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white/30"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="modal = ''"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition">
                        Forzar cierre
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function adminCajas() {
    return {
        modal: '',
        targetId: '',
        targetName: '',
        modalDesc: '',

        abrirModalCrear(id, nombre) {
            this.targetId   = id;
            this.targetName = nombre;
            this.modalDesc  = `Se creará una caja para la sucursal de ${nombre}.`;
            this.modal      = 'crear';
        },
        abrirIngreso(id, nombre) {
            this.targetId   = id;
            this.targetName = nombre;
            this.modalDesc  = `Registrar una entrada de dinero en la caja de ${nombre}.`;
            this.modal      = 'ingreso';
        },
        abrirRetiro(id, nombre) {
            this.targetId   = id;
            this.targetName = nombre;
            this.modalDesc  = `Retirar efectivo de la caja activa de ${nombre}.`;
            this.modal      = 'retiro';
        },
        abrirCierre(id, nombre) {
            this.targetId   = id;
            this.targetName = nombre;
            this.modal      = 'cierre';
        },
    }
}
</script>

</x-app-layout>