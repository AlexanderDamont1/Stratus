<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioConfig extends Model
{
    protected $table = 'negocio_config';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'icono',
        'tipo',
        'opciones',
        'valor_default',
        'grupo',
        'orden',
        'activo',
    ];

    protected $casts = [
        'opciones' => 'array',
        'activo'   => 'boolean',
    ];

    public function valores()
    {
        return $this->hasMany(NegocioConfigValor::class, 'clave', 'clave');
    }

    public function valorParaNegocio(string $idNegocio): string
    {
        $valor = $this->valores->firstWhere('id_negocio', $idNegocio);
        return $valor?->valor ?? $this->valor_default;
    }
}