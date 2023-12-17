<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use DataTables, Auth, DB;
use App\Exports\PembayaranExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\PembayaranMail;
use Illuminate\Support\Facades\Mail;

class PembayaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_pembayaran', ['only' => ['index', 'data']]);
        $this->middleware('permission:edit_kelola_pembayaran', ['only' => ['show', 'store', 'revisi']]);
    }

    public function index(){
        $prodis = DB::table('prodi')->get();
        $tahun_ajarans = DB::table('tahun_ajarans')->get();
        return view('kelola_pembayaran.index', compact('prodis', 'tahun_ajarans'));
    }

    public function data(){
        $datas = Pembayaran::select('pembayarans.*')
                        ->join('users as b', 'pembayarans.mhs_id', '=', 'b.id')
                        ->join('mahasiswas as c', 'c.user_id', '=', 'b.id')
                        ->when(request('status'), function($q){
                            $q->where('pembayarans.status', request('status'));
                        })->when(request('prodi'), function($q){
                            $q->where('c.prodi_id', request('prodi'));
                        })->when(request('tahun_ajaran'), function($q){
                            $q->where('c.tahun_ajaran_id', request('tahun_ajaran'));
                        })->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kelola_pembayaran')) {
                $options = $options ."<a href='". route('kelola.pembayaran.show', $data->id) ."' class='btn btn-warning mx-2'>Verifikasi</a>";
            }
        
            $data->options = $options;
        }

        return DataTables::of($datas)
                            ->addColumn('nim', function($datas){
                                return $datas->mahasiswa->email;
                            })
                            ->addColumn('nama_mhs', function($datas){
                                return $datas->mahasiswa->name;
                            })
                            ->addColumn('prodi', function($datas){
                                return $datas->mahasiswa->mahasiswa->prodi->nama;
                            })
                            ->editColumn('verify_id', function($datas){
                                return $datas->verify_id ? $datas->verify->name : '';
                            })
                            ->addIndexColumn()
                            ->rawColumns(['options'])
                            ->make(true);
    }

    public function show($pembayaran_id){
        $data = Pembayaran::findOrFail($pembayaran_id);
        return view('kelola_pembayaran.form', compact('data'));
    }

    public function store(Request $request, $pembayaran_id){
        $request->validate([
            'status' => 'required|in:diterima,ditolak'
        ]);

        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->status != 'pengajuan') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan!');
        }

        if (!Auth::user()->petugas->ttd) {
            return redirect()->back()->with('error', 'anda belum set tanda tangan');
        }

        DB::beginTransaction();
        try {
            $data->update([
                'ket_verify' => $request->ket_verify,
                'status' => $request->status,
                'verify_id' => Auth::user()->id,
                'revisi' => $request->revisi == 'true' ? "1" : "0"
            ]);
            
            Mail::to($data->mahasiswa->email)->send((new PembayaranMail($data)));
            DB::commit();
            return redirect()->route('kelola.pembayaran.index')->with('success', 'Berhasil disimpan!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

    }

    public function revisi($pembayaran_id){
        $data = Pembayaran::findOrFail($pembayaran_id);
        if ($data->status == 'pengajuan') { 
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan!');
        }
        
        if ($data->verify_id != Auth::user()->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat merevisi pembayaran ini');
        }

        $data->update([
            'ket_verify' => null,
            'status' => 'pengajuan',
            'verify_id' => null
        ]);

        return redirect()->back()->with('success', 'Berhasil direvisi!');
    }

    public function export(){
        return Excel::download(new PembayaranExport, 'pembayaran.xlsx');
    }
}
