<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:add_kelola_krs', ['only' => ['simpan']]);
        $this->middleware('permission:edit_kelola_krs', ['only' => ['revisi']]);
    }

    public function validateTahunSemester($tahun_semester_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'admin' && $mhs_id == null) {
            abort(404);
        }

        $mhs = DB::table('profile_mahasiswas')->where('user_id', $mhs_id)->first();

        if (!$mhs) {
            abort(404);
        }

        $data = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        if (!$data) {
            return [
                'status' => false,
                'message' => 'Tidak Boleh Mengakses Tahun Semester ini'
            ];
        }

        return [
            'status' => true,
            'data' => $data
        ];
    }

    private function getOrCreateKRS($tahun_semester_id, $mhs_id)
    {
        $krs = DB::table('krs')
            ->where('mhs_id', $mhs_id)
            ->where('tahun_semester_id', $tahun_semester_id)
            ->first();

        if (!$krs) {
            $krsId = DB::table('krs')
                ->insertGetId([
                    'mhs_id' => $mhs_id,
                    'tahun_semester_id' => $tahun_semester_id
                ]);

            $krs = DB::table('krs')
                ->where('id', $krsId)
                ->first();
        }

        return $krs;
    }

    public function simpan($tahun_semester_id, $mhs_id)
    {
        DB::table('krs')
            ->where('mhs_id', $mhs_id)
            ->where('tahun_semester_id', $tahun_semester_id)->update([
                'status' => 'diterima'
            ]);

        return redirect()->back()->with('success', 'Data Berhasil Di simpan');
    }

    public function revisi($tahun_semester_id, $mhs_id)
    {
        DB::table('krs')
            ->where('mhs_id', $mhs_id)
            ->where('tahun_semester_id', $tahun_semester_id)->update([
                'status' => 'pending'
            ]);

        return redirect()->back()->with('success', 'Data Berhasil Di revisi');
    }

    public function dataMatkul($tahun_semester_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'mahasiswa') {
            $mhs_id = Auth::user()->id;
        }

        $validate = $this->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $datas = DB::table('krs')
            ->select(
                'krs_matkul.id',
                'matkuls.kode',
                'matkuls.nama as matkul',
                'matkuls.sks_mata_kuliah',
                'tahun_matkul.id as tahun_matkul_id',
                'tahun_matkul.hari',
                'tahun_matkul.jam_mulai',
                'tahun_matkul.jam_akhir',
                'tahun_ajarans.nama as tahun_ajaran'
            )
            ->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->join('tahun_ajarans', 'tahun_ajarans.id', 'tahun_matkul.tahun_ajaran_id')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();


        foreach ($datas as $data) {
            $options = "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('krs.destroy', ['tahun_semester_id' => $tahun_semester_id, 'mhs_id' => $mhs_id, 'tahun_matkul_id' => $data->tahun_matkul_id]) . "`, () => tableMatkul.ajax.reload())'>
                                                                Hapus
                                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('matkul', function($datas){
                return $datas->matkul . ' ' . $datas->tahun_ajaran;
            })
            ->addColumn('ruang', function ($datas) {
                $ruang = DB::table('ruangs')
                    ->select('ruangs.kapasitas', 'ruangs.nama')
                    ->join('tahun_matkul_ruang', 'ruangs.id', 'tahun_matkul_ruang.ruang_id')
                    ->where('tahun_matkul_ruang.tahun_matkul_id', $datas->tahun_matkul_id)
                    ->get();

                $ruangParse = '';
                foreach ($ruang as $item) {
                    $ruangParse .= $item->nama . ' (Kapasitas: ' . $item->kapasitas . ')<br>';
                }

                return $ruangParse;
            })
            ->addColumn('dosen', function ($datas) {
                $dosen = DB::table('tahun_matkul_dosen')
                    ->select('user.name')
                    ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
                    ->select('users.name')
                    ->where('tahun_matkul_dosen.tahun_matkul_id', $datas->tahun_matkul_id)
                    ->get()
                    ->pluck('name')
                    ->toArray();

                return implode(', ', $dosen);
            })
            ->addColumn('hari', function ($datas) {
                return $datas->hari ? config('services.hari')[$datas->hari] : '';
            })
            ->addColumn('jam', function ($datas) {
                return $datas->jam_mulai . ' - ' . $datas->jam_akhir;
            })
            ->rawColumns(['options', 'ruang'])
            ->make(true);
    }

    public function getMatkul($tahun_semester_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'mahasiswa') {
            $mhs_id = Auth::user()->id;
        }

        $validate = $this->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $mhs = DB::table('profile_mahasiswas')
            ->select('profile_mahasiswas.rombel_id', 'profile_mahasiswas.tahun_masuk_id')
            ->where('user_id', $mhs_id)
            ->first();

        if (!$mhs) {
            abort(404);
        }

        $krsMatkul = DB::table('krs')
            ->select('krs_matkul.tahun_matkul_id')
            ->join('krs_matkul', 'krs.id', 'krs_matkul.krs_id')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get()
            ->pluck('tahun_matkul_id')
            ->toArray();

        $getMatkul = DB::table('tahun_matkul')
            ->select('tahun_matkul.id', 'matkuls.nama', 'matkuls.kode', 'matkuls.sks_mata_kuliah', 'users.name as dosen', 'tahun_ajarans.nama as tahun_ajaran')
            ->join('tahun_matkul_rombel', function ($join) use ($mhs) {
                $join->on('tahun_matkul_rombel.tahun_matkul_id', '=', 'tahun_matkul.id')
                    ->where('tahun_matkul_rombel.rombel_id', '=', $mhs->rombel_id);
            })
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->join('tahun_matkul_dosen', 'tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
            ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
            ->join('tahun_ajarans', 'tahun_matkul.tahun_ajaran_id', 'tahun_ajarans.id')
            ->whereNotIn('tahun_matkul.id', $krsMatkul)
            ->where('tahun_matkul.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        //? Variabel ini untuk get matkul yang ngulang dll
        $getMatkulPenambahan = DB::table('tahun_matkul')
                                    ->select('tahun_matkul.id', 'matkuls.nama', 'matkuls.kode', 'matkuls.sks_mata_kuliah', 'users.name as dosen', 'tahun_ajarans.nama as tahun_ajaran')
                                    ->join('tahun_matkul_mhs', 'tahun_matkul_mhs.tahun_matkul_id', '=', 'tahun_matkul.id')
                                    ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
                                    ->join('tahun_matkul_dosen', 'tahun_matkul_dosen.tahun_matkul_id', 'tahun_matkul.id')
                                    ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
                                    ->join('tahun_ajarans', 'tahun_matkul.tahun_ajaran_id', 'tahun_ajarans.id')
                                    ->where('tahun_matkul_mhs.mhs_id', $mhs_id)
                                    ->get();

        $mergeMatkul = $getMatkul->merge($getMatkulPenambahan);

        $matkul = $mergeMatkul->groupBy('id')->map(function ($group) {
            $firstItem = $group->first();
            $firstItem->dosen = implode(', ', $group->pluck('dosen')->toArray());
            return $firstItem;
        })->values()->toArray();

        return response()->json([
            'data' => $matkul
        ], 200);
    }

    public function getTotalSks($tahun_semester_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'mahasiswa') {
            $mhs_id = Auth::user()->id;
        }

        $validate = $this->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $sumSKSMatkulDipilih = DB::table('krs')
            ->select('matkuls.sks_mata_kuliah')
            ->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->sum('matkuls.sks_mata_kuliah');

        return response()->json([
            'total' => $sumSKSMatkulDipilih
        ], 200);
    }

    public function store(Request $request, $tahun_semester_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'mahasiswa') {
            $mhs_id = Auth::user()->id;
        }

        //? Validate Tahun Semester
        $validate = $this->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $krs = $this->getOrCreateKRS($tahun_semester_id, $mhs_id);
        $tahun_semester = $validate['data'];

        if ($role->name == 'mahasiswa') {
            //? Validate status
            if ($krs->status != 'pending' && $krs->status != 'ditolak') {
                return response()->json([
                    'message' => 'Sudah tidak boleh edit KRS!'
                ], 400,);
            }
            //? Validate Tanggal pengisian KRS
            if ($krs->status == 'pending') {
                if (!($tahun_semester->tgl_mulai_krs <= date('Y-m-d') && $tahun_semester->tgl_akhir_krs >= date('Y-m-d'))) {
                    return response()->json([
                        'message' => 'Tanggal mengisi KRS harus diantara ' . parseDate($tahun_semester->tgl_mulai_krs) . ' s.d ' . parseDate($tahun_semester->tgl_akhir_krs)
                    ], 400);
                }
            } else {
                if (!($krs->tgl_mulai_revisi <= date('Y-m-d') && $krs->tgl_akhir_revisi >= date('Y-m-d'))) {
                    return response()->json([
                        'message' => 'Tanggal revisi KRS harus diantara ' . parseDate($krs->tgl_mulai_revisi) . ' s.d ' . parseDate($krs->tgl_akhir_revisi)
                    ], 400);
                }
            }
        }

        $request->validate([
            'tahun_matkul_id' => 'required'
        ], [
            'tahun_matkul_id.required' => 'Mata Kuliah Tidak Boleh Kosong',
        ]);

        //? Validate Max SKS
        $sumSKSMatkulDipilih = DB::table('krs_matkul')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('krs_matkul.krs_id', $krs->id)
            ->sum('matkuls.sks_mata_kuliah');

        $sumSKSMatkulRequest = DB::table('tahun_matkul')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->whereIn('tahun_matkul.id', $request->tahun_matkul_id)
            ->sum('matkuls.sks_mata_kuliah');

        // if (($sumSKSMatkulRequest + $sumSKSMatkulDipilih) > $tahun_semester->jatah_sks) {
        //     return response()->json([
        //         'message' => 'Total SKS Tidak Boleh Lebih Besar Dari Jatah SKS'
        //     ], 400);
        // }

        DB::beginTransaction();
        try {
            DB::table('krs')
                ->where('id', $krs->id)
                ->update([
                    'jml_sks_diambil' => ($sumSKSMatkulRequest + $sumSKSMatkulDipilih),
                    'updated_at' => now()
                ]);

            foreach ($request->tahun_matkul_id as $tahun_matkul_id) {
                if (!DB::table('krs_matkul')->where('krs_id', $krs->id)->where('tahun_matkul_id', $tahun_matkul_id)->exists()) {
                    DB::table('krs_matkul')
                        ->insert([
                            'krs_id' => $krs->id,
                            'tahun_matkul_id' => $tahun_matkul_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Data Berhasil Di Simpan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($tahun_semester_id, $tahun_matkul_id, $mhs_id = null)
    {
        $role = getRole();

        if ($role->name == 'mahasiswa') {
            $mhs_id = Auth::user()->id;
        }

        $cek = DB::table('krs_matkul')
            ->select('krs_matkul.id')
            ->join('krs', 'krs_matkul.krs_id', 'krs.id')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->where('krs_matkul.tahun_matkul_id', $tahun_matkul_id)
            ->first();

        if (!$cek) {
            return response()->json([
                'message' => 'Telah terjadi kesalahan!'
            ], 400);
        }

        DB::beginTransaction();
        try {
            DB::table('krs_matkul')
                ->where('id', $cek->id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil di hapus!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
