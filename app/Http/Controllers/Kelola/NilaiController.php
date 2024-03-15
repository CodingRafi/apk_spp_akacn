<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NilaiController extends Controller
{
    public function index()
    {
        return view('kelola.nilai.index');
    }

    public function dataTahunAjaran()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-nilai.show', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_ajaran_id)
    {
        $matkul = DB::table('tahun_matkul')
            ->select('tahun_matkul.id', 'matkuls.kode', 'matkuls.nama')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        $semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        return view('kelola.nilai.show', compact('matkul', 'semester'));
    }

    public function getRombel($tahun_ajaran_id)
    {
        if (request('tahun_matkul_id') && request('tahun_semester_id')) {
            $rombelMhs = DB::table('users')
                ->select('profile_mahasiswas.rombel_id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->join('krs', 'krs.mhs_id', 'users.id')
                ->join('krs_matkul', function ($join) {
                    $join->on('krs_matkul.krs_id', 'krs.id')
                        ->where('krs_matkul.tahun_matkul_id', request('tahun_matkul_id'));
                })
                ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
                ->where('krs.tahun_semester_id', request('tahun_semester_id'))
                ->distinct('profile_mahasiswas.rombel_id')
                ->get()
                ->pluck('rombel_id')
                ->toArray();

            $datas = DB::table('rombels')
                ->whereIn('rombels.id', $rombelMhs)
                ->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-nilai.detailRombel', ['tahun_ajaran_id' => $tahun_ajaran_id, 'rombel_id' => $data->id, 'tahun_semester_id' => request('tahun_semester_id'), 'tahun_matkul_id' => request('tahun_matkul_id')]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function detailRombel()
    {
        $mutu = DB::table('mutu')->get();
        return view('kelola.nilai.mhs', compact('mutu'));
    }

    public function getNilai($tahun_semester_id, $tahun_matkul_id, $mhs_id)
    {
        $data = DB::table('users')
            ->select('users.name', 'users.login_key', 'mhs_nilai.*')
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_semester_id, $tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id)
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->where('users.id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function dataMhs($tahun_ajaran_id, $rombel_id, $tahun_semester_id, $tahun_matkul_id)
    {
        $datas = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'mhs_nilai.presensi', 'mhs_nilai.uts', 'mhs_nilai.uas', 'mhs_nilai.nilai_mutu', 'mutu.nama as mutu', 'mhs_nilai.publish', 'mhs_nilai.jml_sks')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('krs', 'krs.mhs_id', 'users.id')
            ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_semester_id, $tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id)
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();

        foreach ($datas as $data) {
            $data->options = " <button class='btn btn-warning'
            onclick='editForm(`" . route('kelola-nilai.getNilai', ['tahun_semester_id' => $tahun_semester_id, 'tahun_matkul_id' => $tahun_matkul_id, 'mhs_id' => $data->id]) . "`, `Edit Nilai`, `#nilai`)'>
            <i class='ti-pencil'></i>
            Edit
        </button>";;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('publish', function ($datas) {
                return $datas->publish ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'publish'])
            ->make(true);
    }

    public function store(Request $request, $tahun_semester_id, $tahun_matkul_id, $mhs_id)
    {
        if ($request->mutu_id) {
            $mutu = DB::table('mutu')
                ->where('id', $request->mutu_id)
                ->first();
        }

        $matkul = DB::table('tahun_matkul')
                    ->select('matkuls.sks_mata_kuliah')
                    ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
                    ->where('tahun_matkul.id', $tahun_matkul_id)
                    ->first();

        DB::table('mhs_nilai')
            ->updateOrInsert([
                'mhs_id' => $mhs_id,
                'tahun_semester_id' => $tahun_semester_id,
                'tahun_matkul_id' => $tahun_matkul_id,
            ], [
                'presensi' => $request->presensi,
                'uts' => $request->uts,
                'uas' => $request->uas,
                'mutu_id' => $request->mutu_id,
                'nilai_mutu' => $request->mutu_id ? $mutu->nilai : null,
                'publish' => $request->publish ?? '0',
                'jml_sks' => $matkul->sks_mata_kuliah,
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
