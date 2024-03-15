<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KhsController extends Controller
{
    public function index()
    {
        return view('mahasiswa.khs.index');
    }

    public function dataSemester()
    {
        $user = Auth::user();
        $mhs = $user->mahasiswa;

        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        foreach ($datas as $data) {
            $data->options = '<a href="' . route('khs.show', $data->id) . '" class="btn btn-primary">Detail</a>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_semester_id)
    {
        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $kuesioner = DB::table('kuesioners')
            ->where('status', '1')
            ->get();

        return view('mahasiswa.khs.show', compact('tahun_semester', 'kuesioner'));
    }

    public function data($tahun_semester_id)
    {
        $user = Auth::user();
        $khs = DB::table('krs')
            ->select('matkuls.kode as kode_mk', 'matkuls.nama as matkul', 'mhs_nilai.jml_sks as sks', 'mutu.nama as nilai', 'mhs_nilai.nilai_mutu as bobot', 't_kuesioners.id as kuesioner', 'mhs_nilai.publish as status', 'tahun_matkul.id as tahun_matkul_id')
            ->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->leftJoin('mhs_nilai', function ($q) use ($user, $tahun_semester_id) {
                $q->on('mhs_nilai.tahun_matkul_id', 'krs_matkul.tahun_matkul_id')
                    ->where('mhs_nilai.mhs_id', $user->id)
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id);
            })
            ->leftJoin('t_kuesioners', function ($q) use ($user, $tahun_semester_id) {
                $q->on('t_kuesioners.tahun_matkul_id', 'krs_matkul.tahun_matkul_id')
                    ->where('t_kuesioners.tahun_semester_id', $tahun_semester_id)
                    ->where('t_kuesioners.mhs_id', $user->id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->where('krs.mhs_id', $user->id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
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
            });

        return response()->json([
            'data' => $khs
        ], 200);
    }
}
