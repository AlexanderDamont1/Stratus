<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales — ArrowK</title>
    <meta name="description" content="Encuentra la sucursal ArrowK más cercana a ti.">

    @vite(['resources/css/app.css'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,400;1,700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #f0ebe0;
            --bg-2:        #e8e2d6;
            --surface:     #ffffff;
            --surface-2:   #f5f1ea;
            --border:      #ddd5c4;
            --border-2:    #c8bfad;
            --ink:         #1e1a13;
            --ink-2:       #4a3f30;
            --ink-3:       #8c7d6a;
            --ink-4:       #b8aa98;
            --green:       #2d5a2e;
            --green-mid:   #3d7a3e;
            --green-light: #eaf2ea;
            --green-glow:  rgba(45,90,46,0.12);
            --amber:       #b45309;
        }

        html.dark {
            --bg:          #141210;
            --bg-2:        #1c1916;
            --surface:     #201d19;
            --surface-2:   #2a2520;
            --border:      rgba(255,255,255,0.08);
            --border-2:    rgba(255,255,255,0.15);
            --ink:         #f0ebe0;
            --ink-2:       #c8b99a;
            --ink-3:       #7a6e60;
            --ink-4:       #4a4035;
            --green:       #4caf50;
            --green-mid:   #66bb6a;
            --green-light: rgba(76,175,80,0.1);
            --green-glow:  rgba(76,175,80,0.15);
            --amber:       #f59e0b;
        }

        html, body {
            min-height: 100%;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        [x-cloak] { display: none !important; }

        /* ══════════════════════════════════
           NAVBAR
        ══════════════════════════════════ */
        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 56px;
            transition: background 0.3s;
        }

        .nav-brand {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-brand-mark {
            width: 26px;
            height: 26px;
            background: var(--green);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.3s;
        }

        .nav-brand-mark svg { width: 13px; height: 13px; color: #fff; }

        .nav-brand-name {
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -0.02em;
            transition: color 0.3s;
        }

        .nav-right { display: flex; align-items: center; gap: 16px; }

        .nav-count {
            font-size: 0.75rem;
            color: var(--ink-3);
        }

        .nav-count strong { color: var(--ink); font-weight: 600; }

        /* Dark mode toggle */
        .dark-toggle {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface-2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            color: var(--ink-3);
        }

        .dark-toggle:hover { border-color: var(--border-2); color: var(--ink); }
        .dark-toggle svg { width: 15px; height: 15px; }

        /* ══════════════════════════════════
           HERO
        ══════════════════════════════════ */
        .hero {
            background: var(--bg-2);
            border-bottom: 1px solid var(--border);
            padding: 64px 40px 56px;
            transition: background 0.3s;
        }

        .hero-inner {
            max-width: 840px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: end;
            gap: 40px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--green);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 16px;
        }

        .hero-eyebrow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
        }

        .hero-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.5rem, 5vw, 3.75rem);
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -0.03em;
            line-height: 1.02;
            margin-bottom: 18px;
            transition: color 0.3s;
        }

        .hero-title em {
            font-style: italic;
            font-weight: 700;
            color: var(--green);
        }

        .hero-desc {
            font-size: 1rem;
            color: var(--ink-3);
            line-height: 1.7;
            font-weight: 300;
            max-width: 420px;
        }

        .hero-stats {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-shrink: 0;
        }

        .hero-stat {
            text-align: right;
        }

        .hero-stat-n {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--green);
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .hero-stat-l {
            font-size: 0.625rem;
            font-weight: 600;
            color: var(--ink-4);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 2px;
        }

        .hero-stat-divider {
            width: 100%;
            height: 1px;
            background: var(--border);
        }

        /* ══════════════════════════════════
           BODY / CONTENIDO
        ══════════════════════════════════ */
        .body {
            max-width: 840px;
            margin: 0 auto;
            padding: 40px 40px 80px;
        }

        /* Buscador */
        .search-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0 18px;
            height: 50px;
            margin-bottom: 36px;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.3s;
        }

        .search-wrap:focus-within {
            border-color: var(--green-mid);
            box-shadow: 0 0 0 3px var(--green-glow);
        }

        .search-wrap svg { width: 16px; height: 16px; color: var(--ink-4); flex-shrink: 0; }

        .search-input {
            border: none;
            background: transparent;
            font-size: 0.9375rem;
            color: var(--ink);
            outline: none;
            flex: 1;
            font-family: inherit;
            font-weight: 400;
        }

        .search-input::placeholder { color: var(--ink-4); }

        .search-badge {
            font-size: 0.6875rem;
            font-weight: 500;
            color: var(--green);
            background: var(--green-light);
            border-radius: 99px;
            padding: 3px 10px;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        /* ══════════════════════════════════
           CARDS
        ══════════════════════════════════ */
        .lista { display: flex; flex-direction: column; gap: 14px; }

        .card {
            display: grid;
            grid-template-columns: 200px 1fr;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--surface);
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.3s;
            animation: cardIn 0.35s ease both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card:hover {
            border-color: var(--border-2);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }

        .card.activa {
            border-color: var(--green);
            box-shadow: 0 0 0 3px var(--green-glow), 0 4px 24px rgba(0,0,0,0.06);
        }

        /* ── Mapa ── */
        .card-map {
            position: relative;
            min-height: 200px;
            background: var(--surface-2);
            overflow: hidden;
        }

        .card-map iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
            pointer-events: none;
            position: absolute;
            inset: 0;
        }

        .card-map-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
        }

        /* Número de sucursal */
        .card-map-num {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 3;
            font-size: 0.625rem;
            font-weight: 700;
            color: #fff;
            background: var(--green);
            border-radius: 99px;
            padding: 3px 9px;
            letter-spacing: 0.05em;
        }

        /* Badge abierto/cerrado */
        .map-status {
            position: absolute;
            bottom: 10px;
            left: 10px;
            z-index: 3;
            font-size: 0.5625rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .map-status.open   { background: rgba(45,90,46,0.88); color: #fff; backdrop-filter: blur(6px); }
        .map-status.closed { background: rgba(0,0,0,0.5);     color: rgba(255,255,255,0.65); backdrop-filter: blur(6px); }

        /* Placeholder */
        .map-ph {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--surface-2);
            transition: opacity 0.4s ease;
        }

        .map-ph.loaded { opacity: 0; pointer-events: none; }

        .map-ph-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .map-ph-pin {
            width: 24px;
            height: 24px;
            background: var(--green);
            border-radius: 50% 50% 50% 3px;
            transform: rotate(-45deg);
            border: 2.5px solid var(--surface);
            box-shadow: 0 3px 10px var(--green-glow);
        }

        .map-ph-text {
            font-size: 0.6rem;
            color: var(--ink-4);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        /* ── Info ── */
        .card-info {
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
        }

        .card-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 6px;
            transition: color 0.3s;
        }

        .card-addr {
            font-size: 0.8125rem;
            color: var(--ink-3);
            line-height: 1.6;
            margin-bottom: 16px;
            flex: 1;
            font-weight: 300;
        }

        /* Separador */
        .card-divider {
            height: 1px;
            background: var(--border);
            margin-bottom: 14px;
        }

        /* Fila de meta-info */
        .card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 14px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.6875rem;
            color: var(--ink-2);
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 4px 10px;
            transition: background 0.3s, border-color 0.3s;
        }

        .meta-pill svg { width: 11px; height: 11px; flex-shrink: 0; color: var(--ink-3); }

        .card.activa .meta-pill {
            background: var(--green-light);
            border-color: rgba(45,90,46,0.2);
        }

        /* Coordenadas */
        .coords-row {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 12px;
            margin-bottom: 14px;
            transition: background 0.3s;
        }

        .coords-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            flex-shrink: 0;
        }

        .coords-val {
            flex: 1;
            font-family: ui-monospace, 'Cascadia Code', monospace;
            font-size: 0.6875rem;
            color: var(--ink-2);
            letter-spacing: 0.015em;
        }

        .coords-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.625rem;
            font-family: inherit;
            font-weight: 500;
            color: var(--ink-3);
            padding: 2px 6px;
            border-radius: 5px;
            transition: color 0.12s, background 0.12s;
            flex-shrink: 0;
        }

        .coords-btn:hover { color: var(--green); background: var(--green-light); }

        /* Botones de acción */
        .card-actions { display: flex; gap: 8px; }

        .btn-p {
            flex: 1;
            height: 36px;
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 0.8125rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-p:hover { background: var(--green-mid); }
        .btn-p svg   { width: 12px; height: 12px; }

        .btn-s {
            height: 36px;
            padding: 0 14px;
            background: transparent;
            color: var(--ink-2);
            border: 1px solid var(--border);
            border-radius: 9px;
            font-size: 0.8125rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s;
        }

        .btn-s:hover { border-color: var(--border-2); color: var(--ink); }
        .btn-s svg   { width: 12px; height: 12px; }

        /* ══════════════════════════════════
           EMPTY
        ══════════════════════════════════ */
        .empty {
            text-align: center;
            padding: 72px 24px;
            color: var(--ink-4);
        }

        .empty-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            border-radius: 14px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon svg { width: 22px; height: 22px; color: var(--ink-4); }
        .empty h3 { font-size: 0.9375rem; font-weight: 500; color: var(--ink-3); margin-bottom: 4px; }
        .empty p  { font-size: 0.8125rem; color: var(--ink-4); }

        /* ══════════════════════════════════
           FOOTER
        ══════════════════════════════════ */
        .foot {
            border-top: 1px solid var(--border);
            padding: 24px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: border-color 0.3s;
        }

        .foot-copy { font-size: 0.75rem; color: var(--ink-4); }
        .foot-back {
            font-size: 0.75rem;
            color: var(--ink-3);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.15s;
        }

        .foot-back:hover { color: var(--ink); }
        .foot-back svg { width: 12px; height: 12px; }

        /* ══════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════ */
        @media (max-width: 640px) {
            .nav    { padding: 0 20px; }
            .hero   { padding: 40px 20px 36px; }
            .body   { padding: 28px 20px 60px; }
            .foot   { padding: 20px; flex-direction: column; gap: 8px; text-align: center; }

            .hero-inner { grid-template-columns: 1fr; }
            .hero-stats { flex-direction: row; justify-content: flex-start; gap: 24px; }
            .hero-stat  { text-align: left; }
            .hero-stat-divider { display: none; }

            .card { grid-template-columns: 1fr; }
            .card-map { min-height: 180px; }
        }
    </style>
</head>
<body x-data="catalogo()" :class="{ 'dark': darkMode }" x-init="init()">

    {{-- ── NAVBAR ── --}}
    <nav class="nav">
        <a href="/" class="nav-brand">
            <div class="nav-brand-mark">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="nav-brand-name">ArrowK</span>
        </a>

        <div class="nav-right">
            <p class="nav-count">
                <strong x-text="filtradas.length"></strong>
                <span x-text="' sucursal' + (filtradas.length !== 1 ? 'es' : '')"></span>
            </p>

            <button class="dark-toggle"
                    @click="darkMode = !darkMode; guardarTema()"
                    :title="darkMode ? 'Modo claro' : 'Modo oscuro'">
                <svg x-show="!darkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-eyebrow">
                    <span class="hero-eyebrow-dot"></span>
                    Red de distribución · México
                </div>
                <h1 class="hero-title">
                    Encuentra tu<br>sucursal <em>más cercana</em>
                </h1>
                <p class="hero-desc">
                    Todas nuestras sucursales con ubicación exacta,
                    horarios y contacto directo.
                </p>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-n" x-text="sucursales.length"></div>
                    <div class="hero-stat-l">Sucursales</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-n" x-text="abiertas"></div>
                    <div class="hero-stat-l">Abiertas ahora</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CONTENIDO ── --}}
    <main class="body">

        {{-- Buscador --}}
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input class="search-input" type="text"
                   placeholder="Buscar por nombre, colonia o ciudad…"
                   x-model="busqueda">
            <span class="search-badge"
                  x-show="busqueda.trim()"
                  x-text="filtradas.length + ' resultado' + (filtradas.length !== 1 ? 's' : '')">
            </span>
        </div>

        {{-- Lista --}}
        <div class="lista">

            {{-- Empty state --}}
            <div x-show="filtradas.length === 0" class="empty">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3>Sin resultados</h3>
                <p>No encontramos sucursales para "<span x-text="busqueda"></span>"</p>
            </div>

            <template x-for="(s, i) in filtradas" :key="s.id_usuario">
                <div class="card"
                     :class="{ 'activa': activa === s.id_usuario }"
                     :style="'animation-delay:' + (i * 0.05) + 's'"
                     @click="toggle(s)">

                    {{-- ── Columna mapa ── --}}
                    <div class="card-map">

                        {{-- Placeholder --}}
                        <div class="map-ph" :class="{ 'loaded': cargados[s.id_usuario] }">
                            <div class="map-ph-inner">
                                <div class="map-ph-pin"></div>
                                <span class="map-ph-text">Cargando mapa</span>
                            </div>
                        </div>

                        {{-- Google Maps Embed gratuito, sin API key --}}
                        <iframe
                            :src="'https://maps.google.com/maps?q=' + s.lat + ',' + s.lng + '&z=15&output=embed&hl=es'"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            @load="cargados[s.id_usuario] = true"
                            tabindex="-1"
                            :title="'Mapa de ' + (s.nombre_sucursal || s.nombre_usuario)">
                        </iframe>

                        {{-- Overlay captura click --}}
                        <div class="card-map-overlay"></div>

                        {{-- Número --}}
                        <div class="card-map-num" x-text="(i + 1) + ' / ' + filtradas.length"></div>

                        {{-- Status --}}
                        <div class="map-status"
                             :class="estaAbierto(s) ? 'open' : 'closed'"
                             x-text="estaAbierto(s) ? '● Abierto' : '○ Cerrado'">
                        </div>

                    </div>

                    {{-- ── Columna info ── --}}
                    <div class="card-info">

                        <h2 class="card-name" x-text="s.nombre_sucursal || s.nombre_usuario"></h2>
                        <p class="card-addr" x-text="s.direccion"></p>

                        <div class="card-divider"></div>

                        {{-- Pills --}}
                        <div class="card-meta">
                            <template x-if="s.telefono">
                                <span class="meta-pill">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span x-text="s.telefono"></span>
                                </span>
                            </template>
                            <template x-if="s.horario_apertura && s.horario_cierre">
                                <span class="meta-pill">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="s.horario_apertura + ' – ' + s.horario_cierre"></span>
                                </span>
                            </template>
                        </div>

                        {{-- Coordenadas --}}
                        <div class="coords-row" @click.stop>
                            <div class="coords-dot"></div>
                            <span class="coords-val"
                                  x-text="parseFloat(s.lat).toFixed(6) + ',  ' + parseFloat(s.lng).toFixed(6)">
                            </span>
                            <button class="coords-btn"
                                    @click="copiar(s)"
                                    x-text="copiado === s.id_usuario ? '✓ Copiado' : 'Copiar'">
                            </button>
                        </div>

                        {{-- Acciones --}}
                        <div class="card-actions" @click.stop>
                            <a :href="'https://maps.google.com/?q=' + s.lat + ',' + s.lng"
                               target="_blank"
                               class="btn-p">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Cómo llegar
                            </a>
                            <template x-if="s.telefono">
                                <a :href="'tel:' + s.telefono" class="btn-s">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Llamar
                                </a>
                            </template>
                        </div>

                    </div>
                </div>
            </template>

        </div>
    </main>

    {{-- ── FOOTER ── --}}
    <footer class="foot">
        <span class="foot-copy">© {{ date('Y') }} ArrowK · Todos los derechos reservados</span>
        <a href="/" class="foot-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al inicio
        </a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        const SUCURSALES_DATA = @json($sucursales);

        function catalogo() {
            return {
                sucursales : SUCURSALES_DATA,
                busqueda   : '',
                activa     : null,
                copiado    : null,
                cargados   : {},
                darkMode   : false,

                init: function () {
                    // Leer preferencia guardada o del sistema
                    var saved = localStorage.getItem('arrowk-theme');
                    if (saved) {
                        this.darkMode = saved === 'dark';
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }
                },

                guardarTema: function () {
                    localStorage.setItem('arrowk-theme', this.darkMode ? 'dark' : 'light');
                },

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

                toggle: function (s) {
                    this.activa = (this.activa === s.id_usuario) ? null : s.id_usuario;
                },

                copiar: function (s) {
                    var self = this;
                    var texto = parseFloat(s.lat).toFixed(6) + ', ' + parseFloat(s.lng).toFixed(6);
                    navigator.clipboard.writeText(texto).then(function () {
                        self.copiado = s.id_usuario;
                        setTimeout(function () { self.copiado = null; }, 2000);
                    });
                },

                estaAbierto: function (s) {
                    if (!s.horario_apertura || !s.horario_cierre) return true;
                    var now  = new Date();
                    var mins = now.getHours() * 60 + now.getMinutes();
                    var p    = function (t) {
                        var x = t.split(':');
                        return parseInt(x[0]) * 60 + parseInt(x[1] || 0);
                    };
                    return mins >= p(s.horario_apertura) && mins < p(s.horario_cierre);
                },
            };
        }
    </script>

</body>
</html>