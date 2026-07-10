{{-- resources/views/vendedor/caja/corte-pdf.blade.php --}}
{{-- Renderizado con DomPDF — papel ~80mm (226.77pt ancho) --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            color: #111;
            background: #fff;
            width: 200pt;
            padding: 8pt 6pt 12pt;
        }

        /* ── Header ── */
        .biz-name {
            font-size: 12pt;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 2pt;
        }

        .biz-sub {
            font-size: 8pt;
            text-align: center;
            color: #555;
            margin-bottom: 6pt;
        }

        .tipo-badge {
            display: block;
            text-align: center;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 2pt 0;
            margin-bottom: 4pt;
        }

        .tipo-parcial {
            color: #1a56db;
            border-top: 1pt solid #1a56db;
            border-bottom: 1pt solid #1a56db;
        }

        .tipo-cierre {
            color: #c0392b;
            border-top: 1pt solid #c0392b;
            border-bottom: 1pt solid #c0392b;
        }

        /* ── Dividers ── */
        .div-solid {
            border: none;
            border-top: 1pt solid #333;
            margin: 5pt 0;
        }

        .div-dash {
            border: none;
            border-top: 1pt dashed #aaa;
            margin: 4pt 0;
        }

        /* ── Rows ── */
        .row {
            display: table;
            width: 100%;
            margin-bottom: 2pt;
        }

        .row-lbl {
            display: table-cell;
            width: 55%;
            color: #444;
        }

        .row-val {
            display: table-cell;
            text-align: right;
            font-weight: 700;
        }

        .row-val.big {
            font-size: 11pt;
        }

        .row-val.green {
            color: #15803d;
        }

        .row-val.red {
            color: #b91c1c;
        }

        .row-val.blue {
            color: #1d4ed8;
        }

        /* ── Section title ── */
        .sec-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #666;
            margin: 5pt 0 3pt;
        }

        /* ── Meta info ── */
        .meta {
            font-size: 7.5pt;
            color: #666;
            margin-bottom: 1.5pt;
        }

        .meta-val {
            font-weight: 700;
            color: #333;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 7pt;
            color: #888;
            margin-top: 8pt;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    {{-- ── Encabezado ── --}}
    <div class="biz-name">
        {{ $corte->sesion->caja->negocio->nombre ?? config('app.name', 'Stratus') }}
    </div>
    <div class="biz-sub">Corte de caja</div>

    @php
    $snapshot = is_array($snapshot) ? $snapshot : (array) $snapshot;

    $tipo = $snapshot['tipo'] ?? 'parcial';
    $totales = $snapshot['totales'] ?? [];
    $sesionSnap = $snapshot['sesion'] ?? [];
    $porMetodo = $snapshot['por_metodo'] ?? [];
    $usuario = $snapshot['usuario'] ?? [];
    $ventasCount = $snapshot['ventas_count'] ?? 0;

    $diferencia = $sesion->diferencia ?? null;
    $declarado = $sesion->monto_cierre_declarado ?? null;
    @endphp

    <div class="tipo-badge tipo-{{ $tipo }}">
        {{ $tipo === 'cierre' ? '— Corte de cierre —' : '— Corte parcial —' }}
    </div>

    {{-- ── Datos de sesión ── --}}
    <div class="meta">Cajero:
        <span class="meta-val">
            {{ $usuario['nombre_usuario'] ?? $usuario['nombre'] ?? '—' }}
        </span>
    </div>

    @if(!empty($sesionSnap['abierta_at']))
    <div class="meta">Apertura:
        <span class="meta-val">
            {{ \Carbon\Carbon::parse($sesionSnap['abierta_at'])->format('d/m/Y H:i') }}
        </span>
    </div>
    @endif

    @if($tipo === 'cierre' && $sesion->cerrada_at)
    <div class="meta">Cierre:
        <span class="meta-val">{{ $sesion->cerrada_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="meta">Duración:
        <span class="meta-val">{{ $sesion->duracion ?? '—' }}</span>
    </div>
    @endif

    <div class="meta">Generado: <span class="meta-val">{{ $fecha }}</span></div>

    @if(!empty($sesionSnap['id_sesion']))
    <div class="meta" style="font-size:6.5pt;color:#aaa;">{{ $sesionSnap['id_sesion'] }}</div>
    @endif

    <hr class="div-solid">

    {{-- ── Totales ── --}}
    <div class="sec-title">Resumen</div>

    <div class="row">
        <span class="row-lbl">Fondo inicial</span>
        <span class="row-val">${{ number_format($sesionSnap['fondo_inicial'] ?? 0, 2) }}</span>
    </div>
    <div class="row">
        <span class="row-lbl">Ventas ({{ $ventasCount }})</span>
        <span class="row-val green">+${{ number_format($totales['ingresos_ventas'] ?? 0, 2) }}</span>
    </div>

    @if(($totales['ingresos_manuales'] ?? 0) > 0)
    <div class="row">
        <span class="row-lbl">Ingresos manuales</span>
        <span class="row-val blue">+${{ number_format($totales['ingresos_manuales'], 2) }}</span>
    </div>
    @endif

    @if(($totales['retiros'] ?? 0) > 0)
    <div class="row">
        <span class="row-lbl">Retiros</span>
        <span class="row-val red">-${{ number_format($totales['retiros'], 2) }}</span>
    </div>
    @endif

    @if(($totales['ajustes_neto'] ?? 0) != 0)
    <div class="row">
        <span class="row-lbl">Ajustes neto</span>
        <span class="row-val {{ $totales['ajustes_neto'] >= 0 ? 'green' : 'red' }}">
            {{ $totales['ajustes_neto'] >= 0 ? '+' : '-' }}${{ number_format(abs($totales['ajustes_neto']), 2) }}
        </span>
    </div>
    @endif

    <hr class="div-dash">

    <div class="row">
        <span class="row-lbl" style="font-weight:700">TOTAL SISTEMA</span>
        <span class="row-val big green">${{ number_format($totales['total_sistema'] ?? 0, 2) }}</span>
    </div>

    @if($tipo === 'cierre' && $declarado !== null)
    <hr class="div-dash">
    <div class="row">
        <span class="row-lbl">Monto declarado</span>
        <span class="row-val">${{ number_format($declarado, 2) }}</span>
    </div>
    <div class="row">
        <span class="row-lbl" style="font-weight:700">DIFERENCIA</span>
        <span class="row-val {{ ($diferencia ?? 0) < 0 ? 'red' : 'green' }}">
            {{ ($diferencia ?? 0) >= 0 ? '+' : '' }}${{ number_format($diferencia ?? 0, 2) }}
        </span>
    </div>
    @endif

    {{-- ── Por método de pago ── --}}
    @if(!empty($porMetodo))
    <hr class="div-solid">
    <div class="sec-title">Por método de pago</div>
    @foreach($porMetodo as $m)
    <div class="row">
        <span class="row-lbl">
            {{ $m['nombre'] ?? $m['label'] ?? $m['metodo'] ?? '—' }}{{ ($m['es_efectivo'] ?? false)}}
        </span>
        <span class="row-val green">${{ number_format($m['total'] ?? 0, 2) }}</span>
    </div>
    @endforeach
    @endif

    {{-- ── Detalle de movimientos ── --}}
    @if(!empty($snapshot['movimientos'] ?? []))
    <hr class="div-solid">
    <div class="sec-title">Movimientos</div>
    @foreach($snapshot['movimientos'] as $mv)
    <div class="row">
        <span class="row-lbl" style="font-size:7.5pt;">
            {{ \Carbon\Carbon::parse($mv['fecha'])->format('H:i') }} · {{ $mv['label'] }}
        </span>
        <span class="row-val {{ $mv['es_entrada'] ? 'green' : 'red' }}" style="font-size:8pt;">
            {{ $mv['es_entrada'] ? '+' : '-' }}${{ number_format($mv['monto'], 2) }}
        </span>
    </div>
    @if(!empty($mv['concepto']))
    <div style="font-size:6.5pt;color:#999;margin:-1pt 0 2pt;">{{ $mv['concepto'] }}</div>
    @endif
    @endforeach
    @endif

    {{-- ── Gastos registrados ── --}}
    @if(!empty($snapshot['gastos'] ?? []))
    <hr class="div-solid">
    <div class="sec-title">Gastos registrados</div>
    @foreach($snapshot['gastos'] as $g)
    <div class="row">
        <span class="row-lbl" style="font-size:7.5pt;">
            {{ \Carbon\Carbon::parse($g['fecha'])->format('d/m') }} · {{ $g['motivo'] }}
        </span>
        <span class="row-val red" style="font-size:8pt;">-${{ number_format($g['monto'], 2) }}</span>
    </div>
    @endforeach
    <div class="row" style="margin-top:2pt;">
        <span class="row-lbl" style="font-weight:700">Total gastos</span>
        <span class="row-val red">-${{ number_format($snapshot['gastos_total'] ?? 0, 2) }}</span>
    </div>
    @endif

    {{-- ── Notas de cierre ── --}}
    @if($tipo === 'cierre' && !empty($sesion->notas_cierre))
    <hr class="div-dash">
    <div class="sec-title">Notas</div>
    <div style="font-size:8pt;color:#555;line-height:1.4;">{{ $sesion->notas_cierre }}</div>
    @endif

    {{-- ── Footer ── --}}
    <hr class="div-solid">
    <div class="footer">
        {{ $tipo === 'parcial' ? 'Corte parcial — sesión continúa abierta' : 'Sesión cerrada' }}<br>
        {{ config('app.name', 'Stratus') }} · {{ $fecha }}
    </div>

</body>

</html>