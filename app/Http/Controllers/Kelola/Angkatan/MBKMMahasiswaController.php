<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MBKMMahasiswaController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $datas = DB::table('mbkm_mhs')
            ->select('users.id', 'users.name', 'users.login_key', 'mbkm_mhs.peran')
            ->join('users', 'users.id', 'mbkm_mhs.mhs_id')
            ->where('mbkm_mhs.mbkm_id', $mbkm_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                    onclick='editForm(`" . route('data-master.prodi.mbkm.mahasiswa.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $mbkm_id, 'mhs_id' => $data->id]) . "`, `Edit Mahasiswa`, `#mahasiswaModal`, getMhs)'>
                    <i class='ti-pencil'></i>
                    Edit
                </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.mbkm.mahasiswa.destroy', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $mbkm_id, 'mhs_id' => $data->id]) . "`, () => {tableMhs.ajax.reload()})' type='button'>
                                        Hapus
                                    </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('peran', function ($datas) {
                return config('services.peran')[$datas->peran];
            })
            ->addColumn('mhs', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function getMhs($prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $mhs = User::role('mahasiswa')
            ->select('users.id', 'users.name', 'users.login_key')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->leftJoin('mbkm_mhs', function ($q) use ($mbkm_id) {
                $q->on('mbkm_mhs.mhs_id', '=', 'users.id')
                    ->where('mbkm_mhs.mbkm_id', '=', $mbkm_id);
            })
            ->where('profile_mahasiswas.prodi_id', $prodi_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->when(request('except'), function($q){
                $q->orWhere('users.id', request('except'));
            })
            ->whereNull('mbkm_mhs.mbkm_id')
            ->get();

        return response()->json($mhs, 200);
    }

    public function store(Request $request, $prodi_id, $tahun_ajaran_id, $mbkm_id)
    {
        $request->validate([
            'mhs_id' => 'required',
            'peran' => 'required'
        ]);

        DB::table('mbkm_mhs')->insert([
            'mhs_id' => $request->mhs_id,
            'peran' => $request->peran,
            'mbkm_id' => $mbkm_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function show($prodi_id, $tahun_ajaran_id, $mbkm_id, $mhs_id)
    {
        $data = DB::table('mbkm_mhs')
            ->where('mbkm_id', $mbkm_id)
            ->where('mhs_id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $mbkm_id, $mhs_id)
    {
        $request->validate([
            'peran' => 'required'
        ]);

        DB::table('mbkm_mhs')
            ->where('mbkm_id', $mbkm_id)
            ->where('mhs_id', $mhs_id)
            ->update([
                'peran' => $request->peran,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function destroy($prodi_id, $tahun_ajaran_id, $mbkm_id, $mhs_id)
    {
        DB::table('mbkm_mhs')
            ->where('mbkm_id', $mbkm_id)
            ->where('mhs_id', $mhs_id)
            ->delete();
        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
