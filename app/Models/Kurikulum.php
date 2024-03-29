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
        return $this->hasMany(Matkul::class, 'kurikulum_id', 'id');
    }

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }
}
