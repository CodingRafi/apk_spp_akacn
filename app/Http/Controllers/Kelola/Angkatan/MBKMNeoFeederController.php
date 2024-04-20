<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MBKMNeoFeederController extends Controller
{
    public function store(Request $request, $prodi_id, $tahun_ajaran_id){
        $dataReq = json_decode($request->data);
        foreach ($dataReq as $data) {
            DB::beginTransaction();
            try {
                $tahunSemester = DB::table('tahun_semester')
                                    ->where('prodi_id', $prodi_id)
                                    ->where('tahun_ajaran_id', $tahun_ajaran_id)
                                    ->where('semester_id', $data->id_semester)
                                    ->first();
    
                $aktivitas = DB::table('mbkm')
                                ->insertGetId([
                                    'id_neo_feeder' => $data->id_aktivitas,
                                    'jenis_anggota' => $data->jenis_anggota,
                                    'jenis_aktivitas_id' => $data->id_jenis_aktivitas,
                                    'tahun_semester_id' => $tahunSemester->id,
                                    'judul' => $data->judul,
                                    'ket' => $data->keterangan,
                                    'lokasi' => $data->lokasi,
                                    'sk_tugas' => $data->sk_tugas,
                                    'tgl_sk_tugas' => $data->tgl_sk_tugas,
                                    'tanggal_mulai' => $data->tanggal_mulai,
                                    'tanggal_selesai' => $data->tanggal_selesai,
                                ]);

                                
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Gagal menyimpan'
                ], 500);
            }
        }
    }
}
