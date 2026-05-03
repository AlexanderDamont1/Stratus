<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenido a ArrowX</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f0f0f; color: #e2e8f0; }
    .wrapper { max-width: 600px; margin: 40px auto; background: #1a1a2e; border-radius: 16px; overflow: hidden; border: 1px solid #2d2d4e; }
    .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%); padding: 48px 40px; text-align: center; }
    .logo { font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
    .logo span { color: #a5f3fc; }
    .header-sub { color: rgba(255,255,255,0.85); margin-top: 8px; font-size: 15px; }
    .body { padding: 40px; }
    .greeting { font-size: 22px; font-weight: 700; color: #f1f5f9; margin-bottom: 16px; }
    .text { color: #94a3b8; font-size: 15px; line-height: 1.7; margin-bottom: 24px; }
    .highlight { color: #a5b4fc; font-weight: 600; }
    .card { background: #0f172a; border: 1px solid #2d2d4e; border-radius: 12px; padding: 24px; margin: 28px 0; }
    .card-title { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 16px; }
    .card-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #1e293b; }
    .card-row:last-child { border-bottom: none; }
    .card-label { color: #64748b; font-size: 14px; }
    .card-value { color: #e2e8f0; font-size: 14px; font-weight: 600; }
    .badge { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
    .cta { text-align: center; margin: 32px 0; }
    .btn { display: inline-block; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff !important; text-decoration: none; padding: 14px 36px; border-radius: 50px; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }
    .features { display: grid; gap: 12px; margin: 28px 0; }
    .feature { display: flex; align-items: flex-start; gap: 12px; }
    .feature-icon { width: 36px; height: 36px; background: #1e293b; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; line-height: 36px; text-align: center; }
    .feature-text { flex: 1; }
    .feature-title { color: #f1f5f9; font-size: 14px; font-weight: 600; margin-bottom: 3px; }
    .feature-desc { color: #64748b; font-size: 13px; }
    .divider { height: 1px; background: #1e293b; margin: 28px 0; }
    .footer { background: #0f0f0f; padding: 28px 40px; text-align: center; }
    .footer-text { color: #475569; font-size: 13px; line-height: 1.6; }
    .footer-link { color: #6366f1; text-decoration: none; }
    @media (max-width: 600px) {
      .header { padding: 36px 24px; }
      .body { padding: 28px 24px; }
      .footer { padding: 24px; }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <div class="logo">Arrow<span>X</span></div>
      <div class="header-sub">Sistema de gestión para bicicletas eléctricas</div>
    </div>

    <div class="body">
      <div class="greeting">¡Bienvenido, {{ $nombre }}! 🎉</div>
      <p class="text">
        Tu cuenta en <span class="highlight">ArrowX</span> ha sido verificada exitosamente.
        Estamos muy contentos de tenerte a bordo. Tu negocio
        <span class="highlight">{{ $nombreNegocio }}</span> ya está listo para despegar.
      </p>

      <div class="card">
        <div class="card-title">Resumen de tu cuenta</div>
        <div class="card-row">
          <span class="card-label">Negocio</span>
          <span class="card-value">{{ $nombreNegocio }}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Plan</span>
          <span class="card-value"><span class="badge">Trial gratuito</span></span>
        </div>
        @if($trialFecha)
        <div class="card-row">
          <span class="card-label">Trial válido hasta</span>
          <span class="card-value">{{ $trialFecha }}</span>
        </div>
        @endif
      </div>

      <div class="features">
        <div class="feature">
          <div class="feature-icon">📦</div>
          <div class="feature-text">
            <div class="feature-title">Inventario en tiempo real</div>
            <div class="feature-desc">Controla el stock de cada sucursal al instante con caché inteligente.</div>
          </div>
        </div>
        <div class="feature">
          <div class="feature-icon">⚡</div>
          <div class="feature-text">
            <div class="feature-title">Gestión multi-sucursal</div>
            <div class="feature-desc">Administra varias tiendas desde un solo panel centralizado.</div>
          </div>
        </div>
        <div class="feature">
          <div class="feature-icon">🔔</div>
          <div class="feature-text">
            <div class="feature-title">Alertas y notificaciones</div>
            <div class="feature-desc">Recibe avisos de ventas, robos reportados y movimientos clave.</div>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <p class="text">
        Si tienes dudas o necesitas ayuda para configurar tu cuenta, escríbenos.
        Estaremos encantados de acompañarte en cada paso.
      </p>

      <div class="cta">
        <a href="{{ route('dashboard') }}" class="btn">Ir a mi panel →</a>
      </div>
    </div>

    <div class="footer">
      <p class="footer-text">
        © {{ date('Y') }} ArrowX · Sistema de gestión para e-bikes<br>
        Recibiste este correo porque registraste una cuenta en nuestra plataforma.<br>
        <a href="#" class="footer-link">Política de privacidad</a>
      </p>
    </div>
  </div>
</body>
</html>