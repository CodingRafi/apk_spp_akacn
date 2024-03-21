<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserRombelPembayaranExport implements FromView, WithTitle
{
    private $rombelId;
    private $rombelName;

    public function __construct($rombelId, $rombelName)
    {
        $this->rombelId = $rombelId;
        $this->rombelName = $rombelName;
    }

    public function title(): string
    {
        return $this->rombelName;
    }

    public function view(): View
    {
        $datas = User::role('mahasiswa')
                ->select('rekap_pembayaran.*', 'users.name', 'users.login_key as nim')
                ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
                ->join('rekap_pembayaran', 'users.id', '=', 'rekap_pembayaran.user_id')
                ->where('profile_mahasiswas.rombel_id', $this->rombelId)
                ->where('profile_mahasiswas.prodi_id', request('prodi'))
                ->where('profile_mahasiswas.tahun_masuk_id', request('tahun_ajaran'))
                ->get()
                ->groupBy('user_id');

        return view('users.mahasiswa.export', compact('datas'));
    }
}
