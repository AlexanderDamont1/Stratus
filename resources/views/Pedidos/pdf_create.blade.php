<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formulario de Emisión de Fábrica - {{ $pedido->id_pedido }}</title>
    <style>
        body {
            font-family: Arial;
            font-size: 11px;
            font-weight: bold;
            font-style: italic;
            margin: 0;
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .header-table th,
        .header-table td {
            
            padding: 6px;
        }

        .title {
            font-weight: bold;
            font-size: 20px;
            font-style: italic;
            text-align: center;
            padding: 8px 0;
        }

        .emision {
            font-weight: bold;
            font-style: italic;
            text-align: center;
        }

        .efirma {
            font-style: italic;
            text-align: center;
            height: 60px;
            vertical-align: top;
        }

        .small {
            font-size: 10px;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            font-family: Arial, sans-serif;
            border-top: 1px solid #e8e8e8;
            font-size: 8.5pt;
            color: #777;
            text-align: center;
            font-weight: bold;
            font-style: italic;
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <table class="table header-table">
        <tr>
            <td colspan="7" style="text-align:center;">
                <img src="{{ public_path('logo.jpg') }}" style="height:50px;" alt="Logo">
            </td>
        </tr>
        <tr>
            <td colspan="7" class="title">Formulario de Emisión de Fábrica</td>
        </tr>
        <tr>
           
            <td style="width:8%; text-align:center;">
                <strong>Fecha:</strong><br>
                <span style="font-size: 8px;">{{ now()->format('d/m/Y') }}</span>
            </td>
            <td style="width:16%; text-align:center;">
                <strong>Código:</strong><br>/
            </td>
            <td style="width:26%; text-align:center;">
                <strong>Cliente:</strong><br>{{ $cliente }}
            </td>
            <td style="width:22%; text-align:center;">
                <strong>Distancia:</strong><br>{{ $distancia }}
            </td>
            <td style="width:12%; text-align:center;">
                <strong>Transporte:</strong><br>{{ $transporte }}
            </td>
            <td style="width:16%; text-align:center;">
                <strong>Costo Envío:</strong><br>{{ $costo_envio }}
            </td>
        </tr>
    </table>

    @php
    
    $modelGroups = [];

    foreach ($pedido->items as $item) {
    $m = optional($item->modelo)->nombre_modelo ?? 'N/D';
    $c = optional($item->color)->color ?? 'N/D';

    $modelGroups[$m]['colores'][$c][] = $item;
    }
    $bicGroups = [];
    foreach ($pedido->bicicletas as $bic) {
    $m = optional($bic->modelo)->nombre_modelo ?? 'N/D';
    $c = optional($bic->color)->color ?? 'N/D';
    $bicGroups[$m][$c][] = $bic->num_serie;
    }

    // Construir lista plana de filas
    $filas = [];
    foreach ($modelGroups as $modelName => $modelGroup) {
    foreach ($modelGroup['colores'] as $colorName => $items) {
    $cantidad = collect($items)->sum('cantidad');
    $numSeries = $bicGroups[$modelName][$colorName] ?? [];
    $totalFilas = max(1, $cantidad);

    for ($i = 0; $i < $totalFilas; $i++) {
        $filas[]=[ 'modelo'=> $modelName,
        'color' => $colorName,
        'cantidad' => $cantidad,
        'serie' => $numSeries[$i] ?? '',
        'lote'     => $lotes[count($filas)] ?? '',
        ];
        }
        }
        }
        

        
        $n = count($filas);
        $modeloRowspan = array_fill(0, $n, 0);
        $colorRowspan = array_fill(0, $n, 0);
        $skipModelo = array_fill(0, $n, false);
        $skipColor = array_fill(0, $n, false);

        $i = 0;
        while ($i < $n) {
            // Calcular rowspan de modelo desde $i
            $j=$i;
            while ($j < $n && $filas[$j]['modelo']===$filas[$i]['modelo']) {
            $j++;
            }
            $modeloRowspan[$i]=$j - $i;

            // Dentro del bloque de mismo modelo, calcular rowspan de color
            $k=$i;
            while ($k < $j) {
            $l=$k;
            while ($l < $j && $filas[$l]['color']===$filas[$k]['color']) {
            $l++;
            }
            $colorRowspan[$k]=$l - $k;
            // Marcar las filas siguientes del mismo color para no imprimir celda
            for ($m2=$k + 1; $m2 < $l; $m2++) {
            $skipColor[$m2]=true;
            }
            $k=$l;
            }

            // Marcar filas siguientes del mismo modelo para no imprimir celda modelo
            for ($m2=$i + 1; $m2 < $j; $m2++) {
            $skipModelo[$m2]=true;
            }

            $i=$j;
            }
            @endphp

            <!-- Tabla de ítems -->
            <!-- Tabla de ítems -->
<table class="table header-table">
    <thead>
        <tr>
            <th class="small center" style="width:8%;">No.</th>
            <th class="small center" style="width:15%;">Modelo</th>
            <th class="small center" style="width:16%;">Color</th>
            <th class="small center" style="width:11%;">Cantidad</th>
            <th class="small center" style="width:22%;">No. Serie</th>
            <th class="small center" style="width:12%;">No. Motor</th>
            <th class="small center" style="width:16%;">Lote de Bateria</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($filas as $idx => $fila)
        <tr>
            <td class="center">{{ $idx + 1 }}</td>

            {{-- Modelo: solo si no está siendo absorbido por un rowspan anterior --}}
            @if (!$skipModelo[$idx])
            <td class="center" rowspan="{{ $modeloRowspan[$idx] }}">
                {{ $fila['modelo'] }}
            </td>
            @endif

            {{-- Color y Cantidad: solo si mismo modelo y no absorbido --}}
            @if (!$skipColor[$idx])
            <td class="center" rowspan="{{ $colorRowspan[$idx] }}">
                {{ $fila['color'] }}
            </td>
            <td class="center" rowspan="{{ $colorRowspan[$idx] }}">
                {{ $fila['cantidad'] }}
            </td>
            @endif

            <td class="center">{{ $fila['serie'] }}</td>
            <td class="center"></td>
            <td class="center">{{ $fila['lote'] }}</td>
        </tr>
        @endforeach

        {{-- ── Filas de Cargadores (sin numeración) ── --}}
        @foreach ($cargadores as $spec => $qty)
        <tr>
            <td class="center"></td> {{-- Celda de número vacía --}}
            <td class="center">Cargadores</td>
            <td class="center">{{ $spec }}</td>
            <td class="center">{{ $qty }}</td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
        </tr>
        @endforeach

        {{-- ── Filas de Baterías (sin numeración) ── --}}
        @foreach ($baterias as $spec => $qty)
        <tr>
            <td class="center"></td> {{-- Celda de número vacía --}}
            <td class="center">Baterías</td>
            <td class="center">{{ $spec }}</td>
            <td class="center">{{ $qty }}</td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
        </tr>
        @endforeach
    </tbody>
</table>
          

          
            <!-- Firmas y footer - Versión más compacta -->
            <table class="table header-table">
                <tr>
                    <td style="width:72%; height:60px; font-size:9px; padding:3px; font-style:italic; text-align:center; font-weight:bold; line-height:1.2;">
                        Este pedido es por duplicado, uno se enviará al destino con la mercancía, otro se guardará en fábrica y el archivo electrónico se enviará al departamento comercial.
                    </td>
                    <td rowspan="2" style="width:28%; vertical-align:top; font-size:10px; padding:3px; font-style:italic; text-align:center; font-weight:bold;">
                        Sello o firma del responsable de fábrica:
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px; font-size:10px; height:15px; line-height:1;">
                        Firma del inspector de calidad:
                    </td>
                </tr>
            </table>

            <table class="table header-table">
                <tr>
                    <td style="width:40%; padding:5px;">Firma del chofer:<br></td>
                    <td style="width:60%; padding:5px;">Teléfono chofer:<br></td>
                </tr>
            </table>

            <table class="table header-table">
                <tr>
                    <td class="emision">Recibo de Emisión</td>
                </tr>
            </table>

            <table style="width:100%; border-collapse:collapse; border-left:1px solid black; border-right:1px solid black;">
                <tr style="border-bottom:1px solid black;">
                    <td style="width:33%; padding:5px;">Verificación de orden de emisión</td>
                    <td style="width:33%; padding:5px;">Verificado</td>
                    <td style="width:33%; padding:5px;">Error de verificarlo</td>
                </tr>
            </table>

            <table class="table header-table">
                <tr>
                    <td class="efirma">Firma del responsable de la tienda (el recibo se recibirá tras confirmar el pedido):</td>
                </tr>
            </table>

            <table style="width:100%; border-collapse:collapse; border:none;">
                <tr>
                    <td style="border:1px solid #000; padding:5px; height:50px; font-size:11px; vertical-align:top;">Observación:</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:5px; font-size:9px;">
                        Para cualquier aclaración o informe de daños comuníquese al siguiente número &nbsp; 56 7716 5697
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:8px;  color:red; font-size:9px; line-height:1.4; vertical-align:top;">
                        El pedido deberá ser supervisado por el cliente, una vez firmado este documento la empresa no se hace responsable de cualquier daño o pérdida que pueda ocurrir durante el transporte o después de la entrega.
                    </td>
                </tr>
            </table>


            <div class="footer" style="display:flex; align-items:center; height:22px; position:relative;">
                 <span style="font-size: 8px;">Powered By: CloudLabs</span>
                
            </div>

</body>

</html>