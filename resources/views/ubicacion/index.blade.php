<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configura tu sucursal — ArrowK</title>
    @vite(['resources/css/app.css'])

    {{-- Mapbox GL JS --}}
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js"></script>

    {{-- Mapbox Search JS (autocomplete) --}}
    <script id="search-js" defer
        src="https://api.mapbox.com/search-js/v1.0.0-beta.21/web.js">
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* ── Mapbox SearchBox ─────────────────────────────── */
        mapbox-search-box { width: 100%; }
        mapbox-search-box::part(input) {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            outline: none;
            background: transparent;
            color: inherit;
        }
        mapbox-search-box::part(input):focus {
            box-shadow: 0 0 0 2px #111827;
        }
        .dark mapbox-search-box::part(input) {
            border-color: #4b5563;
            background-color: #374151;
            color: #f9fafb;
        }

        /* ── Pin fijo en el centro del mapa ──────────────── */
        .mapa-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .mapa-wrapper.activo::after,
        .mapa-wrapper.activo::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 10;
        }
        .mapa-wrapper.activo::after {
            bottom: calc(50% - 2px);
            width: 10px;
            height: 5px;
            background: rgba(0,0,0,0.25);
            border-radius: 50%;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .pin-centro {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -100%);
            width: 32px;
            height: 42px;
            pointer-events: none;
            z-index: 10;
            display: none;
            transition: transform 0.15s ease;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.35));
        }
        .mapa-wrapper.activo .pin-centro {
            display: block;
        }
        .mapa-wrapper.arrastrando .pin-centro {
            transform: translate(-50%, -115%);
        }
        .mapa-wrapper.arrastrando::after {
            transform: translateX(-50%) scale(0.7);
            opacity: 0.5;
        }

        /* ── Altura del mapa por breakpoint ──────────────── */
        /* Mobile: altura fija para que el mapa sea visible */
        #mapa-container-inner {
            height: 240px;
        }
        /* Tablet y desktop: ocupa todo el espacio disponible */
        @media (min-width: 1024px) {
            #mapa-container-inner {
                height: 100%;
                flex: 1;
            }
            .mapa-wrapper {
                flex: 1;
            }
        }

        /* ── Scroll suave en mobile ───────────────────────── */
        @media (max-width: 1023px) {
            body {
                align-items: flex-start;
                overflow-y: auto;
                padding: 0;
            }
        }
    </style>
</head>

{{--
    Mobile  : columna única, sin padding lateral en body, scroll vertical
    Desktop : grid de 2 columnas, centrado, con padding
--}}
<body class="min-h-full bg-gray-100 dark:bg-gray-950
             flex items-start lg:items-center justify-center
             lg:p-6">

<div class="w-full max-w-5xl"
     x-data="ubicacionSetup('{{ config('services.mapbox.token') }}')">

    <div class="grid grid-cols-1 lg:grid-cols-2
                lg:min-h-[580px]
                lg:rounded-xl lg:overflow-hidden lg:shadow-sm
                lg:border lg:border-gray-200 lg:dark:border-gray-700">

        {{-- ══════════════════════════════════════════════════
             COLUMNA IZQUIERDA — Info + pasos + usuario
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 flex flex-col justify-between
                    px-6 py-8 lg:px-10 lg:py-10">

            <div>
                {{-- Marca --}}
                <div class="mb-8 lg:mb-10">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">ArrowK</p>
                    <p class="text-xs text-gray-400 mt-0.5">Panel de administración</p>
                </div>

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2
                            bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-700
                            text-amber-700 dark:text-amber-400
                            text-xs font-medium px-3 py-1.5 rounded-full mb-5 lg:mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                    Acción requerida
                </div>

                <h1 class="text-xl lg:text-2xl font-semibold
                           text-gray-900 dark:text-white leading-snug mb-2 lg:mb-3">
                    Configura tu sucursal
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-7 lg:mb-10">
                    Necesitamos la ubicación física de tu sucursal para activar
                    todas las funciones del panel.
                </p>

                {{-- Pasos --}}
                <div class="space-y-4 lg:space-y-5">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700
                                    border border-gray-200 dark:border-gray-600
                                    flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                Ubica tu sucursal
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Busca tu dirección y ajusta el pin en el mapa
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700
                                    border border-gray-200 dark:border-gray-600
                                    flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                Confirma y entra
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Tu panel quedará activo de inmediato
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usuario + logout --}}
            <div class="flex items-center gap-3 pt-6 mt-6 lg:pt-8 lg:mt-8
                        border-t border-gray-100 dark:border-gray-700">
                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600
                            flex items-center justify-center shrink-0">
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}{{ strtoupper(substr(Auth::user()->nombre_usuario, strrpos(Auth::user()->nombre_usuario, ' ') + 1, 1)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">
                        {{ Auth::user()->nombre_usuario }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->correo }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="text-xs text-gray-400 hover:text-red-500
                               dark:hover:text-red-400 transition">
                        Salir
                    </button>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             COLUMNA DERECHA — Buscador + Mapa + Submit
        ══════════════════════════════════════════════════ --}}
        <div class="bg-gray-50 dark:bg-gray-900 flex flex-col
                    border-t lg:border-t-0 lg:border-l
                    border-gray-200 dark:border-gray-700">

            {{-- Buscador + selector de estilo --}}
            <div class="px-4 pt-4 pb-3 lg:px-6 lg:pt-6 lg:pb-4
                        bg-white dark:bg-gray-800
                        border-b border-gray-200 dark:border-gray-700 space-y-3">

                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400
                                  uppercase tracking-wider mb-2">
                        Busca tu dirección
                    </label>
                    <mapbox-search-box
                        x-ref="searchBox"
                        access-token="{{ config('services.mapbox.token') }}"
                        placeholder="Av. Insurgentes Sur, CDMX..."
                        country="MX"
                        language="es"
                        proximity="-99.1332,19.4326"
                    ></mapbox-search-box>
                </div>

                {{-- Selector de estilo: scroll horizontal en mobile --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-0.5 no-scrollbar">
                    <span class="text-xs text-gray-400 shrink-0">Vista:</span>
                    <template x-for="estilo in estilos" :key="estilo.id">
                        <button type="button"
                            @click="cambiarEstilo(estilo.id)"
                            :class="estiloActual === estilo.id
                                ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="text-xs px-2.5 py-1 rounded-full font-medium transition shrink-0">
                            <span x-text="estilo.label"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- ── Bloque del mapa ── --}}
            <div class="flex flex-col lg:flex-1">

                {{-- Wrapper relativo para el pin CSS fijo --}}
                <div x-ref="mapaWrapper"
                     class="mapa-wrapper"
                     :class="{ 'activo': direccion }">

                    {{-- Pin SVG fijo en el centro --}}
                    <svg class="pin-centro" viewBox="0 0 32 42" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 0C7.163 0 0 7.163 0 16c0 10.627 14.087 24.502 15.29 25.698a1 1 0 001.42 0C17.913 40.502 32 26.627 32 16 32 7.163 24.837 0 16 0z"
                              fill="#111827"/>
                        <circle cx="16" cy="16" r="6" fill="white"/>
                    </svg>

                    <div id="mapa-container-inner"
                         x-ref="mapaContainer"
                         class="w-full"></div>
                </div>

                {{-- Footer mapa --}}
                <div class="px-4 py-3 bg-white dark:bg-gray-800
                            border-t border-gray-200 dark:border-gray-700
                            flex items-center justify-between gap-3 min-h-[44px]">

                    <p x-show="!direccion"
                       class="text-xs text-gray-400 italic">
                        Selecciona una dirección para ver el pin
                    </p>

                    {{-- Dirección confirmada --}}
                    <span x-show="direccion && !actualizando" x-cloak
                          class="flex items-center gap-1.5 text-xs
                                 text-gray-700 dark:text-gray-300 min-w-0">
                        <svg class="w-3.5 h-3.5 shrink-0 text-green-500"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"/>
                        </svg>
                        <span x-text="direccion" class="truncate"></span>
                    </span>

                    {{-- Actualizando dirección (reverse geocoding) --}}
                    <span x-show="actualizando" x-cloak
                          class="flex items-center gap-1.5 text-xs text-gray-400 min-w-0">
                        <svg class="w-3.5 h-3.5 shrink-0 animate-spin"
                             fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        Actualizando dirección...
                    </span>

                    <button x-show="direccion && !actualizando" x-cloak
                            type="button" @click="limpiar()"
                            class="shrink-0 text-xs text-gray-400
                                   hover:text-red-500 transition font-medium">
                        Cambiar
                    </button>
                </div>
            </div>

            {{-- Hint + error --}}
            <div class="bg-gray-50 dark:bg-gray-900
                        border-t border-gray-100 dark:border-gray-800
                        px-4 lg:px-6 py-2 min-h-[32px]">
                <p x-show="direccion" x-cloak
                   class="text-xs text-gray-400 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mueve el mapa para ajustar la posición exacta
                </p>
                @error('lat')
                    <p class="text-xs text-red-500">
                        Por favor selecciona una ubicación en el mapa.
                    </p>
                @enderror
            </div>

            {{-- Submit --}}
            <form method="POST" action="{{ route('ubicacion.guardar') }}"
                  class="px-4 py-4 lg:px-6
                         bg-white dark:bg-gray-800
                         border-t border-gray-200 dark:border-gray-700">
                @csrf
                <input type="hidden" name="direccion" :value="direccion">
                <input type="hidden" name="lat"       :value="lat">
                <input type="hidden" name="lng"       :value="lng">
                <input type="hidden" name="place_id"  :value="placeId">

                <button type="submit" :disabled="!direccion"
                    class="w-full bg-gray-900 dark:bg-white dark:text-gray-900 text-white
                           py-3 lg:py-2.5 rounded-lg text-sm font-semibold
                           hover:opacity-90 transition
                           disabled:opacity-40 disabled:cursor-not-allowed">
                    <span x-show="!direccion">Selecciona una ubicación</span>
                    <span x-show="direccion" x-cloak>Confirmar y entrar →</span>
                </button>
            </form>

        </div>{{-- /columna derecha --}}
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('ubicacionSetup', function (mapboxToken) {
            return {
                direccion    : '',
                lat          : '',
                lng          : '',
                placeId      : '',
                actualizando : false,
                mapa         : null,
                estiloActual : 'streets-v12',
                geocodeTimer : null,

                estilos: [
                    { id: 'streets-v12',          label: 'Calles'    },
                    { id: 'satellite-streets-v12', label: 'Satélite' },
                    { id: 'light-v11',             label: 'Claro'     },
                    { id: 'dark-v11',              label: 'Oscuro'    },
                ],

                CDMX: [-99.1332, 19.4326],

                init: function () {
                    var self = this;
                    self.$nextTick(function () {
                        self.iniciarMapa(mapboxToken);
                        self.escucharSearchBox();
                    });
                },

                iniciarMapa: function (token) {
                    var self = this;
                    mapboxgl.accessToken = token;

                    self.mapa = new mapboxgl.Map({
                        container : self.$refs.mapaContainer,
                        style     : 'mapbox://styles/mapbox/' + self.estiloActual,
                        center    : self.CDMX,
                        zoom      : 11,
                    });

                    self.mapa.addControl(
                        new mapboxgl.NavigationControl({ showCompass: false }),
                        'bottom-right'
                    );

                    var wrapper = self.$refs.mapaWrapper;

                    self.mapa.on('dragstart', function () {
                        if (self.direccion) wrapper.classList.add('arrastrando');
                    });
                    self.mapa.on('zoomstart', function () {
                        if (self.direccion) wrapper.classList.add('arrastrando');
                    });

                    self.mapa.on('moveend', function () {
                        wrapper.classList.remove('arrastrando');
                        if (!self.direccion) return;

                        var center = self.mapa.getCenter();
                        self.lat = center.lat;
                        self.lng = center.lng;

                        clearTimeout(self.geocodeTimer);
                        self.geocodeTimer = setTimeout(function () {
                            self.reverseGeocode(center.lng, center.lat, token);
                        }, 400);
                    });
                },

                reverseGeocode: function (lng, lat, token) {
                    var self = this;
                    self.actualizando = true;

                    var url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'
                            + lng + ',' + lat
                            + '.json?access_token=' + token
                            + '&language=es&types=address,poi,place&limit=1';

                    fetch(url)
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            var feature = data.features && data.features[0];
                            if (feature) {
                                self.direccion = feature.place_name || self.direccion;
                                self.placeId   = feature.id         || self.placeId;
                            }
                        })
                        .catch(function () { /* mantener la dirección anterior */ })
                        .finally(function () { self.actualizando = false; });
                },

                cambiarEstilo: function (id) {
                    var self = this;
                    if (self.estiloActual === id) return;
                    self.estiloActual = id;
                    self.mapa.setStyle('mapbox://styles/mapbox/' + id);
                },

                escucharSearchBox: function () {
                    var self = this;

                    self.$refs.searchBox.addEventListener('retrieve', function (e) {
                        var feature = e.detail?.features?.[0];
                        if (!feature) return;

                        var coords = feature.geometry.coordinates;

                        self.lat       = coords[1];
                        self.lng       = coords[0];
                        self.direccion = feature.properties.full_address
                                      || feature.properties.place_name
                                      || '';
                        self.placeId   = feature.properties.mapbox_id || '';

                        self.mapa.flyTo({ center: coords, zoom: 17 });
                    });
                },

                limpiar: function () {
                    var self = this;
                    clearTimeout(self.geocodeTimer);

                    self.direccion    = '';
                    self.lat          = '';
                    self.lng          = '';
                    self.placeId      = '';
                    self.actualizando = false;

                    if (self.mapa) self.mapa.flyTo({ center: self.CDMX, zoom: 11 });
                    if (self.$refs.searchBox) self.$refs.searchBox.value = '';
                }
            };
        });
    });
</script>

</body>
</html>