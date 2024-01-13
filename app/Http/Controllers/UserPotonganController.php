<?php

namespace App\Http\Controllers;

use App\Models\{
    User
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPotonganController extends Controller
{
    public function __construct()
    {
        if (request('role') !== 'mahasiswa') {
            abort(404);
        }
    }

    public function index($role, $id)
    {
        $user = User::findOrFail($id);
        $mhs = $user->mahasiswa;
        $semesters = $mhs->prodi->semester;

        $result = $mhs->potongan;
        $potongans = [];

        foreach ($semesters as $semester) {
            $potongans[$semester->id] = $result->map(function($e) use ($semester) {
                if ($e->semester_id === $semester->id) {
                    return $e;
                }
            })->filter();
        }

        return view('users.potongan.index', compact('user', 'semesters', 'potongans'));
    }

    public function store(Request $request, $role, $id)
    {
        $user = User::findOrFail($id);
        $mhs = $user->mahasiswa;
        $mhs->potongan()->sync($request->potongan_id);
        return redirect()->route('users.potongan.index', ['role' => $role, 'id' => $id])->with('success', 'Berhasil disimpan');
    }

    public function data($role, $id, $semester_id)
    {
        $user = User::findOrFail($id);
        $mhs = $user->mahasiswa;
        $data = $mhs->potongan()->where('semester_id', $semester_id)->get()->pluck('id');

        $options = DB::table('potongans')
            ->where('prodi_id', $mhs->prodi_id)
            ->where('prodi_id', $semester_id)
            ->where('tahun_ajaran_id', $mhs->tahun_ajaran_id)
            ->get();

        return response()->json([
            'message' => 'success',
            'data' => $data,
            'options' => $options
        ], 200);
    }
}
