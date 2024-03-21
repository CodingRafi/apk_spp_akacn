<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class PembayaranMhsDetailExport implements FromView, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return $this->data->nama;
    }

    public function view(): View
    {
        $datas = Pembayaran::where('mhs_id', Auth::user()->id)
                    ->where('tahun_semester_id', $this->data->untuk)
                    ->orWhere('tahun_pembayaran_lain', $this->data->untuk)
                    ->get();

        return view('pembayaran.export', compact('datas'));
    }
}
