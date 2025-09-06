<?php

namespace App\Http\Controllers\Kelola;

use App\Exports\TemplateNilaiExport;
use App\Http\Controllers\Controller;
use App\Imports\NilaiImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class NilaiController extends Controller
{
    public function index()
    {
        $tahunAjarans = DB::table('tahun_ajarans')->get();
        $prodis = DB::table('prodi')->get();

        return view('kelola.nilai.index', compact('prodis', 'tahunAjarans'));
    }

    public function data()
    {
        if (request('prodi_id') && request('tahun_ajaran_id')) {
            $datas = DB::table('krs')
                ->select('tahun_matkul.id', 'matkuls.kode', 'matkuls.nama')
                ->join('krs_matkul', 'krs_matkul.krs_id', '=', 'krs.id')
                ->join('tahun_matkul', 'tahun_matkul.id', '=', 'krs_matkul.tahun_matkul_id')
                ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
                ->when(Auth::user()->hasRole('dosen'), function ($q) {
                    $q->join('tahun_matkul_dosen', function ($q) {
                        $q->on('tahun_matkul_dosen.tahun_matkul_id', '=', 'tahun_matkul.id')
                            ->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
                    });
                })
                ->where('tahun_matkul.prodi_id', request('prodi_id'))
                ->where('tahun_matkul.tahun_ajaran_id', request('tahun_ajaran_id'))
                ->distinct('tahun_matkul.id')
                ->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $data->options = '<a href="' . route(
                'kelola-nilai.show',
                [
                    'tahun_matkul_id' => $data->id,
                ]
            ) . '" class="btn btn-primary">Detail</a>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('matkul', function ($data) {
                return $data->kode . ' - ' . $data->nama;
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_matkul_id)
    {
        $mutu = DB::table('mutu')->get();
        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.nama', 'tahun_matkul.tahun_ajaran_id')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();
        return view('kelola.nilai.show', compact('mutu', 'matkul'));
    }

    public function getNilai($tahun_semester_id, $tahun_matkul_id, $mhs_id)
    {
        $data = DB::table('users')
            ->select('users.name', 'users.login_key', 'mhs_nilai.*')
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_semester_id, $tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id)
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->where('users.id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function publish($tahun_matkul_id)
    {
        DB::table('mhs_nilai')
            ->where('tahun_matkul_id', $tahun_matkul_id)
            ->update([
                'publish' => '1'
            ]);

        return response()->json([
            'message' => 'Berhasil di publish'
        ], 200);
    }

    public function dataMhs($tahun_matkul_id)
    {
        $datas = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mhs_nilai.aktivitas_partisipatif',
                'mhs_nilai.hasil_proyek',
                'mhs_nilai.quizz',
                'mhs_nilai.tugas',
                'mhs_nilai.uts',
                'mhs_nilai.uas',
                'mhs_nilai.nilai_akhir',
                'mhs_nilai.nilai_mutu',
                'mutu.nama as mutu',
                'mhs_nilai.publish',
                'mhs_nilai.jml_sks',
                'mhs_nilai.send_neo_feeder',
                'krs.tahun_semester_id'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('krs', 'krs.mhs_id', 'users.id')
            ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->orderBy('users.login_key', 'asc')
            ->get();

        foreach ($datas as $data) {
            $data->options = " <button class='btn btn-warning'
            onclick='editForm(`" .
                route(
                    'kelola-nilai.getNilai',
                    [
                        'tahun_semester_id' => $data->tahun_semester_id,
                        'tahun_matkul_id' => $tahun_matkul_id,
                        'mhs_id' => $data->id
                    ]
                ) . "`, `Edit Nilai`, `#nilai`)'>
            <i class='ti-pencil'></i>
            Edit
        </button>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('publish', function ($datas) {
                return $datas->publish ? "<i class='bx bx-check text-success'></i>" :
                    "<i class='bx bx-x text-danger'></i>";
            })
            ->editCOlumn('send_neo_feeder', function ($datas) {
                return $datas->send_neo_feeder ? "<i class='bx bx-check text-success'></i>" :
                    "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'publish', 'send_neo_feeder'])
            ->make(true);
    }

    public function store(Request $request, $tahun_semester_id, $tahun_matkul_id, $mhs_id)
    {
        if ($request->mutu_id) {
            $mutu = DB::table('mutu')
                ->where('id', $request->mutu_id)
                ->first();
        }

        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.sks_mata_kuliah')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        DB::table('mhs_nilai')
            ->updateOrInsert([
                'mhs_id' => $mhs_id,
                'tahun_semester_id' => $tahun_semester_id,
                'tahun_matkul_id' => $tahun_matkul_id,
            ], [
                'aktivitas_partisipatif' => $request->aktivitas_partisipatif,
                'hasil_proyek' => $request->hasil_proyek,
                'quizz' => $request->quizz,
                'tugas' => $request->tugas,
                'uts' => $request->uts,
                'uas' => $request->uas,
                'nilai_akhir' => $request->nilai_akhir,
                'mutu_id' => $request->mutu_id,
                'nilai_mutu' => $request->mutu_id ? $mutu->nilai : null,
                'publish' => $request->publish ?? '0',
                'jml_sks' => $matkul->sks_mata_kuliah,
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function downloadTemplate($tahun_matkul_id)
    {
        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.nama')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', $tahun_matkul_id)
            ->first();

        return Excel::download(new TemplateNilaiExport, $matkul->nama . '.xlsx');
    }

    public function importNilai($tahun_matkul_id)
    {
        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.sks_mata_kuliah')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', request('tahun_matkul_id'))
            ->first();

        $mutu = DB::table('mutu')
            ->where('status', '1')
            ->get();

        $krs = DB::table('krs')
            ->select('krs.mhs_id', 'krs.id', 'krs.tahun_semester_id')
            ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->get();

        try {
            Excel::import(new NilaiImport(
                $matkul,
                $mutu,
                $tahun_matkul_id,
                $krs
            ), request()->file('file'));

            return response()->json([
                'message' => 'Berhasil disimpan'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function storeNeoFeeder($tahun_matkul_id)
    {
        try {
            $kelas_kuliah = DB::table('kelas_kuliah')
                ->where('tahun_matkul_id', $tahun_matkul_id)
                ->first();

            if (!$kelas_kuliah) {
                return response()->json([
                    'message' => 'Kelas kuliah belum dikirim ke Neo Feeder'
                ], 400);
            }

            $kelas_kuliah_evaluasi = DB::table('kelas_kuliah_evaluasi')
                ->where('id_kelas_kuliah', $kelas_kuliah->id_kelas_kuliah)
                ->get();

            if (count($kelas_kuliah_evaluasi) != 6) {
                return response()->json([
                    'message' => 'Harap get data evaluasi terlebih dahulu'
                ], 400);
            }

            $datas = DB::table('users')
                ->select(
                    'profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa',
                    'krs_matkul.id_kelas_kuliah_neo_feeder',
                    'mhs_nilai.nilai_akhir as nilai_angka',
                    'mhs_nilai.nilai_mutu as nilai_indeks',
                    'mhs_nilai.aktivitas_partisipatif as nilai_aktivitas_partisipatif',
                    'mhs_nilai.hasil_proyek as nilai_hasil_proyek',
                    'mhs_nilai.quizz as nilai_quiz',
                    'mhs_nilai.tugas as nilai_tugas',
                    'mhs_nilai.uts as nilai_ujian_tengah_semester',
                    'mhs_nilai.uas as nilai_ujian_akhir_semester',
                    'mutu.nama as nilai_huruf',
                    DB::raw("REPLACE(mutu.nilai, '.', ',') as nilai_mutu"),
                    'users.id as mhs_id',
                    'krs_matkul.tahun_matkul_id'
                )
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->join('krs', 'krs.mhs_id', 'users.id')
                ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                    $join->on('krs_matkul.krs_id', 'krs.id')
                        ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
                })
                ->leftJoin('mhs_nilai', function ($q) use ($tahun_matkul_id) {
                    $q->on('users.id', 'mhs_nilai.mhs_id')
                        ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
                })
                ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
                ->where('mhs_nilai.publish', '1')
                ->get();
               
            $dataSend = $datas
                ->map(function ($data) use ($kelas_kuliah_evaluasi, $kelas_kuliah) {
                    return [
                        'id_kls' => $kelas_kuliah->id_kelas_kuliah,
                        'id_komp_eval_nilai_aktivitas_partisipatif' => $kelas_kuliah_evaluasi->firstWhere('nm_jns_eval', 'Aktivitas Partisipatif')->id_komp_eval,
                        'id_komp_eval_nilai_hasil_proyek' => $kelas_kuliah_evaluasi->firstWhere('nm_jns_eval', 'Hasil Proyek')->id_komp_eval,
                        'id_komp_eval_nilai_quiz' => $kelas_kuliah_evaluasi->firstWhere('komponen_evaluasi', 'Quiz')->id_komp_eval,
                        'id_komp_eval_nilai_tugas' => $kelas_kuliah_evaluasi->firstWhere('komponen_evaluasi', 'Tugas')->id_komp_eval,
                        'id_komp_eval_nilai_ujian_akhir_semester' => $kelas_kuliah_evaluasi->firstWhere('komponen_evaluasi', 'Ujian Akhir Semester')->id_komp_eval,
                        'id_komp_eval_nilai_ujian_tengah_semester' => $kelas_kuliah_evaluasi->firstWhere('komponen_evaluasi', 'Ujian Tengah Semester')->id_komp_eval,
                        'id_reg_pd' => $data->neo_feeder_id_registrasi_mahasiswa,
                        'nilai_aktivitas_partisipatif' => $data->nilai_aktivitas_partisipatif ?? 0,
                        'nilai_hasil_proyek' => $data->nilai_hasil_proyek ?? 0,
                        'nilai_angka' => $data->nilai_angka ?? 0,
                        'nilai_angka_masking' => $data->nilai_angka ?? 0,
                        'filter_nilai_huruf' => [
                            'column' => $data->nilai_huruf . " (" . $data->nilai_mutu . ")",
                            'nilai_indeks' => $data->nilai_huruf . "#" . $data->nilai_mutu
                        ],
                        'nilai_huruf' => $data->nilai_huruf ?? 0,
                        'nilai_indeks' => $data->nilai_indeks ?? 0,
                        'nilai_quiz' => $data->nilai_quiz ?? 0,
                        'nilai_tugas' => $data->nilai_tugas ?? 0,
                        'nilai_ujian_akhir_semester' => $data->nilai_ujian_akhir_semester ?? 0,
                        'nilai_ujian_tengah_semester' => $data->nilai_ujian_tengah_semester ?? 0
                    ];
                });

            $dataSend = json_encode($dataSend);

            $token = getTokenNeoFeeder();
            $urlNeoFeeder = getUrlNeoFeeder();

            Http::withHeaders([
                'authorization' => 'Bearer ' . $token,
            ])
                ->withBody($dataSend, 'application/json')
                ->post($urlNeoFeeder . '/ws/nilai/update');

            $mhs_id = $datas->pluck('mhs_id')->toArray();
            DB::table('mhs_nilai')
                ->whereIn('mhs_id', $mhs_id)
                ->update([
                    'send_neo_feeder' => '1'
                ]);

            return response()->json([
                'message' => 'Berhasil dikirim'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function storeGetNeoFeeder(Request $request)
    {
        $dataReq = json_decode($request->data);
        foreach ($dataReq as $data) {
            $mhs = DB::table('users')
                ->select('users.id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $data->id_registrasi_mahasiswa)
                ->where('users.id_neo_feeder', $data->id_mahasiswa)
                ->first();

            $tahunSemester = DB::table('tahun_semester')
                ->where('prodi_id', $data->id_prodi)
                ->where('tahun_ajaran_id', $data->angkatan)
                ->where('semester_id', $data->id_semester)
                ->first();

            if (!$tahunSemester) {
                return response()->json([
                    'message' => 'Tahun semester tidak ditemukan'
                ], 400);
            }

            $tahunMatkul = DB::table('tahun_matkul')
                ->where('prodi_id', $data->id_prodi)
                ->where('tahun_ajaran_id', $data->angkatan)
                ->where('matkul_id', $data->id_matkul)
                ->first();

            if (!$tahunMatkul) {
                return response()->json([
                    'message' => 'Tahun matkul tidak ditemukan'
                ], 400);
            }

            $mutu = DB::table('mutu')
                ->where('nama', $data->nilai_huruf)
                ->first();

            if (!$mutu) {
                return response()->json([
                    'message' => 'Mutu tidak ditemukan'
                ], 400);
            }

            $exists = DB::table('mhs_nilai')
                ->where('mhs_id', $mhs->id)
                ->where('tahun_semester_id', $tahunSemester->id)
                ->where('tahun_matkul_id', $tahunMatkul->id)
                ->exists();

            if (!$exists) {
                DB::table('mhs_nilai')
                    ->insert([
                        'mhs_id' => $mhs->id,
                        'tahun_semester_id' => $tahunSemester->id,
                        'tahun_matkul_id' => $tahunMatkul->id,
                        'jml_sks' => (int) $data->sks_mata_kuliah,
                        'mutu_id' => $mutu->id,
                        'publish' => '1',
                        'nilai_mutu' => $mutu->nilai,
                        'send_neo_feeder' => '1',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}
