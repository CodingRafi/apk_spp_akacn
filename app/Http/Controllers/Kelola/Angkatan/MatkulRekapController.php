<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulRekapController extends Controller
{
    public function index()
    {
        return view('data_master.tahun_ajaran.matkul.rekap');
    }

    public function data($tahun_ajaran_id, $matkul_id)
    {
        $datas = DB::table('krs')
            ->select(DB::raw('distinct krs.tahun_semester_id'), 'semesters.nama as semester')
            ->join('krs_matkul', function ($q) use ($matkul_id) {
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $matkul_id);
            })
            ->join('tahun_semester', 'tahun_semester.id', '=', 'krs.tahun_semester_id')
            ->join('semesters', 'semesters.id', '=', 'tahun_semester.semester_id')
            ->where('krs.status', 'diterima')
            ->get();

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('data-master.tahun-ajaran.matkul.rekap.show', ['id' => $tahun_ajaran_id, 'matkul_id' => $matkul_id, 'tahun_semester_id' => $data->tahun_semester_id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_ajaran_id, $matkul_id, $tahun_semester_id)
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
            ->leftJoin('kelas_kuliah', function ($q) {
                $q->on('kelas_kuliah.tahun_matkul_id', '=', 'tahun_matkul.id')
                    ->where('kelas_kuliah.tahun_semester_id', request('tahun_semester_id'));
            })
            ->where('tahun_matkul.id', $matkul_id)
            ->first();

        $semester = DB::table('tahun_semester')
            ->select('semesters.nama as semester')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        if (!$tahunMatkul || !$semester) {
            abort(404);
        }

        return view('data_master.tahun_ajaran.matkul.show-rekap', compact('prodis', 'tahunMatkul', 'semester'));
    }

    public function getDosen($tahun_ajaran_id, $matkul_id)
    {
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
            ->where('tahun_matkul_dosen.tahun_matkul_id', $matkul_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);
    }

    public function getMhs($tahun_ajaran_id, $matkul_id, $tahun_semester_id)
    {
        $datas = DB::table('krs')
            ->select(
                'users.name',
                'users.login_key',
                'prodi.nama as prodi',
                'profile_mahasiswas.tahun_masuk_id',
                'profile_mahasiswas.jk'
            )
            ->join('krs_matkul', function ($q) use ($matkul_id) {
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $matkul_id);
            })
            ->join('users', 'users.id', '=', 'krs.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->join('prodi', 'prodi.id', '=', 'profile_mahasiswas.prodi_id')
            ->where('krs.status', 'diterima')
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);
    }

    public function update(Request $request, $tahun_ajaran_id, $matkul_id, $tahun_semester_id)
    {
        $request->validate([
            'nama' => 'max:5'
        ]);

        DB::table('kelas_kuliah')
            ->updateOrInsert([
                'tahun_matkul_id' => $matkul_id,
                'tahun_semester_id' => $tahun_semester_id
            ], [
                'nama' => $request->nama,
                'bahasan' => $request->bahasan,
                'tanggal_mulai_efektif' => $request->tanggal_mulai_efektif,
                'tanggal_akhir_efektif' => $request->tanggal_akhir_efektif,
            ]);

        return redirect()->back()->with('success', 'Data Berhasil Di simpan');
    }

    public function getData($tahun_ajaran_id, $matkul_id, $tahun_semester_id)
    {
        $data = DB::table('tahun_matkul')
            ->select(
                'tahun_matkul.prodi_id',
                'tahun_semester.semester_id',
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
            ->leftJoin('kelas_kuliah', function ($q) use ($tahun_semester_id) {
                $q->on('kelas_kuliah.tahun_matkul_id', '=', 'tahun_matkul.id')
                    ->where('kelas_kuliah.tahun_semester_id', $tahun_semester_id);
            })
            ->leftJoin('tahun_semester', 'tahun_semester.id', '=', 'kelas_kuliah.tahun_semester_id')
            ->where('tahun_matkul.id', $matkul_id)
            ->first();

        $mhs = DB::table('krs')
            ->select(
                'users.id as mhs_id',
                'profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa as id_registrasi_mahasiswa'
            )
            ->join('krs_matkul', function ($q) use ($matkul_id) {
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', $matkul_id);
            })
            ->join('users', 'users.id', '=', 'krs.mhs_id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', '=', 'users.id')
            ->where('krs.status', 'diterima')
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();

        $dosen = DB::table('tahun_matkul_dosen')
            ->select(
                'penugasan_dosens.id_registrasi_dosen',
                'tahun_matkul_dosen.sks_substansi_total',
                'tahun_matkul_dosen.rencana_tatap_muka',
                'tahun_matkul_dosen.realisasi_tatap_muka',
                'tahun_matkul_dosen.jenis_evaluasi_id',
            )
            ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
            ->join('penugasan_dosens', function ($q) use ($tahun_ajaran_id) {
                $q->on('penugasan_dosens.id_dosen', 'users.id_neo_feeder')
                    ->where('penugasan_dosens.tahun_ajaran_id', $tahun_ajaran_id);
            })
            ->where('tahun_matkul_dosen.tahun_matkul_id', $matkul_id)
            ->get();

        $data->mahasiswa = $mhs;
        $data->dosen = $dosen;

        return response()->json($data, 200);
    }

    public function updateNeoFeeder(Request $request, $tahun_ajaran_id, $matkul_id, $kelas_kuliah_id)
    {
        if ($request->id_kelas_kuliah) {
            DB::table('kelas_kuliah')
                ->where('id', $kelas_kuliah_id)
                ->update([
                    'id_kelas_kuliah' => $request->id_kelas_kuliah
                ]);
        }

        if ($request->dosen) {
            foreach ($request->dosen as $dosen) {
                DB::table('kelas_kuliah_dosen')
                    ->insertOrIgnore([
                        'id_registrasi_dosen' => $dosen['id_registrasi_dosen'],
                        'id_aktivitas_mengajar' => $dosen['id_aktivitas_mengajar'],
                        'tahun_matkul_id' => $matkul_id,
                        'tahun_semester_id' => $dosen['tahun_semester_id'],
                    ]);
            }
        }

        if ($request->mahasiswa) {
            foreach ($request->mahasiswa as $mhs) {
                DB::table('krs')
                    ->join('krs_matkul', function($q) use($matkul_id){
                        $q->on('krs_matkul.krs_id', '=', 'krs.id')
                            ->where('krs_matkul.tahun_matkul_id', $matkul_id);
                    })
                    ->where('krs.tahun_semester_id', $mhs['tahun_semester_id'])
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
}
