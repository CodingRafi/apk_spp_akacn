<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function prodi(){
        return $this->belongsToMany(Prodi::class, 'matkul_prodi', 'matkul_id', 'prodi_id');
    }
}
