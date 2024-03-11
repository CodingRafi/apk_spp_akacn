<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            if (auth()->user()->can('edit_kelola_krs')) {
                $options = $options . "<a href='" . route('verifikasi-krs.show', $data->id) . "' class='btn btn-warning mx-2'>Verifikasi</a>";
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
            ->select('krs.*', 'b.login_key', 'b.name', 'd.nama as prodi', 'e.name as verify', 'g.nama as semester', 'h.nama as tahun_masuk', 'f.id as tahun_semester_id', 'f.jatah_sks', 'krs.status', 'b.id as mhs_id')
            ->join('users as b', 'krs.mhs_id', '=', 'b.id')
            ->join('profile_mahasiswas as c', 'c.user_id', '=', 'b.id')
            ->join('prodi as d', 'c.prodi_id', '=', 'd.id')
            ->leftJoin('users as e', 'krs.verify_id', '=', 'e.id')
            ->join('tahun_semester as f', 'krs.tahun_semester_id', '=', 'f.id')
            ->join('semesters as g', 'f.semester_id', '=', 'g.id')
            ->join('tahun_ajarans as h', 'c.tahun_masuk_id', '=', 'h.id')
            ->where('krs.id', $id)
            ->first();

        if ($data->status == 'pending') {
            abort(403);
        }

        return view('kelola.krs.show', compact('data'));
    }

    public function store(Request $request, $krs_id)
    {   
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'tgl_mulai' => 'required_if:status,ditolak',
            'tgl_akhir' => 'required_if:status,ditolak'
        ]);

        DB::table('krs')
            ->where('id', $krs_id)
            ->update([
                'status' => $request->status,
                'verify_id' => Auth::user()->id,
                'tgl_mulai_revisi' => $request->tgl_mulai,
                'tgl_akhir_revisi' => $request->tgl_akhir,
            ]);
        return redirect()->route('verifikasi-krs.index')->with('success', 'Berhasil disimpan!');
    }

    public function revisi($krs_id)
    {
        $data = DB::table('krs')->where('id', $krs_id)->first();
        if ($data->status == 'pengajuan' || $data->status == 'pending') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan!');
        }

        if ($data->verify_id != Auth::user()->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat merevisi ini');
        }

        DB::table('krs')
            ->where('id', $krs_id)
            ->update([
                'status' => 'pengajuan',
                'verify_id' => null
            ]);

        return redirect()->back()->with('success', 'Berhasil direvisi!');
    }
}
