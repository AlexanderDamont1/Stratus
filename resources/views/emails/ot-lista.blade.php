@extends('emails.layout')

@section('titulo', 'Vehículo Listo')

@section('contenido')

  <!-- Badge estilo "Vehículo listo" (verde) -->
  <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
    <tr>
      <td style="background:#dcfce7; border-radius:20px; padding:6px 14px;">
        <span style="font-size:11.5px; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; color:#166534;">Vehículo listo</span>
      </td>
    </tr>
  </table>

  <h1 style="font-size:24px; line-height:1.3; font-weight:500; color:#111827; margin:0 0 6px; letter-spacing:-0.01em;">
    {{ $nombreNegocio }}
  </h1>
  <p style="font-size:13px; color:#9ca3af; margin:0 0 24px;">
    Aviso de entrega &middot; {{ $idReparacion }}
  </p>

  <p style="font-size:14px; line-height:1.7; margin:0 0 14px; color:#374151;">
    Hola, <strong style="color:#111827; font-weight:600;">{{ $nombreCliente }}</strong>.
  </p>

  <!-- Tarjeta de estado -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:26px;">
    <tr>
      <td style="border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
        <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 4px;">Estado de la orden</p>
        <p style="font-size:14px; font-weight:600; color:#065f46; line-height:1.55; margin:0;">Tu vehículo ya está listo para recoger.</p>
      </td>
    </tr>
  </table>

  <p style="font-size:14px; line-height:1.7; margin:0 0 20px; color:#374151;">
    Nos complace informarte que tu vehículo ha sido atendido exitosamente
    y está listo en nuestra sucursal. Puedes pasar a recogerlo en nuestro
    horario de atención.
  </p>

  <!-- Detalles de la orden y vehículo -->
  <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 10px;">Detalles de la entrega</p>
  <table class="tabla-piezas" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
    <tbody>
      <tr>
        <td style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          <strong>Orden de servicio</strong>
        </td>
        <td align="right" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          {{ $idReparacion }}
        </td>
      </tr>
      @if($numSerie !== '—')
      <tr>
        <td style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          <strong>Vehículo</strong>
        </td>
        <td align="right" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          {{ $numSerie }}
        </td>
      </tr>
      @endif
      <tr>
        <td style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          <strong>Estado</strong>
        </td>
        <td align="right" style="font-size:13px; color:#374151; padding:10px 0; border-bottom:1px solid #f9fafb;">
          <span style="background:#dcfce7; color:#166534; padding:2px 12px; border-radius:20px; font-weight:600; font-size:12px;">Listo</span>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Nota de identificación -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 8px;">
    <tr>
      <td style="padding:14px 16px; background:#fefce8; border:1px solid #fef9c3; border-radius:12px;">
        <p style="font-size:13px; color:#92400e; line-height:1.55; margin:0;">
          <strong>Importante:</strong> Recuerda traer una identificación oficial al momento de recoger tu vehículo.
        </p>
      </td>
    </tr>
  </table>

  <!-- Nota de validez -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;">
    <tr>
      <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
        <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
          Este aviso es válido para recoger tu vehículo dentro de los próximos días.
          Si no puedes acudir, contáctanos para coordinar otra fecha.
        </p>
      </td>
    </tr>
  </table>

@endsection
