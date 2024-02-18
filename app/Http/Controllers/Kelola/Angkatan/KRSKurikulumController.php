<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\Ruang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KRSKurikulumController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('kurikulums')
            ->select('kurikulums.nama as kurikulum', 'semesters.nama as semester', 'tahun_semester.id')
            ->join('tahun_kurikulum', 'tahun_kurikulum.kurikulum_id', 'kurikulums.id')
            ->join('tahun_semester', 'tahun_semester.id', 'tahun_kurikulum.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.prodi.krs.kurikulum.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'tahun_semester_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";

            $options = $options . "<a href='" . route('data-master.prodi.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('data-master.prodi.destroy', $data->id) . "`)'>
                                            Hapus
                                        </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_semester_id' => 'required',
            'kurikulum_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            DB::table('tahun_kurikulum')->insert([
                'kurikulum_id' => $request->kurikulum_id,
                'tahun_semester_id' => $request->tahun_semester_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil disimpan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $tahun_semester_id)
    {
        $tahun_kurikulum = DB::table('tahun_kurikulum')
            ->where('tahun_semester_id', $tahun_semester_id)
            ->first();
        
        if (!$tahun_kurikulum) {
            abort(404);
        }
        
        $matkuls = DB::table('matkuls')
                    ->where('kurikulum_id', $tahun_kurikulum->kurikulum_id)
                    ->get();

        $dosens = User::role('dosen')
            ->select('users.*')
            ->join('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->where('status', "1")
            ->get();

        $ruangs = Ruang::all();

        return view('data_master.prodi.angkatan.krs.index', compact('matkuls', 'dosens', 'prodi_id', 'tahun_ajaran_id', 'tahun_semester_id', 'ruangs'));
    }
}
