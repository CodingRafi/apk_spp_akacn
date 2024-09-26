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
        return view('data_master_neo_feeder.index', compact('type'));
    }

    public function store(Request $request)
    {
        DB::table($request->tbl)->update([
            'active' => '0'
        ]);
        
        foreach ($request->data as $data) {
            $dataField = $data[1];
            $dataField['active'] = 1;
            DB::table($request->tbl)->updateOrInsert($data[0], $dataField);
        }

        return response()->json([
            'message' => 'Berhasil di get'
        ], 200);
    }

    public function data($type)
    {
        $table = str_replace("-", "_", $type);
        $datas = DB::table("{$table}")->get();
        return response()->json($datas, 200);
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
