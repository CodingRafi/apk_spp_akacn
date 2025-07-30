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

        if (!$user) {
            return;
        }

        $namaNilai = isset($row[3]) ? trim(strtolower($row[3])) : null;
        $nilai = $namaNilai
            ? $this->mutu->first(function ($item) use ($namaNilai) {
                return trim(strtolower($item->nama)) === $namaNilai;
            })
            : null;

        DB::table('mhs_nilai')->updateOrInsert(
            [
                'mhs_id' => $user->id,
                'tahun_semester_id' => $this->tahunSemesterId,
                'tahun_matkul_id' => $this->tahunMatkulId,
            ],
            [
                'mutu_id' => $nilai->id ?? null,
                'nilai_mutu' => $nilai->nilai ?? null,
                'nilai_akhir' => $row[2] ?? null,
                'aktivitas_partisipatif' => $row[4] ?? null,
                'hasil_proyek' => $row[5] ?? null,
                'quizz' => $row[6] ?? null,
                'tugas' => $row[7] ?? null,
                'uts' => $row[8] ?? null,
                'uas' => $row[9] ?? null,
                'publish' => (string) $row[10] ?? 0,
                'jml_sks' => $this->matkul->sks_mata_kuliah,
                'updated_at' => now(),
            ]
        );
    }
}
