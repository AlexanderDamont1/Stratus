@extends('layouts.publico')

@php
    $mant = $cotizacion->reparacion;
@endphp

@section('titulo', 'Cotización — Responder')

@section('badge')
    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                 bg-sky-100 text-sky-800
                 dark:bg-sky-800/30 dark:text-sky-400">
        Cotización pendiente
    </span>
@endsection

@section('heading')
    @if($mant->tipo === 'mantenimiento')
        ¿Incluimos<br>estas piezas?
    @elseif($mant->tipo === 'garantia')
        ¿Procedemos<br>con el reemplazo?
    @else
        ¿Procedemos<br>con la reparación?
    @endif
@endsection

@section('sub')
    @if($mant->tipo === 'mantenimiento')
        Tu mantenimiento se realizará de cualquier forma. Esta pregunta es únicamente
        sobre las piezas adicionales que nuestro técnico detectó durante la inspección.
    @elseif($mant->tipo === 'garantia')
        Revisamos tu vehículo por el reclamo de garantía. Confirma si deseas proceder
        con el reemplazo y, si aplica, el costo adicional no cubierto.
    @else
        Revisa el detalle a continuación y confirma si deseas proceder con el trabajo.
    @endif
@endsection

@section('meta')
    <div class="w-9 h-9 rounded-full bg-sky-100 dark:bg-sky-900/30
                flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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

    {{-- Trabajo a realizar --}}
    <div class="px-6 py-6 space-y-2">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Trabajo a realizar</p>
        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4">
            {{ $cotizacion->descripcion_trabajo }}
        </p>
    </div>

    {{-- Piezas / componentes --}}
    @if(!empty($cotizacion->piezas_detalle))
        <div class="border-t border-gray-100 dark:border-gray-700"></div>
        <div class="px-6 py-6 space-y-2">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1">Piezas / componentes</p>
            @foreach($cotizacion->piezas_detalle as $p)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">{{ $p['nombre'] }} &times; {{ $p['cantidad'] }}</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">${{ number_format($p['subtotal'], 2) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Totales --}}
    <div class="border-t border-gray-100 dark:border-gray-700"></div>
    <div class="px-6 py-6 space-y-1.5">
        @if($cotizacion->costo_piezas > 0)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Piezas</span>
                <span class="text-gray-600 dark:text-gray-300 font-medium">${{ number_format($cotizacion->costo_piezas, 2) }}</span>
            </div>
        @endif
        @if($cotizacion->costo_mano_obra > 0)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Mano de obra</span>
                <span class="text-gray-600 dark:text-gray-300 font-medium">${{ number_format($cotizacion->costo_mano_obra, 2) }}</span>
            </div>
        @endif
        <div class="flex items-center justify-between pt-2 mt-1 border-t border-gray-100 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Total</span>
            <span class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($cotizacion->costo_total, 2) }}</span>
        </div>
    </div>

    @if($mant->tipo === 'mantenimiento')
        <div class="px-6 pb-6">
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl px-4 py-3">
                <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                    Si eliges <strong>"Solo el mantenimiento"</strong>, no se realizará el reemplazo
                    de piezas y el costo será únicamente el del mantenimiento base.
                </p>
            </div>
        </div>
    @endif

    {{-- Botones --}}
    <div class="border-t border-gray-100 dark:border-gray-700"></div>
    <div class="px-6 py-6">
        <div class="flex gap-2">
            <form method="POST" action="{{ route('cotizacion.responder', $cotizacion->token) }}" class="flex-1">
                @csrf
                <input type="hidden" name="respuesta" value="1">
                <button type="submit"
                        class="w-full px-4 py-2.5 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600
                               text-white text-sm font-medium rounded-xl transition-all duration-150 shadow-sm">
                    @if($mant->tipo === 'mantenimiento') Sí, inclúyelo
                    @else Sí, proceder @endif
                </button>
            </form>
            <form method="POST" action="{{ route('cotizacion.responder', $cotizacion->token) }}" class="flex-1">
                @csrf
                <input type="hidden" name="respuesta" value="0">
                <button type="submit"
                        class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600
                               hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300
                               text-sm font-medium rounded-xl transition-all duration-150">
                    @if($mant->tipo === 'mantenimiento') Solo el mantenimiento
                    @else No por ahora @endif
                </button>
            </form>
        </div>
    </div>

    {{-- Nota de expiración --}}
    <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                px-6 py-4 flex items-start gap-2">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs text-gray-400 leading-relaxed">
            Enlace válido hasta
            <strong class="font-medium text-gray-500 dark:text-gray-400">
                {{ \Carbon\Carbon::parse($cotizacion->expires_at)->locale('es')->isoFormat('D [de] MMMM, h:mm A') }}
            </strong>.
        </span>
    </div>
@endsection
