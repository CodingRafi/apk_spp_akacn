<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_pembayaran', ['only' => ['index', 'store']]);
    }

    public function index(){
        // $prodis = DB::table('prodi')->get();
        // $tahun_ajarans = DB::table('tahun_ajarans')->get();
        // $categories = ['pending', 'diterima', 'ditolak'];
        // $query = Pembayaran::select('pembayarans.*')
        //             ->select('pembayarans.status', DB::raw('count(pembayarans.id) as total'))
        //             ->join('users as b', 'pembayarans.mhs_id', '=', 'b.id')
        //             ->join('mahasiswas as c', 'c.user_id', '=', 'b.id')
        //             ->when(request('prodi'), function($q){
        //                 $q->where('c.prodi_id', request('prodi'));
        //             })->when(request('tahun_ajaran'), function($q){
        //                 $q->where('c.tahun_ajaran_id', request('tahun_ajaran'));
        //             })
        //             ->groupBy('pembayarans.status')
        //             ->get()
        //             ->pluck('total', 'status')
        //             ->toArray();
                
        // $data = [];
        // foreach ($categories as $kategori) {
        //     $data[] = $query[$kategori] ?? 0;
        // }

        // return view('dashboard', compact('prodis', 'tahun_ajarans', 'data'));
        return view('dashboard');
    }
}
