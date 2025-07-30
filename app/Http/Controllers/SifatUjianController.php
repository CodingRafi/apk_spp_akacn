<?php

namespace App\Http\Controllers;

use App\Models\SifatUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SifatUjianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_sifat_ujian', ['only' => ['index', 'show']]);
        $this->middleware('permission:add_kelola_sifat_ujian', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_sifat_ujian', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_sifat_ujian', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('data_master.sifat_ujian.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        $datas = SifatUjian::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kelola_sifat_ujian')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.sifat-ujian.show', $data->id) . "`, `Edit Sifat Ujian`, `#sifat_ujian`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kelola_sifat_ujian')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.sifat-ujian.destroy', $data->id) . "`)' type='button'>
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        SifatUjian::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SifatUjian  $sifatUjian
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = SifatUjian::findOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SifatUjian  $sifatUjian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        SifatUjian::findOrFail($id)->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SifatUjian  $sifatUjian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            SifatUjian::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus',
            ], 400);
        }
    }
}
