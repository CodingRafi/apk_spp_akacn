<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Kuesioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KuesionerController extends Controller
{
    public function index()
    {
        return view('data_master.kuesioner.index');
    }

    public function data()
    {
        $datas =  Kuesioner::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_kuesioner')) {
                $options .= " <button class='btn btn-warning'
                        onclick='editForm(`" . route('kelola-kuesioner.template.show', $data->id) . "`, `Edit Kuesioner`, `#kuesioner`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kuesioner')) {
                $options .= "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('kelola-kuesioner.template.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('status', function ($datas) {
                return "<div class='form-check form-switch'>
                            <input class='form-check-input' type='checkbox' role='switch' name='status' value='1' " . ($datas->status ? 'checked' : '') . "
                                id='status' onclick='change_status(this, `" . route('kelola-kuesioner.template.change-status', $datas->id) . "`)'>
                        </div>";
            })
            ->editColumn('pertanyaan', function ($datas) {
                return substr($datas->pertanyaan, 0, 100);
            })
            ->addIndexColumn()
            ->rawColumns(['options', 'pertanyaan', 'status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:input,choice',
            'pertanyaan' => 'required',
        ]);

        Kuesioner::create($request->except('_token', '_method'));

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function change_status($id)
    {
        $data = Kuesioner::find($id);
        $data->update([
            'status' => ($data->status == "1") ? "0" : "1"
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate'
        ], 200);
    }

    public function show($id)
    {
        $kuesioner = Kuesioner::findOrFail($id);
        return response()->json([
            'data' => $kuesioner
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:input,choice',
            'pertanyaan' => 'required',
        ]);

        $kuesioner = Kuesioner::findOrFail($id);
        $kuesioner->update($request->except('_token', '_method'));

        return response()->json([
            'message' => 'Berhasil diubah'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kuesioner  $kuesioner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $kuesioner = Kuesioner::findOrFail($id);
            $kuesioner->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus',
            ], 400);
        }
    }

    public function storeMhs(Request $request){
        DB::beginTransaction();
        try {
            //? Cek udh pernah ngisi belom
            $checkSudahIsi = DB::table('t_kuesioners')
            ->where('mhs_id', auth()->user()->id)
            ->where('tahun_semester_id', $request->input('tahun_semester_id'))
            ->where('tahun_matkul_id', $request->input('tahun_matkul_id'))
            ->first();

            if ($checkSudahIsi) {
                return response()->json([
                    'message' => 'Anda sudah mengisi kuesioner ini'
                ], 400);
            }
            
            $cek = $request->except(['_token', 'tahun_matkul_id', 'tahun_semester_id']);

            $kuesioner = DB::table('kuesioners')
            ->select('kuesioners.id')
            ->where('status', '1')
            ->get()
            ->pluck('id')
            ->toArray();
            
            if (in_array(null, $cek) || count(array_diff($kuesioner, array_keys($cek))) > 0) {
                return response()->json([
                    'message' => 'Semua kolom harus diisi'
                ], 400);
            }

            $tKuesioner = DB::table('t_kuesioners')->insertGetId([
                'mhs_id' => auth()->user()->id,
                'tahun_semester_id' => $request->input('tahun_semester_id'),
                'tahun_matkul_id' => $request->input('tahun_matkul_id'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $answers = [];
            foreach ($cek as $key => $value) {
                $answers[] = [
                    't_kuesioner_id' => $tKuesioner,
                    'kuesioner_id' => $key,
                    'answer' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('t_kuesioners_answer')->insert($answers);
            DB::commit();

            return response()->json([
                'message' => 'Berhasil disimpan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan'
            ], 400);
        }
    }
}
