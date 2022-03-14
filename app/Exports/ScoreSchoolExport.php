<?php

namespace App\Exports;

use App\Custom\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScoreSchoolExport implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	public function view(): View
	{
		$filter = Report::createFilterScoreSchool($this->param);
		$schoolType = Report::createScoreSchoolData($this->param, $filter);

		return view('report.score-school-table', [
			'schoolType' => $schoolType
		]);
	}
}
