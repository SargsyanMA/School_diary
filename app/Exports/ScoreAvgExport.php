<?php

namespace App\Exports;

use App\Custom\Report;
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScoreAvgExport implements FromView
{
	public $param;

	public function __construct($param)
	{
		$this->param = $param ;
	}

	public function view(): View
	{
		$filter = Report::createFilterAttendanceAll($this->param);
		$res = Report::createScoreAvgData($this->param, $filter);
		$student = !empty($filter['student_id']['value'])?User::find($filter['student_id']['value']):null;

		return view('report.score-avg-table', [
			'users' => $res['studentsScores'],
			'studentsScoresClass' => $res['studentsScoresClass'],
			'lessons' => Report::takeLessonsScoreAll($filter, $student)
		]);
	}
}
