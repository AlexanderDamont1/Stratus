<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
  <title>ArrowK · @yield('titulo', 'Notificación')</title>

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
    h1,
    h2 {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      font-family: Arial, Helvetica, sans-serif !important;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #f4f6f9;
      color: #374151;
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
        padding: 28px 24px !important;
      }

      .bd-padding {
        padding: 24px 24px 12px !important;
      }

      .ft-padding {
        padding: 16px 24px !important;
      }

      h1 {
        font-size: 19px !important;
      }

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
      table.tabla-piezas td:nth-child(2) {
        display: none !important;
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
        <table width="580" cellpadding="0" cellspacing="0" border="0" align="center" style="width:580px;">
        <tr><td>
        <![endif]-->

        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"
          style="max-width:580px; width:100%; background:#ffffff; border-radius:16px; border-collapse:separate; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.06); border:1px solid #f3f4f6;">

          <!-- HEADER: fondo oscuro, solo el logo -->
          <tr>
            <td class="header-padding" align="center" bgcolor="#0A0B0B"
              style="background-color:#0A0B0B; padding:32px 24px;">
              <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('arrowk/favicon-arrowk-white.png'))) }}"
                alt="ArrowK" width="150"
                style="display:block; border:0; outline:none; text-decoration:none; margin:0 auto;">
            </td>
          </tr>

          <!-- CUERPO: título del correo + contenido específico de cada uno -->
          <tr>
            <td class="bd-padding" style="padding:32px 32px 12px; font-family:Arial, Helvetica, sans-serif;">
              <h1 style="margin:0 0 20px; font-family:Arial, Helvetica, sans-serif; font-size:22px; font-weight:bold; color:#111827; letter-spacing:-0.01em;">
                @yield('titulo', 'Notificación')
              </h1>
              @yield('contenido')
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="ft-padding" align="center"
              style="padding:22px 32px; border-top:1px solid #f0f0f0; font-family:Arial, Helvetica, sans-serif;">
              <p style="font-size:11.5px; color:#9a9a9a; margin:0;">
                ArrowK Enterprice &sim; {{ date('Y') }}
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
