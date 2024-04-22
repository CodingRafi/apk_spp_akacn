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
        return view('mahasiswa.mbkm.index');
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
            $data->options = "<a class='btn btn-primary'
                    href='" . route('mbkm.show', $data->id) . "'>
                    Detail
                </a>";
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

        if (!$data) {
            abort(404);
        }

        $semester = DB::table('semesters')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->where('tahun_semester.prodi_id', Auth::user()->mahasiswa->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', Auth::user()->mahasiswa->tahun_masuk_id)
            ->get();

        $jenisAktivitas = DB::table('jenis_aktivitas')
            ->get();

        return view('mahasiswa.mbkm.show', compact('data', 'semester', 'jenisAktivitas'));
    }

    public function getPembimbing($mbkm_id){
        $datas = DB::table('mbkm_dosen_pembimbing')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mbkm_dosen_pembimbing.pembimbing_ke',
                'kategori_kegiatans.nama as kategori_kegiatan',
                'mbkm_dosen_pembimbing.id_bimbing_mahasiswa_neo_feeder'
            )
            ->join('users', 'users.id', 'mbkm_dosen_pembimbing.dosen_id')
            ->join('kategori_kegiatans', 'mbkm_dosen_pembimbing.kategori_kegiatan_id', '=', 'kategori_kegiatans.id')
            ->where('mbkm_dosen_pembimbing.mbkm_id', $mbkm_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('dosen', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function getPenguji($mbkm_id){
        $datas = DB::table('mbkm_dosen_penguji')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mbkm_dosen_penguji.penguji_ke',
            )
            ->join('users', 'users.id', 'mbkm_dosen_penguji.dosen_id')
            ->join('kategori_kegiatans', 'mbkm_dosen_penguji.kategori_kegiatan_id', '=', 'kategori_kegiatans.id')
            ->where('mbkm_dosen_penguji.mbkm_id', $mbkm_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('dosen', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->make(true);
    }
}
