<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KalenderAkademikController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_kelola_kalender_akademik', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_kelola_kalender_akademik', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_kalender_akademik', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_kalender_akademik', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.kalender_akademik.index');
    }

    public function data()
    {
        $datas = KalenderAkademik::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <a class='btn btn-info'
                        href='" . route('data-master.kalender-akademik-detail.index', $data->id) . "'>
                        Detail
                    </a>";

            if (auth()->user()->can('edit_kelola_kalender_akademik')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.kalender-akademik.show', $data->id) . "`, `Edit Kalender Akademik`, `#kalender_akademik`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kelola_kalender_akademik')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kalender-akademik.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('is_active', function ($datas) {
                return $datas->is_active ? "<i class='bx bx-check text-success'></i>" :
                    "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'is_active'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        if($request->is_active){
            $adaKalenderAkademikAktif = KalenderAkademik::where('is_active', 1)->exists();

            if ($adaKalenderAkademikAktif) {
                return response()->json([
                    'message' => 'Terdapat kalender akademik yang aktif'
                ], 400);
            }
        }

        KalenderAkademik::create([
            'nama' => $request->nama,
            'is_active' => $request->is_active ?? 0
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    public function show(KalenderAkademik $kalenderAkademik)
    {
        return response()->json([
            'data' => $kalenderAkademik
        ], 200);
    }

    public function update(Request $request, KalenderAkademik $kalenderAkademik)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        if($request->is_active){
            $adaKalenderAkademikAktif = KalenderAkademik::where('is_active', 1)
                ->where('id', '!=', $kalenderAkademik->id)
                ->exists();

            if ($adaKalenderAkademikAktif) {
                return response()->json([
                    'message' => 'Terdapat kalender akademik yang aktif'
                ], 400);
            }
        }

        $kalenderAkademik->update([
            'nama' => $request->nama,
            'is_active' => $request->is_active ?? 0
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate!'
        ], 200);
    }

    public function destroy(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->delete();
        
        return response()->json([
            'message' => 'Berhasil dihapus!'
        ], 200);
    }
}
