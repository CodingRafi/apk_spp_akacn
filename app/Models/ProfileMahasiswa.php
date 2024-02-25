<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileMahasiswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function rombel(){
        return $this->belongsTo(Rombel::class, 'rombel_id', 'id');
    }

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }
}
