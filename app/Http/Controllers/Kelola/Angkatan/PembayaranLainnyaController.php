<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PembayaranLainnyaController extends Controller
{
    public function getJenis()
    {
        $data = DB::table('pembayaran_lainnyas')
            ->select('pembayaran_lainnyas.*')
            ->leftJoin('tahun_pembayaran_lain', 'tahun_pembayaran_lain.pembayaran_lainnya_id', 'pembayaran_lainnyas.id')
            ->whereNull('tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->when(request('pembayaran_lainnya_id') && request('pembayaran_lainnya_id') != '', function ($q) {
                $q->orWhere('tahun_pembayaran_lain.pembayaran_lainnya_id', request('pembayaran_lainnya_id'));
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('tahun_pembayaran_lain')
            ->select('tahun_pembayaran_lain.*', 'pembayaran_lainnyas.nama as jenis')
            ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where('tahun_pembayaran_lain.prodi_id', $prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();
            
        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.prodi.pembayaran-lainnya.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, `Edit Pembayaran`, `#PembayaranLainnya`, getJenisPembayaranLainnya)'>
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
            ->editColumn('publish', function ($data) {
                return ($data->publish ? 'Ya' : 'Tidak');
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
            'pembayaran_lainnya_id' => 'required',
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('tahun_pembayaran_lain')->insert([
                'pembayaran_lainnya_id' => $request->pembayaran_lainnya_id,
                'prodi_id' => $prodi_id,
                'tahun_ajaran_id' => $tahun_ajaran_id,
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
        $data = DB::table('tahun_pembayaran_lain')
            ->where('prodi_id', $prodi_id)
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
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

        DB::beginTransaction();
        try {
            DB::table('tahun_pembayaran_lain')
                ->where('id', $id)
                ->where('prodi_id', $prodi_id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
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
}
