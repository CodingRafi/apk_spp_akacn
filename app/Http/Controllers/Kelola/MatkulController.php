<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatkulRequest;
use App\Models\Matkul;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function index()
    {
        $prodis = DB::table('prodi')->get();
        return view('data_master.matkul.index', compact('prodis'));
    }

    public function dataMatkul()
    {
        $datas = Matkul::with('prodi')->get();

        foreach ($datas as $data) {
            $options = '';

            $options .= "<a href='" . route('data-master.mata-kuliah.materi.index', $data->id) . "' class='btn btn-info mx-2'>Materi</a>";

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.mata-kuliah.show', $data->id) . "`, `Edit Mata Kuliah`, `#matkul`)' type='button'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.mata-kuliah.destroy', $data->id) . "`, () => {tableMatkul.ajax.reload()})' type='button'>
                                                Hapus
                                            </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->editColumn('sync', function ($data) {
                return $data->sync ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'sync'])
            ->make(true);
    }

    public function store(MatkulRequest $request)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token');
            $requestParse['id'] = generateUuid();
            $data = Matkul::create($requestParse);

            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show($id)
    {
        $data = Matkul::find($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MatkulRequest $request, $id)
    {
        $matkul = Matkul::find($id);

        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token');
            $data = $matkul->update($requestParse);
            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Berhasil diubah'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('matkuls')
                ->where('id', $id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function storeNeoFeeder(Request $request)
    {
        foreach ($request->data as $data) {
            DB::table('matkuls')->updateOrInsert([
                'id' => $data['id_matkul'],
            ], [
                'prodi_id' => $data['id_prodi'],
                'kode' => $data['kode_mata_kuliah'],
                'nama' => $data['nama_mata_kuliah'],
                'jenis_matkul' => $data['id_jenis_mata_kuliah'],
                'kel_matkul' => $data['id_kelompok_mata_kuliah'],
                'sks_mata_kuliah' => $data['sks_mata_kuliah'],
                'sks_tatap_muka' => $data['sks_tatap_muka'],
                'sks_praktek' => $data['sks_praktek'],
                'sks_praktek_lapangan' => $data['sks_praktek_lapangan'],
                'sks_simulasi' => $data['sks_simulasi'],
                'ada_sap' => ($data['ada_sap'] ? $data['ada_sap'] : "0"),
                'ada_silabus' => ($data['ada_silabus'] ? $data['ada_silabus'] : "0"),
                'ada_bahan_ajar' => ($data['ada_bahan_ajar'] ? $data['ada_bahan_ajar'] : "0"),
                'ada_acara_praktek' => ($data['ada_acara_praktek'] ? $data['ada_acara_praktek'] : "0"),
                'ada_diklat' => ($data['ada_diktat'] ? $data['ada_diktat'] : "0"),
                'tgl_mulai_aktif' => Carbon::parse($data['tanggal_mulai_efektif'])->format('Y-m-d'),
                'tgl_akhir_aktif' => Carbon::parse($data['tanggal_selesai_efektif'])->format('Y-m-d'),
                'sync' => '1',
            ]);
        }

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }
}
