<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function getWilayah(){
        $data = Wilayah::where('negara_id', request('negara_id'))
                    ->when(request('negara_id') == 'ID', function($q){
                        $q->select('id', 'fullNama as nama')
                        ->where('id_level_wilayah', 3);
                    })
                    ->get();
        return response()->json([
            'data' => $data
        ], 200);
    }
}
