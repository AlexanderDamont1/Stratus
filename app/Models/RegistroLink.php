<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class RegistroLink extends Model
{
    use GeneratesCustomId;

    protected $table      = 'registro_links';
    protected $primaryKey = 'id_link';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_link',
        'token',
        'max_users',
        'usado',
        'expires_at',
    ];

    protected $casts = [
        'usado'      => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected function idPrefix(): string
    {
        return 'LNK';
    }

    /*
    |----------------------------------------
    | Validación de estado
    |----------------------------------------
    */

    public function estaDisponible(): bool
    {
        if ($this->usado) {
            return false;
        }

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }

        return true;
    }

    /*
    |----------------------------------------
    | Scopes
    |----------------------------------------
    */

    public function scopeDisponibles($query)
    {
        return $query->where('usado', false)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeExpirados($query)
    {
        return $query->where('usado', false)
                     ->where('expires_at', '<=', now());
    }
}