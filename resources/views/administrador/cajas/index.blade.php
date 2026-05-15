{{-- resources/views/administrador/cajas/index.blade.php --}}
<x-app-layout>



<style>
:root{
    --c-bg:#0d1117;--c-surface:#161b22;--c-border:#21262d;--c-border2:#30363d;
    --c-text:#e6edf3;--c-muted:#8b949e;
    --c-green:#3fb950;--c-green-d:rgba(63,185,80,.12);
    --c-red:#f85149;--c-red-d:rgba(248,81,73,.12);
    --c-blue:#58a6ff;--c-blue-d:rgba(88,166,255,.1);
    --c-yellow:#d29922;--c-yellow-d:rgba(210,153,34,.12);
    --c-accent:#f0b429;
    --mono:'JetBrains Mono','Fira Code','Courier New',monospace;
}
*{box-sizing:border-box;}
body{background:var(--c-bg);color:var(--c-text);font-family:'Inter',system-ui,sans-serif;}

.page{max-width:1060px;margin:0 auto;padding:1.75rem 1.25rem 4rem;}

/* Header */
.page-hd{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;}
.page-hd h1{font-size:1.35rem;font-weight:700;letter-spacing:-.02em;margin:0 0 .2rem;}
.page-hd p{font-size:.78rem;color:var(--c-muted);margin:0;}

/* Alerts */
.alert{display:flex;align-items:flex-start;gap:.6rem;padding:.85rem 1rem;border-radius:8px;font-size:.82rem;margin-bottom:1.25rem;line-height:1.45;}
.alert-ok{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.25);}
.alert-err{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.25);}

/* Summary bar */
.summary-bar{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.65rem;margin-bottom:1.5rem;}
.sum-card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:8px;padding:.9rem 1.1rem;}
.sum-lbl{font-size:.62rem;color:var(--c-muted);text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:.3rem;}
.sum-val{font-size:1.4rem;font-weight:700;font-family:var(--mono);}
.green{color:var(--c-green);}.yellow{color:var(--c-accent);}.red{color:var(--c-red);}.blue{color:var(--c-blue);}

/* Grid de sucursales */
.suc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;}

/* Tarjeta de sucursal */
.suc-card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:10px;overflow:hidden;display:flex;flex-direction:column;}
.suc-head{padding:1rem 1.1rem .8rem;display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;border-bottom:1px solid var(--c-border);}
.suc-name{font-size:.95rem;font-weight:700;}
.suc-sub{font-size:.72rem;color:var(--c-muted);margin-top:.15rem;font-family:var(--mono);}
.suc-body{padding:.9rem 1.1rem;flex:1;}
.suc-footer{padding:.75rem 1.1rem;border-top:1px solid var(--c-border);display:flex;gap:.5rem;flex-wrap:wrap;}

/* Pill */
.pill{display:inline-flex;align-items:center;gap:.35rem;padding:.22rem .65rem;border-radius:99px;font-size:.66rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap;}
.pill-open{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.3);}
.pill-closed{background:rgba(139,148,158,.1);color:var(--c-muted);border:1px solid var(--c-border2);}
.pill-no-caja{background:var(--c-yellow-d);color:var(--c-yellow);border:1px solid rgba(210,153,34,.3);}
.pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;animation:blink 2s ease infinite;}
@keyframes blink{0%,100%{opacity:1}60%{opacity:.25}}

/* Mini stats dentro de tarjeta */
.mini-stats{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:.75rem;}
.mini-stat{background:#0d1117;border-radius:6px;padding:.6rem .75rem;}
.mini-lbl{font-size:.6rem;color:var(--c-muted);text-transform:uppercase;letter-spacing:.07em;font-weight:600;margin-bottom:.2rem;}
.mini-val{font-size:1.1rem;font-weight:700;font-family:var(--mono);}

/* Info row */
.info-row{display:flex;justify-content:space-between;font-size:.77rem;padding:.25rem 0;}
.info-row .lbl{color:var(--c-muted);}
.info-row .val{font-family:var(--mono);font-size:.74rem;font-weight:600;}

/* No caja state */
.no-caja-state{text-align:center;padding:1.25rem .5rem;}
.no-caja-icon{font-size:1.75rem;margin-bottom:.5rem;}
.no-caja-txt{font-size:.78rem;color:var(--c-muted);margin-bottom:.85rem;line-height:1.4;}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.48rem .9rem;border-radius:6px;font-weight:600;font-size:.76rem;cursor:pointer;border:none;transition:.15s;letter-spacing:.01em;text-decoration:none;font-family:inherit;}
.btn-primary{background:var(--c-accent);color:#0d1117;}
.btn-primary:hover{background:#d4961e;}
.btn-ghost{background:transparent;color:var(--c-muted);border:1px solid var(--c-border2);}
.btn-ghost:hover{color:var(--c-text);border-color:var(--c-muted);}
.btn-danger{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.3);}
.btn-danger:hover{background:rgba(248,81,73,.2);}
.btn-yellow{background:var(--c-yellow-d);color:var(--c-yellow);border:1px solid rgba(210,153,34,.3);}
.btn-yellow:hover{background:rgba(210,153,34,.2);}
.btn-sm{padding:.35rem .7rem;font-size:.7rem;}

/* Empty global */
.empty-page{text-align:center;padding:4rem 2rem;}
.empty-page .ei{font-size:3rem;margin-bottom:1rem;}
.empty-page .et{font-size:1rem;font-weight:700;margin-bottom:.5rem;}
.empty-page .ed{font-size:.82rem;color:var(--c-muted);margin-bottom:2rem;line-height:1.5;}

/* Modals */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.72);backdrop-filter:blur(3px);z-index:900;display:flex;align-items:center;justify-content:center;padding:1rem;}
.modal{background:var(--c-surface);border:1px solid var(--c-border2);border-radius:12px;padding:1.75rem;width:100%;max-width:430px;box-shadow:0 20px 50px rgba(0,0,0,.6);}
.modal-title{font-size:1.05rem;font-weight:700;margin-bottom:.3rem;}
.modal-desc{font-size:.8rem;color:var(--c-muted);margin-bottom:1.4rem;line-height:1.5;}
.modal-footer{display:flex;justify-content:flex-end;gap:.6rem;margin-top:1.4rem;}

/* Fields */
.field{margin-bottom:.9rem;}
.field label{display:block;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--c-muted);margin-bottom:.38rem;}
.field input,.field textarea,.field select{width:100%;background:#0d1117;border:1px solid var(--c-border2);border-radius:6px;padding:.6rem .8rem;color:var(--c-text);font-size:.88rem;font-family:inherit;outline:none;transition:border-color .15s;}
.field input:focus,.field textarea:focus,.field select:focus{border-color:var(--c-blue);}
.field .mono-input{font-family:var(--mono);font-size:1rem;}
.field-hint{font-size:.68rem;color:var(--c-muted);margin-top:.3rem;}

/* Toggle group */
.toggle-group{display:flex;gap:.5rem;}
.toggle-opt{flex:1;padding:.55rem;border:1px solid var(--c-border2);border-radius:6px;text-align:center;cursor:pointer;font-size:.78rem;font-weight:600;transition:.15s;background:transparent;color:var(--c-muted);}
.toggle-opt.selected-in{border-color:rgba(63,185,80,.5);background:var(--c-green-d);color:var(--c-green);}
.toggle-opt.selected-out{border-color:rgba(248,81,73,.4);background:var(--c-red-d);color:var(--c-red);}

@media(max-width:640px){
    .suc-grid{grid-template-columns:1fr;}
    .summary-bar{grid-template-columns:1fr 1fr;}
}
</style>



<div class="page" x-data="adminCajas()" x-init="init()">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-ok">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 11 4 11"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-err">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="page-hd">
        <div>
            <h1>🏦 Gestión de Cajas</h1>
            <p>Vista consolidada — todas las sucursales de tu negocio</p>
        </div>
        <button type="button" class="btn btn-primary" @click="modal='crear'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Asignar caja
        </button>
    </div>

    {{-- Resumen global --}}
    @php
        $totalSistema   = $snapshots->sum(fn($s) => $s['totales']['total_sistema'] ?? 0);
        $totalVentas    = $snapshots->sum(fn($s) => $s['totales']['ingresos_ventas'] ?? 0);
        $cajasAbiertas  = $snapshots->count();
        $sucSinCaja     = $sucursales->filter(fn($s) => !isset($cajas[$s->id_usuario]))->count();
    @endphp
    <div class="summary-bar">
        <div class="sum-card">
            <div class="sum-lbl">Total en cajas abiertas</div>
            <div class="sum-val green">${{ number_format($totalSistema, 2) }}</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Ventas del día (cajas)</div>
            <div class="sum-val yellow">${{ number_format($totalVentas, 2) }}</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Sesiones abiertas</div>
            <div class="sum-val blue">{{ $cajasAbiertas }}</div>
        </div>
        <div class="sum-card">
            <div class="sum-lbl">Sucursales sin caja</div>
            <div class="sum-val {{ $sucSinCaja > 0 ? 'yellow' : '' }}">{{ $sucSinCaja }}</div>
        </div>
    </div>

    {{-- Grid de sucursales --}}
    @if($sucursales->isEmpty())
    <div class="empty-page">
        <div class="ei">🏪</div>
        <div class="et">No hay sucursales registradas</div>
        <div class="ed">Crea vendedores desde <strong>Admin → Vendedores</strong> para ver sus cajas aquí.</div>
    </div>
    @else
    <div class="suc-grid">
        @foreach($sucursales as $suc)
        @php
            $caja     = $cajas[$suc->id_usuario] ?? null;
            $snap     = $snapshots[$suc->id_usuario] ?? null;
            $sesion   = $caja?->sesionActiva;
            $abierta  = $sesion !== null;
        @endphp

        <div class="suc-card">
            {{-- Encabezado --}}
            <div class="suc-head">
                <div>
                    <div class="suc-name">{{ $suc->nombre_usuario }}</div>
                    @if($caja)
                    <div class="suc-sub">{{ $caja->nombre }}</div>
                    @endif
                </div>
                @if(!$caja)
                    <span class="pill pill-no-caja">Sin caja</span>
                @elseif($abierta)
                    <span class="pill pill-open"><span class="pill-dot"></span>Abierta</span>
                @else
                    <span class="pill pill-closed">Cerrada</span>
                @endif
            </div>

            {{-- Cuerpo --}}
            <div class="suc-body">
                @if(!$caja)
                <div class="no-caja-state">
                    <div class="no-caja-icon">📭</div>
                    <div class="no-caja-txt">Esta sucursal no tiene caja asignada.</div>
                    <button type="button" class="btn btn-yellow btn-sm"
                            @click="abrirModalCrear('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')">
                        + Asignar caja
                    </button>
                </div>

                @elseif($abierta && $snap)
                <div class="mini-stats">
                    <div class="mini-stat">
                        <div class="mini-lbl">Total sistema</div>
                        <div class="mini-val green">${{ number_format($snap['totales']['total_sistema'], 2) }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-lbl">Ventas</div>
                        <div class="mini-val yellow">${{ number_format($snap['totales']['ingresos_ventas'], 2) }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-lbl"># Ventas</div>
                        <div class="mini-val">{{ $snap['ventas_count'] }}</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-lbl">Fondo inicial</div>
                        <div class="mini-val">${{ number_format($snap['sesion']['fondo_inicial'], 2) }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <span class="lbl">Abierta desde</span>
                    <span class="val">{{ \Carbon\Carbon::parse($snap['sesion']['abierta_at'])->format('d/m H:i') }}</span>
                </div>
                @if($snap['totales']['retiros'] > 0)
                <div class="info-row">
                    <span class="lbl">Retiros</span>
                    <span class="val red">-${{ number_format($snap['totales']['retiros'], 2) }}</span>
                </div>
                @endif

                @else
                <div style="text-align:center;padding:1rem 0;font-size:.82rem;color:var(--c-muted);">
                    🔒 Sesión cerrada
                </div>
                @endif
            </div>

            {{-- Footer con acciones --}}
            @if($caja)
            <div class="suc-footer">
                <a href="{{ route('admin.cajas.show', $suc->id_usuario) }}" class="btn btn-ghost btn-sm">
                    Ver detalle
                </a>
                @if($abierta)
                <button type="button" class="btn btn-ghost btn-sm"
                        @click="abrirIngreso('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')">
                    + Ingreso
                </button>
                <button type="button" class="btn btn-ghost btn-sm"
                        @click="abrirRetiro('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')">
                    − Retiro
                </button>
                <button type="button" class="btn btn-danger btn-sm"
                        @click="abrirCierre('{{ $suc->id_usuario }}', '{{ addslashes($suc->nombre_usuario) }}')">
                    Forzar cierre
                </button>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══════════════ MODAL: CREAR CAJA ═══════════════ --}}
    <div class="overlay" x-show="modal==='crear'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Asignar caja a sucursal</div>
            <div class="modal-desc" x-text="modalDesc"></div>
            <form method="POST" action="{{ route('admin.cajas.store') }}">
                @csrf
                <input type="hidden" name="id_usuario" x-model="targetId">
                <div class="field">
                    <label>Nombre de la caja <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="nombre" maxlength="80" placeholder="Caja principal">
                    <div class="field-hint">Si lo dejas vacío se usará "Caja principal".</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear caja</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ MODAL: INGRESO ═══════════════ --}}
    <div class="overlay" x-show="modal==='ingreso'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Registrar ingreso</div>
            <div class="modal-desc" x-text="modalDesc"></div>
            <form method="POST" :action="`/admin/cajas/${targetId}/ingreso`">
                @csrf
                <div class="field">
                    <label>Monto</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required>
                </div>
                <div class="field">
                    <label>Concepto</label>
                    <input type="text" name="concepto" maxlength="200" required
                           placeholder="Ej. Transferencia de fondo de reserva...">
                </div>
                <div class="field">
                    <label>Referencia <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="referencia" maxlength="120">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar ingreso</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ MODAL: RETIRO ═══════════════ --}}
    <div class="overlay" x-show="modal==='retiro'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Registrar retiro</div>
            <div class="modal-desc" x-text="modalDesc"></div>
            <form method="POST" :action="`/admin/cajas/${targetId}/retiro`">
                @csrf
                <div class="field">
                    <label>Monto a retirar</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required>
                </div>
                <div class="field">
                    <label>Concepto</label>
                    <input type="text" name="concepto" maxlength="200" required
                           placeholder="Ej. Depósito a banco, gastos operativos...">
                </div>
                <div class="field">
                    <label>Referencia <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="referencia" maxlength="120">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Registrar retiro</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════ MODAL: CIERRE FORZADO ═══════════════ --}}
    <div class="overlay" x-show="modal==='cierre'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title" style="color:var(--c-red)">⚠ Forzar cierre de sesión</div>
            <div class="modal-desc" x-text="'Se cerrará la sesión activa de ' + targetName + ' y se generará un corte de cierre con motivo «admin».'" ></div>
            <form method="POST" :action="`/admin/cajas/${targetId}/cerrar-forzado`">
                @csrf
                <div class="field">
                    <label>Notas <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <textarea name="notas" rows="2" maxlength="500"
                              placeholder="Razón del cierre forzado..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Forzar cierre</button>
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

        init() {},

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