<?php
// app/Events/OtListaParaEntrega.php

namespace App\Events;

use App\Models\OrdenTrabajo;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtListaParaEntrega
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $idOt,
        public readonly string $idNegocio,
    ) {}
}