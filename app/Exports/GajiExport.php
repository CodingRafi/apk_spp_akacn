<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class GajiExport implements FromView
{
    protected $gajiId;

    public function __construct($gajiId)
    {
        $this->gajiId = $gajiId;
    }

    public function view(): View
    {
        $datas = DB::table('gaji_user')
            ->select('gaji_user.*', 'users.name')
            ->join('users', 'users.id', 'gaji_user.user_id')
            ->where('gaji_user.gaji_id', $this->gajiId)
            ->get();
        
        return view('kelola.gaji.export', compact('datas'));
    }
}
