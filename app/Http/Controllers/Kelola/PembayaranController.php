<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Exports\PembayaranExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\PembayaranMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class PembayaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_pembayaran', ['only' => ['index', 'data']]);
        $this->middleware('permission:edit_kelola_pembayaran', ['only' => ['show', 'store', 'revisi']]);
    }

    public function index()
    {
        $prodis = DB::table('prodi')->get();
        $tahun_ajarans = DB::table('tahun_ajarans')->get();
        return view('kelola_pembayaran.index', compact('prodis', 'tahun_ajarans'));
    }

    public function data()
    {
        $datas = Pembayaran::select('pembayarans.*')
            ->join('users as b', 'pembayarans.mhs_id', '=', 'b.id')
            ->join('profile_mahasiswas as c', 'c.user_id', '=', 'b.id')
            ->when(request('status'), function ($q) {
                $q->where('pembayarans.status', request('status'));
            })->when(request('prodi'), function ($q) {
                $q->where('c.prodi_id', request('prodi'));
            })->when(request('tahun_ajaran'), function ($q) {
                $q->where('c.tahun_masuk_id', request('tahun_ajaran'));
            })->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kelola_pembayaran')) {
                $options = $options . "<a href='" . route('kelola-pembayaran.pembayaran.show', $data->id) . "' class='btn btn-warning mx-2'>Verifikasi</a>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('nim', function ($datas) {
                return $datas->mahasiswa->login_key;
            })
            ->addColumn('nama_mhs', function ($datas) {
                return $datas->mahasiswa->name;
            })
            ->addColumn('prodi', function ($datas) {
                return $datas->mahasiswa->mahasiswa->prodi->nama;
            })
            ->editColumn('verify_id', function ($datas) {
                return $datas->verify_id ? $datas->verify->name : '';
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->tahun_pembayaran_id) {
            $data->type = DB::table('tahun_pembayaran')
                ->select('semesters.nama')
                ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
                ->join('semesters', 'tahun_semester.semester_id', 'semesters.id')
                ->where('tahun_pembayaran.id', $data->tahun_pembayaran_id)
                ->first();
        } else {
            $mhs = DB::table('profile_mahasiswas')
                ->where('user_id', $data->mhs_id)
                ->first();
            $data->type = DB::table('tahun_pembayaran_lain')
                ->select('pembayaran_lainnyas.nama')
                ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
                ->where('tahun_pembayaran_lain.prodi_id', $mhs->prodi_id)
                ->where('tahun_pembayaran_lain.tahun_ajaran_id', $mhs->tahun_masuk_id)
                ->first();
        }

        return view('kelola_pembayaran.form', compact('data'));
    }

    public function store(Request $request, $pembayaran_id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak'
        ]);

        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->status != 'pengajuan') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan!');
        }

        if (!Auth::user()->ttd) {
            return redirect()->back()->with('error', 'Anda belum set tanda tangan');
        }

        DB::beginTransaction();
        try {
            $data->update([
                'ket_verify' => $request->ket_verify,
                'status' => $request->status,
                'verify_id' => Auth::user()->id,
                'revisi' => $request->revisi == 'true' ? "1" : "0"
            ]);

            Mail::to($data->mahasiswa->email)->send((new PembayaranMail($data, 'mhs')));
            DB::commit();
            return redirect()->route('kelola-pembayaran.pembayaran.index')->with('success', 'Berhasil disimpan!');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function revisi($pembayaran_id)
    {
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

    public function export()
    {
        return Excel::download(new PembayaranExport, 'pembayaran.xlsx');
    }
}
