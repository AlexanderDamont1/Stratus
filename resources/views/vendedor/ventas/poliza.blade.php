<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Póliza de Garantía</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: letter landscape;
            margin: 18pt 22pt;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #1e293b;
            background: #f2f4f8;
            padding: 12pt;
        }

        /* Papel */
        .document {
            max-width: 100%;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 16pt 20pt 12pt;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* ===== ENCABEZADO ===== */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 8pt;
            margin-bottom: 14pt;
        }
        .header-left {
            display: table-cell;
            vertical-align: bottom;
        }
        .header-right {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
        }
        .empresa-nombre {
            font-size: 15pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.2pt;
        }
        .empresa-sub {
            font-size: 7pt;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            margin-top: 1pt;
        }
        .empresa-contacto {
            font-size: 6.8pt;
            color: #64748b;
            margin-top: 3pt;
        }
        .folio-etiqueta {
            font-size: 6pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.2pt;
        }
        .folio-numero {
            font-size: 13pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.2px;
        }

        /* ===== COLUMNAS ===== */
        .col-izq {
            float: left;
            width: 48%;
        }
        .col-der {
            float: right;
            width: 48%;
        }

        /* ===== TARJETAS ===== */
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 9pt 11pt 11pt;
            margin-bottom: 10pt;
        }
        .card-titulo {
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            color: #334155;
            font-weight: 700;
            margin-bottom: 7pt;
            padding-bottom: 4pt;
            border-bottom: 1.5px solid #e2e8f0;
            display: inline-block;
        }

        /* ===== FILAS DE DATOS ===== */
        .fila-dato {
            display: table;
            width: 100%;
            margin-bottom: 4pt;
        }
        .fila-dato:last-child {
            margin-bottom: 0;
        }
        .celda-dato {
            display: table-cell;
            padding-right: 10pt;
            vertical-align: top;
        }
        .celda-dato:last-child {
            padding-right: 0;
        }
        .etiqueta {
            font-size: 6pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            margin-bottom: 1pt;
        }
        .valor {
            font-size: 8.5pt;
            font-weight: 600;
            color: #0f172a;
        }
        .valor-mono {
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }

        /* ===== TARJETA DE UNIDAD ===== */
        .unidad {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-bottom: 6pt;
            overflow: hidden;
        }
        .unidad:last-child {
            margin-bottom: 0;
        }
        .unidad-cabecera {
            display: table;
            width: 100%;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 4pt 8pt;
        }
        .unidad-info {
            display: table-cell;
            vertical-align: middle;
        }
        .unidad-serie {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 8.5pt;
            font-weight: 700;
            color: #0f172a;
        }
        .unidad-serie .label-serie {
            font-size: 6pt;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            margin-right: 4pt;
        }
        .unidad-serie .numero-serie {
            text-decoration: underline;
            text-underline-offset: 2px;
            text-decoration-thickness: 1.5px;
            text-decoration-color: #94a3b8;
        }
        .unidad-num {
            font-size: 6.5pt;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            font-weight: 700;
        }
        .unidad-modelo {
            font-size: 9pt;
            font-weight: 600;
            color: #0f172a;
        }
        .unidad-cuerpo {
            display: table;
            width: 100%;
            padding: 5pt 8pt;
        }
        .unidad-espec {
            display: table-cell;
            width: 33%;
            padding-right: 6pt;
            vertical-align: top;
        }
        .unidad-espec:last-child {
            padding-right: 0;
        }
        .unidad-espec .etiqueta {
            font-size: 5.8pt;
        }
        .unidad-espec .valor {
            font-size: 8pt;
        }

        /* ===== TABLA DE GARANTÍA ===== */
        .tabla-garantia {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .tabla-garantia thead th {
            background: #f1f5f9;
            border-bottom: 1.5px solid #94a3b8;
            color: #1e293b;
            padding: 3pt 5pt;
            text-align: left;
            font-size: 6pt;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            font-weight: 700;
        }
        .tabla-garantia thead th.centro {
            text-align: center;
        }
        .tabla-garantia tbody td {
            border-bottom: 1px solid #e9edf2;
            padding: 3pt 5pt;
            vertical-align: top;
            color: #334155;
        }
        .tabla-garantia tbody tr:last-child td {
            border-bottom: none;
        }
        .td-num {
            text-align: center;
            font-weight: 600;
            color: #64748b;
            font-size: 7pt;
            width: 13pt;
        }
        .comp-nombre {
            font-weight: 600;
            color: #0f172a;
            font-size: 7.8pt;
        }
        .comp-items {
            color: #64748b;
            font-size: 6.2pt;
            margin-top: 0.5pt;
        }
        .plazo-badge {
            display: inline-block;
            font-weight: 600;
            font-size: 6.8pt;
            color: #1e293b;
            background: #e9edf2;
            border-radius: 10px;
            padding: 0.5pt 7pt;
            letter-spacing: 0.2pt;
        }
        .td-sin-cobertura {
            text-align: center;
            color: #94a3b8;
            font-style: italic;
            font-size: 6.2pt;
        }
        .sin-config {
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 7pt;
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            background: #f8fafc;
        }
        .marca-titulo {
            font-size: 7pt;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            margin: 0 0 4pt;
        }
        .marca-bloque {
            margin-bottom: 8pt;
        }
        .marca-bloque:last-child {
            margin-bottom: 0;
        }

        /* ===== TÉRMINOS Y EXCEPCIONES ===== */
        .poliza-titulo {
            font-size: 13pt;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.2pt;
        }
        .poliza-subtitulo {
            font-size: 6.8pt;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            margin-top: 2pt;
        }

        .lista-terminos {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .lista-terminos li {
            font-size: 6.8pt;
            color: #334155;
            line-height: 1.45;
            margin-bottom: 3.5pt;
            padding-left: 16pt;
            position: relative;
        }
        .lista-terminos li:last-child {
            margin-bottom: 0;
        }
        .lista-terminos li .num {
            position: absolute;
            left: 0;
            top: 0;
            font-weight: 600;
            color: #475569;
            font-size: 6.8pt;
        }

        .excepciones-titulo {
            font-size: 8pt;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
        }
        .excepciones-sub {
            font-size: 6.2pt;
            color: #94a3b8;
            margin-top: 1pt;
            margin-bottom: 5pt;
        }

        /* ===== FIRMA ===== */
        .firma-contenedor {
            margin-top: 14pt;
            display: table;
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 10pt;
        }
        .firma-izq {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
        }
        .firma-der {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: right;
        }
        .firma-linea {
            border-top: 1.5px solid #0f172a;
            width: 120pt;
            display: inline-block;
            padding-top: 14pt;
            margin-bottom: 2pt;
        }
        .firma-etiqueta {
            font-size: 6.2pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6pt;
            display: block;
            margin-top: 2pt;
        }
        .firma-valor {
            font-size: 8pt;
            font-weight: 600;
            color: #0f172a;
        }

        /* ===== PIE ===== */
        .pie-pagina {
            display: table;
            width: 100%;
            margin-top: 10pt;
            border-top: 1px solid #e2e8f0;
            padding-top: 5pt;
        }
        .pie-izq {
            display: table-cell;
            vertical-align: middle;
        }
        .pie-der {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .pie-texto {
            font-size: 6.2pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
        }

        /* ===== RESPONSIVE ===== */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .document {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
            }
            .card {
                box-shadow: none;
                border-color: #ccc;
            }
            .unidad {
                box-shadow: none;
            }
        }
        @media (max-width: 700px) {
            .col-izq,
            .col-der {
                float: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="document">

    @php
        function formatearPlazo(int $meses): string {
            if ($meses === 0) return 'Sin garantía';
            if ($meses % 12 === 0) {
                $a = $meses / 12;
                return $a === 1 ? '1 año' : "{$a} años";
            }
            return "{$meses} meses";
        }

        $sucursalNombre = $personal?->nombre ?? ($negocio->nombre_negocio ?? '—');
        $totalMarcas    = $componentesPorMarca->count();
    @endphp

    <!-- ===== ENCABEZADO ===== -->
    <div class="header">
        <div class="header-left">
            <div class="empresa-nombre">{{ $negocio->nombre_negocio ?? 'GARANTÍA' }}</div>
            <div class="empresa-sub">Póliza de garantía oficial</div>
            <div class="empresa-contacto">
                RFC: {{ $negocio->rfc ?? '—' }}
                @if($negocio->telefono ?? false) &nbsp;·&nbsp; Tel: {{ $negocio->telefono }} @endif
                @if($negocio->direccion ?? false) &nbsp;·&nbsp; {{ $negocio->direccion }} @endif
            </div>
        </div>
        <div class="header-right">
            <div class="folio-etiqueta">Folio</div>
            <div class="folio-numero">#{{ substr($venta->id_venta, -8) }}</div>
        </div>
    </div>

    <div class="clearfix">
        <!-- ===== COLUMNA IZQUIERDA ===== -->
        <div class="col-izq">

            <!-- DATOS DE VENTA -->
            <div class="card">
                <div class="card-titulo">Datos de la venta</div>
                <div class="fila-dato">
                    <div class="celda-dato" style="width:35%">
                        <div class="etiqueta">Sucursal</div>
                        <div class="valor">{{ $sucursalNombre }}</div>
                    </div>
                    <div class="celda-dato" style="width:30%">
                        <div class="etiqueta">Fecha de compra</div>
                        <div class="valor valor-mono">{{ now()->format('d/m/Y') }}</div>
                    </div>
                    <div class="celda-dato">
                        <div class="etiqueta">Teléfono</div>
                        <div class="valor valor-mono">{{ $cliente->telefono }}</div>
                    </div>
                </div>
                <div class="fila-dato">
                    <div class="celda-dato" style="width:60%">
                        <div class="etiqueta">Nombre completo</div>
                        <div class="valor">{{ $cliente->nombre_cliente }} {{ $cliente->apellido1 }} {{ $cliente->apellido2 }}</div>
                    </div>
                    @if($cliente->correo)
                    <div class="celda-dato">
                        <div class="etiqueta">Correo</div>
                        <div class="valor" style="font-size:7.5pt;">{{ $cliente->correo }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- UNIDADES VENDIDAS -->
            <div class="card">
                <div class="card-titulo">Unidades vendidas</div>
                @foreach($bicicletas as $detalle)
                    @php $bici = $detalle->bicicleta; @endphp
                    <div class="unidad">
                        <div class="unidad-cabecera">
                            <div class="unidad-info">
                                <span class="unidad-num">Unidad #{{ $loop->iteration }}</span>
                                <span class="unidad-modelo">
                                    {{ $bici->modelo->marca->nombre_marca ?? '' }}
                                    {{ $bici->modelo->nombre_modelo ?? '—' }}
                                </span>
                            </div>
                            <div class="unidad-serie">
                                <span class="label-serie">No. Serie</span>
                                <span class="numero-serie">{{ $bici->num_serie }}</span>
                            </div>
                        </div>
                        <div class="unidad-cuerpo">
                            <div class="unidad-espec">
                                <div class="etiqueta">Voltaje</div>
                                <div class="valor valor-mono">{{ $bici->voltaje->voltaje ?? '—' }}</div>
                            </div>
                            <div class="unidad-espec">
                                <div class="etiqueta">Color</div>
                                <div class="valor">{{ $bici->color->color ?? '—' }}</div>
                            </div>
                            <div class="unidad-espec">
                                <div class="etiqueta">Cantidad</div>
                                <div class="valor valor-mono">{{ $detalle->cantidad }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- COBERTURA DE GARANTÍA -->
            <div class="card">
                <div class="card-titulo">Cobertura de garantía</div>
                @forelse($componentesPorMarca as $idMarca => $entry)
                    @php
                        $nombreMarca = $entry['marca']->nombre_marca ?? 'Marca desconocida';
                        $componentes = $entry['componentes'];
                    @endphp
                    <div class="marca-bloque">
                        @if($totalMarcas > 1)
                            <div class="marca-titulo">{{ $nombreMarca }}</div>
                        @endif

                        @if($componentes->isEmpty())
                            <div class="sin-config">Sin configuración registrada para <strong>{{ $nombreMarca }}</strong></div>
                        @else
                            <table class="tabla-garantia">
                                <thead>
                                    <tr>
                                        <th style="width:13pt;">#</th>
                                        <th>Componente</th>
                                        <th class="centro" style="width:42pt;">Plazo</th>
                                        <th style="width:65pt;">Cobertura</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($componentes as $comp)
                                        @php
                                            $partes = is_array($comp->incluye) && count($comp->incluye)
                                                ? implode(', ', $comp->incluye) : null;
                                        @endphp
                                        <tr>
                                            <td class="td-num">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="comp-nombre">{{ $comp->nombre_componente }}</div>
                                                @if($partes)<div class="comp-items">{{ $partes }}</div>@endif
                                            </td>
                                            @if($comp->excluido)
                                                <td colspan="2" class="td-sin-cobertura">Sin cobertura</td>
                                            @else
                                                <td style="text-align:center;">
                                                    <span class="plazo-badge">{{ formatearPlazo((int)$comp->duracion_meses) }}</span>
                                                </td>
                                                <td style="color:#475569; font-size:6.2pt;">{{ $comp->cobertura ?? 'Defecto de fábrica' }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @empty
                    <div class="sin-config">Sin configuración de garantía activa. Contacta al administrador.</div>
                @endforelse
            </div>

        </div>

        <!-- ===== COLUMNA DERECHA ===== -->
        <div class="col-der">

            <div style="margin-bottom:8pt;">
                <div class="poliza-titulo">Términos y condiciones</div>
                <div class="poliza-subtitulo">Garantía del fabricante — Leer antes de firmar</div>
            </div>

            <!-- CONDICIONES GENERALES -->
            <div class="card">
                <div class="card-titulo">Condiciones generales</div>
                <ol class="lista-terminos">
                    <li><span class="num">1.</span>Se garantiza este producto por el término de un año.</li>
                    <li><span class="num">2.</span>Se responsabiliza por cualquier defecto o falla en el material de fabricación, verificado por Servicio Técnico Autorizado.</li>
                    <li><span class="num">3.</span>La garantía será válida a partir de la fecha de compra.</li>
                    <li><span class="num">4.</span>La tienda está obligada a proporcionar el manual de uso, nota de venta y póliza de garantía.</li>
                    <li><span class="num">5.</span>Para activar la garantía se deberá presentar la nota de venta y esta póliza.</li>
                    <li><span class="num">6.</span>Se considera uso inadecuado la negligencia, abuso o manejo distinto al manual de uso.</li>
                    <li><span class="num">7.</span>El Servicio Técnico Autorizado es la instancia final para evaluar la falla.</li>
                    <li><span class="num">8.</span>No se asume responsabilidad por daños a terceros ni accidentes por uso indebido.</li>
                    <li><span class="num">9.</span>Desgaste natural (llantas, pastillas, transmisión, cable acelerador, amortiguadores, bujes, aceite de suspensión, líquido de frenos) no cubierto.</li>
                    <li><span class="num">10.</span>No cubre corrosión ni deterioro de pintura/cromado por condiciones ambientales.</li>
                    <li><span class="num">11.</span>No cubre pinchazos, impactos en baterías, daños en plásticos/espejos, limpieza o lubricación.</li>
                    <li><span class="num">12.</span>Accesorios o regalos (canastillas, cascos, etc.) no cubiertos.</li>
                    <li><span class="num">13.</span>Retrasos por causas atribuibles no generan indemnización ni extensión.</li>
                    <li><span class="num">14.</span>Retrasos por fuerza mayor tampoco generan indemnización ni extensión.</li>
                </ol>
            </div>

            <!-- EXCEPCIONES -->
            @php
                $excepcionesPorComponente = collect();
                foreach ($componentesPorMarca as $entry) {
                    foreach ($entry['componentes'] as $comp) {
                        if (!empty($comp->excepciones)) {
                            $excepcionesPorComponente->push([
                                'nombre' => $comp->nombre_componente,
                                'items'  => $comp->excepciones,
                            ]);
                        }
                    }
                }
            @endphp
            @if($excepcionesPorComponente->isNotEmpty())
            <div class="card">
                <div class="excepciones-titulo">Excepciones de la garantía</div>
                <div class="excepciones-sub">Evaluadas previamente por el Servicio Técnico Autorizado</div>
                @foreach($excepcionesPorComponente as $grupo)
                    <div class="marca-titulo" style="margin-top:{{ $loop->first ? '0' : '10pt' }};">{{ $grupo['nombre'] }}</div>
                    <ol class="lista-terminos" style="margin-bottom:6pt;">
                        @foreach($grupo['items'] as $item)
                            <li><span class="num">{{ $loop->iteration }}.</span>{{ $item }}</li>
                        @endforeach
                    </ol>
                @endforeach
            </div>
            @endif

            <!-- FIRMA -->
            <div class="firma-contenedor">
                <div class="firma-izq">
                    <div class="firma-linea"></div>
                    <span class="firma-etiqueta">Nombre y firma del cliente</span>
                </div>
                <div class="firma-der">
                    <span class="firma-etiqueta">Atendido en sucursal</span>
                    <div class="firma-valor">{{ $sucursalNombre }}</div>
                </div>
            </div>

            <!-- PIE -->
            <div class="pie-pagina">
                <div class="pie-izq"><span class="pie-texto">{{ strtoupper($negocio->nombre_negocio ?? '') }}</span></div>
                <div class="pie-der"><span class="pie-texto">Folio #{{ substr($venta->id_venta, -8) }}</span></div>
            </div>

        </div><!-- /.col-der -->
    </div><!-- /.clearfix -->

</div><!-- /.document -->

</body>
</html>