<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
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
        $datas = DB::table('gaji')
            ->select('gaji.tgl_awal', 'gaji.tgl_akhir', 'gaji_user.*')
            ->join('gaji_user', function ($join) {
                $join->on('gaji.id', '=', 'gaji_user.gaji_id')
                    ->where('gaji_user.user_id', Auth::user()->id);
            })
            ->get();

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('gaji.rekap_jadwal', $data->gaji_id) . "' class='btn btn-primary'>Rekap Jadwal</a>";;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('nama', function ($data) {
                return parseDate($data->tgl_awal) . ' - ' . parseDate($data->tgl_akhir);
            })
            ->editColumn('tunjangan', function ($data) {
                return formatRupiah($data->tunjangan);
            })
            ->editColumn('fee_transport', function ($data) {
                return formatRupiah($data->fee_transport);
            })
            ->editColumn('total_fee_transport', function ($data) {
                return formatRupiah($data->total_fee_transport);
            })
            ->editColumn('total', function ($data) {
                return formatRupiah($data->total);
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function rekap_jadwal($gaji_id)
    {
        $gaji = DB::table('gaji')
            ->where('gaji.id', $gaji_id)
            ->first();

        return view('pengajar.gaji.jadwal', compact('gaji'));
    }

    public function rekap_jadwal_data($gaji_id)
    {
        $gaji = DB::table('gaji')
            ->where('gaji.id', $gaji_id)
            ->first();

        $datas = DB::table('jadwal')
            ->select(
                'jadwal.id',
                'jadwal.type',
                'jadwal.presensi_mulai',
                'jadwal.presensi_selesai',
                'jadwal.tgl',
                'jadwal.materi',
                'jadwal.jenis_ujian',
                'matkuls.nama as matkul',
                'jadwal.approved'
            )
            ->where('jadwal.tgl', '>=', $gaji->tgl_awal)
            ->where('jadwal.tgl', '<=', $gaji->tgl_akhir)
            ->where('jadwal.pengajar_id', Auth::user()->id)
            ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->orderBy('jadwal.tgl', 'asc')
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('type', function ($data) {
                return $data->type . '-' . $data->id;
            })
            ->editColumn('matkul', function ($data) {
                return $data->jenis_ujian ? $data->matkul . ' (' . $data->jenis_ujian . ')' : $data->matkul;
            })
            ->addColumn('status', function ($datas) {
                if ($datas->approved == 1) {
                    return '<span class="badge bg-warning text-white">Menunggu Verifikasi</span>';
                } elseif ($datas->approved == 2) {
                    return '<span class="badge bg-success text-white">Disetujui</span>';
                } else {
                    return '<span class="badge bg-danger text-white">Ditolak</span>';
                }
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}
