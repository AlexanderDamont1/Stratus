<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Compra</title>
    <style>
        /* ============================================
           ESTILO TICKET ~80mm (estilo corte de caja)
           Monospace, compacto, limpio, estilo POS
        ============================================ */
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
            width: 220pt;
            max-width: 100%;
            margin: 0 auto;
            padding: 8pt 6pt 12pt;
        }

        /* --- encabezado principal --- */
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

        .ticket-badge {
            display: block;
            text-align: center;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 2pt 0;
            margin-bottom: 4pt;
            color: #1a56db;
            border-top: 1pt solid #1a56db;
            border-bottom: 1pt solid #1a56db;
        }

        /* --- divisores --- */
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

        /* --- filas genéricas (label + valor) --- */
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

        /* --- títulos de sección --- */
        .sec-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #666;
            margin: 5pt 0 3pt;
        }

        /* --- metadatos (folio, fechas) --- */
        .meta {
            font-size: 7.5pt;
            color: #666;
            margin-bottom: 1.5pt;
        }
        .meta-val {
            font-weight: 700;
            color: #333;
        }

        /* --- bloque de bicicleta (estilo compacto) --- */
        .bici-block {
            border: 1pt solid #ddd;
            margin: 6pt 0 6pt;
            padding: 4pt 5pt;
            background: #fefefe;
        }
        .bici-row {
            display: table;
            width: 100%;
            margin-bottom: 2pt;
            font-size: 8pt;
        }
        .bici-row .label {
            display: table-cell;
            width: 35%;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .bici-row .value {
            display: table-cell;
            text-align: right;
            font-weight: 600;
        }
        .bici-header {
            font-weight: 800;
            font-size: 9pt;
            margin-bottom: 4pt;
            border-bottom: 1pt dotted #ccc;
            padding-bottom: 2pt;
            display: flex;
            justify-content: space-between;
        }
        .bici-modelo {
            text-transform: uppercase;
        }
        .bici-precio {
            font-weight: 700;
        }

        /* --- tabla accesorios estilo minimalista --- */
        .accesorios-list {
            margin-top: 3pt;
        }
        .acc-row {
            display: table;
            width: 100%;
            margin-bottom: 2pt;
            font-size: 8pt;
        }
        .acc-nombre {
            display: table-cell;
            width: 55%;
        }
        .acc-cant {
            display: table-cell;
            text-align: center;
            width: 15%;
        }
        .acc-precio {
            display: table-cell;
            text-align: right;
            width: 30%;
            font-weight: 700;
        }
        .badge-gratis {
            background: #e6f7e6;
            font-size: 7pt;
            padding: 0 4px;
            border-radius: 2px;
            font-weight: bold;
            color: #2e7d32;
        }

        /* --- garantía y notas --- */
        .warranty-note {
            background: #f4f9ff;
            padding: 5pt;
            margin: 8pt 0 4pt;
            font-size: 7.5pt;
            text-align: center;
            border-left: 2pt solid #2563eb;
            color: #1e40af;
        }

        /* --- firmas (dos columnas) --- */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 12pt;
            border-top: 1pt dashed #aaa;
            padding-top: 8pt;
        }
        .sig-left {
            display: table-cell;
            width: 50%;
        }
        .sig-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }
        .sig-line {
            border-top: 1pt solid #111;
            margin: 6pt 0 3pt;
            width: 90%;
        }
        .sig-right .sig-line {
            margin-left: auto;
        }
        .sig-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
        }
        .sig-name {
            font-size: 8pt;
            font-weight: 700;
        }

        /* --- footer --- */
        .footer {
            text-align: center;
            font-size: 7pt;
            color: #888;
            margin-top: 10pt;
            line-height: 1.4;
        }
    </style>
</head>
<body>

{{-- ============================================================ --}}
{{--  TICKET DE COMPRA   (estilo ~80mm, monospace, tipo caja)    --}}
{{--  Mantiene toda la info: cliente, bicis, accesorios, totales  --}}
{{-- ============================================================ --}}

<div class="biz-name">
    {{ strtoupper($negocio->nombre_negocio ?? 'TIENDA CICLISTA') }}
</div>
<div class="biz-sub">Comprobante de compra · Ticket fiscal</div>

<div class="ticket-badge">
    VENTA #{{ $venta->id_venta }}
</div>

{{-- meta datos rápidos: fecha y hora --}}
<div class="meta">
    Fecha: <span class="meta-val">{{ now()->format('d/m/Y H:i') }}</span>
</div>
<div class="meta">
    Folio: <span class="meta-val">{{ $venta->id_venta }}</span>
</div>

<hr class="div-solid">

{{-- ==================== CLIENTE ==================== --}}
<div class="sec-title">DATOS DEL CLIENTE</div>
<div class="row">
    <span class="row-lbl">Cliente</span>
    <span class="row-val">
        {{ $cliente->nombre_cliente }} {{ $cliente->apellido1 }}{{ $cliente->apellido2 ? ' ' . $cliente->apellido2 : '' }}
    </span>
</div>
@if($cliente->telefono)
<div class="row">
    <span class="row-lbl">Teléfono</span>
    <span class="row-val">{{ $cliente->telefono }}</span>
</div>
@endif
@if($cliente->correo)
<div class="row">
    <span class="row-lbl">Correo</span>
    <span class="row-val">{{ $cliente->correo }}</span>
</div>
@endif

<hr class="div-dash">

{{-- ==================== EMISOR (negocio) ==================== --}}
<div class="sec-title">EMISOR</div>
<div class="row">
    <span class="row-lbl">Negocio</span>
    <span class="row-val">{{ $negocio->nombre_negocio ?? '—' }}</span>
</div>
@if($negocio->rfc ?? false)
<div class="row">
    <span class="row-lbl">RFC</span>
    <span class="row-val">{{ $negocio->rfc }}</span>
</div>
@endif
@if($negocio->direccion ?? false)
<div class="row">
    <span class="row-lbl">Dirección</span>
    <span class="row-val" style="word-break:break-word;">{{ $negocio->direccion }}</span>
</div>
@endif
@if($negocio->telefono ?? false)
<div class="row">
    <span class="row-lbl">Teléfono</span>
    <span class="row-val">{{ $negocio->telefono }}</span>
</div>
@endif

<hr class="div-solid">

{{-- ==================== BICICLETAS VENDIDAS ==================== --}}
@if($bicicletas->isNotEmpty())
<div class="sec-title">UNIDADES VENDIDAS</div>
@foreach($bicicletas as $detalle)
    @php $bici = $detalle->bicicleta; @endphp
    <div class="bici-block">
        <div class="bici-header">
            <span class="bici-modelo">
                {{ $bici->modelo->marca->nombre_marca ?? '—' }} 
                {{ $bici->modelo->nombre_modelo ?? 'Bicicleta' }}
            </span>
            <span class="bici-precio">${{ number_format($detalle->precio_unitario, 2) }}</span>
        </div>
        <div class="bici-row">
            <span class="label">Cantidad:</span>
            <span class="value">{{ $detalle->cantidad }}</span>
        </div>
        <div class="bici-row">
            <span class="label">N° Serie:</span>
            <span class="value">{{ $bici->num_serie ?? '—' }}</span>
        </div>
        <div class="bici-row">
            <span class="label">Voltaje / Color:</span>
            <span class="value">
                {{ $bici->voltaje->voltaje ?? '—' }} · 
                {{ $bici->color->color ?? '—' }}
            </span>
        </div>
    </div>
@endforeach
@endif

{{-- ==================== ACCESORIOS ==================== --}}
@php
    $accesorios = $venta->detalles->filter(fn($d) => !$d->bicicleta);
@endphp
@if($accesorios->isNotEmpty())
<div class="sec-title">ACCESORIOS</div>
<div class="accesorios-list">
    @foreach($accesorios as $item)
    <div class="acc-row">
        <span class="acc-nombre">{{ $item->producto->nombre_producto ?? 'Producto' }}</span>
        <span class="acc-cant">{{ $item->cantidad }}</span>
        <span class="acc-precio">
            @if((float)$item->precio_unitario === 0.0)
                <span class="badge-gratis">GRATIS</span>
            @else
                ${{ number_format($item->precio_unitario, 2) }}
            @endif
        </span>
    </div>
    @endforeach
</div>
@endif

<hr class="div-dash">

{{-- ==================== RESUMEN ECONÓMICO ==================== --}}
<div class="sec-title">RESUMEN DE PAGO</div>
@php
    $subtotal  = $venta->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
    $descuento = $venta->descuento_total ?? 0;
    $total     = $subtotal - $descuento;
    $totalProductos = $venta->detalles->sum('cantidad');
@endphp

<div class="row">
    <span class="row-lbl">Subtotal</span>
    <span class="row-val">${{ number_format($subtotal, 2) }}</span>
</div>

@if($descuento > 0)
<div class="row">
    <span class="row-lbl">Descuento 
        @if($venta->cupon)({{ $venta->cupon->codigo ?? 'cupón' }})@endif
    </span>
    <span class="row-val red">-${{ number_format($descuento, 2) }}</span>
</div>
@endif

<div class="row">
    <span class="row-lbl">Artículos</span>
    <span class="row-val">{{ $totalProductos }} unidad(es)</span>
</div>

<hr class="div-dash">

<div class="row">
    <span class="row-lbl" style="font-weight:800;">TOTAL PAGADO</span>
    <span class="row-val big green">${{ number_format($total, 2) }}</span>
</div>

{{-- ==================== GARANTÍA (solo si hay bicis) ==================== --}}
@if($bicicletas->isNotEmpty())
<div class="warranty-note">
    🔧 GARANTÍA INCLUIDA 🔧<br>
    Este ticket respalda la compra. Preséntalo con tu póliza para servicios de garantía.
</div>
@endif

{{-- ==================== FIRMAS ==================== --}}
<div class="signatures">
    <div class="sig-left">
        <div class="sig-line" style="width:90%;"></div>
        <div class="sig-label">Nombre y firma del cliente</div>
    </div>
    <div class="sig-right">
        <div class="sig-line" style="width:90%;"></div>
        <div class="sig-label">Atendido por</div>
        <div class="sig-name">{{ $personal?->nombre ?? $negocio->nombre_negocio ?? 'Vendedor(a)' }}</div>
    </div>
</div>

<hr class="div-solid">

{{-- ==================== FOOTER ==================== --}}
<div class="footer">
    Conserva este comprobante <br> Válido como ticket de compra<br>
    {{ config('app.name') }} · {{ now()->format('Y') }} · Folio #{{ $venta->id_venta }}
</div>

</body>
</html>