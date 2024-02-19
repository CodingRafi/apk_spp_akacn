<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class MahasiswaRequest extends FormRequest
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
        $validate =  [
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required',
            'agama_id' => 'required',
            'kewarganegaraan_id' => 'required',
            'kelurahan' => 'required',
            'penerima_kps' => 'required',
            'tahun_masuk_id' => 'required',
            'wilayah_id' => 'required',
            'prodi_id' => 'required',
            'rombel_id' => 'required',
            'rt' => 'digits:3',
            'rw' => 'digits:3',
        ];

        if ($this->method() == 'POST') {
            $validate += [
                'login_key' => 'required|unique:users,login_key',
                'nisn' => 'required|unique:profile_mahasiswas,nisn',
                'nik' => 'required|unique:profile_mahasiswas,nik',
            ];
        } else {
            $mhs = DB::table('profile_mahasiswas')->where('user_id', $this->mahasiswa)->first();
            $validate += [
                'login_key' => 'required|unique:users,login_key,'.$this->mahasiswa,
                'nisn' => 'required|unique:profile_mahasiswas,nisn,'.$mhs->id,
                'nik' => 'required|unique:profile_mahasiswas,nik,'.$mhs->id,
            ];
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'login_key.unique' => 'NIM sudah terdaftar',
            'login_key.required' => 'NIM tidak boleh kosong',
        ];
    }
}
