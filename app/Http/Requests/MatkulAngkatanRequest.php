<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatkulAngkatanRequest extends FormRequest
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
            'matkul_id' => 'required',
            'dosen_id' => 'required',
            'ruang_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_akhir' => 'required|date_format:H:i|after:jam_mulai',
        ];
    }
}
