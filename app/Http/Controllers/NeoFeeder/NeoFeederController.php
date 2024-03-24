<?php

namespace App\Http\Controllers\NeoFeeder;

use App\Http\Controllers\Controller;
use App\Models\Agama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NeoFeederController extends Controller
{
    public function index($type)
    {
        $url = DB::table('settings')
            ->where('id', 2)
            ->first();
        return view('data_master_neo_feeder.index', compact('type', 'url'));
    }

    public function store(Request $request)
    {
        foreach ($request->data as $data) {
            DB::table('agamas')->updateOrInsert([
                'id' => $data['id'],
            ], [
                'nama' => $data['nama'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil di get'
        ], 200);
    }

    public function data($type)
    {
        $table = str_replace("-", "_", $type);
        $datas = DB::table("{$table}s")->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function get($type)
    {
        $result = Artisan::call("neo-feeder:get-{$type}");
        $output = Artisan::output();

        if ($result) {
            return response()->json([
                'output' => $output
            ], 400);
        } else {
            return response()->json([
                'output' => $output
            ], 200);
        }
    }

    public function indexWilayah()
    {
        return view('data_master_neo_feeder.wilayah');
    }

    public function dataWilayah()
    {
        $datas = DB::table("wilayahs")
            ->select('wilayahs.*', 'kewarganegaraans.nama AS kewarganegaraan', 'kewarganegaraans.id AS kewarganegaraan_id')
            ->join('kewarganegaraans', 'kewarganegaraans.id', '=', 'wilayahs.negara_id')
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }
}
