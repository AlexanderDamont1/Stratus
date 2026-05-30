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
            font-size: 7.5px;
            color: #1a1a1a;
            background: #fff;
        }

        /* ══ LAYOUT PRINCIPAL ══ */
        .page {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .col-left {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            border-right: 1px solid #e2e8f0;
        }
        .col-right {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }

        /* ══ HEADER IZQUIERDO ══ */
        .header-band {
            background: #0f172a;
            padding: 11px 13px 9px 13px;
        }
        .header-brand-row {
            display: table;
            width: 100%;
        }
        .header-brand-cell {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .header-meta-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .brand-name {
            font-size: 17px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #ffffff;
            line-height: 1;
        }
        .brand-tagline {
            font-size: 5.5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 3px;
        }
        .folio-label {
            font-size: 5.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .folio-num {
            font-size: 9px;
            font-weight: 700;
            color: #38bdf8;
            font-family: "Courier New", monospace;
        }

        /* ══ STRIP SUCURSAL ══ */
        .sucursal-strip {
            background: #0ea5e9;
            padding: 4px 13px;
        }
        .sucursal-strip-inner {
            display: table;
            width: 100%;
        }
        .sucursal-strip-left  { display: table-cell; vertical-align: middle; }
        .sucursal-strip-right { display: table-cell; vertical-align: middle; text-align: right; }
        .strip-label {
            font-size: 5.5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #e0f2fe;
        }
        .sucursal-name {
            font-size: 8px;
            font-weight: 700;
            color: #ffffff;
        }
        .fecha-value {
            font-size: 7.5px;
            font-weight: 700;
            color: #ffffff;
            font-family: "Courier New", monospace;
        }

        /* ══ CUERPO IZQUIERDO ══ */
        .left-body { padding: 9px 13px; }

        /* ══ LABELS DE SECCIÓN ══ */
        .section-label {
            font-size: 5.5px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ══ CLIENTE ══ */
        .client-grid  { display: table; width: 100%; margin-bottom: 7px; }
        .client-cell  { display: table-cell; padding-right: 7px; vertical-align: top; }
        .client-cell:last-child { padding-right: 0; }
        .field-key {
            font-size: 5.5px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1px;
        }
        .field-val {
            font-size: 7.5px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 0.5px solid #e2e8f0;
            border-radius: 3px;
            padding: 2px 5px;
            min-height: 13px;
        }
        .field-val.mono { font-family: "Courier New", monospace; letter-spacing: 0.5px; }

        /* ══ TARJETA UNIDAD ══ */
        .unit-card {
            border: 0.5px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }
        .unit-header {
            display: table;
            width: 100%;
            background: #0f172a;
            padding: 4px 7px;
        }
        .unit-hl { display: table-cell; vertical-align: middle; }
        .unit-hr { display: table-cell; vertical-align: middle; text-align: right; }
        .unit-num   { font-size: 5.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; }
        .unit-model { font-size: 8.5px; font-weight: 700; color: #ffffff; }
        .unit-sl    { font-size: 5.5px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .unit-sv    { font-size: 7px; font-weight: 700; color: #38bdf8; font-family: "Courier New", monospace; }
        .unit-body  { display: table; width: 100%; padding: 5px 7px; background: #fff; }
        .unit-spec  { display: table-cell; width: 33%; padding-right: 4px; vertical-align: top; }
        .unit-spec:last-child { padding-right: 0; }

        /* ══ TABLA GARANTÍA ══ */
        .garantia-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6px;
            margin-top: 5px;
        }
        .garantia-table thead tr { background: #0f172a; }
        .garantia-table thead th {
            color: #e2e8f0;
            padding: 3px 5px;
            text-align: left;
            font-size: 5.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .garantia-table thead th.tc { text-align: center; }
        .garantia-table tbody td {
            border-bottom: 0.5px solid #e2e8f0;
            padding: 3.5px 5px;
            vertical-align: top;
            color: #334155;
            line-height: 1.4;
        }
        .garantia-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .garantia-table tbody tr:last-child td      { border-bottom: none; }
        .td-num {
            text-align: center;
            font-weight: 700;
            color: #94a3b8;
            font-size: 6px;
            width: 14px;
        }
        .comp-name  { font-weight: 700; color: #0f172a; font-size: 6px; }
        .comp-items { color: #64748b; font-size: 5.5px; margin-top: 1px; }

        /* Pill plazos */
        .pill {
            display: inline-block;
            border-radius: 2px;
            padding: 1px 5px;
            font-size: 5.8px;
            font-weight: 700;
            white-space: nowrap;
        }
        .pill-blue  { background: #e0f2fe; color: #0369a1; }
        .pill-green { background: #dcfce7; color: #166534; }
        .pill-gray  { background: #f1f5f9; color: #94a3b8; }

        .td-consumible { text-align: center; color: #94a3b8; font-style: italic; font-size: 5.5px; }
        .sin-config {
            border: 0.5px dashed #cbd5e1;
            border-radius: 3px;
            padding: 7px;
            font-size: 6px;
            color: #94a3b8;
            text-align: center;
            margin-top: 5px;
        }
        .marca-titulo {
            font-size: 6px;
            font-weight: 700;
            color: #0ea5e9;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 7px 0 2px;
        }

        /* ══ COLUMNA DERECHA ══ */
        .right-header-band {
            background: #0f172a;
            padding: 11px 13px 9px 13px;
        }
        .poliza-title    { font-size: 11px; font-weight: 900; color: #ffffff; letter-spacing: -0.3px; }
        .poliza-subtitle { font-size: 5.5px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }

        .right-strip {
            background: #0284c7;
            padding: 4px 13px;
        }
        .right-strip-text { font-size: 5.5px; color: #bae6fd; text-transform: uppercase; letter-spacing: 1.5px; }

        .right-body { padding: 9px 13px; }

        /* ══ LISTA TÉRMINOS ══ */
        .terminos-list { list-style: none; padding: 0; margin: 0 0 5px 0; }
        .terminos-list li {
            font-size: 5.8px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 2.5px;
            padding-left: 13px;
            position: relative;
        }
        .terminos-list li .n {
            position: absolute;
            left: 0;
            font-weight: 700;
            color: #0ea5e9;
        }

        /* ══ HEADER EXCEPCIONES ══ */
        .excepciones-block {
            background: #fff1f2;
            border: 0.5px solid #fecdd3;
            border-radius: 3px;
            padding: 4px 7px;
            margin: 6px 0 4px;
        }
        .excepciones-title { font-size: 7px; font-weight: 700; color: #be123c; text-transform: uppercase; letter-spacing: 0.8px; }
        .excepciones-sub   { font-size: 5.5px; color: #fb7185; margin-top: 1px; }

        /* ══ FIRMA ══ */
        .firma-wrap {
            margin-top: 9px;
            display: table;
            width: 100%;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 7px;
        }
        .firma-left  { display: table-cell; width: 50%; vertical-align: bottom; }
        .firma-right { display: table-cell; width: 50%; vertical-align: bottom; text-align: right; }
        .firma-line  {
            border-top: 0.5px solid #334155;
            width: 110px;
            display: inline-block;
            padding-top: 16px;
        }
        .firma-key  { font-size: 5.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-top: 2px; }
        .firma-val  { font-size: 6.5px; font-weight: 700; color: #0f172a; }

        /* ══ PIE ══ */
        .pie-row {
            display: table;
            width: 100%;
            margin-top: 7px;
            border-top: 0.5px solid #f1f5f9;
            padding-top: 5px;
        }
        .pie-cell  { display: table-cell; vertical-align: middle; }
        .pie-cell.r { text-align: right; }
        .pie-text  { font-size: 5.5px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1.5px; }
    </style>
</head>
<body>

@php
    function formatearPlazo(int $meses): string {
        if ($meses === 0) return 'Sin garantía';
        if ($meses % 12 === 0) {
            $a = $meses / 12;
            return $a === 1 ? '1 año' : "{$a} años";
        }
        return "{$meses} meses";
    }
    function pillCls(int $meses): string {
        if ($meses === 0) return 'pill pill-gray';
        if ($meses >= 12)  return 'pill pill-green';
        return 'pill pill-blue';
    }

    $sucursalNombre = $personal?->nombre ?? ($negocio->nombre_negocio ?? '—');
    $totalMarcas    = $componentesPorMarca->count();
@endphp

<div class="page">

    {{-- ═══════════ IZQUIERDA ═══════════ --}}
    <div class="col-left">

        <div class="header-band">
            <div class="header-brand-row">
                <div class="header-brand-cell">
                    <div class="brand-name">{{ strtoupper($negocio->nombre_negocio ?? 'GARANTÍA') }}</div>
                    <div class="brand-tagline">Póliza de garantía oficial</div>
                </div>
                <div class="header-meta-cell">
                    <div class="folio-label">Folio</div>
                    <div class="folio-num">#{{ substr($venta->id_venta, -8) }}</div>
                </div>
            </div>
        </div>

        <div class="sucursal-strip">
            <div class="sucursal-strip-inner">
                <div class="sucursal-strip-left">
                    <div class="strip-label">Sucursal</div>
                    <div class="sucursal-name">{{ $sucursalNombre }}</div>
                </div>
                <div class="sucursal-strip-right">
                    <div class="strip-label">Fecha de compra</div>
                    <div class="fecha-value">{{ now()->format('d / m / Y') }}</div>
                </div>
            </div>
        </div>

        <div class="left-body">

            {{-- Cliente --}}
            <div class="section-label">Datos del cliente</div>
            <div class="client-grid">
                <div class="client-cell" style="width:62%">
                    <div class="field-key">Nombre completo</div>
                    <div class="field-val">
                        {{ $cliente->nombre_cliente }} {{ $cliente->apellido1 }} {{ $cliente->apellido2 }}
                    </div>
                </div>
                <div class="client-cell" style="width:38%">
                    <div class="field-key">Teléfono</div>
                    <div class="field-val mono">{{ $cliente->telefono }}</div>
                </div>
            </div>
            @if($cliente->correo)
                <div class="field-key">Correo electrónico</div>
                <div class="field-val" style="margin-bottom:7px;">{{ $cliente->correo }}</div>
            @endif

            {{-- Unidades --}}
            <div class="section-label" style="margin-top:5px;">Unidades vendidas</div>

            @foreach($bicicletas as $detalle)
                @php $bici = $detalle->bicicleta; @endphp
                <div class="unit-card">
                    <div class="unit-header">
                        <div class="unit-hl">
                            <div class="unit-num">Unidad #{{ $loop->iteration }}</div>
                            <div class="unit-model">
                                {{ $bici->modelo->marca->nombre_marca ?? '' }}
                                {{ $bici->modelo->nombre_modelo ?? '—' }}
                            </div>
                        </div>
                        <div class="unit-hr">
                            <div class="unit-sl">No. Serie</div>
                            <div class="unit-sv">{{ $bici->num_serie }}</div>
                        </div>
                    </div>
                    <div class="unit-body">
                        <div class="unit-spec">
                            <div class="field-key">Voltaje</div>
                            <div class="field-val mono">{{ $bici->voltaje->voltaje ?? '—' }}</div>
                        </div>
                        <div class="unit-spec">
                            <div class="field-key">Color</div>
                            <div class="field-val">{{ $bici->color->color ?? '—' }}</div>
                        </div>
                        <div class="unit-spec">
                            <div class="field-key">Cantidad</div>
                            <div class="field-val mono">{{ $detalle->cantidad }}</div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Tabla garantía dinámica --}}
            <div class="section-label" style="margin-top:5px;">Cobertura de garantía</div>

            @forelse($componentesPorMarca as $idMarca => $entry)
                @php
                    $nombreMarca = $entry['marca']->nombre_marca ?? 'Marca desconocida';
                    $componentes = $entry['componentes'];
                @endphp

                @if($totalMarcas > 1)
                    <div class="marca-titulo">{{ $nombreMarca }}</div>
                @endif

                @if($componentes->isEmpty())
                    <div class="sin-config">
                        Sin configuración registrada para <strong>{{ $nombreMarca }}</strong>
                    </div>
                @else
                    <table class="garantia-table">
                        <thead>
                            <tr>
                                <th style="width:14px;">#</th>
                                <th>Componente / Partes</th>
                                <th class="tc" style="width:50px;">Plazo</th>
                                <th style="width:58px;">Cobertura</th>
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
                                        <div class="comp-name">{{ $comp->nombre_componente }}</div>
                                        @if($partes)
                                            <div class="comp-items">{{ $partes }}</div>
                                        @endif
                                    </td>
                                    @if($comp->excluido)
                                        <td colspan="2" class="td-consumible">
                                            Piezas consumibles — sin cobertura
                                        </td>
                                    @else
                                        <td style="text-align:center;">
                                            <span class="{{ pillCls((int)$comp->duracion_meses) }}">
                                                {{ formatearPlazo((int)$comp->duracion_meses) }}
                                            </span>
                                        </td>
                                        <td style="color:#475569; font-size:5.5px;">
                                            {{ $comp->cobertura ?? 'Defecto de fábrica' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            @empty
                <div class="sin-config">
                    Sin configuración de garantía activa. Contacta al administrador.
                </div>
            @endforelse

        </div>
    </div>

    {{-- ═══════════ DERECHA ═══════════ --}}
    <div class="col-right">

        <div class="right-header-band">
            <div class="poliza-title">Términos y condiciones</div>
            <div class="poliza-subtitle">Garantía del fabricante — Leer antes de firmar</div>
        </div>

        <div class="right-strip">
            <div class="right-strip-text">
                RFC: {{ $negocio->rfc ?? '—' }}
                &nbsp;·&nbsp; Tel: {{ $negocio->telefono ?? '—' }}
                @if($negocio->direccion ?? false)
                    &nbsp;·&nbsp; {{ $negocio->direccion }}
                @endif
            </div>
        </div>

        <div class="right-body">

            <div class="section-label">Condiciones generales</div>

            <ol class="terminos-list">
                <li><span class="n">1.</span>Se garantiza este producto por el término de un año.</li>
                <li><span class="n">2.</span>Se responsabiliza por cualquier defecto o falla en el material de fabricación, siempre que sea debidamente verificado por el área de Servicio Técnico Autorizado.</li>
                <li><span class="n">3.</span>La garantía será válida a partir de la fecha de compra.</li>
                <li><span class="n">4.</span>La tienda o distribuidor autorizado está obligado a proporcionar al propietario el manual de uso, nota de venta y póliza de garantía.</li>
                <li><span class="n">5.</span>Para activar la garantía el propietario deberá presentar la nota de venta y esta póliza como requisito para el diagnóstico y la solución correspondiente.</li>
                <li><span class="n">6.</span>Se considera uso inadecuado la negligencia, abuso o manejo que difiera de las instrucciones del manual de uso.</li>
                <li><span class="n">7.</span>El Servicio Técnico Autorizado será la instancia final para evaluar si la falla cumple los términos de este certificado.</li>
                <li><span class="n">8.</span>No se asume responsabilidad por daños al usuario o terceros, ni por accidentes derivados del uso indebido del vehículo.</li>
                <li><span class="n">9.</span>Componentes de desgaste natural (llantas, pastillas de freno, transmisión, cable acelerador, amortiguadores, bujes, pistas de dirección, aceite de suspensión y líquido de frenos) no están cubiertos por la garantía.</li>
                <li><span class="n">10.</span>La garantía no cubre corrosión ni deterioro de pintura, cromado, niquelado u otros daños por condiciones ambientales adversas.</li>
                <li><span class="n">11.</span>No cubre pinchazos, impactos en baterías, daños en plásticos o espejos, ni tareas de limpieza o lubricación.</li>
                <li><span class="n">12.</span>Accesorios o regalos (canastillas, cascos, etc.) no están cubiertos.</li>
                <li><span class="n">13.</span>Retrasos en reparaciones cubiertas por causas atribuibles no generan indemnización ni extensión. Las acciones legales y sus costos no están contemplados.</li>
                <li><span class="n">14.</span>Retrasos por causas no atribuibles (fuerza mayor) tampoco generan indemnización ni extensión de garantía.</li>
            </ol>

            <div class="excepciones-block">
                <div class="excepciones-title">Excepciones de la garantía</div>
                <div class="excepciones-sub">Evaluadas previamente por el Servicio Técnico Autorizado</div>
            </div>

            <ol class="terminos-list">
                <li><span class="n">1.</span>Cortos circuitos por mal uso o modificaciones en controlador, motor, sistema eléctrico, suspensión, frenos u otro componente no original.</li>
                <li><span class="n">2.</span>Sobrecarga o descarga excesiva de las baterías.</li>
                <li><span class="n">3.</span>Daños por impactos o choques del vehículo.</li>
                <li><span class="n">4.</span>Eventos externos: inundaciones, terremotos, incendios, accidentes o robos.</li>
                <li><span class="n">5.</span>Uso de lubricantes no recomendados.</li>
                <li><span class="n">6.</span>Acumulación excesiva de suciedad en componentes electrónicos.</li>
                <li><span class="n">7.</span>Falta de sustitución oportuna de piezas de desgaste natural.</li>
                <li><span class="n">8.</span>Almacenamiento inadecuado del vehículo.</li>
                <li><span class="n">9.</span>Baterías con ácido derramado o infladas — garantía anulada.</li>
                <li><span class="n">10.</span>Daños en cargador por golpes, vibración excesiva o conexiones prolongadas innecesarias.</li>
                <li><span class="n">11.</span>Toda reparación genera costo adicional por mano de obra, independientemente de la garantía.</li>
            </ol>

            {{-- Firma --}}
            <div class="firma-wrap">
                <div class="firma-left">
                    <div class="firma-line"></div>
                    <span class="firma-key">Nombre y firma del cliente</span>
                </div>
                <div class="firma-right">
                    <span class="firma-key">Atendido en sucursal</span>
                    <div class="firma-val">{{ $sucursalNombre }}</div>
                    @if($negocio->telefono ?? false)
                        <div style="font-size:5.5px; color:#94a3b8; margin-top:1px;">{{ $negocio->telefono }}</div>
                    @endif
                </div>
            </div>

            {{-- Pie --}}
            <div class="pie-row">
                <div class="pie-cell">
                    <span class="pie-text">{{ strtoupper($negocio->nombre_negocio ?? '') }} &middot; Póliza oficial de garantía</span>
                </div>
                <div class="pie-cell r">
                    <span class="pie-text">Folio #{{ substr($venta->id_venta, -8) }}</span>
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>