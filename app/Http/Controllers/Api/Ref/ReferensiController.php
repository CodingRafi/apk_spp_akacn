<?php

namespace App\Http\Controllers\Api\Ref;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferensiController extends Controller
{
    public function alatTransportasi()
    {
        $data = DB::table('alat_transportasis')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function jenisTinggal()
    {
        $data = DB::table('jenis_tinggals')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function jenisKelas()
    {
        $data = DB::table('jenis_kelas')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function jenjang()
    {
        $data = DB::table('jenjangs')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function penghasilan()
    {
        $data = DB::table('penghasilans')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function agama()
    {
        $data = DB::table('agamas')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function prodi()
    {
        $data = DB::table('prodi')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function pekerjaan()
    {
        $data = DB::table('pekerjaans')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function kewarganegaraan()
    {
        $data = DB::table('kewarganegaraans')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function wilayah()
    {
        $data = DB::table('wilayahs')
            ->when(request('negara_id'), function ($q) {
                $q->where('negara_id', request('negara_id'));
            })
            ->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function rombel()
    {
        if (!request('tahun_ajaran_id')) {
            return response()->json([
                'message' => 'Tidak ada tahun ajaran'
            ], 400);
        }

        $data = DB::table('rombels')
            ->select('rombels.*', 'users.name as dosen_pa', 'users.login_key as nip_pa')
            ->join('rombel_tahun_ajarans', 'rombel_tahun_ajarans.rombel_id', '=', 'rombels.id')
            ->join('users', 'users.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->where('rombel_tahun_ajarans.tahun_masuk_id', request('tahun_ajaran_id'))
            ->when(request('jenis_kelas_id'), function ($q) {
                $q->where('jenis_kelas_id', request('jenis_kelas_id'));
            })
            ->when(request('prodi_id'), function ($q) {
                $q->where('rombels.prodi_id', request('prodi_id'));
            })
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function tahunAjaran(){
        $data = DB::table('tahun_ajarans')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }
}
