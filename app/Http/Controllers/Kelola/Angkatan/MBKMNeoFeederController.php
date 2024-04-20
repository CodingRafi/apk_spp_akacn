<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MBKMNeoFeederController extends Controller
{
    public function store(Request $request, $prodi_id, $tahun_ajaran_id)
    {
        $dataReq = json_decode($request->data);
        DB::beginTransaction();
        try {
            foreach ($dataReq as $data) {
                $tahunSemester = DB::table('tahun_semester')
                    ->where('prodi_id', $prodi_id)
                    ->where('tahun_ajaran_id', $tahun_ajaran_id)
                    ->where('semester_id', $data->id_semester)
                    ->first();

                DB::table('mbkm')
                    ->updateOrInsert([
                        'id_neo_feeder' => $data->id_aktivitas,
                    ], [
                        'jenis_anggota' => $data->jenis_anggota,
                        'jenis_aktivitas_id' => $data->id_jenis_aktivitas,
                        'tahun_semester_id' => $tahunSemester->id,
                        'judul' => $data->judul,
                        'ket' => $data->keterangan,
                        'lokasi' => $data->lokasi,
                        'sk_tugas' => $data->sk_tugas,
                        'tgl_sk_tugas' => Carbon::parse($data->tanggal_sk_tugas)->format('Y-m-d'),
                        'tanggal_mulai' => Carbon::parse($data->tanggal_mulai)->format('Y-m-d'),
                        'tanggal_selesai' => Carbon::parse($data->tanggal_selesai)->format('Y-m-d'),
                    ]);

                $get = DB::table('mbkm')->where('id_neo_feeder', $data->id_aktivitas)->first();

                //? Mahasiswa
                foreach ($data->mhs as $mhs) {
                    $userMhs = DB::table('users')
                        ->select('users.id')
                        ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                        ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $mhs->id_registrasi_mahasiswa)
                        ->first();

                    DB::table('mbkm_mhs')->updateOrInsert([
                        'id_anggota_neo_feeder' => $mhs->id_anggota,
                        'mbkm_id' => $get->id,
                        'mhs_id' => $userMhs->id,
                    ], [
                        'sync' => 1,
                        'peran' => $mhs->jenis_peran
                    ]);
                }

                //? Dosen Pembimbing
                foreach ($data->dosen_pembimbing as $dosenPembimbing) {
                    $userDosen = DB::table('users')
                        ->select('users.id')
                        ->where('users.id_neo_feeder', $dosenPembimbing->id_dosen)
                        ->first();

                    DB::table('mbkm_dosen_pembimbing')->updateOrInsert([
                        'id_bimbing_mahasiswa_neo_feeder' => $dosenPembimbing->id_bimbing_mahasiswa,
                        'mbkm_id' => $get->id,
                        'dosen_id' => $userDosen->id,
                    ], [
                        'kategori_kegiatan_id' => $dosenPembimbing->id_kategori_kegiatan,
                        'pembimbing_ke' => $dosenPembimbing->pembimbing_ke,
                    ]);
                }

                //? Dosen Penguji
                foreach ($data->dosen_penguji as $dosenPenguji) {
                    $userDosen = DB::table('users')
                        ->select('users.id')
                        ->where('users.id_neo_feeder', $dosenPenguji->id_dosen)
                        ->first();

                    DB::table('mbkm_dosen_penguji')->updateOrInsert([
                        'id_penguji_mahasiswa_neo_feeder' => $dosenPenguji->id_uji,
                        'mbkm_id' => $get->id,
                        'dosen_id' => $userDosen->id,
                    ], [
                        'kategori_kegiatan_id' => $dosenPembimbing->id_kategori_kegiatan,
                        'penguji_ke' => $dosenPenguji->penguji_ke,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Berhasil simpan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
