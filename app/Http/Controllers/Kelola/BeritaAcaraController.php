<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasRole('dosen')) {
            abort(403);
        }

        $tahunAjarans = DB::table('tahun_ajarans')->get();
        $prodis = DB::table('prodi')->get();
        return view('kelola.berita_acara.index', compact('prodis', 'tahunAjarans'));
    }

    public function data()
    {
        if (request('prodi_id') && request('tahun_ajaran_id')) {
            $datas = DB::table('krs')
                ->select('tahun_matkul.id', 'matkuls.kode', 'matkuls.nama', 'tahun_semester.semester_id')
                ->join('krs_matkul', 'krs_matkul.krs_id', '=', 'krs.id')
                ->join('tahun_matkul', 'tahun_matkul.id', '=', 'krs_matkul.tahun_matkul_id')
                ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
                ->join('tahun_semester', 'tahun_semester.id', '=', 'krs.tahun_semester_id')
                ->join('tahun_matkul_dosen', function($q){
                    $q->on('tahun_matkul_dosen.tahun_matkul_id', '=', 'tahun_matkul.id')
                    ->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
                })
                ->where('tahun_matkul.prodi_id', request('prodi_id'))
                ->where('tahun_matkul.tahun_ajaran_id', request('tahun_ajaran_id'))
                ->distinct('tahun_matkul.id')
                ->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $data->options = '<a href="' . route(
                'kelola-presensi.berita-acara.print',
                [
                    'tahun_matkul_id' => $data->id,
                    'semester_id' => $data->semester_id,
                ]
            ) . '" class="btn btn-primary">Download</a>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('matkul', function ($data) {
                return $data->kode . ' - ' . $data->nama;
            })
            ->addColumn('rombel', function ($data) {
                return DB::table('tahun_matkul_rombel')
                    ->join('rombels', 'rombels.id', '=', 'tahun_matkul_rombel.rombel_id')
                    ->where('tahun_matkul_rombel.tahun_matkul_id', $data->id)
                    ->pluck('rombels.nama')
                    ->implode(',');
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function print($tahun_matkul_id, $semester_id)
    {
        $jadwal = Jadwal::where('jadwal.tahun_matkul_id', $tahun_matkul_id)
            ->with('mahasiswa')
            ->orderBy('jadwal.id', 'asc')
            ->get();

        $matkul = DB::table('tahun_matkul')
            ->select(
                'matkuls.kode',
                'matkuls.nama',
                'matkuls.sks_mata_kuliah',
                'tahun_matkul.jam_mulai',
                'tahun_matkul.jam_akhir'
            )
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        $semester = DB::table('semesters')
                ->select('semesters.nama as semester')
                ->where('id', $semester_id)
                ->first();

        $nilai = DB::table('mhs_nilai')
                    ->select('users.name', 'users.login_key', 'mhs_nilai.*', 'mutu.nama as mutu')
                    ->join('users', 'users.id', 'mhs_nilai.mhs_id')
                    ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
                    ->where('tahun_matkul_id', $tahun_matkul_id)
                    ->get();

        $pdf = Pdf::loadView('kelola.berita_acara.print', compact('jadwal', 'matkul', 'semester', 'nilai'));
        return $pdf->stream('Berita Acara.pdf');
    }
}
