<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class PembayaranMhsExport implements FromView
{
    public function view(): View
    {
        $datas = DB::table('rekap_pembayaran')
            ->where('user_id', Auth::user()->id)
            ->get();

        return view('pembayaran.export', compact('datas'));
    }
}
