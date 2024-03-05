<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PresensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_presensi', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_kelola_presensi', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_presensi', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_presensi', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('dosen.presensi.index');
    }

    public function dataTahunAjaran()
    {
        $datas = DB::table('tahun_matkul')
            ->select('tahun_matkul.tahun_ajaran_id', 'tahun_ajarans.nama')
            ->join('tahun_ajarans', 'tahun_matkul.tahun_ajaran_id', '=', 'tahun_ajarans.id')
            ->where('dosen_id', Auth::user()->id)
            ->distinct('tahun_ajaran_id')
            ->get();

        foreach ($datas as $data) {
            $data->options = '<a href="' . route('kelola-presensi.show', $data->tahun_ajaran_id) . '" 
            class="btn btn-primary btn-sm">Detail</a>';;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function data($tahun_ajaran_id)
    {
        $datas = DB::table('jadwal')
            ->select('jadwal.*')
            ->join('tahun_semester', 'jadwal.tahun_semester_id', '=', 'tahun_semester.id')
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->where('jadwal.pengajar_id', Auth::user()->id)
            ->get();

        foreach ($datas as $data) {
            $options = "<button class='btn btn-info'
                            onclick='editForm(`" . route('kelola-presensi.detailJadwal', ['tahun_ajaran_id' => $tahun_ajaran_id, 'jadwal_id' => $data->id]) . "`, `Detail Jadwal`, `#detailJadwal`, detailJadwal)'>
                            <i class='ti-pencil'></i>
                            Detail
                        </button>";

            $options .= "<button class='btn btn-warning'
                            onclick='editForm(`" . route('kelola-presensi.detailJadwal', ['tahun_ajaran_id' => $tahun_ajaran_id, 'jadwal_id' => $data->id]) . "`, `Edit Jadwal`, `#jadwal`, editJadwal)'>
                            <i class='ti-pencil'></i>
                            Edit
                        </button>";

            $data->options = $options;
        }


        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request, $tahun_ajaran_id)
    {
        $request->validate([
            'tahun_matkul_id' => 'required',
            'materi' => 'required',
            'kode' => 'required|max:6|min:6',
        ]);

        $cek = $this->getTotalPelajaran($tahun_ajaran_id, $request->tahun_matkul_id);
        $statusCodeCek = $cek->getStatusCode();
        $cek = json_decode($cek->getContent());

        //? Validasi jumlah pembelajaran yang sudah terjadi
        if ($statusCodeCek == 200) {
            if ($cek->total >= 14) {
                return response()->json([
                    'message' => 'Maksimal 14 pelajaran'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => $cek->message
            ], 400);
        }

        $getTahunSemesterAktif = DB::table('tahun_semester')
            ->select('id')
            ->where('status', '1')
            ->orderBy('id', 'desc')
            ->first();

        //? Validasi tahun semester
        if (!$getTahunSemesterAktif) {
            return response()->json([
                'message' => 'Tidak ada semester yang aktif'
            ], 400);
        }

        $getTahunMatkul = DB::table('tahun_matkul')
            ->where('id', $request->tahun_matkul_id)
            ->first();

        if (!$getTahunMatkul) {
            return response()->json([
                'message' => 'Tidak ada jadwal mata kuliah'
            ], 400);
        }

        //? Validasi IP
        if ($getTahunMatkul->cek_ip == '1') {
            $whitelist_ip = DB::table('whitelist_ip')->get()->pluck('ip')->toArray();
            if (!in_array($request->ip(), $whitelist_ip)) {
                return response()->json([
                    'message' => 'Jaringan anda tidak valid!'
                ], 400);
            }
        }

        //? Validasi hari
        $today = Carbon::now();
        Carbon::setLocale('id');
        $day = $today->translatedFormat('l');

        if ($day != config('services.hari')[$getTahunMatkul->hari]) {
            return response()->json([
                'message' => 'Sekarang bukan hari ' . config('services.hari')[$getTahunMatkul->hari]
            ], 400);
        }

        //? Validasi jam
        if ($today->format('H:i') < $getTahunMatkul->jam_mulai || $today->format('H:i') > $getTahunMatkul->jam_akhir) {
            return response()->json([
                'message' => 'Sekarang bukan waktunya pembelajaran'
            ], 400);
        }

        DB::table('jadwal')->insert([
            'pengajar_id' => Auth::user()->id,
            'presensi_mulai' => now(),
            'tgl' => now(),
            'materi' => $request->materi,
            'tahun_matkul_id' => $request->tahun_matkul_id,
            'tahun_semester_id' => $getTahunSemesterAktif->id,
            'ket' => $request->ket,
            'kode' => $request->kode,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function getTotalPelajaran($tahun_ajaran_id, $tahun_matkul_id)
    {
        $semesterAktif = DB::table('tahun_semester')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->where('status', '1')
            ->orderBy('id', 'desc')
            ->first();

        if (!$semesterAktif) {
            return response()->json([
                'message' => 'Tidak ada semester aktif'
            ], 400);
        }

        $totalJadwal = DB::table('jadwal')
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->where('tahun_semester_id', $semesterAktif->id)
            ->count();

        return response()->json([
            'total' => $totalJadwal
        ], 200);
    }

    public function show($tahun_ajaran_id)
    {
        $tahunMatkul = DB::table('tahun_matkul')
            ->select(
                'tahun_matkul.id',
                'matkuls.nama',
                'tahun_matkul.hari',
                'tahun_matkul.jam_mulai',
                'tahun_matkul.jam_akhir'
            )
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->where('tahun_matkul.dosen_id', Auth::user()->id)
            ->get()
            ->map(function ($data) {
                $data->rombel = DB::table('tahun_matkul_rombel')
                    ->select('rombels.nama')
                    ->join('rombels', 'rombels.id', '=', 'tahun_matkul_rombel.rombel_id')
                    ->where('tahun_matkul_rombel.tahun_matkul_id', $data->id)
                    ->get()
                    ->pluck('nama')
                    ->implode(',');
                return $data;
            });

        return view('dosen.presensi.show', compact('tahunMatkul'));
    }

    public function detailJadwal($tahun_ajaran_id, $jadwal_id)
    {
        $data = DB::table('jadwal')
            ->where('jadwal.id', $jadwal_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $tahun_ajaran_id, $jadwal_id)
    {
        $request->validate([
            'materi' => 'required',
        ]);

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'materi' => $request->materi,
                'ket' => $request->ket
            ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function dataPresensi($tahun_ajaran_id, $jadwal_id)
    {
        $datas = DB::table('jadwal_presensi')
            ->select('users.name', 'users.login_key', 'rombels.nama as rombel')
            ->join('users', 'users.id', '=', 'jadwal_presensi.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->join('rombels', 'rombels.id', '=', 'profile_mahasiswas.rombel_id')
            ->where('jadwal_presensi.jadwal_id', $jadwal_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);
    }

    public function dataChart($tahun_ajaran_id, $jadwal_id)
    {
        $datas = DB::table('jadwal_presensi')
            ->select(DB::raw('count(mhs_id) as y'), 'rombels.nama as rombel')
            ->join('users', 'users.id', '=', 'jadwal_presensi.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->join('rombels', 'rombels.id', '=', 'profile_mahasiswas.rombel_id')
            ->where('jadwal_presensi.jadwal_id', $jadwal_id)
            ->groupBy('rombel')
            ->get()
            ->map(function ($data) {
                return [
                    'y' => $data->y,
                    'name' => $data->rombel
                ];
            })
            ->toArray();

        return response()->json([
            'data' => $datas
        ], 200);
    }
}
