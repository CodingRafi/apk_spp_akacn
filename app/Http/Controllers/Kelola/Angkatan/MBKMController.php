<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\MbkmRequest;
use App\Models\KategoriKegiatan;
use App\Models\MBKM;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MBKMController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = MBKM::select('mbkm.judul', 'mbkm.id', 'mbkm.id_neo_feeder')
            ->join('tahun_semester', 'mbkm.tahun_semester_id', 'tahun_semester.id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->with('mahasiswa')
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<button class='btn btn-primary mx-2' onclick='storeToNeoFeeder(`". $data->id ."`, `". $data->id_neo_feeder ."`)' type='button'>
                                                Send To Neo Feeder
                                            </button>";

            $options .= " <a href='". route('data-master.prodi.mbkm.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) ."' class='btn btn-primary'>Detail</a>";


            if (auth()->user()->can('delete_kelola_mbkm')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.mbkm.destroy', ['tahun_ajaran_id' => $tahun_ajaran_id, 'prodi_id' => $prodi_id, 'id' => $data->id]) . "`, () => {tableMbkm.ajax.reload()})' type='button'>
                                                    Hapus
                                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('jml_mhs', function ($datas) {
                return $datas->mahasiswa()->count();
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(MbkmRequest $request, $prodi_id)
    {
        DB::beginTransaction();
        try {
            $req = $request->except('_method', '_token', 'mhs_id');
            $req['prodi_id'] = $prodi_id;

            MBKM::create($req);

            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($tahun_ajaran_id, $prodi_id, $id)
    {
        $data = MBKM::where('id', $id)
            ->first();

        $kategoriKegiatan = KategoriKegiatan::all();

        return view('data_master.prodi.angkatan.mbkm.show', compact('data', 'kategoriKegiatan'));
    }

    public function update(MbkmRequest $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        DB::beginTransaction();
        try {
            $data = MBKM::where('id', $id)->first();
            $data->update($request->except('_method', '_token', 'mhs_id'));
            DB::commit();
            return response()->json([
                'message' => 'Berhasil diupdate'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 200);
        }
    }

    public function destroy($prodi_id, $tahun_ajaran_id, $id)
    {
        $data = MBKM::findOrFail($id);
        if ($data->id_neo_feeder) {
            return response()->json([
                'message' => 'Hapus yang di neo feeder dulu'
            ], 200);
        }

        DB::beginTransaction();
        try {
            $data->mahasiswa()->delete();
            $data->dosenPembimbing()->delete();
            $data->dosenPenguji()->delete();
            $data->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
