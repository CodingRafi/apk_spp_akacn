<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function potongan(){
        return $this->belongsToMany(Potongan::class, 'potongan_mhs', 'mhs_id', 'potongan_id');
    }
}
