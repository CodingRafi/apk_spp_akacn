<?php

namespace App\Http\Controllers\Kelola\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MahasiswaRequest;
use Illuminate\Support\Facades\DB;

class MahasiwaController extends Controller
{
    public function index()
    {
        $prodis = DB::table('prodi')->get();
        $tahun_ajarans = DB::table('tahun_ajarans')->get();
        return view('users.mahasiswa.index', compact('prodis', 'tahun_ajarans'));
    }
    
    public function create(){
        $tahun_ajarans = DB::table('tahun_ajarans')->get();
        $prodis = DB::table('prodi')->get();
        return view('users.mahasiswa.form', compact('tahun_ajarans', 'prodis'));
    }

    public function store(MahasiswaRequest $request){
        dd($request);
    }
}
