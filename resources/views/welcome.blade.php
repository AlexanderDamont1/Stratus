<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ArrowK</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-figtree bg-white dark:bg-gray-900 text-gray-900 dark:text-white antialiased overflow-x-hidden">

  <!-- NAV -->
  <nav id="nav" class="fixed top-0 left-0 right-0 z-50 px-6 h-14 flex items-center justify-between transition-colors duration-300 border-b border-transparent bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm">
    <a href="/" class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
      <img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK" class="h-8 w-auto dark:hidden">
      <img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK" class="h-8 w-auto hidden dark:block">
    </a>
    <ul class="hidden md:flex items-center gap-7 text-sm text-gray-500 dark:text-gray-400">
      <li><a href="#problema" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-200">El problema</a></li>
      <li><a href="#plataforma" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Plataforma</a></li>
      <li><a href="#contacto" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-200">Contacto</a></li>
    </ul>
    <div class="hidden md:flex items-center gap-3">
      @auth
      <a href="{{ url('/dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 text-sm">Ir al panel</a>
      @else
      <a href="{{ route('login') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 text-sm">Iniciar sesión</a>
      @endauth
      <a href="#contacto" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-4 py-1.5 rounded-full text-sm font-medium hover:bg-gray-800 dark:hover:bg-white transition-all duration-200 transform hover:scale-105 active:scale-95">Solicitar demo</a>
    </div>
    <!-- Burger -->
    <button id="burger" class="md:hidden flex flex-col gap-1 p-2 border border-gray-200 dark:border-gray-700 rounded-lg transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500" onclick="toggleMenu()">
      <span class="block w-5 h-0.5 bg-gray-600 dark:bg-gray-400 transition-all duration-200"></span>
      <span class="block w-5 h-0.5 bg-gray-600 dark:bg-gray-400 transition-all duration-200"></span>
      <span class="block w-5 h-0.5 bg-gray-600 dark:bg-gray-400 transition-all duration-200"></span>
    </button>
  </nav>

  <!-- MOBILE MENU -->
  <div id="mob-menu" class="fixed inset-0 z-40 bg-white dark:bg-gray-900 p-6 pt-20 flex flex-col transform -translate-y-full transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] pointer-events-none">
    <a href="#problema" class="py-4 border-b border-white dark:border-gray-800 text-gray-700 dark:text-gray-300 flex justify-between items-center transition-colors duration-200 hover:text-gray-900 dark:hover:text-white" onclick="closeMenu()">El problema <span class="text-gray-400 transition-transform duration-200 group-hover:translate-x-1">→</span></a>
    <a href="#plataforma" class="py-4 border-b border-white dark:border-gray-800 text-gray-700 dark:text-gray-300 flex justify-between items-center transition-colors duration-200 hover:text-gray-900 dark:hover:text-white" onclick="closeMenu()">Plataforma <span class="text-gray-400 transition-transform duration-200 group-hover:translate-x-1">→</span></a>
    <a href="#contacto" class="py-4 border-b border-white dark:border-gray-800 text-gray-700 dark:text-gray-300 flex justify-between items-center transition-colors duration-200 hover:text-gray-900 dark:hover:text-white" onclick="closeMenu()">Contacto <span class="text-gray-400 transition-transform duration-200 group-hover:translate-x-1">→</span></a>
    @auth
    <a href="{{ url('/dashboard') }}" class="py-4 border-b border-white dark:border-gray-800 text-gray-700 dark:text-gray-300 flex justify-between items-center transition-colors duration-200 hover:text-gray-900 dark:hover:text-white" onclick="closeMenu()">Ir al panel <span class="text-gray-400 transition-transform duration-200 group-hover:translate-x-1">→</span></a>
    @else
    <a href="{{ route('login') }}" class="py-4 border-b border-white dark:border-gray-800 text-gray-700 dark:text-gray-300 flex justify-between items-center transition-colors duration-200 hover:text-gray-900 dark:hover:text-white" onclick="closeMenu()">Iniciar sesión <span class="text-gray-400 transition-transform duration-200 group-hover:translate-x-1">→</span></a>
    @endauth
    <a href="#contacto" class="mt-8 w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-3 rounded-xl text-center text-sm font-medium transition-all duration-200 hover:bg-gray-800 dark:hover:bg-white transform hover:scale-[1.02] active:scale-95" onclick="closeMenu()">Solicitar demo</a>
  </div>

  <!-- HERO -->
  <section class="min-h-screen flex items-center px-6 pt-24 pb-16 max-w-3xl mx-auto">
    <div class="w-full">
      <div class="inline-flex items-center gap-2 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-700 rounded-full px-3 py-1 text-xs mb-7 transition-all duration-700 ease-out hover:scale-105 hover:rotate-1 hover:shadow-md">
        <i class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_0_3px_rgba(34,197,94,0.15)] animate-pulse"></i>
        Face beta · Prueba gratis 15 días
      </div>
      <h1 class="text-4xl md:text-5xl font-semibold text-gray-900 dark:text-white leading-[1.05] tracking-tight mb-4 transition-all duration-700 ease-out">
        Tu negocio. <em class="not-italic text-gray-500 dark:text-gray-400">Sin caos.</em>
      </h1>
      <p class="text-base md:text-lg text-gray-500 dark:text-gray-400 leading-relaxed max-w-md mb-8 transition-all duration-700 delay-100 ease-out">
        ArrowK centraliza pedidos, inventario, clientes y garantías.
        Reemplaza WhatsApp y hojas de cálculo con una plataforma
        diseñada para distribuidores de bicicletas eléctricas en México.
      </p>
      <div class="flex flex-wrap items-center gap-3 mb-10">
        <a href="#contacto" class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-sm hover:shadow-md">
          Solicitar demo gratis
          <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </a>
        <a href="#plataforma" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-700 rounded-full text-sm text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 transform hover:scale-105 active:scale-95">Ver plataforma</a>
      </div>
      <div class="flex flex-wrap gap-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
          </svg>POS
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
          </svg>Inventario
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
            <circle cx="9" cy="7" r="4" />
          </svg>Clientes
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>Garantías
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
            <rect x="9" y="3" width="6" height="4" rx="2" />
          </svg>Reparaciones
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:scale-105 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-sm">
          <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
          </svg>Dashboard
        </div>
      </div>
    </div>
  </section>

  <!-- PROBLEMA / SOLUCIÓN -->
  <section id="problema" class="py-16 px-6 bg-white dark:bg-gray-900">
    <div class="max-w-5xl mx-auto">
      <div class="text-center sm:text-left rv opacity-0 translate-y-5 transition-all duration-700 ease-out">
        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Problema · Solución</p>
        <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight mb-2">Del caos informal al <em class="not-italic text-gray-500 dark:text-gray-400">control total</em></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg">Así opera la mayoría de distribuidores hoy. Así operan con ArrowK.</p>
      </div>
      <div class="grid md:grid-cols-2 gap-6 mt-10">
        <!-- Bad -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md rv opacity-0 translate-y-5 transition-all duration-700 delay-100 ease-out">
          <div class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-medium px-3 py-1 rounded-full mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
            Sin ArrowK
          </div>
          <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Operación fragmentada</h3>
          <div class="space-y-3">
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Pedidos por WhatsApp sin confirmación ni trazabilidad</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Inventario en Excel siempre desactualizado</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Garantías sin registro ni control centralizado</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-red-50 dark:bg-red-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Clientes sin historial ni seguimiento efectivo</p>
            </div>
          </div>
        </div>
        <!-- Good -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md rv opacity-0 translate-y-5 transition-all duration-700 delay-200 ease-out">
          <div class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-medium px-3 py-1 rounded-full mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            Con ArrowK
          </div>
          <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Operación centralizada</h3>
          <div class="space-y-3">
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-green-50 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Órdenes digitales con aprobación y trazabilidad completa</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-green-50 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Stock actualizado automáticamente en cada venta</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-green-50 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">Garantías rastreadas por número de serie con alertas</p>
            </div>
            <div class="flex gap-2 transition-all duration-200 hover:translate-x-1">
              <div class="w-5 h-5 rounded bg-green-50 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0 mt-0.5"><svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12" />
                </svg></div>
              <p class="text-sm text-gray-600 dark:text-gray-400">CRM completo con historial y seguimiento por cliente</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PLATAFORMA (previews) -->
  <section id="plataforma" class="py-16 px-6 bg-white dark:bg-gray-900 border-y border-white dark:border-gray-900 shadow-sm">
    <div class="max-w-6xl mx-auto">
      <div class="mb-8 rv opacity-0 translate-y-5 transition-all duration-700 ease-out">
        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Vista del producto</p>
        <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight">Diseñado para <em class="not-italic text-gray-500 dark:text-gray-400">escalar</em></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg">Dashboard completo que refleja la operación real de tu negocio.</p>
      </div>

      <!-- Pestañas -->
      <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 mb-6">
        <button class="tab-btn active flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-900 bg-gray-900 text-white transition-all duration-200 hover:scale-105 hover:shadow-md" data-tab="pedidos">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="8" y1="10" x2="16" y2="10" />
            <line x1="8" y1="14" x2="12" y2="14" />
          </svg>
          Pedidos
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="catalogo">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
          </svg>
          Catálogo
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="garantias">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            <polyline points="9 12 11 14 15 10" />
          </svg>
          Garantías
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="cajas">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="9" width="18" height="12" rx="2" />
            <path d="M3 9V5a2 2 0 012-2h14a2 2 0 012 2v4" />
            <line x1="8" y1="15" x2="16" y2="15" />
          </svg>
          Cajas
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="config">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
          </svg>
          Configuración
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="sucursales">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          Sucursales
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-orange-300 dark:border-orange-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-orange-400 dark:hover:border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30 transition-all duration-200 hover:scale-105" data-tab="corte">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
          </svg>
          Corte
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-red-300 dark:border-red-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-red-400 dark:hover:border-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200 hover:scale-105" data-tab="robo">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 8v4" />
            <path d="M12 16h.01" />
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
          </svg>
          Robo
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-600 transition-all duration-200 hover:scale-105" data-tab="ordenes">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="9" y="2" width="6" height="4" rx="1" />
            <path d="M4 10h16" />
            <path d="M4 14h16" />
            <path d="M4 18h12" />
          </svg>
          Órdenes
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-green-300 dark:border-green-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-green-400 dark:hover:border-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 transition-all duration-200 hover:scale-105" data-tab="ventas">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
          </svg>
          Ventas
        </button>
        <button class="tab-btn flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-blue-300 dark:border-blue-700 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:border-blue-400 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all duration-200 hover:scale-105" data-tab="nuevaventa">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 5v14" />
            <path d="M5 12h14" />
          </svg>
          Nueva Venta
        </button>
      </div>

      <!-- Contenedor de paneles -->
      <div style="font-size: 13px; transform: scale(0.90);  width: 105%;">
        <!-- ===== PANEL: PEDIDOS ===== -->
        <div id="panel-pedidos" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / pedidos</span>
          </div>
          <div class="bg-white dark:bg-gray-900">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pedidos</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Gestiona los pedidos de bicicletas</p>
              </div>
              <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-md text-xs font-medium opacity-60 cursor-not-allowed transition-all duration-200 hover:opacity-80">+ Nuevo Pedido</button>
            </div>
            <div class="grid grid-cols-3 gap-3 p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-900 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Total</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">48</div>
                <div class="text-[10px] text-gray-400 dark:text-gray-500">pedidos registrados</div>
              </div>
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-900 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Esta página</div>
                <div class="text-lg font-semibold text-blue-600">10</div>
                <div class="text-[10px] text-gray-400 dark:text-gray-500">pedidos visibles</div>
              </div>
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Página</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">1 <span class="text-sm text-gray-400 dark:text-gray-500 font-normal">/5</span></div>
                <div class="text-[10px] text-gray-400 dark:text-gray-500">de 5 páginas</div>
              </div>
            </div>
            <div class="flex flex-wrap gap-3 p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 items-end">
              <div class="flex-1 min-w-[140px]">
                <label class="text-[10px] text-gray-400 dark:text-gray-500 block mb-1">Buscar</label>
                <input type="text" placeholder="N° Pedido o Negocio" disabled class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-sm bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
              </div>
              <div class="min-w-[120px]">
                <label class="text-[10px] text-gray-400 dark:text-gray-500 block mb-1">Status</label>
                <select disabled class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-sm bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                  <option>Todos</option>
                  <option>Solicitado</option>
                  <option>Verificando Pago</option>
                  <option>Listo para Entregar</option>
                  <option>Entregado</option>
                </select>
              </div>
              <div class="flex gap-2">
                <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">Filtrar</button>
                <button disabled class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-full text-xs text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-600 dark:hover:text-gray-300">Limpiar</button>
              </div>
            </div>
            <table class="w-full text-sm">
              <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                  <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">N° Pedido</th>
                  <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Negocio</th>
                  <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cantidad</th>
                  <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-4 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white dark:divide-gray-800">
                <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-2.5 font-mono text-xs text-gray-900 dark:text-white">PED-00048</td>
                  <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">Distribuidora Norte</td>
                  <td class="px-4 py-2.5 text-center">3</td>
                  <td class="px-4 py-2.5 text-center"><span class="inline-block px-2 py-0.5 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700 rounded text-[10px] font-medium">Solicitado</span></td>
                  <td class="px-4 py-2.5">09/03/2025</td>
                  <td class="px-4 py-2.5 text-center text-red-600 text-xs font-medium transition-colors duration-200 hover:text-red-800">Eliminar</td>
                </tr>
                <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-2.5 font-mono text-xs text-gray-900 dark:text-white">PED-00047</td>
                  <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">Bici Express</td>
                  <td class="px-4 py-2.5 text-center">5</td>
                  <td class="px-4 py-2.5 text-center"><span class="inline-block px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-700 rounded text-[10px] font-medium">Preparado</span></td>
                  <td class="px-4 py-2.5">08/03/2025</td>
                  <td class="px-4 py-2.5 text-center text-gray-400">—</td>
                </tr>
                <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-2.5 font-mono text-xs text-gray-900 dark:text-white">PED-00046</td>
                  <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">VoltageMX</td>
                  <td class="px-4 py-2.5 text-center">2</td>
                  <td class="px-4 py-2.5 text-center"><span class="inline-block px-2 py-0.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700 rounded text-[10px] font-medium">Entregado</span></td>
                  <td class="px-4 py-2.5">07/03/2025</td>
                  <td class="px-4 py-2.5 text-center text-gray-400">—</td>
                </tr>
                <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-2.5 font-mono text-xs text-gray-900 dark:text-white">PED-00045</td>
                  <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">EcoRide Sur</td>
                  <td class="px-4 py-2.5 text-center">4</td>
                  <td class="px-4 py-2.5 text-center"><span class="inline-block px-2 py-0.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700 rounded text-[10px] font-medium">Entregado</span></td>
                  <td class="px-4 py-2.5">06/03/2025</td>
                  <td class="px-4 py-2.5 text-center text-gray-400">—</td>
                </tr>
                <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <td class="px-4 py-2.5 font-mono text-xs text-gray-900 dark:text-white">PED-00044</td>
                  <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">PowerCycle MX</td>
                  <td class="px-4 py-2.5 text-center">6</td>
                  <td class="px-4 py-2.5 text-center"><span class="inline-block px-2 py-0.5 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700 rounded text-[10px] font-medium">Solicitado</span></td>
                  <td class="px-4 py-2.5">05/03/2025</td>
                  <td class="px-4 py-2.5 text-center text-red-600 text-xs font-medium transition-colors duration-200 hover:text-red-800">Eliminar</td>
                </tr>
              </tbody>
            </table>
            <div class="px-4 py-2.5 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800 text-xs text-gray-400 dark:text-gray-500">
              <span>Mostrando 1–5 de 48</span>
              <div class="flex gap-1">
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">‹</span>
                <span class="px-2.5 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded transition-all duration-200 hover:bg-gray-800 dark:hover:bg-white">1</span>
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">2</span>
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">3</span>
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">4</span>
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">5</span>
                <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">›</span>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: CATÁLOGO ===== -->
        <div id="panel-catalogo" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <!-- Contenido del catálogo (idéntico al anterior pero con animaciones) -->
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / catalogo</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex flex-wrap justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Catálogo de productos</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Gestiona marcas, modelos, colores y voltajes</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-700">5 / 10 marcas</span>
                <button disabled class="px-3 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">+ Nueva marca</button>
              </div>
            </div>
            <div class="space-y-4">
              <!-- Yadea -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">Yadea</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">3 modelos</span>
                  </div>
                  <div class="flex gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Editar</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Modelo</span>
                    <span class="text-xs text-red-600 border border-red-200 dark:border-red-800 px-2 py-0.5 rounded bg-red-50 dark:bg-red-900/30 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Eliminar</span>
                  </div>
                </div>
                <div class="p-4 space-y-3">
                  <div class="grid grid-cols-3 gap-2 text-[10px] text-gray-400 dark:text-gray-500 border-b pb-1">
                    <div>Modelo</div>
                    <div>Colores</div>
                    <div>Voltajes</div>
                  </div>
                  <div class="grid grid-cols-3 gap-2 items-center border-b pb-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                    <div class="flex justify-between items-center"><span class="text-sm text-gray-700 dark:text-gray-300">Ova</span><span class="text-gray-400 text-xs transition-opacity duration-200 hover:opacity-100">✎ ✕</span></div>
                    <div class="flex gap-1">
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-red-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-blue-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-green-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+</span>
                    </div>
                    <div class="flex gap-1"><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">48V</span><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">60V</span><span class="text-xs border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2 py-0.5 text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+ voltaje</span></div>
                  </div>
                  <div class="grid grid-cols-3 gap-2 items-center border-b pb-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                    <div class="flex justify-between items-center"><span class="text-sm text-gray-700 dark:text-gray-300">GB18</span><span class="text-gray-400 text-xs transition-opacity duration-200 hover:opacity-100">✎ ✕</span></div>
                    <div class="flex gap-1">
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-black transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-white transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+</span>
                    </div>
                    <div class="flex gap-1"><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">72V</span><span class="text-xs border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2 py-0.5 text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+ voltaje</span></div>
                  </div>
                  <div class="grid grid-cols-3 gap-2 items-center transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                    <div class="flex justify-between items-center"><span class="text-sm text-gray-700 dark:text-gray-300">Keeness</span><span class="text-gray-400 text-xs transition-opacity duration-200 hover:opacity-100">✎ ✕</span></div>
                    <div class="flex gap-1">
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-gray-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-orange-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+</span>
                    </div>
                    <div class="flex gap-1"><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">48V</span><span class="text-xs border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2 py-0.5 text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+ voltaje</span></div>
                  </div>
                  <div class="flex items-center gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">3 / 20 modelos</span>
                    <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                      <div class="w-[15%] h-full bg-green-500 rounded-full transition-all duration-1000"></div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Evobike -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">Evobike</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">1 modelo</span>
                  </div>
                  <div class="flex gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Editar</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Modelo</span>
                    <span class="text-xs text-red-600 border border-red-200 dark:border-red-800 px-2 py-0.5 rounded bg-red-50 dark:bg-red-900/30 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Eliminar</span>
                  </div>
                </div>
              </div>
              <!-- NWOW -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">NWOW</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">2 modelos</span>
                  </div>
                  <div class="flex gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Editar</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Modelo</span>
                    <span class="text-xs text-red-600 border border-red-200 dark:border-red-800 px-2 py-0.5 rounded bg-red-50 dark:bg-red-900/30 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Eliminar</span>
                  </div>
                </div>
                <div class="p-4 space-y-3">
                  <div class="grid grid-cols-3 gap-2 text-[10px] text-gray-400 dark:text-gray-500 border-b pb-1">
                    <div>Modelo</div>
                    <div>Colores</div>
                    <div>Voltajes</div>
                  </div>
                  <div class="grid grid-cols-3 gap-2 items-center border-b pb-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                    <div class="flex justify-between items-center"><span class="text-sm text-gray-700 dark:text-gray-300">EMC Golf2</span><span class="text-gray-400 text-xs transition-opacity duration-200 hover:opacity-100">✎ ✕</span></div>
                    <div class="flex gap-1">
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-red-500 transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-white transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+</span>
                    </div>
                    <div class="flex gap-1"><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">48V</span><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">60V</span><span class="text-xs border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2 py-0.5 text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+ voltaje</span></div>
                  </div>
                  <div class="grid grid-cols-3 gap-2 items-center transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                    <div class="flex justify-between items-center"><span class="text-sm text-gray-700 dark:text-gray-300">ERV2</span><span class="text-gray-400 text-xs transition-opacity duration-200 hover:opacity-100">✎ ✕</span></div>
                    <div class="flex gap-1">
                      <span class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 bg-black transition-transform duration-200 hover:scale-110"></span>
                      <span class="w-6 h-6 rounded border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+</span>
                    </div>
                    <div class="flex gap-1"><span class="text-xs bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-2 py-0.5 transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700">72V</span><span class="text-xs border border-dashed border-gray-300 dark:border-gray-600 rounded-full px-2 py-0.5 text-gray-400 dark:text-gray-500 transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300">+ voltaje</span></div>
                  </div>
                  <div class="flex items-center gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">2 / 20 modelos</span>
                    <div class="flex-1 h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                      <div class="w-[10%] h-full bg-green-500 rounded-full transition-all duration-1000"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: GARANTÍAS ===== -->
        <div id="panel-garantias" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <!-- Contenido abreviado por brevedad, pero aplicando mismas animaciones -->
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / garantias / editar-marca</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex flex-wrap justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Garantía — Yadea</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Sube la póliza PDF, configura los componentes y la política de reemplazo</p>
              </div>
              <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-3 py-1 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Garantía activa</span>
                <div class="w-8 h-4 bg-gray-900 dark:bg-white rounded-full relative transition-all duration-200"><span class="absolute right-0.5 top-0.5 w-3 h-3 bg-white dark:bg-gray-900 rounded-full shadow transition-all duration-200"></span></div>
              </div>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
              <!-- Columna izquierda -->
              <div class="md:col-span-1 space-y-4">
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="font-semibold text-sm text-gray-900 dark:text-white">Póliza PDF</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">La IA extrae los componentes automáticamente</p>
                  <div class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded mt-3 px-3 py-1.5 transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-900/50">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs text-blue-700 dark:text-blue-300 font-medium truncate">poliza_yadea_2025.pdf</span>
                  </div>
                  <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center mt-3 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-white dark:hover:bg-gray-700">
                    <svg class="w-7 h-7 text-gray-400 dark:text-gray-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><span class="font-medium">Haz clic</span> o arrastra el PDF</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">Máximo 4 MB</p>
                  </div>
                  <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded mt-3 px-3 py-1.5 transition-all duration-200 hover:bg-green-100 dark:hover:bg-green-900/50">
                    <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs text-green-700 dark:text-green-300">PDF procesado correctamente.</span>
                  </div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="font-semibold text-sm text-gray-900 dark:text-white">Política de reemplazo</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">¿Qué garantía recibe un componente sustituido?</p>
                  <div class="mt-3 space-y-2">
                    <div class="border border-gray-200 dark:border-gray-700 rounded p-3 flex gap-3 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                      <div class="w-4 h-4 rounded-full border-2 border-gray-300 dark:border-gray-600 mt-0.5"></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Heredar <span class="text-[10px] bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-1.5 rounded ml-1">Tiempo restante</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">El componente nuevo continúa con el tiempo de garantía que le quedaba al original.</p>
                      </div>
                    </div>
                    <div class="border-2 border-gray-900 dark:border-white rounded p-3 flex gap-3 bg-gray-50 dark:bg-gray-800 transition-all duration-200 hover:shadow-inner">
                      <div class="w-4 h-4 rounded-full bg-gray-900 dark:bg-white border-2 border-gray-900 dark:border-white flex items-center justify-center"><span class="w-1.5 h-1.5 rounded-full bg-white dark:bg-gray-900"></span></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Nueva completa <span class="text-[10px] bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-1.5 rounded ml-1">Duración original</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">El componente nuevo recibe la duración total de garantía como si fuera compra nueva.</p>
                      </div>
                    </div>
                    <div class="border border-gray-200 dark:border-gray-700 rounded p-3 flex gap-3 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                      <div class="w-4 h-4 rounded-full border-2 border-gray-300 dark:border-gray-600 mt-0.5"></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Mini <span class="text-[10px] bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-1.5 rounded ml-1">Configurable</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Garantía corta con días configurables. Ideal para reemplazos en garantía.</p>
                      </div>
                    </div>
                    <button disabled class="w-full py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar política</button>
                  </div>
                </div>
              </div>
              <!-- Columna derecha -->
              <div class="md:col-span-2 border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                  <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Componentes con garantía</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Revisa lo que la IA extrajo y ajusta antes de guardar.</p>
                  </div>
                  <button disabled class="px-3 py-1 border border-gray-200 dark:border-gray-700 rounded text-xs text-gray-500 dark:text-gray-400 bg-transparent transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">+ Agregar</button>
                </div>
                <div class="space-y-3">
                  <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="flex justify-between items-center"><span class="font-medium text-sm text-gray-900 dark:text-white">Motor</span><span class="flex gap-1"><span class="text-[10px] bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-1.5 rounded">Serial</span><button disabled class="text-gray-400 transition-colors duration-200 hover:text-red-500">✕</button></span></div>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Clave</label><input disabled value="motor" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Nombre</label><input disabled value="Motor eléctrico" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Meses</label><input disabled value="24" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs text-center bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    </div>
                    <div class="mt-1"><label class="text-[10px] text-gray-400 dark:text-gray-500">Cobertura</label><input disabled value="Defecto de fábrica, mal funcionamiento" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div class="mt-1"><label class="text-[10px] text-gray-400 dark:text-gray-500">Incluye <span class="font-normal text-gray-400 dark:text-gray-500">(separado por comas)</span></label><input disabled value="mando, freno, convertidor de velocidad" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div class="flex gap-4 mt-2"><label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300"><input type="checkbox" checked disabled class="accent-purple-600 transition-all duration-200"> Serializable</label><label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300"><input type="checkbox" disabled> Excluido (consumible)</label></div>
                  </div>
                  <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <div class="flex justify-between items-center"><span class="font-medium text-sm text-gray-900 dark:text-white">Batería</span><span class="flex gap-1"><span class="text-[10px] bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-1.5 rounded">Serial</span><span class="text-[10px] bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-1.5 rounded">Excluido</span><button disabled class="text-gray-400 transition-colors duration-200 hover:text-red-500">✕</button></span></div>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Clave</label><input disabled value="bateria" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Nombre</label><input disabled value="Batería de litio" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                      <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Meses</label><input disabled value="18" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs text-center bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    </div>
                    <div class="mt-1"><label class="text-[10px] text-gray-400 dark:text-gray-500">Cobertura</label><input disabled value="Capacidad reducida, defecto de fábrica" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div class="mt-1"><label class="text-[10px] text-gray-400 dark:text-gray-500">Incluye <span class="font-normal text-gray-400 dark:text-gray-500">(separado por comas)</span></label><input disabled value="cargador, BMS" class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-xs bg-gray-50 dark:bg-gray-800 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div class="flex gap-4 mt-2"><label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300"><input type="checkbox" checked disabled class="accent-purple-600 transition-all duration-200"> Serializable</label><label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300"><input type="checkbox" checked disabled class="accent-red-600 transition-all duration-200"> Excluido (consumible)</label></div>
                  </div>
                  <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2">
                    <span class="text-xs text-gray-400 dark:text-gray-500">1 activo · 1 excluido</span>
                    <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar componentes</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: CAJAS ===== -->
        <div id="panel-cajas" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / cajas</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Gestión de Cajas</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Vista consolidada — todas las sucursales</p>
              </div>
              <button disabled class="px-3 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">+ Asignar caja</button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Total en cajas</div>
                <div class="text-xl font-semibold text-green-600">$128,450.00</div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Ventas del día</div>
                <div class="text-xl font-semibold text-yellow-600">$46,320.00</div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Sesiones abiertas</div>
                <div class="text-xl font-semibold text-blue-600">3</div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Sin caja</div>
                <div class="text-xl font-semibold text-yellow-600">1</div>
              </div>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
              <!-- Tarjeta caja -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Norte</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">Caja Principal</p>
                  </div>
                  <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>Abierta</span>
                </div>
                <div class="p-3 flex-1">
                  <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Total sistema</div>
                      <div class="text-base font-semibold text-green-600">$58,200.00</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Ventas</div>
                      <div class="text-base font-semibold text-yellow-600">$21,400.00</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500"># Ventas</div>
                      <div class="text-base font-semibold text-gray-900 dark:text-white">12</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Fondo inicial</div>
                      <div class="text-base font-semibold text-gray-900 dark:text-white">$5,000.00</div>
                    </div>
                  </div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between"><span>Abierta desde</span><span class="font-mono text-gray-600 dark:text-gray-300">10/03 09:30</span></div>
                </div>
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-1">
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver detalle</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Ingreso</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">− Retiro</button>
                  <button disabled class="text-xs border border-red-200 dark:border-red-800 rounded px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Forzar cierre</button>
                </div>
              </div>
              <!-- Otras tarjetas (similares con animaciones) -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Sur</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">Caja Secundaria</p>
                  </div>
                  <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>Abierta</span>
                </div>
                <div class="p-3 flex-1">
                  <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Total sistema</div>
                      <div class="text-base font-semibold text-green-600">$42,800.00</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Ventas</div>
                      <div class="text-base font-semibold text-yellow-600">$15,600.00</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500"># Ventas</div>
                      <div class="text-base font-semibold text-gray-900 dark:text-white">8</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                      <div class="text-[10px] text-gray-400 dark:text-gray-500">Fondo inicial</div>
                      <div class="text-base font-semibold text-gray-900 dark:text-white">$3,000.00</div>
                    </div>
                  </div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between"><span>Abierta desde</span><span class="font-mono text-gray-600 dark:text-gray-300">10/03 08:15</span></div>
                </div>
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-1">
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver detalle</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Ingreso</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">− Retiro</button>
                  <button disabled class="text-xs border border-red-200 dark:border-red-800 rounded px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Forzar cierre</button>
                </div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 opacity-60">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Centro</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">Caja Principal</p>
                  </div>
                  <span class="text-[10px] font-medium bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">Cerrada</span>
                </div>
                <div class="p-3 flex-1 text-center py-6">
                  <div class="text-3xl mb-2">🔒</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500">Sesión cerrada</div>
                </div>
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-1">
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver detalle</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 opacity-50">+ Ingreso</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 opacity-50">− Retiro</button>
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 opacity-50">Forzar cierre</button>
                </div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                  <div>
                    <p class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Oriente</p>
                  </div>
                  <span class="text-[10px] font-medium bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Sin caja</span>
                </div>
                <div class="p-3 flex-1 text-center py-6">
                  <div class="text-3xl mb-2">📭</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 mb-3">Esta sucursal no tiene caja asignada.</div>
                  <button disabled class="text-xs border border-yellow-300 dark:border-yellow-700 rounded px-3 py-1 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 transition-all duration-200 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 hover:border-yellow-400 dark:hover:border-yellow-600">+ Asignar caja</button>
                </div>
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-1">
                  <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 opacity-50">Ver detalle</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: CONFIGURACIÓN ===== -->
        <div id="panel-config" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / configuracion</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Configuración general</h3>
              <p class="text-xs text-gray-400 dark:text-gray-500">Estos ajustes afectarán a todas las sucursales y centros de venta.</p>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl divide-y divide-gray-200 dark:divide-gray-700">
              <div x-data="{ open: true }">
                <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                  <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6h11M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z" />
                    </svg></div>
                  <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Modo de venta</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Controla el flujo de ventas por defecto.</p>
                  </div>
                  <span class="text-xs bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">Estándar</span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <div x-show="open" class="px-4 py-4 border-t border-gray-200 dark:border-gray-900 bg-gray-50 dark:bg-gray-900">
                  <div class="grid grid-cols-2 gap-3">
                    <div class="border-2 border-gray-900 dark:border-white rounded p-3 bg-white dark:bg-gray-900 flex items-start gap-2 transition-all duration-200 hover:shadow-md">
                      <div class="w-5 h-5 rounded-full bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 mt-0.5"><span class="w-2 h-2 rounded-full bg-white dark:bg-gray-900"></span></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Estándar</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Venta tradicional con ticket y caja.</p>
                      </div>
                    </div>
                    <div class="border-2 border-gray-200 dark:border-gray-700 rounded p-3 bg-white dark:bg-gray-900 flex items-start gap-2 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-md">
                      <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600 flex-shrink-0 mt-0.5"></div>
                      <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Rápido</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Sin ticket, solo cierre de caja.</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-3 mt-3"><button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar</button></div>
                </div>
              </div>
              <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                  <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                  <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">IVA predeterminado</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Porcentaje de IVA aplicado en nuevas ventas.</p>
                  </div>
                  <span class="text-xs bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">16%</span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <div x-show="open" class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                  <input disabled type="number" value="16" class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                  <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-3 mt-3"><button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar</button></div>
                </div>
              </div>
              <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                  <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg></div>
                  <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Notificaciones por correo</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Envía alertas de ventas y eventos a los administradores.</p>
                  </div>
                  <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full transition-colors duration-200 hover:bg-green-200 dark:hover:bg-green-900/50">Activado</span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <div x-show="open" class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-6 bg-gray-900 dark:bg-white rounded-full relative transition-all duration-200"><span class="absolute right-0.5 top-0.5 w-4 h-4 bg-white dark:bg-gray-900 rounded-full shadow transition-all duration-200"></span></div><span class="text-sm text-gray-500 dark:text-gray-400">Activado</span>
                  </div>
                  <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-3 mt-3"><button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar</button></div>
                </div>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase">Facturación</div>
              <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                  <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center text-white dark:text-gray-900"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg></div>
                  <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Serie de facturación</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Prefijo para los folios de factura.</p>
                  </div>
                  <span class="text-xs bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">F-001</span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-300" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <div x-show="open" class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                  <input disabled type="text" value="F-001" class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 text-sm bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                  <div class="flex justify-end border-t border-gray-200 dark:border-gray-700 pt-3 mt-3"><button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Guardar</button></div>
                </div>
              </div>
            </div>
            <div class="text-center text-xs text-gray-400 dark:text-gray-500 mt-4">Los cambios se aplicarán de inmediato para todas las sucursales.</div>
          </div>
        </div>


        <!-- ===== PANEL: SUCURSALES ===== -->
        <div id="panel-sucursales" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / bicicletas</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex flex-wrap justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bicicletas</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Gestiona el inventario de bicicletas</p>
              </div>
              <button disabled class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> Ingresar Bicicleta</button>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Total</div>
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">48</div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">En Stock</div>
                <div class="text-2xl font-semibold text-green-600">32</div>
              </div>
              <div class="border border-gray-200 dark:border-gray-700 rounded p-3 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Vendidas</div>
                <div class="text-2xl font-semibold text-blue-600">16</div>
              </div>
            </div>
            <div class="space-y-3">
              <!-- Acordeón Sucursal Norte -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center cursor-pointer" onclick="toggleAcordeon(this)">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Norte</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">12 unidades</span>
                  </div>
                  <span class="text-xs text-gray-400 dark:text-gray-500">Mostrando 5 de 12</span>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                      <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">N° Serie</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Marca</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Modelo</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Voltaje</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Color</th>
                        <th class="px-3 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Fecha</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white dark:divide-gray-800">
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-001</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Yadea</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Ova</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">48V</td>
                        <td class="px-3 py-2">
                          <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                            <div class="absolute left-0 top-0 w-1/2 h-full bg-red-500"></div>
                            <div class="absolute right-0 top-0 w-1/2 h-full bg-blue-500"></div>
                          </div>
                        </td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">En Stock</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">10/03/2025</td>
                      </tr>
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-002</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">NWOW</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">EMC Golf2</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">60V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Negro</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">En Reparación</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">09/03/2025</td>
                      </tr>
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-003</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Evobike</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Keeness</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">48V</td>
                        <td class="px-3 py-2">
                          <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700 overflow-hidden relative">
                            <div class="absolute left-0 top-0 w-1/2 h-full bg-gray-400"></div>
                            <div class="absolute right-0 top-0 w-1/2 h-full bg-orange-500"></div>
                          </div>
                        </td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">En Stock</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">08/03/2025</td>
                      </tr>
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-004</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">&lt;</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Model X</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">72V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Blanco</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full">Vendido</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">07/03/2025</td>
                      </tr>
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-005</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Yadea</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">GB18</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">60V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Rojo</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">En Stock</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">06/03/2025</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs text-gray-400 dark:text-gray-500">
                  <span>Página 1 de 3</span>
                  <div class="flex gap-1">
                    <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Anterior</span>
                    <span class="px-2 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded transition-all duration-200 hover:bg-gray-800 dark:hover:bg-white">1</span>
                    <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">2</span>
                    <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">3</span>
                    <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Siguiente →</span>
                  </div>
                </div>
              </div>
              <!-- Sucursal Sur (colapsado) -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center cursor-pointer" onclick="toggleAcordeon(this)">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Sur</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">8 unidades</span>
                  </div>
                  <span class="text-xs text-gray-400 dark:text-gray-500">Mostrando 5 de 8</span>
                </div>
                <div style="display:none;" class="overflow-x-auto">
                  <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                      <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">N° Serie</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Marca</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Modelo</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Voltaje</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Color</th>
                        <th class="px-3 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Fecha</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white dark:divide-gray-800">
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-010</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">NWOW</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">ERV2</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">72V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Negro</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">En Stock</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">10/03/2025</td>
                      </tr>
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-011</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Yadea</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Ova</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">48V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Azul</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full">Vendido</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">09/03/2025</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- Sucursal Centro (colapsado) -->
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center cursor-pointer" onclick="toggleAcordeon(this)">
                  <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-sm text-gray-900 dark:text-white">Sucursal Centro</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">5 unidades</span>
                  </div>
                  <span class="text-xs text-gray-400 dark:text-gray-500">Mostrando 5 de 5</span>
                </div>
                <div style="display:none;" class="overflow-x-auto">
                  <table class="w-full min-w-[700px] text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                      <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">N° Serie</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Marca</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Modelo</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Voltaje</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Color</th>
                        <th class="px-3 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Fecha</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white dark:divide-gray-800">
                      <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-3 py-2 font-mono text-xs text-gray-900 dark:text-white">SN-2025-020</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Tesla</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">Model Y</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">72V</td>
                        <td class="px-3 py-2"><span class="text-gray-500 dark:text-gray-400">Blanco</span></td>
                        <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">En Stock</span></td>
                        <td class="px-3 py-2 text-xs text-gray-400 dark:text-gray-500">08/03/2025</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: CORTE ===== -->
        <div id="panel-corte" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / mi-caja</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Mi Caja</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Caja principal · Administrador</p>
              </div>
              <span class="inline-flex items-center gap-1.5 text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>Abierta</span>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Total sistema</div>
                <div class="text-xl font-semibold text-green-600">$128,450.00</div>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Ventas cobradas</div>
                <div class="text-xl font-semibold text-yellow-600">$46,320.00</div>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase"># Ventas</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">12</div>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Fondo inicial</div>
                <div class="text-xl font-semibold text-gray-900 dark:text-white">$5,000.00</div>
              </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 text-xs text-gray-400 dark:text-gray-500 space-y-1">
              <div class="flex justify-between"><span>Abierta desde</span><span class="text-gray-600 dark:text-gray-300 font-medium">10/03/2025 09:30</span></div>
              <div class="flex justify-between"><span>Ingresos manuales</span><span class="text-blue-600 font-medium">+$1,200.00</span></div>
              <div class="flex justify-between"><span>Retiros</span><span class="text-red-600 font-medium">-$350.00</span></div>
              <div class="flex justify-between"><span>Ajustes</span><span class="text-green-600 font-medium">+$50.00</span></div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 mt-3 pt-3">
              <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Por método de pago</div>
              <div class="flex justify-between text-xs mt-1"><span>💵 Efectivo</span><span class="text-green-600 font-medium">$32,100.00</span></div>
              <div class="flex justify-between text-xs"><span>💳 Tarjeta</span><span class="text-green-600 font-medium">$14,220.00</span></div>
            </div>
            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
              <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">+ Ingreso manual</button>
              <button disabled class="text-xs border border-gray-200 dark:border-gray-700 rounded px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Corte parcial</button>
              <button disabled class="text-xs border border-red-200 dark:border-red-800 rounded px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Cerrar caja</button>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 mt-4">
              <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between text-xs font-medium text-gray-400 dark:text-gray-500"><span>Últimas sesiones</span><span>3 registros</span></div>
              <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-xs">
                  <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                      <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Apertura</th>
                      <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Cierre</th>
                      <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Fondo</th>
                      <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Sistema</th>
                      <th class="px-3 py-2 text-left text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Diferencia</th>
                      <th class="px-3 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Estado</th>
                      <th class="px-3 py-2 text-center text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">PDF</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-white dark:divide-gray-800">
                    <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                      <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">10/03/2025 09:30</td>
                      <td class="px-3 py-2 text-gray-400 dark:text-gray-500">—</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$5,000.00</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$128,450.00</td>
                      <td class="px-3 py-2 text-green-600 font-medium">+$150.00</td>
                      <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">Abierta</span></td>
                      <td class="px-3 py-2 text-center text-gray-400 dark:text-gray-500">—</td>
                    </tr>
                    <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                      <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">09/03/2025 08:15</td>
                      <td class="px-3 py-2 text-gray-400 dark:text-gray-500">09/03/2025 20:00</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$5,000.00</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$92,300.00</td>
                      <td class="px-3 py-2 text-green-600 font-medium">+$80.00</td>
                      <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Cerrada</span></td>
                      <td class="px-3 py-2 text-center"><a href="#" class="text-red-600 font-medium inline-flex items-center gap-1 transition-colors duration-200 hover:text-red-800"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                          </svg>PDF</a></td>
                    </tr>
                    <tr class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                      <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">08/03/2025 09:00</td>
                      <td class="px-3 py-2 text-gray-400 dark:text-gray-500">08/03/2025 19:30</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$5,000.00</td>
                      <td class="px-3 py-2 text-gray-900 dark:text-white">$67,800.00</td>
                      <td class="px-3 py-2 text-red-600 font-medium">-$20.00</td>
                      <td class="px-3 py-2 text-center"><span class="inline-block text-[10px] font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full">Cerrada</span></td>
                      <td class="px-3 py-2 text-center"><a href="#" class="text-red-600 font-medium inline-flex items-center gap-1 transition-colors duration-200 hover:text-red-800"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                          </svg>PDF</a></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2 text-xs text-gray-400 dark:text-gray-500 mt-3">
              <span>Modales:</span>
              <button disabled class="border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Abrir caja</button>
              <button disabled class="border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ingreso manual</button>
              <button disabled class="border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Corte parcial</button>
              <button disabled class="border border-red-200 dark:border-red-800 rounded px-2 py-0.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50 hover:border-red-300 dark:hover:border-red-700">Cerrar caja</button>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: ÓRDENES ===== -->
        <div id="panel-ordenes" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / ordenes</span>
          </div>
          <div class="bg-white dark:bg-gray-900">
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-start gap-2">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Órdenes de trabajo</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">5 activas · 2 listas para entrega</p>
              </div>
              <button disabled class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-3 py-1.5 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> Nueva OT</button>
            </div>
            <div class="flex gap-1 overflow-x-auto px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 scrollbar-hide">
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full whitespace-nowrap">Todas</span>
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-full whitespace-nowrap transition-colors duration-200 hover:bg-white dark:hover:bg-gray-600">Recibidas</span>
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-full whitespace-nowrap transition-colors duration-200 hover:bg-white dark:hover:bg-gray-600">Diagnóstico</span>
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-full whitespace-nowrap transition-colors duration-200 hover:bg-white dark:hover:bg-gray-600">Cotización enviada</span>
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-full whitespace-nowrap transition-colors duration-200 hover:bg-white dark:hover:bg-gray-600">En proceso</span>
              <span class="inline-block text-xs font-medium px-3 py-1 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-full whitespace-nowrap transition-colors duration-200 hover:bg-white dark:hover:bg-gray-600">Listas</span>
            </div>
            <div class="grid md:grid-cols-3 gap-0">
              <!-- Lista -->
              <div class="md:col-span-1 border-r border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-800 max-h-[500px] overflow-y-auto">
                <div class="border-2 border-gray-900 dark:border-white rounded-lg p-3 bg-white dark:bg-gray-900 mb-2 transition-all duration-200 hover:shadow-md">
                  <div class="flex justify-between items-start">
                    <div class="flex flex-wrap gap-1"><span class="text-xs font-mono text-gray-400 dark:text-gray-500">OT-001</span><span class="text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-1.5 rounded-full">Cotización enviada</span><span class="text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-1.5 rounded-full">Garantía</span></div><span class="text-[10px] text-gray-400 dark:text-gray-500">hace 2h</span>
                  </div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">Juan Pérez</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">Yadea · Ova · 48V</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">No enciende, batería dañada</div>
                  <div class="flex justify-between items-center mt-2"><span class="text-[10px] text-red-600 font-medium">⏰ Cotización expirada</span><span class="text-xs font-semibold text-gray-900 dark:text-white">$3,500.00</span></div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 border-l-4 border-yellow-400 rounded-lg p-3 bg-white dark:bg-gray-900 mb-2 transition-all duration-200 hover:shadow-md">
                  <div class="flex justify-between items-start">
                    <div class="flex flex-wrap gap-1"><span class="text-xs font-mono text-gray-400 dark:text-gray-500">OT-002</span><span class="text-[10px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 rounded-full">Diagnóstico</span></div><span class="text-[10px] text-gray-400 dark:text-gray-500">hace 4h</span>
                  </div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">María Gómez</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">NWOW · EMC Golf2 · 60V</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Freno delantero no responde</div>
                  <div class="flex justify-between items-center mt-2"><span class="text-[10px] text-gray-400 dark:text-gray-500">&nbsp;</span><span class="text-xs font-semibold text-gray-400 dark:text-gray-500">Por cotizar</span></div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-900 mb-2 transition-all duration-200 hover:shadow-md">
                  <div class="flex justify-between items-start">
                    <div class="flex flex-wrap gap-1"><span class="text-xs font-mono text-gray-400 dark:text-gray-500">OT-003</span><span class="text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-1.5 rounded-full">En proceso</span><span class="text-[10px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-1.5 rounded-full">Mantenimiento</span></div><span class="text-[10px] text-gray-400 dark:text-gray-500">ayer</span>
                  </div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">Carlos Ruiz</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">Evobike · Keeness · 48V</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Limpieza general y ajuste de cadena</div>
                  <div class="flex justify-between items-center mt-2"><span class="text-[10px] text-gray-400 dark:text-gray-500">&nbsp;</span><span class="text-xs font-semibold text-gray-900 dark:text-white">$1,200.00</span></div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white dark:bg-gray-900 transition-all duration-200 hover:shadow-md">
                  <div class="flex justify-between items-start">
                    <div class="flex flex-wrap gap-1"><span class="text-xs font-mono text-gray-400 dark:text-gray-500">OT-004</span><span class="text-[10px] font-medium bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 px-1.5 rounded-full">Lista</span></div><span class="text-[10px] text-gray-400 dark:text-gray-500">ayer</span>
                  </div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">Laura Sánchez</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 font-mono">NWOW · ERV2 · 72V</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cambio de motor trasero</div>
                  <div class="flex justify-between items-center mt-2"><span class="text-[10px] text-gray-400 dark:text-gray-500">&nbsp;</span><span class="text-xs font-semibold text-gray-900 dark:text-white">$4,800.00</span></div>
                </div>
                <div class="flex justify-between items-center mt-3 text-xs text-gray-400 dark:text-gray-500">
                  <span class="border border-gray-200 dark:border-gray-700 rounded px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Anterior</span>
                  <span>1 / 2</span>
                  <span class="border border-gray-200 dark:border-gray-700 rounded px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Siguiente →</span>
                </div>
              </div>
              <!-- Detalle -->
              <div class="md:col-span-2 p-4 bg-white dark:bg-gray-900">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                  <div class="flex flex-wrap items-center gap-2"><span class="font-mono text-sm font-semibold text-gray-900 dark:text-white">OT-001</span><span class="text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Cotización enviada</span><span class="text-[10px] font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full">✦ Garantía</span></div>
                  <button disabled class="text-gray-400 dark:text-gray-500 text-lg transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300">✕</button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
                  <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                    <div class="text-[9px] text-gray-400 dark:text-gray-500 uppercase">Cliente</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Juan Pérez</div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                    <div class="text-[9px] text-gray-400 dark:text-gray-500 uppercase">Unidad</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white font-mono">Yadea · Ova · 48V</div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                    <div class="text-[9px] text-gray-400 dark:text-gray-500 uppercase">Costo total</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">$3,500.00</div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                    <div class="text-[9px] text-gray-400 dark:text-gray-500 uppercase">Teléfono</div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white font-mono">555-1234</div>
                  </div>
                </div>
                <div class="flex items-start gap-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded p-3 mb-3 transition-all duration-200 hover:bg-red-100 dark:hover:bg-red-900/50">
                  <svg class="w-4 h-4 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  <div>
                    <p class="text-xs font-semibold text-red-700 dark:text-red-400">El cliente rechazó la cotización</p>
                    <p class="text-xs text-red-600 dark:text-red-300">Contáctalo para decidir si se cancela o se negocia: <span class="font-semibold">555-1234</span></p>
                    <div class="flex gap-2 mt-1"><span class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-0.5 rounded transition-colors duration-200 hover:bg-red-200 dark:hover:bg-red-900/50">📞 Llamar</span><span class="text-[10px] bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded transition-colors duration-200 hover:bg-green-200 dark:hover:bg-green-900/50">WhatsApp</span></div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Problema reportado</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">No enciende, batería dañada</p>
                  </div>
                  <div>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Diagnóstico</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Batería con celdas en corto, requiere reemplazo</p>
                  </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3 mb-3 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700">
                  <div class="flex justify-between items-center"><span class="text-xs font-medium text-gray-400 dark:text-gray-500">Cotización enviada</span><span class="text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Pendiente</span></div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reemplazo de batería y verificación del sistema eléctrico.</p>
                  <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500"><span>Piezas: Batería × 1</span><span class="font-medium text-gray-900 dark:text-white">$3,500.00</span></div>
                  <div class="flex justify-between text-xs border-t border-gray-200 dark:border-gray-700 pt-1 mt-1"><span class="text-gray-400 dark:text-gray-500">Total cotización</span><span class="font-semibold text-gray-900 dark:text-white">$3,500.00</span></div>
                </div>
                <div class="mb-3">
                  <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Piezas</p>
                  <div class="bg-gray-50 dark:bg-gray-800 rounded p-2 flex justify-between text-xs transition-all duration-200 hover:bg-white dark:hover:bg-gray-700"><span>Batería de litio × 1</span><span class="font-medium text-gray-900 dark:text-white">$3,500.00</span></div>
                </div>
                <div class="mb-3">
                  <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Progreso</p>
                  <div class="flex items-center gap-0">
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-900 dark:bg-white"></span><span class="text-[8px] text-gray-900 dark:text-white font-medium mt-0.5">Recibida</span></div>
                    <div class="h-0.5 flex-1 bg-gray-900 dark:bg-white"></div>
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-900 dark:bg-white"></span><span class="text-[8px] text-gray-900 dark:text-white font-medium mt-0.5">Diagnóst.</span></div>
                    <div class="h-0.5 flex-1 bg-gray-900 dark:bg-white"></div>
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-900 dark:bg-white"></span><span class="text-[8px] text-gray-900 dark:text-white font-medium mt-0.5">Cotización</span></div>
                    <div class="h-0.5 flex-1 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700"></span><span class="text-[8px] text-gray-400 dark:text-gray-500 mt-0.5">Proceso</span></div>
                    <div class="h-0.5 flex-1 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700"></span><span class="text-[8px] text-gray-400 dark:text-gray-500 mt-0.5">Lista</span></div>
                    <div class="h-0.5 flex-1 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="flex flex-col items-center flex-1"><span class="w-2 h-2 rounded-full bg-gray-200 dark:bg-gray-700"></span><span class="text-[8px] text-gray-400 dark:text-gray-500 mt-0.5">Entregada</span></div>
                  </div>
                </div>
                <div class="mb-3">
                  <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">Historial</p>
                  <div class="text-xs text-gray-400 dark:text-gray-500 space-y-1 max-h-12 overflow-y-auto">
                    <div class="flex items-center gap-1 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300"><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>Recibida · Admin · hace 2h</div>
                    <div class="flex items-center gap-1 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300"><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>Diagnóstico · Técnico · hace 1h</div>
                    <div class="flex items-center gap-1 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300"><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>Cotización enviada · Admin · hace 30m</div>
                  </div>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex gap-2">
                  <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">Resolver manualmente</button>
                  <button disabled class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar orden</button>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex justify-center gap-2 text-xs text-gray-400 dark:text-gray-500">
              <span>Modales:</span>
              <button disabled class="border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Enviar cotización</button>
              <button disabled class="border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Resolver manualmente</button>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: ROBO ===== -->
        <div id="panel-robo" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / reporte-robo</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5 relative">
            <div class="mb-5">
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Reporte de robo</h3>
              <p class="text-xs text-gray-400 dark:text-gray-500">Registra el robo de un vehículo ArrowX</p>
            </div>

            <!-- Vehículos en custodia -->
            <div class="mb-5">
              <div class="flex items-center gap-2 mb-2"><span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                <p class="text-[10px] font-semibold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">Vehículos en custodia — esperando recolección del dueño</p>
              </div>
              <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-3 mb-2 flex justify-between items-start transition-all duration-200 hover:shadow-md">
                <div>
                  <p class="text-xs font-mono font-semibold text-gray-900 dark:text-white">SN-2025-001</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">Yadea · Ova</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">Cliente: <span class="font-medium text-gray-700 dark:text-gray-300">Juan Pérez</span> · 555-1234</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">Reportado por: <span class="text-gray-700 dark:text-gray-300">Sucursal Norte</span></p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">En custodia desde: <span class="text-gray-700 dark:text-gray-300">10/03/2025</span></p>
                </div>
                <button disabled class="bg-yellow-600 dark:bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded opacity-60 transition-all duration-200 hover:opacity-80">✓ Marcar entregado</button>
              </div>
              <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-3 flex justify-between items-start transition-all duration-200 hover:shadow-md">
                <div>
                  <p class="text-xs font-mono font-semibold text-gray-900 dark:text-white">SN-2025-002</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">NWOW · EMC Golf2</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">Cliente: <span class="font-medium text-gray-700 dark:text-gray-300">María García</span> · 555-5678</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">Reportado por: <span class="text-gray-700 dark:text-gray-300">Sucursal Sur</span></p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">En custodia desde: <span class="text-gray-700 dark:text-gray-300">09/03/2025</span></p>
                </div>
                <button disabled class="bg-yellow-600 dark:bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded opacity-60 transition-all duration-200 hover:opacity-80">✓ Marcar entregado</button>
              </div>
            </div>

            <!-- Paso 1 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 max-w-md mb-5 transition-all duration-200 hover:shadow-md">
              <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Paso 1 — Buscar vehículo</p>
              <div class="flex gap-2 mt-2">
                <input type="text" placeholder="Número de serie (17 caracteres)" maxlength="17" disabled class="flex-1 px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-sm bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 uppercase transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg> Buscar</button>
              </div>
              <button disabled class="text-xs text-gray-400 dark:text-gray-500 underline mt-2 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300">← Buscar otro vehículo</button>
            </div>

            <!-- Paso 2 -->
            <div class="mb-5">
              <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Paso 2 — Confirma los datos con el cliente</p>
              <div class="grid sm:grid-cols-2 gap-3 mt-2">
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-3 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Vehículo</p>
                  <dl class="text-xs space-y-1 mt-1">
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">N° de serie</dt>
                      <dd class="font-mono font-medium text-gray-900 dark:text-white">SN-2025-001</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Marca</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">Yadea</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Modelo</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">Ova</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Voltaje</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">48V</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Color</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">Rojo</dd>
                    </div>
                  </dl>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-3 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Cliente</p>
                  <dl class="text-xs space-y-1 mt-1">
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Nombre</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">Juan Pérez</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Teléfono</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">555-1234</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Correo</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">juan@email.com</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-1 mt-1">
                      <dt class="text-gray-400 dark:text-gray-500">Comprado en</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">Sucursal Norte</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-gray-400 dark:text-gray-500">Fecha de compra</dt>
                      <dd class="font-medium text-gray-900 dark:text-white">05/01/2025</dd>
                    </div>
                  </dl>
                </div>
              </div>
              <div class="flex items-start gap-2 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-xl p-3 max-w-xl mt-2 transition-all duration-200 hover:bg-yellow-100 dark:hover:bg-yellow-900/50">
                <svg class="w-4 h-4 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <p class="text-xs text-yellow-800 dark:text-yellow-300">El cliente no tiene correo registrado. Se levantará el reporte pero <strong>no se podrá enviar la notificación por correo</strong>.</p>
              </div>
            </div>

            <!-- Paso 3 -->
            <div class="max-w-xl mb-5">
              <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Paso 3 — Levantar reporte</p>
              <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-3 transition-all duration-200 hover:shadow-md">
                <div><label class="text-xs text-gray-400 dark:text-gray-500 block mb-1">Notas adicionales <span class="text-gray-400 dark:text-gray-500 font-normal">(opcional)</span></label><textarea rows="2" disabled placeholder="Describe las circunstancias del robo, lugar, hora aproximada..." class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 resize-none transition-all duration-200 focus:ring-1 focus:ring-gray-300"></textarea></div>
                <div class="flex items-start gap-2 bg-gray-50 dark:bg-gray-800 rounded p-2 text-xs text-gray-400 dark:text-gray-500 transition-all duration-200 hover:bg-white dark:hover:bg-gray-700"><svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <p>Al levantar el reporte, el sistema enviará un correo al cliente para que <strong>confirme el robo</strong>. Una vez confirmado, el vehículo quedará marcado en toda la red ArrowX.</p>
                </div>
                <div class="flex justify-end"><button disabled class="inline-flex items-center gap-1.5 bg-red-600 text-white px-4 py-1.5 rounded text-sm font-semibold opacity-60 transition-all duration-200 hover:opacity-80"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg> Levantar reporte de robo</button></div>
              </div>
            </div>

            <!-- Modal activo -->
            <div class="relative bg-black/40 dark:bg-black/60 rounded-xl p-8 flex items-center justify-center transition-all duration-300">
              <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full text-center shadow-2xl transition-all duration-300 hover:scale-105">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-3 transition-all duration-300 hover:scale-110"><svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg></div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Reporte registrado</h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 mb-3">Se envió un correo al cliente para confirmar el robo. El vehículo quedará marcado en la red ArrowX al confirmarse.</p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded p-3 text-left mb-4 transition-all duration-200 hover:bg-white dark:hover:bg-gray-600">
                  <p class="text-xs text-gray-400 dark:text-gray-500">Folio del reporte</p>
                  <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">RPT-2025-0042</p>
                </div>
                <button disabled class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80">Cerrar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: VENTAS ===== -->
        <div id="panel-ventas" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / ventas</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ventas</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Historial de ventas registradas</p>
              </div>
              <button disabled class="inline-flex items-center gap-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-3 py-1.5 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg> Nueva venta</button>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
              <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 flex justify-between items-center border-b border-gray-200 dark:border-gray-700"><span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Registro</span><span class="text-xs text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-700 px-2 py-0.5 rounded-full border border-gray-200 dark:border-gray-700">8 ventas</span></div>
              <!-- Lista desktop -->
              <div class="hidden md:block divide-y divide-white dark:divide-gray-800">
                <div class="flex items-center gap-3 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">JP</div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2"><span class="text-sm font-semibold text-gray-900 dark:text-white">Juan Pérez</span><span class="font-mono text-[10px] text-gray-400 dark:text-gray-500">VTA-001</span></div>
                    <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>555-1234</span><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span><span>2 productos</span></div>
                  </div>
                  <div class="text-right shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    <p>10/03/2025</p>
                    <p>14:30</p>
                  </div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="text-right shrink-0 min-w-[80px]"><span class="text-sm font-semibold text-gray-900 dark:text-white">$12,800.00</span></div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="flex flex-col items-end gap-1 shrink-0"><span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver</span><span class="text-xs text-blue-600 border border-blue-200 dark:border-blue-700 rounded px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-900/50 hover:border-blue-300 dark:hover:border-blue-600">Ticket</span></div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">MG</div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2"><span class="text-sm font-semibold text-gray-900 dark:text-white">María García</span><span class="font-mono text-[10px] text-gray-400 dark:text-gray-500">VTA-002</span></div>
                    <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>555-5678</span><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span><span>1 producto</span></div>
                  </div>
                  <div class="text-right shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    <p>09/03/2025</p>
                    <p>11:15</p>
                  </div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="text-right shrink-0 min-w-[80px]"><span class="text-sm font-semibold text-gray-900 dark:text-white">$6,500.00</span></div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="flex flex-col items-end gap-1 shrink-0"><span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver</span><span class="text-xs text-blue-600 border border-blue-200 dark:border-blue-700 rounded px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-900/50 hover:border-blue-300 dark:hover:border-blue-600">Póliza</span></div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">CR</div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-baseline gap-2"><span class="text-sm font-semibold text-gray-900 dark:text-white">Carlos Ruiz</span><span class="font-mono text-[10px] text-gray-400 dark:text-gray-500">VTA-003</span></div>
                    <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>555-9012</span><span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span><span>3 productos</span></div>
                  </div>
                  <div class="text-right shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    <p>08/03/2025</p>
                    <p>16:45</p>
                  </div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="text-right shrink-0 min-w-[80px]"><span class="text-sm font-semibold text-gray-900 dark:text-white">$23,400.00</span></div>
                  <div class="w-px h-7 bg-gray-200 dark:bg-gray-700 shrink-0"></div>
                  <div class="flex flex-col items-end gap-1 shrink-0"><span class="text-xs text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-2 py-0.5 bg-white dark:bg-gray-800 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ver</span><span class="text-xs text-blue-600 border border-blue-200 dark:border-blue-700 rounded px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-900/50 hover:border-blue-300 dark:hover:border-blue-600">Póliza</span></div>
                </div>
              </div>
              <!-- Lista mobile -->
              <div class="block md:hidden divide-y divide-white dark:divide-gray-800">
                <div class="flex items-center gap-2 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">JP</div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Juan Pérez</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">10/03/2025 · 2 prod.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">$12,800.00</p><span class="text-xs text-blue-600 transition-colors duration-200 hover:text-blue-800">Ver →</span>
                  </div>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">MG</div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">María García</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">09/03/2025 · 1 prod.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">$6,500.00</p><span class="text-xs text-blue-600 transition-colors duration-200 hover:text-blue-800">Ver →</span>
                  </div>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                  <div class="w-9 h-9 rounded-lg bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">CR</div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Carlos Ruiz</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">08/03/2025 · 3 prod.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">$23,400.00</p><span class="text-xs text-blue-600 transition-colors duration-200 hover:text-blue-800">Ver →</span>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center text-xs text-gray-400 dark:text-gray-500">
                <span>Mostrando 1–3 de 8</span>
                <div class="flex gap-1">
                  <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">‹</span>
                  <span class="px-2.5 py-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded transition-all duration-200 hover:bg-gray-800 dark:hover:bg-white">1</span>
                  <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">2</span>
                  <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">3</span>
                  <span class="px-2 py-1 border border-gray-200 dark:border-gray-700 rounded bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">›</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== PANEL: NUEVA VENTA ===== -->
        <div id="panel-nuevaventa" class="tab-panel transition-all duration-300 ease-out opacity-0 scale-95 hidden border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md">
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2.5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="flex gap-1.5">
              <span class="w-3 h-3 rounded-full bg-red-400"></span>
              <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
              <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">cloudlabs.arrowk / ventas/nueva</span>
          </div>
          <div class="bg-white dark:bg-gray-900 p-5">
            <div class="flex flex-wrap justify-between items-start gap-3 mb-5">
              <div>
                <a href="#" class="text-xs text-gray-400 dark:text-gray-500 inline-flex items-center gap-1 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg> Volver a ventas</a>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-1">Nueva venta</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">Escanea el QR o escribe el N° de serie de cada bicicleta</p>
              </div>
              <div class="text-right">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total</p>
                <p class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums">$23,400.00</p>
                <p class="text-xs text-green-600">− $200.00 con cupón</p>
              </div>
            </div>

            <div class="grid lg:grid-cols-5 gap-5">
              <!-- Izquierda (3/5) -->
              <div class="lg:col-span-3 space-y-4">
                <!-- Bicicleta -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Bicicleta</p>
                  <div class="flex gap-2 mt-2">
                    <div class="flex-1 relative"><input type="text" value="HE0EA2A00SA963753" maxlength="17" disabled class="w-full px-3 py-1.5 border border-green-400 dark:border-green-700 rounded text-sm font-mono uppercase bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 pr-14 transition-all duration-200 focus:ring-1 focus:ring-green-300"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-mono text-green-500">17/17</span></div>
                    <button disabled class="px-4 py-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded text-sm font-medium opacity-60 transition-all duration-200 hover:opacity-80 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg> Buscar</button>
                  </div>
                  <div class="mt-3 border border-green-200 dark:border-green-700 rounded-lg p-3 bg-green-50 dark:bg-green-900/30 flex items-start gap-3 transition-all duration-200 hover:shadow-sm">
                    <div class="w-9 h-9 rounded border border-gray-200 dark:border-gray-700 bg-red-500 shrink-0"></div>
                    <div class="flex-1">
                      <p class="text-sm font-semibold text-gray-900 dark:text-white">Yadea Ova</p>
                      <p class="text-xs text-gray-400 dark:text-gray-500 font-mono">HE0EA2A00SA963753</p>
                      <p class="text-xs text-gray-400 dark:text-gray-500">48V · Rojo</p>
                      <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">$11,500.00</p>
                    </div>
                    <div class="shrink-0 flex flex-col items-end gap-1"><button disabled class="bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold opacity-60 transition-all duration-200 hover:opacity-80 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg> Agregar</button><button disabled class="text-xs text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300">Cancelar</button></div>
                  </div>
                </div>
                <!-- Accesorios -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Accesorios</p>
                  <div class="divide-y divide-white dark:divide-gray-800">
                    <div class="flex items-center gap-2 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-2 rounded">
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">Casco Pro</p>
                        <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>$250.00</span><span class="text-[10px] bg-white dark:bg-gray-700 px-1.5 rounded border border-gray-200 dark:border-gray-700">12 en stock</span></div>
                      </div><button disabled class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-1 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">+ Agregar</button>
                    </div>
                    <div class="flex items-center gap-2 py-2 opacity-50 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-2 rounded">
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">Batería extra</p>
                        <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>$1,800.00</span><span class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-1.5 rounded border border-red-200 dark:border-red-700">Sin stock</span></div>
                      </div><button disabled class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-1 rounded text-xs font-medium opacity-30">+ Agregar</button>
                    </div>
                    <div class="flex items-center gap-2 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 px-2 rounded">
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">Candado U-Lock</p>
                        <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500"><span>$120.00</span><span class="text-[10px] bg-white dark:bg-gray-700 px-1.5 rounded border border-gray-200 dark:border-gray-700">5 en stock</span></div>
                      </div><button disabled class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-1 rounded text-xs font-medium opacity-60 transition-all duration-200 hover:opacity-80">+ Agregar</button>
                    </div>
                  </div>
                </div>
                <!-- Cliente -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <div class="flex justify-between items-center">
                    <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Cliente</p><span class="text-[10px] font-semibold text-red-600 bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded-full">Campos requeridos</span>
                  </div>
                  <div class="grid grid-cols-2 gap-2 mt-2">
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Nombre *</label><input value="Juan" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Primer apellido *</label><input value="Pérez" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Segundo apellido</label><input value="López" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Teléfono *</label><input value="555-1234" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Correo</label><input value="juan@email.com" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div><label class="text-[10px] text-gray-400 dark:text-gray-500">Dirección</label><input value="Av. Reforma 123" disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-2 py-1 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                  </div>
                </div>
                <!-- Vendedor -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Vendedor</p>
                  <select disabled class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 mt-1 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                    <option>— Sin especificar —</option>
                    <option selected>Ana Martínez</option>
                    <option>Carlos Gómez</option>
                  </select>
                </div>
              </div>
              <!-- Derecha (2/5) -->
              <div class="lg:col-span-2 space-y-4">
                <!-- Carrito -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                  <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center"><span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Carrito</span><span class="text-[10px] font-semibold bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-1.5 py-0.5 rounded-full">4</span><span class="text-xs text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">Vaciar</span></div>
                  <div class="px-4 py-2 divide-y divide-white dark:divide-gray-800">
                    <div class="flex items-center gap-2 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                      <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700 bg-red-500 shrink-0"></div>
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">Yadea Ova</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">HE0EA2A00SA963753</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">$11,500.00</p>
                      </div>
                      <div class="flex items-center gap-0.5"><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">−</span><span class="w-5 text-center text-sm font-medium text-gray-900 dark:text-white">1</span><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">+</span></div><span class="text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">✕</span>
                    </div>
                    <div class="flex items-center gap-2 py-2 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                      <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700 flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg></div>
                      <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">Casco Pro</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">$250.00 × 2</p>
                      </div>
                      <div class="flex items-center gap-0.5"><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">−</span><span class="w-5 text-center text-sm font-medium text-gray-900 dark:text-white">2</span><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">+</span></div><span class="text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">✕</span>
                    </div>
                    <div class="flex items-center gap-2 py-2 opacity-50 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded">
                      <div class="w-7 h-7 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-700 flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg></div>
                      <div class="flex-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Candado U-Lock</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">$120.00 × 1</p>
                      </div>
                      <div class="flex items-center gap-0.5"><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">−</span><span class="w-5 text-center text-sm font-medium text-gray-500 dark:text-gray-400">1</span><span class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-sm transition-colors duration-200 hover:bg-gray-200 dark:hover:bg-gray-600">+</span></div><span class="text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">✕</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 space-y-1 text-xs text-gray-400 dark:text-gray-500">
                      <div class="flex justify-between"><span>Subtotal</span><span class="tabular-nums">$12,420.00</span></div>
                      <div class="flex justify-between text-green-600"><span>Descuento cupón</span><span class="tabular-nums">− $200.00</span></div>
                      <div class="flex justify-between text-green-600"><span>Regalo</span><span class="tabular-nums">− $150.00</span></div>
                    </div>
                  </div>
                </div>
                <!-- Cupón -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 transition-all duration-200 hover:shadow-md">
                  <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase">Cupón</p>
                  <div class="mt-1 flex items-center justify-between border border-green-200 dark:border-green-700 rounded-lg px-3 py-1.5 bg-green-50 dark:bg-green-900/30 transition-all duration-200 hover:bg-green-100 dark:hover:bg-green-900/50">
                    <div>
                      <p class="text-xs font-semibold text-green-700 dark:text-green-300 font-mono">DESCUENTO10</p>
                      <p class="text-[10px] text-green-600 dark:text-green-400">10% de descuento</p>
                    </div>
                    <div class="flex items-center gap-2"><span class="text-sm font-bold text-green-700 dark:text-green-300">− $200.00</span><span class="text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">✕</span></div>
                  </div>
                </div>
                <!-- Pago -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md">
                  <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center"><span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pago</span><span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1 transition-colors duration-200 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                      </svg> Dividir pago</span></div>
                  <div class="px-4 py-2 space-y-3">
                    <div class="flex items-center gap-2"><select disabled class="flex-1 border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                        <option selected>Efectivo</option>
                        <option>Tarjeta</option>
                        <option>Transferencia</option>
                      </select>
                      <div class="relative w-24"><span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">$</span><input type="number" value="6200.00" disabled class="w-full pl-5 pr-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-right transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    </div>
                    <div class="flex items-center gap-2"><select disabled class="flex-1 border border-gray-200 dark:border-gray-700 rounded px-2 py-1.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300">
                        <option>Efectivo</option>
                        <option selected>Tarjeta</option>
                        <option>Transferencia</option>
                      </select>
                      <div class="relative w-24"><span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400 dark:text-gray-500">$</span><input type="number" value="5850.00" disabled class="w-full pl-5 pr-2 py-1.5 border border-gray-200 dark:border-gray-700 rounded text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-right transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div><span class="text-gray-400 dark:text-gray-500 transition-colors duration-200 hover:text-red-500">✕</span>
                    </div>
                    <div class="pl-4 border-l-2 border-gray-200 dark:border-gray-700"><input type="text" value="****-1234" disabled placeholder="Folio / últimos 4 dígitos…" class="w-full border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 transition-all duration-200 focus:ring-1 focus:ring-gray-300"></div>
                    <div>
                      <div class="h-1 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                        <div class="w-full h-full bg-green-500 rounded-full transition-all duration-1000"></div>
                      </div>
                      <div class="flex justify-between text-xs text-green-600 mt-1"><span>✓ Pago completo</span><span>$0.00 por asignar</span></div>
                    </div>
                  </div>
                </div>
                <button disabled class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-2.5 rounded-xl text-sm font-semibold opacity-60 transition-all duration-200 hover:opacity-80">Registrar venta · $12,020.00</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- CONTACTO -->
  <section id="contacto" class="py-16 px-6 bg-white dark:bg-gray-900 border-y border-gray-200 dark:border-gray-900 shadow-sm">
    <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-10 items-center">
      <div class="rv opacity-0 translate-y-5 transition-all duration-700 ease-out">
        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Hablemos</p>
        <h2 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-white leading-tight tracking-tight">¿Listo para <em class="not-italic text-gray-500 dark:text-gray-400">digitalizar</em> tu operación?</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-lg mt-3">Cuéntanos de qué se trata. Respondemos en menos de 24 horas con una demo personalizada, sin costo ni compromiso.</p>
        <div class="space-y-2 mt-4">
          <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 transition-all duration-200 hover:translate-x-1"><span class="w-5 h-5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center"><svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg></span>Demo personalizada sin costo</div>
          <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 transition-all duration-200 hover:translate-x-1"><span class="w-5 h-5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center"><svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg></span>Respuesta en menos de 24 horas</div>
          <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 transition-all duration-200 hover:translate-x-1"><span class="w-5 h-5 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center"><svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg></span>Sin contratos ni compromisos</div>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 text-center transition-all duration-300 hover:shadow-xl hover:-translate-y-1 rv opacity-0 translate-y-5 transition-all duration-700 delay-100 ease-out">
        <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 flex items-center justify-center mx-auto mb-3 transition-all duration-300 hover:scale-110"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#16a34a" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
          </svg></div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">Escríbenos por WhatsApp</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">Cuéntanos tu operación y te preparamos una demo personalizada.</p>
        <a href="https://wa.me/5215511743162?text=Hola%2C%20me%20interesa%20conocer%20m%C3%A1s%20sobre%20ArrowK.%20%C2%BFPodr%C3%ADan%20darme%20informes%3F" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-2.5 rounded-full text-sm font-medium hover:bg-gray-800 dark:hover:bg-white transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-sm hover:shadow-md"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
          </svg> Contactar por WhatsApp</a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="border-t border-gray-200 dark:border-gray-700 py-6 px-6 bg-white dark:bg-gray-900">
    <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-2"><img src="{{ asset('arrowk/favicon-arrowk.svg') }}" alt="ArrowK" class="h-8 w-auto dark:hidden"><img src="{{ asset('arrowk/favicon-arrowk-white.svg') }}" alt="ArrowK" class="h-8 w-auto hidden dark:block"></div>
      <p class="text-xs text-gray-400 dark:text-gray-500">© {{ date('Y') }} ArrowK · CloudLabs · Ixtapaluca, Estado de México</p>
      <div class="flex gap-4 text-xs text-gray-400 dark:text-gray-500">
        <a href="mailto:cloudlabs342@gmail.com" class="transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300">Correo</a>
        <a href="https://wa.me/5511743162" class="transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300">WhatsApp</a>
        @auth
        <a href="{{ url('/dashboard') }}" class="transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300">Panel</a>
        @else
        <a href="{{ route('login') }}" class="transition-colors duration-200 hover:text-gray-700 dark:hover:text-gray-300">Iniciar sesión</a>
        @endauth
      </div>
    </div>
  </footer>

  <!-- Alpine.js y scripts -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
  <script>
    // Nav scroll
    window.addEventListener('scroll', () => {
      const nav = document.getElementById('nav');
      nav.classList.toggle('border-gray-200 dark:border-gray-700', window.scrollY > 20);
      nav.classList.toggle('border-transparent', window.scrollY <= 20);
    }, {
      passive: true
    });

    // Mobile menu
    function toggleMenu() {
      const m = document.getElementById('mob-menu');
      const b = document.getElementById('burger');
      const open = m.classList.contains('translate-y-0');
      m.classList.toggle('translate-y-0', !open);
      m.classList.toggle('pointer-events-auto', !open);
      m.classList.toggle('-translate-y-full', open);
      b.classList.toggle('open', !open);
      document.body.style.overflow = open ? '' : 'hidden';
    }

    function closeMenu() {
      document.getElementById('mob-menu').classList.remove('translate-y-0');
      document.getElementById('mob-menu').classList.add('-translate-y-full');
      document.getElementById('mob-menu').classList.remove('pointer-events-auto');
      document.getElementById('burger').classList.remove('open');
      document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeMenu();
    });

    // ---- FUNCIÓN PRINCIPAL DE PESTAÑAS ----
    function activateTab(tabId) {
      // 1. Ocultar todos los paneles y resetear clases de animación
      document.querySelectorAll('.tab-panel').forEach(p => {
        p.style.display = 'none';
        p.classList.remove('opacity-100', 'scale-100');
        p.classList.add('opacity-0', 'scale-95');
      });

      // 2. Mostrar y animar el panel seleccionado
      const panel = document.getElementById('panel-' + tabId);
      if (panel) {
        panel.style.display = 'block';
        // Forzar reflow para que la transición se active
        panel.offsetHeight;
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('opacity-100', 'scale-100');
      }

      // 3. Actualizar estilos de pestañas (usando clases Tailwind)
      document.querySelectorAll('.tab-btn').forEach(btn => {
        const isActive = btn.dataset.tab === tabId;
        // Remover todas las clases de estado
        btn.classList.remove('bg-gray-900', 'text-white', 'border-gray-900', 'bg-gray-50', 'text-gray-500', 'border-gray-200', 'dark:bg-gray-700', 'dark:text-gray-400', 'dark:border-gray-700', 'dark:bg-white', 'dark:text-gray-900', 'dark:border-white');
        // Añadir las clases según estado
        if (isActive) {
          btn.classList.add('bg-gray-900', 'text-white', 'border-gray-900', 'dark:bg-white', 'dark:text-gray-900', 'dark:border-white');
        } else {
          btn.classList.add('bg-gray-50', 'text-gray-500', 'border-gray-200', 'dark:bg-gray-700', 'dark:text-gray-400', 'dark:border-gray-700');
        }
      });
    }

    // 4. Asignar eventos a las pestañas
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        activateTab(tabId);
      });
    });

    // 5. Activar la primera pestaña por defecto (Pedidos)
    document.querySelector('.tab-btn.active')?.click();

    // Acordeones (sucursales)
    function toggleAcordeon(header) {
      const container = header.parentElement;
      const body = container.querySelector('div[style*="display: none;"]');
      const arrow = header.querySelector('svg');
      if (body) {
        if (body.style.display === 'none') {
          body.style.display = 'block';
          if (arrow) arrow.style.transform = 'rotate(90deg)';
        } else {
          body.style.display = 'none';
          if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
      }
    }

    // Intersection Observer para animaciones al hacer scroll
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('opacity-100', 'translate-y-0');
          e.target.classList.remove('opacity-0', 'translate-y-5');
          obs.unobserve(e.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -24px 0px'
    });

    document.querySelectorAll('.rv').forEach(el => {
      el.classList.add('opacity-0', 'translate-y-5', 'transition', 'duration-700', 'ease-out');
      obs.observe(el);
    });
  </script>

  <style>
    /* Solo para el efecto de animación y el punto verde (si no está en Tailwind) */
    .animate-pulse-slow {
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.3;
      }
    }

    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>

</body>

</html>