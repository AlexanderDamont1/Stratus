<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configura tu sucursal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
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
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            outline: none;
            background: transparent;
            color: inherit;
            transition: all 0.2s ease;
        }
        mapbox-search-box::part(input):focus {
            border-color: #6b7280;
            box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.2);
        }
        .dark mapbox-search-box::part(input) {
            border-color: #4b5563;
            background-color: #1f2937;
            color: #f9fafb;
        }

        /* ── Pin fijo en el centro del mapa ──────────────── */
        .mapa-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .mapa-wrapper.activo::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: calc(50% - 2px);
            width: 10px;
            height: 5px;
            background: rgba(0,0,0,0.25);
            border-radius: 50%;
            transform: translateX(-50%);
            pointer-events: none;
            z-index: 10;
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

        #mapa-container-inner {
            height: 100%;
        }

        @media (min-width: 1024px) {
            .mapa-wrapper {
                flex: 1;
                min-height: 0;
            }
            .tarjeta-ubicacion {
                height: 580px;
            }
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* ── Mobile: mapa fijo a pantalla completa, ~75svh ── */
        .mapa-mobile-fijo {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 75svh;
            z-index: 0;
        }
        @media (min-width: 1024px) {
            .mapa-mobile-fijo {
                position: static;
                height: auto;
            }
        }

        /* ── Mobile: sheet inferior, colapsada vs expandida ── */
        .sheet-mobile {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 20;
            transition: height 0.3s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .sheet-mobile.colapsada { height: auto; }
        .sheet-mobile.expandida { height: 90svh; top: auto; }
        @media (min-width: 1024px) {
            .sheet-mobile {
                position: static;
                height: auto !important;
            }
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900
             lg:min-h-[100svh] lg:flex lg:items-center lg:justify-center lg:px-6 lg:py-8
             overflow-x-hidden"
      x-data="ubicacionSetup('{{ config('services.mapbox.token') }}')">

<div class="w-full lg:max-w-7xl lg:mx-auto">

    {{-- ══════════════════════════════════════════════════
         MOBILE — header flotante + mapa fijo + sheet
         (oculto en desktop)
    ══════════════════════════════════════════════════ --}}
    <div class="lg:hidden">

        {{-- Espaciador: empuja el contenido bajo el mapa fijo --}}
        <div style="height: 75svh;"></div>

        {{-- Mapa fijo de fondo --}}
        <div class="mapa-mobile-fijo">
            <div x-ref="mapaWrapperMobile"
                 class="mapa-wrapper !flex-none h-full"
                 :class="{ 'activo': direccion }">

                <svg class="pin-centro" viewBox="0 0 32 42" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 0C7.163 0 0 7.163 0 16c0 10.627 14.087 24.502 15.29 25.698a1 1 0 001.42 0C17.913 40.502 32 26.627 32 16 32 7.163 24.837 0 16 0z"
                          fill="#111827"/>
                    <circle cx="16" cy="16" r="6" fill="white"/>
                </svg>

                <div x-ref="mapaContainerMobile" class="w-full h-full"></div>
            </div>

            {{-- Header flotante: logo + badge unidos, esquina superior izquierda --}}
            <div class="absolute top-0 left-0 right-0 z-10
                        px-5 pt-[max(1.25rem,env(safe-area-inset-top))]">
                <div class="inline-flex items-center gap-2.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm
                            rounded-full pl-2 pr-1.5 py-1.5 shadow-sm">
                    <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                         class="h-5 w-auto object-contain dark:hidden" />
                    <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK"
                         class="h-5 w-auto object-contain hidden dark:block" />

                    <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full
                                 bg-sky-100 text-sky-800
                                 dark:bg-sky-800/30 dark:text-sky-400">
                        Configuración
                    </span>
                </div>
            </div>
        </div>

        {{-- Sheet inferior (colapsada / expandida) --}}
        <div class="sheet-mobile"
             :class="buscadorExpandido ? 'expandida' : 'colapsada'">
            <div class="bg-white dark:bg-gray-800 h-full flex flex-col
                        rounded-t-3xl shadow-[0_-4px_24px_rgba(0,0,0,0.08)]
                        border-t border-gray-100 dark:border-gray-700">

                {{-- Manija --}}
                <button type="button"
                        x-show="buscadorExpandido" x-cloak
                        @click="cerrarBuscador()"
                        class="w-full flex justify-center pt-3 pb-1">
                    <span class="w-9 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                </button>
                <div x-show="!buscadorExpandido" class="w-full flex justify-center pt-3 pb-1">
                    <span class="w-9 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                </div>

                {{-- Contenido colapsado: título + buscador + submit --}}
                <div x-show="!buscadorExpandido" class="px-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] space-y-4">
                    <div class="text-center space-y-1 pt-1">
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Fija tu sucursal
                        </h1>
                        <p class="text-sm text-gray-400">
                            <span x-show="!direccion">Busca tu dirección o arrastra el mapa</span>
                            <span x-show="direccion" x-cloak>Arrastra el mapa para ajustar el pin</span>
                        </p>
                    </div>

                    <div class="w-full flex items-center gap-3 border border-gray-200 dark:border-gray-700
                                rounded-xl pl-4 pr-2 py-1">
                        <button type="button" @click="abrirBuscador()"
                                class="flex-1 min-w-0 flex items-center gap-3 py-2.5 text-left">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <span class="text-sm truncate min-w-0"
                                  :class="direccion ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400'"
                                  x-text="direccion || 'Busca tu dirección'"></span>
                        </button>

                        <button type="button" x-show="direccion" x-cloak
                                @click="limpiar()"
                                class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                                       text-gray-400 hover:text-gray-600 dark:hover:text-gray-200
                                       hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('ubicacion.guardar') }}">
                        @csrf
                        <input type="hidden" name="direccion" :value="direccion">
                        <input type="hidden" name="lat"       :value="lat">
                        <input type="hidden" name="lng"       :value="lng">
                        <input type="hidden" name="place_id"  :value="placeId">

                        <button type="submit" :disabled="!direccion"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5
                                   bg-gray-900 dark:bg-white text-white dark:text-black
                                   text-sm font-medium rounded-xl transition-all duration-150
                                   disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                            <span x-show="!direccion">Selecciona una ubicación</span>
                            <span x-show="direccion" x-cloak>Confirmar y entrar →</span>
                        </button>
                    </form>
                </div>

                {{-- Contenido expandido: buscador a pantalla completa --}}
                <div x-show="buscadorExpandido" x-cloak class="flex-1 min-h-0 flex flex-col px-5 pb-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Busca tu dirección
                    </p>
                    <mapbox-search-box
                        x-ref="searchBoxMobile"
                        access-token="{{ config('services.mapbox.token') }}"
                        placeholder="Av. Insurgentes Sur, CDMX..."
                        country="MX"
                        language="es"
                        proximity="-99.1332,19.4326"
                    ></mapbox-search-box>

                    <p class="text-xs text-gray-400 mt-4">
                        Escribe para ver sugerencias, o cierra y arrastra el mapa para ajustar manualmente.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         DESKTOP — grid 2 columnas (oculto en mobile)
    ══════════════════════════════════════════════════ --}}
    <div class="hidden lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">

        {{-- COLUMNA IZQUIERDA — Marca + pasos + usuario --}}
        <div class="text-left space-y-6 lg:justify-self-center lg:max-w-sm">

            <div class="flex items-center gap-3">
                <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK"
                     class="h-14 w-auto object-contain dark:hidden" />
                <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK"
                     class="h-14 w-auto object-contain hidden dark:block" />

                <span class="inline-block px-4 py-1.5 text-xs font-semibold rounded-full
                             bg-sky-100 text-sky-800
                             dark:bg-sky-800/30 dark:text-sky-400">
                    Configuración
                </span>
            </div>

            <div class="space-y-2">
                <h1 class="text-3xl font-light text-gray-900 dark:text-white leading-tight">
                    Configura tu sucursal
                </h1>
                <p class="text-base text-gray-400 leading-relaxed max-w-sm">
                    Necesitamos la ubicación física de tu sucursal para activar
                    todas las funciones del panel.
                </p>
            </div>

            {{-- Pasos --}}
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                                 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 text-left">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            Ubica tu sucursal
                        </p>
                        <p class="text-xs text-gray-400">
                            Busca tu dirección y ajusta el pin en el mapa
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                                 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div class="min-w-0 text-left">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            Confirma y entra
                        </p>
                        <p class="text-xs text-gray-400">
                            Tu panel quedará activo de inmediato
                        </p>
                    </div>
                </div>
            </div>

            {{-- Usuario + logout --}}
            <div class="flex items-center gap-3 pt-6 mt-2 border-t border-gray-200 dark:border-gray-700">
                <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                            flex items-center justify-center shrink-0">
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr(Auth::user()->nombre_usuario, 0, 1)) }}{{ strtoupper(substr(Auth::user()->nombre_usuario, strrpos(Auth::user()->nombre_usuario, ' ') + 1, 1)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                        {{ Auth::user()->nombre_usuario }}
                    </p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->correo }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="text-xs text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">
                        Salir
                    </button>
                </form>
            </div>
        </div>

        {{-- COLUMNA DERECHA — Tarjeta: buscador + mapa + submit --}}
        <div class="space-y-6">
            <div class="tarjeta-ubicacion bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                        dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">

                {{-- Buscador + selector de estilo --}}
                <div class="px-6 pt-6 pb-4 space-y-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
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

                {{-- Mapa --}}
                <div class="flex flex-col lg:flex-1 lg:min-h-0">
                    <div x-ref="mapaWrapper"
                         class="mapa-wrapper"
                         :class="{ 'activo': direccion }">

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

                    {{-- Footer mapa: estado de dirección --}}
                    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700
                                flex items-center justify-between gap-3 min-h-[48px]">

                        
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
                <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-2 min-h-[32px] flex items-center">
                    <p x-show="direccion" x-cloak
                       class="text-xs text-gray-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
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
                      class="px-6 py-5 border-t border-gray-100 dark:border-gray-700">
                    @csrf
                    <input type="hidden" name="direccion" :value="direccion">
                    <input type="hidden" name="lat"       :value="lat">
                    <input type="hidden" name="lng"       :value="lng">
                    <input type="hidden" name="place_id"  :value="placeId">

                    <button type="submit" :disabled="!direccion"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 lg:py-3.5
                               bg-gray-900 dark:bg-white text-white dark:text-black
                               hover:bg-gray-800 dark:hover:bg-gray-100
                               text-sm font-medium rounded-xl transition-all duration-150
                               disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <span x-show="!direccion">Selecciona una ubicación</span>
                        <span x-show="direccion" x-cloak>Confirmar y entrar →</span>
                    </button>
                </form>
            </div>

            {{-- Footer nota --}}
            <div class="flex items-start gap-2 px-2">
                <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs text-gray-400 leading-relaxed">
                    Esta información solo se usa para ubicar tu sucursal en el mapa del panel.
                </span>
            </div>
        </div>{{-- /columna derecha --}}
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('ubicacionSetup', function (mapboxToken) {
            return {
                direccion        : '',
                lat              : '',
                lng              : '',
                placeId          : '',
                actualizando     : false,
                mapa             : null,
                mapaMobile       : null,
                estiloActual     : 'streets-v12',
                geocodeTimer     : null,
                buscadorExpandido: false,
                esMobile         : window.matchMedia('(max-width: 1023px)').matches,

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
                        if (self.esMobile) {
                            self.iniciarMapa(mapboxToken, self.$refs.mapaContainerMobile, 'mapaMobile');
                            self.escucharSearchBox(self.$refs.searchBoxMobile);
                        } else {
                            self.iniciarMapa(mapboxToken, self.$refs.mapaContainer, 'mapa');
                            self.escucharSearchBox(self.$refs.searchBox);
                        }
                    });
                },

                iniciarMapa: function (token, contenedor, propiedad) {
                    var self = this;
                    mapboxgl.accessToken = token;

                    self[propiedad] = new mapboxgl.Map({
                        container : contenedor,
                        style     : 'mapbox://styles/mapbox/' + self.estiloActual,
                        center    : self.CDMX,
                        zoom      : 11,
                    });

                    self[propiedad].addControl(
                        new mapboxgl.NavigationControl({ showCompass: false }),
                        'bottom-right'
                    );

                    var wrapper = self.esMobile ? self.$refs.mapaWrapperMobile : self.$refs.mapaWrapper;

                    self[propiedad].on('dragstart', function () {
                        if (self.direccion) wrapper.classList.add('arrastrando');
                    });
                    self[propiedad].on('zoomstart', function () {
                        if (self.direccion) wrapper.classList.add('arrastrando');
                    });

                    self[propiedad].on('moveend', function () {
                        wrapper.classList.remove('arrastrando');
                        if (!self.direccion) return;

                        var center = self[propiedad].getCenter();
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
                    if (self.mapa) self.mapa.setStyle('mapbox://styles/mapbox/' + id);
                    if (self.mapaMobile) self.mapaMobile.setStyle('mapbox://styles/mapbox/' + id);
                },

                escucharSearchBox: function (searchBoxEl) {
                    var self = this;
                    if (!searchBoxEl) return;

                    searchBoxEl.addEventListener('retrieve', function (e) {
                        var feature = e.detail?.features?.[0];
                        if (!feature) return;

                        var coords = feature.geometry.coordinates;
                        var mapaActivo = self.esMobile ? self.mapaMobile : self.mapa;

                        self.lat       = coords[1];
                        self.lng       = coords[0];
                        self.direccion = feature.properties.full_address
                                      || feature.properties.place_name
                                      || '';
                        self.placeId   = feature.properties.mapbox_id || '';

                        if (mapaActivo) mapaActivo.flyTo({ center: coords, zoom: 15 });
                        self.buscadorExpandido = false;
                    });
                },

                abrirBuscador: function () {
                    var self = this;
                    self.buscadorExpandido = true;
                    self.$nextTick(function () {
                        var input = self.$refs.searchBoxMobile;
                        if (input) input.focus();
                    });
                },

                cerrarBuscador: function () {
                    this.buscadorExpandido = false;
                },

                limpiar: function () {
                    var self = this;
                    clearTimeout(self.geocodeTimer);

                    self.direccion    = '';
                    self.lat          = '';
                    self.lng          = '';
                    self.placeId      = '';
                    self.actualizando = false;

                    var mapaActivo = self.esMobile ? self.mapaMobile : self.mapa;
                    if (mapaActivo) mapaActivo.flyTo({ center: self.CDMX, zoom: 11 });

                    if (self.$refs.searchBox) self.$refs.searchBox.value = '';
                    if (self.$refs.searchBoxMobile) self.$refs.searchBoxMobile.value = '';
                }
            };
        });
    });
</script>

</body>
</html>