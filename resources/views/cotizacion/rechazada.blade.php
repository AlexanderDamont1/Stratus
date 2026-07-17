@extends('layouts.publico')

@php
    // La cotización puede volver desde el controlador sin la relación
    // "reparacion" precargada (procesarRespuestaToken() usa ->fresh()),
    // así que Eloquent la carga de forma diferida acá. Con null-safe (?->)
    // evitamos un error si por algún motivo no existiera la orden asociada.
    $tipo = $cotizacion->reparacion?->tipo;
@endphp

@section('titulo', 'Respuesta registrada')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-gray-100 text-gray-700
                 dark:bg-gray-700 dark:text-gray-300">
        Respuesta registrada
    </span>
@endsection

@section('heading')
    Respuesta<br>registrada
@endsection

@section('sub')
    Hemos recibido tu decisión sobre la cotización.
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                Realizaremos únicamente el mantenimiento base, sin el reemplazo de piezas
                adicionales. Nos comunicaremos contigo cuando esté listo.
            </p>
        @elseif($tipo === 'garantia')
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                No se realizará el reemplazo del componente cubierto por garantía. Un miembro
                de nuestro equipo se comunicará contigo para revisar otras alternativas.
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Nuestro equipo ah recibido tu decisión. Te la tendremos listo lo mas rapido posible.
            </p>
        @endif
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-start gap-2">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-400 leading-relaxed">
            Si tienes alguna duda sobre esta decisión, contáctanos directamente.
        </span>
    </div>
@endsection
