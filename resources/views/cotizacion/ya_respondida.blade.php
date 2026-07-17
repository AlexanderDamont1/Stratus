@extends('layouts.publico')

@section('titulo', 'Ya respondida')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-sky-100 text-sky-800
                 dark:bg-sky-800/30 dark:text-sky-400">
        Ya respondida
    </span>
@endsection

@section('heading')
    Esta cotización<br>ya fue respondida
@endsection

@section('sub')
    Ya registramos tu respuesta anteriormente.
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-sky-100 dark:bg-sky-900/30
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
            Si tienes alguna duda sobre tu respuesta, comunícate directamente con nosotros.
        </p>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-start gap-2">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-400 leading-relaxed">
            Cada enlace de cotización solo puede responderse una vez.
        </span>
    </div>
@endsection
