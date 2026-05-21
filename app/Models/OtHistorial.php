<?php
// app/Models/OtHistorial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtHistorial extends Model
{
    protected $table      = 'ot_historial';
    public    $timestamps = false;

    protected $fillable = [
        'id_ot', 'estado_anterior', 'estado_nuevo', 'id_usuario', 'nota',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario() { return $this->belongsTo(Usuario::class,    'id_usuario', 'id_usuario'); }
    public function ot()      { return $this->belongsTo(OrdenTrabajo::class, 'id_ot',    'id_ot'); }
}