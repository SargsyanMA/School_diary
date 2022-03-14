<?php

namespace App\Exports;

use App\Custom\Report;
use App\Homework;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HomeworkExport implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	public function view(): View
	{
		return view('report.homework-table', [
			'homeworks' => Homework::reportQuery(Report::createFilter($this->param))
		]);
	}
}
