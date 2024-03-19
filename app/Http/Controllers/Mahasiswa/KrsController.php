<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Controllers\KrsController as ControllersKrsController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KrsController extends Controller
{
    protected $krsController;

    public function __construct(ControllersKrsController $krsController)
    {
        $this->krsController = $krsController;
        $this->middleware('permission:view_krs', ['only' => ['index']]);
        $this->middleware('permission:add_krs', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_krs', ['only' => ['edit', 'update', 'revisi', 'storeRevisi']]);
        $this->middleware('permission:delete_krs', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('mahasiswa.krs.index');
    }

    private function validateMhsId($mhs_id = null)
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            $mhs_id = $user->id;
        }

        $user = User::findOrFail($mhs_id);

        if ((!$user->hasRole('mahasiswa') && $mhs_id == null) || $user->mahasiswa == null) {
            abort(404);
        }

        return $mhs_id;
    }

    public function dataSemester($mhs_id = null)
    {
        $mhs_id = $this->validateMhsId($mhs_id);
        $mhs = User::findOrFail($mhs_id)->mahasiswa;
        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs', 'krs.jml_sks_diambil', 'krs.status')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('krs', function ($join) use($mhs_id) {
                $join->on('krs.tahun_semester_id', 'tahun_semester.id')
                    ->where('krs.mhs_id', $mhs_id);
            })
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('krs.show', ['tahun_semester_id' => $data->id, 'mhs_id' => $mhs_id]) . "' class='btn btn-info mx-2'>Detail</a>";

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('tgl_pengisian', function ($datas) {
                return parseDate($datas->tgl_mulai_krs) . ' s.d ' . parseDate($datas->tgl_akhir_krs);
            })
            ->editColumn('jatah_sks', function ($datas) {
                return $datas->jatah_sks . ' SKS';
            })
            ->addColumn('sks_diambil', function ($datas) {
                return ($datas->jml_sks_diambil ?? 0) . ' SKS';
            })
            ->addColumn('status', function ($datas) {
                $status = '';

                if ($datas->status) {
                    if ($datas->status == 'pending') {
                        $status = '<span class="badge bg-warning text-white">SEDANG MENGISI</span>';
                    } elseif ($datas->status == 'diterima') {
                        $status = '<span class="badge bg-success text-white">DITERIMA</span>';
                    } elseif ($datas->status == 'ditolak') {
                        $status = '<span class="badge bg-danger text-white">DITOLAK</span>';
                    } else {
                        $status = '<span class="badge bg-secondary text-white">PENGAJUAN</span>';
                    }
                } else {
                    $status = '<span class="badge bg-warning text-white">BELUM MENGISI</span>';
                }

                return $status;
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function show($tahun_semester_id, $mhs_id = null)
    {
        $mhs_id = $this->validateMhsId($mhs_id);
        $validate = $this->krsController->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            abort(404);
        }

        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama', 'tahun_semester.jatah_sks', 'tahun_semester.tgl_mulai_krs', 'tahun_semester.tgl_akhir_krs', 'tahun_semester.status')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $krs = DB::table('krs')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->first();

        return view('mahasiswa.krs.show', compact('tahun_semester', 'krs', 'mhs_id'));
    }


    public function ajukan($tahun_semester_id)
    {
        DB::table('krs')
            ->where('mhs_id', Auth::user()->id)
            ->where('tahun_semester_id', $tahun_semester_id)->update([
                'status' => 'pengajuan'
            ]);

        return redirect()->back()->with('success', 'Data Berhasil Di Ajukan');
    }

    public function revisi($tahun_semester_id)
    {
        $data =  DB::table('krs')
            ->where('mhs_id', Auth::user()->id)
            ->where('tahun_semester_id', $tahun_semester_id)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error', 'Data Tidak Ditemukan');
        }

        if ($data->status != 'ditolak') {
            return redirect()->back()->with('error', 'Maaf telah terjadi kesalahan!');
        }

        if (!($data->tgl_mulai_revisi <= date('Y-m-d') && $data->tgl_akhir_revisi >= date('Y-m-d'))) {
            return redirect()->back()->with('error', 'Bukan Tanggal Revisi!');
        }

        DB::table('krs')
            ->where('mhs_id', Auth::user()->id)
            ->where('tahun_semester_id', $tahun_semester_id)
            ->update([
                'status' => 'pengajuan',
                'verify_id' => null
            ]);

        return redirect()->back()->with('success', 'Berhasil direvisi!');
    }
}
