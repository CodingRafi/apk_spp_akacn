<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function jenisKelas(){
        return $this->belongsTo(JenisKelas::class, 'jenis_kelas_id', 'id');
    }
}
