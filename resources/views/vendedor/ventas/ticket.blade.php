<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Compra - #{{ $venta->id_venta }}</title>
    <style>
        :root {
            --primary: #111827;
            --blue: #2563eb;
            --blue-light: #eff6ff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg-body: #f3f4f6;
            --bg-card: #ffffff;
            --bg-alt: #f9fafb;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            font-size: 13px;
            line-height: 1.5;
            padding: 20px;
        }

        /* ─── Contenedor Principal ─── */
        .ticket-wrapper {
            max-width: 760px;
            margin: 0 auto;
            background: var(--bg-card);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05), 0 4px 6px rgba(0,0,0,0.02);
            overflow: hidden;
        }

        /* ─── HEADER ─── */
        .header {
            background-color: var(--primary);
            color: #fff;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header-brand h1 {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1;
        }
        .header-brand span { color: #60a5fa; }
        .header-brand p {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 6px;
        }
        .header-meta { text-align: right; }
        .header-meta .label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .header-meta .folio { font-size: 18px; font-weight: 700; font-family: monospace; margin: 2px 0; }
        .header-meta .date { font-size: 11px; color: #d1d5db; }

        /* ─── CUERPO (Grid 2 columnas) ─── */
        .ticket-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            padding: 32px;
        }

        /* ─── Etiquetas de sección ─── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        /* ─── Cliente y Emisor ─── */
        .info-block strong { display: block; font-size: 15px; color: var(--primary); line-height: 1.2; }
        .info-block p { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        /* ─── Tarjetas de Bicicletas ─── */
        .bici-card {
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .bici-header {
            background: var(--bg-alt);
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }
        .bici-header .unit { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        .bici-header .model { font-size: 13px; font-weight: 800; color: var(--primary); }
        .bici-header .price { font-weight: 700; font-family: monospace; font-size: 14px; }
        
        .bici-details { display: flex; padding: 10px 14px; background: #fff; }
        .bici-detail-item { flex: 1; }
        .bici-detail-item span { display: block; font-size: 9px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; }
        .bici-detail-item strong { font-size: 12px; }

        .bici-footer {
            background: var(--bg-alt);
            padding: 8px 14px;
            border-top: 1px dashed var(--border);
            font-size: 11px;
            display: flex;
            justify-content: space-between;
        }
        .bici-footer span { font-size: 9px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; }
        .bici-footer strong { font-family: monospace; }

        /* ─── Tabla de Accesorios ─── */
        .acc-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .acc-table th { font-size: 10px; text-transform: uppercase; color: var(--text-muted); text-align: left; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
        .acc-table th.text-right { text-align: right; }
        .acc-table th.text-center { text-align: center; }
        .acc-table td { padding: 8px 0; border-bottom: 1px solid var(--bg-alt); font-size: 12px; }
        .acc-table td.text-right { text-align: right; font-family: monospace; font-weight: 700; }
        .acc-table td.text-center { text-align: center; }
        .badge-free { background: #d1fae5; color: #065f46; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 800; letter-spacing: 0.5px; }

        /* ─── Resumen Totales ─── */
        .totals { margin-top: 16px; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 12px; color: var(--text-muted); }
        .total-row.discount { color: #16a34a; }
        .total-row strong { font-family: monospace; font-size: 13px; }
        
        .total-final {
            background: var(--primary);
            color: #fff;
            padding: 16px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }
        .total-final span { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #9ca3af; }
        .total-final strong { font-size: 24px; font-family: monospace; font-weight: 900; }

        /* ─── Garantía ─── */
        .warranty-box {
            background: var(--blue-light);
            border: 1px solid #bfdbfe;
            padding: 12px;
            border-radius: 6px;
            margin-top: 24px;
        }
        .warranty-box h4 { color: var(--blue); font-size: 10px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .warranty-box p { font-size: 11px; color: #3b82f6; line-height: 1.4; }

        /* ─── Firmas ─── */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px dashed var(--border);
        }
        .sig-block { flex: 1; max-width: 45%; }
        .sig-line { border-top: 1px solid var(--text-dark); margin-bottom: 6px; padding-top: 24px; }
        .sig-block span { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .sig-block p { font-size: 12px; font-weight: 700; color: var(--primary); margin-top: 2px; }
        .sig-block.right { text-align: right; }
        .sig-block.right .sig-line { border: none; padding-top: 25px; }

        /* ─── FOOTER ─── */
        .footer {
            background: var(--bg-alt);
            padding: 16px 32px;
            border-top: 1px dashed var(--border);
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
        }
        .footer strong { color: var(--primary); font-family: monospace; }

        /* ─── RESPONSIVE (Móviles) ─── */
        @media screen and (max-width: 640px) {
            body { padding: 10px; }
            .header { flex-direction: column; align-items: flex-start; gap: 16px; padding: 20px; }
            .header-meta { text-align: left; }
            .ticket-body { grid-template-columns: 1fr; gap: 24px; padding: 20px; }
            .signatures { flex-direction: column; gap: 32px; }
            .sig-block { max-width: 100%; }
            .sig-block.right { text-align: left; }
            .footer { flex-direction: column; text-align: center; gap: 8px; }
        }
    </style>
</head>
<body>

<div class="ticket-wrapper">
    
    {{-- ══ HEADER ══ --}}
    <header class="header">
        <div class="header-brand">
            <h1>{{ strtoupper($negocio->nombre_negocio ?? 'TIENDA') }}</h1>
            <p>Comprobante de compra</p>
        </div>
        <div class="header-meta">
            <div class="label">Folio de venta</div>
            <div class="folio">#{{ $venta->id_venta }}</div>
            <div class="date">{{ now()->format('d/m/Y') }} · {{ now()->format('H:i') }} hrs</div>
        </div>
    </header>

    {{-- ══ CUERPO PRINCIPAL ══ --}}
    <div class="ticket-body">
        
        {{-- COLUMNA IZQUIERDA: Cliente y Productos --}}
        <div class="col-left">
            
            <div class="section-title">Cliente</div>
            <div class="info-block">
                <strong>{{ $cliente->nombre_cliente }} {{ $cliente->apellido1 }}{{ $cliente->apellido2 ? ' ' . $cliente->apellido2 : '' }}</strong>
                <p>
                    @if($cliente->telefono) Tel: {{ $cliente->telefono }}<br> @endif
                    @if($cliente->correo) {{ $cliente->correo }} @endif
                </p>
            </div>

            <div style="height: 24px;"></div>

            {{-- Bicicletas --}}
            @if($bicicletas->isNotEmpty())
                <div class="section-title">Unidades vendidas</div>
                
                @foreach($bicicletas as $detalle)
                    @php $bici = $detalle->bicicleta; @endphp
                    <div class="bici-card">
                        <div class="bici-header">
                            <div>
                                <span class="unit">Unidad {{ $loop->iteration }}</span>
                                <div class="model">
                                    {{ $bici->modelo->marca->nombre_marca ?? '' }} 
                                    {{ $bici->modelo->nombre_modelo ?? '—' }}
                                </div>
                            </div>
                            <div class="price">${{ number_format($detalle->precio_unitario, 2) }}</div>
                        </div>
                        <div class="bici-details">
                            <div class="bici-detail-item">
                                <span>Voltaje</span>
                                <strong>{{ $bici->voltaje->voltaje ?? '—' }}</strong>
                            </div>
                            <div class="bici-detail-item">
                                <span>Color</span>
                                <strong>{{ $bici->color->color ?? '—' }}</strong>
                            </div>
                            <div class="bici-detail-item">
                                <span>Cant.</span>
                                <strong>{{ $detalle->cantidad }}</strong>
                            </div>
                        </div>
                        <div class="bici-footer">
                            <span>Número de serie</span>
                            <strong>{{ $bici->num_serie }}</strong>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Accesorios --}}
            @php
                $otros = $venta->detalles->filter(fn($d) => !$d->bicicleta);
            @endphp
            
            @if($otros->isNotEmpty())
                <div class="section-title" style="margin-top: 24px;">Accesorios</div>
                <table class="acc-table">
                    <thead>
                        <tr>
                            <th style="width: 60%;">Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otros as $d)
                            <tr>
                                <td>{{ $d->producto->nombre_producto ?? '—' }}</td>
                                <td class="text-center">{{ $d->cantidad }}</td>
                                <td class="text-right">
                                    @if((float)$d->precio_unitario === 0.0)
                                        <span class="badge-free">GRATIS</span>
                                    @else
                                        ${{ number_format($d->precio_unitario, 2) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- COLUMNA DERECHA: Emisor, Totales y Firma --}}
        <div class="col-right">
            
            <div class="section-title">Emisor</div>
            <div class="info-block">
                <strong>{{ $negocio->nombre_negocio ?? '—' }}</strong>
                <p>
                    @if($negocio->rfc ?? false) RFC: {{ $negocio->rfc }}<br> @endif
                    @if($negocio->direccion ?? false) {{ $negocio->direccion }}<br> @endif
                    @if($negocio->telefono ?? false) Tel: {{ $negocio->telefono }} @endif
                </p>
            </div>

            <div style="height: 24px;"></div>

            <div class="section-title">Resumen</div>
            <div class="totals">
                @php
                    $subtotal  = $venta->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad);
                    $descuento = $venta->descuento_total ?? 0;
                    $total     = $subtotal - $descuento;
                @endphp

                <div class="total-row">
                    <span>Subtotal</span>
                    <strong>${{ number_format($subtotal, 2) }}</strong>
                </div>

                @if($descuento > 0)
                    <div class="total-row discount">
                        <span>Descuento @if($venta->cupon) ({{ $venta->cupon->codigo ?? '' }}) @endif</span>
                        <strong>- ${{ number_format($descuento, 2) }}</strong>
                    </div>
                @endif

                <div class="total-row">
                    <span>Productos</span>
                    <strong>{{ $venta->detalles->sum('cantidad') }} pza(s)</strong>
                </div>

                <div class="total-final">
                    <span>Total Pagado</span>
                    <strong>${{ number_format($total, 2) }}</strong>
                </div>
            </div>

            @if($bicicletas->isNotEmpty())
                <div class="warranty-box">
                    <h4>Garantía incluida</h4>
                    <p>Este comprobante acredita la compra. Para hacer válida la garantía, preséntelo junto con su póliza en cualquier sucursal autorizada.</p>
                </div>
            @endif

            <div class="signatures">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <span>Nombre y firma del cliente</span>
                </div>
                <div class="sig-block right">
                    <div class="sig-line"></div>
                    <span>Atendido por</span>
                    <p>{{ $vendedor->nombre_usuario }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <footer class="footer">
        <div>Conserve este comprobante · Válido como ticket de compra · {{ now()->format('Y') }}</div>
        <div>Folio <strong>#{{ $venta->id_venta }}</strong></div>
    </footer>

</div>

</body>
</html>