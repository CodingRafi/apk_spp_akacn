<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatkulRequest extends FormRequest
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
            'kurikulum_id' => 'required',
            'prodi_id' => 'required',
            'kode' => 'required',
            'nama' => 'required',
            'sks_mata_kuliah' => 'required|min:0',
            'sks_tatap_muka' => 'min:0',
            'sks_praktek' => 'min:0',
            'sks_praktek_lapangan' => 'min:0',
            'sks_simulasi' => 'min:0',
        ];
    }
}
