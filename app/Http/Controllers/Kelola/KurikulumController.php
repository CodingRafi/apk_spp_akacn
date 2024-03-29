<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KurikulumController extends Controller
{
    public function index()
    {
        return view('data_master.kurikulum.index');
    }

    public function create()
    {
        $prodis = Prodi::all();
        $semesters = Semester::all();
        return view('data_master.kurikulum.form', compact('prodis', 'semesters'));
    }

    public function getMatkul($kurikulum_id)
    {
        $kurikulum = DB::table('kurikulums')
            ->where('id', $kurikulum_id)
            ->first();

        $datas = DB::table('matkuls')
            ->leftJoin('kurikulum_matkul', function ($q) {
                $q->on('kurikulum_matkul.matkul_id', '=', 'matkuls.id')
                    ->where('kurikulum_matkul.kurikulum_id', '=', request('kurikulum_id'));
            })
            ->whereNull('kurikulum_matkul.matkul_id')
            ->where('matkuls.prodi_id', $kurikulum->prodi_id)
            ->select('matkuls.*')
            ->get();

        return response()->json([
            'data' => $datas
        ], 200);
    }

    public function storeMatkul(Request $request)
    {
        $request->validate([
            'kurikulum_id' => 'required'
        ]);

        foreach ($request->matkul_id as $matkul_id) {
            DB::table('kurikulum_matkul')->insert([
                'kurikulum_id' => $request->kurikulum_id,
                'matkul_id' => $matkul_id
            ]);
        }

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function dataMatkul($kurikulum_id)
    {
        if ($kurikulum_id != ':id') {
            $datas = Kurikulum::find($kurikulum_id)->matkul()->with('prodi')->get();
        } else {
            $datas = [];
        }
        foreach ($datas as $data) {
            $data->options = "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kurikulum.destroyMatkul', ['kurikulum_id' => $kurikulum_id, 'matkul_id' => $data->id]) . "`, () => {tableMatkul.ajax.reload()})' type='button'>
                        Hapus
                    </button>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function destroyMatkul($kurikulum_id, $matkul_id)
    {
        DB::table('kurikulum_matkul')
            ->where('kurikulum_id', $kurikulum_id)
            ->where('matkul_id', $matkul_id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }

    public function data()
    {
        $datas = Kurikulum::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.kurikulum.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kurikulum.destroy', $data->id) . "`)' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($data) {
                return $data->prodi->nama;
            })
            ->addColumn('semester', function ($data) {
                return $data->semester->nama;
            })
            ->editColumn('sync', function ($data) {
                return $data->sync ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'sync'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jml_sks_lulus' => 'required',
            'jml_sks_wajib' => 'required',
            'jml_sks_pilihan' => 'required',
            'semester_id' => 'required',
            'prodi_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = Kurikulum::create([
                'id' => generateUuid(),
                'nama' => $request->nama,
                'jml_sks_lulus' => $request->jml_sks_lulus,
                'jml_sks_wajib' => $request->jml_sks_wajib,
                'jml_sks_pilihan' => $request->jml_sks_pilihan,
                'semester_id' => $request->semester_id,
                'prodi_id' => $request->prodi_id,
                'jml_sks_mata_kuliah_wajib' => $request->jml_sks_mata_kuliah_wajib,
                'jml_sks_mata_kuliah_pilihan' => $request->jml_sks_mata_kuliah_pilihan,
                'sync' => '0'
            ]);
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

    public function edit(Kurikulum $kurikulum)
    {
        $prodis = Prodi::all();
        $semesters = Semester::all();
        return view('data_master.kurikulum.form', [
            'prodis' => $prodis,
            'data' => $kurikulum,
            'semesters' => $semesters
        ]);
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'nama' => 'required',
            'jml_sks_lulus' => 'required',
            'jml_sks_wajib' => 'required',
            'semester_id' => 'required',
            'prodi_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $kurikulum->update([
                'nama' => $request->nama,
                'jml_sks_lulus' => $request->jml_sks_lulus,
                'jml_sks_wajib' => $request->jml_sks_wajib,
                'jml_sks_pilihan' => $request->jml_sks_pilihan,
                'semester_id' => $request->semester_id,
                'prodi_id' => $request->prodi_id,
                'jml_sks_mata_kuliah_wajib' => $request->jml_sks_mata_kuliah_wajib,
                'jml_sks_mata_kuliah_pilihan' => $request->jml_sks_mata_kuliah_pilihan,
                'sync' => '0'
            ]);
            DB::commit();
            return response()->json([
                'data' => $kurikulum,
                'message' => 'Berhasil diupdate'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Kurikulum $kurikulum)
    {
        DB::beginTransaction();
        try {
            $kurikulum->delete();
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

    public function storeNeoFeeder(Request $request)
    {
        foreach ($request->data as $data) {
            $cek = DB::table('kurikulums')
                ->where('id_neo_feeder', $data['id_kurikulum'])
                ->exists();

            $dataReq = [
                'nama' => $data['nama_kurikulum'],
                'prodi_id' => $data['id_prodi'],
                'semester_id' => $data['id_semester'],
                'jml_sks_lulus' => $data['jumlah_sks_lulus'],
                'jml_sks_wajib' => $data['jumlah_sks_wajib'],
                'jml_sks_pilihan' => $data['jumlah_sks_pilihan'],
                'jml_sks_mata_kuliah_wajib' => $data['jumlah_sks_mata_kuliah_wajib'],
                'jml_sks_mata_kuliah_pilihan' => $data['jumlah_sks_mata_kuliah_pilihan'],
                'sync' => "1",
            ];

            if ($cek) {
                $dataReq['updated_at'] = now();
                Kurikulum::where('id_neo_feeder', $data['id_kurikulum'])->update($dataReq);
            } else {
                $dataReq['id'] = generateUuid();
                $dataReq['id_neo_feeder'] = $data['id_kurikulum'];
                Kurikulum::create($dataReq);
            }
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
