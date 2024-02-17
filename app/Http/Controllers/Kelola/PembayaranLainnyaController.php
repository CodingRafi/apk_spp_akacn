<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PembayaranLainnyaController extends Controller
{
    public function index()
    {
        return view('kelola_pembayaran.pembayaran_lainnya.index');
    }

    public function data()
    {
        $datas = DB::table('pembayaran_lainnyas')->get();

        foreach ($datas as $data) {
            $options = '';
            if (auth()->user()->can('edit_pembayaran_lainnya')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('kelola-pembayaran.pembayaran-lainnya.show', $data->id) . "`, `Ubah Pembayaran Lainnya`, `#pembayaranLainnya`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_pembayaran_lainnya')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-pembayaran.pembayaran-lainnya.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        DB::table('pembayaran_lainnyas')->insert([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function show($id)
    {
        $data = DB::table('pembayaran_lainnyas')->where('id', $id)->first();
        return response()->json([
            'message' => 'Berhasil',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        DB::table('pembayaran_lainnyas')->where('id', $id)->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }
}
