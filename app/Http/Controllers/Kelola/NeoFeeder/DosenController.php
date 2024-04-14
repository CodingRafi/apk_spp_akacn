<?php

namespace App\Http\Controllers\Kelola\NeoFeeder;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function store(Request $request)
    {
        foreach ($request->data as $data) {
            if ($data['id_jenis_sdm'] == 12) {
                DB::beginTransaction();
                try {
                    $wilayah = DB::table('wilayahs')->where('id', $data['id_wilayah'])->first();
    
                    $user = User::updateOrCreate([
                        'login_key' => $data['nidn']
                    ], [
                        'name' => $data['nama_dosen'],
                        'email' => $data['email'],
                        'id_neo_feeder' => $data['id_dosen']
                    ]);
    
                    $user->assignRole('dosen');

                    $user->dosen()->updateOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'tempat_lahir' => $data['tempat_lahir'],
                        'tgl_lahir' => Carbon::createFromFormat('d-m-Y', $data['tanggal_lahir'])->format('Y-m-d'),
                        'jk' => ($data['jenis_kelamin'] == 'P' ? 'p' : 'l'),
                        'agama_id' => $data['id_agama'],
                        'status' => ($data['id_status_aktif'] == '1' ? '1' : '0'),
                        'nip' => $data['nip'],
                        'nama_ibu' => $data['nama_ibu_kandung'],
                        'nik' => $data['nik'],
                        'npwp' => $data['npwp'],
                        'no_sk_cpns' => $data['no_sk_cpns'],
                        'tgl_sk_cpns' => Carbon::parse($data['tanggal_sk_cpns'])->format('Y-m-d'),
                        'no_sk_pengangkatan' => $data['no_sk_pengangkatan'],
                        'mulai_sk_pengangkatan' => Carbon::parse($data['mulai_sk_pengangkatan'])->format('Y-m-d'),
                        'lembaga_pengangkat_id' => $data['id_lembaga_pengangkatan'],
                        'nama_pangkat_golongan' => $data['nama_pangkat_golongan'],
                        'jalan' => $data['jalan'],
                        'dusun' => $data['dusun'],
                        'rt' => $data['rt'],
                        'rw' => $data['rw'],
                        'kode_pos' => $data['kode_pos'],
                        'kewarganegaraan_id' => $wilayah->negara_id,
                        'wilayah_id' => $data['id_wilayah'],
                        'telepon' => $data['telepon'],
                        'handphone' => $data['handphone'],
                        'status_pernikahan' => $data['status_pernikahan'],
                        'tgl_mulai_pns' => Carbon::parse($data['tanggal_mulai_pns'])->format('Y-m-d'),
                    ]);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'message' => $th->getMessage()
                    ], 400);
                }
            }
        }

        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
