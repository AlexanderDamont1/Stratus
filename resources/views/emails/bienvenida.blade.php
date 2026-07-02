@extends('emails.layout')

@section('titulo', 'Cuenta verificada')

@section('contenido')

  <!-- Estado -->
  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
    <tr>
      <td style="background:#dcfce7; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#166534;">Cuenta verificada</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:28px; line-height:1.3; font-weight:600; color:#1a1a1a; margin:0 0 10px; letter-spacing:-0.02em;">
    Hola, {{ $nombre }}
  </h1>

  <p style="font-size:15px; line-height:1.7; color:#444; margin:0 0 28px;">
    <strong style="color:#1a1a1a; font-weight:600;">{{ $nombreNegocio }}</strong> ya está
    verificada y lista para operar en ArrowX.
  </p>

  <!-- Datos -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #f0f0f0; margin-bottom:30px;">
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:14px 0 6px; font-size:14px; color:#888; width:40%; vertical-align:top;">Empresa</td>
      <td style="padding:14px 0 6px; font-size:14px; font-weight:600; color:#1a1a1a; text-align:right; vertical-align:top;">{{ $nombreNegocio }}</td>
    </tr>
    <tr class="stack-row" style="border-bottom:1px solid #f0f0f0;">
      <td style="padding:8px 0 6px; font-size:14px; color:#888;">Plan</td>
      <td style="padding:8px 0 6px; text-align:right;">
        <span style="display:inline-block; background:#f3e8ff; color:#6b21a8; font-size:12.5px; font-weight:600; padding:4px 12px; border-radius:20px;">Trial gratuito</span>
      </td>
    </tr>
    @if($trialFecha)
    <tr class="stack-row">
      <td style="padding:8px 0 6px; font-size:14px; color:#888;">Válido hasta</td>
      <td style="padding:8px 0 6px; font-size:14px; font-weight:600; color:#1a1a1a; text-align:right;">{{ $trialFecha }}</td>
    </tr>
    @endif
  </table>

  <!-- Beneficios -->
  <div style="margin-bottom:8px;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
      <tr>
        <td style="width:6px; vertical-align:top; padding-top:7px;">
          <span style="display:block; width:4px; height:4px; border-radius:50%; background:#1a1a1a;"></span>
        </td>
        <td style="padding-left:12px; vertical-align:top;">
          <p style="font-size:14px; font-weight:600; color:#1a1a1a; margin:0;">Inventario y sucursales en un solo lugar.</p>
        </td>
      </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
      <tr>
        <td style="width:6px; vertical-align:top; padding-top:7px;">
          <span style="display:block; width:4px; height:4px; border-radius:50%; background:#1a1a1a;"></span>
        </td>
        <td style="padding-left:12px; vertical-align:top;">
          <p style="font-size:14px; font-weight:600; color:#1a1a1a; margin:0;">Métricas siempre actualizadas.</p>
        </td>
      </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td style="width:6px; vertical-align:top; padding-top:7px;">
          <span style="display:block; width:4px; height:4px; border-radius:50%; background:#1a1a1a;"></span>
        </td>
        <td style="padding-left:12px; vertical-align:top;">
          <p style="font-size:14px; font-weight:600; color:#1a1a1a; margin:0;">Alertas de la actividad importante.</p>
        </td>
      </tr>
    </table>

  </div>

  <!-- Botón -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 28px 0 8px;">
    <tr>
      <td align="center" class="btn-table">
        <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block;">
          <tr>
            <td align="center" bgcolor="#1a1a1a" style="background:#1a1a1a; border-radius:10px;">
              <a href="{{ route('login') }}" class="btn-link" target="_blank"
                style="display:inline-block; background:#1a1a1a; color:#ffffff; font-size:15px; font-weight:600; text-decoration:none; padding:14px 40px; border-radius:10px; line-height:1.4; letter-spacing:0.02em;">
                Acceder al panel
              </a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

@endsection