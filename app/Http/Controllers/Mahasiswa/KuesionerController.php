<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuesionerController extends Controller
{
    public function store(Request $request){
        //? Cek udh pernah ngisi belom
        $checkSudahIsi = DB::table('t_kuesioners')
        ->where('mhs_id', auth()->user()->id)
        ->where('tahun_semester_id', $request->input('tahun_semester_id'))
        ->where('tahun_matkul_id', $request->input('tahun_matkul_id'))
        ->first();

        if ($checkSudahIsi) {
            return response()->json([
                'message' => 'Anda sudah mengisi kuesioner ini'
            ], 400);
        }
        
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
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $answers = [];
        foreach ($cek as $key => $value) {
            $answers[] = [
                't_kuesioner_id' => $tKuesioner,
                'kuesioner_id' => $key,
                'answer' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('t_kuesioners_answer')->insert($answers);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
