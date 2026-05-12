<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioConfig extends Model
{
    protected $table = 'negocio_config';
    protected $primaryKey = 'id_ncf';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_ncf',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $fecha  = now()->format('ymd');
            $letras = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
            $nums   = random_int(100, 999);

            $model->id_ncf = "NCF{$fecha}{$letras}{$nums}";
        });
    }
}