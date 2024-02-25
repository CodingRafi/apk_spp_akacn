<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class UserPembayaranExport implements FromView
{
    private $role;

    public function __construct($role)
    {
        $this->role = $role;
    }

    public function view(): View
    {
        $role = $this->role;
        $datas = User::select('users.*')
            ->join('profile_mahasiswas as b', 'users.id', 'b.user_id')
            ->where('b.prodi_id', request('prodi'))
            ->where('b.tahun_masuk_id', request('tahun_ajaran'))
            ->role($role)
            ->get();

        $semesters = DB::table('semesters')
            ->select('semesters.*', 'semester_tahun.nominal', 'semester_tahun.publish')
            ->join('semester_tahun', 'semesters.id', 'semester_tahun.semester_id')
            ->where('semester_tahun.tahun_ajaran_id', request('tahun_ajaran'))
            ->where('semester_tahun.prodi_id', request('prodi'))
            ->get();

        foreach ($datas as $data) {
            $pembayaran = [];
            $mhs = $data->mahasiswa;
            $potongans = $mhs->potongan;
            foreach ($semesters as $semester) {
                $total_pembayaran = DB::table('pembayarans')
                    ->select(DB::raw('sum(nominal) as total'))
                    ->where('status', 'diterima')
                    ->where('mhs_id', $data->id)
                    ->where('semester_id', $semester->id)
                    ->first();

                $potongan = $potongans->map(function ($q) use ($semester) {
                    if ($q->semester_id == $semester->id) {
                        return $q->nominal;
                    }
                });

                $pembayaran[$semester->id] = [
                    'bayar' => $total_pembayaran->total,
                    'harus' => $semester->nominal,
                    'potongan' => $potongan->sum()
                ];
            }
            $data['pembayaran'] = $pembayaran;
        }

        return view('users.export', compact('datas', 'semesters'));
    }
}
