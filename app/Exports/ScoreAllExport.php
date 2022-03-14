<?php

namespace App\Exports;

use App\Custom\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScoreAllExport implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param;
	}

	public function view(): View
	{
		return view('report.score-all-table', [
			'users' => $this->param['users'],
			'filter' => $this->param['filter'],
			'lessons' => $this->param['lessons'],
		]);
	}
}
