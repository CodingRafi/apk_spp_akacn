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
            'indexStart' => 7
        ]
    ],

    'max_pertemuan' => 14,

    'neo_feeder' => [
        'USERNAME' => env('USERNAME_NEO_FEEDER'),
        'PASSWORD' => env('PASSWORD_NEO_FEEDER'),
        'KEY_ENCRYPT' => env('KEY_ENCRYPT_NEO_FEEDER'),
        'ID_PT' => env('ID_PT'),
        'USER_ID' => env('USER_ID_NEO_FEEDER'),
    ],

    'peran' => [
        '1' => 'Ketua',
        '2' => 'Anggota',
        '3' => 'Personal'
    ],

    'choice_kuesioner' => [
        '1' => 'Tidak Baik',
        '2' => 'Cukup Baik',
        '3' => 'Baik',
        '4' => 'Sangat Baik',
    ]
];
