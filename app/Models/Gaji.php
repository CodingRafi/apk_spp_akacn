<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'gaji';

    public function users(){
        return $this->belongsTo(User::class);
    }
}
