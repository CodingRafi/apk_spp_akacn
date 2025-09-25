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
        $validate = [
            'name' => 'required',
            'rt' => 'nullable|digits:3',
            'rw' => 'nullable|digits:3',
            'profile' => 'file|mimes:png,jpg,jpeg|max:1024',
        ];

        $role = getRole();
        if ($role->name == 'admin') {
            $validate += ['dosen_id' => 'required'];
        }

        if ($this->method() == 'POST') {
            $validate += [
                'email' => 'required|unique:users,email',
                'login_key' => 'required|unique:users,login_key'
            ];
        } else {
            $validate += [
                'email' => 'required|unique:users,email,' . $this->asisten,
                'login_key' => 'required|unique:users,login_key,' . $this->asisten
            ];
        }

        return $validate;
    }

    public function messages()
    {
        return [
            'login_key.required' => 'NIDN wajib diisi',
            'login_key.unique' => 'NIDN sudah terdaftar'
        ];
    }
}
