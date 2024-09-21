<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulMhsController extends Controller
{
    public function index($tahun_ajaran_id, $id)
    {
        $tahunMasuk = DB::table('tahun_ajarans')
                        ->select('id', 'nama')
                        ->where('id', '!=', $tahun_ajaran_id)
                        ->get();
        return view('data_master.tahun_ajaran.matkul.setMhs', compact(
            'id',
            'tahun_ajaran_id',
            'tahunMasuk'
        ));
    }

    public function data($tahun_ajaran_id, $id){
        $datas = DB::table('tahun_matkul_mhs')
                    ->select('tahun_matkul_mhs.id', 'users.name', 'users.login_key')
                    ->join('users', 'users.id', 'tahun_matkul_mhs.mhs_id')
                    ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                    ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.tahun-ajaran.matkul.mhs.destroy', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id, 'tahun_matkul_mhs_id' => $data->id]) . "`, () => {tableMhs.ajax.reload()})' type='button'>
                                    Hapus
                                </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function getMhs($tahun_ajaran_id, $id, $tahunMasuk){
        $mhs = DB::table('users')
                ->select('users.id', 'users.name', 'users.login_key')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
                ->leftJoin('tahun_matkul_mhs', 'tahun_matkul_mhs.mhs_id', 'users.id')
                ->where('profile_mahasiswas.tahun_masuk_id', $tahunMasuk)
                ->whereNull('tahun_matkul_mhs.mhs_id')
                ->get();

        return response()->json($mhs, 200);
    }

    public function store(Request $request, $tahun_ajaran_id, $id){
        foreach ($request->mhs_id as  $mhs_id) {
            DB::table('tahun_matkul_mhs')->insert([
                'tahun_matkul_id' => $id,
                'mhs_id' => $mhs_id
            ]);
        }

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function destroy($tahun_ajaran_id, $matkul_id, $tahun_matkul_mhs_id)
    {
        $getTahunMatkulMhs = DB::table('tahun_matkul_mhs')
                ->where('id', $tahun_matkul_mhs_id)
                ->first();

        $check = DB::table('krs')
                    ->join('krs_matkul', function($q) use($getTahunMatkulMhs){
                        $q->on('krs_matkul.krs_id', 'krs.id')
                            ->where('krs_matkul.tahun_matkul_id', $getTahunMatkulMhs->tahun_matkul_id);
                    })
                    ->where('mhs_id', $getTahunMatkulMhs->mhs_id)
                    ->count();

        if ($check > 0) {
            return response()->json([
                'message' => 'Gagal dihapus. Mahasiswa sudah menggunakan.'
            ], 400);
        }

        DB::table('tahun_matkul_mhs')
            ->where('id', $tahun_matkul_mhs_id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
