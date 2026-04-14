<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Póliza de Garantía</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #1a1a1a;
            background: #fff;
        }

        /* ─── Layout principal: izquierda | derecha ─── */
        .page {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .col-left {
            display: table-cell;
            width: 44%;
            padding: 14px 12px 14px 14px;
            border-right: 1px solid #ccc;
            vertical-align: top;
        }

        .col-right {
            display: table-cell;
            width: 56%;
            padding: 14px 14px 14px 12px;
            vertical-align: top;
        }

        /* ─── Cabecera izquierda ─── */
        .header-left {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .header-logo-cell {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }
        .header-info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: right;
        }
        .brand-name {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -1px;
            color: #111;
        }
        .brand-name span { color: #2563eb; }
        .negocio-info {
            font-size: 6.5px;
            color: #444;
            line-height: 1.5;
        }

        /* ─── Separador ─── */
        hr { border: none; border-top: 1px solid #ddd; margin: 8px 0; }
        hr.bold { border-top: 2px solid #111; }

        /* ─── Campo de datos ─── */
        .field-label {
            font-size: 6.5px;
            color: #555;
            margin-bottom: 1px;
        }
        .field-value {
            border: 1px solid #bbb;
            border-radius: 3px;
            padding: 3px 6px;
            min-height: 14px;
            font-size: 7.5px;
            color: #111;
            background: #f9f9f9;
            margin-bottom: 6px;
        }
        .field-value.lg {
            min-height: 22px;
        }

        .row-2 {
            display: table;
            width: 100%;
        }
        .cell-half {
            display: table-cell;
            width: 50%;
            padding-right: 6px;
        }
        .cell-half:last-child { padding-right: 0; }

        /* ─── Fecha ─── */
        .fecha-row {
            display: table;
            width: 50%;
            margin: 0 auto 10px auto;
            text-align: center;
        }
        .fecha-label {
            font-size: 7px;
            color: #444;
            margin-bottom: 4px;
        }
        .fecha-boxes {
            display: table;
            margin: 0 auto;
        }
        .fecha-box {
            display: table-cell;
            border: 1px solid #bbb;
            border-radius: 3px;
            padding: 3px 10px;
            font-size: 9px;
            text-align: center;
            background: #f9f9f9;
        }
        .fecha-sep {
            display: table-cell;
            padding: 0 4px;
            vertical-align: middle;
            font-size: 10px;
            color: #888;
        }

        /* ─── Tabla de series/garantía ─── */
        .garantia-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5px;
            margin-top: 8px;
        }
        .garantia-table th {
            background: #111;
            color: #fff;
            padding: 4px 5px;
            text-align: center;
            font-size: 6.5px;
        }
        .garantia-table td {
            border: 1px solid #ccc;
            padding: 4px 5px;
            vertical-align: top;
            color: #222;
            line-height: 1.35;
        }
        .garantia-table tr:nth-child(even) td { background: #f5f5f5; }
        .serie-num {
            text-align: center;
            font-weight: bold;
            width: 24px;
        }

        /* ─── Términos lado derecho ─── */
        .terminos-title {
            font-size: 7.5px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #111;
        }
        .terminos-list {
            padding-left: 0;
            list-style: none;
        }
        .terminos-list li {
            margin-bottom: 3px;
            font-size: 6.2px;
            color: #333;
            line-height: 1.4;
            padding-left: 0;
        }
        .terminos-list li::before {
            content: attr(data-n) ". ";
            font-weight: bold;
        }

        .excepciones-title {
            font-size: 7.5px;
            font-weight: bold;
            color: #c00;
            text-align: center;
            margin: 7px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ─── Firma ─── */
        .firma-section {
            margin-top: 10px;
            text-align: right;
        }
        .firma-line {
            display: inline-block;
            border-top: 1px solid #333;
            width: 140px;
            margin-top: 22px;
        }
        .firma-label {
            font-size: 6.5px;
            color: #555;
            text-align: center;
            width: 140px;
            display: inline-block;
        }

        /* Datos de la unidad vendida (bloque destacado) */
        .unidad-bloque {
            background: #f0f4ff;
            border: 1px solid #c3d0f0;
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 8px;
        }
        .unidad-bloque .ub-title {
            font-size: 7px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 4px;
        }
        .ub-grid {
            display: table;
            width: 100%;
        }
        .ub-cell {
            display: table-cell;
            width: 33%;
            padding-right: 4px;
        }
        .ub-cell:last-child { padding-right: 0; }
    </style>
</head>
<body>
<div class="page">

    {{-- ══════════════ COLUMNA IZQUIERDA ══════════════ --}}
    <div class="col-left">

        {{-- Cabecera: marca + info negocio --}}
        <div class="header-left">
            <div class="header-logo-cell">
                <div class="brand-name">{{ strtoupper($negocio->nombre_negocio ?? 'GARANTÍA') }}</div>
            </div>
            <div class="header-info-cell negocio-info">
                @if($negocio)
                    RFC: {{ $negocio->rfc ?? '—' }}<br>
                    {{ $negocio->direccion ?? '' }}<br>
                    Tel: {{ $negocio->telefono ?? '' }}
                @endif
            </div>
        </div>

        <hr>

        {{-- Fecha de compra --}}
        <div class="fecha-label" style="text-align:center; margin-bottom:4px;">Fecha de compra:</div>
        <div class="fecha-row">
            <div class="fecha-boxes">
                <div class="fecha-box">{{ now()->format('d') }}</div>
                <div class="fecha-sep">/</div>
                <div class="fecha-box">{{ now()->format('m') }}</div>
                <div class="fecha-sep">/</div>
                <div class="fecha-box">{{ now()->format('Y') }}</div>
            </div>
        </div>

        {{-- Datos del cliente --}}
        <div class="field-label">Nombre del cliente:</div>
        <div class="field-value">
            {{ $cliente->nombre_cliente }} {{ $cliente->apellido1 }} {{ $cliente->apellido2 }}
        </div>

        <div class="field-label">Teléfono:</div>
        <div class="field-value">{{ $cliente->telefono }}</div>

        <div class="field-label">Dirección:</div>
        <div class="field-value lg">{{ $cliente->correo ?? '' }}{{ ($cliente->correo && isset($ventaDireccion)) ? ' — ' : '' }}{{ $ventaDireccion ?? '' }}</div>

        <hr style="margin: 6px 0;">

        {{-- Por cada bicicleta vendida --}}
        @foreach($bicicletas as $detalle)
        @php $bici = $detalle->bicicleta; @endphp
        <div class="unidad-bloque">
            <div class="ub-title">Unidad #{{ $loop->iteration }}</div>
            <div class="ub-grid">
                <div class="ub-cell">
                    <div class="field-label">Marca:</div>
                    <div class="field-value">{{ $bici->modelo->marca->nombre_marca ?? '—' }}</div>
                </div>
                <div class="ub-cell">
                    <div class="field-label">Modelo:</div>
                    <div class="field-value">{{ $bici->modelo->nombre_modelo ?? '—' }}</div>
                </div>
                <div class="ub-cell">
                    <div class="field-label">Color:</div>
                    <div class="field-value">{{ $bici->color->color ?? '—' }}</div>
                </div>
            </div>
            <div class="field-label">Nº de Serie:</div>
            <div class="field-value" style="font-family: monospace; letter-spacing:0.5px;">{{ $bici->num_serie }}</div>
        </div>
        @endforeach

        <div class="field-label">Sucursal:</div>
        <div class="field-value">{{ $vendedor->nombre_usuario }}</div>

        <hr style="margin-top:8px;">

        {{-- Tabla de series de garantía --}}
        <table class="garantia-table">
            <thead>
                <tr>
                    <th class="serie-num">Serie</th>
                    <th style="width:45%">Partes</th>
                    <th style="width:18%">Plazo de garantía</th>
                    <th style="width:24%">Contenido de la garantía</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="serie-num">1</td>
                    <td>Mango de velocidad, mango de freno, convertidor, claxon, dispositivo anti robo, luces de led, medidor, lazo, máquina de voz.</td>
                    <td style="text-align:center">3 meses</td>
                    <td>Defecto de fábrica</td>
                </tr>
                <tr>
                    <td class="serie-num">2</td>
                    <td>Cargador</td>
                    <td style="text-align:center">6 meses</td>
                    <td>Defecto de fábrica</td>
                </tr>
                <tr>
                    <td class="serie-num">3</td>
                    <td>Batería de plomo ácido</td>
                    <td style="text-align:center">6 meses</td>
                    <td>Reemplazo de batería (únicamente por defecto de fábrica)</td>
                </tr>
                <tr>
                    <td class="serie-num">4</td>
                    <td>Batería de litio</td>
                    <td style="text-align:center">6 meses</td>
                    <td>Reemplazar batería después de 6 meses (únicamente por defecto de fábrica)</td>
                </tr>
                <tr>
                    <td class="serie-num">5</td>
                    <td>Amortiguadores delanteros y traseros, rejilla trasera, tubo de escape, soporte principal, soporte lateral</td>
                    <td style="text-align:center">6 meses</td>
                    <td>Rotura de herrajes, desolado.</td>
                </tr>
                <tr>
                    <td class="serie-num">6</td>
                    <td>Motor</td>
                    <td style="text-align:center">1 año</td>
                    <td>Defecto de fábrica</td>
                </tr>
                <tr>
                    <td class="serie-num">7</td>
                    <td>Controlador</td>
                    <td style="text-align:center">1 año</td>
                    <td>Defecto de fábrica</td>
                </tr>
                <tr>
                    <td class="serie-num">8</td>
                    <td>Manija de dirección, marco, horquilla delantera, columna de dirección, horquilla trasera</td>
                    <td style="text-align:center">1 año</td>
                    <td>Rotura, desolado</td>
                </tr>
                <tr>
                    <td class="serie-num">9</td>
                    <td>Piezas consumibles: neumáticos, espejos retrovisores, fusibles, faro led, direccionales cables, interruptores, zapatas de freno, volante, cojín de asiento.</td>
                    <td colspan="2" style="text-align:center">Las piezas consumibles no están cubiertas por la garantía.</td>
                </tr>
            </tbody>
        </table>

    </div>

    {{-- ══════════════ COLUMNA DERECHA ══════════════ --}}
    <div class="col-right">

        <div class="terminos-title">Términos y condiciones de la garantía</div>

        <ol class="terminos-list">
            <li data-n="1">Se garantiza este producto por el término de un año.</li>
            <li data-n="2">Se responsabiliza por cualquier defecto o falla en el material de fabricación, siempre que sea debidamente verificado por el área de Servicio Técnico Autorizado.</li>
            <li data-n="3">La garantía será válida a partir de la fecha de compra.</li>
            <li data-n="4">La tienda principal o el distribuidor autorizado que venda el vehículo está obligado a proporcionar al propietario el manual de uso, nota de venta y póliza de garantía.</li>
            <li data-n="5">Para activar la garantía del vehículo, el propietario deberá presentar la nota de venta y la póliza de garantía como requisito para llevar a cabo el diagnóstico y la correspondiente solución.</li>
            <li data-n="6">Se considera como uso inadecuado del vehículo la negligencia, abuso o manejo que difiera de las instrucciones proporcionadas en el manual de uso.</li>
            <li data-n="7">El área de Servicio Técnico Autorizado será la instancia final encargada de evaluar el vehículo y decidir si la falla o avería cumple con los términos de este certificado para ser cubierta por la garantía.</li>
            <li data-n="8">No se asume responsabilidad por daños sufridos por el usuario o terceras personas. Además, exime de responsabilidad en casos de accidentes u otras situaciones derivadas del uso indebido del vehículo.</li>
            <li data-n="9">Se reconoce como componentes sujetos al desgaste natural los siguientes elementos: llantas, pastillas o cintas de freno, sistema de transmisión, cable de acelerador, amortiguadores, bujes de suspensión trasera y pistas de dirección. Esto también incluye sustancias o materiales de consumo como aceite de suspensión y líquido de frenos. Estos elementos no estarán cubiertos por la garantía ya que se espera que experimenten desgaste como parte normal de la operación del vehículo.</li>
            <li data-n="10">La garantía no incluye la cobertura de corrosión ni el deterioro de la pintura, cromado, niquelado u otros daños causados por condiciones ambientales adversas o factores externos.</li>
            <li data-n="11">Es importante tener en cuenta que la garantía no se extiende a gastos externos, como pinchazos en las llantas, impactos en las baterías, daños en piezas de plástico o espejos, ni a tareas de limpieza o lubricación del vehículo.</li>
            <li data-n="12">Los accesorios o regalos adicionales como canastillas, cascos, entre otros, no están cubiertos por la garantía.</li>
            <li data-n="13">En el caso de retrasos en las reparaciones cubiertas por la garantía debido a circunstancias atribuibles, no conlleva a indemnización ni extensión de la garantía. Cualquier acción legal, penal o administrativa llevada a cabo ante cualquier autoridad y los costos asociados a estas no están contemplados dentro de la garantía.</li>
            <li data-n="14">Los retrasos en las reparaciones cubiertas por la garantía debido a circunstancias no atribuibles (casos fortuitos o fuerza mayor) no conllevan indemnización ni extensión de la garantía.</li>
        </ol>

        <div class="excepciones-title">Excepciones de la garantía</div>

        <p style="font-size:6.2px; color:#444; margin-bottom:4px; line-height:1.4;">
            Nuestras tiendas principales y distribuidores autorizados no asumen responsabilidad por garantía en los siguientes casos, siempre y cuando sean previamente evaluados por el Servicio Técnico Autorizado:
        </p>

        <ol class="terminos-list">
            <li data-n="1">Problemas derivados de conectar cables, cortos circuitos por mal uso o modificaciones en controlador, motor, sistema eléctrico, suspensión, frenos o cualquier otro componente que no sea original ni esté respaldado.</li>
            <li data-n="2">Problemas resultantes de una sobrecarga o descarga excesiva de las baterías.</li>
            <li data-n="3">Problemas que tengan su origen en impactos o choques del vehículo.</li>
            <li data-n="4">Problemas surgidos como consecuencia de eventos externos, como inundaciones, terremotos, incendios, accidentes, robos o daños originados en situaciones fortuitas.</li>
            <li data-n="5">Problemas causados por el uso de lubricantes que no estén recomendados.</li>
            <li data-n="6">Problemas generados por una acumulación excesiva de suciedad en los componentes electrónicos.</li>
            <li data-n="7">Problemas resultantes de la falta de sustitución oportuna de las piezas de desgaste natural del vehículo.</li>
            <li data-n="8">Problemas que se deban a un inadecuado almacenamiento del vehículo.</li>
            <li data-n="9">La garantía de los vehículos quedará anulada en caso de que las baterías presenten ácido derramado o estén infladas.</li>
            <li data-n="10">Problemas en el cargador resultantes por golpes, exceso de vibración o conexiones prolongadas innecesarias.</li>
            <li data-n="11">Todas las reparaciones incurrirán en un costo adicional por la mano de obra de nuestros técnicos, independientemente de la garantía.</li>
        </ol>

        {{-- Firma --}}
        <div class="firma-section">
            <div class="firma-line"></div><br>
            <div class="firma-label">Nombre y firma del cliente</div>
        </div>

    </div>
</div>
</body>
</html>