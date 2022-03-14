<?php

namespace App\Exports;

use App\Custom\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScoreAllExportAvg implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	public function view(): View
	{
		$filter = Report::createFilterScoreAll($this->param);
		$studentsScores = Report::createScoreAllAvgData($this->param, $filter);

		return view('report.score-all-avg-table', [
			'users' => $studentsScores,
			'lessons' => Report::takeLessonsScoreAll($filter)
		]);
	}
}
