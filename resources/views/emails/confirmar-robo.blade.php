@extends('emails.layout')

@section('titulo', 'Confirma tu Reporte')

@section('contenido')

  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#fee2e2; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#991b1b;">Reporte de robo</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    Hola, {{ $nombreCliente }}
  </h1>

  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Hemos recibido un reporte de robo para uno de tus vehículos registrados en ArrowK.
  </p>

  <!-- Datos del vehículo -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #f0f0f0; margin-bottom:26px;">
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:14px 0 6px; font-size:13px; color:#9ca3af;">Vehículo</td>
      <td style="padding:14px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $marca }} {{ $modelo }}</td>
    </tr>
    <tr class="stack-row">
      <td style="padding:10px 0 6px; font-size:13px; color:#9ca3af;">N° de serie</td>
      <td style="padding:10px 0 6px; font-size:13px; font-weight:600; color:#111827; text-align:right;">{{ $serie }}</td>
    </tr>
  </table>

  <p style="font-size:14px; line-height:1.7; margin:0 0 8px; color:#374151;">
    Si fuiste tú quien reportó el robo, confirma haciendo clic en el botón de abajo.
  </p>

  <!-- Botón -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 16px 0 28px;">
    <tr>
      <td align="center" class="btn-cell">
        <a href="{{ $url }}" class="btn-link" target="_blank"
           style="display:inline-block; background:#111827; color:#ffffff; font-size:15px; font-weight:600; text-decoration:none; padding:14px 40px; border-radius:10px; text-align:center; letter-spacing:0.02em;">
          Confirmar reporte de robo
        </a>
      </td>
    </tr>
  </table>

  <!-- Nota de expiración -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:8px;">
    <tr>
      <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
        <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
          El enlace expira en <strong style="color:#6b7280;">48 horas</strong>.
          Si no reconoces este reporte, ignora este correo.
        </p>
      </td>
    </tr>
  </table>

@endsection
