<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class DosenRequest extends FormRequest
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
        if ($this->method() == 'POST') {
            return [
                'name' => 'required',
                'email' => 'unique:users,email',
                'login_key' => 'required|unique:users,login_key',
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'kewarganegaraan_id' => 'required',
                'wilayah_id' => 'required',
                'nik' => 'unique:profile_dosens,nik',
                'nip' => 'nullable|unique:profile_dosens,nip',
                'npwp' => 'unique:profile_dosens,npwp',
                'rt' => 'digits:3',
                'rw' => 'digits:3',
                'profile' => 'file|mimes:png,jpg,jpeg|max:1024'
            ];
        }else{
            $dosen = DB::table('profile_dosens')
                            ->select('id')
                            ->where('user_id', $this->dosen)
                            ->first();
            return [
                'name' => 'required',
                'email' => 'unique:users,email,'.$this->dosen,
                'login_key' => 'required|unique:users,login_key,'.$this->dosen,
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'nik' => 'unique:profile_dosens,nik,'.$dosen->id,
                'nip' => 'nullable|unique:profile_dosens,nip,'.$dosen->id,
                'npwp' => 'unique:profile_dosens,npwp,'.$dosen->id,
                'rt' => 'digits:3',
                'rw' => 'digits:3',
                'kewarganegaraan_id' => 'required',
                'wilayah_id' => 'required',
                'profile' => 'file|mimes:png,jpg,jpeg|max:1024'
            ];
        }
    }

    public function messages()
    {
        return [
            'login_key.required' => 'NIDN wajib diisi',
            'login_key.unique' => 'NIDN sudah terdaftar'
        ];
    }
}
