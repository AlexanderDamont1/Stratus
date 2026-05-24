<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cotización adicional — Mantenimiento</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f4f5;color:#333}
  .wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)}
  .hd{background:#1e40af;padding:28px 32px}
  .hd h1{color:#fff;font-size:19px;font-weight:700}
  .hd p{color:#bfdbfe;font-size:12px;margin-top:4px}
  .bd{padding:28px 32px}
  .bd p{font-size:14px;line-height:1.65;margin-bottom:14px;color:#444}
  .label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#999;margin:22px 0 8px}
  .desc-box{background:#eff6ff;border-left:3px solid #1e40af;border-radius:0 8px 8px 0;padding:12px 16px;font-size:13px;color:#333;line-height:1.5}
  table{width:100%;border-collapse:collapse;margin-bottom:6px}
  th{font-size:11px;color:#aaa;font-weight:600;text-align:left;padding:5px 8px;border-bottom:1px solid #eee}
  td{font-size:13px;color:#555;padding:8px 8px;border-bottom:1px solid #f2f2f2}
  td.r{text-align:right}
  .total td{font-weight:700;color:#111;font-size:14px;border-top:2px solid #eee;border-bottom:none;padding-top:12px}
  .btns{display:flex;gap:12px;margin:28px 0 4px;flex-wrap:wrap}
  .btn{display:inline-block;padding:13px 26px;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;text-align:center}
  .btn-yes{background:#1e40af;color:#fff}
  .btn-no{background:#f0f0f0;color:#666}
  .note{background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:12px;color:#92400e;margin-top:16px}
  .expires{font-size:11px;color:#f59e0b;margin-top:12px}
  .ft{background:#fafafa;border-top:1px solid #eee;padding:18px 32px}
  .ft p{font-size:11px;color:#bbb;line-height:1.5}
</style>
</head>
<body>
<div class="wrap">

  <div class="hd">
    <h1>{{ $nombreNegocio }}</h1>
    <p>Mantenimiento · {{ $cotizacion->id_cotizacion }}</p>
  </div>

  <div class="bd">
    <p>Hola, <strong>{{ $nombreCliente }}</strong>.</p>
    <p>
      Hemos recibido tu vehículo para su mantenimiento. Durante la inspección previa,
      nuestro técnico detectó algunas piezas que te recomendamos reemplazar por
      componentes completamente nuevos para garantizar el mejor funcionamiento.
    </p>

    <div class="label">Observación del técnico</div>
    <div class="desc-box">{{ $cotizacion->descripcion_trabajo }}</div>

    @if(!empty($cotizacion->piezas_detalle))
    <div class="label">Piezas sugeridas</div>
    <table>
      <thead>
        <tr><th>Pieza</th><th style="text-align:center">Cant.</th><th style="text-align:right">Subtotal</th></tr>
      </thead>
      <tbody>
        @foreach($cotizacion->piezas_detalle as $p)
        <tr>
          <td>{{ $p['nombre'] }}</td>
          <td style="text-align:center">{{ $p['cantidad'] }}</td>
          <td class="r">${{ number_format($p['subtotal'], 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    <div class="label">Costo adicional</div>
    <table>
      <tbody>
        @if($cotizacion->costo_mano_obra > 0)
        <tr><td>Mano de obra adicional</td><td class="r">${{ number_format($cotizacion->costo_mano_obra, 2) }}</td></tr>
        @endif
        @if($cotizacion->costo_piezas > 0)
        <tr><td>Piezas</td><td class="r">${{ number_format($cotizacion->costo_piezas, 2) }}</td></tr>
        @endif
        <tr class="total"><td>Total adicional</td><td class="r">${{ number_format($cotizacion->costo_total, 2) }}</td></tr>
      </tbody>
    </table>

    <p style="margin-top:20px">
      ¿Deseas que incluyamos el reemplazo de estas piezas en tu mantenimiento?
    </p>

    <div class="btns">
      <a href="{{ $urlResponder }}?r=1" class="btn btn-yes">✓ Sí, inclúyelo</a>
      <a href="{{ $urlResponder }}?r=0" class="btn btn-no">Solo el mantenimiento</a>
    </div>

    <div class="note">
      Tu mantenimiento se realizará de cualquier forma.
      Esta pregunta es solo sobre las piezas adicionales.
    </div>

    <p class="expires">
      ⏰ Este enlace expira el
      {{ $cotizacion->expires_at?->setTimezone('America/Mexico_City')->format('d \d\e F \d\e Y, H:i') }} hrs.
    </p>
  </div>

  <div class="ft">
    <p>Si tienes dudas, contáctanos directamente.</p>
    <p>{{ $nombreNegocio }}</p>
  </div>

</div>
</body>
</html>