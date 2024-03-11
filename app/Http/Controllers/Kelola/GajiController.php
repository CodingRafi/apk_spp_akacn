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

    public function store(Request $request)
    {
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date'
        ]);

        $getDefaultFeeTransport = DB::table('settings')->where('id', 1)->first();
        $defaultFeeTransport = (int) $getDefaultFeeTransport->value;
        $pengajar = User::role(['dosen', 'asdos'])
            ->select('users.*', 'profile_dosens.nominal_tunjangan as tunjangan')
            ->leftJoin('profile_dosens', 'profile_dosens.user_id', 'users.id')
            ->leftJoin('profile_asdos', 'profile_asdos.user_id', 'users.id')
            ->where(function ($q) {
                $q->where('profile_dosens.status', '1')
                    ->orWhere('profile_asdos.status', '1');
            })
            ->with(['jadwalPengajar' => function ($q) use ($request) {
                $q->where('tgl', '>=', $request->tgl_awal)
                    ->where('tgl', '<=', $request->tgl_akhir);
            }])
            ->get()
            ->map(function ($data) {
                $data->total_kehadiran = $data->jadwalPengajar->count();
                return $data;
            });

        $gaji = DB::table('gaji')
            ->insertGetId([
                'tgl_awal' => $request->tgl_awal,
                'tgl_akhir' => $request->tgl_akhir,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        foreach ($pengajar as $item) {
            $total_fee_transport = ($defaultFeeTransport * $item->total_kehadiran);
            $tunjangan = (int) $item->tunjangan ?? 0;
            DB::table('gaji_user')
                ->insert([
                    'gaji_id' => $gaji,
                    'user_id' => $item->id,
                    'tunjangan' => $tunjangan,
                    'fee_transport' => $defaultFeeTransport,
                    'total_kehadiran' => $item->total_kehadiran,
                    'total_fee_transport' => $total_fee_transport,
                    'total' => $total_fee_transport + $tunjangan,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }

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

        DB::table('gaji_user')
            ->where('gaji_id', $data->id)
            ->delete();
            
        $data->delete();
        return response()->json([
            'message' => 'Berhasil di hapus'
        ], 200);
    }
}
