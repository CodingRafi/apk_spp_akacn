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

    function getRombelMhs($mhs_id)
    {
        if ($mhs_id) {
            $data = DB::table('rombel_mhs')
                        ->join('rombel_tahun_ajarans', 'rombel_mhs.rombel_tahun_ajaran_id', '=', 'rombel_tahun_ajarans.id')
                        ->join('rombels', 'rombel_tahun_ajarans.rombel_id', '=', 'rombels.id')
                        ->join('users', 'users.id', '=', 'rombel_tahun_ajarans.dosen_pa_id')
                        ->select('rombels.nama as nama', 'users.name as dosen_pa')
                        ->where('rombel_mhs.mhs_id', $mhs_id)
                        ->first();

            if ($data) {
                return [
                    'nama' => $data->nama,
                    'dosen_pa' => $data->dosen_pa
                ];
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

if (!function_exists('getHari')) {

    function getHari($timestamp)
    {
        $carbonDate = Carbon::parse($timestamp);
        Carbon::setLocale('id');
        return $carbonDate->translatedFormat('l');
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
