<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\JenisEvaluasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulDosenController extends Controller
{
    public function index($tahun_ajaran_id, $id)
    {
        $tahunAjaranAktif = DB::table('tahun_ajarans')->where('status', "1")->first();
        $dosens = [];

        if ($tahunAjaranAktif) {
            $dosens = User::role('dosen')
                ->select('users.*')
                ->join('profile_dosens', 'profile_dosens.user_id', 'users.id')
                ->leftJoin('penugasan_dosens', function ($q) use ($tahunAjaranAktif) {
                    $q->on('users.id_neo_feeder', '=', 'penugasan_dosens.id_dosen')
                        ->where('penugasan_dosens.tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->where(function ($q) {
                    $q->where('profile_dosens.status', '1')
                        ->orWhereNotNull('penugasan_dosens.id');
                })
                ->get();
        }

        $jenisEvaluasi = JenisEvaluasi::all();

        return view('data_master.tahun_ajaran.matkul.setDosen', compact(
            'id',
            'tahun_ajaran_id',
            'dosens',
            'jenisEvaluasi'
        ));
    }

    public function store(Request $request, $tahun_ajaran_id, $id)
    {
        $request->validate([
            'dosen_id' => 'required',
            'sks_substansi_total' => 'required',
            'rencana_tatap_muka' => 'required',
            'realisasi_tatap_muka' => 'required',
            'jenis_evaluasi_id' => 'required'
        ]);

        DB::table('tahun_matkul_dosen')->insert([
            'dosen_id' => $request->dosen_id,
            'sks_substansi_total' => $request->sks_substansi_total,
            'rencana_tatap_muka' => $request->rencana_tatap_muka,
            'realisasi_tatap_muka' => $request->realisasi_tatap_muka,
            'jenis_evaluasi_id' => $request->jenis_evaluasi_id,
            'tahun_matkul_id' => $id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function data($tahun_ajaran_id, $id)
    {
        $datas = DB::table('tahun_matkul_dosen')
            ->select('users.name', 'users.login_key', 'tahun_matkul_dosen.*', 'jenis_evaluasis.nama as jenisEvaluasi')
            ->join('users', 'users.id', 'tahun_matkul_dosen.dosen_id')
            ->leftJoin('jenis_evaluasis', 'jenis_evaluasis.id', 'tahun_matkul_dosen.jenis_evaluasi_id')
            ->where('tahun_matkul_dosen.tahun_matkul_id', $id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                                onclick='editForm(`" . route('data-master.tahun-ajaran.matkul.dosen.show', ['id' => $tahun_ajaran_id, 'matkul_id' => $id, 'tahun_matkul_dosen_id' => $data->id]) . "`, `Edit Dosen`, `#setDosen`, editDosen)'>
                                <i class='ti-pencil'></i>
                                Edit
                            </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.tahun-ajaran.matkul.dosen.destroy', ['id' => $tahun_ajaran_id, 'matkul_id' => $id, 'tahun_matkul_dosen_id' => $data->id]) . "`, () => {tableDosen.ajax.reload()})' type='button'>
                                                                Hapus
                                                            </button>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_ajaran_id, $matkul_id, $tahun_matkul_dosen_id)
    {
        $data = DB::table('tahun_matkul_dosen')
            ->where('id', $tahun_matkul_dosen_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $tahun_ajaran_id, $matkul_id, $tahun_matkul_dosen_id)
    {
        $request->validate([
            'sks_substansi_total' => 'required',
            'rencana_tatap_muka' => 'required',
            'realisasi_tatap_muka' => 'required',
            'jenis_evaluasi_id' => 'required'
        ]);

        DB::table('tahun_matkul_dosen')
            ->where('id', $tahun_matkul_dosen_id)
            ->update([
                'sks_substansi_total' => $request->sks_substansi_total,
                'rencana_tatap_muka' => $request->rencana_tatap_muka,
                'realisasi_tatap_muka' => $request->realisasi_tatap_muka,
                'jenis_evaluasi_id' => $request->jenis_evaluasi_id,
                'updated_at' => now()
            ]);

        return response()->json([
            'message' => 'Berhasil ditambahkan'
        ], 200);
    }

    public function destroy($tahun_ajaran_id, $matkul_id, $tahun_matkul_dosen_id)
    {
        $get = DB::table('tahun_matkul_dosen')
            ->where('id', $tahun_matkul_dosen_id)
            ->first();

        //? Validation jika sudah buat jadwal
        $check = DB::table('jadwal')
            ->where('tahun_matkul_id', $matkul_id)
            ->where('pengajar_id', $get->dosen_id)
            ->count();

        if ($check > 0) {
            return response()->json([
                'message' => 'Tidak bisa dihapus karena sudah ada jadwal'
            ], 400);
        }

        DB::table('tahun_matkul_dosen')
            ->where('id', $tahun_matkul_dosen_id)
            ->delete();

        return response()->json([
            'message' => 'Berhasil dihapus'
        ], 200);
    }
}
