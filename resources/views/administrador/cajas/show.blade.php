{{-- resources/views/administrador/cajas/show.blade.php --}}
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

.page{max-width:960px;margin:0 auto;padding:1.75rem 1.25rem 4rem;}

/* Breadcrumb */
.breadcrumb{display:flex;align-items:center;gap:.5rem;font-size:.75rem;color:var(--c-muted);margin-bottom:1.25rem;}
.breadcrumb a{color:var(--c-muted);text-decoration:none;}
.breadcrumb a:hover{color:var(--c-text);}
.breadcrumb-sep{color:var(--c-border2);}

/* Header */
.page-hd{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;}
.page-hd h1{font-size:1.3rem;font-weight:700;letter-spacing:-.02em;margin:0 0 .2rem;}
.page-hd p{font-size:.78rem;color:var(--c-muted);margin:0;font-family:var(--mono);}

/* Alerts */
.alert{display:flex;align-items:flex-start;gap:.6rem;padding:.85rem 1rem;border-radius:8px;font-size:.82rem;margin-bottom:1.25rem;line-height:1.45;}
.alert-ok{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.25);}
.alert-err{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.25);}
.alert svg{flex-shrink:0;margin-top:.1rem;}

/* Pills */
.pill{display:inline-flex;align-items:center;gap:.35rem;padding:.24rem .7rem;border-radius:99px;font-size:.68rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;}
.pill-open{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.3);}
.pill-closed{background:rgba(139,148,158,.1);color:var(--c-muted);border:1px solid var(--c-border2);}
.pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;animation:blink 2s ease infinite;}
@keyframes blink{0%,100%{opacity:1}60%{opacity:.25}}

/* Cards */
.card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:10px;padding:1.35rem;}
.mt1{margin-top:1rem;}.mt15{margin-top:1.5rem;}
.card-lbl{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--c-muted);margin-bottom:.85rem;}

/* Stats */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:.65rem;margin-bottom:1.1rem;}
.stat{background:#0d1117;border:1px solid var(--c-border);border-radius:8px;padding:.9rem 1rem;}
.stat-lbl{font-size:.62rem;color:var(--c-muted);text-transform:uppercase;letter-spacing:.07em;font-weight:600;margin-bottom:.3rem;}
.stat-val{font-size:1.35rem;font-weight:700;font-family:var(--mono);line-height:1;}
.green{color:var(--c-green);}.yellow{color:var(--c-accent);}.red{color:var(--c-red);}.blue{color:var(--c-blue);}

/* Métodos */
.metodo-row{display:flex;align-items:center;justify-content:space-between;padding:.55rem 0;border-bottom:1px dashed var(--c-border);font-size:.82rem;}
.metodo-row:last-child{border-bottom:none;}
.metodo-name{display:flex;align-items:center;gap:.5rem;}
.metodo-icon{width:26px;height:26px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.78rem;background:var(--c-blue-d);}
.metodo-val{font-family:var(--mono);font-weight:600;color:var(--c-green);}

/* Info row */
.info-row{display:flex;justify-content:space-between;align-items:center;font-size:.79rem;padding:.28rem 0;}
.info-row .lbl{color:var(--c-muted);}
.info-row .val{font-family:var(--mono);font-weight:600;font-size:.77rem;}
hr.sep{border:none;border-top:1px solid var(--c-border);margin:.85rem 0;}

/* Actions bar */
.actions{display:flex;flex-wrap:wrap;gap:.6rem;}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:7px;font-weight:600;font-size:.78rem;cursor:pointer;border:none;transition:.15s;letter-spacing:.01em;text-decoration:none;font-family:inherit;}
.btn-primary{background:var(--c-accent);color:#0d1117;}
.btn-primary:hover{background:#d4961e;}
.btn-ghost{background:transparent;color:var(--c-muted);border:1px solid var(--c-border2);}
.btn-ghost:hover{color:var(--c-text);border-color:var(--c-muted);}
.btn-danger{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.3);}
.btn-danger:hover{background:rgba(248,81,73,.2);}
.btn-blue{background:var(--c-blue-d);color:var(--c-blue);border:1px solid rgba(88,166,255,.3);}
.btn-blue:hover{background:rgba(88,166,255,.18);}
.btn-yellow{background:var(--c-yellow-d);color:var(--c-yellow);border:1px solid rgba(210,153,34,.3);}
.btn-yellow:hover{background:rgba(210,153,34,.2);}
.btn-sm{padding:.35rem .72rem;font-size:.71rem;}

/* No-sesión state */
.no-sesion{text-align:center;padding:2rem 1rem;}
.no-sesion-icon{font-size:2.2rem;margin-bottom:.75rem;}
.no-sesion-txt{font-size:.82rem;color:var(--c-muted);line-height:1.5;}

/* No-caja state */
.no-caja-box{text-align:center;padding:2.5rem 1.5rem;}
.no-caja-icon{font-size:2.5rem;margin-bottom:.85rem;}
.no-caja-title{font-size:1rem;font-weight:700;margin-bottom:.4rem;}
.no-caja-desc{font-size:.82rem;color:var(--c-muted);line-height:1.5;margin-bottom:1.5rem;}

/* Historial table */
.tbl-wrap{overflow-x:auto;}
table.hist{width:100%;border-collapse:collapse;font-size:.79rem;}
table.hist th{text-align:left;font-size:.61rem;text-transform:uppercase;letter-spacing:.07em;color:var(--c-muted);padding:.5rem .65rem;border-bottom:1px solid var(--c-border);font-weight:700;white-space:nowrap;}
table.hist td{padding:.62rem .65rem;border-bottom:1px solid #0d1117;vertical-align:middle;}
table.hist tr:last-child td{border-bottom:none;}
table.hist tr:hover td{background:rgba(255,255,255,.02);}
.mono{font-family:var(--mono);}

/* Chips */
.chip{display:inline-block;padding:.16rem .48rem;border-radius:4px;font-size:.62rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;}
.chip-open{background:var(--c-green-d);color:var(--c-green);}
.chip-closed{background:var(--c-red-d);color:var(--c-red);}
.chip-auto{background:rgba(139,148,158,.12);color:var(--c-muted);}
.chip-vendedor{background:var(--c-blue-d);color:var(--c-blue);}
.chip-admin{background:var(--c-yellow-d);color:var(--c-yellow);}

/* Pagination */
.pagination{display:flex;gap:.4rem;justify-content:center;margin-top:1.25rem;flex-wrap:wrap;}
.pagination a,.pagination span{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 .5rem;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;border:1px solid var(--c-border);color:var(--c-muted);transition:.15s;}
.pagination a:hover{border-color:var(--c-border2);color:var(--c-text);}
.pagination .active span{background:var(--c-accent);color:#0d1117;border-color:var(--c-accent);}
.pagination .disabled span{opacity:.35;cursor:default;}

/* Modals */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,.72);backdrop-filter:blur(3px);z-index:900;display:flex;align-items:center;justify-content:center;padding:1rem;}
.modal{background:var(--c-surface);border:1px solid var(--c-border2);border-radius:12px;padding:1.75rem;width:100%;max-width:430px;box-shadow:0 20px 50px rgba(0,0,0,.6);}
.modal-title{font-size:1.05rem;font-weight:700;margin-bottom:.3rem;}
.modal-desc{font-size:.8rem;color:var(--c-muted);margin-bottom:1.4rem;line-height:1.5;}
.modal-footer{display:flex;justify-content:flex-end;gap:.6rem;margin-top:1.4rem;}

/* Fields */
.field{margin-bottom:.9rem;}
.field label{display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--c-muted);margin-bottom:.38rem;}
.field input,.field textarea,.field select{width:100%;background:#0d1117;border:1px solid var(--c-border2);border-radius:6px;padding:.58rem .78rem;color:var(--c-text);font-size:.88rem;font-family:inherit;outline:none;transition:border-color .15s;}
.field input:focus,.field textarea:focus,.field select:focus{border-color:var(--c-blue);}
.field .mono-input{font-family:var(--mono);font-size:1rem;}
.field-hint{font-size:.67rem;color:var(--c-muted);margin-top:.3rem;}

/* Toggle dirs (ajuste) */
.dir-toggle{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:.9rem;}
.dir-opt{padding:.6rem;border:1px solid var(--c-border2);border-radius:6px;text-align:center;cursor:pointer;font-size:.78rem;font-weight:600;transition:.15s;user-select:none;}
.dir-opt:hover{border-color:var(--c-border2);opacity:.85;}
.dir-entrada{border-color:rgba(63,185,80,.4)!important;background:var(--c-green-d)!important;color:var(--c-green)!important;}
.dir-salida{border-color:rgba(248,81,73,.4)!important;background:var(--c-red-d)!important;color:var(--c-red)!important;}

@media(max-width:580px){
    .stat-grid{grid-template-columns:1fr 1fr;}
    .stat-val{font-size:1.1rem;}
    table.hist{font-size:.72rem;}
    table.hist th,table.hist td{padding:.45rem .38rem;}
}
</style>

<div class="page" x-data="showCaja()" x-init="init()">

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.cajas.index') }}">← Cajas</a>
        <span class="breadcrumb-sep">/</span>
        <span>{{ $sucursal->nombre_usuario }}</span>
    </div>

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
            <h1>🏧 {{ $sucursal->nombre_usuario }}</h1>
            <p>
                {{ $caja->nombre ?? 'Sin caja' }}
                @if($caja) &nbsp;·&nbsp; {{ $caja->id_caja }} @endif
            </p>
        </div>
        @if($sesion)
            <span class="pill pill-open"><span class="pill-dot"></span>Sesión abierta</span>
        @elseif($caja)
            <span class="pill pill-closed">Sesión cerrada</span>
        @endif
    </div>

    {{-- ════════ SIN CAJA ════════ --}}
    @if(!$caja)
    <div class="card">
        <div class="no-caja-box">
            <div class="no-caja-icon">📭</div>
            <div class="no-caja-title">Esta sucursal no tiene caja asignada</div>
            <div class="no-caja-desc">Crea una caja para que el vendedor pueda abrir sesiones y registrar movimientos.</div>
            <button type="button" class="btn btn-primary" @click="modal='crear'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Crear caja para esta sucursal
            </button>
        </div>
    </div>

    @else

    {{-- ════════ CON CAJA — SESIÓN ACTIVA ════════ --}}
    @if($sesion && $snapshot)
    <div class="card">
        <div class="card-lbl">Sesión activa — resumen</div>
        <div class="stat-grid">
            <div class="stat">
                <div class="stat-lbl">Total sistema</div>
                <div class="stat-val green">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Fondo inicial</div>
                <div class="stat-val">${{ number_format($snapshot['sesion']['fondo_inicial'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Ventas</div>
                <div class="stat-val yellow">${{ number_format($snapshot['totales']['ingresos_ventas'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl"># Ventas</div>
                <div class="stat-val">{{ $snapshot['ventas_count'] }}</div>
            </div>
            @if($snapshot['totales']['ingresos_manuales'] > 0)
            <div class="stat">
                <div class="stat-lbl">Ing. manuales</div>
                <div class="stat-val blue">${{ number_format($snapshot['totales']['ingresos_manuales'], 2) }}</div>
            </div>
            @endif
            @if($snapshot['totales']['retiros'] > 0)
            <div class="stat">
                <div class="stat-lbl">Retiros</div>
                <div class="stat-val red">${{ number_format($snapshot['totales']['retiros'], 2) }}</div>
            </div>
            @endif
            @if($snapshot['totales']['ajustes_neto'] != 0)
            <div class="stat">
                <div class="stat-lbl">Ajustes neto</div>
                <div class="stat-val {{ $snapshot['totales']['ajustes_neto'] >= 0 ? 'green' : 'red' }}">
                    {{ $snapshot['totales']['ajustes_neto'] >= 0 ? '+' : '' }}${{ number_format($snapshot['totales']['ajustes_neto'], 2) }}
                </div>
            </div>
            @endif
        </div>

        @if(!empty($snapshot['por_metodo']))
        <div class="card-lbl">Por método de pago</div>
        @foreach($snapshot['por_metodo'] as $m)
        <div class="metodo-row">
            <div class="metodo-name">
                <div class="metodo-icon">{{ $m['es_efectivo'] ? '💵' : '💳' }}</div>
                <span>{{ $m['nombre'] }}</span>
            </div>
            <span class="metodo-val">${{ number_format($m['total'], 2) }}</span>
        </div>
        @endforeach
        @endif

        <hr class="sep">
        <div class="info-row">
            <span class="lbl">Abierta por</span>
            <span class="val">{{ $snapshot['usuario']['nombre_usuario'] }}</span>
        </div>
        <div class="info-row">
            <span class="lbl">Desde</span>
            <span class="val">{{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="lbl">ID sesión</span>
            <span class="val" style="font-size:.66rem">{{ $sesion->id_sesion }}</span>
        </div>
    </div>

    {{-- Operaciones admin sobre la sesión activa --}}
    <div class="card mt1">
        <div class="card-lbl">Operaciones de administrador</div>
        <div class="actions">
            <button type="button" class="btn btn-blue" @click="modal='ingreso'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Registrar ingreso
            </button>
            <button type="button" class="btn btn-ghost" @click="modal='retiro'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Registrar retiro
            </button>
            <button type="button" class="btn btn-yellow" @click="modal='ajuste'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
                Ajuste contable
            </button>
            <button type="button" class="btn btn-danger" @click="modal='cierre'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Forzar cierre
            </button>
        </div>
    </div>

    @else
    {{-- Sesión cerrada --}}
    <div class="card">
        <div class="no-sesion">
            <div class="no-sesion-icon">🔒</div>
            <div class="no-sesion-txt">
                No hay sesión activa en esta caja.<br>
                El vendedor debe abrir la sesión desde su panel.
            </div>
        </div>
    </div>
    @endif

    {{-- ════════ HISTORIAL DE SESIONES ════════ --}}
    <div class="card mt15">
        <div class="card-lbl">Historial de sesiones</div>

        @if($historial instanceof \Illuminate\Pagination\LengthAwarePaginator ? $historial->isEmpty() : $historial->isEmpty())
        <div style="text-align:center;padding:1.5rem;font-size:.82rem;color:var(--c-muted);">
            No hay sesiones registradas aún.
        </div>
        @else
        <div class="tbl-wrap">
            <table class="hist">
                <thead>
                    <tr>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th>Duración</th>
                        <th>Fondo</th>
                        <th>Sistema</th>
                        <th>Declarado</th>
                        <th>Diferencia</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($historial as $ses)
                @php $corteRec = $ses->cortes()->orderByDesc('created_at')->first(); @endphp
                <tr>
                    <td style="white-space:nowrap">{{ $ses->abierta_at?->format('d/m/Y H:i') }}</td>
                    <td style="white-space:nowrap">{{ $ses->cerrada_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>{{ $ses->duracion }}</td>
                    <td class="mono">${{ number_format($ses->fondo_inicial, 2) }}</td>
                    <td class="mono">${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}</td>
                    <td class="mono">
                        @if($ses->monto_cierre_declarado !== null)
                            ${{ number_format($ses->monto_cierre_declarado, 2) }}
                        @else —
                        @endif
                    </td>
                    <td class="mono" style="color:{{ ($ses->diferencia ?? 0) < 0 ? 'var(--c-red)' : 'var(--c-green)' }}">
                        @if($ses->diferencia !== null)
                            {{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($ses->motivo_cierre === 'admin')
                            <span class="chip chip-admin">Admin</span>
                        @elseif($ses->motivo_cierre === 'vendedor')
                            <span class="chip chip-vendedor">Vendedor</span>
                        @elseif($ses->motivo_cierre === 'sistema')
                            <span class="chip chip-auto">Sistema</span>
                        @else
                            <span style="color:var(--c-muted);font-size:.75rem">—</span>
                        @endif
                    </td>
                    <td>
                        @if($ses->estado === 'abierta')         <span class="chip chip-open">Abierta</span>
                        @elseif($ses->estado === 'auto_cerrada') <span class="chip chip-auto">Auto</span>
                        @else                                    <span class="chip chip-closed">Cerrada</span>
                        @endif
                    </td>
                    <td>
                        @if($corteRec)
                        <a href="{{ route('admin.cajas.corte.pdf', $corteRec->id_corte) }}" target="_blank" class="btn btn-ghost btn-sm">PDF</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($historial instanceof \Illuminate\Pagination\LengthAwarePaginator && $historial->hasPages())
        <div class="pagination">
            {{-- Anterior --}}
            @if($historial->onFirstPage())
                <span class="disabled"><span>‹</span></span>
            @else
                <a href="{{ $historial->previousPageUrl() }}">‹</a>
            @endif

            @foreach($historial->getUrlRange(1, $historial->lastPage()) as $page => $url)
                @if($page == $historial->currentPage())
                    <span class="active"><span>{{ $page }}</span></span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if($historial->hasMorePages())
                <a href="{{ $historial->nextPageUrl() }}">›</a>
            @else
                <span class="disabled"><span>›</span></span>
            @endif
        </div>
        @endif
        @endif
    </div>

    @endif {{-- fin @if(!$caja) --}}

    {{-- ═══════════ MODAL: CREAR CAJA ═══════════ --}}
    <div class="overlay" x-show="modal==='crear'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Crear caja — {{ $sucursal->nombre_usuario }}</div>
            <div class="modal-desc">Se asignará una caja a esta sucursal. Solo se puede tener una caja por sucursal.</div>
            <form method="POST" action="{{ route('admin.cajas.store') }}">
                @csrf
                <input type="hidden" name="id_usuario" value="{{ $sucursal->id_usuario }}">
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

    {{-- ═══════════ MODAL: INGRESO ═══════════ --}}
    <div class="overlay" x-show="modal==='ingreso'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Registrar ingreso</div>
            <div class="modal-desc">Entrada de dinero en la sesión activa de <strong>{{ $sucursal->nombre_usuario }}</strong>.</div>
            <form method="POST" action="{{ route('admin.cajas.ingreso', $sucursal->id_usuario) }}">
                @csrf
                <div class="field">
                    <label>Monto</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required
                           x-init="$watch('modal', v => v==='ingreso' && $nextTick(() => $el.focus()))">
                </div>
                <div class="field">
                    <label>Concepto</label>
                    <input type="text" name="concepto" maxlength="200" required
                           placeholder="Ej. Transferencia de fondo de reserva...">
                </div>
                <div class="field">
                    <label>Referencia <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="referencia" maxlength="120" placeholder="Folio, número...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar ingreso</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════ MODAL: RETIRO ═══════════ --}}
    <div class="overlay" x-show="modal==='retiro'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title">Registrar retiro</div>
            <div class="modal-desc">Salida de efectivo de la sesión activa de <strong>{{ $sucursal->nombre_usuario }}</strong>.</div>
            <form method="POST" action="{{ route('admin.cajas.retiro', $sucursal->id_usuario) }}">
                @csrf
                <div class="field">
                    <label>Monto a retirar</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required
                           x-init="$watch('modal', v => v==='retiro' && $nextTick(() => $el.focus()))">
                </div>
                <div class="field">
                    <label>Concepto</label>
                    <input type="text" name="concepto" maxlength="200" required
                           placeholder="Ej. Depósito a banco, gastos operativos...">
                </div>
                <div class="field">
                    <label>Referencia <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="referencia" maxlength="120" placeholder="Folio, número...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Registrar retiro</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════ MODAL: AJUSTE ═══════════ --}}
    <div class="overlay" x-show="modal==='ajuste'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop x-data="{ dir: 1 }">
            <div class="modal-title">Ajuste contable</div>
            <div class="modal-desc">Corrección de saldo con justificación. Queda registrado en el log como ajuste de administrador.</div>
            <form method="POST" action="{{ route('admin.cajas.ajuste', $sucursal->id_usuario) }}">
                @csrf
                {{-- Dirección --}}
                <div style="margin-bottom:.9rem;">
                    <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--c-muted);margin-bottom:.38rem;">Dirección del ajuste</div>
                    <div class="dir-toggle">
                        <div class="dir-opt" :class="dir===1 ? 'dir-entrada' : ''" @click="dir=1">
                            ↑ Entrada (suma)
                        </div>
                        <div class="dir-opt" :class="dir===0 ? 'dir-salida' : ''" @click="dir=0">
                            ↓ Salida (resta)
                        </div>
                    </div>
                    <input type="hidden" name="es_entrada" :value="dir">
                </div>
                <div class="field">
                    <label>Monto</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required>
                </div>
                <div class="field">
                    <label>Concepto / justificación</label>
                    <input type="text" name="concepto" maxlength="200" required
                           placeholder="Ej. Corrección por error de captura del {{ now()->format('d/m/Y') }}...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-yellow">Aplicar ajuste</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════ MODAL: CIERRE FORZADO ═══════════ --}}
    <div class="overlay" x-show="modal==='cierre'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop>
            <div class="modal-title" style="color:var(--c-red)">⚠ Forzar cierre de sesión</div>
            <div class="modal-desc">
                Se cerrará la sesión activa de <strong>{{ $sucursal->nombre_usuario }}</strong>.
                Se generará un corte de cierre con motivo <em>«admin»</em> y se te redirigirá al PDF.
            </div>
            @if($snapshot)
            <div style="background:#0d1117;border:1px solid var(--c-border);border-radius:7px;padding:.85rem 1rem;margin-bottom:1rem;">
                <div class="info-row">
                    <span class="lbl">Total sistema al cierre</span>
                    <span class="val green">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="lbl">Ventas registradas</span>
                    <span class="val">{{ $snapshot['ventas_count'] }}</span>
                </div>
            </div>
            @endif
            <form method="POST" action="{{ route('admin.cajas.cerrar.forzado', $sucursal->id_usuario) }}">
                @csrf
                <div class="field">
                    <label>Notas <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <textarea name="notas" rows="2" maxlength="500"
                              placeholder="Razón del cierre forzado..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Forzar cierre y generar PDF</button>
                </div>
            </form>
        </div>
    </div>

</div>


<script>
function showCaja() {
    return {
        modal: '',
        init() {
            // Abrir modal de crear si llegamos sin caja
            @if(!$caja)
            // No auto-abrir, el usuario decide
            @endif
        },
    }
}
</script>
</x-app-layout>