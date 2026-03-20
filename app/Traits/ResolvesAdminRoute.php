<?php
namespace App\Traits;

trait ResolvesAdminRoute
{
    private function routeByRol(string $recurso): string
    {
        $rol = auth()->user()->id_rol;

        $rutas = [
            'modelos'        => $rol === 1 ? 'admin.catalogo.modelos.index'       : 'gestor.vehiculos.modelos.index',
            'colores'        => $rol === 1 ? 'admin.catalogo.colores.index'       : 'gestor.vehiculos.colores.index',
            'voltajes'       => $rol === 1 ? 'admin.catalogo.voltajes.index'      : 'gestor.vehiculos.voltajes.index',
            'marcas'         => 'admin.catalogo.marcas.index', // solo rol 1
            'modelo-voltaje' => $rol === 1 ? 'admin.catalogo.modelo-voltaje.index': 'modelo-voltaje',
        ];

        return $rutas[$recurso];
    }
}