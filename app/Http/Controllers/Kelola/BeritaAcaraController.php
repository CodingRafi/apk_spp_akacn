<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeritaAcaraController extends Controller
{
    public function index(){
        return view('kelola.berita_acara.index');
    }

    public function data(){
        
    }
}
