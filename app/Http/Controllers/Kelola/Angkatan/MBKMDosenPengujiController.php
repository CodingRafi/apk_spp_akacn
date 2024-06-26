<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MBKMDosenPengujiController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $datas = DB::table('mbkm_dosen_penguji')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mbkm_dosen_penguji.penguji_ke',
                'kategori_kegiatans.nama as kategori_kegiatan',
                'mbkm_dosen_penguji.id_uji_neo_feeder'
            )
            ->join('users', 'users.id', 'mbkm_dosen_penguji.dosen_id')
            ->join('kategori_kegiatans', 'mbkm_dosen_penguji.kategori_kegiatan_id', '=', 'kategori_kegiatans.id')
            ->where('mbkm_dosen_penguji.mbkm_id', $mbkm_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                    onclick='editForm(`" . route('data-master.prodi.mbkm.dosen-penguji.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $mbkm_id, 'dosen_id' => $data->id]) . "`, `Edit Dosen Penguji`, `#dosenPengujiModal`, getDosenPenguji)'>
                    <i class='ti-pencil'></i>
                    Edit
                </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.mbkm.dosen-penguji.destroy', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $mbkm_id, 'dosen_id' => $data->id]) . "`, () => {tableDosenPenguji.ajax.reload()})' type='button'>
                                        Hapus
                                    </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('dosen', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->editCOlumn('send_neo_feeder', function ($datas) {
                return $datas->id_uji_neo_feeder ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'send_neo_feeder'])
            ->make(true);
    }

    public function getDosen($prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $data = User::role('dosen')
            ->select('users.id', 'users.name', 'users.login_key')
            ->join('profile_dosens', 'profile_dosens.user_id', '=', 'users.id')
            ->leftJoin('mbkm_dosen_penguji', function ($q) use ($mbkm_id) {
                $q->on('mbkm_dosen_penguji.dosen_id', '=', 'users.id')
                    ->where('mbkm_dosen_penguji.mbkm_id', '=', $mbkm_id);
            })
            ->when(request('except'), function ($q) {
                $q->orWhere('users.id', request('except'));
            })
            ->whereNull('mbkm_dosen_penguji.mbkm_id')
            ->get();

        return response()->json($data, 200);
    }

    public function store(Request $request, $prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $request->validate([
            'dosen_id' => 'required',
            'kategori_kegiatan_id' => 'required',
            'penguji_ke' => 'required'
        ]);

        DB::table('mbkm_dosen_penguji')->insert([
            'dosen_id' => $request->dosen_id,
            'kategori_kegiatan_id' => $request->kategori_kegiatan_id,
            'penguji_ke' => $request->penguji_ke,
            'mbkm_id' => $mbkm_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function show($prodi_id, $tahun_ajaran_id, $mbkm_id, $dosen_id)
    {
        $data = DB::table('mbkm_dosen_penguji')
            ->where('mbkm_id', $mbkm_id)
            ->where('dosen_id', $dosen_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $mbkm_id, $dosen_id)
    {
        $request->validate([
            'kategori_kegiatan_id' => 'required',
            'penguji_ke' => 'required'
        ]);

        DB::table('mbkm_dosen_penguji')
            ->where('mbkm_id', $mbkm_id)
            ->where('dosen_id', $dosen_id)
            ->update([
                'kategori_kegiatan_id' => $request->kategori_kegiatan_id,
                'penguji_ke' => $request->penguji_ke,
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function destroy($prodi_id, $tahun_ajaran_id, $mbkm_id, $dosen_id)
    {
        DB::table('mbkm_dosen_penguji')
            ->where('mbkm_id', $mbkm_id)
            ->where('dosen_id', $dosen_id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
