<?php

namespace App\Http\Controllers\Report;

use App\Custom\Period;
use App\Custom\Report;
use App\Custom\Year;
use App\Exports\ScoreAllExport;
use App\Grade;
use App\ScorePeriod;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ScoreAllController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $layout = null)
    {
        $data = $this->getData($request);

        return view('report.score-all', [
            'title' => 'Сводная ведомость учета успеваемости',
            'filter' => $data['filter'],
            'layout' => $layout,
            'users' => $data['users'],
            'lessons' => $data['lessons'],
        ]);
    }

    public function excel(Request $request)
    {
        $grade = Grade::query()
            ->where(['id' => $request->get('grade_id')])
            ->first();
        $grade = $grade->number ?? '';

        $periods = $request->get('period');
        foreach ($periods as $item) {
            $period[] = Period::$periodNames[$item] ?? '';
        }
        $periodText = implode('/', $period ?? []);

        return Excel::download(
            new ScoreAllExport($this->getData($request)),
            "Сводная ведомость учета успеваемости(класс $grade, Период $periodText).xlsx"
        );
    }

    private function getData(Request $request)
    {
        $filter = Report::createFilter($request, ['period[]', 'grade_id', 'grade_letter', 'student_id', 'year']);
        $studentsScores = Report::createScoreAllData($request, $filter);

        if ($request->get('grade_id') > 0) {

            $classStudents = User::query()
                ->where('role_id', 2)
                ->where('users.class', $filter['grade_id']['value'])
                ->when($filter['grade_letter']['value'], function ($query) use ($filter) {
                    $query->where('users.class_letter', $filter['grade_letter']['value']);
                })
                ->when($filter['student_id']['value'], function ($query) use ($filter) {
                    $query->where('id', $filter['student_id']['value']);
                })
                ->orderBy('users.name', 'ASC')
                ->get();

            if (!empty($classStudents)) {
                //@todo переделать как scoreAvg
                $studentsScores = [];
                $periodNumbers = array_map(function ($e) {return Period::$periodNumbers[$e] ?? 1; }, $filter['period[]']['value']);
                foreach ($classStudents as $st) {
                    $studentsScores[$st->id]['name'] = $st->name;
                    $studentsScores[$st->id]['score'] = ScorePeriod::query()
                        ->where('student_id', $st->id)
                        ->whereIn('score_period.period_number', $periodNumbers)
                        ->when($filter['year']['value'], function ($query) use ($filter) {
                            $begin = $filter['year']['value'] . '-09-01';
                            $end = ((int)$filter['year']['value']+1) . '-08-01';
                            $query
                                ->where('date', '>=', Carbon::parse($begin))
                                ->where('date', '<=', Carbon::parse($end));
                        }, function ($query) use ($filter) {
                            $query->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()));
                        })

                        ->get()
                        ->groupBy(['period_number', 'lesson_id']);
                }
            }
        }

        $student = !empty($filter['student_id']['value'])?User::find($filter['student_id']['value']):null;

        return [
            'filter' => $filter,
            'users' => $studentsScores,
            'lessons' => Report::takeLessonsScoreAll($filter, $student)
        ];
    }

}
