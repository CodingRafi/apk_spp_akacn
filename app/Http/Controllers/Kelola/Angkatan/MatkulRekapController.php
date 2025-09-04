<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulRekapController extends Controller
{
    public function index()
    {
        $semesters = DB::table('semesters')->get();

        return view('rekap_perkuliahan.index', compact('semesters'));
    }

    public function data()
    {
        $datas = [];

        if (request('semester_id')) {
            $datas = DB::table('krs as k')
                ->join('krs_matkul as km', 'km.krs_id', '=', 'k.id')
                ->join('tahun_matkul as tm', 'tm.id', '=', 'km.tahun_matkul_id')
                ->join('tahun_ajarans as ta', 'ta.id', '=', 'tm.tahun_ajaran_id')
                ->join('matkuls as m', 'm.id', '=', 'tm.matkul_id')
                ->join('tahun_semester as ts', 'ts.id', '=', 'k.tahun_semester_id')
                ->leftJoin('kelas_kuliah as kk', 'kk.tahun_matkul_id', '=', 'tm.id')
                ->where('ts.semester_id', request('semester_id'))
                ->groupBy('m.nama', 'tm.id', 'ta.nama', 'kk.id', 'm.kode')
                ->select('tm.id', 'm.nama as matkul', 'm.kode as kode', 'ta.nama as tahun_ajaran', 'kk.id as kelas_kuliah')
                ->get()
                ->toArray();
        }

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('rekap-perkuliahan.show', ['semester_id' => request('semester_id'), 'tahun_matkul_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('status', function ($datas) {
                return $datas->kelas_kuliah ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['status', 'options'])
            ->make(true);
    }

    public function show($semester, $tahun_matkul_id)
    {
        $prodis = Prodi::all();
        $tahunMatkul = DB::table('tahun_matkul')
            ->select(
                'matkuls.kode',
                'matkuls.nama as matkul',
                'matkuls.sks_mata_kuliah',
                'matkuls.sks_tatap_muka',
                'matkuls.sks_praktek',
                'matkuls.sks_praktek_lapangan',
                'matkuls.sks_simulasi',
                'tahun_matkul.id',
                'tahun_matkul.prodi_id',
                'tahun_matkul.lingkup',
                'tahun_matkul.mode',
                'kelas_kuliah.nama',
                'kelas_kuliah.bahasan',
                'kelas_kuliah.tanggal_mulai_efektif',
                'kelas_kuliah.tanggal_akhir_efektif',
                'kelas_kuliah.id_kelas_kuliah',
            )
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->leftJoin('kelas_kuliah', 'kelas_kuliah.tahun_matkul_id', 'tahun_matkul.id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        $semester = DB::table('tahun_semester')
            ->select('semesters.nama as semester')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('semesters.id', $semester)
            ->first();

        if (!$tahunMatkul || !$semester) {
            abort(404);
        }

        $tahunMatkul->test = null;

        return view('rekap_perkuliahan.show', compact('prodis', 'tahunMatkul', 'semester'));
    }

    public function getDosen($semester_id, $tahun_matkul_id)
    {
        $kelasKuliah = DB::table('kelas_kuliah')
        ->where('tahun_matkul_id', $tahun_matkul_id)
        ->first();

        $semester = DB::table('semesters')
            ->where('id', $semester_id)
            ->first();

        $datas = DB::table('tahun_matkul_dosen')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'tahun_matkul_dosen.sks_substansi_total',
                'tahun_matkul_dosen.rencana_tatap_muka',
                'tahun_matkul_dosen.realisasi_tatap_muka',
                'jenis_evaluasis.nama as jenisEvaluasi'
            )
            ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
            ->leftJoin('jenis_evaluasis', 'tahun_matkul_dosen.jenis_evaluasi_id', 'jenis_evaluasis.id')
            ->join('penugasan_dosens', function ($q) use ($semester) {
                $q->on('penugasan_dosens.id_dosen', 'users.id_neo_feeder')
                    ->where('penugasan_dosens.tahun_ajaran_id', $semester->tahun_ajaran_id);
            })
            ->when($kelasKuliah && $kelasKuliah->id_kelas_kuliah, function ($q) use ($kelasKuliah) {
                $q->leftJoin('kelas_kuliah_dosen', function($q2) use($kelasKuliah) {
                    $q2->on('kelas_kuliah_dosen.id_registrasi_dosen', 'penugasan_dosens.id_registrasi_dosen')
                        ->where('kelas_kuliah_dosen.id_kelas_kuliah', $kelasKuliah->id_kelas_kuliah);
                })->addSelect('kelas_kuliah_dosen.id_aktivitas_mengajar');
            })
            ->where('tahun_matkul_dosen.tahun_matkul_id', $tahun_matkul_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('send_neo_feeder', function ($datas) {
                return isset($datas->id_aktivitas_mengajar) ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['send_neo_feeder'])
            ->make(true);
    }

    public function getMhs($semester_id, $tahun_matkul_id)
    {
        $tahun_semester = DB::table('tahun_semester')
            ->select('id')
            ->where('semester_id', $semester_id)
            ->get()
            ->pluck('id')
            ->toArray();

        $datas = DB::table('krs')
            ->select(
                'users.name',
                'users.login_key',
                'prodi.nama as prodi',
                'profile_mahasiswas.tahun_masuk_id',
                'profile_mahasiswas.jk',
                'krs_matkul.id_kelas_kuliah_neo_feeder'
            )
            ->join('krs_matkul', function ($q) use ($tahun_matkul_id) {
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->join('users', 'users.id', '=', 'krs.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->join('prodi', 'prodi.id', '=', 'profile_mahasiswas.prodi_id')
            ->where('krs.status', 'diterima')
            ->whereIn('krs.tahun_semester_id', $tahun_semester)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('send_neo_feeder', function ($datas) {
                return $datas->id_kelas_kuliah_neo_feeder ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['send_neo_feeder'])
            ->make(true);
    }

    public function update(Request $request, $tahun_matkul_id)
    {
        $request->validate([
            'nama' => 'max:5'
        ]);

        DB::table('kelas_kuliah')
            ->updateOrInsert([
                'tahun_matkul_id' => $tahun_matkul_id
            ], [
                'nama' => $request->nama,
                'bahasan' => $request->bahasan,
                'tanggal_mulai_efektif' => $request->tanggal_mulai_efektif,
                'tanggal_akhir_efektif' => $request->tanggal_akhir_efektif,
            ]);

        return redirect()->back()->with('success', 'Data Berhasil Di simpan');
    }

    public function getData($semester_id, $tahun_matkul_id)
    {
        $data = DB::table('tahun_matkul')
            ->select(
                'tahun_matkul.prodi_id',
                'matkuls.id as matkul_id',
                'kelas_kuliah.nama',
                'tahun_matkul.lingkup',
                'tahun_matkul.mode',
                'kelas_kuliah.bahasan',
                'kelas_kuliah.tanggal_mulai_efektif',
                'kelas_kuliah.tanggal_akhir_efektif',
                'kelas_kuliah.id as kelas_kuliah_id'
            )
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->leftJoin('kelas_kuliah', 'kelas_kuliah.tahun_matkul_id', 'tahun_matkul.id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        $semester = DB::table('semesters')
            ->where('id', $semester_id)
            ->first();

        $kelasKuliah = DB::table('kelas_kuliah')
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->first();

        $dosen = DB::table('tahun_matkul_dosen')
            ->select(
                'penugasan_dosens.id_registrasi_dosen',
                'tahun_matkul_dosen.sks_substansi_total',
                'tahun_matkul_dosen.rencana_tatap_muka',
                'tahun_matkul_dosen.realisasi_tatap_muka',
                'tahun_matkul_dosen.jenis_evaluasi_id',
            )
            ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
            ->join('penugasan_dosens', function ($q) use ($semester) {
                $q->on('penugasan_dosens.id_dosen', 'users.id_neo_feeder')
                    ->where('penugasan_dosens.tahun_ajaran_id', $semester->tahun_ajaran_id);
            })
            ->when($kelasKuliah && $kelasKuliah->id_kelas_kuliah, function ($q) use ($kelasKuliah) {
                $q->leftJoin('kelas_kuliah_dosen', function($q2) use($kelasKuliah) {
                    $q2->on('kelas_kuliah_dosen.id_registrasi_dosen', 'penugasan_dosens.id_registrasi_dosen')
                        ->where('kelas_kuliah_dosen.id_kelas_kuliah', $kelasKuliah->id_kelas_kuliah);
                })->addSelect('kelas_kuliah_dosen.id_aktivitas_mengajar')
                ->whereNull('kelas_kuliah_dosen.id_aktivitas_mengajar');
            })
            ->where('tahun_matkul_dosen.tahun_matkul_id', $tahun_matkul_id)
            ->get();

        $tahun_semester = DB::table('tahun_semester')
            ->select('id')
            ->where('semester_id', $semester_id)
            ->get()
            ->pluck('id')
            ->toArray();

        $mhs = DB::table('krs')
            ->select(
                'users.id as mhs_id',
                'profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa as id_registrasi_mahasiswa'
            )
            ->join('krs_matkul', function ($q) use ($tahun_matkul_id) {
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id)
                    ->whereNull('krs_matkul.id_kelas_kuliah_neo_feeder');
            })
            ->join('users', 'users.id', '=', 'krs.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->where('krs.status', 'diterima')
            ->whereIn('krs.tahun_semester_id', $tahun_semester)
            ->get();

        $data->mahasiswa = $mhs;
        $data->dosen = $dosen;

        return response()->json($data, 200);
    }

    public function updateNeoFeeder(Request $request, $tahun_matkul_id)
    {
        if ($request->id_kelas_kuliah) {
            DB::table('kelas_kuliah')
                ->where('tahun_matkul_id', $tahun_matkul_id)
                ->update([
                    'id_kelas_kuliah' => $request->id_kelas_kuliah
                ]);
        }

        $kelasKuliah = DB::table('kelas_kuliah')
                    ->select('kelas_kuliah.id_kelas_kuliah', )
                    ->where('tahun_matkul_id', $tahun_matkul_id)
                    ->first();

        if ($request->dosen) {
            foreach ($request->dosen as $dosen) {
                DB::table('kelas_kuliah_dosen')
                    ->insertOrIgnore([
                        'id_registrasi_dosen' => $dosen['id_registrasi_dosen'],
                        'id_aktivitas_mengajar' => $dosen['id_aktivitas_mengajar'],
                        'id_kelas_kuliah' => $kelasKuliah->id_kelas_kuliah,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }
        }

        if ($request->mahasiswa) {
            foreach ($request->mahasiswa as $mhs) {
                DB::table('krs')
                    ->join('krs_matkul', function ($q) use ($tahun_matkul_id) {
                        $q->on('krs_matkul.krs_id', '=', 'krs.id')
                            ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
                    })
                    ->where('krs.mhs_id', $mhs['mhs_id'])
                    ->update([
                        'krs_matkul.id_kelas_kuliah_neo_feeder' => $mhs['id_kelas_kuliah']
                    ]);
            }
        }

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    private function sumSKS($krs_id){
        return DB::table('krs_matkul')
                    ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
                    ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
                    ->where('krs_matkul.krs_id', $krs_id)
                    ->sum('matkuls.sks_mata_kuliah');
    }

    public function storeNeoFeeder(Request $request)
    {
        $data = json_decode($request->data);

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                $angkatanUnique = array_unique(array_column($row->mahasiswa, 'angkatan'));
                
                DB::table('tahun_matkul')->updateOrInsert([
                    'prodi_id' => $row->id_prodi,
                    'tahun_ajaran_id' => max($angkatanUnique),
                    'matkul_id' => $row->id_matkul,
                ], [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $get = DB::table('tahun_matkul')
                    ->where('prodi_id', $row->id_prodi)
                    ->where('tahun_ajaran_id', max($angkatanUnique))
                    ->where('matkul_id', $row->id_matkul)
                    ->first();

                //? Dosen
                foreach ($row->dosen as $dosen) {
                    $user = DB::table('users')
                        ->where('id_neo_feeder', $dosen->id_dosen)
                        ->first();

                    DB::table('tahun_matkul_dosen')->updateOrInsert([
                        'dosen_id' => $user->id,
                        'tahun_matkul_id' => $get->id,
                    ], [
                        'sks_substansi_total' => $dosen->sks_substansi_total,
                        'rencana_tatap_muka' => $dosen->rencana_tatap_muka,
                        'realisasi_tatap_muka' => $dosen->realisasi_tatap_muka,
                        'jenis_evaluasi_id' => $dosen->jenis_evaluasi_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $tahunSemester = DB::table('tahun_semester')
                        ->select('id', 'tahun_ajaran_id')
                        ->where('semester_id', $request->semester_id)
                        ->get()
                        ->pluck('id', 'tahun_ajaran_id');

                if (count($tahunSemester) < count($angkatanUnique)) {
                    return response()->json([
                        'message' => 'Tahun semester ada yang kosong'
                    ], 400);
                }

                //? Mahasiswa
                foreach ($row->mahasiswa as $mhs) {
                    $user = DB::table('users')
                        ->select('users.id')
                        ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                        ->where('users.id_neo_feeder', $mhs->id_mahasiswa)
                        ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $mhs->id_registrasi_mahasiswa)
                        ->first();

                    if ($mhs->angkatan != max($angkatanUnique)) {
                        DB::table('tahun_matkul_mhs')->insertOrIgnore([
                            'mhs_id' => $user->id,
                            'tahun_matkul_id' => $get->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $krs = DB::table('krs')
                        ->select('id')
                        ->where('mhs_id', $user->id)
                        ->where('krs.tahun_semester_id', $tahunSemester[$mhs->angkatan])
                        ->first();

                    if ($krs) {
                        $krs = $krs->id;
                        DB::table('krs_matkul')->updateOrInsert([
                            'krs_id' => $krs,
                            'tahun_matkul_id' => $get->id,
                        ], [
                            'id_kelas_kuliah_neo_feeder' => $row->id_kelas_kuliah
                        ]);
                    } else {
                        $krs = DB::table('krs')->insertGetId([
                            'mhs_id' => $user->id,
                            'verify_id' => 1,
                            'tahun_semester_id' => $tahunSemester[$mhs->angkatan],
                            'status' => 'diterima',
                            'lock' => '0',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        DB::table('krs_matkul')->insert([
                            'krs_id' => $krs,
                            'tahun_matkul_id' => $get->id,
                            'id_kelas_kuliah_neo_feeder' => $row->id_kelas_kuliah
                        ]);
                    }

                    $sksDiambil = $this->sumSKS($krs);
                    DB::table('krs')
                        ->where('id', $krs)
                        ->update([
                            'jml_sks_diambil' => $sksDiambil
                        ]);
                }

                //? Kelas kuliah
                DB::table('kelas_kuliah')->updateOrInsert([
                    'id_kelas_kuliah' => $row->id_kelas_kuliah,
                    'tahun_matkul_id' => $get->id,
                ], [
                    'nama' => $row->nama,
                    'bahasan' => $row->bahasan,
                    'tanggal_mulai_efektif' => Carbon::parse($row->tanggal_mulai_efektif)->format('Y-m-d'),
                    'tanggal_akhir_efektif' => Carbon::parse($row->tanggal_akhir_efektif)->format('Y-m-d')
                ]);

                //? Kelas Kuliah Dosen
                foreach ($row->dosen as $dosen) {
                    DB::table('kelas_kuliah_dosen')->insertOrIgnore([
                        'id_registrasi_dosen' => $dosen->id_registrasi_dosen,
                        'id_aktivitas_mengajar' => $dosen->id_aktivitas_mengajar,
                        'id_kelas_kuliah' => $row->id_kelas_kuliah,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
