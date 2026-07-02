<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
  <title>Vehículo listo — Entrega</title>

  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <style>
    .ExternalClass, .ReadMsgBody { width: 100%; background-color: #f4f6f9; }
    body, table, td, p, a, div, span, h1, h2 {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      font-family: 'Figtree', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
    }
    body { margin: 0; padding: 0; background-color: #f4f6f9; color: #374151; line-height: 1.6; }
    .yshortcuts a { border-bottom: none !important; }

    @media screen and (max-width: 560px) {
      .wrap-padding { padding: 24px 16px !important; }
      .bd-padding   { padding: 24px 24px 12px !important; }
      .ft-padding   { padding: 16px 24px !important; }
      h1            { font-size: 22px !important; }
      .btn-cell {
        display: block !important;
        width: 100% !important;
        padding: 0 0 10px !important;
      }
      .btn-link {
        width: 100% !important;
        display: block !important;
        text-align: center !important;
        box-sizing: border-box !important;
      }
      table.tabla-piezas th:nth-child(2),
      table.tabla-piezas td:nth-child(2) { display: none !important; }
    }
  </style>
</head>

<body style="margin:0; padding:0; background:#f4f6f9;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#f4f6f9" style="background-color:#f4f6f9; width:100%;">
    <tr>
      <td align="center" class="wrap-padding" style="padding:40px 20px;">

        <!--[if mso]>
        <table width="580" cellpadding="0" cellspacing="0" border="0" align="center" style="width:580px;">
        <tr><td>
        <![endif]-->

        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="max-width:580px; width:100%; background:#ffffff; border-radius:16px; border-collapse:separate; box-shadow:0 1px 3px rgba(0,0,0,0.04); border:1px solid #f3f4f6;">

          <!-- CUERPO -->
          <tr>
            <td class="bd-padding" style="padding:32px 32px 12px;">

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

              <!-- Tarjeta de estado: igual que "Observación del técnico" pero con icono de check -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:26px;">
                <tr>
                  <td style="border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="36" style="vertical-align:top;">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td width="32" height="32" align="center" valign="middle" style="background:#ecfdf5; border-radius:50%; font-size:16px;">✅</td>
                            </tr>
                          </table>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                          <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 4px;">Estado de la orden</p>
                          <p style="font-size:14px; font-weight:600; color:#065f46; line-height:1.55; margin:0;">¡Tu vehículo ya está listo para recoger!</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <p style="font-size:14px; line-height:1.7; margin:0 0 20px; color:#374151;">
                Nos complace informarte que tu vehículo ha sido atendido exitosamente
                y está listo en nuestra sucursal. Puedes pasar a recogerlo en nuestro
                horario de atención.
              </p>

              <!-- Detalles de la orden y vehículo en formato similar a la tabla de piezas -->
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
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="28" style="vertical-align:top;">
                          <span style="font-size:16px;">🪪</span>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                          <p style="font-size:13px; color:#92400e; line-height:1.55; margin:0;">
                            <strong>Importante:</strong> Recuerda traer una identificación oficial al momento de recoger tu vehículo.
                          </p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Nota de validez (similar al bloque de expiración de la primera plantilla) -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;">
                <tr>
                  <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="28" style="vertical-align:top;">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr><td width="24" height="24" align="center" valign="middle" style="background:#e0f2fe; border-radius:50%; font-size:11px;">📅</td></tr>
                          </table>
                        </td>
                        <td style="vertical-align:top; padding-left:8px;">
                          <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
                            Este aviso es válido para recoger tu vehículo dentro de los próximos días.
                            Si no puedes acudir, contáctanos para coordinar otra fecha.
                          </p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="ft-padding" style="padding:20px 32px; border-top:1px solid #f3f4f6;" align="center">
              <p style="font-size:11.5px; color:#9ca3af; line-height:1.6; margin:0;">
                Si tienes dudas, contáctanos directamente &middot; {{ $nombreNegocio }}
              </p>
            </td>
          </tr>

        </table>

        <!--[if mso]>
        </td></tr>
        </table>
        <![endif]-->

      </td>
    </tr>
  </table>

</body>
</html>