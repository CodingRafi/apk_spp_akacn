<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard');
    }
}
