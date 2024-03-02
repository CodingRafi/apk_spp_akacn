<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function roles(){
        return $this->belongsToMany(Role::class, 'template_surat_role', 'template_surat_id', 'role_id');
    }
}
