<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class Transaccion extends Model
{
    use HasFactory, OracleCompatible;

    protected $table = 'TRANSACCION';
    protected $primaryKey = 'TRA_ID';
    public $timestamps = true;

    protected $fillable = [
        'TRA_CODIGO',
        'TRA_DESCRIPCION'
    ];
}
