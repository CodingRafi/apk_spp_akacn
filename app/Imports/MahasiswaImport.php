<?php

namespace App\Imports;

use App\Models\{
    User,
    Mahasiswa
};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToCollection, WithValidation, WithStartRow
{
    private $tahunAjaranId;
    private $prodiId;

    public function __construct($tahunAjaranId, $prodiId){
        $this->tahunAjaranId = $tahunAjaranId;
        $this->prodiId = $prodiId;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $data) {
            $user = User::create([
                'name' => $data[0],
                'email' => $data[1],
                'password' => bcrypt('000000')
            ]);

            $user->assignRole('mahasiswa');

            Mahasiswa::create([
                'nim' => $data[2],
                'user_id' => $user->id,
                'prodi_id' => $this->prodiId,
                'tahun_ajaran_id' => $this->tahunAjaranId
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '1' => 'unique:users,email',
            '2' => 'unique:mahasiswas,nim',
        ];
    }
}
