<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class TahunAjaranRequest extends FormRequest
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
            'tgl_mulai' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $startYear = date('Y', strtotime($value));
                    $cek = DB::table('tahun_ajarans')->where('id', $startYear)->first();
                    if ($cek && ($this->method() == 'POST' || ($this->method() == 'PUT' && $cek->id != request('tahun_ajaran')))) {
                        $fail('Tahun Ajaran ini sudah ada');
                    }
                }
            ],
            'tgl_selesai' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $startYear = date('Y', strtotime($this->input('tgl_mulai')));
                    $endYear = date('Y', strtotime($value));
                    if ($endYear <= $startYear) {
                        $fail('The end year must be greater than the start year.');
                    }
                },
            ],
            'nama' => 'required'
        ];
    }
}
