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
                $date    = now()->format('ymdHis');
                $rand    = rand(100, 999);
                $model->{$model->getKeyName()} = $prefix . $date . $rand;
            }
        });
    }
}