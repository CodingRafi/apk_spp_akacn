<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileAsdos extends Model
{
    use HasFactory;

    protected $table = 'profile_asdos';
    protected $guarded = ['id'];
}
