<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Nuevo contacto — ArrowK</title>
  <!--[if mso]>
  <noscript>
    <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
  </noscript>
  <![endif]-->
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      background-color: #07080d;
      font-family: -apple-system, 'Helvetica Neue', 'Segoe UI', Roboto, Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      color: #e8eaf2;
    }
    a {
      color: #818cf8;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    a:hover {
      text-decoration: underline;
      color: #a5b4fc;
    }
    @keyframes fadeSlideUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes glowPulse {
      0% {
        box-shadow: 0 0 0 0 rgba(79, 110, 247, 0.3);
      }
      70% {
        box-shadow: 0 0 0 8px rgba(79, 110, 247, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(79, 110, 247, 0);
      }
    }
    @keyframes shimmer {
      0% {
        background-position: -200% 0;
      }
      100% {
        background-position: 200% 0;
      }
    }
    .animated-card {
      animation: fadeSlideUp 0.5s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }
    .glow-btn {
      animation: glowPulse 2.2s infinite;
      transition: all 0.2s ease;
    }
    .glow-btn:hover {
      transform: translateY(-2px);
      background: #6a7ef0;
      box-shadow: 0 8px 20px rgba(79, 110, 247, 0.4);
      animation: none;
    }
    .shimmer-text {
      background: linear-gradient(120deg, #e8eaf2 0%, #a5b4fc 40%, #e8eaf2 70%);
      background-size: 200% auto;
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      animation: shimmer 5s linear infinite;
    }
    @media only screen and (max-width: 600px) {
      .email-wrapper {
        padding: 16px !important;
      }
      .email-card {
        padding: 28px 20px !important;
        border-radius: 20px !important;
      }
      .field-row {
        display: block !important;
      }
      .field-col {
        width: 100% !important;
        margin-bottom: 12px;
        padding-right: 0 !important;
      }
      .resp-stack {
        display: block;
        width: 100%;
      }
      .btn-responsive {
        width: 100%;
        text-align: center;
      }
      h1 {
        font-size: 26px !important;
      }
    }
    .hover-lift {
      transition: transform 0.25s ease, border-color 0.2s;
    }
    .hover-lift:hover {
      transform: translateY(-3px);
      border-color: rgba(129, 140, 248, 0.4) !important;
      background: rgba(255, 255, 255, 0.06) !important;
    }
  </style>
</head>
<body style="background-color:#07080d; margin:0; padding:0;">

  <!-- Preheader elegante (invisible, mejora preview) -->
  <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#07080d;opacity:0;">
    ⚡ {{ $data['name'] }} quiere conectar con ArrowK — innovación y velocidad en cada detalle.
  </div>

  <!-- Wrapper principal con gradiente de fondo ambientado -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#07080d; min-height:100vh;">
    <tr>
      <td align="center" class="email-wrapper" style="padding:48px 20px;">

        <!-- Contenedor con brillo animado en borde (efecto "mamalon") -->
        <div style="max-width:620px; margin:0 auto; position:relative;">
          <!-- glow envolvente sutil -->
          <div style="position:absolute; inset:-2px; background:radial-gradient(circle at 30% 20%, rgba(79,110,247,0.2), transparent 80%); border-radius:32px; filter:blur(12px); pointer-events:none;"></div>
          
          <!-- CARD PRINCIPAL con animación de entrada -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0" class="animated-card"
            style="max-width:620px; background:#0c0d14; border:1px solid rgba(79,110,247,0.25); border-radius:32px; overflow:hidden; backdrop-filter:blur(0px); box-shadow:0 25px 40px -12px rgba(0,0,0,0.5);">

            <!-- Top gradient neon accent (vibra) -->
            <tr>
              <td style="height:4px; background:linear-gradient(90deg, #4f6ef7, #818cf8, #c084fc, #4f6ef7); background-size:200% 100%; animation:shimmer 3s linear infinite; font-size:0;">&nbsp;</td>
            </tr>

            <!-- HEADER con logo SVG + iconos animados -->
            <tr>
              <td style="padding:32px 40px 20px;" class="email-card">
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tr>
                    <td style="vertical-align:middle;">
                      <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <!-- SVG logo animado tipo flecha futurista -->
                        <div style="background:#0c0d14; border-radius:14px; display:inline-flex; align-items:center; justify-content:center;">
                          <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="44" height="44" rx="12" fill="#1e1b3b" stroke="url(#gradBorder)" stroke-width="1.2"/>
                            <defs>
                              <linearGradient id="gradBorder" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#4f6ef7"/>
                                <stop offset="100%" stop-color="#c084fc"/>
                              </linearGradient>
                            </defs>
                            <path d="M24 12 L32 22 L24 32" stroke="#a5b4fc" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none">
                              <animate attributeName="stroke-dasharray" from="0 20" to="20 0" dur="1.2s" repeatCount="indefinite" begin="0s"/>
                            </path>
                            <path d="M12 22 L30 22" stroke="#818cf8" stroke-width="2.2" stroke-linecap="round">
                              <animate attributeName="stroke-dashoffset" values="5;0" dur="0.8s" repeatCount="indefinite"/>
                            </path>
                            <circle cx="32" cy="22" r="2.2" fill="#c084fc">
                              <animate attributeName="r" values="1.8;2.6;1.8" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                          </svg>
                        </div>
                        <span style="font-family:'Inter',Georgia,serif; font-size:26px; font-weight:600; background:linear-gradient(135deg,#FFFFFF,#c7d2fe); -webkit-background-clip:text; background-clip:text; color:transparent; letter-spacing:-0.3px;">ArrowK</span>
                        <span style="margin-left:auto; display:inline-block; background:rgba(79,110,247,0.2); backdrop-filter:blur(4px); padding:4px 12px; border-radius:40px; font-size:11px; font-weight:600; color:#c7d2fe; border:0.5px solid rgba(129,140,248,0.4);">⚡ ALPHA</span>
                      </div>
                    </td>
                  </tr>
                </table>

                <div style="height:1px; background:linear-gradient(90deg, rgba(79,110,247,0.2), rgba(129,140,248,0.6), rgba(79,110,247,0.2)); margin:24px 0 16px;"></div>

                <!-- Badge con icono svg y efecto -->
                <div style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15" stroke="#a5b4fc" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="9" stroke="#818cf8" stroke-width="1.5"/>
                    <path d="M12 4V2" stroke="#c084fc" stroke-width="1.8" stroke-linecap="round">
                      <animate attributeName="opacity" values="0.4;1;0.4" dur="1.4s" repeatCount="indefinite"/>
                    </path>
                  </svg>
                  <span style="display:inline-block; padding:4px 14px; background:rgba(79,110,247,0.14); border:0.5px solid rgba(129,140,248,0.4); border-radius:100px; font-size:12px; font-weight:700; color:#c7d2fe; letter-spacing:0.03em; text-transform:uppercase; backdrop-filter:blur(2px);">🔥 NUEVO LEAD · ALTA PRIORIDAD</span>
                </div>

                <!-- Título principal mamalon -->
                <h1 style="font-family:'Inter',Georgia,serif; font-size:32px; font-weight:700; color:#e8eaf2; letter-spacing:-0.02em; line-height:1.2; margin-bottom:12px;">
                  {{ $data['name'] }} <span style="color:#a5b4fc; font-weight:500;">quiere</span><br>
                  <span class="shimmer-text" style="font-style:italic;">acelerar con ArrowK</span> 🚀
                </h1>
                <p style="font-size:15px; color:#9ca3af; line-height:1.55; margin-bottom:0; border-left:2px solid #4f6ef7; padding-left:14px;">
                  Mensaje directo desde el formulario de contacto — alta probabilidad de conversión.
                </p>
              </td>
            </tr>

            <!-- INFO CARD con animaciones en hover -->
            <tr>
              <td style="padding:0 40px 28px;" class="email-card">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                  <!-- fila: nombre + email (con íconos SVG integrados) -->
                  <tr class="field-row">
                    <td width="48%" class="field-col" style="padding-right:8px; vertical-align:top;">
                      <div class="hover-lift" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:14px 18px; transition:all 0.2s;">
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 16.8 18.2 15 16 15H8C5.8 15 4 16.8 4 19V21" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="12" cy="7" r="4" stroke="#818cf8" stroke-width="1.5"/>
                            <animate attributeName="opacity" values="0.7;1;0.7" dur="2s" repeatCount="indefinite"/>
                          </svg>
                          <span style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">Nombre completo</span>
                        </div>
                        <div style="font-size:16px; color:#f0f2fa; font-weight:600;">{{ $data['name'] }}</div>
                      </div>
                    </td>
                    <td width="4%"> </td>
                    <td width="48%" class="field-col" style="vertical-align:top;">
                      <div class="hover-lift" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:14px 18px;">
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="4" width="20" height="16" rx="2" stroke="#c084fc" stroke-width="1.2"/>
                            <path d="M22 7L12 14L2 7" stroke="#c084fc" stroke-width="1.2" stroke-linejoin="round"/>
                            <animate attributeName="stroke-dasharray" values="0 15;15 0" dur="1.8s" repeatCount="indefinite"/>
                          </svg>
                          <span style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">Correo directo</span>
                        </div>
                        <div style="font-size:14px; color:#a5b4fc; font-weight:500; word-break:break-all;">{{ $data['email'] }}</div>
                      </div>
                    </td>
                  </tr>
                  <tr><td colspan="3" style="height:14px;"></td></tr>

                  <!-- Empresa + Rol con diseño top -->
                  <tr class="field-row">
                    <td width="48%" class="field-col" style="padding-right:8px;">
                      <div class="hover-lift" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:14px 18px;">
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 9L12 3L21 9L12 15L3 9Z" stroke="#818cf8" stroke-width="1.2" stroke-linejoin="round"/>
                            <path d="M5 12V18L12 22L19 18V12" stroke="#818cf8" stroke-width="1.2" stroke-linejoin="round"/>
                          </svg>
                          <span style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">Empresa</span>
                        </div>
                        <div style="font-size:15px; color:#e2e8f0; font-weight:500;">{{ $data['company'] ?? '—' }}</div>
                      </div>
                    </td>
                    <td width="4%"> </td>
                    <td width="48%" class="field-col">
                      <div class="hover-lift" style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:14px 18px;">
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6V12L16 14" stroke="#c084fc" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="12" cy="12" r="9" stroke="#c084fc" stroke-width="1.3"/>
                          </svg>
                          <span style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">Rol / Cargo</span>
                        </div>
                        <div style="font-size:14px; color:#cbd5e1;">{{ $data['role'] ?? 'No especificado' }}</div>
                      </div>
                    </td>
                  </tr>

                  @if(!empty($data['message']))
                  <tr><td colspan="3" style="height:18px;"></td></tr>
                  <tr>
                    <td colspan="3">
                      <div class="hover-lift" style="background:rgba(79,110,247,0.04); border:1px solid rgba(129,140,248,0.25); border-radius:20px; padding:20px;">
                        <div style="display:flex; gap:10px; align-items:flex-start;">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 10H21M6 19H18C19.1 19 20 18.1 20 17V7C20 5.9 19.1 5 18 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19Z" stroke="#a5b4fc" stroke-width="1.3" stroke-linejoin="round"/>
                            <path d="M8 14H16" stroke="#c084fc" stroke-width="1.5" stroke-linecap="round">
                              <animate attributeName="opacity" values="0.5;1;0.5" dur="2.2s" repeatCount="indefinite"/>
                            </path>
                          </svg>
                          <div style="flex:1">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:#818cf8; margin-bottom:8px;">📬 Mensaje personal</div>
                            <div style="font-size:15px; color:#e2e8f0; line-height:1.6; font-style:normal;">{{ $data['message'] }}</div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @endif
                </table>

                <!-- CTA botón mamalón con animación de brillo y SVG -->
                <div style="margin-top:34px; text-align:center;">
                  <a href="mailto:{{ $data['email'] }}?subject=🔥 Hablemos de ArrowK — tu mensaje nos importa"
                     class="glow-btn"
                     style="display:inline-flex; align-items:center; gap:12px; background:#4f6ef7; padding:14px 36px; border-radius:60px; color:white; font-weight:700; font-size:15px; text-decoration:none; letter-spacing:-0.2px; box-shadow:0 4px 14px rgba(79,110,247,0.4); transition:0.2s; border:1px solid rgba(255,255,255,0.2);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M22 2L11 13" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                      <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="white" stroke-width="1.5" stroke-linejoin="round" fill="rgba(255,255,255,0.1)">
                        <animate attributeName="fill-opacity" values="0.1;0.3;0.1" dur="1.5s" repeatCount="indefinite"/>
                      </path>
                    </svg>
                    Responder a {{ explode(' ', $data['name'])[0] }} ahora
                  </a>
                  <p style="font-size:12px; color:#5b5e7a; margin-top:16px;">⚡ Tiempo de respuesta promedio: 2 horas</p>
                </div>
              </td>
            </tr>

            <!-- Footer con íconos de red social / extra -->
            <tr>
              <td style="padding:20px 40px 28px; border-top:1px solid rgba(129,140,248,0.2); background:linear-gradient(180deg, rgba(12,13,20,0.9), #08090f);">
                <table width="100%">
                  <tr>
                    <td align="center">
                      <div style="display:flex; gap:28px; justify-content:center; margin-bottom:12px;">
                        <a href="https://arrowk.io" style="display:flex; align-items:center; gap:6px; color:#818cf8; font-size:12px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="currentColor" stroke-width="1.2"/>
                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.2"/>
                          </svg>
                          arrowk.io
                        </a>
                        <span style="color:#3f425a;">•</span>
                        <a href="#" style="display:flex; align-items:center; gap:6px; color:#818cf8; font-size:12px;">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="1.2"/>
                            <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="1.2"/>
                          </svg>
                          contacto@arrowk.io
                        </a>
                      </div>
                      <p style="font-size:10px; color:#4a4d6e; line-height:1.5; text-align:center; margin-top:8px;">
                        ⚡ Este mensaje fue generado automáticamente desde el formulario de contacto de alto rendimiento.
                        <br>Recibido el {{ now()->format('d/m/Y \a \l\a\s H:i') }} hrs · IP registrada para seguimiento.
                      </p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
</body>
</html>
```