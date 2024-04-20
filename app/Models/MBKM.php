<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBKM extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'mbkm';

    public function mahasiswa(){
        return $this->belongsToMany(User::class, 'mbkm_mhs', 'mbkm_id', 'mhs_id');
    }

    public function dosenPembimbing(){
        return $this->belongsToMany(User::class, 'mbkm_dosen_pembimbing', 'mbkm_id', 'dosen_id');
    }

    public function dosenPenguji(){
        return $this->belongsToMany(User::class, 'mbkm_dosen_penguji', 'mbkm_id', 'dosen_id');
    }
}
