<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\{
    Potongan,
    Prodi,
    TahunAjaran,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PotonganController extends Controller
{
    public function index()
    {
        return view('kelola_pembayaran.potongan.index');
    }

    public function data()
    {
        $datas = Potongan::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_potongan')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('kelola-pembayaran.potongan.show', $data->id) . "`, `Ubah Potongan`, `#potongan`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_potongan')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-pembayaran.potongan.destroy', $data->id) . "`)' type='button'>
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
            'nama' => 'required',
        ]);

        Potongan::create($request->all());

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function show(Potongan $potongan)
    {
        return response()->json([
            'data' => $potongan
        ], 200);
    }

    public function update(Request $request, Potongan $potongan)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        $potongan->update($request->all());

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    public function destroy(Potongan $potongan)
    {
        DB::beginTransaction();
        try {
            $potongan->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal dihapus');
        }
    }
}
