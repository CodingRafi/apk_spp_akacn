<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Kuesioner;
use Illuminate\Http\Request;
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
                        onclick='editForm(`" . route('data-master.kuesioner.show', $data->id) . "`, `Edit Kuesioner`, `#kuesioner`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_kuesioner')) {
                $options .= "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kuesioner.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->editColumn('status', function ($datas) {
                return "<div class='form-check form-switch'>
                            <input class='form-check-input' type='checkbox' role='switch' name='status' value='1' " . ($datas->status ? 'checked' : '') . "
                                id='status' onclick='change_status(this, `" . route('data-master.kuesioner.change-status', $datas->id) . "`)'>
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

    public function show(Kuesioner $kuesioner)
    {
        //
    }

    public function update(Request $request, Kuesioner $kuesioner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kuesioner  $kuesioner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kuesioner $kuesioner)
    {
        //
    }
}
