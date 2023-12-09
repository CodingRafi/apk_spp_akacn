<?php

namespace App\Http\Controllers;

use DataTables, DB;
use App\Models\{
    TahunAjaran,
    Semester,
    Prodi
};
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function data($prodi_id){
        $datas = Semester::where('prodi_id', $prodi_id)->get();

        foreach ($datas as $data) {
                $options = '';

                $options = $options ."<a href='". route('data-master.prodi.semester.show', ['prodi_id' => $prodi_id, 'semester_id' => $data->id]) ."' class='btn btn-info mx-2'>Set Biaya</a>";

                if (auth()->user()->can('edit_semester')) {
                    $options = $options ."<a href='". route('data-master.prodi.semester.edit', ['prodi_id' => $prodi_id, 'semester_id' => $data->id]) ."' class='btn btn-warning mx-2'>Edit</a>";
                }
                
                if (auth()->user()->can('delete_semester')) {
                    $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`". route('data-master.prodi.semester.destroy', ['prodi_id' => $prodi_id, 'semester_id' => $data->id]) ."`)'>
                                        Hapus
                                    </button>";

                $data->options = $options;
            }
        }

        return DataTables::of($datas)
                            ->addIndexColumn()
                            ->rawColumns(['options'])
                            ->make(true);
    }

    public function create($prodi_id)
    {
        return view('data_master.prodi.semester.form', compact('prodi_id'));
    }

    public function store(Request $request, $prodi_id)
    {
        $request->validate(['nama' => 'required']);
        Semester::create([
            'nama' => $request->nama,
            'prodi_id' => $prodi_id
        ]);
        return redirect()->route('data-master.prodi.show', $prodi_id)->with('success', 'Berhasil ditambahkan');
    }

    public function show($prodi_id, $semester_id)
    {
        $data = Semester::findOrFail($semester_id);
        if ($data->prodi_id != $prodi_id) {
            abort(403);
        }

        $prodi = Prodi::findOrFail($prodi_id);

        return view('data_master.prodi.semester.show', compact('data', 'prodi'));
    }

    public function edit($prodi_id, $semester_id)
    {
        $data = Semester::findOrFail($semester_id);
        if ($data->prodi_id != $prodi_id) {
            abort(403);
        }

        return view('data_master.prodi.semester.form', compact('data', 'prodi_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $prodi_id, $semester_id)
    {
        $request->validate(['nama' => 'required']);
        $data = Semester::findOrFail($semester_id);
        if ($data->prodi_id != $prodi_id) {
            abort(403);
        }
        $data->update(['nama' => $request->nama]);
        return redirect()->route('data-master.prodi.show', $prodi_id)->with('success', 'Berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function destroy(Semester $semester)
    {
        //
    }
}
