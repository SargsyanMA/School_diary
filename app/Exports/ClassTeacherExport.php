<?php

namespace App\Exports;

use App\Custom\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClassTeacherExport implements FromView
{
	public $studentType;

	public function __construct($studentType)
	{
		$this->studentType = $studentType ;
	}

	public function view(): View
	{
		return view('report.class-teacher-table', [
			'studentType' => $this->studentType
		]);
	}
}
