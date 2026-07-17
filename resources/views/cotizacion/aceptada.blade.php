@extends('layouts.publico')

@php
    $tipo = $cotizacion->reparacion?->tipo;
@endphp

@section('titulo', 'Cotización aceptada')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-green-100 text-green-800
                 dark:bg-green-800/30 dark:text-green-400">
        Cotización aceptada
    </span>
@endsection

@section('heading')
    ¡Listo!<br>Cotización aceptada
@endsection

@section('sub')
    Hemos recibido tu respuesta y comenzaremos con el trabajo.
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900/30
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div class="min-w-0 text-left">
        <p class="text-xs text-gray-400">Cotización</p>
        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
            {{ $cotizacion->id_cotizacion }}
        </p>
    </div>
@endsection

@section('contenido')
    <div class="px-6 py-6 space-y-3">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Detalle</p>
        @if($tipo === 'mantenimiento')
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Incluiremos el reemplazo de piezas junto con tu mantenimiento y comenzaremos
                el trabajo a la brevedad.
            </p>
        @elseif($tipo === 'garantia')
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Procederemos con el reemplazo del componente cubierto por garantía a la brevedad.
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Nuestro equipo comenzará el trabajo a la brevedad.
            </p>
        @endif
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700"></div>

    <div class="px-6 py-6 space-y-4">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">¿Qué sigue?</p>
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30
                             flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    Nuestro equipo trabajará en tu vehículo
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30
                             flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </span>
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    Te avisaremos por correo cuando esté listo para recoger
                </span>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-start gap-2">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-400 leading-relaxed">
            Conserva el folio de tu cotización para cualquier aclaración con nuestro equipo.
        </span>
    </div>
@endsection
