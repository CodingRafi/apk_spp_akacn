<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $validate = [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
        ];

        if (request('role') == 'mahasiswa') {
            $validate += [
                'nim' => 'required|unique:mahasiswas,nim',
                'nisn' => 'required|unique:mahasiswas,nisn',
                'nik' => 'required|unique:mahasiswas,nik',
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'kewarganegaraan_id' => 'required',
                'kelurahan' => 'required',
                'wilayah_id' => 'required',
                'penerima_kps' => 'required',
                'tahun_ajaran_id' => 'required',
            ];
        } else {
            $validate += [
                'nip' => 'required|unique:petugas,nip',
                'ttd' => 'required|file|max:1024|mimes:png,jpg,jpeg'
            ];
        }

        return $validate;
    }
}
