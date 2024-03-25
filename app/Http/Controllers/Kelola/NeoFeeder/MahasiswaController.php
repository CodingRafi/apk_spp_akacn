<?php

namespace App\Http\Controllers\Kelola\NeoFeeder;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function store(Request $request){
        foreach ($request->data as $data) {
            DB::beginTransaction();
            try {
                $user = User::updateOrCreate([
                    'login_key' => $data
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'message' => $th->getMessage()
                ], 400);
            }
        }
    }
}
