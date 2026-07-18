<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de vehículos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: letter portrait;
            margin: 22pt;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }
        .header-left  { display: table-cell; vertical-align: bottom; }
        .header-right { display: table-cell; vertical-align: bottom; text-align: right; }

        .titulo { font-size: 14pt; font-weight: 700; color: #0f172a; }
        .subtitulo { font-size: 8pt; color: #64748b; margin-top: 2pt; }
        .meta { font-size: 7.5pt; color: #64748b; }
        .meta strong { color: #0f172a; }

        table.datos { width: 100%; border-collapse: collapse; }
        table.datos th {
            background: #f1f5f9;
            color: #475569;
            font-size: 6.8pt;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            text-align: left;
            padding: 5pt 5pt;
            border-bottom: 1px solid #cbd5e1;
        }
        table.datos td {
            padding: 5pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: top;
        }
        table.datos tr:nth-child(even) td { background: #f8fafc; }

        .serie   { font-family: 'Courier New', Courier, monospace; font-weight: 700; }
        .muted   { color: #94a3b8; }
        .vendida { color: #059669; font-weight: 700; }
        .fecha-hora { color: #94a3b8; font-size: 6.5pt; }

        .footer {
            margin-top: 14pt;
            padding-top: 6pt;
            border-top: 0.5pt solid #e2e8f0;
            font-size: 6.5pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <div class="titulo">Historial de vehículos</div>
            <div class="subtitulo">{{ $nombreNegocio }}</div>
        </div>
        <div class="header-right">
            <div class="meta">
                @if($desde && $hasta)
                    Período: <strong>del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</strong>
                @elseif($desde)
                    Período: <strong>desde el {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}</strong>
                @elseif($hasta)
                    Período: <strong>hasta el {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</strong>
                @else
                    Período: <strong>todos</strong>
                @endif
            </div>
            @if($sucursalNombre)
            <div class="meta">Sucursal: <strong>{{ $sucursalNombre }}</strong></div>
            @endif
            <div class="meta">Generado: <strong>{{ $generadoEn }}</strong></div>
            <div class="meta">Total: <strong>{{ count($items) }}</strong> vehículo{{ count($items) === 1 ? '' : 's' }}</div>
        </div>
    </div>

    <table class="datos">
        <thead>
            <tr>
                <th>N° de serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Voltaje</th>
                <th>En fábrica</th>
                <th>Ingreso a sucursal</th>
                <th>Vendida</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $bici)
                @php
                    $colorNombre = trim(explode('|', $bici->color?->color ?? '')[0] ?? '') ?: '—';
                @endphp
                <tr>
                    <td class="serie">{{ $bici->num_serie }}</td>
                    <td>{{ $bici->modelo?->marca?->nombre_marca ?? '—' }}</td>
                    <td>{{ $bici->modelo?->nombre_modelo ?? '—' }}</td>
                    <td>{{ $colorNombre }}</td>
                    <td>{{ $bici->voltaje?->voltaje ?? '—' }}</td>
                    <td>
                        {{ $bici->created_at?->format('d/m/Y') }}
                        <span class="fecha-hora">{{ $bici->created_at?->format('H:i') }}</span>
                    </td>
                    <td>
                        @if($bici->fecha_ingreso_sucursal)
                            {{ $bici->fecha_ingreso_sucursal->format('d/m/Y') }}
                            <span class="fecha-hora">{{ $bici->fecha_ingreso_sucursal->format('H:i') }}</span>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($bici->fecha_vendida)
                            <span class="vendida">{{ $bici->fecha_vendida->format('d/m/Y') }}</span>
                            <span class="fecha-hora">{{ $bici->fecha_vendida->format('H:i') }}</span>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94a3b8; padding:14pt;">
                        No hay vehículos en el período seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        ArrowK Enterprise — reporte generado automáticamente
    </div>

</body>
</html>
