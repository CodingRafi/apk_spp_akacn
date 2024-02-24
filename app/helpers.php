<?php

use App\Models\TahunAjaran;
use Carbon\Carbon;
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
        $date = Carbon::createFromFormat('Y-m-d', $date);
        return $date->translatedFormat('d F Y');
    }
}
