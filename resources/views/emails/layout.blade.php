<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
  <title>ArrowX · @yield('titulo', 'Notificación')</title>

  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <style>
    .ExternalClass,
    .ReadMsgBody {
      width: 100%;
      background-color: #f4f6f9;
    }

    body,
    table,
    td,
    p,
    a,
    div,
    span,
    h1 {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      font-family: 'Figtree', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #f4f6f9;
      line-height: 1.6;
    }

    .yshortcuts a {
      border-bottom: none !important;
    }

    @media screen and (max-width: 560px) {
      .wrap-padding {
        padding: 24px 16px !important;
      }

      .header-padding {
        padding: 20px 24px 12px !important;
      }

      .body-padding {
        padding: 24px 24px 12px !important;
      }

      .footer-padding {
        padding: 24px 24px 28px !important;
      }

      h1 {
        font-size: 20px !important;
      }

      .stack-row td {
        display: block !important;
        width: 100% !important;
        text-align: left !important;
        padding: 4px 0 !important;
        border-bottom: none !important;
      }

      .stack-row td:last-child {
        padding-top: 0 !important;
        padding-bottom: 14px !important;
        font-weight: 600 !important;
        text-align: left !important;
      }

      .btn-table,
      .btn-link {
        width: 100% !important;
        display: block !important;
        text-align: center !important;
        box-sizing: border-box !important;
      }

      .btn-link {
        padding: 14px 20px !important;
        font-size: 15px !important;
      }
    }

    @media screen and (max-width: 400px) {
      .body-padding {
        padding: 16px 16px 8px !important;
      }

      .header-padding {
        padding: 14px 16px 8px !important;
      }

      .footer-padding {
        padding: 18px 16px 24px !important;
      }
    }

    @yield('estilos')
  </style>
</head>

<body style="margin:0; padding:0; background:#f4f6f9;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#f4f6f9"
    style="background-color:#f4f6f9; width:100%;">
    <tr>
      <td align="center" class="wrap-padding" style="padding:40px 20px;">

        <!--[if mso]>
        <table width="560" cellpadding="0" cellspacing="0" border="0" align="center" style="width:560px;">
        <tr><td>
        <![endif]-->

        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
          style="max-width:560px; width:100%; background:#ffffff; border-radius:16px; border-collapse:separate; box-shadow:0 4px 24px rgba(0,0,0,0.04); border:1px solid #ececec;">

          <!-- Línea de acento -->
          <tr>
            <td style="height:4px; background:#1a1a1a; border-radius:16px 16px 0 0; font-size:0; line-height:0;">&nbsp;
            </td>
          </tr>

          <!-- HEADER: logo PNG (los SVG no se renderizan en Gmail/Outlook) -->
          <tr>
            <td class="header-padding" style="padding:24px 32px 8px;">
              <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding-right:8px; vertical-align:middle;">
                    <img src="{{ asset('arrowk/favicon-arrowk.png') }}" alt="ArrowX" width="22" height="22"
                      style="display:block; border:0; outline:none; text-decoration:none;">
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CUERPO: contenido específico de cada correo -->
          <tr>
            <td class="body-padding" style="padding:28px 32px 12px;">
              @yield('contenido')
            </td>
          </tr>

          <!-- FOOTER: logo PNG + redes + links + copyright -->
          <tr>
            <td class="footer-padding" style="padding:28px 32px 32px; border-top:1px solid #f0f0f0;" align="center">

              <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 18px;">
                <tr>
                  <td style="padding:0 8px;">
                    <a href="#"
                      style="display:inline-block; width:40px; height:40px; border-radius:50%; background:#f3f4f6; text-align:center; line-height:40px; text-decoration:none;">
                      <img src="https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/instagram.svg" width="20"
                        height="20" alt="Instagram"
                        style="display:inline-block; vertical-align:middle; margin-top:0px;">
                    </a>
                  </td>
                  <td style="padding:0 8px;">
                    <a href="#"
                      style="display:inline-block; width:40px; height:40px; border-radius:50%; background:#f3f4f6; text-align:center; line-height:40px; text-decoration:none;">
                      <img src="https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/facebook.svg" width="20" height="20"
                        alt="Facebook" style="display:inline-block; vertical-align:middle; margin-top:0px;">
                    </a>
                  </td>
                  <td style="padding:0 8px;">
                    <a href="#"
                      style="display:inline-block; width:40px; height:40px; border-radius:50%; background:#f3f4f6; text-align:center; line-height:40px; text-decoration:none;">
                      <img src="https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/whatsapp.svg" width="20" height="20"
                        alt="WhatsApp" style="display:inline-block; vertical-align:middle; margin-top:0px;">
                    </a>
                  </td>
                  <td style="padding:0 8px;">
                    <a href="#"
                      style="display:inline-block; width:40px; height:40px; border-radius:50%; background:#f3f4f6; text-align:center; line-height:40px; text-decoration:none;">
                      <img src="https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/gmail.svg" width="20" height="20"
                        alt="Gmail" style="display:inline-block; vertical-align:middle; margin-top:0px;">
                    </a>
                  </td>
                </tr>
              </table>

              <p style="font-size:12px; color:#9a9a9a; margin:0 0 12px;">
                <a href="#" style="color:#9a9a9a; text-decoration:none;">Centro de ayuda</a>&nbsp;&middot;&nbsp;
                <a href="#" style="color:#9a9a9a; text-decoration:none;">Privacidad</a>&nbsp;&middot;&nbsp;
                <a href="#" style="color:#9a9a9a; text-decoration:none;">Términos</a>
              </p>

              <p style="font-size:11.5px; line-height:1.7; color:#b0b0b0; margin:0;">
                © {{ date('Y') }} CloudLabs. Todos los derechos reservados.
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