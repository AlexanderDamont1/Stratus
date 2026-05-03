<?php

namespace App\Jobs;

use App\Models\Usuario;
use App\Notifications\VerificarEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarVerificacionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly Usuario $vendedor,
        public readonly string $token,
        public readonly string $nombre,
    ) {}

    public function handle(): void
    {
        if (empty($this->vendedor->correo)) {
            return;
        }

        $this->vendedor->notify(
            new VerificarEmailNotification($this->token, $this->nombre)
        );
    }
}