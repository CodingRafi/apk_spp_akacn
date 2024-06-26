<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\TahunAjaranRequest;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TahunAjaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_tahun_ajaran', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_tahun_ajaran', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_tahun_ajaran', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_tahun_ajaran', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.tahun_ajaran.index');
    }

    public function data()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.tahun-ajaran.matkul.index', ['id' => $data->id]) . "' class='btn btn-info mx-2'>Mata Kuliah</a>";

            if (auth()->user()->can('edit_tahun_ajaran')) {
                $options = $options . "<a href='" . route('data-master.tahun-ajaran.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('status', function ($datas) {
                return $datas->status ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function create()
    {
        return view('data_master.tahun_ajaran.form');
    }

    public function store(TahunAjaranRequest $request)
    {
        abort(404);
        if ($request->status && getTahunAjaranActive()) {
            return response()->json([
                'message' => 'ada tahun ajaran yang sedang aktif'
            ], 400);
        }

        $data = TahunAjaran::create([
            'id' => explode('-', $request->tgl_mulai)[0],
            'nama' => $request->nama,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status ?? "0"
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'data' => $data
        ], 200);
    }

    public function edit($id)
    {
        $data = TahunAjaran::findOrFail($id);
        return view('data_master.tahun_ajaran.form', compact('data'));
    }

    public function update(TahunAjaranRequest $request, TahunAjaran $tahunAjaran)
    {
        abort(404);
        if ($request->status) {
            $tahun_active = getTahunAjaranActive();
            if ($tahun_active && $tahun_active->id !== $tahunAjaran->id) {
                return response()->json([
                    'message' => 'ada tahun ajaran yang sedang aktif'
                ], 400);
            }
        }

        $tahunAjaran->update([
            'nama' => $request->nama,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'status' => $request->status ?? "0"
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan',
            'data' => $tahunAjaran
        ]);
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        abort(404);
        DB::beginTransaction();
        try {
            $tahunAjaran->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus'
            ], 400);
        }
    }

    public function storeNeoFeeder(Request $request)
    {
        foreach ($request->data as $data) {
            DB::table('tahun_ajarans')->updateOrInsert([
                'id' => $data['id_tahun_ajaran'],
            ], [
                'nama' => $data['nama_tahun_ajaran'],
                'status' => $data['a_periode_aktif'],
                'tgl_mulai' => Carbon::parse($data['tanggal_mulai'])->format('Y-m-d'),
                'tgl_selesai' => Carbon::parse($data['tanggal_selesai'])->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
