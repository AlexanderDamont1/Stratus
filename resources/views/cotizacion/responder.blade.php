<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización — Responder</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; border: 1px solid #e4e4e7; max-width: 520px; width: 100%; overflow: hidden; }
        .header { background: #18181b; padding: 24px 28px; }
        .header h1 { color: #fff; font-size: 17px; font-weight: 600; }
        .header p  { color: #a1a1aa; font-size: 12px; margin-top: 3px; }
        .body { padding: 28px; }
        h2 { font-size: 16px; color: #18181b; margin-bottom: 6px; }
        .sub { font-size: 13px; color: #71717a; margin-bottom: 20px; line-height: 1.55; }
        .section-label { font-size: 11px; font-weight: 600; color: #a1a1aa; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }
        .desc-box { background: #f4f4f5; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #3f3f46; line-height: 1.65; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 13px; }
        th { text-align: left; color: #a1a1aa; font-weight: 600; font-size: 11px; padding: 5px 6px; border-bottom: 1px solid #e4e4e7; }
        td { padding: 7px 6px; color: #3f3f46; border-bottom: 1px solid #f4f4f5; }
        td.right { text-align: right; font-weight: 500; }
        .totales { background: #fafafa; border: 1px solid #e4e4e7; border-radius: 8px; padding: 14px 18px; margin: 16px 0 24px; }
        .t-row { display: flex; justify-content: space-between; font-size: 13px; color: #52525b; padding: 3px 0; }
        .t-row.total { font-size: 15px; font-weight: 700; color: #18181b; border-top: 1px solid #e4e4e7; margin-top: 8px; padding-top: 10px; }
        .nota-mant { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #78350f; line-height: 1.6; margin-bottom: 20px; }
        .acciones { display: flex; gap: 10px; }
        .acciones form { flex: 1; }
        .btn { width: 100%; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: opacity .15s; }
        .btn:hover { opacity: .85; }
        .btn-si  { background: #18181b; color: #fff; }
        .btn-no  { background: #fff; color: #18181b; border: 1.5px solid #d4d4d8; }
        .expira  { text-align: center; font-size: 12px; color: #a1a1aa; margin-top: 16px; }
    </style>
</head>
<body>
<div class="card">

    <div class="header">
        {{-- El negocio no se pasa directo a esta vista, lo sacamos de la relación --}}
        <h1>Cotización {{ $cotizacion->id_cotizacion }}</h1>
        <p>Responde antes de que expire el enlace</p>
    </div>

    <div class="body">

        @php $mant = $cotizacion->reparacion; @endphp

        @if($mant->tipo === 'reparacion')
            <h2>¿Deseas que reemplacemos las piezas sugeridas?</h2>
            <p class="sub">
                Tu reparacion se realizará de cualquier forma. Esta pregunta es únicamente
                sobre las piezas adicionales que nuestro técnico detectó durante la inspección.
            </p>
        @else
            <h2>¿Apruebas esta cotización de reparación?</h2>
            <p class="sub">
                Revisa el detalle a continuación y confirma si deseas proceder con el trabajo.
            </p>
        @endif

        {{-- Descripción del trabajo --}}
        <p class="section-label">Trabajo a realizar</p>
        <div class="desc-box">{{ $cotizacion->descripcion_trabajo }}</div>

        {{-- Tabla de piezas --}}
        @if(!empty($cotizacion->piezas_detalle))
            <p class="section-label">Piezas / componentes</p>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th style="text-align:center">Cant.</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotizacion->piezas_detalle as $p)
                    <tr>
                        <td>{{ $p['nombre'] }}</td>
                        <td style="text-align:center">{{ $p['cantidad'] }}</td>
                        <td class="right">${{ number_format($p['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Totales --}}
        <div class="totales">
            @if($mant->tipo === 'reparacion' && $cotizacion->costo_reparacion_base > 0)
                <div class="t-row">
                    <span>reparacion base</span>
                    <span>${{ number_format($cotizacion->costo_reparacion_base, 2) }}</span>
                </div>
            @endif
            @if($cotizacion->costo_piezas > 0)
                <div class="t-row">
                    <span>Piezas</span>
                    <span>${{ number_format($cotizacion->costo_piezas, 2) }}</span>
                </div>
            @endif
            @if($cotizacion->costo_mano_obra > 0)
                <div class="t-row">
                    <span>Mano de obra</span>
                    <span>${{ number_format($cotizacion->costo_mano_obra, 2) }}</span>
                </div>
            @endif
            <div class="t-row total">
                <span>Total</span>
                <span>${{ number_format($cotizacion->costo_total, 2) }}</span>
            </div>
        </div>

        @if($mant->tipo === 'reparacion')
            <div class="nota-mant">
                💡 Si eliges <strong>"Solo el reparacion"</strong>, no se realizará el reemplazo
                de piezas y el costo será únicamente el del reparacion base.
            </div>
        @endif

        {{-- Botones --}}
        <div class="acciones">
            <form method="POST" action="{{ route('cotizacion.responder', $cotizacion->token) }}">
                @csrf
                <input type="hidden" name="respuesta" value="1">
                <button type="submit" class="btn btn-si">
                    @if($mant->tipo === 'reparacion') ✓ Sí, incluir piezas
                    @else ✓ Aprobar reparación @endif
                </button>
            </form>
            <form method="POST" action="{{ route('cotizacion.responder', $cotizacion->token) }}">
                @csrf
                <input type="hidden" name="respuesta" value="0">
                <button type="submit" class="btn btn-no">
                    @if($mant->tipo === 'reparacion') Solo el reparacion
                    @else No por ahora @endif
                </button>
            </form>
        </div>

        <p class="expira">
            Enlace válido hasta
            {{ \Carbon\Carbon::parse($cotizacion->expires_at)->locale('es')->isoFormat('D [de] MMMM, h:mm A') }}
        </p>

    </div>
</div>
</body>
</html>