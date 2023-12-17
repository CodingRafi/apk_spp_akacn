<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mahasiswa(){
        return $this->belongsTo(User::class, 'mhs_id');
    }

    public function verify(){
        return $this->belongsTo(User::class, 'verify_id');
    }

    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
