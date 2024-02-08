<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhitelistIP extends Model
{
    use HasFactory;

    protected $table = 'whitelist_ip';
    protected $guarded = ['id'];
}
