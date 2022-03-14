<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{

    private $data;
    private $total;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
	{
		return view('report.attendance-table', [
			'data' => $this->data,

        ]);
	}
}
