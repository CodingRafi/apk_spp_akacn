<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SemesterController extends Controller
{
    public function data($tahun_ajaran_id)
    {
        if ($tahun_ajaran_id != ':id') {
            $datas = Semester::where('tahun_ajaran_id', $tahun_ajaran_id)->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.semester.show', $data->id) . "`, `Edit Semester`, `#semester`)' type='button'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('status', function ($datas) {
                return $datas->status ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->editColumn('tgl_mulai', function ($datas) {
                return parseDate($datas->tgl_mulai);
            })
            ->editColumn('tgl_selesai', function ($datas) {
                return parseDate($datas->tgl_selesai);
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function store(SemesterRequest $request)
    {
        $idSemester = ($request->tahun_ajaran_id . $request->semester);
        $cek = DB::table('semesters')->where('id', $idSemester)->count();

        if ($cek > 0) {
            return response()->json([
                'message' => 'Semester ini sudah ada'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $requestParse = $request->all();
            $requestParse['id'] = $idSemester;
            $data = Semester::create($requestParse);
            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function getLastSemester($tahun_ajaran_id){
        $data = DB::table('semesters')->where('tahun_ajaran_id', $tahun_ajaran_id)->orderBy('id', 'desc')->first();
        return response()->json([
            'semester' => $data ? $data->semester : 0
        ], 200);
    }

    public function show($id)
    {
        $data = Semester::find($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(SemesterRequest $request, $id)
    {
        $semester = Semester::find($id);
        $cek = DB::table('tahun_semester')->where('semester_id', $id)->count();
        
        if ($semester->status != $request->status && $cek > 0) {
            return response()->json([
                'message' => 'Semester ini sudah digunakan, tidak bisa ubah status'
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $data = $semester->update([
                'nama' => $request->nama,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_selesai' => $request->tgl_selesai,
                'status' => $request->status ?? "0"
            ]);
            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Matkul $matkul)
    {
        //
    }
}
