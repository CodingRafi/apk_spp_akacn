<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'matkul' => [
        'jenis' => [
            "A" => "Wajib",
            "B" => "Pilihan",
            "C" => "Wajib Peminatan",
            "D" => "Pilihan Peminatan",
            "S" => "Tugas akhir/Skripsi/Tesis/Disertasi"
        ],
        "kelompok" => [
            "A" => "MPK",
            "B" => "MKK",
            "C" => "MKB",
            "D" => "MPB",
            "E" => "MBB",
            "F" => "MKU / MKDU",
            "G" => "MKDK",
            "H" => "MKK"
        ]
    ],

    'hari' => [
        '1' => 'Senin',
        '2' => 'Selasa',
        '3' => 'Rabu',
        '4' => 'Kamis',
        '5' => 'Jumat',
        '6' => 'Sabtu',
        '7' => 'Minggu',
    ],

    'statusPresensi' => [
        'H' => 'Hadir',
        '-' => 'Tidak Hadir',
        'C' => 'Cuti',
        'I' => 'Izin',
        'S' => 'Sakit',
        'A' => 'Alpa',
    ],

    'ujian' => [
        [
            'key' => 'uts',
            'value' => 'UTS',
            'indexStart' => 0
        ],
        [
            'key' => 'uas',
            'value' => 'UAS',
            'indexStart' => 8
        ]
    ],

    'max_pertemuan' => 14,

    'neo_feeder' => [
        'url' => 'https://cac6-110-138-84-162.ngrok-free.app/ws/live2.php',
        'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF9wZW5nZ3VuYSI6ImJlMGU0ODk1LTVjYjYtNGMxYi04NjZlLTZkMzU1MDc0OWIwMSIsInVzZXJuYW1lIjoiMDM0MDk1Iiwibm1fcGVuZ2d1bmEiOiJOZ2F0ZW1vIiwidGVtcGF0X2xhaGlyIjoiR3JvYm9nYW4iLCJ0Z2xfbGFoaXIiOiIxOTczLTA1LTA1VDE3OjAwOjAwLjAwMFoiLCJqZW5pc19rZWxhbWluIjoiTCIsImFsYW1hdCI6IkthbXB1bmcgU3VndSBUYW11LCBLZWwuIE1la2FyamF5YSBSVC4wMDcgUlcuMjIgTm8uMjciLCJ5bSI6ImFrYWNhcmFrYUBnbWFpbC5jb20iLCJza3lwZSI6IiIsIm5vX3RlbCI6IjAyMTg3MTAwMDEiLCJhcHByb3ZhbF9wZW5nZ3VuYSI6IjEiLCJhX2FrdGlmIjoiMSIsInRnbF9nYW50aV9wd2QiOiIyMDIzLTA5LTI2VDE3OjAwOjAwLjAwMFoiLCJpZF9zZG1fcGVuZ2d1bmEiOm51bGwsImlkX3BkX3BlbmdndW5hIjpudWxsLCJpZF93aWwiOiIwMjY2MDMgICIsImxhc3RfdXBkYXRlIjoiMjAyMy0xMS0xMVQwOTowOTo1Mi4wNTBaIiwic29mdF9kZWxldGUiOiIwIiwibGFzdF9zeW5jIjoiMjAyNC0wMy0xNFQwNTo1NjoyNC41MDJaIiwiaWRfdXBkYXRlciI6ImQ4YmZhYTdhLTQyNWMtNDM5Mi1iMmQ5LWZmZTBkOGNlM2MzYSIsImNzZiI6IjAiLCJ0b2tlbl9yZWciOm51bGwsImphYmF0YW4iOm51bGwsInRnbF9jcmVhdGUiOiIxOTY5LTEyLTMxVDE3OjAwOjAwLjAwMFoiLCJuaWsiOm51bGwsInNhbHQiOm51bGwsImlkX3BlcmFuIjozLCJubV9wZXJhbiI6IkFkbWluIFBUIiwiaWRfc3AiOiJhYzQxZGYyYS04MWI4LTQ5NjMtYmIxOS1mNjc2NjM2Y2M0ODciLCJpZF9zbXQiOiIyMDIzMiIsImlhdCI6MTcxMDQyMTg5MSwiZXhwIjoxNzEwNDIzNjkxfQ.RPVSckceXQHPed9Tc_fUycalLK_hAAe9OW0HJte8KRo'
    ]
];
