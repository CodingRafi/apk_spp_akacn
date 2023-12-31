<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Pembayaran;

class HomeController extends Controller
{
    public function upload_file(Request $request){
        $file = $request->file('file')->store('file_config');
        return response()->json([
            'location' => asset('storage/' . $file)
        ], 200);
    }
}
