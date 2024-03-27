<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembiayaan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pembiayaans';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
