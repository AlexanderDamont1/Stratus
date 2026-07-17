@extends('emails.layout')

@section('titulo', 'Verifica tu Cuenta')

@section('contenido')

  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#e0f2fe; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#075985;">Verificación pendiente</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    Hola, {{ $nombre }}
  </h1>

  <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
    Gracias por registrarte en ArrowK. Para activar tu cuenta y comenzar a usar el sistema,
    confirma tu correo electrónico haciendo clic en el botón de abajo.
  </p>

  <!-- Botón -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 8px 0 28px;">
    <tr>
      <td align="center" class="btn-cell">
        <a href="{{ $url }}" class="btn-link" target="_blank"
           style="display:inline-block; background:#111827; color:#ffffff; font-size:15px; font-weight:600; text-decoration:none; padding:14px 40px; border-radius:10px; text-align:center; letter-spacing:0.02em;">
          Verificar cuenta
        </a>
      </td>
    </tr>
  </table>

  <!-- Nota de expiración -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:8px;">
    <tr>
      <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
        <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
          Este enlace expira en <strong style="color:#6b7280;">24 horas</strong>.
          Si no creaste esta cuenta, puedes ignorar este correo.
        </p>
      </td>
    </tr>
  </table>

@endsection
