@extends('layouts.publico')

@section('titulo', $exito ? 'Robo confirmado' : 'Enlace inválido')

@section('badge')
    @if($exito)
        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                     bg-green-100 text-green-800
                     dark:bg-green-800/30 dark:text-green-400">
            Robo confirmado
        </span>
    @else
        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                     bg-red-100 text-red-800
                     dark:bg-red-800/30 dark:text-red-400">
            Enlace inválido
        </span>
    @endif
@endsection

@section('heading')
    @if($exito)
        Robo<br>confirmado
    @else
        Enlace<br>inválido
    @endif
@endsection

@section('sub')
    {{ $mensaje }}
@endsection

@if($exito)
@section('meta')
    <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900/30
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div class="min-w-0 text-left">
        <p class="text-xs text-gray-400">Folio</p>
        <p class="text-sm font-medium font-mono text-gray-800 dark:text-gray-200 truncate">
            {{ $folio }}
        </p>
    </div>
@endsection
@endif

@section('contenido')
    @if($exito)
        <div class="px-6 py-6 space-y-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Detalle del reporte</p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Folio</span>
                <span class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $folio }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">N.º de serie</span>
                <span class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $serie }}</span>
            </div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                    px-6 py-4 flex items-start gap-2">
            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs text-gray-400 leading-relaxed">
                Guarda tu folio para cualquier aclaración con la sucursal.
            </span>
        </div>
    @else
        <div class="px-6 py-6 space-y-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Detalle</p>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                Contacta a la sucursal donde reportaste el robo para más información.
            </p>
        </div>
    @endif
@endsection
