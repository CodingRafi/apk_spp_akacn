<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';
    protected $guarded = ['id'];

    public function pengajar(){
        return $this->belongsTo(User::class, 'pengajar_id');
    }

    public function mahasiswa(){
        return $this->belongsToMany(User::class, 'jadwal_presensi', 'jadwal_id', 'mhs_id')->withPivot('status');
    }
}
