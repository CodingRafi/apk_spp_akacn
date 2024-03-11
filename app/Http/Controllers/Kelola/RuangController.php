<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RuangController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_ruang', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_ruang', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_ruang', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_ruang', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.ruang.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kapasitas' => 'required|min:0'
        ]);

        Ruang::create([
            'nama' => $request->nama,
            'kapasitas' => $request->kapasitas,
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function data()
    {
        $datas = Ruang::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_ruang')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.ruang.show', $data->id) . "`, `Edit Ruang`, `#ruang`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_ruang')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.ruang.destroy', $data->id) . "`)' type='button'>
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

    public function show($id)
    {
        $data = Ruang::findOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'kapasitas' => 'required|min:0'
        ]);

        $data = Ruang::findOrFail($id);
        $data->update($request->all());

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function destroy($id)
    {
        $data = Ruang::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Berhasil dihapus');
    }
}
