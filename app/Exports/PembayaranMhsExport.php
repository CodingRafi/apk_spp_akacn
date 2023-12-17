<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\Exportable;
use DB, Auth;

class PembayaranMhsExport implements WithMultipleSheets
{
    private $semester;

    public function __construct($semester){
        $this->semester = $semester;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->semester as $row) {
            $sheets[] = new PembayaranMhsDetailExport($row);
        }
        return $sheets;
    }
}
