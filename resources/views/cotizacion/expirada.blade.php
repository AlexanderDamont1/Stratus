@extends('layouts.publico')

@section('titulo', 'Enlace expirado')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-amber-100 text-amber-800
                 dark:bg-amber-800/30 dark:text-amber-400">
        Enlace expirado
    </span>
@endsection

@section('heading')
    Enlace<br>expirado
@endsection

@section('sub')
    Este enlace ya no está disponible.
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-900/30
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
            No te preocupes — nuestro equipo se comunicará contigo directamente
            para resolver tu orden.
        </p>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-start gap-2">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-400 leading-relaxed">
            Los enlaces de cotización son válidos por 12 horas desde su envío.
        </span>
    </div>
@endsection
