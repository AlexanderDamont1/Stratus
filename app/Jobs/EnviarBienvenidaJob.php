<?php
// app/Jobs/EnviarBienvenidaJob.php

namespace App\Jobs;

use App\Models\Usuario;
use App\Notifications\BienvenidaNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarBienvenidaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly Usuario $usuario,
        public readonly string $nombreNegocio,
        public readonly ?string $trialEndsAt = null,
    ) {}

    public function handle(): void
    {
        if (empty($this->usuario->correo)) {
            return;
        }

        $this->usuario->notify(new BienvenidaNotification(
            nombre:        $this->usuario->nombre_usuario,
            nombreNegocio: $this->nombreNegocio,
            trialEndsAt:   $this->trialEndsAt,
        ));
    }
}