<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function tahun_ajaran(){
        return $this->belongsToMany(TahunAjaran::class, 'semester_tahun', 'semester_id', 'tahun_ajaran_id')->withPivot('ket', 'nominal', 'publish');
    }

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
