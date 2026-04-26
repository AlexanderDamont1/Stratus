<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de compra — {{ $venta->id_venta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
        }

        .page {
            width: 216mm;
            min-height: 279mm;
            margin: 0 auto;
            padding: 12mm 14mm;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10px;
            border-bottom: 2px solid #111;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #111;
        }

        .brand-sub {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .ticket-meta {
            text-align: right;
        }

        .ticket-id {
            font-size: 13px;
            font-weight: 700;
            color: #111;
            font-family: monospace;
        }

        .ticket-label {
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .ticket-date {
            font-size: 10px;
            color: #555;
            margin-top: 4px;
        }

        /* ── BADGE ── */
        .badge-ticket {
            display: inline-block;
            background: #111;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 18px;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #999;
            margin-bottom: 8px;
        }

        /* ── CLIENTE ── */
        .cliente-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: #f8f8f8;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }

        .cliente-item label {
            display: block;
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .cliente-item span {
            font-size: 11px;
            font-weight: 600;
            color: #111;
        }

        /* ── VENDEDOR ── */
        .vendedor-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .vendedor-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #111;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .vendedor-info label {
            display: block;
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .vendedor-info span {
            font-size: 11px;
            font-weight: 600;
            color: #111;
        }

        /* ── TABLA ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        thead tr {
            background: #111;
            color: #fff;
        }

        thead th {
            padding: 8px 10px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }

        thead th.text-right {
            text-align: right;
        }

        thead th.text-center {
            text-align: center;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        tbody td {
            padding: 9px 10px;
            color: #333;
            vertical-align: top;
        }

        .td-right { text-align: right; }
        .td-center { text-align: center; }

        .producto-nombre {
            font-weight: 600;
            color: #111;
            margin-bottom: 2px;
        }

        .producto-tipo {
            display: inline-block;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1px 5px;
            border-radius: 10px;
            background: #efefef;
            color: #666;
        }

        .producto-tipo.bici {
            background: #e8eaff;
            color: #4338ca;
        }

        .serie-mono {
            font-family: monospace;
            font-size: 9px;
            color: #888;
            background: #f3f3f3;
            padding: 1px 4px;
            border-radius: 3px;
        }

        .detalle-bici {
            font-size: 9px;
            color: #777;
            margin-top: 2px;
        }

        /* ── TOTALES ── */
        .totales-wrapper {
            border: 1px solid #e5e5e5;
            border-top: none;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .tabla-wrapper {
            border: 1px solid #e5e5e5;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .totales-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 14px;
            border-top: 1px solid #f0f0f0;
        }

        .totales-row.total-final {
            background: #111;
            color: #fff;
            padding: 12px 14px;
        }

        .totales-row label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .totales-row.total-final label {
            color: #ccc;
            font-size: 9px;
        }

        .totales-row span {
            font-size: 11px;
            font-weight: 600;
            color: #111;
        }

        .totales-row.total-final span {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 1px solid #e5e5e5;
            padding-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left p {
            font-size: 9px;
            color: #aaa;
            line-height: 1.6;
        }

        .footer-left strong {
            color: #555;
        }

        .firma-box {
            text-align: center;
            width: 140px;
        }

        .firma-line {
            border-top: 1px solid #ccc;
            margin-bottom: 4px;
        }

        .firma-label {
            font-size: 8px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── GRACIAS ── */
        .gracias {
            text-align: center;
            margin: 18px 0 14px;
        }

        .gracias p {
            font-size: 10px;
            color: #aaa;
        }

        .gracias strong {
            font-size: 13px;
            color: #111;
            display: block;
            margin-bottom: 2px;
        }

        @media print {
            body { background: #fff; }
            .page { padding: 8mm 10mm; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="brand">
            <span class="brand-name">{{ $negocio->nombre_negocio }}</span>
            <span class="brand-sub">Comprobante de compra</span>
        </div>
        <div class="ticket-meta">
            <div class="ticket-label">Folio</div>
            <div class="ticket-id">{{ $venta->id_venta }}</div>
            <div class="ticket-date">
                {{ $venta->created_at->format('d/m/Y') }}
                &nbsp;·&nbsp;
                {{ $venta->created_at->format('H:i') }} hrs
            </div>
        </div>
    </div>

    <span class="badge-ticket">Ticket de compra</span>

    {{-- ── CLIENTE ── --}}
    <div class="section-title">Datos del cliente</div>
    <div class="cliente-grid">
        <div class="cliente-item">
            <label>Nombre completo</label>
            <span>
                {{ $cliente->nombre_cliente }}
                {{ $cliente->apellido1 }}
                {{ $cliente->apellido2 }}
            </span>
        </div>
        <div class="cliente-item">
            <label>Teléfono</label>
            <span>{{ $cliente->telefono }}</span>
        </div>
        @if($cliente->correo)
        <div class="cliente-item">
            <label>Correo electrónico</label>
            <span>{{ $cliente->correo }}</span>
        </div>
        @endif
    </div>

    {{-- ── VENDEDOR ── --}}
    <div class="vendedor-row">
        <div class="vendedor-avatar">
            {{ strtoupper(substr($vendedor->nombre_usuario, 0, 1)) }}
        </div>
        <div class="vendedor-info">
            <label>Atendido por</label>
            <span>{{ $vendedor->nombre_usuario }}</span>
        </div>
    </div>

    {{-- ── TABLA DE PRODUCTOS ── --}}
    <div class="section-title">Productos</div>
    <div class="tabla-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>N° Serie</th>
                    <th>Especificaciones</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($venta->detalles as $detalle)
                @php
                    $subtotal = $detalle->precio_unitario * $detalle->cantidad;
                    $total   += $subtotal;
                    $esBici   = ($detalle->producto->tipo ?? '') === '2';
                @endphp
                <tr>
                    <td>
                        <div class="producto-nombre">
                            {{ $detalle->producto->nombre_producto ?? '—' }}
                        </div>
                        <span class="producto-tipo {{ $esBici ? 'bici' : '' }}">
                            {{ $esBici ? 'Bicicleta' : 'Accesorio' }}
                        </span>
                    </td>
                    <td>
                        @if($detalle->num_serie)
                            <span class="serie-mono">{{ $detalle->num_serie }}</span>
                        @else
                            <span style="color:#ccc">—</span>
                        @endif
                    </td>
                    <td>
                        @if($detalle->bicicleta)
                            <div class="detalle-bici">
                                {{ $detalle->bicicleta->modelo->nombre_modelo ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ $detalle->bicicleta->color->color ?? '—' }}
                                &nbsp;·&nbsp;
                                {{ $detalle->bicicleta->voltaje->voltaje ?? '—' }}
                            </div>
                        @else
                            <span style="color:#ccc">—</span>
                        @endif
                    </td>
                    <td class="td-center" style="font-weight:600">
                        {{ $detalle->cantidad }}
                    </td>
                    <td class="td-right" style="color:#555">
                        ${{ number_format($detalle->precio_unitario, 2) }}
                    </td>
                    <td class="td-right" style="font-weight:700;color:#111">
                        ${{ number_format($subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── TOTALES ── --}}
    <div class="totales-wrapper">
        <div class="totales-row">
            <label>Subtotal</label>
            <span>${{ number_format($total, 2) }}</span>
        </div>
        <div class="totales-row total-final">
            <label>Total pagado</label>
            <span>${{ number_format($total, 2) }}</span>
        </div>
    </div>

    {{-- ── GRACIAS ── --}}
    <div class="gracias">
        <strong>¡Gracias por tu compra!</strong>
        <p>Conserva este comprobante para cualquier aclaración.</p>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <p>
                <strong>Documento generado:</strong>
                {{ now()->format('d/m/Y H:i') }} hrs
            </p>
            <p>
                <strong>Negocio:</strong> {{ $negocio->nombre_negocio }}
            </p>
            <p style="margin-top:4px;font-size:8px;color:#ccc">
                Este documento es un comprobante de compra interno.
            </p>
        </div>

        <div class="firma-box">
            <div class="firma-line"></div>
            <div class="firma-label">Firma del vendedor</div>
        </div>
    </div>

</div>
</body>
</html>