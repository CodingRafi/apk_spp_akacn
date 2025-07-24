<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NilaiImport implements ToModel, WithValidation, WithStartRow
{
    protected $matkul;
    protected $mutu;
    protected $tahunSemesterId;
    protected $tahunMatkulId;

    public function __construct(
        $matkul,
        $mutu,
        $tahunSemesterId,
        $tahunMatkulId,
    ) {
        $this->matkul = $matkul;
        $this->mutu = $mutu;
        $this->tahunSemesterId = $tahunSemesterId;
        $this->tahunMatkulId = $tahunMatkulId;
    }

    public function rules(): array
    {
        return [
            '0' => 'required',
            '1' => 'required|numeric',
            '2' => 'required|numeric',
            '3' => 'required',
            '4' => 'required|numeric',
            '5' => 'required|numeric',
            '6' => 'required|numeric',
            '7' => 'required|numeric',
            '8' => 'required|numeric',
            '9' => 'required|numeric',
            '10' => 'required|numeric',
            '11' => 'required|numeric',
            '12' => 'required|in:0,1'
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $user = DB::table('users')
            ->where('login_key', $row[1])
            ->first();

        $nilai = $this->mutu->filter(function ($item) use ($row) {
            return trim(strtolower($item->nama)) == trim(strtolower($row[3]));
        })->first();
        
        if ($user && $nilai) {
            DB::table('mhs_nilai')
                ->updateOrInsert([
                    'mhs_id' => $user->id,
                    'tahun_semester_id' => $this->tahunSemesterId,
                    'tahun_matkul_id' => $this->tahunMatkulId,
                ], [
                    'mutu_id' => $nilai->id,
                    'nilai_mutu' => $nilai->nilai,
                    'nilai_angka' => $row[2],
                    'presensi' => $row[4],
                    'aktivitas_partisipatif' => $row[5],
                    'hasil_proyek' => $row[6],
                    'quizz' => $row[7],
                    'tugas' => $row[8],
                    'uts' => $row[9],
                    'uas' => $row[10],
                    'nilai_akhir' => $row[11],
                    'publish' => (string) $row[12],
                    'jml_sks' => $this->matkul->sks_mata_kuliah,
                    'updated_at' => now()
                ]);
        }
    }
}
