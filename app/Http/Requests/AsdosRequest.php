<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsdosRequest extends FormRequest
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
                'rt' => 'digits:3',
                'rw' => 'digits:3',
            ];
        } else {
            return [
                'name' => 'required',
                'email' => 'unique:users,email,' . $this->asdo,
                'login_key' => 'required|unique:users,login_key,' . $this->asdo,
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'rt' => 'digits:3',
                'rw' => 'digits:3',
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
