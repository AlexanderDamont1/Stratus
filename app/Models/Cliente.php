<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesCustomId;
use Illuminate\Notifications\Notifiable;

class Cliente extends Model
{
    use GeneratesCustomId, Notifiable;  // ← esto faltaba

    protected $table      = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_cliente',
        'id_negocio',
        'nombre_cliente',
        'apellido1',
        'apellido2',
        'telefono',
        'correo',
    ];

    protected function idPrefix(): string { return 'CLI'; }

    public function routeNotificationForMail($notification = null): ?string
    {
        return !empty($this->correo) ? $this->correo : null;
    }

    public function negocio()  { return $this->belongsTo(Negocio::class,  'id_negocio', 'id_negocio'); }
    public function bicicletas(){ return $this->hasMany(Bicicleta::class,  'id_cliente', 'id_cliente'); }
    public function ventas()   { return $this->hasMany(Venta::class,       'id_cliente', 'id_cliente'); }
}