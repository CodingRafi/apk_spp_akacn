<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function getWilayah(){
        $data = Wilayah::where('negara_id', request('negara_id'))->get();
        return response()->json([
            'data' => $data
        ], 200);
    }
}
