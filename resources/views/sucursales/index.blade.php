<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales — ArrowK</title>
    <meta name="description" content="Encuentra la sucursal ArrowK más cercana a ti.">

    @vite(['resources/css/app.css'])



    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; overflow: hidden; }

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f9fafb;
            color: #111827;
        }

        /* ── Layout ───────────────────────────────────────────── */
        .root {
            display: grid;
            grid-template-columns: 380px 1fr;
            grid-template-rows: 56px 1fr;
            height: 100vh;
        }

        /* ── Topbar ───────────────────────────────────────────── */
        .topbar {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            z-index: 30;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2563eb;
        }

        .brand-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.02em;
        }

        .topbar-meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .topbar-meta strong {
            color: #374151;
            font-weight: 600;
        }

        /* ── Sidebar ──────────────────────────────────────────── */
        .sidebar {
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header del sidebar */
        .sidebar-head {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
        }

        .sidebar-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.02em;
            margin-bottom: 2px;
        }

        .sidebar-sub {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Buscador */
        .search-wrap {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 0 12px;
            height: 36px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .search-wrap:focus-within {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.07);
            background: #fff;
        }

        .search-wrap svg {
            width: 14px;
            height: 14px;
            color: #9ca3af;
            flex-shrink: 0;
        }

        .search-input {
            border: none;
            background: transparent;
            font-size: 0.8125rem;
            color: #374151;
            outline: none;
            flex: 1;
            font-family: inherit;
        }

        .search-input::placeholder { color: #d1d5db; }

        /* Strip de estadísticas */
        .stats-strip {
            display: flex;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
        }

        .stat {
            flex: 1;
            padding: 10px 14px;
            border-right: 1px solid #f3f4f6;
        }

        .stat:last-child { border-right: none; }

        .stat-val {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.025em;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.625rem;
            color: #9ca3af;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* Lista */
        .list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            scroll-behavior: smooth;
        }

        .list::-webkit-scrollbar { width: 3px; }
        .list::-webkit-scrollbar-track { background: transparent; }
        .list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 99px; }

        /* Empty state */
        .empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            color: #9ca3af;
            text-align: center;
        }

        .empty svg { width: 32px; height: 32px; margin-bottom: 8px; }
        .empty p { font-size: 0.8125rem; }

        /* Tarjeta de sucursal */
        .card {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            margin-bottom: 5px;
            cursor: pointer;
            background: #fff;
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.1s;
            animation: cardIn 0.25s ease both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card:hover {
            border-color: #bfdbfe;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
        }

        .card.activa {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
            background: #eff6ff;
        }

        /* Fila superior de la tarjeta */
        .card-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 7px;
        }

        .card-num {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-size: 0.6875rem;
            font-weight: 700;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }

        .card.activa .card-num {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .card-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
            flex: 1;
            letter-spacing: -0.01em;
        }

        /* Badges de status */
        .badge {
            font-size: 0.625rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 99px;
            flex-shrink: 0;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-open   { background: #d1fae5; color: #065f46; }
        .badge-closed { background: #f3f4f6; color: #6b7280; }

        /* Dirección */
        .card-addr {
            font-size: 0.75rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 8px;
            padding-left: 30px;
        }

        /* Pills de info */
        .card-pills {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            padding-left: 30px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.6875rem;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 99px;
            padding: 2px 8px;
            transition: border-color 0.15s;
        }

        .pill svg { width: 10px; height: 10px; flex-shrink: 0; }
        .card.activa .pill { border-color: #bfdbfe; background: #eff6ff; color: #1d4ed8; }

        /* Acciones expandibles al seleccionar */
        .card-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.22s ease, opacity 0.18s ease, margin-top 0.18s, padding-top 0.18s, border-color 0.18s;
            margin-top: 0;
            padding-top: 0;
            border-top: 1px solid transparent;
        }

        .card.activa .card-actions {
            max-height: 48px;
            opacity: 1;
            margin-top: 10px;
            padding-top: 10px;
            border-color: #e5e7eb;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            height: 30px;
            border-radius: 7px;
            font-size: 0.7375rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: opacity 0.12s;
        }

        .btn-action:hover { opacity: 0.82; }
        .btn-action svg  { width: 11px; height: 11px; }

        .btn-primary { background: #1d4ed8; color: #fff; }
        .btn-ghost   { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb !important; }

        /* ── Mapa iframe ──────────────────────────────────────── */
        #mapa {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* ── Panel de coordenadas ────────────────────────────── */
        .coords-panel {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.22s ease, opacity 0.18s ease, margin-top 0.18s, padding-top 0.18s, border-color 0.18s;
            margin-top: 0;
            padding-top: 0;
            border-top: 1px solid transparent;
        }

        .card.activa .coords-panel {
            max-height: 44px;
            opacity: 1;
            margin-top: 8px;
            padding-top: 8px;
            border-color: #e5e7eb;
        }

        .coords-inner {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 5px 10px;
        }

        .coords-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #2563eb;
            flex-shrink: 0;
        }

        .coords-text {
            font-size: 0.6875rem;
            font-family: ui-monospace, 'Cascadia Code', 'Fira Code', monospace;
            color: #4b5563;
            letter-spacing: 0.01em;
            flex: 1;
        }

        .coords-copy {
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 4px;
            color: #9ca3af;
            font-size: 0.625rem;
            font-family: inherit;
            transition: color 0.12s, background 0.12s;
            flex-shrink: 0;
        }

        .coords-copy:hover { color: #2563eb; background: #eff6ff; }

        /* Responsive */
        @media (max-width: 768px) {
            html, body { overflow: auto; }

            .root {
                grid-template-columns: 1fr;
                grid-template-rows: 56px 55vh 1fr;
                height: auto;
            }

            .sidebar {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                order: 3;
            }

            .map-pane { order: 2; min-height: 55vh; }
        }
    </style>
</head>
<body>

<div class="root" x-data="sucursalesCatalogo()">

    {{-- ── TOPBAR ── --}}
    <header class="topbar">
        <a href="/" class="topbar-brand">
            <div class="brand-dot"></div>
            <span class="brand-name">ArrowK</span>
        </a>
        <p class="topbar-meta">
            <strong x-text="filtradas.length"></strong>
            <span x-text="filtradas.length === 1 ? ' sucursal disponible' : ' sucursales disponibles'"></span>
        </p>
    </header>

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">

        <div class="sidebar-head">
            <p class="sidebar-title">Nuestras sucursales</p>
            <p class="sidebar-sub">Encuéntranos cerca de ti</p>

            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input class="search-input" type="text"
                       placeholder="Buscar por nombre o colonia…"
                       x-model="busqueda">
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-strip">
            <div class="stat">
                <div class="stat-val" x-text="filtradas.length"></div>
                <div class="stat-label">Sucursales</div>
            </div>
            <div class="stat">
                <div class="stat-val" x-text="abiertas"></div>
                <div class="stat-label">Abiertas</div>
            </div>
            <div class="stat">
                <div class="stat-val">MX</div>
                <div class="stat-label">País</div>
            </div>
        </div>

        {{-- Lista --}}
        <div class="list">

            {{-- Empty --}}
            <div x-show="filtradas.length === 0" class="empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Sin resultados para "<span x-text="busqueda"></span>"</p>
            </div>

            <template x-for="(s, i) in filtradas" :key="s.id_usuario">
                <div class="card"
                     :class="{ 'activa': activa === s.id_usuario }"
                     :style="'animation-delay:' + (i * 0.035) + 's'"
                     @click="seleccionar(s)">

                    {{-- Fila superior --}}
                    <div class="card-row">
                        <div class="card-num" x-text="i + 1"></div>
                        <span class="card-name" x-text="s.nombre_sucursal || s.nombre_usuario"></span>
                        <span class="badge"
                              :class="estaAbierto(s) ? 'badge-open' : 'badge-closed'"
                              x-text="estaAbierto(s) ? 'Abierto' : 'Cerrado'">
                        </span>
                    </div>

                    {{-- Dirección --}}
                    <p class="card-addr" x-text="s.direccion"></p>

                    {{-- Pills --}}
                    <div class="card-pills">
                        <template x-if="s.telefono">
                            <span class="pill">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span x-text="s.telefono"></span>
                            </span>
                        </template>
                        <template x-if="s.horario_apertura && s.horario_cierre">
                            <span class="pill">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="s.horario_apertura + ' – ' + s.horario_cierre"></span>
                            </span>
                        </template>
                    </div>

                    {{-- Panel de coordenadas ── aparece al seleccionar --}}
                    <div class="coords-panel">
                        <div class="coords-inner">
                            <div class="coords-dot"></div>
                            <span class="coords-text"
                                  :id="'coords-' + s.id_usuario"
                                  x-text="parseFloat(s.lat).toFixed(6) + ',  ' + parseFloat(s.lng).toFixed(6)">
                            </span>
                            <button class="coords-copy"
                                    @click.stop="copiarCoords(s)"
                                    x-text="copiado === s.id_usuario ? '✓ Copiado' : 'Copiar'">
                            </button>
                        </div>
                    </div>

                    {{-- Acciones (aparecen al seleccionar) --}}
                    <div class="card-actions">
                        <a :href="'https://maps.google.com/?q=' + s.lat + ',' + s.lng"
                           target="_blank"
                           @click.stop
                           class="btn-action btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Cómo llegar
                        </a>
                        <template x-if="s.telefono">
                            <a :href="'tel:' + s.telefono"
                               @click.stop
                               class="btn-action btn-ghost">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Llamar
                            </a>
                        </template>
                        <template x-if="!s.telefono">
                            <span></span>
                        </template>
                    </div>

                </div>
            </template>
        </div>

    </aside>

    {{-- ── MAPA Google Maps Embed (gratuito, sin API key) ── --}}
    <div class="map-pane">
        <iframe
            id="mapa"
            x-bind:src="mapaUrl"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

</div>

{{-- Sin librerías externas de mapa, solo Alpine --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    const SUCURSALES_DATA = @json($sucursales);

    // URL base del embed de Google Maps (sin API key)
    // Usamos el modo "place" con q=lat,lng — completamente gratuito
    function buildMapUrl(lat, lng, zoom) {
        zoom = zoom || 14;
        return 'https://maps.google.com/maps?q=' + lat + ',' + lng
             + '&z=' + zoom
             + '&output=embed'
             + '&hl=es';
    }

    // URL inicial: centro de todas las sucursales
    function buildOverviewUrl(sucursales) {
        if (!sucursales.length) {
            return buildMapUrl(19.4326, -99.1332, 11);
        }
        if (sucursales.length === 1) {
            return buildMapUrl(sucursales[0].lat, sucursales[0].lng, 15);
        }
        var lat = sucursales.reduce(function (a, b) { return a + parseFloat(b.lat); }, 0) / sucursales.length;
        var lng = sucursales.reduce(function (a, b) { return a + parseFloat(b.lng); }, 0) / sucursales.length;
        return buildMapUrl(lat, lng, 11);
    }

    document.addEventListener('alpine:init', function () {
        Alpine.data('sucursalesCatalogo', function () {
            return {
                sucursales : SUCURSALES_DATA,
                busqueda   : '',
                activa     : null,
                copiado    : null,
                mapaUrl    : buildOverviewUrl(SUCURSALES_DATA),

                /* ── Computed ───────────────────────────────────── */
                get filtradas() {
                    var q = this.busqueda.toLowerCase().trim();
                    if (!q) return this.sucursales;
                    return this.sucursales.filter(function (s) {
                        return (s.nombre_sucursal || s.nombre_usuario || '').toLowerCase().includes(q)
                            || (s.direccion || '').toLowerCase().includes(q);
                    });
                },

                get abiertas() {
                    var self = this;
                    return self.sucursales.filter(function (s) {
                        return self.estaAbierto(s);
                    }).length;
                },

                /* ── Seleccionar ────────────────────────────────── */
                seleccionar: function (s) {
                    var self = this;

                    // Si ya estaba activa, la deselecciona (toggle)
                    if (self.activa === s.id_usuario) {
                        self.activa  = null;
                        self.mapaUrl = buildOverviewUrl(self.sucursales);
                        return;
                    }

                    self.activa  = s.id_usuario;
                    // Apuntar el iframe a las coordenadas exactas de la sucursal
                    self.mapaUrl = buildMapUrl(parseFloat(s.lat), parseFloat(s.lng), 16);

                    // Scroll a la tarjeta
                    self.$nextTick(function () {
                        var cardEl = document.querySelector('.card.activa');
                        if (cardEl) cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    });
                },

                /* ── Copiar coordenadas ─────────────────────────── */
                copiarCoords: function (s) {
                    var self = this;
                    var texto = parseFloat(s.lat).toFixed(6) + ', ' + parseFloat(s.lng).toFixed(6);
                    navigator.clipboard.writeText(texto).then(function () {
                        self.copiado = s.id_usuario;
                        setTimeout(function () { self.copiado = null; }, 2000);
                    });
                },

                /* ── Horario ────────────────────────────────────── */
                estaAbierto: function (s) {
                    if (!s.horario_apertura || !s.horario_cierre) return true;
                    var now  = new Date();
                    var mins = now.getHours() * 60 + now.getMinutes();
                    var parse = function (t) {
                        var p = t.split(':');
                        return parseInt(p[0]) * 60 + parseInt(p[1] || 0);
                    };
                    return mins >= parse(s.horario_apertura) && mins < parse(s.horario_cierre);
                },
            };
        });
    });
</script>

</body>
</html>