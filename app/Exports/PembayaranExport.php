<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PembayaranExport implements FromView
{
    public function view(): View
    {
        $datas = Pembayaran::select(
            'pembayarans.*',
            'b.email as nim',
            'b.name as nama_mhs',
            'd.nama as prodi',
            'semesters.nama as semester',
            'pembayaran_lainnyas.nama as lainnya',
            'pembayarans.tahun_pembayaran_id',
            'pembayarans.tahun_pembayaran_lain_id',
            'f.nama as tahun_ajaran'
        )
            ->join('users as b', 'pembayarans.mhs_id', '=', 'b.id')
            ->join('profile_mahasiswas as c', 'c.user_id', '=', 'b.id')
            ->join('prodi as d', 'd.id', '=', 'c.prodi_id')
            ->leftJoin('tahun_pembayaran', 'tahun_pembayaran.id', '=', 'pembayarans.tahun_pembayaran_id')
            ->leftJoin('tahun_semester', 'tahun_semester.id', '=', 'tahun_pembayaran.tahun_semester_id')
            ->leftJoin('semesters', 'semesters.id', '=', 'tahun_semester.semester_id')
            ->leftJoin('tahun_pembayaran_lain', 'tahun_pembayaran_lain.id', '=', 'pembayarans.tahun_pembayaran_lain_id')
            ->leftJoin('pembayaran_lainnyas', 'pembayaran_lainnyas.id', '=', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->join('tahun_ajarans as f', 'f.id', '=', 'c.tahun_masuk_id')
            ->when(request('status'), function ($q) {
                $q->where('pembayarans.status', request('status'));
            })->when(request('prodi'), function ($q) {
                $q->where('c.prodi_id', request('prodi'));
            })->when(request('tahun_ajaran'), function ($q) {
                $q->where('c.tahun_masuk_id', request('tahun_ajaran'));
            })->get();

        return view('kelola_pembayaran.export', compact('datas'));
    }
}
