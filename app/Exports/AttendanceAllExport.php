<?php

namespace App\Exports;

use App\Custom\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceAllExport implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	public function view(): View
	{
		$filter = Report::createFilterAttendanceAll($this->param);
		$res = Report::createAttendanceAllData($this->param, $filter);

		return view('report.attendance-all-table', [
			'users' => $res['classStudents'],
			'studentAttendance' => $res['studentAttendance']
		]);
	}
}
