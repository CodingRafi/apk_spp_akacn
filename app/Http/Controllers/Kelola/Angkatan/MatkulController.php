<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatkulAngkatanRequest;
use App\Models\Kurikulum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function index($tahun_ajaran_id)
    {
        $prodis = DB::table('prodi')->get();
        $ruangs = DB::table('ruangs')->get();
        $dosens = User::role('dosen')
            ->select('users.*')
            ->join('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->where('profile_dosens.status', '1')
            ->get();

        $semester = DB::table('semesters')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->get()
            ->map(function ($data) {
                return "id_semester='{$data->id}'";
            })
            ->implode(' or ');

        return view('data_master.prodi.angkatan.partials.matkul', compact('prodis', 'ruangs', 'dosens', 'semester'));
    }

    public function getKurikulum($tahun_ajaran_id, $prodi_id)
    {
        $kurikulum = Kurikulum::where('prodi_id', $prodi_id)
            ->get();

        return response()->json([
            'data' => $kurikulum
        ], 200);
    }

    public function getMatkul($tahun_ajaran_id, $kurikulum_id)
    {
        $matkuls = Kurikulum::findOrFail($kurikulum_id)
            ->matkul()
            ->with('prodi')
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
            ->select('matkuls.nama as matkul', 'kurikulums.nama as kurikulum', 'matkuls.kode', 'tahun_matkul.id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->leftJoin('kurikulums', 'kurikulums.id', 'tahun_matkul.kurikulum_id')
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->when(request('prodi_id'), function ($q) {
                $q->where('tahun_matkul.prodi_id', request('prodi_id'));
            })
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_matkul')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.tahun-ajaran.matkul.show', ['id' => $tahun_ajaran_id, 'matkul_id' => $data->id]) . "`, `Edit Mata Kuliah`, `#Matkul`, get_kurikulum)'>
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
            DB::table('tahun_matkul')->insert($requestParse);
            $tahun_matkul_id = DB::getPdo()->lastInsertId();

            foreach ($request->dosen_id as $dosen_id) {
                DB::table('tahun_matkul_dosen')->insert([
                    'tahun_matkul_id' => $tahun_matkul_id,
                    'dosen_id' => $dosen_id
                ]);
            }

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

                foreach ($row->mahasiswa as $mhs) {
                    $user = DB::table('users')
                        ->select('users.id')
                        ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                        ->where('profile_mahasiswas.neo_feeder_id_mahasiswa', $mhs->id_mahasiswa)
                        ->where('profile_mahasiswas.neo_feeder_id_registrasi_mahasiswa', $mhs->id_registrasi_mahasiswa)
                        ->first();

                    $tahunSemester = DB::table('tahun_semester')
                        ->where('prodi_id', $row->id_prodi)
                        ->where('tahun_ajaran_id', $tahun_ajaran_id)
                        ->where('semester_id', $row->id_semester)
                        ->first();

                    $krs = DB::table('krs')
                        ->where('mhs_id', $user->id)
                        ->where('krs.tahun_semester_id', $tahunSemester->id)
                        ->first();

                    if ($krs) {
                        DB::table('krs_matkul')->updateOrInsert([
                            'krs_id' => $krs->id,
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
