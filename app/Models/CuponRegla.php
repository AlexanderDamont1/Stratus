<?php

namespace App\Models;

use App\Traits\GeneratesCustomId;
use Illuminate\Database\Eloquent\Model;

class CuponRegla extends Model
{
    use GeneratesCustomId;

    protected $table      = 'cupon_reglas';
    protected $primaryKey = 'id_regla';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'id_regla',
        'id_cupon',
        'tipo',
        'valor',
    ];

    protected function idPrefix(): string { return 'REG'; }

    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'id_cupon', 'id_cupon');
    }
}