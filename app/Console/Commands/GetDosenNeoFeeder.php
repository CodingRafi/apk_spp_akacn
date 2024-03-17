<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GetDosenNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-dosen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get dosen NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $resUser = getDataNeoFeeder([
            "act" => "GetListDosen",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);

        if (!$resUser['status']) {
            $this->error($resUser['message']);
            return 1;
        }

        $resDetailUser = getDataNeoFeeder([
            "act" => "DetailBiodataDosen",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);
        
        if (!$resDetailUser['status']) {
            $this->error($resDetailUser['message']);
            return 1;
        }

        $detailUser = $resDetailUser['res']->json()['data'];

        foreach ($resUser['res']->json()['data'] as $data) {
            $index = array_search($data['id_dosen'], array_column($detailUser, 'id_dosen'));
            $dataUser = $detailUser[$index];

            if ($dataUser['id_jenis_sdm'] == 12) {
                $user = User::updateOrCreate([
                    'login_key' => $dataUser['nidn']
                ],[
                    'name' => $dataUser['nama_dosen'],
                    'email' => $dataUser['email'],
                    'id_neo_feeder' => $data['id_dosen']
                ]);
    
                $user->assignRole('dosen');
                try {
                    $user->dosen()->updateOrCreate([
                        'user_id' => $user->id,
                    ],[
                        'tempat_lahir' => $dataUser['tempat_lahir'],
                        'tgl_lahir' => Carbon::createFromFormat('d-m-Y', $dataUser['tanggal_lahir'])->format('Y-m-d'),
                        'jk' => ($dataUser['jenis_kelamin'] == 'P' ? 'p' : 'l'),
                        'agama_id' => $dataUser['id_agama'],
                        'status' => ($dataUser['id_status_aktif'] == '1' ? '1' : '0'),
                        'nip' => $dataUser['nip'],
                        'nama_ibu' => $dataUser['nama_ibu_kandung'],
                        'nik' => $dataUser['nik'],
                        'npwp' => $dataUser['npwp'],
                        'no_sk_cpns' => $dataUser['no_sk_cpns'],
                        'tgl_sk_cpns' => Carbon::parse($dataUser['tanggal_sk_cpns'])->format('Y-m-d'),
                        'no_sk_pengangkatan' => $dataUser['no_sk_pengangkatan'],
                        'mulai_sk_pengangkatan' => Carbon::parse($dataUser['mulai_sk_pengangkatan'])->format('Y-m-d'),
                        'lembaga_pengangkat_id' => $dataUser['id_lembaga_pengangkatan'],
                        'nama_pangkat_golongan' => $dataUser['nama_pangkat_golongan'],
                        'jalan' => $dataUser['jalan'],
                        'dusun' => $dataUser['dusun'],
                        'rt' => $dataUser['rt'],
                        'rw' => $dataUser['rw'],
                        'kode_pos' => $dataUser['kode_pos'],
                        'wilayah_id' => $dataUser['id_wilayah'],
                        'telepon' => $dataUser['telepon'],
                        'handphone' => $dataUser['handphone'],
                        'status_pernikahan' => $dataUser['status_pernikahan'],
                        'tgl_mulai_pns' => Carbon::parse($dataUser['tanggal_mulai_pns'])->format('Y-m-d'),
                    ]);
                } catch (\Throwable $th) {
                    dd($th);
                }
            }
        }

        $this->info('Data dosen berhasil di get!');
        return 0;
    }
}
