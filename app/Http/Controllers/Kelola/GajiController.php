<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class GajiController extends Controller
{
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
                    ->whereIn('id', [1,3,4,5])
                    ->get();

        $defaultFeeTransportDosen = (int) $default[0]->value;
        $defaultFeeTransportAsdos = (int) $default[1]->value;
        $defaultFeeSksDosen = (int) $default[2]->value;
        $defaultFeeSksAsdos = (int) $default[3]->value;

        $pengajar = User::role(['dosen', 'asdos'])
            ->select('users.*', 'profile_dosens.nominal_tunjangan as tunjangan')
            ->leftJoin('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->leftJoin('profile_asdos', 'profile_asdos.user_id', 'users.id')
            ->where(function ($q) {
                $q->where('profile_dosens.status', '1')
                    ->orWhere('profile_asdos.status', '1');
            })
            ->with(['jadwalPengajar' => function ($q) use ($gaji) {
                $q->select('jadwal.*', 'matkuls.sks_mata_kuliah')
                    ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
                    ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
                    ->where('jadwal.tgl', '>=', $gaji->tgl_awal)
                    ->where('jadwal.tgl', '<=', $gaji->tgl_akhir);
            }, 'roles'])
            ->get();
            
        foreach ($pengajar as $item) {
            $feeSks = ($item->roles[0]->name == 'dosen') ? $defaultFeeSksDosen : $defaultFeeSksAsdos;
            $feeTransportasi = ($item->roles[0]->name == 'dosen') ? $defaultFeeTransportDosen : $defaultFeeTransportAsdos;
            $sumAllFeeSks = 0;
            $jadwalPengajar = $item->jadwalPengajar->groupBy('tahun_matkul_id');
            foreach ($jadwalPengajar as $matkul_id => $jadwal) {
                $totalKehadiran = $jadwal->count();
                $sks = $jadwal->first()->sks_mata_kuliah;
                $totalFeeSks = $totalKehadiran * $sks * $feeSks;
                $sumAllFeeSks += $totalFeeSks;
                DB::table('gaji_matkul')
                    ->updateOrInsert([
                        'gaji_id' => $gajiId,
                        'user_id' => $item->id,
                        'tahun_matkul_id' => $matkul_id,
                    ], [
                        'total_kehadiran' => $totalKehadiran,
                        'sks' => $sks,
                        'fee_sks' => $feeSks,
                        'total_fee_sks' => $totalFeeSks,
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
                    'fee_sks' => $feeSks,
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
        $data = DB::table('gaji_user')
            ->select('gaji_user.*', 'users.name')
            ->join('users', 'users.id', 'gaji_user.user_id')
            ->where('gaji_user.gaji_id', $gaji)
            ->get();
        
        return DataTables::of($data)
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
}
