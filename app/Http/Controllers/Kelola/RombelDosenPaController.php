<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RombelDosenPaController extends Controller
{
    public function index()
    {
        return view('data_master.rombel.dosen_pa.index');
    }

    public function dataTahunAjaran($rombel_id)
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $options = "<a href='" . route('data-master.rombel.dosen-pa.show', ['rombel_id' => $rombel_id, 'tahun_ajaran_id' => $data->id]) . "' class='btn btn-primary'>Set Dosen PA</a>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function data($rombel_id, $tahun_ajaran_id)
    {
        $datas = DB::table('rombel_tahun_ajarans')
            ->select('users.id', 'users.name', 'users.login_key', 'rombel_tahun_ajarans.id as rombel_tahun_ajaran_id')
            ->join('users', 'users.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
            ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_ajaran_id)
            ->get()
            ->toArray();

        $mhs = DB::table('rombel_tahun_ajarans')
            ->select('rombel_tahun_ajarans.id as rombel_tahun_ajaran_id', 'users.name', 'users.login_key')
            ->join('rombel_mhs', 'rombel_mhs.rombel_tahun_ajaran_id', 'rombel_tahun_ajarans.id')
            ->join('users', 'users.id', 'rombel_mhs.mhs_id')
            ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
            ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_ajaran_id)
            ->get()
            ->toArray();

        $datas = array_map(function ($data) use ($mhs) {
            $filteredMhs = array_filter($mhs, function($item) use($data) {
                return $item->rombel_tahun_ajaran_id === $data->rombel_tahun_ajaran_id;
            });

            $data->mhs = implode(', ', array_map(function ($item) {
                return "{$item->name} ({$item->login_key})";
            }, $filteredMhs));

            return $data;
        }, $datas);

        foreach ($datas as $data) {
            $options = "";
            $options = $options . " <button class='btn btn-primary'
                        onclick='editForm(`" . route('data-master.rombel.dosen-pa.showMahasiswa', ['rombel_id' => $rombel_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'dosen_id' => $data->id]) . "`, `Set Mahasiswa`, `#mahasiswa`, getMhs)'>
                        <i class='ti-pencil'></i>
                        Set Mahasiswa
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.dosen-pa.destroy', ['rombel_id' => $rombel_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'dosen_id' => $data->id]) . "`)'>
                                    Hapus
                                </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('name', function ($datas) {
                return $datas->name . ' (' . $datas->login_key . ')';
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function getDosen($rombel_id, $tahun_ajaran_id)
    {
        $dosen = User::select('users.id', 'users.name', 'users.login_key')
            ->join('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->leftJoin('rombel_tahun_ajarans', function ($q) use ($rombel_id, $tahun_ajaran_id) {
                $q->on('rombel_tahun_ajarans.dosen_pa_id', 'users.id')
                    ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
                    ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_ajaran_id);
            })
            ->where('profile_dosens.status', '1')
            ->whereNull('rombel_tahun_ajarans.id')
            ->role('dosen')
            ->get();

        return response()->json($dosen, 200);
    }

    public function store(Request $request, $rombel_id, $tahun_ajaran_id)
    {
        $request->validate([
            'dosen_pa_id' => 'required'
        ]);

        foreach ($request->dosen_pa_id as $dosen_pa_id) {
            DB::table('rombel_tahun_ajarans')->insert([
                'rombel_id' => $rombel_id,
                'dosen_pa_id' => $dosen_pa_id,
                'tahun_masuk_id' => $tahun_ajaran_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'berhasil disimpan'
        ], 200);
    }

    public function show()
    {
        return view('data_master.rombel.dosen_pa.show');
    }

    public function getDosenPa()
    {
        $data = DB::table('rombels')
            ->select('rombels.id', DB::raw("GROUP_CONCAT(CONCAT(users.name, ' (', users.login_key, ')')) as dosen_pa"), 'rombels.nama')
            ->join('rombel_tahun_ajarans', 'rombel_tahun_ajarans.rombel_id', '=', 'rombels.id')
            ->join('users', 'users.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->when(request('jenis_kelas_id'), function ($q) {
                $q->where('rombels.jenis_kelas_id', request('jenis_kelas_id'));
            })
            ->when(request('tahun_ajaran_id'), function ($q) {
                $q->where('rombel_tahun_ajarans.tahun_masuk_id', request('tahun_ajaran_id'));
            })
            ->when(request('prodi_id'), function ($q) {
                $q->where('rombels.prodi_id', request('prodi_id'));
            })
            ->groupBy('rombels.id', 'rombel_tahun_ajarans.tahun_masuk_id', 'rombels.nama')
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function destroy($rombel_id, $tahun_masuk_id, $dosen_pa_id)
    {
        try {
            DB::table('rombel_tahun_ajarans')
                ->where('tahun_masuk_id', $tahun_masuk_id)
                ->where('rombel_id', $rombel_id)
                ->where('dosen_pa_id', $dosen_pa_id)
                ->delete();

            return response()->json([
                'message' => 'Berhasil di hapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function showMahasiswa($rombel_id, $tahun_masuk_id, $dosen_pa_id)
    {
        $mhs = DB::table('rombel_tahun_ajarans')
            ->select('mhs_id')
            ->join('rombel_mhs', 'rombel_mhs.rombel_tahun_ajaran_id', 'rombel_tahun_ajarans.id')
            ->where('rombel_tahun_ajarans.dosen_pa_id', $dosen_pa_id)
            ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_masuk_id)
            ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
            ->get()
            ->pluck('mhs_id')
            ->toArray();

        return response()->json([
            'data' => [
                'dosen_id' => $dosen_pa_id,
                'mhs' => $mhs
            ]
        ], 200);
    }

    public function listMahasiswa($rombel_id, $tahun_ajaran_id, $dosen_pa_id)
    {
        $rombel_tahun_ajaran = DB::table('rombel_tahun_ajarans')
            ->select('id')
            ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
            ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_ajaran_id)
            ->where('rombel_tahun_ajarans.dosen_pa_id', $dosen_pa_id)
            ->first();

        $mhs = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key')
            ->join('profile_mahasiswas', function ($q) use ($tahun_ajaran_id) {
                $q->on('profile_mahasiswas.user_id', 'users.id')
                    ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id);
            })
            ->leftJoin('rombel_mhs', 'rombel_mhs.mhs_id', 'users.id')
            ->whereNull('rombel_mhs.id')
            ->orWhere('rombel_mhs.rombel_tahun_ajaran_id', $rombel_tahun_ajaran->id)
            ->get();

        return response()->json([
            'data' => $mhs
        ], 200);
    }

    public function updateMahasiswa(Request $request, $rombel_id, $tahun_ajaran_id, $dosen_pa_id)
    {
        $rombel_tahun_ajaran = DB::table('rombel_tahun_ajarans')
            ->select('id')
            ->where('rombel_tahun_ajarans.rombel_id', $rombel_id)
            ->where('rombel_tahun_ajarans.tahun_masuk_id', $tahun_ajaran_id)
            ->where('rombel_tahun_ajarans.dosen_pa_id', $dosen_pa_id)
            ->first();

        $dataMhs = DB::table('rombel_mhs')
            ->select('mhs_id')
            ->where('rombel_tahun_ajaran_id', $rombel_tahun_ajaran->id)
            ->get()
            ->pluck('mhs_id')
            ->toArray();

        try {
            // Compare for insert
            $insert = array_diff(($request->mhs_id ?? []), $dataMhs);

            foreach ($insert as $value) {
                DB::table('rombel_mhs')->insert([
                    'mhs_id' => $value,
                    'rombel_tahun_ajaran_id' => $rombel_tahun_ajaran->id
                ]);
            }

            // Compare for delete
            $delete = array_diff($dataMhs, ($request->mhs_id ?? []));

            foreach ($delete as $value) {
                DB::table('rombel_mhs')
                    ->where('mhs_id', $value)
                    ->where('rombel_tahun_ajaran_id', $rombel_tahun_ajaran->id)
                    ->delete();
            }

            return response()->json([
                'message' => 'success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
