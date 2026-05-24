<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tu vehículo está listo</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f4f5;color:#333}
  .wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
  .hd{background:#065f46;padding:28px 32px}
  .hd h1{color:#fff;font-size:19px;font-weight:700}
  .hd p{color:#a7f3d0;font-size:12px;margin-top:4px}
  .bd{padding:28px 32px}
  .bd p{font-size:14px;line-height:1.65;margin-bottom:14px;color:#444}
  .check{display:flex;align-items:center;gap:14px;background:#ecfdf5;border:1px solid #6ee7b7;border-radius:12px;padding:16px 20px;margin:20px 0}
  .check-icon{font-size:30px;flex-shrink:0}
  .check-text{font-size:15px;font-weight:700;color:#065f46}
  .detail{background:#f8f8f8;border-radius:8px;padding:14px 18px;font-size:13px;color:#555;margin:0 0 18px}
  .detail span{display:block;margin:3px 0}
  .ft{background:#fafafa;border-top:1px solid #eee;padding:18px 32px}
  .ft p{font-size:11px;color:#bbb;line-height:1.5}
</style>
</head>
<body>
<div class="wrap">

  <div class="hd">
    <h1>{{ $nombreNegocio }}</h1>
    <p>Aviso de entrega · {{ $idReparacion }}</p>
  </div>

  <div class="bd">
    <p>Hola, <strong>{{ $nombreCliente }}</strong>.</p>

    <div class="check">
      <span class="check-icon">✅</span>
      <span class="check-text">¡Tu vehículo ya está listo para recoger!</span>
    </div>

    <p>
      Nos complace informarte que tu vehículo ha sido atendido exitosamente
      y está listo en nuestra sucursal. Puedes pasar a recogerlo en nuestro
      horario de atención.
    </p>

    <div class="detail">
      <span><strong>Orden:</strong> {{ $idReparacion }}</span>
      @if($numSerie !== '—')
      <span><strong>Vehículo:</strong> {{ $numSerie }}</span>
      @endif
    </div>

    <p>
      Recuerda traer una identificación oficial al momento de recoger tu vehículo.
    </p>
  </div>

  <div class="ft">
    <p>{{ $nombreNegocio }} — Este es un mensaje automático.</p>
  </div>

</div>
</body>
</html>