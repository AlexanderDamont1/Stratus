@extends('emails.layout')

@section('titulo', 'Reporte Confirmado')

@section('contenido')

  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#dcfce7; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#166534;">Reporte confirmado</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    Hola, {{ $nombreCliente }}
  </h1>

  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Tu reporte de robo ha sido <strong style="color:#111827; font-weight:600;">confirmado</strong> exitosamente.
    Tu vehículo ha sido marcado en toda la red ArrowK. Si alguna sucursal lo detecta,
    serás notificado de inmediato.
  </p>

  <!-- Datos del vehículo -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #f0f0f0; margin-bottom:26px;">
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:14px 0 6px; font-size:13px; color:#9ca3af;">Vehículo</td>
      <td style="padding:14px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $marca }} {{ $modelo }}</td>
    </tr>
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">N° de serie</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $serie }}</td>
    </tr>
    <tr class="stack-row">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">Folio del reporte</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $folio }}</td>
    </tr>
  </table>

  <!-- Nota -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td style="padding:14px 16px; background:#fefce8; border:1px solid #fef9c3; border-radius:12px;">
        <p style="font-size:13px; color:#92400e; line-height:1.55; margin:0;">
          Guarda tu folio para cualquier aclaración.
        </p>
      </td>
    </tr>
  </table>

@endsection
