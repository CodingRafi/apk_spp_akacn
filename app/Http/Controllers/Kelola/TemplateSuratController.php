<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $roles = Role::where('name', '!=', 'admin')->get()->pluck('name', 'id');
        return view('data_master.template_surat.index', compact('roles'));
    }

    public function data()
    {
        $datas = TemplateSurat::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_template_surat')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.template-surat.show', $data->id) . "`, `Edit Template Surat`, `#template_surat`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_template_surat')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.template-surat.destroy', $data->id) . "`)' type='button'>
                                        Hapus
                                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('file', function($datas){
                return '<a href="' . asset('storage/' . $datas->path) . '" target="_blank">Lihat</a>';
            })
            ->addColumn('role', function($datas){
                return $datas->roles->pluck('name')->implode(', ');
            })
            ->rawColumns(['options', 'file'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'role_id' => 'required',
            'file' => 'required|file|max:1024|mimes:pdf',
        ]);

        $path = $request->file('file')->store('template_surat');

        $data = TemplateSurat::create([
            'nama' => $request->nama,
            'path' => $path,
        ]);

        $data->roles()->sync($request->role_id);

        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    public function show($id)
    {
        $data = TemplateSurat::find($id);
        $data->role_id = $data->roles->pluck('id')->toArray();
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'role_id' => 'required',
            'file' => 'file|max:1024|mimes:pdf',
        ]);

        $dataRequest = [
            'nama' => $request->nama
        ];

        if ($request->file('file')) {
            $path = $request->file('file')->store('template_surat');
            $dataRequest['path'] = $path;
        }

        $data = TemplateSurat::find($id);
        $data->update($dataRequest);
        $data->roles()->sync($request->role_id);
        return response()->json([
            'message' => 'Berhasil diupdate!'
        ], 200);
    }

    public function destroy($id)
    {
        $data = TemplateSurat::find($id);
        $data->roles()->detach();
        $data->delete();
        return response()->json([
            'message' => 'Berhasil dihapus!'
        ], 200);
    }
}
