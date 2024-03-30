<?php

namespace App\Http\Controllers\Kelola\NeoFeeder;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    private function parseFormatTgl($tgl)
    {
        try {
            $date = Carbon::createFromFormat('d-m-Y', $tgl);
            return $date->format('Y-m-d');
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function store(Request $request)
    {
        $dataReq = json_decode($request->data);
        foreach ($dataReq as $data) {
            DB::beginTransaction();
            try {
                foreach ($data->riwayat as $riwayat) {
                    $user = User::updateOrCreate([
                        'login_key' => $riwayat->nim,
                    ], [
                        'name' => $riwayat->nama_mahasiswa,
                        'email' => $data->email,
                    ]);

                    $user->assignRole('mahasiswa');

                    DB::table('profile_mahasiswas')
                        ->updateOrInsert([
                            'user_id' => $user->id,
                            'neo_feeder_id_mahasiswa' => $riwayat->id_mahasiswa,
                            'neo_feeder_id_registrasi_mahasiswa' => $riwayat->id_registrasi_mahasiswa,
                        ], [
                            'sync_neo_feeder' => '1',
                            'nisn' => $data->nisn,
                            'nik' => $data->nik,
                            'tempat_lahir' => $data->tempat_lahir,
                            'tgl_lahir' => $this->parseFormatTgl($data->tanggal_lahir),
                            'jk' => ($data->jenis_kelamin == 'P' ? 'p' : 'l'),
                            'kewarganegaraan_id' => $data->id_negara,
                            'wilayah_id' => 'id_wilayah',
                            'jalan' => $data->jalan,
                            'rt' => $data->rt,
                            'rw' => $data->rw,
                            'dusun' => $data->dusun,
                            'kelurahan' => $data->kelurahan,
                            'kode_pos' => $data->kode_pos,

                            'nama_ayah' => $data->nama_ayah,
                            'nik_ayah' => $data->nik_ayah,
                            'tgl_lahir_ayah' => $this->parseFormatTgl($data->tanggal_lahir_ayah),
                            'jenjang_ayah_id' => $data->id_pendidikan_ayah,
                            'pekerjaan_ayah_id' => $data->id_pekerjaan_ayah,
                            'penghasilan_ayah_id' => $data->id_penghasilan_ayah,

                            'nama_ibu' => $data->nama_ibu_kandung,
                            'nik_ibu' => $data->nik_ibu,
                            'tgl_lahir_ibu' => $this->parseFormatTgl($data->tanggal_lahir_ibu),
                            'jenjang_ibu_id' => $data->id_pendidikan_ibu,
                            'pekerjaan_ibu_id' => $data->id_pekerjaan_ibu,
                            'penghasilan_ibu_id' => $data->id_penghasilan_ibu,

                            'nama_wali' => $data->nama_wali,
                            'tgl_lahir_wali' => $this->parseFormatTgl($data->tanggal_lahir_wali),
                            'jenjang_wali_id' => $data->id_pendidikan_wali,
                            'pekerjaan_wali_id' => $data->id_pekerjaan_wali,
                            'penghasilan_wali_id' => $data->id_penghasilan_wali,

                            'mhs_kebutuhan_khusus' => $data->id_kebutuhan_khusus_mahasiswa,
                            'ayah_kebutuhan_khusus' => $data->id_kebutuhan_khusus_ayah,
                            'ibu_kebutuhan_khusus' => $data->id_kebutuhan_khusus_ibu,

                            'tahun_masuk_id' => substr($riwayat->id_periode_masuk, 0, -1),
                            'semester_id' => $riwayat->id_periode_masuk,
                            'jenis_pembiayaan_id' => $riwayat->id_pembiayaan,
                            'jenis_daftar_id' => $riwayat->id_jenis_daftar,
                            'jalur_masuk_id' => $data->jalur_masuk,
                            'jenis_keluar_id' => $riwayat->id_jenis_keluar,
                            'prodi_id' => $riwayat->id_prodi,
                            'source' => 'neo_feeder'
                        ]);
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => $th->getMessage()
                ], 400);
            }
        }
    }
}
