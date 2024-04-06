<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ResponseKuesionerController extends Controller
{
    public function index()
    {
        $prodi = DB::table('prodi')->get();
        $tahunAjaran = DB::table('tahun_ajarans')->get();
        return view('data_master.response_kuesioner.index', compact('prodi', 'tahunAjaran'));
    }

    public function data()
    {
        $datas = [];
        if (request('tahun_ajaran_id') && request('prodi_id')) {
            $datas = DB::table('t_kuesioners')
                ->select(
                    'users.name',
                    'users.login_key',
                    't_kuesioners.id',
                    'semesters.nama as semester',
                    'matkuls.nama as matkul',
                    'matkuls.kode'
                )
                ->join('users', 't_kuesioners.mhs_id', '=', 'users.id')
                ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
                ->join('tahun_semester', 't_kuesioners.tahun_semester_id', '=', 'tahun_semester.id')
                ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
                ->join('tahun_matkul', 't_kuesioners.tahun_matkul_id', '=', 'tahun_matkul.id')
                ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
                ->where('profile_mahasiswas.prodi_id', request('prodi_id'))
                ->where('profile_mahasiswas.tahun_masuk_id', request('tahun_ajaran_id'))
                ->when(request('tahun_semester_id'), function ($q) {
                    $q->where('t_kuesioners.tahun_semester_id', request('tahun_semester_id'));
                })
                ->when(request('tahun_matkul_id'), function ($q) {
                    $q->where('t_kuesioners.tahun_matkul_id', request('tahun_matkul_id'));
                })
                ->get();
        }

        foreach ($datas as $data) {
            $data->options = '<button class="btn btn-primary" onclick="showResponse(' . $data->id . ')" type="buttom">Detail</button>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function getSemester()
    {
        $tahunSemester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.tahun_ajaran_id', request('tahun_ajaran_id'))
            ->where('tahun_semester.prodi_id', request('prodi_id'))
            ->get();

        return response()->json([
            'data' => $tahunSemester
        ], 200);
    }

    public function getMatkul()
    {
        $matkul = DB::table('tahun_matkul')
            ->select('tahun_matkul.id', 'matkuls.kode', 'matkuls.nama')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.tahun_ajaran_id', request('tahun_ajaran_id'))
            ->where('tahun_matkul.prodi_id', request('prodi_id'))
            ->get();

        return response()->json([
            'data' => $matkul
        ], 200);
    }

    public function show($id)
    {
        $datas = DB::table('t_kuesioners')
            ->select(
                'kuesioners.type',
                'kuesioners.pertanyaan',
                't_kuesioners_answer.answer',
                't_kuesioners_answer.id'
            )
            ->join('t_kuesioners_answer', 't_kuesioners.id', 't_kuesioners_answer.t_kuesioner_id')
            ->join('kuesioners', 'kuesioners.id', 't_kuesioners_answer.kuesioner_id')
            ->where('t_kuesioners.id', $id)
            ->get();

        return response()->json([
            'data' => $datas
        ], 200);
    }
}
