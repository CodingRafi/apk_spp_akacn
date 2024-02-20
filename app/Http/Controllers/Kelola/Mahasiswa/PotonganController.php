<?php

namespace App\Http\Controllers\Kelola\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PotonganController extends Controller
{
    public function __construct()
    {
        if (request('role') != 'mahasiswa') {
            abort(404);
        }
    }

    public function get($role, $user_id)
    {
        $user = User::find($user_id);
        $mhs = $user->mahasiswa;

        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id')
            ->where('prodi_id', $mhs->prodi_id)
            ->where('tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get()
            ->pluck('id');

        $tahun_pembayaran_lainnya = DB::table('tahun_pembayaran_lain')
            ->select('tahun_pembayaran_lain.id')
            ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where('tahun_pembayaran_lain.prodi_id', $mhs->prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get()
            ->pluck('id');

        $data = DB::table('potongan_tahun_ajaran')
            ->select('potongans.id', 'potongans.nama', 'potongan_tahun_ajaran.nominal')
            ->join('potongans', 'potongans.id', 'potongan_tahun_ajaran.potongan_id')
            ->leftJoin('potongan_mhs', function ($join) use ($user_id) {
                $join->on('potongan_mhs.potongan_id', 'potongans.id')
                    ->where('potongan_mhs.mhs_id', $user_id);
            })
            ->where(function ($q) use ($tahun_semester, $tahun_pembayaran_lainnya) {
                $q->whereIn('potongan_tahun_ajaran.tahun_semester_id', $tahun_semester)
                    ->orWhereIn('potongan_tahun_ajaran.tahun_pembayaran_lain_id', $tahun_pembayaran_lainnya);
            })
            ->whereNull('potongan_mhs.mhs_id')
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function index($role, $user_id)
    {
        $datas = Pembayaran::getPembayaranMahasiswa($user_id);

        return view('users.mahasiswa.potongan.index', compact('datas'));
    }

    public function store(Request $request, $role, $user_id)
    {
        $user = User::findOrFail($user_id);

        $user->potongan()->syncWithoutDetaching($request->potongan_id);

        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    public function data($data, $user_id)
    {
        $user = User::findOrFail($user_id);
        $potongan_id = $user->potongan->pluck('id');

        $datas = DB::table('potongan_tahun_ajaran')
            ->select('potongan_tahun_ajaran.id', 'potongans.nama as potongan', 'semesters.nama as semester', 'pembayaran_lainnyas.nama as lainnya', 'potongan_tahun_ajaran.publish', 'potongan_tahun_ajaran.nominal')
            ->join('potongans', 'potongans.id', 'potongan_tahun_ajaran.potongan_id')
            ->leftJoin('tahun_semester', 'tahun_semester.id', 'potongan_tahun_ajaran.tahun_semester_id')
            ->leftJoin('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('tahun_pembayaran_lain', 'tahun_pembayaran_lain.id', 'potongan_tahun_ajaran.tahun_pembayaran_lain_id')
            ->leftJoin('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->whereIn('potongans.id', $potongan_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                                    Hapus
                                                </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('namaParse', function ($data) {
                return $data->semester ?? $data->lainnya;
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options'])
            ->make(true);
    }
}
