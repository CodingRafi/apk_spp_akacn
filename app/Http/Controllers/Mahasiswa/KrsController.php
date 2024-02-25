<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    public function index()
    {
        return view('mahasiswa.krs.index');
    }

    public function dataSemester()
    {
        $mhs = Auth::user()->mahasiswa;
        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('krs.show', ['tahun_semester_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('tgl_pengisian', function ($datas) {
                return parseDate($datas->tgl_mulai_krs) . ' s.d ' . parseDate($datas->tgl_akhir_krs);
            })
            ->addColumn('sks_diambil', function ($datas) {
                return 0;
            })
            ->addColumn('status', function ($datas) {
                return 'Belum Mengisi';
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    private function validateTahunSemester($tahun_semester_id)
    {
        $mhs = Auth::user()->mahasiswa;

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

    public function show($tahun_semester_id)
    {
        $validate = $this->validateTahunSemester($tahun_semester_id);

        if (!$validate['status']) {
            abort(404);
        }

        $mhs = Auth::user()->mahasiswa;

        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $data = DB::table('tahun_matkul')
            ->join('tahun_matkul_rombel', 'tahun_matkul.id', 'tahun_matkul_rombel.tahun_matkul_id')
            ->where('tahun_matkul.prodi_id', $mhs->prodi_id)
            ->where('tahun_matkul.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->where('tahun_matkul_rombel.rombel_id', $mhs->rombel_id)
            ->get();

        $krs = DB::table('krs')
            ->where('krs.mhs_id', Auth::user()->id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->first();

        return view('mahasiswa.krs.show', compact('tahun_semester', 'data', 'krs'));
    }

    public function getMatkul($tahun_semester_id)
    {
        $validate = $this->validateTahunSemester($tahun_semester_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $matkul = DB::table('tahun_matkul')
            ->select('tahun_matkul.id', 'matkuls.nama', 'users.name as dosen', 'matkuls.kode', 'matkuls.sks_mata_kuliah')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->join('users', 'users.id', 'tahun_matkul.dosen_id')
            ->leftJoin('krs_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->leftJoin('krs', function ($join) use ($tahun_semester_id) {
                $join->on('krs_matkul.krs_id', 'krs.id')
                    ->where('krs.mhs_id', Auth::user()->id)
                    ->where('krs.tahun_semester_id', $tahun_semester_id);
            })
            ->whereNull('krs_matkul.tahun_matkul_id')
            ->get();

        return response()->json([
            'data' => $matkul
        ], 200);
    }

    public function dataMatkul($tahun_semester_id)
    {
        $validate = $this->validateTahunSemester($tahun_semester_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        $datas = DB::table('krs')
            ->select('krs_matkul.id', 'matkuls.kode', 'matkuls.nama', 'users.name as dosen', 'matkuls.sks_mata_kuliah')
            ->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->join('users', 'users.id', 'tahun_matkul.dosen_id')
            ->where('krs.mhs_id', Auth::user()->id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->get();


        foreach ($datas as $data) {
            $options = "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('krs.destroy', ['tahun_semester_id' => $tahun_semester_id, 'krs_matkul_id' => $data->id]) . "`)'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    private function getOrCreateKRS($tahun_semester_id)
    {
        $krs = DB::table('krs')
            ->where('mhs_id', Auth::user()->id)
            ->where('tahun_semester_id', $tahun_semester_id)
            ->first();

        if (!$krs) {
            $krs = DB::table('krs')
                ->insertGetId([
                    'mhs_id' => Auth::user()->id,
                    'tahun_semester_id' => $tahun_semester_id
                ]);
        } else {
            $krs = $krs->id;
        }

        return $krs;
    }

    public function getTotalSKS($tahun_semester_id)
    {
        $krs = $this->getOrCreateKRS($tahun_semester_id);

        $sumSKSMatkulDipilih = DB::table('krs_matkul')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('krs_matkul.krs_id', $krs)
            ->sum('matkuls.sks_mata_kuliah');

        return response()->json([
            'total' => $sumSKSMatkulDipilih
        ], 200);
    }

    public function store(Request $request, $tahun_semester_id)
    {
        //? Validate Tahun Semester
        $validate = $this->validateTahunSemester($tahun_semester_id);

        if (!$validate['status']) {
            return response()->json($validate, 400);
        }

        //? Validate Tanggal pengisian KRS
        $tahun_semester = $validate['data'];
        if (!($tahun_semester->tgl_mulai_krs <= date('Y-m-d') && $tahun_semester->tgl_akhir_krs >= date('Y-m-d'))) {
            return response()->json([
                'message' => 'Tanggal mengisi KRS harus diantara ' . parseDate($tahun_semester->tgl_mulai_krs) . ' s.d ' . parseDate($tahun_semester->tgl_akhir_krs)
            ], 400);
        }

        $request->validate([
            'tahun_matkul_id' => 'required'
        ], [
            'tahun_matkul_id.required' => 'Mata Kuliah Tidak Boleh Kosong',
        ]);

        $krs = $this->getOrCreateKRS($tahun_semester_id);

        //? Validate Max SKS
        $sumSKSMatkulDipilih = DB::table('krs_matkul')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('krs_matkul.krs_id', $krs)
            ->sum('matkuls.sks_mata_kuliah');

        $sumSKSMatkulRequest = DB::table('tahun_matkul')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->whereIn('tahun_matkul.id', $request->tahun_matkul_id)
            ->sum('matkuls.sks_mata_kuliah');

        if (($sumSKSMatkulRequest + $sumSKSMatkulDipilih) > $tahun_semester->jatah_sks) {
            return response()->json([
                'message' => 'Total SKS Tidak Boleh Lebih Besar Dari Jatah SKS'
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($request->tahun_matkul_id as $tahun_matkul_id) {
                if (!DB::table('krs_matkul')->where('krs_id', $krs)->where('tahun_matkul_id', $tahun_matkul_id)->exists()) {
                    DB::table('krs_matkul')
                        ->insert([
                            'krs_id' => $krs,
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

    public function destroy($tahun_semester_id, $krs_matkul_id)
    {
        DB::beginTransaction();
        try {
            $cek = DB::table('krs_matkul')
                ->join('krs', 'krs_matkul.krs_id', 'krs.id')
                ->where('krs.mhs_id', Auth::user()->id)
                ->where('krs.tahun_semester_id', $tahun_semester_id)
                ->where('krs_matkul.id', $krs_matkul_id)
                ->first();

            if (!$cek) {
                return response()->json([
                    'message' => 'Telah terjadi kesalahan!'
                ], 400);
            }

            DB::table('krs_matkul')->where('id', $krs_matkul_id)->delete();
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

    public function ajukan(Request $request, $tahun_semester_id)
    {
    }
}
