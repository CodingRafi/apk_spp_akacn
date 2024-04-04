<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PresensiController extends Controller
{
    private function getSemesterAktif($tahun_ajaran_id, $prodi_id)
    {
        return DB::table('tahun_semester')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->where('prodi_id', $prodi_id)
            ->where('status', '1')
            ->orderBy('id', 'desc')
            ->first();
    }

    public function index()
    {
        return view('kelola.presensi.index');
    }

    public function getTahunAjaran()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $options = "<a href='" . route('kelola-presensi.rekap.index', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Rekap</a>";
            $options .= "<a href='" . route('kelola-presensi.presensi.show', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Jadwal</a>";
            if (Auth::user()->hasRole('dosen')) {
                $options .= "<a href='" . route('kelola-presensi.berita-acara.index', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Berita Acara</a>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function getMateri($tahun_ajaran_id, $tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_ajaran_id, $tahun_matkul->prodi_id);

        if (!$getTahunSemesterAktif) {
            return response()->json([
                'message' => 'Tidak ada semester aktif'
            ], 400);
        }

        $data = DB::table('tahun_matkul')
            ->select('matkul_materi.*')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->join('matkul_materi', 'matkul_materi.matkul_id', '=', 'matkuls.id')
            ->leftJoin('jadwal', function ($q) use ($getTahunSemesterAktif) {
                $q->on('jadwal.materi_id', 'matkul_materi.id')
                    ->where('jadwal.tahun_semester_id', $getTahunSemesterAktif->id);
            })
            ->whereNull('jadwal.id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->get();
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getPengajar($tahun_ajaran_id, $tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('dosen_id')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->where('id', $tahun_matkul_id)
            ->first();

        if (!$tahun_matkul) {
            return response()->json([
                'message' => 'Tidak ada tahun matkul'
            ], 400);
        }

        $asdos = DB::table('users')
            ->select('users.*')
            ->join('profile_asdos', 'users.id', '=', 'profile_asdos.user_id')
            ->where('profile_asdos.dosen_id', $tahun_matkul->dosen_id)
            ->get();

        return response()->json([
            'data' => $asdos
        ], 200);
    }

    public function getPengawas()
    {
        $user = User::role(['dosen', 'asdos'])->get();

        return response()->json([
            'data' => $user
        ], 200);
    }

    public function getTotalPelajaran($tahun_ajaran_id, $tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $semesterAktif = $this->getSemesterAktif($tahun_ajaran_id, $tahun_matkul->prodi_id);

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

    public function getSemester($prodi_id){
        $data = DB::table('tahun_semester')
                ->select('tahun_semester.id', 'semesters.nama')
                ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
                ->where('tahun_semester.prodi_id', $prodi_id)
                ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getMatkul($prodi_id){
        $data = DB::table('tahun_matkul')
            ->select('matkuls.nama', 'tahun_matkul.id')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.prodi_id', $prodi_id)
            ->when(Auth::user()->hasRole('dosen'), function ($q) {
                $q->join('tahun_matkul_dosen', 'tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                    ->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function store(Request $request, $tahun_ajaran_id)
    {
        $roleUser = getRole();

        $validate = [
            'tahun_matkul_id' => 'required',
            'kode' => 'required|max:6|min:6',
            'type' => 'required',
            'pengajar_id' => 'required',
            'materi_id' => 'required'
        ];

        $request->validate($validate);

        if ($request->type == 'pertemuan') {
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
        }

        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id')
            ->where('id', $request->tahun_matkul_id)
            ->first();

        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_ajaran_id, $tahun_matkul->prodi_id);

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
                'message' => 'Tidak ada mata kuliah'
            ], 400);
        }

        //? Validasi sudah dibikin belum
        $tgl = $roleUser->name == 'admin' ? $request->tgl : Carbon::now()->format('Y-m-d');
        $cekJadwalHari = DB::table('jadwal')
            ->where('tahun_matkul_id', $request->tahun_matkul_id)
            ->where('tgl', $tgl)
            ->count();

        if ($cekJadwalHari > 0) {
            return response()->json([
                'message' => 'Sudah ada jadwal hari ini'
            ], 400);
        }

        //? Validasi IP
        if ($roleUser->name == 'dosen') {
            //? Validasi hari
            $today = Carbon::now();
            Carbon::setLocale('id');
            $day = $today->translatedFormat('l');

            if ($day != config('services.hari')[$getTahunMatkul->hari]) {
                return response()->json([
                    'message' => 'Sekarang bukan hari ' . config('services.hari')[$getTahunMatkul->hari]
                ], 400);
            }

            if ($getTahunMatkul->cek_ip == '1') {
                $whitelist_ip = DB::table('whitelist_ip')->get()->pluck('ip')->toArray();
                if (!in_array($request->ip(), $whitelist_ip)) {
                    return response()->json([
                        'message' => 'Jaringan anda tidak valid!'
                    ], 400);
                }
            }

            //? Validasi jam
            if ($today->format('H:i') < $getTahunMatkul->jam_mulai || $today->format('H:i') > $getTahunMatkul->jam_akhir) {
                return response()->json([
                    'message' => 'Sekarang bukan waktunya pembelajaran'
                ], 400);
            }
        }

        $materi = DB::table('matkul_materi')
            ->where('id', $request->materi_id)
            ->first();

        $data = [
            'tgl' => $tgl,
            'materi_id' => $request->materi_id,
            'materi' => $materi->materi,
            'tahun_matkul_id' => $request->tahun_matkul_id,
            'tahun_semester_id' => $getTahunSemesterAktif->id,
            'ket' => $request->ket,
            'kode' => $request->kode,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($roleUser->name == 'dosen') {
            $data += [
                'pengajar_id' => Auth::user()->id,
                'presensi_mulai' => now(),
            ];
        } else {
            $data += [
                'pengajar_id' => $request->pengajar_id,
            ];
        }

        DB::table('jadwal')->insert($data);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function show($tahun_ajaran_id)
    {
        $role = getRole();
        $prodis = DB::table('tahun_matkul')
            ->select('prodi.*')
            ->join('prodi', 'prodi.id', 'tahun_matkul.prodi_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->when(Auth::user()->hasRole('dosen'), function ($q) {
                $q->join('tahun_matkul_dosen', 'tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                    ->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
            })
            ->get();

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
            ->when($role->name == 'dosen', function ($query) {
                $query->whereExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('tahun_matkul_dosen')
                        ->whereColumn('tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                        ->where('tahun_matkul_dosen.dosen_id', Auth::id());
                });
            })
            ->get();

        return view('kelola.presensi.showTahunAjaran', compact('prodis', 'role', 'tahunMatkul'));
    }

    public function getJadwal($tahun_ajaran_id)
    {
        $role = getRole();
        $jadwals = DB::table('jadwal')
            ->select('jadwal.*', 'matkuls.nama as matkul', 'matkuls.kode as kode_matkul')
            ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->when(request('prodi_id'), function ($q) {
                $q->where('tahun_matkul.prodi_id', request('prodi_id'));
            })
            ->when(request('tahun_semester_id'), function ($q) {
                $q->where('jadwal.tahun_semester_id', request('tahun_semester_id'));
            })
            ->when(request('tahun_matkul_id'), function ($q) {
                $q->where('jadwal.tahun_matkul_id', request('tahun_matkul_id'));
            })
            ->when($role->name == 'dosen', function ($q) {
                $asdos = DB::table('users')
                    ->select('users.id')
                    ->join('profile_asdos', 'profile_asdos.user_id', 'users.id')
                    ->where('profile_asdos.dosen_id', Auth::user()->id)
                    ->get()
                    ->pluck('id')
                    ->toArray();

                $q->where('pengajar_id', Auth::user()->id)
                    ->orWhere('pengajar_id', $asdos);
            })
            ->when($role->name == 'asdos', function ($q) {
                $q->where('pengajar_id', Auth::user()->id);
            })
            ->orderBy('id', 'desc')
            ->get();

        $datas = isset($jadwals) ? $jadwals : [];

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-presensi.presensi.showJadwal', ['tahun_ajaran_id' => $tahun_ajaran_id, 'jadwal_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('tgl', function ($datas) {
                return parseDate($datas->tgl);
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function showJadwal($tahun_ajaran_id, $jadwal_id)
    {
        $data = DB::table('jadwal')
            ->select('jadwal.*', 'matkuls.nama as matkul', 'users.name as pengajar')
            ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->join('users', 'jadwal.pengajar_id', '=', 'users.id')
            ->where('jadwal.id', $jadwal_id)
            ->first();

        if (!$data) {
            abort(404);
        }

        $rombel = DB::table('tahun_matkul_rombel')
            ->select('rombels.id', 'rombels.nama')
            ->join('rombels', 'rombels.id', '=', 'tahun_matkul_rombel.rombel_id')
            ->where('tahun_matkul_rombel.tahun_matkul_id', $data->tahun_matkul_id)
            ->get();

        return view('kelola.presensi.showJadwal', compact('data', 'rombel'));
    }

    public function updateJadwal(Request $request, $jadwal_id)
    {
        $request->validate([
            'materi' => 'required'
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

    public function mulaiJadwal(Request $request, $jadwal_id)
    {
        $role = getRole();

        if ($role->name != 'asdos') {
            return redirect()->back()->with('error', 'Telah terjadi kesalahan');
        }

        $jadwal = DB::table('jadwal')
            ->select('tahun_matkul.jam_akhir', 'jadwal.presensi_selesai', 'jadwal.presensi_mulai', 'jadwal.tgl', 'tahun_matkul.cek_ip', 'tahun_matkul.jam_mulai', 'tahun_matkul.jam_akhir')
            ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->where('jadwal.id', $jadwal_id)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        if ($jadwal->presensi_mulai) {
            return redirect()->back()->with('error', 'Jadwal sudah dimulai');
        }

        if ($jadwal->cek_ip == '1') {
            $whitelist_ip = DB::table('whitelist_ip')->get()->pluck('ip')->toArray();
            if (!in_array($request->ip(), $whitelist_ip)) {
                return redirect()->back()->with('error', 'Jaringan tidak valid');
            }
        }

        $today = Carbon::now();
        if ($jadwal->tgl != $today->format('Y-m-d')) {
            return redirect()->back()->with('error', 'Bukan tanggal pelajaran');
        }

        if ($today->format('H:i') < $jadwal->jam_mulai || $today->format('H:i') > $jadwal->jam_akhir) {
            return redirect()->back()->with('error', 'Sekarang bukan waktunya pembelajaran');
        }

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'presensi_mulai' => $today
            ]);

        return redirect()->back()->with('success', 'Berhasil disimpan!');
    }

    public function selesaiJadwal(Request $request, $jadwal_id)
    {
        $jadwal = DB::table('jadwal')
            ->select('tahun_matkul.jam_akhir', 'jadwal.presensi_selesai', 'tahun_matkul.cek_ip')
            ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->where('jadwal.id', $jadwal_id)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        if ($jadwal->presensi_selesai) {
            return redirect()->back()->with('error', 'Jadwal sudah diselesaikan');
        }

        if ($jadwal->cek_ip == '1') {
            $whitelist_ip = DB::table('whitelist_ip')->get()->pluck('ip')->toArray();
            if (!in_array($request->ip(), $whitelist_ip)) {
                return redirect()->back()->with('error', 'Jaringan tidak valid');
            }
        }

        $today = Carbon::now();
        if ($today->format('H:i') < $jadwal->jam_akhir) {
            return redirect()->back()->with('error', 'Tidak bisa selesaikan jadwal sebelum jam pelajaran berakhir');
        }

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'presensi_selesai' => $today
            ]);

        return redirect()->back()->with('success', 'Jadwal Berhasil diselesaikan!');
    }

    public function getPresensi($tahun_ajaran_id, $jadwal_id, $rombel_id)
    {
        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status', 'profile_mahasiswas.rombel_id')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function getPresensiMhs($tahun_ajaran_id, $jadwal_id, $rombel_id, $mhs_id)
    {
        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->where('users.id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function updatePresensiMhs(Request $request, $tahun_ajaran_id, $jadwal_id, $rombel_id, $mhs_id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $getJadwal = DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->first();

        if (!$getJadwal) {
            return response()->json([
                'message' => 'Jadwal tidak ditemukan'
            ], 400);
        }

        if (!Auth::user()->hasRole('admin') && $getJadwal->presensi_selesai) {
            return response()->json([
                'message' => 'Jadwal sudah diselesaikan, tidak bisa merubah presensi'
            ], 400);
        }

        $get = DB::table('jadwal_presensi')
            ->where('jadwal_id', $jadwal_id)
            ->where('mhs_id', $mhs_id)
            ->first();

        if ($get) {
            if (Auth::user()->hasRole('admin') || $get->created_id == Auth::user()->id) {
                DB::table('jadwal_presensi')
                    ->where('id', $get->id)
                    ->update([
                        'status' => $request->status,
                        'updated_at' => Carbon::now()
                    ]);
            } else {
                return response()->json([
                    'message' => 'Tidak bisa merubah presensi'
                ], 400);
            }
        } else {
            DB::table('jadwal_presensi')->insert([
                'jadwal_id' => $jadwal_id,
                'mhs_id' => $mhs_id,
                'status' => $request->status,
                'created_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function getJenisUjian($tahun_ajaran_id, $tahun_matkul_id)
    {
        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_ajaran_id);
        $jadwalUjian = DB::table('jadwal')
            ->select('jenis_ujian')
            ->where('type', 'ujian')
            ->where('tahun_semester_id', $getTahunSemesterAktif->id)
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->get()
            ->pluck('jenis_ujian')
            ->toArray();

        $defaultUjian = array_column(config('services.ujian'), 'key');

        return response()->json([
            'data' => array_diff($defaultUjian, $jadwalUjian)
        ], 200);
    }
}
