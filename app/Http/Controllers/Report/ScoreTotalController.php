<?php

namespace App\Http\Controllers\Report;

use App\Custom\Period;
use App\Custom\Report;
use App\Exports\ClassTeacherExport;
use App\Exports\ScoreTotalExport;
use App\Grade;
use App\Schedule;
use App\Score;
use App\ScorePeriod;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ScoreTotalController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $layout = null)
    {
        $data = $this->getData($request);

        return view('report.score-total', [
            'title' => 'Отчеты: оценки',
            'filter' => $data['filter'],
            'layout' => $layout,
            'schedule' => $data['schedule'],
            'scores' => $data['scores'],
            'attendance' => $data['attendance'],
            'weightedAverage' => $data['weightedAverage'],
            'totalAverage' => $data['totalAverage'],
            'student' => $data['student'],
            'scorePeriod' => $data['scorePeriod']
        ]);
    }

    public function excel(Request $request)
    {
        //$data = $this->getData($request);
        return Excel::download(new ScoreTotalExport($request), 'score-total.xlsx');
    }

    private function getData(Request $request)
    {
        $filter = Report::createFilter($request, ['period', 'grade_id', 'grade_letter', 'student_id']);
        $student = null !== $filter['student_id']['value'] ? User::find($filter['student_id']['value']) : null;

        return [
            'filter' => $filter,
            'schedule' => null !== $student? Score::studentSchedule($student):[],
            'scores' => Report::searchResult($filter),
            'attendance' => Report::attendanceStudent($filter),
            'weightedAverage' => Report::weightedAverageScore($filter),
            'totalAverage' => Report::weightedAverageScore($filter, null)[0],
            'student' => $student,
            'scorePeriod' => isset($student->scorePeriod)
                ? $student->scorePeriod->groupby(['period_number', 'lesson_id'])
                : []
        ];
    }

}
