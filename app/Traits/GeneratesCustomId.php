<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesCustomId
{
    protected static function bootGeneratesCustomId(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $prefix  = method_exists($model, 'idPrefix') ? $model->idPrefix() : 'ID';
                $date    = now()->format('ymd');
                $rand    = rand(100, 999);
                $letras = Str::upper(Str::random(3));

                $model->{$model->getKeyName()} = $prefix . $date . $letras . $rand;
            }
        });
    }
}