<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    public function index()
    {
        return view('mahasiswa.krs.index');
    }

    public function dataSemester()
    {
        $mhs = Auth::user()->mahasiswa;
        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('krs.show', ['tahun_semester_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('tgl_pengisian', function ($datas) {
                return parseDate($datas->tgl_mulai_krs) . ' s.d ' . parseDate($datas->tgl_akhir_krs);
            })
            ->addColumn('sks_diambil', function ($datas) {
                return 0;
            })
            ->addColumn('status', function ($datas) {
                return 'Belum Mengisi';
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_semester_id)
    {
        //! belum Validate

        $mhs = Auth::user()->mahasiswa;

        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $data = DB::table('tahun_matkul')
                    ->join('tahun_matkul_rombel', 'tahun_matkul.id', 'tahun_matkul_rombel.tahun_matkul_id')
                    ->where('tahun_matkul.prodi_id', $mhs->prodi_id)
                    ->where('tahun_matkul.tahun_ajaran_id', $mhs->tahun_masuk_id)
                    ->where('tahun_matkul_rombel.rombel_id', $mhs->rombel_id)
                    ->get();

        return view('mahasiswa.krs.show', compact('tahun_semester', 'data'));
    }
}
