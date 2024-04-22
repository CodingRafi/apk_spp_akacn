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
            ->when(Auth::user()->hasRole('mahasiswa'), function ($q) {
                $q->join('mbkm_mhs', function ($q) {
                    $q->on('mbkm_mhs.mbkm_id', 'mbkm.id')
                        ->where('mbkm_mhs.mhs_id', auth()->user()->id);
                });
            })
            ->when(Auth::user()->hasRole('dosen'), function ($q) {
                $q->leftJoin('mbkm_dosen_pembimbing', 'mbkm.id', 'mbkm_dosen_pembimbing.mbkm_id')
                    ->leftJoin('mbkm_dosen_penguji', 'mbkm.id', 'mbkm_dosen_penguji.mbkm_id')
                    ->where('mbkm_dosen_pembimbing.dosen_id', auth()->user()->id)
                    ->orWhere('mbkm_dosen_penguji.dosen_id', auth()->user()->id);
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
            ->when(Auth::user()->hasRole('mahasiswa'), function ($q) {
                $q->join('mbkm_mhs', function ($q) {
                    $q->on('mbkm_mhs.mbkm_id', 'mbkm.id')
                        ->where('mbkm_mhs.mhs_id', auth()->user()->id);
                });
            })
            ->when(Auth::user()->hasRole('dosen'), function ($q) {
                $q->leftJoin('mbkm_dosen_pembimbing', 'mbkm.id', 'mbkm_dosen_pembimbing.mbkm_id')
                    ->leftJoin('mbkm_dosen_penguji', 'mbkm.id', 'mbkm_dosen_penguji.mbkm_id')
                    ->where('mbkm_dosen_pembimbing.dosen_id', auth()->user()->id)
                    ->orWhere('mbkm_dosen_penguji.dosen_id', auth()->user()->id);
            })
            ->join('tahun_semester', 'mbkm.tahun_semester_id', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', 'semesters.id')
            ->where('mbkm.id', $id)
            ->select('mbkm.*', 'semesters.nama as semester')
            ->first();

        if (!$data) {
            abort(404);
        }

        $jenisAktivitas = DB::table('jenis_aktivitas')
            ->get();

        return view('mahasiswa.mbkm.show', compact('data', 'jenisAktivitas'));
    }

    public function getPembimbing($mbkm_id)
    {
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

    public function getPenguji($mbkm_id)
    {
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

    public function getMhs($mbkm_id){
        $datas = DB::table('mbkm_mhs')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mbkm_mhs.peran',
                'mbkm_mhs.id_anggota_neo_feeder'
            )
            ->join('users', 'users.id', 'mbkm_mhs.mhs_id')
            ->where('mbkm_mhs.mbkm_id', $mbkm_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('peran', function ($datas) {
                return config('services.peran')[$datas->peran];
            })
            ->addColumn('mhs', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->rawColumns(['options'])
            ->make(true);
    }
}
