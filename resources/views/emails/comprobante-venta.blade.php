@extends('emails.layout')

@section('titulo', 'Recibo de Compra')

@section('contenido')

  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#dcfce7; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#166534;">Compra confirmada</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    Hola, {{ $cliente->nombre_cliente }}
  </h1>
  <p style="font-size:13px; color:#9ca3af; margin:0 0 24px;">
    Recibo &middot; {{ $venta->id_venta }}
  </p>

  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Gracias por tu compra. Aquí está el resumen de tu pedido:
  </p>

  <table class="tabla-piezas" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
    <thead>
      <tr>
        <th align="left" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Producto</th>
        <th align="center" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Cant.</th>
        <th align="right" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($detalles as $detalle)
      <tr>
        <td style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          {{ $detalle->producto->nombre_producto ?? '—' }}
          @if($detalle->bicicleta)
          <br><span style="font-size:11px; color:#9ca3af;">
            Serie: {{ $detalle->bicicleta->num_serie }} &middot; {{ $detalle->bicicleta->modelo->nombre_modelo ?? '' }} {{ $detalle->bicicleta->voltaje->voltaje ?? '' }}
          </span>
          @endif
        </td>
        <td align="center" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb; vertical-align:top;">{{ $detalle->cantidad }}</td>
        <td align="right" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb; vertical-align:top;">${{ number_format($detalle->precio_unitario * $detalle->cantidad, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:8px 0 26px;">
    <tbody>
      <tr>
        <td style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">Total</td>
        <td align="right" style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">${{ number_format($total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <!-- Datos de la venta -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #f0f0f0;">
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:14px 0 6px; font-size:13px; color:#9ca3af;">Vendedor</td>
      <td style="padding:14px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $nombreVendedor }}</td>
    </tr>
    <tr class="stack-row">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">Fecha</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $fecha }}</td>
    </tr>
  </table>

@endsection
