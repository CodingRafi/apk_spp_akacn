<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Controllers\KrsController as ControllersKrsController;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
            ->select(
                'tahun_semester.id',
                'semesters.nama',
                'tahun_semester.jatah_sks',
                'tahun_semester.tgl_mulai_krs',
                'tahun_semester.tgl_akhir_krs',
                'krs.jml_sks_diambil',
                'krs.status'
            )
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->leftJoin('krs', function ($join) use ($mhs_id) {
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

    public function validatePembayaran($tahun_semester_id, $mhs_id)
    {
        $pembayaran = DB::table('tahun_pembayaran')
            ->where('tahun_semester_id', $tahun_semester_id)
            ->first();

        if (!$pembayaran) {
            return [
                'status' => false,
                'code' => 1,
                'message' => 'Pembayaran Belum Dibuat, bukan waktu pengisian KRS!'
            ];
        }

        if ($pembayaran->publish == 0) {
            return [
                'status' => false,
                'code' => 2,
                'message' => 'Pembayaran Belum dipublish, bukan waktu pengisian KRS!'
            ];
        }

        $cekPembayaran = DB::table('rekap_pembayaran')
            ->where('type', 'semester')
            ->where('untuk', $tahun_semester_id)
            ->where('user_id', $mhs_id)
            ->first();

        if ($cekPembayaran->sisa > 0) {
            return [
                'status' => false,
                'code' => 3,
                'message' => 'Pembayaran Belum Lunas, belum bisa isi KRS!'
            ];
        }

        return [
            'status' => true,
            'code' => 0
        ];
    }

    public function show($tahun_semester_id, $mhs_id = null)
    {
        $mhs_id = $this->validateMhsId($mhs_id);
        $validate = $this->krsController->validateTahunSemester($tahun_semester_id, $mhs_id);

        if (!$validate['status']) {
            abort(404);
        }

        $validationPembayaran = $this->validatePembayaran($tahun_semester_id, $mhs_id);

        $tahun_semester = DB::table('tahun_semester')
            ->select(
                'tahun_semester.id',
                'semesters.nama',
                'tahun_semester.jatah_sks',
                'tahun_semester.tgl_mulai_krs',
                'tahun_semester.tgl_akhir_krs',
                'tahun_semester.status'
            )
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $krs = DB::table('krs')
            ->where('krs.mhs_id', $mhs_id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->first();

        return view('mahasiswa.krs.show', compact('tahun_semester', 'krs', 'mhs_id', 'validationPembayaran'));
    }


    public function ajukan($tahun_semester_id)
    {
        $validationPembayaran = $this->validatePembayaran($tahun_semester_id, Auth::user()->id);

        if (!$validationPembayaran['status']) {
            return redirect()->back()->with('error', $validationPembayaran['message']);
        }

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

    public function print($tahun_semester_id)
    {
        $krs = DB::table('krs')
            ->select(
                'krs.*',
                'users.name',
                'prodi.nama as prodi',
                'semesters.nama as semester',
                'users.login_key as nim',
                'dosenPa.name as dosenPa'
            )
            ->join('tahun_semester', 'krs.tahun_semester_id', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', 'semesters.id')
            ->join('users', 'krs.mhs_id', 'users.id')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('prodi', 'profile_mahasiswas.prodi_id', 'prodi.id')
            ->join('rombels', 'profile_mahasiswas.rombel_id', 'rombels.id')
            ->join('rombel_tahun_ajarans', function ($q) {
                $q->on('rombels.id', 'rombel_tahun_ajarans.rombel_id')
                    ->on('rombel_tahun_ajarans.tahun_masuk_id', 'profile_mahasiswas.tahun_masuk_id');
            })
            ->join('users as dosenPa', 'dosenPa.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->where('krs.mhs_id', Auth::user()->id)
            ->where('krs.tahun_semester_id', $tahun_semester_id)
            ->first();

        if (!$krs) {
            abort(404);
        }

        $krsMatkul = DB::table('krs_matkul')
            ->select(
                'tahun_matkul.id',
                'matkuls.nama as matkul',
                'tahun_matkul.hari',
                'tahun_matkul.jam_mulai',
                'tahun_matkul.jam_akhir',
                'matkuls.sks_mata_kuliah'
            )
            ->join('tahun_matkul', 'krs_matkul.tahun_matkul_id', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', 'matkuls.id')
            ->where('krs_matkul.krs_id', $krs->id)
            ->get()
            ->map(function ($data) {
                $dosen = DB::table('tahun_matkul_dosen')
                    ->select('users.name')
                    ->join('users', 'tahun_matkul_dosen.dosen_id', 'users.id')
                    ->where('tahun_matkul_dosen.tahun_matkul_id', $data->id)
                    ->get()
                    ->pluck('name')
                    ->implode(', ');
                $data->dosen = $dosen;

                $ruang = DB::table('tahun_matkul_ruang')
                    ->select('ruangs.nama')
                    ->join('ruangs', 'tahun_matkul_ruang.ruang_id', 'ruangs.id')
                    ->where('tahun_matkul_ruang.tahun_matkul_id', $data->id)
                    ->get()
                    ->pluck('nama')
                    ->implode(', ');

                $data->ruang = $ruang;
                return $data;
            });

        $admin = DB::table('users')->first();

        // $footerView = view('components.footer-pdf')->render();
        $pdf = Pdf::loadView('mahasiswa.krs.print', compact('krs', 'krsMatkul', 'admin'));
        // $pdf->getDomPDF()->setHttpContext(stream_context_create([
        //     'ssl' => [
        //         'allow_self_signed'=> TRUE,
        //         'verify_peer' => FALSE,
        //         'verify_peer_name' => FALSE,
        //     ]
        // ]));
        // $pdf->getDomPDF()->loadHtml('<h1>Isi PDF</h1>'); // Ganti dengan konten PDF Anda
        // $pdf->getDomPDF()->setPaper('A4', 'portrait');
        // $pdf->getDomPDF()->render();
        // $canvas = $pdf->getDomPDF()->getCanvas();
        // $canvas->page_text(72, 18, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", null, 10, array(0,0,0)); // Tambahkan nomor halaman
        // $canvas->page_script($footerView); // Tambahkan footer
    
        // Unduh PDF
        return $pdf->stream('nama_file.pdf');
    }
}
