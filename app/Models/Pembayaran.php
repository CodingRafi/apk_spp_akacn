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
        $user = User::findOrFail($user_id);
        $mhs = $user->mahasiswa;
        $semester = DB::table('tahun_pembayaran')
            ->select('semesters.nama', 'tahun_semester.id as untuk', 'tahun_pembayaran.nominal as harus', 'rekap_pembayaran.total_pembayaran', 'rekap_pembayaran.potongan', 'rekap_pembayaran.sisa', DB::raw('"semester" AS type'))
            ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('rekap_pembayaran', function ($q) use ($user) {
                $q->on('rekap_pembayaran.untuk', 'tahun_semester.id')
                    ->where('rekap_pembayaran.type', 'semester')
                    ->where('rekap_pembayaran.user_id', $user->id);
            })
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

            
            $pembayaranLain = DB::table('tahun_pembayaran_lain')
            ->select('pembayaran_lainnyas.nama', 'tahun_pembayaran_lain.id as untuk', 'tahun_pembayaran_lain.nominal as harus', 'rekap_pembayaran.total_pembayaran', 'rekap_pembayaran.potongan', 'rekap_pembayaran.sisa', DB::raw('"lainnya" AS type'))
            ->join('pembayaran_lainnyas', 'tahun_pembayaran_lain.pembayaran_lainnya_id', 'pembayaran_lainnyas.id')
            ->leftJoin('rekap_pembayaran', function ($q) use ($user) {
                $q->on('rekap_pembayaran.untuk', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
                ->where('rekap_pembayaran.type', 'lainnya')
                    ->where('rekap_pembayaran.user_id', $user->id);
            })
            ->where('tahun_pembayaran_lain.prodi_id', $mhs->prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();
            
            return $semester->merge($pembayaranLain);
    }
}
