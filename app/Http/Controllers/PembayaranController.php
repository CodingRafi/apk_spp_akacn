<?php

namespace App\Http\Controllers;

use App\Models\{
    Pembayaran,
    Semester
};
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Auth, DB, DataTables;
use App\Exports\PembayaranMhsExport;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\PembayaranMail;
use Illuminate\Support\Facades\Mail;

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
        return view('pembayaran.index');
    }

    public function data(){
        $mhs = Auth::user()->mahasiswa;
        $data = DB::table('semesters as a')
                        ->select('a.*', 'c.publish', 'c.nominal')
                        ->join('prodi as b', 'a.prodi_id', '=', 'b.id')
                        ->leftJoin('semester_tahun as c', function($join) use($mhs){
                            $join->on('a.id', '=', 'c.semester_id')
                                ->where('c.tahun_ajaran_id', '=', $mhs->tahun_ajaran_id);
                        })
                        ->where('a.prodi_id', $mhs->prodi_id)
                        ->get();
                        
        foreach ($data as $row) {
            if ($row->publish) {
                $row->sudah_dibayar = DB::table('pembayarans')
                                            ->select(DB::raw('sum(nominal) as total'))
                                            ->where('mhs_id', Auth::user()->id)
                                            ->where('semester_id', $row->id)
                                            ->where('status', 'diterima')
                                            ->first()->total;
                                        }
        }
        
        return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('options', function($data){
                                return "<a href='". route('pembayaran.show', $data->id) ."' class='btn btn-info mx-2'>Detail</a>";
                            })
                            ->addColumn('sudah_dibayar', function($data){
                                return $data->publish ? formatRupiah($data->sudah_dibayar ? $data->sudah_dibayar : 0) : '-';
                            })
                            ->addColumn('harus_dibayar', function($data){
                                return $data->publish ? formatRupiah($data->nominal) : '-';
                            })
                            ->addColumn('status', function($data){
                                return $data->publish ? ($data->sudah_dibayar >= $data->nominal) ? '<span class="badge bg-success">Lunas</span>' : '<span class="badge bg-danger">Belum Lunas</span>' : '-';
                            })
                            ->rawColumns(['options', 'status'])
                            ->make(true);
    }

    public function dataPembayaran($semester_id){
        $datas = DB::table('pembayarans')
                        ->where('mhs_id', Auth::user()->id)
                        ->where('semester_id', $semester_id)
                        ->get();
        
        foreach ($datas as $data) {
            $options = '';

            $options = $options ."<a href='". route('pembayaran.showPembayaran', ['semester_id' => $semester_id, 'pembayaran_id' => $data->id]) ."' class='btn btn-primary mx-2'>Detail</a>";
            
            if ($data->status == 'diterima') {
                $options = $options ."<a href='". route('pembayaran.print', ['semester_id' => $semester_id, 'pembayaran_id' => $data->id]) ."' class='btn btn-info mx-2'>Kwitansi</a>";
            }

            if (auth()->user()->can('edit_pembayaran')) {
                $options = $options ."<a href='". route('pembayaran.edit', ['semester_id' => $semester_id, 'pembayaran_id' => $data->id]) ."' class='btn btn-warning mx-2'>Edit</a>";
            }
            
            if (auth()->user()->can('delete_pembayaran')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`". route('pembayaran.destroy', ['semester_id' => $semester_id, 'pembayaran_id' => $data->id]) ."`)'>
                                    Hapus
                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
                            ->addIndexColumn()
                            ->editColumn('bukti', function($datas){
                                return "<a href='". asset('storage/' . $datas->bukti) ."' class='btn btn-primary' target='_blank'>Lihat</a>";
                            })
                            ->editColumn('tgl_bayar', function($datas){
                                return date("d F Y", strtotime($datas->tgl_bayar));
                            })
                            ->editColumn('nominal', function($datas){
                                return formatRupiah($datas->nominal);
                            })
                            ->rawColumns(['options', 'bukti'])
                            ->make(true);
    }
    
    public function create($semester_id)
    {
        $semester = Semester::where('id', $semester_id)->first();
        $mhs = Auth::user()->mahasiswa;

        //? validasi publish tahun ajaran
        $data = $semester->tahun_ajaran()->where('tahun_ajaran_id', $mhs->tahun_ajaran_id)->first();
        if (!$data || !$data->pivot->publish) {
            abort(404);
        }

        return view('pembayaran.form', compact('semester', 'mhs'));
    }

    public function store(Request $request, $semester_id)
    {
        $semester = Semester::where('id', $semester_id)->first();
        $mhs = Auth::user()->mahasiswa;
        
        //? validasi publish tahun ajaran
        $data = $semester->tahun_ajaran()->where('tahun_ajaran_id', $mhs->tahun_ajaran_id)->first();
        if (!$data || !$data->pivot->publish) {
            abort(404);
        }
        
        $request->validate([
            'tgl_bayar' => 'required', 
            'nominal' => 'required|numeric',
            'bukti' => 'required|file|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $bukti = $request->file('bukti')->store('bukti');
            
            $pembayaran = Pembayaran::create([
                'tgl_bayar' => $request->tgl_bayar,
                'nominal' => $request->nominal,
                'bukti' => $bukti,
                'mhs_id' => Auth::user()->id,
                'semester_id' => $semester_id,
                'status' => 'pengajuan',
                'ket_mhs' => $request->ket_mhs,
                'prodi_id' => $data->pivot->prodi_id
            ]);
            
            Mail::to(Auth::user()->email)->send((new PembayaranMail($pembayaran)));
            DB::commit();
            return redirect()->route('pembayaran.show', ['semester_id' => $semester_id])
                        ->with('success', 'Pembayaran berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }
    }

    public function show($semester_id)
    {
        $semester = Semester::where('id', $semester_id)->first();
        $mhs = Auth::user()->mahasiswa;
        $sudah_dibayar = DB::table('pembayarans')
                        ->select(DB::raw('sum(nominal) as total'))
                        ->where('mhs_id', Auth::user()->id)
                        ->where('semester_id', $semester_id)
                        ->where('status', 'diterima')
                        ->first()->total;
    
        return view('pembayaran.show', compact('semester', 'mhs', 'sudah_dibayar'));
    }

    public function showPembayaran($semester_id, $pembayaran_id){
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        $semester = Semester::where('id', $semester_id)->first();
        $page = 'show';
        return view('pembayaran.form', compact('data', 'semester', 'page'));
    }

    public function edit($semester_id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }
        
        if ($data->status != 'pengajuan' || $data->verify_id) {
            return redirect()->back()->with('error', 'Tidak dapat diedit');
        }

        $semester = Semester::where('id', $semester_id)->first();

        return view('pembayaran.form', compact('data', 'semester'));
    }

    public function update(Request $request, $semester_id, $pembayaran_id)
    {
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id) {
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

        return redirect()->route('pembayaran.show', ['semester_id' => $semester_id])
                    ->with('success', 'Pembayaran berhasil diedit');
    }

    public function destroy($semester_id, $pembayaran_id)
    {
        $pembayaran = Pembayaran::findOrFail($pembayaran_id);

        if ($pembayaran->mhs_id != Auth::user()->id || $pembayaran->semester_id != $semester_id) {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }
        
        if ($pembayaran->status != 'pengajuan' || $pembayaran->verify_id) {
            return redirect()->back()->with('error', 'Tidak dapat dihapus');
        }

        Storage::delete($pembayaran->bukti);
        $pembayaran->delete();

        return redirect()->back()->with('success', 'Berhasil dihapus');
    }

    public function revisi($semester_id, $pembayaran_id){
        $data = Pembayaran::findOrFail($pembayaran_id);

        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id || $data->status == 'pengajuan') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan');
        }

        $semester = Semester::where('id', $semester_id)->first();
        $page = 'form';
        $revisi = true;
        return view('pembayaran.form', compact('data', 'semester', 'page', 'revisi'));
    }

    public function storeRevisi(Request $request, $semester_id, $pembayaran_id){
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

    public function export(){
        $mhs = Auth::user()->mahasiswa;
        $semester = DB::table('semesters')
                            ->where('prodi_id', $mhs->prodi_id)
                            ->get();

        if (count($semester) < 1) {
            return redirect()->back()->with('error', 'Tidak ada semester');
        }

        return Excel::download(new PembayaranMhsExport($semester), 'pembayaran.xlsx');
    }

    public function print($semester_id, $pembayaran_id){
        $data = Pembayaran::findOrFail($pembayaran_id);
        if ($data->mhs_id != Auth::user()->id || $data->semester_id != $semester_id || $data->status != 'diterima') {
            abort(404);
        }
        return PDF::loadView('pembayaran.print', compact('data'))->setPaper([0, 0, 600, 480])->stream('kwitansi.pdf');
    }
}
