<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.semester.destroy', ['semester_id' => $data->id]) . "`, () => {tableSemester.ajax.reload()})' type='button'>
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

    public function get($tahun_ajaran_id){
        $data = Semester::where('tahun_ajaran_id', $tahun_ajaran_id)->get();
        return response()->json([
            'data' => $data
        ], 200);
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

    public function getLastSemester($tahun_ajaran_id)
    {
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

    public function getNeoFeeder($tahun_ajaran_id)
    {
        $result = Artisan::call("neo-feeder:get-semester {$tahun_ajaran_id}");
        $output = Artisan::output();

        if ($result) {
            return response()->json([
                'output' => $output
            ], 400);
        } else {
            return response()->json([
                'output' => $output
            ], 200);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Semester::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus'
            ], 400);
        }
    }

    public function storeNeoFeeder(Request $request){
        foreach ($request->data as $data) {
            DB::table('semesters')->updateOrInsert([
                'id' => $data['id_semester'],
            ], [
                'tahun_ajaran_id' => $data['id_tahun_ajaran'],
                'nama' => $data['nama_semester'],
                'semester' => $data['semester'],
                'status' => $data['a_periode_aktif'],
                'tgl_mulai' => Carbon::parse($data['tanggal_mulai'])->format('Y-m-d'),
                'tgl_selesai' => Carbon::parse($data['tanggal_selesai'])->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
