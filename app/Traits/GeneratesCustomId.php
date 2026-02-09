<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait GeneratesCustomId
{
    protected static function bootGeneratesCustomId()
    {
        static::creating(function ($model) {

            $primaryKey = $model->getKeyName();

            if (!empty($model->$primaryKey)) {
                return;
            }

            $prefix = $model->idPrefix();
            $date   = Carbon::now()->format('ymd');

            $lastId = DB::table($model->getTable())
                ->where($primaryKey, 'like', "{$prefix}{$date}%")
                ->orderBy($primaryKey, 'desc')
                ->value($primaryKey);

            $sequence = $lastId
                ? intval(substr($lastId, -4)) + 1
                : 1;

            $model->$primaryKey =
                $prefix .
                $date .
                str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    abstract protected function idPrefix(): string;
}
