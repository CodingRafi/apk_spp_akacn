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
                'tahun_ajaran_id' => 'required',
                'nim' => 'required|unique:mahasiswas,nim'
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
