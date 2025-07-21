<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use App\Models\KalenderAkademikDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KalenderAkademikDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_kalender_akademik', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_kelola_kalender_akademik', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_kalender_akademik', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_kalender_akademik', ['only' => ['destroy']]);
    }

    public function index($kalender_akademik_id)
    {
        $data = KalenderAkademik::find($kalender_akademik_id);
        return view('data_master.kalender_akademik_detail.index', compact('data'));
    }

    public function data($kalender_akademik_id){
        $datas = KalenderAkademikDetail::where('kalender_akademik_id', $kalender_akademik_id)->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kelola_kalender_akademik')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.kalender-akademik-detail.show', ['kalender_akademik_id' => $kalender_akademik_id, 'kalender_akademik_detail_id' => $data->id]) . "`, `Edit Kalender Akademik`, `#kalender_akademik`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kelola_kalender_akademik')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kalender-akademik-detail.destroy', ['kalender_akademik_id' => $kalender_akademik_id, 'kalender_akademik_detail_id' => $data->id]) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }


    public function store(Request $request, $kalender_akademik_id)
    {
        $request->validate([
            'tgl' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            KalenderAkademikDetail::create([
                'kalender_akademik_id' => $kalender_akademik_id,
                'tgl' => $request->tgl,
                'ket' => $request->ket,
                'created_at' => now(),
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($kalender_akademik_id, $id)
    {
        $data = KalenderAkademikDetail::find($id);
        return response()->json([
            'data' => $data
        ]);
    }

    public function update(Request $request, $kalender_akademik_id, $id)
    {
        $request->validate([
            'tgl' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            KalenderAkademikDetail::where('id', $id)->update([
                'tgl' => $request->tgl,
                'ket' => $request->ket,
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil diubah'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy($kalender_akademik_id, $id)
    {
        DB::beginTransaction();
        try {
            KalenderAkademikDetail::where('id', $id)->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
