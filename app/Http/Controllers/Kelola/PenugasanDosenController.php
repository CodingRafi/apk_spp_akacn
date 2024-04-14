<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenugasanDosenController extends Controller
{
    public function index()
    {
        return view('kelola.penugasan_dosen.index');
    }

    public function dataTahunAjaran()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('penugasan-dosen.show', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show()
    {
        return view('kelola.penugasan_dosen.show');
    }

    public function data($tahunAjaranId)
    {
        $datas = DB::table('penugasan_dosens')
            ->select('users.name', 'users.login_key', 'penugasan_dosens.*', 'prodi.nama as prodi')
            ->join('users', 'users.id_neo_feeder', 'penugasan_dosens.id_dosen')
            ->leftJoin('prodi', 'penugasan_dosens.prodi_id', 'prodi.id')
            ->where('penugasan_dosens.tahun_ajaran_id', $tahunAjaranId)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('a_sp_homebase', function($data){
                return $data->a_sp_homebase == '1' ? 'Ya' : 'Tidak';
            })
            ->rawColumns(['a_sp_homebase'])
            ->make(true);
    }

    public function storeNeoFeeder(Request $request, $tahunAjaranId)
    {
        $prodi = DB::table('prodi')->pluck('id')->toArray();
        foreach ($request->data as $data) {
            if (empty($data['id_prodi']) || in_array($data['id_prodi'], $prodi)) {
                DB::beginTransaction();
                try {
                    DB::table('penugasan_dosens')->updateOrInsert([
                        'id_registrasi_dosen' => $data['id_registrasi_dosen'],
                        'id_dosen' => $data['id_dosen'],
                        'tahun_ajaran_id' => $tahunAjaranId,
                    ], [
                        'prodi_id' => $data['id_prodi'],
                        'nomor_surat_tugas' => $data['nomor_surat_tugas'],
                        'tanggal_surat_tugas' => Carbon::parse($data['tanggal_surat_tugas'])->format('Y-m-d'),
                        'mulai_surat_tugas' => Carbon::parse($data['mulai_surat_tugas'])->format('Y-m-d'),
                        'tgl_create' => Carbon::parse($data['tgl_create'])->format('Y-m-d'),
                        'tgl_ptk_keluar' => Carbon::parse($data['tgl_ptk_keluar'])->format('Y-m-d'),
                        'status_pegawai_id' => $data['id_stat_pegawai'],
                        'jenis_keluar_id' => $data['id_jns_keluar'],
                        'ikatan_kerja_id' => $data['id_ikatan_kerja'],
                        'a_sp_homebase' => $data['a_sp_homebase'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    dd($data);
                    return response()->json([
                        'message' => $th->getMessage()
                    ], 400);
                }
            }
        }
        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
