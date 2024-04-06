<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{
    public function store(Request $request){
        $cek = $request->except(['_token', 'tahun_matkul_id', 'tahun_semester_id']);

        $kuesioner = DB::table('kuesioners')
        ->select('kuesioners.id')
        ->where('status', '1')
        ->get()
        ->pluck('id')
        ->toArray();
        
        if (in_array(null, $cek) || count(array_diff($kuesioner, array_keys($cek))) > 0) {
            return response()->json([
                'message' => 'Semua kolom harus diisi'
            ], 400);
        }

        $tKuesioner = DB::table('t_kuesioners')->insertGetId([
            'mhs_id' => auth()->user()->id,
            'tahun_semester_id' => $request->input('tahun_semester_id'),
            'tahun_matkul_id' => $request->input('tahun_matkul_id'),
        ]);

        foreach ($cek as $key => $value) {
            DB::table('t_kuesioners_answer')->insert([
                't_kuesioner_id' => $tKuesioner,
                'kuesioner_id' => $key,
                'answer' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
