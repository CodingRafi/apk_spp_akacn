<?php

namespace App\Http\Controllers;

use App\Models\WhitelistIP;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WhitelistIPController extends Controller
{
    public function index()
    {
        return view('data_master.whitelist_ip.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip' => 'required',
            'nama' => 'required'
        ]);

        WhitelistIP::create([
            'nama' => $request->nama,
            'ip' => $request->ip,
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function get_ip(Request $request)
    {
        return response()->json([
            'message' => 'success',
            'ip' => $request->ip()
        ], 200);
    }

    public function data()
    {
        $datas = WhitelistIP::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('delete_whitelist_ip')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('kelola-presensi.whitelist-ip.destroy', $data->id) . "`)'>
                                    Hapus
                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function destroy($id)
    {
        $data = WhitelistIP::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Berhasil dihapus');
    }
}
