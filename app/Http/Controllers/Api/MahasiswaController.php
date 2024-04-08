<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\storeApiMhsRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function store(storeApiMhsRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama,
                'login_key' => $request->nim,
                'email' => $request->email
            ]);

            $user->assignRole('mahasiswa');

            DB::table('profile_mahasiswas')->insert([
                'user_id' => $user->id,
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'jk' => $request->jk,

                //? Alamat
                'kewarganegaraan_id' => $request->kewarganegaraan_id,
                'wilayah_id' => $request->wilayah_id,
                'jalan' => $request->jalan,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'dusun' => $request->dusun,
                'kelurahan' => $request->kelurahan,
                'kode_pos' => $request->kode_pos,

                //? Ayah
                'nama_ayah' => $request->nama_ayah,
                'tgl_lahir_ayah' => $request->tgl_lahir_ayah,
                'nik_ayah' => $request->nik_ayah,
                'jenjang_ayah_id' => $request->jenjang_ayah_id,
                'pekerjaan_ayah_id' => $request->pekerjaan_ayah_id,
                'penghasilan_ayah_id' => $request->penghasilan_ayah_id,

                //? Ibu
                'nama_ibu' => $request->nama_ibu,
                'tgl_lahir_ibu' => $request->tgl_lahir_ibu,
                'nik_ibu' => $request->nik_ibu,
                'jenjang_ibu_id' => $request->jenjang_ibu_id,
                'pekerjaan_ibu_id' => $request->pekerjaan_ibu_id,
                'penghasilan_ibu_id' => $request->penghasilan_ibu_id,

                //? Wali
                'nama_wali' => $request->nama_wali,
                'tgl_lahir_wali' => $request->tgl_lahir_wali,
                'nik_wali' => $request->nik_wali,
                'jenjang_wali_id' => $request->jenjang_wali_id,
                'pekerjaan_wali_id' => $request->pekerjaan_wali_id,
                'penghasilan_wali_id' => $request->penghasilan_wali_id,

                'telepon' => $request->telepon,
                'penerima_kps' => $request->penerima_kps,
                'npwp' => $request->npwp,
                
                'agama_id' => $request->agama_id,
                'rombel_id' => $request->rombel_id,
                'prodi_id' => $request->prodi_id,
                'jenis_tinggal_id' => $request->jenis_tinggal_id,
                'alat_transportasi_id' => $request->alat_transportasi_id,
                'mhs_kebutuhan_khusus' => $request->mhs_kebutuhan_khusus,
                'tahun_masuk_id' => $request->tahun_masuk_id,
                'semester_id' => $request->semester_id,
                'jenis_pembiayaan_id' => $request->jenis_pembiayaan_id,
                'jalur_masuk_id' => $request->jalur_masuk_id,
                'jenis_kelas_id' => $request->jenis_kelas_id,
                'source' => 'pmb'
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Berhasil disimpan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
