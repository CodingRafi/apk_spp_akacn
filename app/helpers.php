<?php

use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Role;

if (!function_exists('getRoleWithout')) {

    function getRoleWithout($except = [])
    {
        return Role::whereNotIn('name', $except)->get();
    }
}

if (!function_exists('formatRupiah')) {

    function formatRupiah($data)
    {
        return 'Rp ' . number_format($data, 0, ',', '.');
    }
}

if (!function_exists('getTahunAjaranActive')) {

    function getTahunAjaranActive()
    {
        return TahunAjaran::where('status', "1")->first();
    }
}

if (!function_exists('generateUuid')) {

    function generateUuid()
    {
        return Uuid::uuid4()->toString();
    }
}

if (!function_exists('parseDate')) {

    function parseDate($date)
    {
        return date("d F Y", strtotime($date));
    }
}

if (!function_exists('getRole')) {

    function getRole()
    {
        return Auth::user()->roles->first();
    }
}

if (!function_exists('getUrlNeoFeeder')) {

    function getUrlNeoFeeder()
    {
        return DB::table('settings')->where('id', 2)->first()->value;
    }
}

if (!function_exists('getRombelMhs')) {

    function getRombelMhs($prodi_id, $tahun_masuk_id, $rombel_id)
    {
        if ($rombel_id) {
            $data = DB::table('rombels')
                ->join('rombel_tahun_ajarans', 'rombels.id', '=', 'rombel_tahun_ajarans.rombel_id')
                ->join('users', 'users.id', '=', 'rombel_tahun_ajarans.dosen_pa_id')
                ->where('prodi_id', $prodi_id)
                ->where('tahun_masuk_id', $tahun_masuk_id)
                ->where('rombel_id', $rombel_id)
                ->select('rombels.nama', 'users.name as dosen_pa')
                ->get();

            // Langsung manipulasi data jika query mengembalikan hasil
            if ($data->isNotEmpty()) {
                $groupedData = $data->groupBy('nama');
                $firstGroup = $groupedData->first();

                // Pastikan grup memiliki elemen sebelum mencoba mengakses properti
                if ($firstGroup->isNotEmpty()) {
                    return [
                        'nama' => $firstGroup->first()->nama,
                        'dosen_pa' => $firstGroup->pluck('dosen_pa')->implode(', ')
                    ];
                }
            }
        }

        // Mengembalikan data default jika $rombel_id tidak ada atau query tidak mengembalikan hasil
        return [
            'nama' => '',
            'dosen_pa' => '',
        ];
    }
}

if (!function_exists('encryptString')) {

    function encryptString($string)
    {
        $key = '123';
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted = openssl_encrypt($string, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $encrypted = base64_encode($iv . $encrypted);
        return $encrypted;
    }
}

if (!function_exists('getDataNeoFeeder')) {

    function getDataNeoFeeder($raw)
    {
        $url = getUrlNeoFeeder();

        if ($url == '') {
            return [
                'status' => false,
                'message' => 'Url NEO FEEDER belum diset'
            ];
        }

        //? Get Token
        $resToken = Http::post($url, [
            "act" => "GetToken",
            "username" => config('services.neo_feeder.USERNAME'),
            "password" => config('services.neo_feeder.PASSWORD')
        ]);

        if ($resToken->status() != 200) {
            return [
                'status' => false,
                'message' => 'Gagal get token'
            ];
        }

        $token = $resToken->json()['data']['token'];

        //? Get Data
        $raw['token'] = $token;
        $res = Http::post($url, $raw);

        if ($res->status() != 200) {
            return [
                'status' => false,
                'message' => 'Gagal get data'
            ];
        }

        return [
            'status' => true,
            'res' => $res
        ];
    }
}
