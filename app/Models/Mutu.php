<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'mutu';

    public function prodi(){
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }
}
