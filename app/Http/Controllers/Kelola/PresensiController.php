<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PresensiController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view_kelola_presensi', ['only' => ['index', 'show', 'showJadwal']]);
        $this->middleware('permission:add_kelola_presensi', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_kelola_presensi', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_kelola_presensi', ['only' => ['destroy']]);
    }
}
