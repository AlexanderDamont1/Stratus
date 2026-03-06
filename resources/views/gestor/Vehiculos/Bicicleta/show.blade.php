@extends('layouts.app')

@section('title', 'Detalles de Bicicleta')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Detalles de la Bicicleta</h3>
            <div class="flex space-x-2">
                <a href="{{ route('bicicletas.edit', $bicicleta->num_serie) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('bicicletas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información General -->
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Información General</h4>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Número de Serie</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->num_serie }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($bicicleta->status == 'disponible') bg-green-100 text-green-800
                                    @elseif($bicicleta->status == 'en_mantenimiento') bg-yellow-100 text-yellow-800
                                    @elseif($bicicleta->status == 'prestado') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $bicicleta->status)) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Relaciones -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Asignaciones</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Negocio</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->negocio->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->cliente->nombre ?? 'Sin asignar' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Especificaciones -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Especificaciones</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Producto</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->producto->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Modelo</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->modelo->nombre ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Voltaje</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bicicleta->voltaje->voltaje ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Color</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center">
                                <span class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $bicicleta->color->codigo_hex ?? '#000000' }}"></span>
                                {{ $bicicleta->color->nombre ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Mantenimientos -->
                <div class="col-span-2 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Historial de Mantenimientos</h4>
                    @if($bicicleta->mantenimientos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Costo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bicicleta->mantenimientos as $mantenimiento)
                                    <tr>
                                        <td class="px-4 py-2 text-sm">{{ $mantenimiento->fecha }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $mantenimiento->tipo }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $mantenimiento->descripcion }}</td>
                                        <td class="px-4 py-2 text-sm">${{ number_format($mantenimiento->costo, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No hay mantenimientos registrados</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection