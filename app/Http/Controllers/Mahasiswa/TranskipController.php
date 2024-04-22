<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranskipController extends Controller
{
    public function index()
    {
        return view('mahasiswa.transkip.index');
    }

    private function getTranskip($user)
    {
        $loop = [];
        $datas = DB::table('rekap_krs_matkul')
            ->select(
                'rekap_krs_matkul.*',
                'semesters.nama as semester',
                'matkuls.kode as kode_mk',
                'matkuls.nama as matkul'
            )
            ->join('tahun_semester', 'rekap_krs_matkul.tahun_semester_id', '=', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
            ->join('tahun_matkul', 'rekap_krs_matkul.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->where('mhs_id', $user->id)
            ->get()
            ->groupBy('semester');

        foreach ($datas as $indexSemester =>  $data) {
            foreach ($data as $indexMatkul => $matkul) {
                $cek = array_filter($loop, function ($item) use ($matkul) {
                    return $item['tahun_matkul_id'] == $matkul->tahun_matkul_id;
                });
                
                if (!empty($cek)) {
                    if ($matkul->nilai_mutu > $cek[0]['nilai_mutu']) {
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->jml_sks = $matkul->jml_sks;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->mutu = $matkul->mutu;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->nilai_mutu = $matkul->nilai_mutu;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->bobot_x_sks = $matkul->bobot_x_sks;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->kuesioner = $matkul->kuesioner;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->status = $matkul->status;
                    }
                    unset($datas[$indexSemester][$indexMatkul]);
                }else{
                    $loop[] = [
                        'tahun_matkul_id' => $matkul->tahun_matkul_id,
                        'nilai_mutu' => $matkul->nilai_mutu,
                        'index_semester' => $indexSemester,
                        'index_matkul' => $indexMatkul
                    ];
                }
            }
        }

        return $datas;
    }

    public function data()
    {
        $user = Auth::user();
        $data = $this->getTranskip($user);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function print()
    {
        $user = Auth::user();
        $rekap = $this->getTranskip($user);

        $data = DB::table('users')
            ->select(
                'users.name',
                'users.login_key as nim',
                'dosen.name as dosenPa',
                'prodi.nama as prodi',
                'profile_mahasiswas.tahun_masuk_id as angkatan'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('rombels', 'rombels.id', 'profile_mahasiswas.rombel_id')
            ->join('rombel_tahun_ajarans', function ($q) {
                $q->on('rombel_tahun_ajarans.rombel_id', 'rombels.id')
                    ->on('rombel_tahun_ajarans.tahun_masuk_id', 'profile_mahasiswas.tahun_masuk_id');
            })
            ->join('users as dosen', 'dosen.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->join('prodi', 'prodi.id', 'profile_mahasiswas.prodi_id')
            ->where('users.id', $user->id)
            ->first();

        return Pdf::loadView('mahasiswa.transkip.print', compact('data', 'rekap'))
            ->stream('transkip.pdf');
    }
}
