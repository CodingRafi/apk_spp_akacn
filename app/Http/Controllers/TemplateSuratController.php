<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_template_surat', ['only' => ['index', 'store']]);
    }

    public function index(){
        if (getRole()->name == 'admin') {
            abort(403);
        }
        return view('data_master.template_surat.index');
    }
}
