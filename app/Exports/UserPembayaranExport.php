<?php

namespace App\Exports;

use DB;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserPembayaranExport implements FromView
{
    private $role;

    public function __construct($role){
        $this->role = $role;
    }

    public function view(): View
    {
        $role = $this->role;
        $datas = User::select('users.*')
                    ->join('mahasiswas as b', 'users.id', 'b.user_id')
                    ->where('b.prodi_id', request('prodi'))
                    ->where('b.tahun_ajaran_id', request('tahun_ajaran'))
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
            foreach ($semesters as $semester) {
                if ($semester->publish) {
                    $total_pembayaran = DB::table('pembayarans')
                                                ->select(DB::raw('sum(nominal) as total'))
                                                ->where('status', 'diterima')
                                                ->where('mhs_id', $data->id)
                                                ->where('semester_id', $semester->id)
                                                ->first();
    
                    $pembayaran[$semester->id] = [
                        'bayar' => $total_pembayaran->total,
                        'harus' => $semester->nominal,
                        'publish' => $semester->publish
                    ];
                }else{
                    $pembayaran[$semester->id] = [
                        'publish' => $semester->publish
                    ];
                }
            }
            $data['pembayaran'] = $pembayaran;
        }

        return view('kelola_pembayaran.export', compact('datas', 'semesters'));
    }
}
