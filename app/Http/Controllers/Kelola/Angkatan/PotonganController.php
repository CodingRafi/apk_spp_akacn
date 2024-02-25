<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PotonganController extends Controller
{
    public function getSemester($prodi_id, $tahun_ajaran_id)
    {
        $data = DB::table('tahun_semester')
            ->select('tahun_semester.*', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getPembayaranLainnya($prodi_id, $tahun_ajaran_id)
    {
        $data = DB::table('tahun_pembayaran_lain')
            ->select('tahun_pembayaran_lain.*', 'pembayaran_lainnyas.nama')
            ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where('tahun_pembayaran_lain.prodi_id', $prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('potongan_tahun_ajaran')
            ->select('potongan_tahun_ajaran.id', 'potongans.nama as potongan', 'semesters.nama as semester', 'pembayaran_lainnyas.nama as lainnya', 'potongan_tahun_ajaran.publish', 'potongan_tahun_ajaran.nominal')
            ->join('potongans', 'potongans.id', 'potongan_tahun_ajaran.potongan_id')
            ->leftJoin('tahun_semester', 'tahun_semester.id', 'potongan_tahun_ajaran.tahun_semester_id')
            ->leftJoin('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('tahun_pembayaran_lain', 'tahun_pembayaran_lain.id', 'potongan_tahun_ajaran.tahun_pembayaran_lain_id')
            ->leftJoin('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where(function ($q) use ($prodi_id, $tahun_ajaran_id) {
                $q->where('tahun_semester.prodi_id', $prodi_id)
                    ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id);
            })
            ->orWhere(function ($q) use ($prodi_id, $tahun_ajaran_id) {
                $q->where('tahun_pembayaran_lain.prodi_id', $prodi_id)
                    ->where('tahun_pembayaran_lain.tahun_ajaran_id', $tahun_ajaran_id);
            })
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.prodi.potongan.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, `Edit Potongan`, `#Potongan`, get_potongan)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.potongan.destroy', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, () => {tablePotongan.ajax.reload()})' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('namaParse', function ($data) {
                return $data->semester ?? $data->lainnya;
            })
            ->editColumn('publish', function ($data) {
                return $data->publish ? 'Ya' : 'Tidak';
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request, $prodi_id, $tahun_ajaran_id)
    {
        $request->validate([
            'potongan_id' => 'required',
            'type' => 'required',
            'tahun_semester_id' => 'required_if:type,semester',
            'tahun_pembayaran_lain_id' => 'required_if:type,lainnya',
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('potongan_tahun_ajaran')->insert([
                'potongan_id' => $request->potongan_id,
                'type' => $request->type,
                'tahun_semester_id' => $request->tahun_semester_id,
                'tahun_pembayaran_lain_id' => $request->tahun_pembayaran_lain_id,
                'nominal' => $request->nominal,
                'ket' => $request->ket,
                'publish' => $request->publish ?? '0',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $id)
    {
        $data = DB::table('potongan_tahun_ajaran')
            ->where('id', $id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        $request->validate([
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        $data = DB::table('potongan_tahun_ajaran')->where('id', $id)->first();
        $cek = DB::table('potongan_mhs')
            ->where('potongan_tahun_ajaran_id', $id)
            ->count();

        if ($request->publish !== $data->publish && $cek > 0) {
            return response()->json([
                'message' => 'Sudah diset ke mahasiswa tidak bisa dinonaktifkan publishnya'
            ], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('potongan_tahun_ajaran')
                ->where('id', $id)
                ->update([
                    'nominal' => $request->nominal,
                    'ket' => $request->ket,
                    'publish' => $request->publish ?? '0',
                    'updated_at' => now()
                ]);

            DB::commit();
            return response()->json([
                'message' => 'Berhasil diubah'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy($prodi_id, $tahun_ajaran_id, $id)
    {
        $cek = DB::table('potongan_mhs')
            ->where('potongan_tahun_ajaran_id', $id)
            ->count();

        if ($cek > 0) {
            return response()->json([
                'message' => 'Sudah diset ke mahasiswa tidak bisa dihapus'
            ], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('potongan_tahun_ajaran')->where('id', $id)->delete();
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
