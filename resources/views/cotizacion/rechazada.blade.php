{{-- resources/views/cotizacion/rechazada.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta registrada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; border: 1px solid #e4e4e7; max-width: 420px; width: 100%; padding: 40px 32px; text-align: center; }
        .icon { width: 60px; height: 60px; background: #fafafa; border-radius: 50%; font-size: 28px; line-height: 60px; margin: 0 auto 20px; border: 1.5px solid #e4e4e7; }
        h2 { font-size: 18px; color: #18181b; margin-bottom: 8px; }
        p  { font-size: 14px; color: #71717a; line-height: 1.65; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">👍</div>
    <h2>Respuesta registrada</h2>
    @if($cotizacion->mantenimiento->tipo === 'mantenimiento')
        <p>Entendido. Realizaremos únicamente el mantenimiento base. Nos comunicaremos contigo cuando esté listo.</p>
    @else
        <p>
            Hemos registrado tu respuesta. Un miembro de nuestro equipo se comunicará contigo
            para ver si hay alguna alternativa que se ajuste mejor a tus necesidades.
        </p>
    @endif
</div>
</body>
</html>