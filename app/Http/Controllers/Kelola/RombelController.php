<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RombelController extends Controller
{
    public function index()
    {
        $prodis = Prodi::where('status', "1")->get();
        return view('data_master.rombel.index', compact('prodis'));
    }

    public function data()
    {
        $datas = Rombel::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='". route('data-master.rombel.setDosenPa', $data->id) ."' class='btn btn-primary'>Set Dosen PA</a>";

            if (auth()->user()->can('edit_rombel')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.rombel.show', $data->id) . "`, `Edit Rombel`, `#rombel`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_rombel')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'prodi_id' => 'required'
        ]);

        Rombel::create([
            'prodi_id' => $request->prodi_id,
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function show(Rombel $rombel)
    {
        return response()->json([
            'code' => 200,
            'data' => $rombel,
            'message' => 'success'
        ], 200);
    }

    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'nama' => 'required',
            'prodi_id' => 'required'
        ]);

        $rombel->update($request->all());

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rombel  $rombel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rombel $rombel)
    {
        DB::beginTransaction();
        try {
            $rombel->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setDosenPa($id)
    {
        $dosen = User::role('dosen')->get();
        return view('data_master.rombel.set-dosen-pa', compact('dosen'));
    }

    public function storeDosenPa(Request $request){
        dd($request);
    }

    public function getTahunAjaran(){
        $tahun_ajarans = TahunAjaran::select('tahun_ajarans.*')
                        ->leftJoin('rombel_tahun_ajarans', 'rombel_tahun_ajarans.tahun_masuk_id', '=', 'tahun_ajarans.id')
                        ->when(request('id_pivot') && request('id_pivot') !== '', function ($query) {
                            $oldData = DB::table('rombel_tahun_ajarans')->where('id', request('id'))->first();
                            $query->orWhere('id', $oldData->tahun_ajaran_id);
                        })
                        ->whereNull('tahun_ajarans.id')
                        ->get();
        
        return response()->json([
            'message' => 'success',
            'data' => $tahun_ajarans
        ], 200);
    }
}