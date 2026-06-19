<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
    <title>ArrowX · Verificación exitosa</title>

    <!-- Fonts from Bunny Fonts (Figtree) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        /* Estilos generales para clientes modernos + media queries responsivas */
        .ExternalClass, .ReadMsgBody {
            width: 100%;
            background-color: #f6f7f9;
        }
        body, table, td, p, a, div, span, h1, h2, h3 {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Figtree', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f6f7f9;
            font-family: 'Figtree', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            line-height: 1.5;
        }
        /* Tablas responsivas */
        .yshortcuts a {
            border-bottom: none !important;
        }
        @media screen and (max-width: 560px) {
            .responsive-table {
                width: 100% !important;
            }
            .stack-cell {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                padding: 12px 16px !important;
            }
            .stack-row td {
                display: block !important;
                width: 100% !important;
                text-align: left !important;
                padding: 12px 20px !important;
                border-bottom: 1px solid #eef2f6 !important;
            }
            .stack-row:last-child td {
                border-bottom: none !important;
            }
            .card-inner .stack-row td:first-child {
                padding-bottom: 0 !important;
            }
            .card-inner .stack-row td:last-child {
                padding-top: 0 !important;
            }
            .btn-table {
                width: 100% !important;
                text-align: center !important;
            }
            .btn-link {
                width: 100% !important;
                display: block !important;
                text-align: center !important;
                box-sizing: border-box !important;
            }
            .header-padding, .body-padding, .footer-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            h1 {
                font-size: 28px !important;
                line-height: 1.2 !important;
            }
            .status-badge {
                font-size: 11px !important;
                padding: 6px 12px !important;
            }
        }
        @media only screen and (max-width: 480px) {
            .responsive-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .feature-item {
                padding: 16px 0 !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background:#f6f7f9; font-family:'Figtree','Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif !important;">

    <!-- Contenedor principal centrado / tabla envolvente 100% -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#f6f7f9" style="background-color:#f6f7f9; width:100%;">
        <tr>
            <td align="center" style="padding:40px 16px 40px 16px;">
                <!-- Tarjeta principal: ancho máximo 700px -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="max-width:700px; width:100%; background:#ffffff; border:1px solid #e9edf2; border-radius:20px; border-collapse:separate; overflow:hidden;">                    
                    <!-- CABECERA -->
                    <tr>
                        <td style="padding:36px 40px; border-bottom:1px solid #eef2f6; background:#ffffff;" class="header-padding">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size:22px; font-weight:600; letter-spacing:-0.04em; color:#020617; font-family:'Figtree','Inter',sans-serif !important;">
                                        Arrow<span style="color:#64748b; font-weight:500;">X</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CUERPO PRINCIPAL -->
                    <tr>
                        <td style="padding:48px 40px;" class="body-padding responsive-padding">
                            
                            <!-- Estado: cuenta verificada -->
                            <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:26px;">
                                <tr>
                                    <td style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:999px; padding:8px 16px 8px 14px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="font-size:16px; color:#16a34a; font-weight:700; padding-right:8px; font-family:'Figtree','Inter',sans-serif !important;">✓</td>
                                                <td style="font-size:12px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#15803d; font-family:'Figtree','Inter',sans-serif !important;">Cuenta verificada</td>
                                            </tr>
                                    </td>
                                </tr>
                            </table>

                            <!-- Título dinámico -->
                            <h1 style="font-size:34px; line-height:1.05; letter-spacing:-0.06em; font-weight:600; color:#020617; margin:0 0 24px 0; padding:0; font-family:'Figtree','Inter',sans-serif !important;">
                                Bienvenido, {{ $nombre }}
                            </h1>

                            <!-- textos descriptivos -->
                            <p style="font-size:15px; line-height:1.9; color:#475569; margin:0 0 18px 0; font-family:'Figtree','Inter',sans-serif !important;">
                                Tu empresa <strong style="color:#0f172a; font-weight:600; font-family:'Figtree','Inter',sans-serif !important;">{{ $nombreNegocio }}</strong> ha sido verificada correctamente y ya puede operar dentro de ArrowX.
                            </p>

                            <p style="font-size:15px; line-height:1.9; color:#475569; margin:0 0 28px 0; font-family:'Figtree','Inter',sans-serif !important;">
                                Nuestra plataforma está diseñada para negocios que requieren una operación moderna, estable dejando de lado los sistemas tradicionales (Excel estaticos, software de escritorio, etc.) y buscan una solución en la nube que les permita crecer sin preocuparse por limitaciones técnicas.
                            </p>

                            <!-- Tarjeta de información (datos empresa / plan) -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #eef2f6; border-radius:16px; background:#fcfcfd; margin:0 0 0 0;">
                                <tr class="stack-row" style="border-bottom:1px solid #eef2f6;">
                                    <td class="label-col" style="padding:18px 22px; font-size:14px; color:#64748b; width:40%; border-bottom:inherit; font-family:'Figtree','Inter',sans-serif !important;">
                                        Empresa
                                    </td>
                                    <td class="value-col" style="padding:18px 22px; font-size:14px; font-weight:600; color:#020617; text-align:right; border-bottom:inherit; font-family:'Figtree','Inter',sans-serif !important;">
                                        {{ $nombreNegocio }}
                                    </td>
                                </tr>
                                <tr class="stack-row" style="border-bottom:1px solid #eef2f6;">
                                    <td style="padding:18px 22px; font-size:14px; color:#64748b; font-family:'Figtree','Inter',sans-serif !important;">
                                        Plan
                                    </td>
                                    <td style="padding:18px 22px; text-align:right;">
                                        <span style="display:inline-block; padding:6px 12px; border-radius:999px; background:#f8fafc; border:1px solid #e2e8f0; color:#0f172a; font-size:12px; font-weight:600; font-family:'Figtree','Inter',sans-serif !important;">Trial gratuito</span>
                                    </td>
                                </tr>
                                @if($trialFecha)
                                <tr class="stack-row" style="border-bottom:none;">
                                    <td style="padding:18px 22px; font-size:14px; color:#64748b; font-family:'Figtree','Inter',sans-serif !important;">
                                        Válido hasta
                                    </td>
                                    <td style="padding:18px 22px; font-size:14px; font-weight:600; color:#020617; text-align:right; font-family:'Figtree','Inter',sans-serif !important;">
                                        {{ $trialFecha }}
                                    </td>
                                </tr>
                                @endif
                            </table>

                            <!-- Sección de características -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:42px; border-top:1px solid #f1f5f9; padding-top:34px;">
                                <tr>
                                    <td style="padding:0 0 16px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding-bottom:24px;">
                                                    <div style="font-size:14px; font-weight:600; color:#020617; margin-bottom:6px; letter-spacing:-0.01em; font-family:'Figtree','Inter',sans-serif !important;">Gestión centralizada</div>
                                                    <div style="font-size:14px; line-height:1.8; color:#64748b; font-family:'Figtree','Inter',sans-serif !important;">Administra inventario, sucursales y operaciones desde un único entorno.</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:24px;">
                                                    <div style="font-size:14px; font-weight:600; color:#020617; margin-bottom:6px; letter-spacing:-0.01em; font-family:'Figtree','Inter',sans-serif !important;">Información en tiempo real</div>
                                                    <div style="font-size:14px; line-height:1.8; color:#64748b; font-family:'Figtree','Inter',sans-serif !important;">Visualiza movimientos y métricas actualizadas para una mejor toma de decisiones.</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom:0;">
                                                    <div style="font-size:14px; font-weight:600; color:#020617; margin-bottom:6px; letter-spacing:-0.01em; font-family:'Figtree','Inter',sans-serif !important;">Monitoreo operativo</div>
                                                    <div style="font-size:14px; line-height:1.8; color:#64748b; font-family:'Figtree','Inter',sans-serif !important;">Mantente informado sobre eventos relevantes y actividad importante dentro de tu empresa.</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botón de acceso -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:40px;">
                                <tr>
                                    <td align="left" class="btn-table">
                                        <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block;">
                                            <tr>
                                                <td align="center" bgcolor="#020617" style="background:#020617; border-radius:12px;">
                                                    <a href="{{ route('login') }}" style="display:inline-block; background:#020617; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; padding:12px 28px; border-radius:12px; letter-spacing:-0.01em; line-height:1.4; font-family:'Figtree','Inter',sans-serif !important;" target="_blank">
                                                        Acceder al panel
                                                    </a>
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
                        <td style="padding:28px 40px 34px; border-top:1px solid #eef2f6; background:#ffffff; text-align:center;" class="footer-padding responsive-padding">
                            <p style="font-size:12px; line-height:1.8; color:#94a3b8; margin:0; font-family:'Figtree','Inter',sans-serif !important;">
                                Powered by CloudLabs · {{ date('Y') }} ©
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- fin main card -->
            </td>
        </tr>
    </table>

   
</body>
</html>