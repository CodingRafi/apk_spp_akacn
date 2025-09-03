<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class EvaluasiNeoFeederController extends Controller
{
    private function getToken()
    {
        $userId = config('services.neo_feeder.USER_ID');
        $urlNeoFeeder = getUrlNeoFeeder();

        $response = Http::post($urlNeoFeeder . '/ws/user/update_token', [
            "userid" => $userId
        ]);
        return $response->json();
    }

    private function convertKomponenEvaluasi($komponen_evaluasi)
    {
        switch ($komponen_evaluasi) {
            case 'UTS':
                return 'Ujian Tengah Semester';
                break;
            case 'UAS':
                return 'Ujian Akhir Semester';
                break;
            case 'TGS':
                return 'Tugas';
                break;
            case 'QIZ':
                return 'Quiz';
                break;
            default:
                return '-';
        }
    }

    public function index($id_kelas_kuliah)
    {
        $token = $this->getToken();
        $urlNeoFeeder = getUrlNeoFeeder();

        try {
            $response = Http::withHeaders([
                'authorization' => 'Bearer ' . $token,
            ])
                ->get($urlNeoFeeder . '/ws/kelas/editrencanaevaluasi/' . $id_kelas_kuliah);

            $response = $response->json();

            foreach ($response as $value) {
                DB::table('kelas_kuliah_evaluasi')->updateOrInsert(
                    [
                        'id_kelas_kuliah' => $id_kelas_kuliah,
                        'id_jns_eval' => $value['id_jns_eval']
                    ],
                    [
                        'id_komp_eval'      => $value['id_komp_eval'],
                        'nm_jns_eval'       => $value['nm_jns_eval'],
                        'komponen_evaluasi' => $this->convertKomponenEvaluasi($value['nama']),
                        'nama_inggris'      => $value['nama_inggris'],
                        'bobot'             => $value['bobot'],
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ]
                );
            }

            return response()->json([
                'message' => 'Berhasil Get Data'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Maaf, Terjadi Kesalahan'
            ], 500);
        }
    }

    public function data($id_kelas_kuliah){
        $datas = DB::table('kelas_kuliah_evaluasi')
            ->where('id_kelas_kuliah', $id_kelas_kuliah)
            ->get();

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }
}
