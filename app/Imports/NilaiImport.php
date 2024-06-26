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
        $mutu = $this->mutu->pluck('id')->implode(',');
        return [
            '1' => 'required',
            '2' => 'required|numeric',
            '3' => 'required|numeric',
            '4' => 'required|numeric',
            '5' => 'required|numeric',
            '6' => 'required|numeric',
            '7' => 'required|numeric|in:' . $mutu,
            '8' => 'required|in:0,1'
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
            return $item->id == $row[7];
        })->first();
        
        if ($user && $nilai) {
            DB::table('mhs_nilai')
                ->updateOrInsert([
                    'mhs_id' => $user->id,
                    'tahun_semester_id' => $this->tahunSemesterId,
                    'tahun_matkul_id' => $this->tahunMatkulId,
                ], [
                    'presensi' => $row[2],
                    'tugas' => $row[3],
                    'uts' => $row[4],
                    'uas' => $row[5],
                    'nilai_akhir' => $row[6],
                    'mutu_id' => $row[7],
                    'nilai_mutu' => $nilai->nilai,
                    'publish' => (string) $row[8],
                    'jml_sks' => $this->matkul->sks_mata_kuliah,
                    'updated_at' => now()
                ]);
        }
    }
}
