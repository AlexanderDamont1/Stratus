<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
  <title>Cotización adicional — Mantenimiento</title>

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

              <!-- Badge sky, igual estructura que 'Acceso seguro' del login -->
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
                Mantenimiento &middot; {{ $cotizacion->id_cotizacion }}
              </p>

              <p style="font-size:14px; line-height:1.7; margin:0 0 14px; color:#374151;">
                Hola, <strong style="color:#111827; font-weight:600;">{{ $nombreCliente }}</strong>.
              </p>
              <p style="font-size:14px; line-height:1.7; margin:0 0 24px; color:#374151;">
                Hemos recibido tu vehículo para su mantenimiento. Durante la inspección previa,
                nuestro técnico detectó algunas piezas que te recomendamos reemplazar por
                componentes completamente nuevos para garantizar el mejor funcionamiento.
              </p>

              <!-- Observación: tarjeta con icono circular, igual patrón que el bloque inferior del login -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:26px;">
                <tr>
                  <td style="border:1px solid #f3f4f6; border-radius:12px; padding:16px;">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="36" style="vertical-align:top;">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td width="32" height="32" align="center" valign="middle" style="background:#f3f4f6; border-radius:50%; font-size:14px;">🔧</td>
                            </tr>
                          </table>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                          <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 4px;">Observación del técnico</p>
                          <p style="font-size:13px; color:#374151; line-height:1.55; margin:0;">{{ $cotizacion->descripcion_trabajo }}</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              @if(!empty($cotizacion->piezas_detalle))
              <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:0 0 10px;">Piezas sugeridas</p>
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

              <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#9ca3af; margin:22px 0 10px;">Costo adicional</p>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
                <tbody>
                  @if($cotizacion->costo_mano_obra > 0)
                  <tr>
                    <td style="font-size:13px; color:#374151; padding:8px 0; border-bottom:1px solid #f9fafb;">Mano de obra adicional</td>
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
                    <td style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">Total adicional</td>
                    <td align="right" style="font-weight:600; color:#111827; font-size:15px; padding:14px 0 8px; border-top:1px solid #e5e7eb;">${{ number_format($cotizacion->costo_total, 2) }}</td>
                  </tr>
                </tbody>
              </table>

              <p style="font-size:14px; line-height:1.7; margin:24px 0 0; color:#374151;">
                ¿Deseas que incluyamos el reemplazo de estas piezas en tu mantenimiento?
              </p>

              <!-- Botones: primario gray-900 igual al botón del login, secundario tipo 'outline' como el botón de Google -->
              <table cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 4px;">
                <tr>
                  <td class="btn-cell" style="padding:0 12px 0 0;">
                    <a href="{{ $urlResponder }}?r=1" class="btn-link" target="_blank"
                       style="display:inline-block; background:#111827; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; padding:13px 26px; border-radius:10px; text-align:center;">
                      Sí, inclúyelo
                    </a>
                  </td>
                  <td class="btn-cell">
                    <a href="{{ $urlResponder }}?r=0" class="btn-link" target="_blank"
                       style="display:inline-block; background:#ffffff; color:#374151; font-size:14px; font-weight:600; text-decoration:none; padding:12px 25px; border-radius:10px; border:1px solid #d1d5db; text-align:center;">
                      Solo el mantenimiento
                    </a>
                  </td>
                </tr>
              </table>

              <!-- Nota: mismo patrón del footer del login (icono + texto gris) -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:22px;">
                <tr>
                  <td style="border-top:1px solid #f3f4f6; padding-top:16px;">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td width="28" style="vertical-align:top;">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr><td width="24" height="24" align="center" valign="middle" style="background:#fffbeb; border-radius:50%; font-size:11px;">⏰</td></tr>
                          </table>
                        </td>
                        <td style="vertical-align:top; padding-left:8px;">
                          <p style="font-size:12px; color:#9ca3af; line-height:1.55; margin:0;">
                            Tu mantenimiento se realizará de cualquier forma; esta pregunta es solo sobre las piezas adicionales.
                            Este enlace expira el <strong style="color:#6b7280;">{{ $cotizacion->expires_at?->setTimezone('America/Mexico_City')->locale('es')->translatedFormat('d \d\e F \d\e Y, H:i') }} hrs.</strong>
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