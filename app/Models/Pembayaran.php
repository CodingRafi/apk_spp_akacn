<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pembayaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mhs_id');
    }

    public function verify()
    {
        return $this->belongsTo(User::class, 'verify_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public static function getPembayaranMahasiswa($user_id)
    {
        $user = User::find($user_id);
        $mhs = $user->mahasiswa;

        $tahun_semester = DB::table('tahun_semester')
            ->where('prodi_id', $mhs->prodi_id)
            ->where('tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get()
            ->pluck('id');

        $tahun_pembayaran_semester = DB::table('tahun_pembayaran')
            ->select('tahun_pembayaran.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->whereIn('tahun_semester_id', $tahun_semester)
            ->get()
            ->map(function ($data) {
                $data->type = 'semester';
                return $data;
            });

        $tahun_pembayaran_lainnya = DB::table('tahun_pembayaran_lain')
            ->select('tahun_pembayaran_lain.id', 'pembayaran_lainnyas.nama')
            ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where('tahun_pembayaran_lain.prodi_id', $mhs->prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get()
            ->map(function ($data) {
                $data->type = 'lainnya';
                return $data;
            });

        return array_merge($tahun_pembayaran_semester->toArray(), $tahun_pembayaran_lainnya->toArray());
    }
}
