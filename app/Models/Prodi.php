<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $table = 'prodi';

    public function semester(){
        return $this->hasMany(Semester::class, 'prodi_id');
    }

    public function jenjang(){
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }
}
