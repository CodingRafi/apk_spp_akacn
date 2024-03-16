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
            "username" => "034095",
            "password" => "034095akacaraka"
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
