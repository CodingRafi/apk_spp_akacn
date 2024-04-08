<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeApiMhsRequest extends FormRequest
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
        return [
            'nama' => 'required',
            'nim' => 'required|unique:users,login_key',
            'email' => 'required|email',
            'nisn' => 'required',
            'nik' => 'required',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required',
            'jk' => 'required',
            
            //?alamat
            'kewarganegaraan_id' => 'required',
            'wilayah_id' => 'required',
            'jalan' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'dusun' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required',

            //?ibu
            'nama_ibu' => 'required',

            'agama_id' => 'required',
            'rombel_id' => 'required',
            'prodi_id' => 'required',
            'tahun_masuk_id' => 'required',
            'semester_id' => 'required',
            'jalur_masuk_id' => 'required',
            'jenis_pembiayaan_id' => 'required',
            'jenis_kelas_id' => 'required'
        ];
    }
}
