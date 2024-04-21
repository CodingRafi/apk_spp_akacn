<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatkulAngkatanRequest;
use App\Models\JenisEvaluasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function index($tahun_ajaran_id)
    {
        $prodis = DB::table('prodi')->get();
        $ruangs = DB::table('ruangs')->get();

        $semester = DB::table('semesters')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->get()
            ->map(function ($data) {
                return "id_semester='{$data->id}'";
            })
            ->implode(' or ');

        return view('data_master.tahun_ajaran.matkul.index', compact('prodis', 'ruangs', 'semester'));
    }

    public function getMatkul($tahun_ajaran_id, $prodi_id)
    {
        $matkuls = DB::table('matkuls')
            ->where('prodi_id', $prodi_id)
            ->get();

        return response()->json([
            'data' => $matkuls
        ], 200);
    }

    public function getRombel($tahun_ajaran_id, $prodi_id)
    {
        $rombel = DB::table('rombels')
            ->where('prodi_id', $prodi_id)
            ->get();

        return response()->json([
            'data' => $rombel
        ], 200);
    }

    public function data($tahun_ajaran_id)
    {
        $datas = DB::table('tahun_matkul')
            ->select('matkuls.nama as matkul', 'matkuls.kode', 'tahun_matkul.id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->when(request('prodi_id'), function ($q) {
                $q->where('tahun_matkul.prodi_id', request('prodi_id'));
            })
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_matkul')) {
                $options = $options . "<a href='". route('data-master.tahun-ajaran.matkul.rekap.index', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id]) ."' class='btn btn-info mx-2'>Neo Feeder</a>";
                $options = $options . "<a href='" . route('data-master.tahun-ajaran.matkul.dosen.index', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id]) . "' class='btn btn-primary mx-2'>Set Dosen</a>";
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.tahun-ajaran.matkul.show', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id]) . "`, `Edit Mata Kuliah`, `#Matkul`, editTahunMatkul)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_matkul')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.tahun-ajaran.matkul.destroy', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id]) . "`, () => {tableMatkul.ajax.reload()})' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('rombel', function ($datas) {
                $rombel = DB::table('tahun_matkul_rombel')
                    ->select('rombels.nama as rombel')
                    ->join('rombels', 'rombels.id', 'tahun_matkul_rombel.rombel_id')
                    ->where('tahun_matkul_rombel.tahun_matkul_id', $datas->id)
                    ->get()
                    ->pluck('rombel');
                return implode(', ', $rombel->toArray());
            })
            ->addColumn('dosen', function ($datas) {
                $dosen = DB::table('tahun_matkul_dosen')
                    ->select('users.name as dosen')
                    ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
                    ->where('tahun_matkul_dosen.tahun_matkul_id', $datas->id)
                    ->get()
                    ->pluck('dosen');
                return implode(', ', $dosen->toArray());
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(MatkulAngkatanRequest $request, $tahun_ajaran_id)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token', 'rombel_id', 'ruang_id', 'dosen_id');
            $requestParse['tahun_ajaran_id'] = $tahun_ajaran_id;
            $requestParse['created_at'] = now();
            $requestParse['updated_at'] = now();
            DB::table('tahun_matkul')->insert($requestParse);
            $tahun_matkul_id = DB::getPdo()->lastInsertId();

            foreach ($request->rombel_id as $rombel_id) {
                DB::table('tahun_matkul_rombel')->insert([
                    'tahun_matkul_id' => $tahun_matkul_id,
                    'rombel_id' => $rombel_id
                ]);
            }

            foreach ($request->ruang_id as $ruang_id) {
                DB::table('tahun_matkul_ruang')->insert([
                    'tahun_matkul_id' => $tahun_matkul_id,
                    'ruang_id' => $ruang_id
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($tahun_ajaran_id, $id)
    {
        $data = DB::table('tahun_matkul')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->where('tahun_matkul.id', $id)
            ->first();

        $data->dosen_id = DB::table('tahun_matkul_dosen')
            ->where('tahun_matkul_dosen.tahun_matkul_id', $id)
            ->pluck('dosen_id')
            ->toArray();

        $data->ruang_id = DB::table('tahun_matkul_ruang')
            ->where('tahun_matkul_ruang.tahun_matkul_id', $id)
            ->pluck('ruang_id')
            ->toArray();

        $data->rombel_id = DB::table('tahun_matkul_rombel')
            ->where('tahun_matkul_rombel.tahun_matkul_id', $id)
            ->pluck('rombel_id')
            ->toArray();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MatkulAngkatanRequest $request, $tahun_ajaran_id, $id)
    {
        //? Validasi 
        $dataRombel = DB::table('tahun_matkul_rombel')
            ->where('tahun_matkul_rombel.tahun_matkul_id', $id)
            ->pluck('rombel_id')
            ->toArray();

        if (array_diff($dataRombel, ($request->rombel_id ?? []))) {
            $cek = DB::table('krs_matkul')
                ->where('krs_matkul.tahun_matkul_id', $id)
                ->count();

            if ($cek > 0) {
                return response()->json([
                    'message' => 'Rombel tidak bisa dihapus!'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token', 'ruang_id', 'rombel_id', 'dosen_id');
            DB::table('tahun_matkul')
                ->where('id', $id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                ->update($requestParse);

            DB::table('tahun_matkul_dosen')
                ->where('tahun_matkul_id', $id)
                ->delete();

            foreach (($request->dosen_id ?? []) as $dosen_id) {
                DB::table('tahun_matkul_dosen')->insert([
                    'tahun_matkul_id' => $id,
                    'dosen_id' => $dosen_id
                ]);
            }

            DB::table('tahun_matkul_rombel')
                ->where('tahun_matkul_id', $id)
                ->delete();

            foreach (($request->rombel_id ?? []) as $rombel_id) {
                DB::table('tahun_matkul_rombel')->insert([
                    'tahun_matkul_id' => $id,
                    'rombel_id' => $rombel_id
                ]);
            }

            DB::table('tahun_matkul_ruang')
                ->where('tahun_matkul_id', $id)
                ->delete();

            foreach (($request->ruang_id ?? []) as $ruang_id) {
                DB::table('tahun_matkul_ruang')->insert([
                    'tahun_matkul_id' => $id,
                    'ruang_id' => $ruang_id
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil diubah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy($tahun_ajaran_id, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('tahun_matkul')
                ->where('id', $id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
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

    private function sumSKS($krs_id){
        return DB::table('krs_matkul')
                    ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
                    ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
                    ->where('krs_matkul.krs_id', $krs_id)
                    ->sum('matkuls.sks_mata_kuliah');
    }

    public function storeNeoFeeder(Request $request, $tahun_ajaran_id)
    {
        $data = json_decode($request->data);
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                DB::table('tahun_matkul')->updateOrInsert([
                    'prodi_id' => $row->id_prodi,
                    'tahun_ajaran_id' => $tahun_ajaran_id,
                    'matkul_id' => $row->id_matkul,
                ], [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $get = DB::table('tahun_matkul')
                    ->where('prodi_id', $row->id_prodi)
                    ->where('tahun_ajaran_id', $tahun_ajaran_id)
                    ->where('matkul_id', $row->id_matkul)
                    ->first();

                $tahunSemester = DB::table('tahun_semester')
                    ->where('prodi_id', $row->id_prodi)
                    ->where('tahun_ajaran_id', $tahun_ajaran_id)
                    ->where('semester_id', $row->id_semester)
                    ->first();

                if (!$tahunSemester) {
                    return response()->json([
                        'message' => 'Tahun semester tidak ditemukan'
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

                    $krs = DB::table('krs')
                        ->select('id')
                        ->where('mhs_id', $user->id)
                        ->where('krs.tahun_semester_id', $tahunSemester->id)
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
                            'tahun_semester_id' => $tahunSemester->id,
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

                //? Kelas kuliah
                DB::table('kelas_kuliah')->insert([
                    'id_kelas_kuliah' => $row->id_kelas_kuliah,
                    'tahun_matkul_id' => $get->id,
                    'tahun_semester_id' => $tahunSemester->id,
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
                        'tahun_semester_id' => $tahunSemester->id,
                        'tahun_matkul_id' => $get->id
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ]);
    }
}
