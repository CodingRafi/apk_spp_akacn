<?php

namespace App\Http\Controllers\Kelola;

use App\Exports\TemplateNilaiExport;
use App\Http\Controllers\Controller;
use App\Imports\NilaiImport;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class NilaiController extends Controller
{
    public function index()
    {
        return view('kelola.nilai.index');
    }

    public function dataTahunAjaran()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $data->options = "<a href='" .
                route(
                    'kelola-nilai.show',
                    ['tahun_ajaran_id' => $data->id]
                ) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function storeNeoFeeder(Request $request)
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

    public function show($tahun_ajaran_id)
    {
        $prodis = DB::table('prodi')->get();
        return view('kelola.nilai.show', compact('prodis'));
    }

    public function getMatkul($tahun_ajaran_id)
    {
        $matkuls = DB::table('tahun_matkul')
            ->select('tahun_matkul.id', 'matkuls.nama', 'matkuls.kode')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->where('tahun_matkul.prodi_id', request('prodi_id'))
            ->get();

        return response()->json([
            'data' => $matkuls
        ], 200);
    }

    public function getRombel($tahun_ajaran_id)
    {
        if (request('tahun_matkul_id') && request('tahun_semester_id')) {
            $rombelMhs = DB::table('users')
                ->select('profile_mahasiswas.rombel_id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->join('krs', 'krs.mhs_id', 'users.id')
                ->join('krs_matkul', function ($join) {
                    $join->on('krs_matkul.krs_id', 'krs.id')
                        ->where('krs_matkul.tahun_matkul_id', request('tahun_matkul_id'));
                })
                ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
                ->where('krs.tahun_semester_id', request('tahun_semester_id'))
                ->distinct('profile_mahasiswas.rombel_id')
                ->get()
                ->pluck('rombel_id')
                ->toArray();

            $datas = DB::table('rombels')
                ->whereIn('rombels.id', $rombelMhs)
                ->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $data->options = "<a href='" . route(
                'kelola-nilai.detailRombel',
                [
                    'tahun_ajaran_id' => $tahun_ajaran_id,
                    'rombel_id' => $data->id,
                    'tahun_semester_id' => request('tahun_semester_id'),
                    'tahun_matkul_id' => request('tahun_matkul_id')
                ]
            ) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function detailRombel($tahun_ajaran_id, $rombel_id)
    {
        $rombel = DB::table('rombels')
            ->where('id', $rombel_id)
            ->first();

        $mutu = DB::table('mutu')
            ->where('status', '1')
            ->where('prodi_id', $rombel->prodi_id)
            ->get();

        return view('kelola.nilai.mhs', compact('mutu'));
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

    public function dataMhs($tahun_ajaran_id, $rombel_id, $tahun_semester_id, $tahun_matkul_id)
    {
        $datas = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.login_key',
                'mhs_nilai.presensi',
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
                'mhs_nilai.send_neo_feeder'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('krs', 'krs.mhs_id', 'users.id')
            ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_semester_id, $tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id)
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();

        foreach ($datas as $data) {
            $data->options = " <button class='btn btn-warning'
            onclick='editForm(`" .
                route(
                    'kelola-nilai.getNilai',
                    [
                        'tahun_semester_id' => $tahun_semester_id,
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
                'presensi' => $request->presensi,
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

    public function downloadTemplate()
    {
        return Excel::download(new TemplateNilaiExport, 'template.xlsx');
    }

    public function importNilai($tahun_ajaran_id, $rombel_id, $tahun_semester_id, $tahun_matkul_id)
    {
        $matkul = DB::table('tahun_matkul')
            ->select('matkuls.sks_mata_kuliah')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.id', request('tahun_matkul_id'))
            ->first();

        $mutu = DB::table('mutu')
            ->where('status', '1')
            ->get();

        try {
            Excel::import(new NilaiImport(
                $matkul,
                $mutu,
                $tahun_semester_id,
                $tahun_matkul_id
            ), request()->file('file'));

            return response()->json([
                'message' => 'Berhasil disimpan'
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function getDataNilai($tahun_ajaran_id, $rombel_id, $tahun_semester_id, $tahun_matkul_id)
    {
        $datas = DB::table('users')
            ->select(
                'profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa',
                'krs_matkul.id_kelas_kuliah_neo_feeder',
                'mhs_nilai.nilai_akhir as nilai_angka',
                'mhs_nilai.nilai_mutu as nilai_indeks',
                'mutu.nama as nilai_huruf',
                'users.id as mhs_id',
                'krs_matkul.tahun_matkul_id'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('krs', 'krs.mhs_id', 'users.id')
            ->join('krs_matkul', function ($join) use ($tahun_matkul_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mhs_nilai', function ($q) use ($tahun_semester_id, $tahun_matkul_id) {
                $q->on('users.id', 'mhs_nilai.mhs_id')
                    ->where('mhs_nilai.tahun_semester_id', $tahun_semester_id)
                    ->where('mhs_nilai.tahun_matkul_id', $tahun_matkul_id);
            })
            ->leftJoin('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->where('mhs_nilai.publish', '1')
            ->get();

        return response()->json($datas, 200);
    }

    public function updateNeoFeeder(Request $request){
        foreach (json_decode($request->data) as $data) {
            DB::table('mhs_nilai')
                ->where('mhs_id', $data->mhs_id)
                ->where('tahun_matkul_id', $data->tahun_matkul_id)
                ->update([
                    'send_neo_feeder' => '1'
                ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
