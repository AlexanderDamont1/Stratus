<?php
namespace App\Traits;

trait ResolvesAdminRoute
{
    private function routeByRol(string $recurso): string
    {
        $rol = auth()->user()->id_rol;

        $rutas = [
            'modelos'        => $rol === 1 ? 'admin.catalogo.index'       : 'gestor.vehiculos.modelos.index',
            'colores'        => $rol === 1 ? 'admin.catalogo.index'       : 'gestor.vehiculos.colores.index',
            'voltajes'       => $rol === 1 ? 'admin.catalogo.voltajes.index'      : 'gestor.vehiculos.voltajes.index',
            'marcas'         => 'admin.catalogo.index', // solo rol 1
            'modelo-voltaje' => $rol === 1 ? 'admin.catalogo.index': 'modelo-voltaje',
            'bicicletas'     => $rol === 1 ? 'bicicletas.index'       : 'bicicletas.index',
        ];

        return $rutas[$recurso];
    }
}