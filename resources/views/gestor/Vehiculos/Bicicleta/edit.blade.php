@extends('layouts.app')

@section('title', 'Editar Bicicleta')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Editar Bicicleta - {{ $bicicleta->num_serie }}</h3>
        </div>

        <form action="{{ route('bicicletas.update', $bicicleta->num_serie) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Número de Serie (solo lectura) -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Serie</label>
                    <input type="text" value="{{ $bicicleta->num_serie }}" disabled
                           class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Negocio -->
                <div>
                    <label for="id_negocio" class="block text-sm font-medium text-gray-700 mb-1">Negocio <span class="text-red-500">*</span></label>
                    <select name="id_negocio" id="id_negocio" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_negocio') border-red-500 @enderror">
                        <option value="">Seleccione un negocio</option>
                        @foreach($negocios as $negocio)
                            <option value="{{ $negocio->id_negocio }}" {{ old('id_negocio', $bicicleta->id_negocio) == $negocio->id_negocio ? 'selected' : '' }}>
                                {{ $negocio->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_negocio')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cliente -->
                <div>
                    <label for="id_cliente" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select name="id_cliente" id="id_cliente"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_cliente') border-red-500 @enderror">
                        <option value="">Sin asignar</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', $bicicleta->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_cliente')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Producto -->
                <div>
                    <label for="id_producto" class="block text-sm font-medium text-gray-700 mb-1">Producto <span class="text-red-500">*</span></label>
                    <select name="id_producto" id="id_producto" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_producto') border-red-500 @enderror">
                        <option value="">Seleccione un producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id_producto }}" {{ old('id_producto', $bicicleta->id_producto) == $producto->id_producto ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_producto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modelo -->
                <div>
                    <label for="id_modelo" class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                    <select name="id_modelo" id="id_modelo" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_modelo') border-red-500 @enderror">
                        <option value="">Seleccione un modelo</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id_modelo }}" {{ old('id_modelo', $bicicleta->id_modelo) == $modelo->id_modelo ? 'selected' : '' }}>
                                {{ $modelo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_modelo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Voltaje -->
                <div>
                    <label for="id_voltaje" class="block text-sm font-medium text-gray-700 mb-1">Voltaje <span class="text-red-500">*</span></label>
                    <select name="id_voltaje" id="id_voltaje" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_voltaje') border-red-500 @enderror">
                        <option value="">Seleccione un voltaje</option>
                        @foreach($voltajes as $voltaje)
                            <option value="{{ $voltaje->id_voltaje }}" {{ old('id_voltaje', $bicicleta->id_voltaje) == $voltaje->id_voltaje ? 'selected' : '' }}>
                                {{ $voltaje->voltaje }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_voltaje')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div>
                    <label for="id_color" class="block text-sm font-medium text-gray-700 mb-1">Color <span class="text-red-500">*</span></label>
                    <select name="id_color" id="id_color" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_color') border-red-500 @enderror">
                        <option value="">Seleccione un color</option>
                        @foreach($colores as $color)
                            <option value="{{ $color->id_color }}" {{ old('id_color', $bicicleta->id_color) == $color->id_color ? 'selected' : '' }}>
                                {{ $color->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_color')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Seleccione un estado</option>
                        <option value="disponible" {{ old('status', $bicicleta->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="en_mantenimiento" {{ old('status', $bicicleta->status) == 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        <option value="prestado" {{ old('status', $bicicleta->status) == 'prestado' ? 'selected' : '' }}>Prestado</option>
                        <option value="danada" {{ old('status', $bicicleta->status) == 'danada' ? 'selected' : '' }}>Dañada</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('bicicletas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </a>
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection