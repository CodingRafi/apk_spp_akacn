<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Exports\TranskipExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TranskipController extends Controller
{
    public function index()
    {
        return view('mahasiswa.transkip.index');
    }

    private function getTranskip($user)
    {
        $loop = [];
        $datas = DB::table('rekap_krs_matkul')
            ->select(
                'rekap_krs_matkul.*',
                'semesters.nama as semester',
                'matkuls.kode as kode_mk',
                'matkuls.nama as matkul'
            )
            ->join('tahun_semester', 'rekap_krs_matkul.tahun_semester_id', '=', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
            ->join('tahun_matkul', 'rekap_krs_matkul.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->where('mhs_id', $user->id)
            ->get()
            ->groupBy('semester');

        foreach ($datas as $indexSemester =>  $data) {
            foreach ($data as $indexMatkul => $matkul) {
                $cek = array_filter($loop, function ($item) use ($matkul) {
                    return $item['tahun_matkul_id'] == $matkul->tahun_matkul_id;
                });

                if (!empty($cek)) {
                    if ($matkul->nilai_mutu > $cek[0]['nilai_mutu']) {
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->jml_sks = $matkul->jml_sks;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->mutu = $matkul->mutu;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->nilai_mutu = $matkul->nilai_mutu;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->bobot_x_sks = $matkul->bobot_x_sks;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->kuesioner = $matkul->kuesioner;
                        $datas[$cek[0]['index_semester']][$cek[0]['index_matkul']]->status = $matkul->status;
                    }
                    unset($datas[$indexSemester][$indexMatkul]);
                } else {
                    $loop[] = [
                        'tahun_matkul_id' => $matkul->tahun_matkul_id,
                        'nilai_mutu' => $matkul->nilai_mutu,
                        'index_semester' => $indexSemester,
                        'index_matkul' => $indexMatkul
                    ];
                }
            }
        }

        return $datas;
    }

    public function data()
    {
        $user = Auth::user();
        $data = $this->getTranskip($user);

        return response()->json([
            'data' => $data
        ], 200);
    }

    private function generateIPK($datas)
    {
        $mutu_x_sks = [];
        $sks = [];
        $result = [];

        foreach ($datas as $key => $data) {
            $jml_sks_semester = 0;
            foreach ($data as $row) {
                if ($row->status == 1 && $row->kuesioner != null) {
                    $mutu_x_sks[] = $row->bobot_x_sks;
                    $sks[] = $row->jml_sks;
                    $jml_sks_semester += $row->jml_sks;
                } else {
                    $mutu_x_sks[] = 0;
                    $sks[] = 0;
                }
            }

            $result[$key] = [
                'ipk' => array_sum($mutu_x_sks) / array_sum($sks),
                'sks' => $jml_sks_semester
            ];
        }

        return $result;
    }

    private function generateExportTranskip(){
        $user = Auth::user();
        $rekap = $this->getTranskip($user);
        $ipk = $this->generateIPK($rekap);

        $data = DB::table('users')
            ->select(
                'users.name',
                'users.login_key as nim',
                'prodi.nama as prodi',
                'profile_mahasiswas.tahun_masuk_id as angkatan',
                'profile_mahasiswas.rombel_id',
                'profile_mahasiswas.prodi_id',
                'jenjangs.nama as jenjang',
                'profile_mahasiswas.tempat_lahir',
                'profile_mahasiswas.tgl_lahir',
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('prodi', 'prodi.id', 'profile_mahasiswas.prodi_id')
            ->join('jenjangs', 'jenjangs.id', 'prodi.jenjang_id')
            ->where('users.id', $user->id)
            ->first();

        $getRombel = getRombelMhs($data->prodi_id, $data->angkatan, $data->rombel_id);

        $data->rombel = $getRombel['nama'];
        $data->dosenPa = $getRombel['dosen_pa'];

        $totalSKS = array_reduce($ipk, function ($carry, $item) {
            return $carry + $item['sks'];
        }, 0);

        return [
            'data' => $data,
            'rekap' => $rekap,
            'ipk' => $ipk,
            'totalSKS' => $totalSKS,
        ];
    }

    public function print()
    {
        $data = $this->generateExportTranskip();
        
        return Pdf::loadView('mahasiswa.transkip.print', $data)
            ->stream('transkip.pdf');
    }

    public function export(){
        $data = $this->generateExportTranskip();

        return Excel::download(new TranskipExport($data), 'transkip.xlsx');
    }
}
