<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_presensi', ['only' => ['index', 'data', 'show', 'indexTahunMatkul', 'dataTahunMatkul']]);
        $this->middleware('permission:add_kelola_presensi', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_presensi', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_presensi', ['only' => ['destroy']]);
        $this->middleware('permission:jadwal_approval', ['only' => ['destroy']]);
    }

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
        $tahunAjarans = DB::table('tahun_ajarans')->get();

        return view('kelola.jadwal.index', compact('tahunAjarans'));
    }

    public function data()
    {
        $tahun_ajaran_id = request('tahun_ajaran_id');

        if ($tahun_ajaran_id) {
            if (!Auth::user()->hasRole('admin')) {
                $query = "
                    WITH cte_nama_matkul AS (
                        SELECT DISTINCT j.tahun_matkul_id
                        FROM jadwal j
                        JOIN tahun_matkul tm ON tm.id = j.tahun_matkul_id AND tm.tahun_ajaran_id = ?
                        WHERE j.pengajar_id = ?
                    )
                    SELECT
                        s.kode as kode_matkul,
                        s.nama AS matkul,
                        cnm.tahun_matkul_id
                    FROM cte_nama_matkul cnm
                    JOIN tahun_matkul tm ON tm.id = cnm.tahun_matkul_id
                    JOIN matkuls s ON s.id = tm.matkul_id
                ";
                $bindings = [$tahun_ajaran_id, Auth::user()->id];
            } else {
                $query = "
                    SELECT
                        s.kode as kode_matkul,
                        s.nama AS matkul,
                        tm.id AS tahun_matkul_id
                    FROM tahun_matkul tm
                    JOIN matkuls s ON s.id = tm.matkul_id
                    WHERE tm.tahun_ajaran_id = ?
                ";
                $bindings = [$tahun_ajaran_id];
            }

            $jadwals = DB::select($query, $bindings);
        }

        $datas = isset($jadwals) ? $jadwals : [];

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-presensi.jadwal.tahun_matkul.indexTahunMatkul', ['tahun_matkul_id' => $data->tahun_matkul_id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function indexTahunMatkul($tahun_matkul_id)
    {
        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.nama as matkul')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        return view('kelola.jadwal.tahun_matkul.index', compact('matkul'));
    }

    public function dataTahunMatkul($tahun_matkul_id)
    {
        $jadwals = DB::table('jadwal')
            ->select('jadwal.*', 'matkuls.nama as matkul', 'matkuls.kode as kode_matkul')
            ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->when(Auth::user()->hasRole('dosen') || Auth::user()->hasRole('asisten'), function ($q) {
                $q->where('pengajar_id', Auth::user()->id);
            })
            ->where('jadwal.tahun_matkul_id', $tahun_matkul_id)
            ->orderBy('id', 'desc')
            ->get();

        $datas = isset($jadwals) ? $jadwals : [];

        foreach ($datas as $data) {
            $options = "<div class='d-flex'>";
            $options .= "<a href='" . route('kelola-presensi.jadwal.tahun_matkul.show', ['tahun_matkul_id' => $tahun_matkul_id, 'jadwal_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";

            if (auth()->user()->can('edit_kelola_presensi')) {
                $options .= " <button class='btn btn-warning'
                        onclick='editForm(`" . route('kelola-presensi.jadwal.tahun_matkul.edit', ['tahun_matkul_id' => $tahun_matkul_id, 'jadwal_id' => $data->id]) . "`, `Edit Jadwal`, `#jadwal`, editJadwal)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kelola_presensi')) {
                $options .= "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-presensi.jadwal.tahun_matkul.delete', ['tahun_matkul_id' => $tahun_matkul_id, 'jadwal_id' => $data->id]) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $options .= "</div>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('tgl', function ($datas) {
                return parseDate($datas->tgl);
            })
            ->addColumn('status', function ($datas) {
                if (!is_null($datas->approved)) {
                    if ($datas->approved == 1) {
                        return '<span class="badge bg-warning text-white">Menunggu Verifikasi</span>';
                    } elseif ($datas->approved == 2) {
                        return '<span class="badge bg-success text-white">Disetujui</span>';
                    } else {
                        return '<span class="badge bg-danger text-white">Ditolak</span>';
                    }
                }
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function store(Request $request, $tahun_matkul_id)
    {
        $roleUser = getRole();

        $validate = [
            'kode' => 'required|max:6|min:6',
            'type' => 'required',
            'pengajar_id' => 'required',
        ];

        if ($request->type == 'pertemuan') {
            $validate += [
                'materi_id' => 'required',
            ];
        }

        if ($roleUser->name == 'admin') {
            $validate += [
                'tgl' => 'required',
            ];
        }

        $request->validate($validate);

        if ($request->type == 'pertemuan') {
            $cek = $this->getTotalPelajaran($tahun_matkul_id);
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
            ->select('prodi_id', 'tahun_ajaran_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_matkul->tahun_ajaran_id, $tahun_matkul->prodi_id);

        //? Validasi tahun semester
        if (!$getTahunSemesterAktif) {
            return response()->json([
                'message' => 'Tidak ada semester yang aktif'
            ], 400);
        }

        $getTahunMatkul = DB::table('tahun_matkul')
            ->where('id', $tahun_matkul_id)
            ->first();

        if (!$getTahunMatkul) {
            return response()->json([
                'message' => 'Tidak ada mata kuliah'
            ], 400);
        }

        //? Validasi sudah dibikin belum
        $tgl = $roleUser->name == 'admin' ? $request->tgl : Carbon::now()->format('Y-m-d');
        $cekJadwalHari = DB::table('jadwal')
            ->where('tahun_matkul_id', $tahun_matkul_id)
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

            if (!$getTahunMatkul->hari) {
                return response()->json([
                    'message' => 'Hari Belum di set'
                ], 400);
            }

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
            if ($today->format('H:i') < date("H:i", strtotime($getTahunMatkul->jam_mulai)) || $today->format('H:i') > date("H:i", strtotime($getTahunMatkul->jam_akhir))) {
                return response()->json([
                    'message' => 'Sekarang bukan waktunya pembelajaran'
                ], 400);
            }
        }

        $data = [
            'tgl' => $tgl,
            'tahun_matkul_id' => $tahun_matkul_id,
            'tahun_semester_id' => $getTahunSemesterAktif->id,
            'ket' => $request->ket,
            'kode' => $request->kode,
            'created_at' => now(),
            'updated_at' => now()
        ];

        if ($request->type == 'pertemuan') {
            $materi = DB::table('matkul_materi')
                ->where('id', $request->materi_id)
                ->first();

            $data += [
                'materi_id' => $request->materi_id,
                'materi' => $materi->materi,
                'type' => 'pertemuan',
                'jenis_ujian' => null
            ];
        } else {
            $data += [
                'materi_id' => null,
                'materi' => null,
                'type' => 'ujian',
                'jenis_ujian' => $request->jenis
            ];
        }

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

    public function getSemester($tahun_ajaran_id, $prodi_id)
    {
        $data = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getMatkul($tahun_ajaran_id, $prodi_id)
    {
        $data = DB::table('tahun_matkul')
            ->select('matkuls.nama', 'tahun_matkul.id')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.prodi_id', $prodi_id)
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->when(Auth::user()->hasRole('dosen'), function ($q) {
                $q->join('tahun_matkul_dosen', 'tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                    ->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getPelajaran($tahun_ajaran_id, $prodi_id)
    {
        $data = DB::table('tahun_matkul')
            ->select(
                'tahun_matkul.id',
                'matkuls.nama',
                'tahun_matkul.hari',
                'tahun_matkul.jam_mulai',
                'tahun_matkul.jam_akhir',
                'matkuls.kode'
            )
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.prodi_id', $prodi_id)
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->when(Auth::user()->hasRole('dosen'), function ($query) {
                $query->whereExists(function ($subquery) {
                    $subquery->select(DB::raw(1))
                        ->from('tahun_matkul_dosen')
                        ->whereColumn('tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                        ->where('tahun_matkul_dosen.dosen_id', Auth::id());
                });
            })
            ->get()
            ->map(function ($row) {
                $row->label = $row->kode . ' - ' . $row->nama . ' | ' . ($row->hari ? config('services.hari')[$row->hari] : '') . ', ' . $row->jam_mulai . ' - ' . $row->jam_akhir;
                return $row;
            });

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getMateri($tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id', 'tahun_ajaran_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_matkul->tahun_ajaran_id, $tahun_matkul->prodi_id);

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
            ->when(request('except'), function ($q) {
                $q->orWhere('matkul_materi.id', '=', request('except'));
            })
            ->distinct()
            ->get();
            
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getPengajar($tahun_matkul_id)
    {
        $data = DB::select(DB::raw("
            WITH dosen_utama AS (
                SELECT
                    users.id,
                    users.login_key,
                    users.name
                FROM tahun_matkul
                INNER JOIN tahun_matkul_dosen ON tahun_matkul_dosen.tahun_matkul_id = tahun_matkul.id
                INNER JOIN users ON users.id = tahun_matkul_dosen.dosen_id
                WHERE tahun_matkul.id = :tahun_matkul_id
            )
            SELECT * FROM dosen_utama
            UNION
            SELECT
                users.id,
                users.login_key,
                users.name
            FROM dosen_asdos
            JOIN users ON users.id = dosen_asdos.asdos_id
            WHERE dosen_asdos.dosen_id IN (SELECT id FROM dosen_utama)
        "), [
            'tahun_matkul_id' => $tahun_matkul_id,
        ]);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function getPengawas()
    {
        $user = User::role(['dosen', 'asisten'])->get();

        return response()->json([
            'data' => $user
        ], 200);
    }

    public function getTotalPelajaran($tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id', 'tahun_ajaran_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $semesterAktif = $this->getSemesterAktif($tahun_matkul->tahun_ajaran_id, $tahun_matkul->prodi_id);

        if (!$semesterAktif) {
            return response()->json([
                'message' => 'Tidak ada semester aktif'
            ], 400);
        }

        $totalJadwal = DB::table('jadwal')
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->where('tahun_semester_id', $semesterAktif->id)
            ->where('type', 'pertemuan')
            ->count();

        return response()->json([
            'total' => $totalJadwal
        ], 200);
    }

    public function getJenisUjian($tahun_matkul_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('prodi_id', 'tahun_ajaran_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $getTahunSemesterAktif = $this->getSemesterAktif($tahun_matkul->tahun_ajaran_id, $tahun_matkul->prodi_id);
        $jadwalUjian = DB::table('jadwal')
            ->select('jenis_ujian')
            ->where('type', 'ujian')
            ->where('tahun_semester_id', $getTahunSemesterAktif->id)
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->get()
            ->pluck('jenis_ujian');

        $defaultUjian = array_column(config('services.ujian'), 'key');

        if (request('except')) {
            $jadwalUjian = $jadwalUjian->reject(function ($item) {
                return $item === request('except');
            });
        }

        return response()->json([
            'data' => array_diff($defaultUjian, $jadwalUjian->toArray())
        ], 200);
    }

    public function show($tahun_matkul_id, $jadwal_id)
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
            ->where('tahun_matkul_rombel.tahun_matkul_id', $tahun_matkul_id)
            ->get();

        $materi = DB::table('tahun_matkul')
            ->select('matkul_materi.*')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->join('matkul_materi', 'matkul_materi.matkul_id', '=', 'matkuls.id')
            ->leftJoin('jadwal', function ($q) use ($data) {
                $q->on('jadwal.materi_id', 'matkul_materi.id')
                    ->where('jadwal.tahun_semester_id', $data->tahun_semester_id);
            })
            ->where(function ($q) use ($data) {
                $q->whereNull('jadwal.id')
                    ->orWhere('matkul_materi.id', $data->materi_id);
            })
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->get();

        return view('kelola.jadwal.show', compact('data', 'rombel', 'materi'));
    }

    public function getPresensi($tahun_matkul_id, $jadwal_id, $rombel_id)
    {
        $tahun_matkul = DB::table('tahun_matkul')
            ->select('tahun_ajaran_id')
            ->where('id', $tahun_matkul_id)
            ->first();

        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status', 'profile_mahasiswas.rombel_id')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_matkul->tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function getPresensiMhs($tahun_matkul_id, $jadwal_id, $rombel_id, $mhs_id)
    {
        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('users.id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function updatePresensiMhs(Request $request, $tahun_matkul_id, $jadwal_id, $rombel_id, $mhs_id)
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

    public function edit($tahun_ajaran_id, $jadwal_id)
    {
        $data = DB::table('jadwal as j')
            ->select(
                'j.id',
                'j.type',
                'j.kode',
                'j.pengajar_id',
                'j.materi_id',
                'j.tgl',
                'j.jenis_ujian',
                'j.tahun_matkul_id',
                'j.tahun_semester_id',
                'tahun_matkul.prodi_id',
                'tahun_matkul.tahun_ajaran_id',
            )
            ->join('tahun_matkul', 'tahun_matkul.id', '=', 'j.tahun_matkul_id')
            ->where('j.id', $jadwal_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $tahun_ajaran_id, $jadwal_id)
    {
        $jadwal = DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->first();

        if ($jadwal->presensi_mulai) {
            return response()->json([
                'message' => 'Jadwal sudah dimulai, tidak bisa diedit'
            ], 400);
        }

        $data = [
            'tgl' => $request->tgl,
            'tahun_matkul_id' => $request->tahun_matkul_id,
            'ket' => $request->ket,
            'kode' => $request->kode,
            'updated_at' => now()
        ];

        if ($request->type == 'pertemuan') {
            $materi = DB::table('matkul_materi')
                ->where('id', $request->materi_id)
                ->first();

            $data += [
                'materi_id' => $request->materi_id,
                'materi' => $materi->materi,
                'type' => 'pertemuan',
                'jenis_ujian' => null
            ];
        } else {
            $data += [
                'materi_id' => null,
                'materi' => null,
                'type' => 'ujian',
                'jenis_ujian' => $request->jenis
            ];
        }

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update($data);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function delete($tahun_ajaran_id, $jadwal_id)
    {
        $jadwal = DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->first();

        if ($jadwal->presensi_mulai) {
            return response()->json([
                'message' => 'Jadwal sudah dimulai, tidak bisa dihapus'
            ], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('jadwal')
                ->where('id', $jadwal_id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus',
            ], 400);
        }
    }

    public function updateJadwalMengajar(Request $request, $jadwal_id)
    {
        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'ket' => $request->ket
            ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function mulaiJadwal(Request $request, $jadwal_id)
    {
        $jadwal = DB::table('jadwal')
            ->select('tahun_matkul.jam_akhir', 'jadwal.presensi_selesai', 'jadwal.presensi_mulai', 'jadwal.tgl', 'tahun_matkul.cek_ip', 'tahun_matkul.jam_mulai', 'tahun_matkul.jam_akhir', 'jadwal.type')
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
            return redirect()->back()->with('error', 'Tanggal tidak valid');
        }

        if ($jadwal->type == 'pertemuan') {
            if (($today->format('H:i') < date("H:i", strtotime($jadwal->jam_mulai))) ||
                ($today->format('H:i') > date("H:i", strtotime($jadwal->jam_akhir)))
            ) {
                return redirect()->back()->with('error', 'Waktu tidak valid');
            }
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
            ->select('tahun_matkul.jam_akhir', 'jadwal.presensi_selesai', 'tahun_matkul.cek_ip', 'jadwal.type')
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
        if ($jadwal->type == 'pertemuan' && $today->format('H:i') < date("H:i", strtotime($jadwal->jam_akhir))) {
            return redirect()->back()->with('error', 'Tidak bisa selesaikan jadwal sebelum jam pelajaran berakhir');
        }

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'presensi_selesai' => $today,
                'approved' => 1
            ]);

        return redirect()->back()->with('success', 'Jadwal Berhasil diselesaikan!');
    }

    public function storeApproval(Request $request, $jadwal_id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'approved' => $request->status,
                'ket_approved' => $request->ket_approved
            ]);

        return redirect()->back()->with('success', 'Berhasil disimpan!');
    }

    public function RevisiApproval(Request $request, $jadwal_id)
    {
        DB::table('jadwal')
            ->where('id', $jadwal_id)
            ->update([
                'approved' => 1,
                'ket_approved' => null
            ]);

        return redirect()->back()->with('success', 'Berhasil direvisi!');
    }
}
