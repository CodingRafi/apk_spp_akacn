<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function tahunAjaran(){
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function mahasiswa(){
        return $this->belongsToMany(Mahasiswa::class, 'potongan_mhs', 'potongan_id', 'mhs_id');
    }
}
