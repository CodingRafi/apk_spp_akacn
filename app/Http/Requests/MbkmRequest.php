<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MbkmRequest extends FormRequest
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
            'jenis_anggota' => 'required',
            'jenis_aktivitas_id' => 'required',
            'tahun_semester_id' => 'required',
            'judul' => 'required',
        ];
    }
}
