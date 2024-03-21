<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserPembayaranExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $rombel = DB::table('rombels')
                ->select('rombels.*')
                ->join('rombel_tahun_ajarans', 'rombel_tahun_ajarans.rombel_id', '=', 'rombels.id')
                ->where('rombels.prodi_id', request('prodi'))
                ->where('rombel_tahun_ajarans.tahun_masuk_id', request('tahun_ajaran'))
                ->when(request('rombel'), function($q){
                    $q->where('rombels.id', request('rombel'));
                })
                ->get();

        $sheets = [];

        foreach ($rombel as $item) {
            $sheets[] = new UserRombelPembayaranExport($item->id, $item->nama);
        }

        return $sheets;
    }
}
