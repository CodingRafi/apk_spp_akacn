<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\{
    Pembayaran,
    Semester
};
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Exports\PembayaranMhsExport;
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
        $this->middleware('permission:view_pembayaran', ['only' => ['index', 'data', 'dataPembayaran', 'show', 'showPembayaran']]);
        $this->middleware('permission:add_pembayaran', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_pembayaran', ['only' => ['edit', 'update', 'revisi', 'storeRevisi']]);
        $this->middleware('permission:delete_pembayaran', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('mahasiswa.pembayaran.index');
    }

    public function data()
    {
        $datas = Pembayaran::getPembayaranMahasiswa(Auth::user()->id);

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('pembayaran.show', ['type' => $data->type, 'id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('tagihan', function () {
                return 'Rp. 0';
            })
            ->addColumn('status', function () {
                return 'Belum Dibayar';
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function dataPembayaran($type, $id)
    {
        $datas = DB::table('pembayarans')
            ->where('mhs_id', Auth::user()->id)
            ->when($type == 'semester', function ($q) use ($id) {
                $q->where('tahun_semester_id', $id);
            })
            ->when($type == 'lainnya', function ($q) use ($id) {
                $q->where('tahun_pembayaran_lain_id', $id);
            })
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('pembayaran.showPembayaran', ['type' => $type, 'id' => $id, 'pembayaran_id' => $data->id]) . "' class='btn btn-primary mx-2'>Detail</a>";

            if ($data->status == 'diterima') {
                $options = $options . "<a href='" . route('pembayaran.print', ['type' => $type, 'id' => $id, 'pembayaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Kwitansi</a>";
            }

            if (auth()->user()->can('edit_pembayaran')) {
                $options = $options . "<a href='" . route('pembayaran.edit', ['type' => $type, 'id' => $id, 'pembayaran_id' => $data->id]) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            if (auth()->user()->can('delete_pembayaran')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('pembayaran.destroy', ['type' => $type, 'id' => $id, 'pembayaran_id' => $data->id]) . "`)'>
                                    Hapus
                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('bukti', function ($datas) {
                return "<a href='" . asset('storage/' . $datas->bukti) . "' class='btn btn-primary' target='_blank'>Lihat</a>";
            })
            ->editColumn('tgl_bayar', function ($datas) {
                return parseDate($datas->tgl_bayar);
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options', 'bukti'])
            ->make(true);
    }

    public function create($type, $id)
    {
        return view('mahasiswa.pembayaran.form');
    }

    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'tgl_bayar' => 'required',
            'nominal' => 'required|numeric',
            'bukti' => 'required|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $bukti = $request->file('bukti')->store('bukti');
            $requestParse = [
                'tgl_bayar' => $request->tgl_bayar,
                'nominal' => $request->nominal,
                'bukti' => $bukti,
                'mhs_id' => Auth::user()->id,
                'status' => 'pengajuan',
                'ket_mhs' => $request->ket_mhs,
            ];

            $requestParse[($type == 'semester' ? 'tahun_semester_id' : 'tahun_pembayaran_id')] = $id;
            $pembayaran = Pembayaran::create($requestParse);

            $admin = DB::table('users')->find(1);
            Mail::to(Auth::user()->email)->send((new PembayaranMail($pembayaran, 'mhs')));
            Mail::to($admin->email)->send((new PembayaranMail($pembayaran, 'admin')));
            DB::commit();
            return redirect()->route('pembayaran.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Pembayaran berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show($type, $id)
    {
        if ($type == 'semester') {
            $data = DB::table('tahun_pembayaran')
                ->select('tahun_pembayaran.*', 'semesters.nama')
                ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
                ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
                ->where('tahun_pembayaran.tahun_semester_id', $id)
                ->first();
        } else {
        }

        if (!$data) {
            abort(404);
        }

        $potongan = DB::table('potongan_mhs')
            ->select('potongans.nama', 'potongan_tahun_ajaran.type', 'potongan_tahun_ajaran.ket', 'potongan_tahun_ajaran.nominal')
            ->join('potongan_tahun_ajaran', 'potongan_tahun_ajaran.id', 'potongan_mhs.potongan_tahun_ajaran_id')
            ->join('potongans', 'potongans.id', 'potongan_tahun_ajaran.potongan_id')
            ->where('potongan_mhs.mhs_id', Auth::user()->id)
            ->where('potongans.type', $type)
            ->when('type' == 'semester', function ($q) use ($id) {
                return $q->where('potongan_tahun_ajaran.tahun_semester_id', $id);
            })
            ->when('type' == 'lainnya', function ($q) use ($id) {
                return $q->where('potongan_tahun_ajaran.tahun_pembayaran_lain_id', $id);
            })
            ->get();

        $sudah_dibayar = DB::table('pembayarans')
            ->where('mhs_id', Auth::user()->id)
            ->when($type == 'semester', function ($q) use ($id) {
                return $q->where('tahun_semester_id', $id);
            })
            ->when($type == 'lainnya', function ($q) use ($id) {
                return $q->where('tahun_pembayaran_lain_id', $id);
            })
            ->where('status', 'diterima')
            ->sum('nominal');
        return view('mahasiswa.pembayaran.show', compact('data', 'potongan', 'sudah_dibayar'));
    }

    public function showPembayaran($type, $id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);
        if ($data->mhs_id != Auth::user()->id || ($type == 'semester' ? $data->tahun_semester_id != $id : $data->tahun_pembayaran_lain_id != $id)) {
            abort(404);
        }

        $page = 'show';
        return view('mahasiswa.pembayaran.form', compact('data', 'page'));
    }

    public function edit($type, $id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || ($type == 'semester' ? $data->tahun_semester_id != $id : $data->tahun_pembayaran_lain_id != $id)) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        if ($data->status != 'pengajuan' || $data->verify_id) {
            return redirect()->back()->with('error', 'Tidak dapat diedit');
        }

        return view('mahasiswa.pembayaran.form', compact('data'));
    }

    public function update(Request $request, $type, $id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || ($type == 'semester' ? $data->tahun_semester_id != $id : $data->tahun_pembayaran_lain_id != $id)) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        if ($data->status != 'pengajuan' || $data->verify_id) {
            return redirect()->back()->with('error', 'Tidak dapat diedit');
        }

        $request->validate([
            'tgl_bayar' => 'required',
            'nominal' => 'required|numeric',
            'bukti' => 'file|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->bukti) {
            Storage::delete($data->bukti);
            $bukti = $request->file('bukti')->store('bukti');
        }

        $data->update([
            'tgl_bayar' => $request->tgl_bayar,
            'nominal' => $request->nominal,
            'bukti' => isset($bukti) ? $bukti : $data->bukti,
            'ket_mhs' => $request->ket_mhs
        ]);

        return redirect()->route('pembayaran.show', ['type' => $type, 'id' => $id])
            ->with('success', 'Pembayaran berhasil diedit');
    }

    public function destroy($type, $id, $pembayaran_id)
    {
        $pembayaran = Pembayaran::findOrFail($pembayaran_id);

        if ($pembayaran->mhs_id != Auth::user()->id || ($type == 'semester' ? $pembayaran->tahun_semester_id != $id : $pembayaran->tahun_pembayaran_lain_id != $id)) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        if ($pembayaran->status != 'pengajuan' || $pembayaran->verify_id) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus');
        }

        Storage::delete($pembayaran->bukti);
        $pembayaran->delete();

        return redirect()->back()->with('success', 'Berhasil dihapus');
    }

    public function revisi($semester_id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id || $data->status == 'pengajuan') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        $semester = Semester::where('id', $semester_id)->first();
        $page = 'form';
        $revisi = true;
        return view('mahasiswa.pembayaran.form', compact('data', 'semester', 'page', 'revisi'));
    }

    public function storeRevisi(Request $request, $semester_id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id || $data->status == 'pengajuan') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        $request->validate([
            'tgl_bayar' => 'required',
            'nominal' => 'required|numeric',
            'bukti' => 'file|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->bukti) {
            Storage::delete($data->bukti);
            $bukti = $request->file('bukti')->store('bukti');
        }

        $data->update([
            'tgl_bayar' => $request->tgl_bayar,
            'nominal' => $request->nominal,
            'bukti' => isset($bukti) ? $bukti : $data->bukti,
            'ket_mhs' => $request->ket_mhs,
            'status' => 'pengajuan'
        ]);

        return redirect()->route('pembayaran.show', ['semester_id' => $semester_id])
            ->with('success', 'Pembayaran berhasil direvisi');
    }

    public function export()
    {
        $mhs = Auth::user()->mahasiswa;
        $semester = DB::table('semesters')
            ->where('prodi_id', $mhs->prodi_id)
            ->get();

        if (count($semester) < 1) {
            return redirect()->back()->with('error', 'Tidak ada semester');
        }

        return Excel::download(new PembayaranMhsExport($semester), 'pembayaran.xlsx');
    }

    public function print($type, $id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);
        if ($data->mhs_id != Auth::user()->id || ($type == 'semester' ? $data->tahun_semester_id != $id : $data->tahun_pembayaran_lain_id != $id) || $data->status != 'diterima') {
            abort(404);
        }
        return PDF::loadView('mahasiswa.pembayaran.print', compact('data'))->setPaper([0, 0, 600, 550])->stream('kwitansi.pdf');
    }
}