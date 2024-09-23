<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BimbinganController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_bimbingan', ['only' => ['index', 'data', 'show']]);
        $this->middleware('permission:edit_bimbingan', ['only' => ['edit']]);
    }

    public function index()
    {
        return view('mahasiswa.bimbingan.index');
    }

    private function validateMhsId($mhs_id = null)
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            $mhs_id = $user->id;
        }

        $user = User::findOrFail($mhs_id);
        if ((!$user->hasRole('mahasiswa') && $mhs_id == null) || $user->mahasiswa == null) {
            abort(404);
        }

        return $mhs_id;
    }

    public function data($mhs_id = null)
    {
        $mhsId = $this->validateMhsId($mhs_id);
        $mhs = User::findOrFail($mhsId)->mahasiswa;

        $datas = DB::table('tahun_semester')
            ->select(
                'tahun_semester.id',
                'semesters.nama',
                'tahun_semester.jatah_sks',
                'tahun_semester.tgl_mulai_krs',
                'tahun_semester.tgl_akhir_krs',
                'krs.jml_sks_diambil',
                'krs.status'
            )
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('krs', function ($join) use ($mhs_id) {
                $join->on('krs.tahun_semester_id', 'tahun_semester.id')
                    ->where('krs.mhs_id', $mhs_id);
            })
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->orderBy('semesters.id', 'asc')
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('bimbingan.show', ['tahun_semester_id' => $data->id, 'mhs_id' => $mhsId]) . "`, `Bimbingan Akademik`, `#bimbingan`)'>
                       Catatan
                    </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahunSemesterId, $mhsId)
    {
        $mhsId = $this->validateMhsId($mhsId);
        $data = DB::table('bimbingan_akademik')
            ->select('bimbingan_akademik.*')
            ->where('bimbingan_akademik.tahun_semester_id', $tahunSemesterId)
            ->where('bimbingan_akademik.mhs_id', $mhsId)
            ->first();

        return response()->json([
            'data' => $data ?? []
        ], 200);
    }

    public function storeOrUpdate(Request $request, $tahunSemesterId, $mhsId)
    {
        $mhsId = $this->validateMhsId($mhsId);
        DB::table('bimbingan_akademik')
            ->updateOrInsert([
                'mhs_id' => $mhsId,
                'tahun_semester_id' => $tahunSemesterId
            ], [
                'catatan' => $request->catatan
            ]);

        return response()->json([
            'message' => 'Data bimbingan akademik berhasil disimpan'
        ], 200);
    }
}
