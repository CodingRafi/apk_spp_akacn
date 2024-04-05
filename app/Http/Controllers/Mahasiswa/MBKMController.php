<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\MBKM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MBKMController extends Controller
{
    public function index()
    {
        $semester = DB::table('semesters')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->where('tahun_semester.prodi_id', Auth::user()->mahasiswa->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', Auth::user()->mahasiswa->tahun_masuk_id)
            ->get();

        $jenisAktivitas = DB::table('jenis_aktivitas')
            ->get();

        return view('mahasiswa.mbkm.index', compact('semester', 'jenisAktivitas'));
    }

    public function data()
    {
        $datas = DB::table('mbkm')
            ->join('mbkm_mhs', function ($q) {
                $q->on('mbkm_mhs.mbkm_id', 'mbkm.id')
                    ->where('mbkm_mhs.mhs_id', auth()->user()->id);
            })
            ->select('mbkm.*')
            ->get();

        foreach ($datas as $data) {
            $data->options = "<button class='btn btn-primary'
                    onclick='editForm(`" . route('mbkm.show', $data->id) . "`, `Detail MBKM`, `#Mbkm`)'>
                    Detail
                </button>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($id)
    {
        $data = DB::table('mbkm')
            ->join('mbkm_mhs', function ($q) {
                $q->on('mbkm_mhs.mbkm_id', 'mbkm.id')
                    ->where('mbkm_mhs.mhs_id', auth()->user()->id);
            })
            ->where('mbkm.id', $id)
            ->select('mbkm.*')
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }
}
