{{-- resources/views/vendedor/caja/index.blade.php --}}
<x-app-layout>
<style>
:root{
    --c-bg:#0d1117;--c-surface:#161b22;--c-border:#21262d;--c-border2:#30363d;
    --c-text:#e6edf3;--c-muted:#8b949e;
    --c-green:#3fb950;--c-green-d:rgba(63,185,80,.12);
    --c-red:#f85149;--c-red-d:rgba(248,81,73,.12);
    --c-blue:#58a6ff;--c-blue-d:rgba(88,166,255,.1);
    --c-accent:#f0b429;
    --mono:'JetBrains Mono','Fira Code','Courier New',monospace;
}
*{box-sizing:border-box;}

.caja-page{max-width:900px;margin:0 auto;padding:1.75rem 1.25rem 4rem;font-family:'Inter',system-ui,sans-serif;color:var(--c-text);}

.page-hd{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.75rem;}
.page-hd h1{font-size:1.35rem;font-weight:700;letter-spacing:-.02em;margin:0 0 .2rem;color:var(--c-text);}
.page-hd p{font-size:.78rem;color:var(--c-muted);margin:0;font-family:var(--mono);}

.pill{display:inline-flex;align-items:center;gap:.4rem;padding:.28rem .8rem;border-radius:99px;font-size:.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;}
.pill-open{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.3);}
.pill-closed{background:rgba(139,148,158,.1);color:var(--c-muted);border:1px solid var(--c-border2);}
.pill-dot{width:6px;height:6px;border-radius:50%;background:currentColor;animation:blink 2s ease infinite;}
@keyframes blink{0%,100%{opacity:1}60%{opacity:.25}}

.alert{display:flex;align-items:flex-start;gap:.6rem;padding:.85rem 1rem;border-radius:8px;font-size:.82rem;margin-bottom:1.25rem;line-height:1.45;}
.alert-ok{background:var(--c-green-d);color:var(--c-green);border:1px solid rgba(63,185,80,.25);}
.alert-err{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.25);}

.card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:10px;padding:1.35rem;}
.mt1{margin-top:1rem;}.mt15{margin-top:1.5rem;}
.card-lbl{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--c-muted);margin-bottom:.85rem;}

.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.65rem;margin-bottom:1.1rem;}
.stat{background:#0d1117;border:1px solid var(--c-border);border-radius:8px;padding:.9rem 1rem;}
.stat-lbl{font-size:.63rem;color:var(--c-muted);text-transform:uppercase;letter-spacing:.07em;font-weight:600;margin-bottom:.3rem;}
.stat-val{font-size:1.4rem;font-weight:700;font-family:var(--mono);line-height:1;}
.cv-green{color:var(--c-green);}.cv-yellow{color:var(--c-accent);}.cv-red{color:var(--c-red);}.cv-blue{color:var(--c-blue);}

.metodo-list{border-top:1px solid var(--c-border);margin-top:1rem;padding-top:.75rem;}
.metodo-row{display:flex;align-items:center;justify-content:space-between;padding:.55rem 0;border-bottom:1px dashed var(--c-border);font-size:.83rem;}
.metodo-row:last-child{border-bottom:none;}
.metodo-name{display:flex;align-items:center;gap:.55rem;color:var(--c-text);}
.metodo-icon{width:26px;height:26px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.8rem;background:var(--c-blue-d);}
.metodo-val{font-family:var(--mono);font-weight:600;color:var(--c-green);font-size:.85rem;}

.info-row{display:flex;justify-content:space-between;align-items:center;font-size:.8rem;padding:.3rem 0;}
.info-row .lbl{color:var(--c-muted);}
.info-row .val{font-family:var(--mono);font-weight:600;font-size:.78rem;color:var(--c-text);}
hr.sep{border:none;border-top:1px solid var(--c-border);margin:.9rem 0;}

.actions{display:flex;flex-wrap:wrap;gap:.65rem;}

.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:7px;font-weight:600;font-size:.8rem;cursor:pointer;border:none;transition:.15s;letter-spacing:.01em;text-decoration:none;font-family:inherit;}
.btn-primary{background:var(--c-accent);color:#0d1117;}
.btn-primary:hover{background:#d4961e;}
.btn-ghost{background:transparent;color:var(--c-muted);border:1px solid var(--c-border2);}
.btn-ghost:hover{color:var(--c-text);border-color:var(--c-muted);}
.btn-danger{background:var(--c-red-d);color:var(--c-red);border:1px solid rgba(248,81,73,.3);}
.btn-danger:hover{background:rgba(248,81,73,.2);}
.btn-blue{background:var(--c-blue-d);color:var(--c-blue);border:1px solid rgba(88,166,255,.3);}
.btn-blue:hover{background:rgba(88,166,255,.18);}
.btn-sm{padding:.38rem .8rem;font-size:.72rem;}

.empty-card{text-align:center;padding:3rem 2rem;}
.empty-icon{font-size:2.75rem;margin-bottom:1rem;}
.empty-title{font-size:1.05rem;font-weight:700;margin-bottom:.4rem;color:var(--c-text);}
.empty-desc{font-size:.82rem;color:var(--c-muted);margin-bottom:2rem;line-height:1.5;}

.tbl-wrap{overflow-x:auto;}
table.hist{width:100%;border-collapse:collapse;font-size:.79rem;}
table.hist th{text-align:left;font-size:.62rem;text-transform:uppercase;letter-spacing:.07em;color:var(--c-muted);padding:.5rem .6rem;border-bottom:1px solid var(--c-border);font-weight:700;white-space:nowrap;}
table.hist td{padding:.6rem .6rem;border-bottom:1px solid #0d1117;vertical-align:middle;color:var(--c-text);}
table.hist tr:last-child td{border-bottom:none;}
table.hist tr:hover td{background:rgba(255,255,255,.02);}
.mono{font-family:var(--mono);}

.chip{display:inline-block;padding:.17rem .5rem;border-radius:4px;font-size:.63rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;}
.chip-open{background:var(--c-green-d);color:var(--c-green);}
.chip-closed{background:var(--c-red-d);color:var(--c-red);}
.chip-auto{background:rgba(139,148,158,.12);color:var(--c-muted);}

.overlay{position:fixed;inset:0;background:rgba(0,0,0,.72);backdrop-filter:blur(3px);z-index:900;display:flex;align-items:center;justify-content:center;padding:1rem;}
.modal{background:var(--c-surface);border:1px solid var(--c-border2);border-radius:12px;padding:1.75rem;width:100%;max-width:430px;box-shadow:0 20px 50px rgba(0,0,0,.6);}
.modal-title{font-size:1.05rem;font-weight:700;margin-bottom:.3rem;color:var(--c-text);}
.modal-desc{font-size:.8rem;color:var(--c-muted);margin-bottom:1.4rem;line-height:1.5;}
.modal-footer{display:flex;justify-content:flex-end;gap:.6rem;margin-top:1.4rem;}

.field{margin-bottom:.9rem;}
.field label{display:block;font-size:.66rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--c-muted);margin-bottom:.38rem;}
.field input,.field textarea,.field select{width:100%;background:#0d1117;border:1px solid var(--c-border2);border-radius:6px;padding:.6rem .8rem;color:var(--c-text);font-size:.88rem;font-family:inherit;outline:none;transition:border-color .15s;}
.field input:focus,.field textarea:focus,.field select:focus{border-color:var(--c-blue);}
.field .mono-input{font-family:var(--mono);font-size:1.05rem;}
.field-hint{font-size:.68rem;color:var(--c-muted);margin-top:.3rem;}

.preview-box{background:#0d1117;border:1px solid var(--c-border);border-radius:7px;padding:.9rem 1rem;margin-bottom:1rem;}

@media(max-width:580px){
    .stat-grid{grid-template-columns:1fr 1fr;}
    .stat-val{font-size:1.15rem;}
    table.hist{font-size:.72rem;}
    table.hist th,table.hist td{padding:.45rem .4rem;}
}
</style>

<div class="caja-page" x-data="{ modal: '' }" x-init="
    const p = new URLSearchParams(window.location.search);
    if (p.get('abrir') === '1') modal = 'abrir';
">

    {{-- ── Alertas ── --}}
    @if(session('success'))
    <div class="alert alert-ok" role="alert">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="shrink:0;margin-top:.1rem"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-err" role="alert">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="shrink:0;margin-top:.1rem"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Header ── --}}
    <div class="page-hd">
        <div>
            <h1>🏧 Mi Caja</h1>
            <p>{{ $caja->nombre ?? 'Caja principal' }} &nbsp;·&nbsp; {{ auth()->user()->nombre_usuario }}</p>
        </div>
        @if($sesion)
            <span class="pill pill-open"><span class="pill-dot"></span>Abierta</span>
        @else
            <span class="pill pill-closed">Cerrada</span>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════
         SESIÓN ABIERTA
    ══════════════════════════════════════════════ --}}
    @if($sesion && $snapshot)

    <div class="card">
        <div class="card-lbl">Resumen — sesión actual</div>
        <div class="stat-grid">
            <div class="stat">
                <div class="stat-lbl">Total sistema</div>
                <div class="stat-val cv-green">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Fondo inicial</div>
                <div class="stat-val">${{ number_format($snapshot['sesion']['fondo_inicial'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl">Ventas cobradas</div>
                <div class="stat-val cv-yellow">${{ number_format($snapshot['totales']['ingresos_ventas'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="stat-lbl"># Ventas</div>
                <div class="stat-val">{{ $snapshot['ventas_count'] }}</div>
            </div>
            @if(($snapshot['totales']['ingresos_manuales'] ?? 0) > 0)
            <div class="stat">
                <div class="stat-lbl">Ing. manuales</div>
                <div class="stat-val cv-blue">${{ number_format($snapshot['totales']['ingresos_manuales'], 2) }}</div>
            </div>
            @endif
            @if(($snapshot['totales']['retiros'] ?? 0) > 0)
            <div class="stat">
                <div class="stat-lbl">Retiros</div>
                <div class="stat-val cv-red">-${{ number_format($snapshot['totales']['retiros'], 2) }}</div>
            </div>
            @endif
            @if(($snapshot['totales']['ajustes_neto'] ?? 0) != 0)
            <div class="stat">
                <div class="stat-lbl">Ajustes</div>
                @php $aj = $snapshot['totales']['ajustes_neto']; @endphp
                <div class="stat-val {{ $aj >= 0 ? 'cv-green' : 'cv-red' }}">
                    {{ $aj >= 0 ? '+' : '' }}${{ number_format($aj, 2) }}
                </div>
            </div>
            @endif
        </div>

        {{-- Por método de pago — FIX: clave 'label' no 'nombre' --}}
        @if(!empty($snapshot['por_metodo']))
        <div class="card-lbl" style="margin-top:.5rem">Por método de pago</div>
        <div class="metodo-list">
            @foreach($snapshot['por_metodo'] as $m)
            <div class="metodo-row">
                <div class="metodo-name">
                    <div class="metodo-icon">{{ ($m['es_efectivo'] ?? false) ? '💵' : '💳' }}</div>
                    <span>{{ $m['label'] }}</span>  {{-- ← corregido: era $m['nombre'] --}}
                </div>
                <span class="metodo-val">${{ number_format($m['total'], 2) }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <hr class="sep">
        <div class="info-row">
            <span class="lbl">Abierta desde</span>
            <span class="val">{{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="lbl">ID sesión</span>
            <span class="val" style="font-size:.67rem">{{ $sesion->id_sesion }}</span>
        </div>
    </div>

    {{-- Operaciones --}}
    <div class="card mt1">
        <div class="card-lbl">Operaciones</div>
        <div class="actions">
            <button type="button" class="btn btn-blue" @click="modal='ingreso'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Ingreso manual
            </button>
            <button type="button" class="btn btn-ghost" @click="modal='corte'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                Corte parcial
            </button>
            <button type="button" class="btn btn-danger" @click="modal='cierre'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Cerrar caja
            </button>
        </div>
    </div>

    @else

    {{-- ══════════════════════════════════════════════
         CAJA CERRADA
    ══════════════════════════════════════════════ --}}
    <div class="card empty-card">
        <div class="empty-icon">🔒</div>
        <div class="empty-title">Caja cerrada</div>
        <div class="empty-desc">Abre una sesión para comenzar a registrar los movimientos del día.</div>
        <button type="button" class="btn btn-primary" @click="modal='abrir'" style="margin:0 auto;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Abrir sesión de caja
        </button>
    </div>

    @endif

    {{-- ══════════════════════════════════════════════
         Historial de sesiones
    ══════════════════════════════════════════════ --}}
    @if($historial->count())
    <div class="card mt15">
        <div class="card-lbl">Últimas sesiones</div>
        <div class="tbl-wrap">
            <table class="hist">
                <thead>
                    <tr>
                        <th>Apertura</th><th>Cierre</th><th>Fondo</th>
                        <th>Sistema</th><th>Diferencia</th><th>Estado</th><th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($historial as $ses)
                @php $corteRec = $ses->cortes()->orderByDesc('created_at')->first(); @endphp
                <tr>
                    <td>{{ $ses->abierta_at?->format('d/m H:i') }}</td>
                    <td>{{ $ses->cerrada_at?->format('d/m H:i') ?? '—' }}</td>
                    <td class="mono">${{ number_format($ses->fondo_inicial, 2) }}</td>
                    <td class="mono">${{ number_format($ses->monto_cierre_sistema ?? 0, 2) }}</td>
                    <td class="mono" style="color:{{ (($ses->diferencia ?? 0) < 0) ? 'var(--c-red)' : 'var(--c-green)' }}">
                        @if($ses->diferencia !== null)
                            {{ $ses->diferencia >= 0 ? '+' : '' }}${{ number_format($ses->diferencia, 2) }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($ses->estado === 'abierta')
                            <span class="chip chip-open">Abierta</span>
                        @elseif($ses->estado === 'auto_cerrada')
                            <span class="chip chip-auto">Auto</span>
                        @else
                            <span class="chip chip-closed">Cerrada</span>
                        @endif
                    </td>
                    <td>
                        @if($corteRec)
                        <a href="{{ route('caja.corte.pdf', $corteRec->id_corte) }}" target="_blank" class="btn btn-ghost btn-sm">PDF</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════
         MODAL: ABRIR SESIÓN
    ════════════════════════════════════════ --}}
    <div class="overlay" x-show="modal==='abrir'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-title">Abrir sesión de caja</div>
            <div class="modal-desc">Ingresa el fondo inicial en efectivo con el que comienzas la jornada.</div>
            <form method="POST" action="{{ route('caja.abrir') }}">
                @csrf
                <div class="field">
                    <label>Fondo inicial (efectivo)</label>
                    <input type="number" name="fondo_inicial" step="0.01" min="0" max="999999.99"
                           placeholder="0.00" class="mono-input" required
                           x-init="$watch('modal', v => v==='abrir' && $nextTick(() => $el.focus()))">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Abrir caja</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         MODAL: INGRESO MANUAL
    ════════════════════════════════════════ --}}
    <div class="overlay" x-show="modal==='ingreso'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-title">Registrar ingreso manual</div>
            <div class="modal-desc">Entrada de dinero que no proviene de una venta del sistema.</div>
            <form method="POST" action="{{ route('caja.ingreso') }}">
                @csrf
                <div class="field">
                    <label>Monto</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="999999.99"
                           placeholder="0.00" class="mono-input" required
                           x-init="$watch('modal', v => v==='ingreso' && $nextTick(() => $el.focus()))">
                </div>
                <div class="field">
                    <label>Método de pago</label>
                    <select name="metodo">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="field">
                    <label>Concepto</label>
                    <input type="text" name="concepto" maxlength="200" placeholder="Ej. Depósito de efectivo..." required>
                </div>
                <div class="field">
                    <label>Referencia <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="text" name="referencia" maxlength="120" placeholder="Folio, número de transferencia...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar ingreso</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         MODAL: CORTE PARCIAL
    ════════════════════════════════════════ --}}
    <div class="overlay" x-show="modal==='corte'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-title">Generar corte parcial</div>
            <div class="modal-desc">Se genera un PDF con el resumen actual. <strong>La sesión no se cierra.</strong></div>
            @if($snapshot)
            <div class="preview-box">
                <div class="info-row">
                    <span class="lbl">Total sistema</span>
                    <span class="val cv-green">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="lbl">Ventas</span>
                    <span class="val">{{ $snapshot['ventas_count'] }}</span>
                </div>
                @if(($snapshot['totales']['retiros'] ?? 0) > 0)
                <div class="info-row">
                    <span class="lbl">Retiros</span>
                    <span class="val cv-red">-${{ number_format($snapshot['totales']['retiros'], 2) }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="lbl">Desde</span>
                    <span class="val">{{ \Carbon\Carbon::parse($snapshot['sesion']['abierta_at'])->format('d/m H:i') }}</span>
                </div>
            </div>
            @endif
            <form method="POST" action="{{ route('caja.corte.parcial') }}">
                @csrf
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar PDF</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         MODAL: CERRAR SESIÓN
    ════════════════════════════════════════ --}}
    <div class="overlay" x-show="modal==='cierre'" x-cloak @click.self="modal=''"
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal" @click.stop
             x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="modal-title" style="color:var(--c-red)">⚠ Cerrar sesión de caja</div>
            <div class="modal-desc">Se generará el corte de cierre. No podrás registrar más movimientos en esta sesión.</div>
            @if($snapshot)
            <div class="preview-box">
                <div class="info-row">
                    <span class="lbl">Total sistema</span>
                    <span class="val cv-green">${{ number_format($snapshot['totales']['total_sistema'], 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="lbl">Ventas</span>
                    <span class="val">{{ $snapshot['ventas_count'] }}</span>
                </div>
                @if(($snapshot['totales']['retiros'] ?? 0) > 0)
                <div class="info-row">
                    <span class="lbl">Retiros realizados</span>
                    <span class="val cv-red">-${{ number_format($snapshot['totales']['retiros'], 2) }}</span>
                </div>
                @endif
            </div>
            @endif
            <form method="POST" action="{{ route('caja.cerrar') }}">
                @csrf
                <div class="field">
                    <label>Monto declarado <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <input type="number" name="monto_declarado" step="0.01" min="0" max="9999999.99"
                           placeholder="{{ $snapshot ? number_format($snapshot['totales']['total_sistema'], 2) : '0.00' }}"
                           class="mono-input">
                    <div class="field-hint">Si lo dejas vacío se usa el total del sistema como declarado.</div>
                </div>
                <div class="field">
                    <label>Notas <span style="color:var(--c-muted);font-weight:400">(opcional)</span></label>
                    <textarea name="notas" rows="2" maxlength="500" placeholder="Observaciones del cierre..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" @click="modal=''">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Cerrar y generar corte</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>