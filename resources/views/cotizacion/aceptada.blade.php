{{-- resources/views/cotizacion/aceptada.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización aceptada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; border: 1px solid #e4e4e7; max-width: 420px; width: 100%; padding: 40px 32px; text-align: center; }
        .icon { width: 60px; height: 60px; background: #f0fdf4; border-radius: 50%; font-size: 28px; line-height: 60px; margin: 0 auto 20px; }
        h2 { font-size: 18px; color: #18181b; margin-bottom: 8px; }
        p  { font-size: 14px; color: #71717a; line-height: 1.65; }
        .id { display: inline-block; margin-top: 16px; background: #f4f4f5; border-radius: 6px; padding: 6px 14px; font-size: 13px; font-family: monospace; color: #3f3f46; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">✓</div>
    <h2>¡Listo! Cotización aceptada</h2>
    <p>Hemos recibido tu respuesta. Nuestro equipo comenzará el trabajo a la brevedad.</p>
    <span class="id">{{ $cotizacion->id_cotizacion }}</span>
    <p style="margin-top:20px">Te avisaremos por correo cuando tu vehículo esté listo para recoger.</p>
</div>
</body>
</html>