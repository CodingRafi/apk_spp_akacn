<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademikDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function KalenderAkademik()
    {
        return $this->belongsTo(KalenderAkademik::class);
    }
}
