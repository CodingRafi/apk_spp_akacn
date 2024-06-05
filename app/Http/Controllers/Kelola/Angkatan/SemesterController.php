<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SemesterController extends Controller
{
    public function index($prodi_id, $tahun_ajaran_id)
    {
        $data = DB::table('semesters')
            ->select('semesters.*')
            ->leftJoin('tahun_semester', function ($join) use ($prodi_id) {
                $join->on('tahun_semester.semester_id', 'semesters.id')
                    ->where('tahun_semester.prodi_id', $prodi_id);
            })
            ->join('tahun_ajarans', 'tahun_ajarans.id', 'semesters.tahun_ajaran_id')
            ->whereNull('tahun_semester.semester_id')
            ->where('tahun_ajarans.id', $tahun_ajaran_id)
            ->where('semesters.status', "1")
            ->when(request('semester_id') && request('semester_id') != '', function ($q) {
                $q->orWhere('tahun_semester.semester_id', request('semester_id'));
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('semesters')
            ->select(
                'semesters.nama',
                'tahun_semester.jatah_sks as jatah_sks_semester',
                'tahun_semester.id as tahun_semester_id',
                'tahun_semester.tgl_mulai_krs',
                'tahun_semester.tgl_akhir_krs',
                'tahun_semester.tgl_mulai',
                'tahun_semester.tgl_akhir',
                'tahun_semester.status'
            )
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';
            if (auth()->user()->can('edit_prodi')) {
                $options = $options . " <button class='btn btn-warning'
                onclick='editForm(`" . route('data-master.prodi.semester.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'tahun_semester_id' => $data->tahun_semester_id]) . "`, `Edit Semester`, `#AddSemester`, getSemester)'>
                <i class='ti-pencil'></i>
                Edit
            </button>";
            }

            if (auth()->user()->can('delete_prodi')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.semester.destroy', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'tahun_semester_id' => $data->tahun_semester_id]) . "`)'>
                                                    Hapus
                                                </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('status', function ($datas) {
                return $datas->status ? "<i class='bx bx-check text-success'></i>" :
                    "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function store(Request $request, $prodi_id, $tahun_ajaran_id)
    {
        $request->validate([
            'semester_id' => 'required',
            'jatah_sks' => 'required',
            'tgl_mulai_krs' => 'required',
            'tgl_akhir_krs' => 'required|after:tgl_mulai_krs',
            'tgl_mulai' => 'required',
            'tgl_akhir' => 'required|after:tgl_mulai',
        ]);

        if ($request->status) {
            $check = DB::table('tahun_semester')
                ->where('prodi_id', $prodi_id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                ->where('status', "1")
                ->count();

            if ($check > 0) {
                return response()->json([
                    'message' => 'Sudah ada semester aktif'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            DB::table('tahun_semester')->insert([
                'prodi_id' => $prodi_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
                'semester_id' => $request->semester_id,
                'jatah_sks' => $request->jatah_sks,
                'tgl_mulai_krs' => $request->tgl_mulai_krs,
                'tgl_akhir_krs' => $request->tgl_akhir_krs,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'status' => ($request->status ?? "0"),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $tahun_semester_id)
    {
        $data = DB::table('tahun_semester')->where('id', $tahun_semester_id)->first();
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        $request->validate([
            'jatah_sks' => 'required',
            'tgl_mulai_krs' => 'required',
            'tgl_akhir_krs' => 'required|after:tgl_mulai_krs',
            'tgl_mulai' => 'required',
            'tgl_akhir' => 'required|after:tgl_mulai',
        ]);

        $data = DB::table('tahun_semester')->where('id', $id)->first();

        if ($data->status != $request->status && $request->status == "1") {
            $check = DB::table('tahun_semester')
                ->where('prodi_id', $prodi_id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                ->where('status', "1")
                ->count();

            if ($check > 0) {
                return response()->json([
                    'message' => 'Sudah ada semester aktif'
                ], 400);
            }
        }

        DB::table('tahun_semester')->where('id', $id)->update([
            'jatah_sks' => $request->jatah_sks,
            'tgl_mulai_krs' => $request->tgl_mulai_krs,
            'tgl_akhir_krs' => $request->tgl_akhir_krs,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_akhir' => $request->tgl_akhir,
            'status' => request('status') ?? '0',
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    public function destroy($prodi_id, $tahun_ajaran_id, $id){
        DB::beginTransaction();
        try {
            DB::table('tahun_semester')
                ->where('id', $id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
