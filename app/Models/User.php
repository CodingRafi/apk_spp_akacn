<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function dosen(){
        return $this->hasOne(ProfileDosen::class, 'user_id', 'id');
    }

    public function mahasiswa(){
        return $this->hasOne(ProfileMahasiswa::class, 'user_id', 'id');
    }

    public function asdos(){
        return $this->hasOne(ProfileAsdos::class, 'user_id', 'id');
    }

    public function petugas(){
        return $this->hasOne(ProfilePetugas::class, 'user_id', 'id');
    }

    public function jadwalMahasiswa(){
        return $this->belongsToMany(Jadwal::class, 'jadwal_presensi', 'mhs_id', 'jadwal_id')->withPivot('status');
    }

    public function jadwalPengajar(){
        return $this->hasMany(Jadwal::class, 'pengajar_id');
    }
}
