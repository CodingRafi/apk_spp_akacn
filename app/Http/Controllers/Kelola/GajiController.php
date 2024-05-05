<?php

namespace App\Http\Controllers\Kelola;

use App\Exports\GajiExport;
use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class GajiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_gaji', ['only' => ['index', 'show']]);
        $this->middleware('permission:add_kelola_gaji', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_gaji', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_gaji', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('kelola.gaji.index');
    }

    public function data()
    {
        $datas = Gaji::all();

        foreach ($datas as $data) {
            $options = '';
            $options .= '<a href="' . route('kelola-gaji.show', $data->id) . '" class="btn btn-primary">Detail</a>';
            $options .= "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-gaji.destroy', $data->id) . "`)' type='button'>
            Hapus
        </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    private function generateGaji($gajiId)
    {
        $gaji = Gaji::findOrFail($gajiId);
        $default = DB::table('settings')
            ->whereIn('id', [1, 3, 4, 5, 6, 7])
            ->get();

        $defaultFeeTransportDosen = (int) $default[0]->value;
        $defaultFeeTransportAsdos = (int) $default[1]->value;
        $defaultFeeSksTeoriDosen = (int) $default[2]->value;
        $defaultFeeSksTeoriAsdos = (int) $default[3]->value;
        $defaultFeeSksPraktekDosen = (int) $default[4]->value;
        $defaultFeeSksPraktekAsdos = (int) $default[5]->value;

        $pengajar = User::role(['dosen', 'asdos'])
            ->select('users.*', 'profile_dosens.nominal_tunjangan as tunjangan')
            ->leftJoin('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->leftJoin('profile_asdos', 'profile_asdos.user_id', 'users.id')
            ->where(function ($q) {
                $q->where('profile_dosens.status', '1')
                    ->orWhere('profile_asdos.status', '1');
            })
            ->with(['jadwalPengajar' => function ($q) use ($gaji) {
                $q->select('jadwal.*', 'matkuls.sks_mata_kuliah', 'matkul_materi.type as type_materi')
                    ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
                    ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
                    ->join('matkul_materi', 'matkul_materi.id', '=', 'jadwal.materi_id')
                    ->where('jadwal.tgl', '>=', $gaji->tgl_awal)
                    ->where('jadwal.tgl', '<=', $gaji->tgl_akhir);
            }, 'roles'])
            ->get();

        foreach ($pengajar as $item) {
            $feeTeoriSks = ($item->roles[0]->name == 'dosen') ? $defaultFeeSksTeoriDosen : $defaultFeeSksTeoriAsdos;
            $feePraktekSks = ($item->roles[0]->name == 'dosen') ? $defaultFeeSksPraktekDosen : $defaultFeeSksPraktekAsdos;
            $feeTransportasi = ($item->roles[0]->name == 'dosen') ? $defaultFeeTransportDosen : $defaultFeeTransportAsdos;
            $sumAllFeeSks = 0;
            $jadwalPengajar = $item->jadwalPengajar->groupBy('tahun_matkul_id');
            foreach ($jadwalPengajar as $matkul_id => $jadwal) {
                $sks = $jadwal->first()->sks_mata_kuliah;
                //Calculate Fee Teori
                $totalKehadiranTeori = $jadwal->where('type_materi', 'teori')->count();
                $totalFeeSksTeori = $totalKehadiranTeori * $sks * $feeTeoriSks;

                //Calculate Fee Praktek 
                $totalKehadiranPraktek = $jadwal->where('type_materi', 'praktek')->count();
                $totalFeeSksPraktek = $totalKehadiranPraktek * $sks * $feePraktekSks;

                $sumAllFeeSks += ($totalFeeSksTeori + $totalFeeSksPraktek);
                DB::table('gaji_matkul')
                    ->updateOrInsert([
                        'gaji_id' => $gajiId,
                        'user_id' => $item->id,
                        'tahun_matkul_id' => $matkul_id,
                    ], [
                        'total_kehadiran_teori' => $totalKehadiranTeori,
                        'total_kehadiran_praktek' => $totalKehadiranPraktek,
                        'sks' => $sks,
                        'fee_sks_teori' => $feeTeoriSks,
                        'total_fee_sks_teori' => $totalFeeSksTeori,
                        'fee_sks_praktek' => $feePraktekSks,
                        'total_fee_sks_praktek' => $totalFeeSksPraktek,
                        'total' => $totalFeeSksTeori + $totalFeeSksPraktek,
                    ]);
            }

            $total_fee_transport = ($feeTransportasi * $item->jadwalPengajar->count());
            $tunjangan = (int) $item->tunjangan ?? 0;
            DB::table('gaji_user')
                ->updateOrInsert([
                    'gaji_id' => $gajiId,
                    'user_id' => $item->id,
                ], [
                    'tunjangan' => $tunjangan,
                    'fee_transport' => $feeTransportasi,
                    'total_kehadiran' => $item->jadwalPengajar->count(),
                    'total_fee_transport' => $total_fee_transport,
                    'total_fee_matkul' => $sumAllFeeSks,
                    'total' => $total_fee_transport + $tunjangan + $sumAllFeeSks,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }

        return true;
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date'
        ]);

        $gaji = DB::table('gaji')
            ->insertGetId([
                'tgl_awal' => $request->tgl_awal,
                'tgl_akhir' => $request->tgl_akhir,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        $this->generateGaji($gaji);

        return response()->json([
            'message' => 'Berhasil digenerate'
        ], 200);
    }

    public function show($id)
    {
        $data = Gaji::findOrFail($id);
        return view('kelola.gaji.show', compact('data'));
    }

    public function dataDetail($gaji)
    {
        $datas = DB::table('gaji_user')
            ->select('gaji_user.*', 'users.name')
            ->join('users', 'users.id', 'gaji_user.user_id')
            ->where('gaji_user.gaji_id', $gaji)
            ->get();

        foreach ($datas as $data) {
            $options = '';
            $options .= '<a href="' . route('kelola-gaji.showMatkul', ['id' => $gaji, 'user_id' => $data->user_id]) . '" class="btn btn-primary">Gaji Matkul</a>';
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('tunjangan', function ($data) {
                return formatRupiah($data->tunjangan);
            })
            ->editColumn('fee_transport', function ($data) {
                return formatRupiah($data->fee_transport);
            })
            ->editColumn('total', function ($data) {
                return formatRupiah($data->total);
            })
            ->editColumn('total_fee_transport', function ($data) {
                return formatRupiah($data->total_fee_transport);
            })
            ->editColumn('total_fee_matkul', function ($data) {
                return formatRupiah($data->total_fee_matkul);
            })
            ->editColumn('fee_sks', function ($data) {
                return formatRupiah($data->fee_sks);
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function publish($id)
    {
        $data = Gaji::findOrFail($id);
        $data->status = '1';
        $data->save();
        return redirect()->back()->with('success', 'Berhasil di publish');
    }

    public function unpublish($id)
    {
        $data = Gaji::findOrFail($id);
        $data->status = '0';
        $data->save();
        return redirect()->back()->with('success', 'Berhasil di unpublish');
    }

    public function generateUlang($id)
    {
        $gaji = Gaji::findOrFail($id);
        if ($gaji->status == '1') {
            return response()->json([
                'message' => 'Gaji sudah dipublish!'
            ], 200);
        }

        $this->generateGaji($id);

        return response()->json([
            'message' => 'Berhasil digenerate ulang!'
        ], 200);
    }

    public function destroy($id)
    {
        $data = Gaji::findOrFail($id);

        if (
            $data->status == 1
        ) {
            return response()->json([
                'message' => 'Gaji sudah dipublish!'
            ], 400);
        }

        DB::table('gaji_matkul')
            ->where('gaji_id', $data->id)
            ->delete();

        DB::table('gaji_user')
            ->where('gaji_id', $data->id)
            ->delete();

        $data->delete();
        return response()->json([
            'message' => 'Berhasil di hapus'
        ], 200);
    }

    public function showMatkul($gaji, $user_id)
    {
        return view('kelola.gaji.showMatkul');
    }

    public function dataMatkul($gaji, $user_id)
    {
        $datas = DB::table('gaji_matkul')
            ->select('gaji_matkul.*', 'matkuls.nama as matkul')
            ->join('tahun_matkul', 'tahun_matkul.id', 'gaji_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('gaji_matkul.gaji_id', $gaji)
            ->where('gaji_matkul.user_id', $user_id)
            ->get();

        return DataTables::of($datas)
            ->editColumn('fee_sks_teori', function ($data) {
                return formatRupiah($data->fee_sks_teori);
            })
            ->editColumn('total_fee_sks_teori', function ($data) {
                return formatRupiah($data->total_fee_sks_teori);
            })
            ->editColumn('fee_sks_praktek', function ($data) {
                return formatRupiah($data->fee_sks_praktek);
            })
            ->editColumn('total_fee_sks_praktek', function ($data) {
                return formatRupiah($data->total_fee_sks_praktek);
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function export($gajiId){
        return Excel::download(new GajiExport($gajiId), 'gaji.xlsx');
    }
}
