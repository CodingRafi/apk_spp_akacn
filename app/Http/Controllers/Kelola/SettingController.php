<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_setting', ['only' => ['index', 'data', 'show']]);
        $this->middleware('permission:edit_setting', ['only' => ['update']]);
    }

    public function index()
    {
        return view('setting.index');
    }

    public function data()
    {
        $datas = Setting::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_setting')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('setting.show', $data->id) . "`, `Edit Setting`, `#setting`, editSetting)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($id)
    {
        $data = DB::table('settings')->where('id', $id)->first();
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        DB::table('settings')
            ->where('id', $id)
            ->update([
                'nama' => $request->nama,
                'value' => $request->value,
            ]);

        return response()->json([
            'message' => 'Berhasil diubah
            !'
        ], 200);
    }
}
