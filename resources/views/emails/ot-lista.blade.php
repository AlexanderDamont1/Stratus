{{-- resources/views/emails/ot-lista.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tu bicicleta está lista</title>
<style>
  body { margin: 0; padding: 0; background: #f4f4f0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
  .wrap { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header { background: #111; padding: 32px 36px 28px; }
  .header h1 { margin: 0; color: #fff; font-size: 20px; font-weight: 600; letter-spacing: -.3px; }
  .header p { margin: 6px 0 0; color: #888; font-size: 13px; }
  .body { padding: 32px 36px; }
  .greeting { font-size: 15px; color: #222; font-weight: 600; margin-bottom: 8px; }
  .text { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 24px; }
  .card { background: #f8f8f5; border: 1px solid #e8e8e4; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px; }
  .card-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
  .card-row:last-child { margin-bottom: 0; }
  .card-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: .5px; }
  .card-value { font-size: 13px; color: #222; font-weight: 600; font-family: 'Courier New', monospace; }
  .badge { display: inline-block; background: #111; color: #fff; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; margin-bottom: 20px; }
  .footer { padding: 20px 36px 28px; border-top: 1px solid #f0f0ec; }
  .footer p { font-size: 12px; color: #aaa; margin: 0; line-height: 1.6; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>{{ $nombreNegocio }}</h1>
    <p>Sistema de reparaciones</p>
  </div>
  <div class="body">
    <span class="badge">Lista para recoger ✓</span>
    <p class="greeting">Hola {{ $nombreCliente }},</p>
    <p class="text">
      Tu bicicleta ya está lista. Puedes pasar a recogerla a la sucursal
      <strong>{{ $nombreSucursal }}</strong> en el horario de atención.
    </p>
    <div class="card">
      <div class="card-row">
        <span class="card-label">Orden</span>
        <span class="card-value">{{ $idOt }}</span>
      </div>
      <div class="card-row">
        <span class="card-label">Unidad</span>
        <span class="card-value">{{ $numSerie }}</span>
      </div>
      <div class="card-row">
        <span class="card-label">Sucursal</span>
        <span class="card-value" style="font-family:inherit">{{ $nombreSucursal }}</span>
      </div>
    </div>
    <p class="text" style="margin-bottom:0">
      Si tienes alguna duda, comunícate directamente con nosotros.
      <br>¡Te esperamos!
    </p>
  </div>
  <div class="footer">
    <p>Este correo fue enviado automáticamente por {{ $nombreNegocio }}.<br>
    Por favor no respondas a este mensaje.</p>
  </div>
</div>
</body>
</html>