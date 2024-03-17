<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\{
    Agama,
    AlatTransportasi,
    JenisTinggal,
    Jenjang,
    Kewarganegaraan,
    User,
    TahunAjaran,
    Prodi,
    Pekerjaan,
    Penghasilan,
    Wilayah
};
use App\Exports\UserPembayaranExport;
use Illuminate\Http\Request;
use App\Imports\MahasiswaImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_users', ['only' => ['index', 'data', 'store', 'exportPembayaran', 'printPembayaran']]);
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
        $datas = User::select('users.*')
            ->when($role == 'mahasiswa', function ($q) {
                $q->join('profile_mahasiswas as b', 'users.id', 'b.user_id')
                    ->when(request('prodi') || request('tahun_ajaran') || request('rombel'), function ($q) {
                        $q->where('b.prodi_id', request('prodi'))
                            ->orWhere('b.tahun_masuk_id', request('tahun_ajaran'))
                            ->orWhere('b.rombel_id', request('rombel'));
                    });
            })
            ->role($role)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_users')) {
                if ($role == 'mahasiswa') {
                    $options = $options . "<a href='" . route('kelola-users.potongan.index', ['role' => $role, 'user_id' => $data->id]) . "' class='btn btn-primary mx-2'>Potongan</a>";
                }
                $options = $options . "<a href='" . route('kelola-users.edit', ['role' => $role, 'id' => $data->id]) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            if (auth()->user()->can('delete_users') && $role != 'dosen') {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('kelola-users.' . $role . '.destroy', $data->id) . "`)'>
                                    Hapus
                                </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function create($role)
    {
        $agamas = Agama::all();
        $wilayah = Wilayah::all();
        $return = [
            'agamas' => $agamas,
            'wilayah' => $wilayah
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where('status', '1')->get();
            $kewarganegaraan = Kewarganegaraan::all();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans
            ];
        } elseif ($role == 'asdos') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();
            $return += [
                'dosen' => $dosen
            ];
        }

        return view('users.form', $return);
    }

    public function edit($role, $id)
    {
        $data = User::findOrFail($id);
        $agamas = Agama::all();
        $wilayah = Wilayah::all();
        $return = [
            'agamas' => $agamas,
            'wilayah' => $wilayah,
            'data' => $data
        ];

        if ($role == 'mahasiswa') {
            $tahun_ajarans = TahunAjaran::all();
            $prodis = Prodi::where(function ($q) use ($data) {
                $q->where('status', '1')
                    ->orWhere('id', $data->mahasiswa->prodi_id);
            })->get();
            $kewarganegaraan = Kewarganegaraan::all();
            $jenis_tinggal = JenisTinggal::all();
            $alat_transportasi = AlatTransportasi::all();
            $pekerjaans = Pekerjaan::all();
            $jenjang = Jenjang::all();
            $penghasilans = Penghasilan::all();
            $return += [
                'tahun_ajarans' => $tahun_ajarans,
                'prodis' => $prodis,
                'kewarganegaraan' => $kewarganegaraan,
                'jenis_tinggal' => $jenis_tinggal,
                'alat_transportasi' => $alat_transportasi,
                'pekerjaans' => $pekerjaans,
                'jenjang' => $jenjang,
                'penghasilans' => $penghasilans
            ];
        } else if ($role == 'asdos') {
            $dosen = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens as b', 'users.id', 'b.user_id')
                ->where('b.status', '1')
                ->get();
            $return += [
                'dosen' => $dosen
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

        return Excel::download(new UserPembayaranExport($role), 'pembayaran.xlsx');
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
}
