<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Mutu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MutuController extends Controller
{

    public function index()
    {
        return view('data_master.mutu.index');
    }

    public function data()
    {
        $data = Mutu::all();

        foreach ($data as $item) {
            $options = '';

            // if (auth()->user()->can('edit_mutu')) {
            //     $options = $options . " <button class='btn btn-warning'
            //             onclick='editForm(`" . route('data-master.mutu.show', $item->id) . "`, `Edit Mutu`, `#mutu`)'>
            //             <i class='ti-pencil'></i>
            //             Edit
            //         </button>";
            // }

            // if (auth()->user()->can('delete_mutu')) {
            //     $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.mutu.destroy', $item->id) . "`)' type='button'>
            //                             Hapus
            //                         </button>";
            // }

            $item->options = $options;
        }

        return DataTables::of($data)
            ->editColumn('status', function ($data) {
                return $data->status ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->addColumn('prodi', function($data){
                return $data->prodi->nama;
            })
            ->addIndexColumn()
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nilai' => 'required'
        ]);

        Mutu::create([
            'nama' => $request->nama,
            'nilai' => $request->nilai,
            'status' => $request->status ?? '0',
        ]);
        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    public function show($id)
    {
        $data = Mutu::findOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nilai' => 'required'
        ]);

        $mutu = Mutu::findOrFail($id);
        $mutu->update([
            'nama' => $request->nama,
            'nilai' => $request->nilai,
            'status' => $request->status ?? '0',
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate!'
        ], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $mutu = Mutu::findOrFail($id);
            $mutu->delete();
            DB::commit();
            return response()->json([
                'message' => 'Mutu Berhasil Dihapus'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Mutu Gagal Dihapus'
            ], 500);
        }
    }

    public function storeNeoFeeder(Request $request)
    {
        foreach ($request->data as $data) {
            DB::table('mutu')->updateOrInsert([
                'id_neo_feeder' => $data['id_bobot_nilai'],
            ], [
                'prodi_id' => $data['id_prodi'],
                'nama' => $data['nilai_huruf'],
                'nilai' => $data['nilai_indeks'],
                'bobot_minimum' => $data['bobot_minimum'],
                'bobot_maksimum' => $data['bobot_maksimum'],
                'status' => '1',
                'tanggal_mulai_efektif' => Carbon::parse($data['tanggal_mulai_efektif'])->format('Y-m-d'),
                'tanggal_akhir_efektif' => Carbon::parse($data['tanggal_akhir_efektif'])->format('Y-m-d'),
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
