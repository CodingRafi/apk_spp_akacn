<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\{
    Agama,
    AlatTransportasi,
    JalurMasuk,
    JenisDaftar,
    JenisKelas,
    JenisKeluar,
    JenisPembiayaan,
    JenisTinggal,
    Jenjang,
    Kewarganegaraan,
    User,
    TahunAjaran,
    Prodi,
    Pekerjaan,
    Penghasilan,
};
use App\Exports\UserPembayaranExport;
use Illuminate\Http\Request;
use App\Imports\MahasiswaImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_users', ['only' => [
            'index', 'data', 'store',
            'exportPembayaran', 'printPembayaran',
            'show'
        ]]);
        $this->middleware('permission:add_users', ['only' => ['create', 'store', 'import', 'saveImport']]);
        $this->middleware('permission:edit_users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_users', ['only' => ['destroy']]);
    }

    public function index($role)
    {
        $return = [];
        if ($role == 'mahasiswa') {
            $prodis = DB::table('prodi')->get();
            $tahun_ajarans = DB::table('tahun_ajarans')->get();
            $rombels = DB::table('rombels')->get();
            $return = [
                'prodis' => $prodis,
                'tahun_ajarans' => $tahun_ajarans,
                'rombels' => $rombels
            ];
        }
        return view('users.index', $return);
    }

    public function data($role)
    {
        if ($role != 'mahasiswa' || ($role == 'mahasiswa' && request('prodi') && request('tahun_ajaran'))) {
            $datas = User::select('users.*')
                ->when($role == 'mahasiswa', function ($q) {
                    $q->join('profile_mahasiswas as b', 'users.id', 'b.user_id')
                        ->when(request('prodi') || request('tahun_ajaran'), function ($q) {
                            $q->where('b.prodi_id', request('prodi'))
                                ->where('b.tahun_masuk_id', request('tahun_ajaran'));
                        })
                        ->when(request('rombel'), function($q){
                            $q->where('b.rombel_id', request('rombel'));
                        });
                })
                ->role($role)
                ->get();
        }else{
            $datas = [];
        }

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_users') && $role == 'mahasiswa' && $data->mahasiswa->sync_neo_feeder == 0) {
                $options .= '<button class="btn btn-info m-1" type="button" onclick="sendDataMhsToNeoFeeder(' . $data->id . ', `'. $data->id_neo_feeder .'`)">Kirim Neo Feeder</button>';
            }

            $options = $options . "<a href='" . route('kelola-users.show', ['role' => $role, 'id' => $data->id]) . "' class='btn btn-primary m-1'>Detail</a>";


            if (auth()->user()->can('edit_users') && ($role != 'dosen' || ($role == 'dosen' && $data->dosen->source == 'app'))) {
                $options = $options . "<a href='" . route('kelola-users.edit', ['role' => $role, 'id' => $data->id]) . "' class='btn btn-warning m-1'>Edit</a>";
            }

            if (auth()->user()->can('delete_users') && ($role != 'dosen' || ($role == 'dosen' && $data->dosen->source == 'app'))) {
                $options = $options . "<button class='btn btn-danger m-1' onclick='deleteData(`" . route('kelola-users.' . $role . '.destroy', $data->id) . "`)'>
                                    Hapus
                                </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('sync_neo_feeder', function ($data) {
                if (request('role') == 'mahasiswa') {
                    return $data->mahasiswa->sync_neo_feeder ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
                }

                return '';
            })
            ->rawColumns(['options', 'sync_neo_feeder'])
            ->make(true);
    }

    public function create($role)
    {
        $agamas = Agama::all();
        $kewarganegaraan = Kewarganegaraan::all();
        $return = [
            'agamas' => $agamas,
            'kewarganegaraan' => $kewarganegaraan
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where('status', '1')->get();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $jenisKelas = JenisKelas::all();
            $jenisPembiayaan = JenisPembiayaan::all();
            $jenisDaftar = JenisDaftar::all();
            $jalurMasuk = JalurMasuk::all();
            $jenisKeluar = JenisKeluar::all();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans,
                'jenisKelas' => $jenisKelas,
                'jenisPembiayaan' => $jenisPembiayaan,
                'jenisDaftar' => $jenisDaftar,
                'jalurMasuk' => $jalurMasuk,
                'jenisKeluar' => $jenisKeluar,
            ];
        } elseif ($role == 'asisten') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();
            $return += [
                'dosen' => $dosen
            ];
        } elseif ($role == 'dosen') {
            $lembagaPengangkat = DB::table('lembaga_pengangkats')
                ->get();
            $pangkatGolongan = DB::table('pangkat_golongans')
                ->get();
            $return += [
                'lembagaPengangkat' => $lembagaPengangkat,
                'pangkatGolongan' => $pangkatGolongan,
            ];
        }

        return view('users.form', $return);
    }

    public function edit($role, $id)
    {
        $data = User::findOrFail($id);
        if (!$data->hasRole($role)) {
            abort(404);
        }

        if ($role == 'dosen' && $data->dosen->source == 'neo_feeder') {
            abort(403);
        }

        $agamas = Agama::all();
        $kewarganegaraan = Kewarganegaraan::all();
        $return = [
            'agamas' => $agamas,
            'data' => $data,
            'kewarganegaraan' => $kewarganegaraan
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where(function ($q) use ($data) {
                $q->where('status', '1')
                    ->orWhere('id', $data->mahasiswa->prodi_id);
            })->get();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $jenisKelas = JenisKelas::all();
            $jenisPembiayaan = JenisPembiayaan::all();
            $jenisDaftar = JenisDaftar::all();
            $jalurMasuk = JalurMasuk::all();
            $jenisKeluar = JenisKeluar::all();
            $countPembayaran = DB::table('pembayarans')
                ->where('mhs_id', $data->id)
                ->count();
            $countKrs = DB::table('krs')
                ->where('mhs_id', $data->id)
                ->count();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans,
                'jenisKelas' => $jenisKelas,
                'countPembayaran' => $countPembayaran,
                'countKrs' => $countKrs,
                'jenisPembiayaan' => $jenisPembiayaan,
                'jenisDaftar' => $jenisDaftar,
                'jalurMasuk' => $jalurMasuk,
                'jenisKeluar' => $jenisKeluar,
            ];
        } elseif ($role == 'asisten') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();

            $data->dosen_id = $data->asdos_dosen->pluck('id')->toArray();
            
            $return += [
                'dosen' => $dosen,
            ];
        } elseif ($role == 'dosen') {
            $lembagaPengangkat = DB::table('lembaga_pengangkats')
                ->get();
            $pangkatGolongan = DB::table('pangkat_golongans')
                ->get();
            $return += [
                'lembagaPengangkat' => $lembagaPengangkat,
                'pangkatGolongan' => $pangkatGolongan,
            ];
        }

        return view('users.form', $return);
    }

    public function import($role)
    {
        if ($role != 'mahasiswa') {
            abort(404);
        }

        $tahun_ajarans = TahunAjaran::all();
        $prodis = Prodi::all();

        return view('users.import', compact('tahun_ajarans', 'prodis'));
    }

    public function saveImport(Request $request, $role)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
            'tahun_ajaran_id' => 'required',
            'prodi_id' => 'required'
        ]);

        try {
            $file = $request->file('file');
            Excel::import(new MahasiswaImport($request->tahun_ajaran_id, $request->prodi_id), $file);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return redirect()->back()->withErrors($errors)->withInput();
        }

        return redirect()->route('users.index', ['role' => $role])->with('success', 'Berhasil diimport');
    }

    public function exportPembayaran($role)
    {
        if ($role != 'mahasiswa') {
            abort(404);
        }

        if (!request('prodi') || !request('tahun_ajaran')) {
            return redirect()->back()->with('error', 'Harap pilih filter prodi dan tahun Masuk');
        }

        return Excel::download(new UserPembayaranExport, 'pembayaran.xlsx');
    }

    public function printPembayaran($role, $user_id)
    {
        if ($role != 'mahasiswa') {
            abort(404);
        }

        $data = User::findOrFail($user_id);
        $mhs = $data->mahasiswa;
        $semesters = DB::table('semesters as a')
            ->select('a.nama', 'b.nominal', 'b.publish', 'a.id')
            ->join('semester_tahun as b', 'a.id', 'b.semester_id')
            ->where('b.tahun_ajaran_id', $mhs->tahun_ajaran_id)
            ->where('b.prodi_id', $mhs->prodi_id)
            ->get();

        $pembayarans = [];
        foreach ($semesters as $semester) {
            $payments = DB::table('pembayarans')
                ->select('pembayarans.*', 'users.name as nama_verify')
                ->leftJoin('users', 'users.id', 'pembayarans.verify_id')
                ->where('pembayarans.status', 'diterima')
                ->where('pembayarans.mhs_id', $data->id)
                ->where('pembayarans.semester_id', $semester->id)
                ->get()
                ->toArray();
            $total = array_sum(array_column($payments, 'nominal'));

            $potongans = $mhs->potongan()->where('semester_id', $semester->id)->get();

            $pembayarans[$semester->id] = [
                'total' => $total,
                'payments' => $payments,
                'semester' => $semester,
                'potongans' => $potongans
            ];
        }

        return Pdf::loadView('kelola_pembayaran.print', compact('data', 'pembayarans'))->stream('pembayaran.pdf');
    }

    public function show($role, $id)
    {
        $data = User::findOrFail($id);
        if (!$data->hasRole($role)) {
            abort(404);
        }

        if (Auth::user()->hasRole('dosen') && $role != 'mahasiswa') {
            abort(404);
        }

        $agamas = Agama::all();
        $kewarganegaraan = Kewarganegaraan::all();
        $return = [
            'agamas' => $agamas,
            'kewarganegaraan' => $kewarganegaraan,
            'data' => $data
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where(function ($q) use ($data) {
                $q->where('status', '1')
                    ->orWhere('id', $data->mahasiswa->prodi_id);
            })->get();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $jenisKelas = JenisKelas::all();
            $jenisPembiayaan = JenisPembiayaan::all();
            $jenisDaftar = JenisDaftar::all();
            $jalurMasuk = JalurMasuk::all();
            $jenisKeluar = JenisKeluar::all();
            $mhs = $data->mahasiswa;
            $tahun_semester = DB::table('tahun_semester')
                ->select('tahun_semester.id', 'semesters.nama')
                ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
                ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
                ->where('tahun_semester.prodi_id', $mhs->prodi_id)
                ->get();

            $tahunPembayaranLain = DB::table('tahun_pembayaran_lain')
                ->select('tahun_pembayaran_lain.id', 'b.nama')
                ->join('pembayaran_lainnyas as b', 'b.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
                ->where('tahun_pembayaran_lain.prodi_id', $mhs->prodi_id)
                ->where('tahun_pembayaran_lain.tahun_ajaran_id', $mhs->tahun_masuk_id)
                ->get();

            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans,
                'tahun_semester' => $tahun_semester,
                'jenisKelas' => $jenisKelas,
                'tahunPembayaranLain' => $tahunPembayaranLain,
                'jenisPembiayaan' => $jenisPembiayaan,
                'jenisDaftar' => $jenisDaftar,
                'jalurMasuk' => $jalurMasuk,
                'jenisKeluar' => $jenisKeluar,
            ];
        } elseif ($role == 'asisten') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();

            $data->dosen_id = $data->asdos_dosen->pluck('id')->toArray();
                
            $return += [
                'dosen' => $dosen
            ];
        } elseif ($role == 'dosen') {
            $lembagaPengangkat = DB::table('lembaga_pengangkats')
                ->get();
            $pangkatGolongan = DB::table('pangkat_golongans')
                ->get();
            $return += [
                'lembagaPengangkat' => $lembagaPengangkat,
                'pangkatGolongan' => $pangkatGolongan,
            ];
        }

        return view('users.' . $role . '.show', $return);
    }
}
