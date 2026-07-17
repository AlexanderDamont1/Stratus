@extends('emails.layout')

@section('titulo', 'Vehículo Encontrado')

@section('contenido')

  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#dcfce7; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#166534;">¡Buenas noticias!</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    Hola, {{ $nombreCliente }}
  </h1>

  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Tu vehículo con reporte de robo ha sido detectado en la red ArrowK.
    Ha sido resguardado por la sucursal indicada abajo — comunícate con ellos para coordinar la recuperación.
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
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">Folio del reporte</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $folio }}</td>
    </tr>
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">Detectado en</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $sucursal }}</td>
    </tr>
    <tr class="stack-row">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">Fecha de detección</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $fecha }}</td>
    </tr>
  </table>

  @if($ubicacionUrl)
  <!-- Botón de ubicación -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 4px 0 28px;">
    <tr>
      <td align="center" class="btn-cell">
        <a href="{{ $ubicacionUrl }}" class="btn-link" target="_blank"
           style="display:inline-block; background:#111827; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; padding:13px 26px; border-radius:10px; text-align:center;">
          Ver ubicación de la sucursal
        </a>
      </td>
    </tr>
  </table>
  @endif

  <p style="font-size:13px; line-height:1.7; margin:0; color:#9ca3af;">
    Gracias por confiar en la tecnología de <strong style="color:#6b7280;">CloudLabs</strong>.
  </p>

@endsection
