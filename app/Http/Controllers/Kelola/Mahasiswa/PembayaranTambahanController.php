<?php

namespace App\Http\Controllers\Kelola\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PembayaranTambahan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PembayaranTambahanController extends Controller
{
    public function data($role, $user_id)
    {
        $datas = PembayaranTambahan::select('pembayaran_tambahans.*', 'semesters.nama as semester', 'pembayaran_lainnyas.nama as lainnya')
            ->leftJoin('tahun_semester', 'pembayaran_tambahans.tahun_semester_id', '=', 'tahun_semester.id')
            ->leftJoin('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
            ->leftJoin('tahun_pembayaran_lain', 'pembayaran_tambahans.tahun_pembayaran_lain_id', '=', 'tahun_pembayaran_lain.id')
            ->leftJoin('pembayaran_lainnyas', 'tahun_pembayaran_lain.pembayaran_lainnya_id', '=', 'pembayaran_lainnyas.id')
            ->where('pembayaran_tambahans.mhs_id', $user_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('kelola-users.pembayaran-tambahan.show', ['role' => 'mahasiswa', 'user_id' => $user_id, 'id' => $data->id]) . "`, `Edit Pembayaran Tambahan`, `#pembayaranTambahan`, editPembayaranTambahan)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-users.pembayaran-tambahan.destroy', ['role' => 'mahasiswa', 'user_id' => $user_id, 'id' => $data->id]) . "`, () => {tablePembayaranTambahan.ajax.reload();tablePembayaran.ajax.reload()})' type='button'>
                                                Hapus
                                            </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('namaParse', function ($data) {
                return $data->semester ?? $data->lainnya;
            })
            ->editColumn('nominal', function ($data) {
                return formatRupiah($data->nominal);
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request, $role, $user_id)
    {
        $request->validate([
            'type' => 'required',
            'nama' => 'required',
            'nominal' => 'required',
            'tahun_semester_id' => 'required_if:type,semester',
            'tahun_pembayaran_lain_id' => 'required_if:type,lainnya'
        ]);

        PembayaranTambahan::create([
            'mhs_id' => $user_id,
            'nama' => $request->nama,
            'nominal' => $request->nominal,
            'tahun_semester_id' => $request->tahun_semester_id,
            'tahun_pembayaran_lain_id' => $request->tahun_pembayaran_lain_id,
            'type' => $request->type
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function show($role, $user_id, $id)
    {
        $data = PembayaranTambahan::where('mhs_id', $user_id)
            ->where('id', $id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $role, $user_id, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nominal' => 'required',
            'tahun_semester_id' => 'required'
        ]);

        PembayaranTambahan::where('mhs_id', $user_id)
            ->where('id', $id)
            ->update([
                'nama' => $request->nama,
                'nominal' => $request->nominal,
                'tahun_semester_id' => $request->tahun_semester_id,
            ]);

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    public function destroy($role, $user_id, $id)
    {
        PembayaranTambahan::where('mhs_id', $user_id)
            ->where('id', $id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
