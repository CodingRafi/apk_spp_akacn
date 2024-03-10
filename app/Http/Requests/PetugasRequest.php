<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetugasRequest extends FormRequest
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
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'rt' => 'digits:3',
                'rw' => 'digits:3',
                'profile' => 'file|mimes:png,jpg,jpeg|max:1024',
                'ttd' => 'file|mimes:png,jpg,jpeg|max:1024'
            ];
        } else {
            return [
                'name' => 'required',
                'email' => 'unique:users,email,' . $this->petuga,
                'tempat_lahir' => 'required',
                'tgl_lahir' => 'required',
                'agama_id' => 'required',
                'rt' => 'digits:3',
                'rw' => 'digits:3',
                'profile' => 'file|mimes:png,jpg,jpeg|max:1024',
                'ttd' => 'file|mimes:png,jpg,jpeg|max:1024',
            ];
        }
    }
}
