<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GajiController extends Controller
{
    public function index()
    {
        return view('pengajar.gaji.index');
    }

    public function data()
    {
        $data = DB::table('gaji')
            ->select('gaji.tgl_awal', 'gaji.tgl_akhir', 'gaji_user.*')
            ->join('gaji_user', function ($join) {
                $join->on('gaji.id', '=', 'gaji_user.gaji_id')
                    ->where('gaji_user.user_id', Auth::user()->id);
            })
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama', function($data){
                return parseDate($data->tgl_awal) . ' - ' . parseDate($data->tgl_akhir);
            })
            ->editColumn('tunjangan', function($data){
                return formatRupiah($data->tunjangan);
            })
            ->editColumn('fee_transport', function($data){
                return formatRupiah($data->fee_transport);
            })
            ->editColumn('total_fee_transport', function($data){
                return formatRupiah($data->total_fee_transport);
            })
            ->editColumn('total', function($data){
                return formatRupiah($data->total);
            })
            ->rawColumns(['options'])
            ->make(true);
    }
}
