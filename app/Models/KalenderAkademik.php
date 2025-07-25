<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function KalenderAkademikDetail()
    {
        return $this->hasMany(KalenderAkademikDetail::class);
    }

    public static function getKalenderAkademikAktif(){
        return KalenderAkademik::with('kalenderAkademikDetail')
            ->where('is_active', 1)
            ->first();
    }
}
