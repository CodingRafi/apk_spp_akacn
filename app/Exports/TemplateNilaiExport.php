<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class TemplateNilaiExport implements FromView
{
    public function view(): View
    {
        $datas = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('krs', 'krs.mhs_id', 'users.id')
            ->join('krs_matkul', function ($join) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', request('tahun_matkul_id'));
            })
            ->orderBy('users.login_key', 'asc')
            ->get();

        return view('kelola.krs.template', compact('datas'));
    }
}
