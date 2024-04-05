<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\MbkmRequest;
use App\Models\MBKM;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MBKMController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = MBKM::select('mbkm.judul', 'mbkm.id')
            ->join('tahun_semester', 'mbkm.tahun_semester_id', 'tahun_semester.id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->with('mahasiswa')
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kelola_mbkm')) {
                $options = $options . " <button class='btn btn-warning'
                                    onclick='editForm(`" . route('data-master.prodi.mbkm.show', ['tahun_ajaran_id' => $tahun_ajaran_id, 'prodi_id' => $prodi_id, 'id' => $data->id]) . "`, `Edit MBKM`, `#Mbkm`)'>
                                    <i class='ti-pencil'></i>
                                    Edit
                                </button>";
            }

            if (auth()->user()->can('delete_kelola_mbkm')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.mbkm.destroy', ['tahun_ajaran_id' => $tahun_ajaran_id, 'prodi_id' => $prodi_id, 'id' => $data->id]) . "`, () => {tableMbkm.ajax.reload()})' type='button'>
                                                    Hapus
                                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('jml_mhs', function ($datas) {
                return $datas->mahasiswa()->whereNull('deleted_at')->count();
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

            $data = MBKM::create($req);

            foreach ($request->mhs_id as $mhs_id) {
                DB::table('mbkm_mhs')->insert([
                    'mbkm_id' => $data->id,
                    'mhs_id' => $mhs_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

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
            ->with('mahasiswa')
            ->first();

        $data->mhs_id = $data->mahasiswa()
                ->whereNull('deleted_at')
                ->pluck('users.id')
                ->toArray();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MbkmRequest $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        $data = MBKM::where('id', $id)->first();
        $mhsDb = $data->mahasiswa()
            ->where('deleted_at', null)
            ->pluck('users.id')
            ->toArray();

        DB::beginTransaction();
        try {
            //? delete mahasiswa
            $deleteMhs = array_diff($mhsDb, ($request->mhs_id ?? []));
            foreach ($deleteMhs as $mhs) {
                DB::table('mbkm_mhs')
                    ->where('mbkm_id', $id)
                    ->where('mhs_id', $mhs)
                    ->update([
                        'deleted_at' => now(),
                    ]);
            }

            //? Add mahasiswa
            $addMhs = array_diff(($request->mhs_id ?? []), $mhsDb);
            foreach ($addMhs as $mhs) {
                $exist = DB::table('mbkm_mhs')
                    ->where('mbkm_id', $id)
                    ->where('mhs_id', $mhs)
                    ->exists();

                if ($exist) {
                    DB::table('mbkm_mhs')
                        ->where('mbkm_id', $id)
                        ->where('mhs_id', $mhs)
                        ->update([
                            'deleted_at' => null
                        ]);
                } else {
                    DB::table('mbkm_mhs')->insert([
                        'mbkm_id' => $id,
                        'mhs_id' => $mhs,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

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
