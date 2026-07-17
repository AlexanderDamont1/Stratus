@extends('emails.layout')

@section('titulo', 'Cotización de Reparación')

@section('contenido')

  <!-- Badge sky -->
  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#e0f2fe; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#075985;">Cotización pendiente</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    {{ $nombreNegocio }}
  </h1>
  <p style="font-size:13px; color:#9ca3af; margin:0 0 24px;">
    Cotización &middot; {{ $cotizacion->id_cotizacion }}
  </p>

  <p style="font-size:14px; line-height:1.7; margin:0 0 14px; color:#374151;">
    Hola, <strong style="color:#111827; font-weight:600;">{{ $nombreCliente }}</strong>.
  </p>
  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Nuestro técnico revisó tu vehículo. Te compartimos la cotización
    con el trabajo necesario y los costos para que puedas decidir.
  </p>

  <!-- Trabajo a realizar -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:26px;">
    <tr>
      <td style="border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 4px;">Trabajo a realizar</p>
        <p style="font-size:13px; color:#374151; line-height:1.55; margin:0;">{{ $cotizacion->descripcion_trabajo }}</p>
      </td>
    </tr>
  </table>

  @if(!empty($cotizacion->piezas_detalle))
  <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 10px;">Piezas / componentes</p>
  <table class="tabla-piezas" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
    <thead>
      <tr>
        <th align="left" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Pieza</th>
        <th align="center" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Cant.</th>
        <th align="right" style="font-size:11px; color:#9ca3af; font-weight:600; padding:6px 0; border-bottom:1px solid #f3f4f6;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cotizacion->piezas_detalle as $p)
      <tr>
        <td style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">{{ $p['nombre'] }}</td>
        <td align="center" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">{{ $p['cantidad'] }}</td>
        <td align="right" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">${{ number_format($p['subtotal'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:22px 0 10px;">Resumen de costos</p>
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
    <tbody>
      @if($cotizacion->costo_mano_obra > 0)
      <tr>
        <td style="font-size:13px; color:#374151; padding:8px 0; border-bottom:1px solid #f9fafb;">Mano de obra</td>
        <td align="right" style="font-size:13px; color:#374151; padding:8px 0; border-bottom:1px solid #f9fafb;">${{ number_format($cotizacion->costo_mano_obra, 2) }}</td>
      </tr>
      @endif
      @if($cotizacion->costo_piezas > 0)
      <tr>
        <td style="font-size:13px; color:#374151; padding:8px 0; border-bottom:1px solid #f9fafb;">Piezas</td>
        <td align="right" style="font-size:13px; color:#374151; padding:8px 0; border-bottom:1px solid #f9fafb;">${{ number_format($cotizacion->costo_piezas, 2) }}</td>
      </tr>
      @endif
      <tr>
        <td style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">Total estimado</td>
        <td align="right" style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">${{ number_format($cotizacion->costo_total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <p style="font-size:14px; line-height:1.7; margin:24px 0 0; color:#374151;">
    ¿Deseas que procedamos con la reparación?
  </p>

  <!-- Botones -->
  <table cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 4px;">
    <tr>
      <td class="btn-cell" style="padding:0 12px 0 0;">
        <a href="{{ $urlResponder }}?r=1" class="btn-link" target="_blank"
           style="display:inline-block; background:#111827; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; padding:13px 26px; border-radius:10px; text-align:center;">
          Sí, proceder
        </a>
      </td>
      <td class="btn-cell">
        <a href="{{ $urlResponder }}?r=0" class="btn-link" target="_blank"
           style="display:inline-block; background:#ffffff; color:#374151; font-size:14px; font-weight:600; text-decoration:none; padding:12px 25px; border-radius:10px; border:1px solid #d1d5db; text-align:center;">
          No por ahora
        </a>
      </td>
    </tr>
  </table>

  <!-- Nota de expiración -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;">
    <tr>
      <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
        <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
          Este enlace expira el <strong style="color:#6b7280;">{{ $cotizacion->expires_at?->setTimezone('America/Mexico_City')->locale('es')->translatedFormat('d \d\e F \d\e Y, H:i') }} hrs.</strong>
        </p>
      </td>
    </tr>
  </table>

@endsection
