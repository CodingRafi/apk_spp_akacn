<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    public function index()
    {
        $prodis = DB::table('prodi')->get();
        $tahun_ajarans = DB::table('tahun_ajarans')->get();
        return view('kelola.krs.index', compact('prodis', 'tahun_ajarans'));
    }

    public function data()
    {
        $datas = DB::table('krs')
            ->select('krs.*', 'b.login_key', 'b.name', 'd.nama as prodi', 'e.name as verify', 'g.nama as semester')
            ->join('users as b', 'krs.mhs_id', '=', 'b.id')
            ->join('profile_mahasiswas as c', 'c.user_id', '=', 'b.id')
            ->join('prodi as d', 'c.prodi_id', '=', 'd.id')
            ->leftJoin('users as e', 'krs.verify_id', '=', 'e.id')
            ->join('tahun_semester as f', 'krs.tahun_semester_id', '=', 'f.id')
            ->join('semesters as g', 'f.semester_id', '=', 'g.id')
            ->when(request('status'), function ($q) {
                $q->where('krs.status', request('status'));
            })->when(request('prodi'), function ($q) {
                $q->where('c.prodi_id', request('prodi'));
            })->when(request('tahun_ajaran'), function ($q) {
                $q->where('c.tahun_ajaran_id', request('tahun_ajaran'));
            })->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('verifikasi-krs.show', $data->id) . "' class='btn btn-warning mx-2'>Verifikasi</a>";
            if (auth()->user()->can('edit_kelola_krs')) {
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('nim', function ($datas) {
                return $datas->login_key;
            })
            ->addColumn('nama_mhs', function ($datas) {
                return $datas->name;
            })
            ->addColumn('prodi', function ($datas) {
                return $datas->prodi;
            })
            ->editColumn('verify_id', function ($datas) {
                return $datas->verify;
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($id)
    {
        $data = DB::table('krs')
            ->select('krs.*', 'b.login_key', 'b.name', 'd.nama as prodi', 'e.name as verify', 'g.nama as semester', 'h.nama as tahun_masuk')
            ->join('users as b', 'krs.mhs_id', '=', 'b.id')
            ->join('profile_mahasiswas as c', 'c.user_id', '=', 'b.id')
            ->join('prodi as d', 'c.prodi_id', '=', 'd.id')
            ->leftJoin('users as e', 'krs.verify_id', '=', 'e.id')
            ->join('tahun_semester as f', 'krs.tahun_semester_id', '=', 'f.id')
            ->join('semesters as g', 'f.semester_id', '=', 'g.id')
            ->join('tahun_ajarans as h', 'c.tahun_masuk_id', '=', 'h.id')
            ->where('krs.id', $id)
            ->first();

        return view('kelola.krs.show', compact('data'));
    }

    public function dataMatkul($id)
    {
        $datas = DB::table('krs_matkul as a')
            ->select('a.id', 'c.nama as matkul', 'c.sks_mata_kuliah', 'c.kode', 'd.name as dosen', 'a.tahun_matkul_id')
            ->join('tahun_matkul as b', 'a.tahun_matkul_id', '=', 'b.id')
            ->join('matkuls as c', 'b.matkul_id', '=', 'c.id')
            ->join('users as d', 'b.dosen_id', '=', 'd.id')
            ->where('a.krs_id', $id)
            ->get();

        foreach ($datas as $data) {
            $options = "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('verifikasi-krs.destroy', ['id' => $id, 'krs_matkul_id' => $data->id]) . "`)'>
                                                                Hapus
                                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('ruang', function ($datas) {
                $ruang = DB::table('ruangs')
                    ->select('ruangs.kapasitas', 'ruangs.nama')
                    ->join('tahun_matkul_ruang', 'ruangs.id', 'tahun_matkul_ruang.ruang_id')
                    ->where('tahun_matkul_ruang.tahun_matkul_id', $datas->tahun_matkul_id)
                    ->get();

                $ruangParse = '';
                foreach ($ruang as $item) {
                    $ruangParse .= $item->nama . ' (Kapasitas: ' . $item->kapasitas . ')<br>';
                }

                return $ruangParse;
            })
            ->rawColumns(['options', 'ruang'])
            ->make(true);
    }
}
