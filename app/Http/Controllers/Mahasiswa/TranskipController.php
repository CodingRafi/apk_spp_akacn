<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranskipController extends Controller
{
    public function index()
    {
        return view('mahasiswa.transkip.index');
    }

    public function data()
    {
        $user = Auth::user();
        $data = DB::table('krs')
            ->select('matkuls.kode as kode_mk', 'matkuls.nama as matkul', 'mhs_nilai.jml_sks as sks', 'mutu.nama as nilai', 'mhs_nilai.nilai_mutu as bobot', 't_kuesioners.id as kuesioner', 'mhs_nilai.publish as status', 'tahun_matkul.id as tahun_matkul_id', 'semesters.nama as semester')
            ->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('tahun_semester', 'tahun_semester.id', 'krs.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->leftJoin('mhs_nilai', function ($q) use ($user) {
                $q->on('mhs_nilai.tahun_matkul_id', 'krs_matkul.tahun_matkul_id')
                    ->where('mhs_nilai.mhs_id', $user->id);
            })
            ->leftJoin('t_kuesioners', function ($q) use ($user) {
                $q->on('t_kuesioners.tahun_matkul_id', 'krs_matkul.tahun_matkul_id')
                    ->where('t_kuesioners.mhs_id', $user->id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->where('krs.mhs_id', $user->id)
            ->get()
            ->map(function ($data) {
                //? Menghitung bobot x sks
                $mutu = $data->bobot ?? 0;
                $kredit = ((int) $data->sks ?? 0);

                if ($mutu !== 0 && $kredit !== 0) {
                    $data->bobot_x_sks = $mutu * $kredit / $kredit;
                } else {
                    $data->bobot_x_sks = 0;
                }

                if (($data->status != null && $data->status == 0) || $data->kuesioner == null) {
                    $data->sks = null;
                    $data->nilai = null;
                    $data->bobot = null;
                    $data->bobot_x_sks = 0;
                }

                return $data;
            })
            ->groupBy('semester');

        return response()->json([
            'data' => $data
        ], 200);
    }
}
