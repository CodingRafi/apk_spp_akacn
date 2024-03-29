<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function matkul(){
        return $this->belongsToMany(Matkul::class, 'kurikulum_matkul', 'kurikulum_id', 'matkul_id');
    }

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }
}
