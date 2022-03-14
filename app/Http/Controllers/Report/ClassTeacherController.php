<?php

namespace App\Http\Controllers\Report;

use App\Custom\Period;
use App\Custom\Year;
use App\Exports\ClassTeacherExport;
use App\Grade;
use App\Schedule;
use App\Score;
use App\ScorePeriod;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClassTeacherController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($layout = null, Request $request)
    {
        $data = $this->getData($request);

        return view('report.class-teacher', [
            'title' => 'Отчет классного руководителя за учебный период',
            'filter' => $data['filter'],
            'layout' => $layout,
            'studentType' => $data['studentType']
        ]);
    }

    public function excel(Request $request)
    {
        $data = $this->getData($request);
        return Excel::download(new ClassTeacherExport($data['studentType']), 'classTeacher.xlsx');
    }

    private function getData(Request $request) {

        $grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $periodType = $grade->isHighSchool ? 'half' : 'quarter';

        $filter = [
            'grade_id' => [
                'title' => 'Параллель',
                'type' => 'select',
                'options' => Grade::getActive(),
                'value' => $grade->id,
                'name_field' => 'numberLetter'
            ],
            'grade_letter' => [
                'title' => 'Класс',
                'type' => 'select',
                'options' => [
                    ['id'=> 'А', 'name'=> 'А'],
                    ['id'=> 'Б', 'name'=> 'Б'],
                    ['id'=> 'В', 'name'=> 'В']
                ],
                'value' => $request->get('grade_letter', null),
                'name_field' => 'name',
                'multiple' => false
            ],
            'period' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => array_map(
                    function ($k, $v) {
                        return ['id'=> $k, 'name'=> $v];
                    },
                    array_keys(Period::$periodNames),
                    Period::$periodNames
                ),
                'value' =>  $request->get('period', Period::CH1),
                'name_field' => 'name'
            ]
        ];

        $students = User::query()
            ->where('role_id', User::STUDENT)
            ->where('class', $grade->id)
            ->when(isset($filter['grade_letter']) && $filter['grade_letter']['value'], function ($query) use ($filter) {
                /** @var Builder $query */
                $query->where('users.class_letter', $filter['grade_letter']['value']);
            })
            ->get();

        $scoresPeriod = ScorePeriod::query()
            ->where('grade_id', $grade->id)
			->where('period_type', $periodType)
			->where('period_number', Period::$periodNumbers[$filter['period']['value']])
            ->where('date', '>=', Carbon::parse(Year::getInstance()->getYearBegin()))
			->get()
            ->groupBy(['student_id', 'lesson_id']);


        $scores = Score::query()
            ->select([
                DB::raw("sum(if(score.value='.',2,score.value)*score_types.weight)/sum(score_types.weight) as score"),
                'score.student_id',
                'score.lesson_id'
            ])
            ->leftJoin('score_types', 'score_types.id', '=', 'score.type_id')
            ->join('schedule', 'schedule.id', '=', 'score.schedule_id')
            ->where('schedule.grade_id', $grade->id)
            ->whereBetween('date', Period::defineFirstAndLastDays($filter))
            ->groupBy(['score.student_id', 'score.lesson_id'])
            ->get()
            ->groupBy(['student_id', 'lesson_id']);


        //dd($grade->id);

        $studentType['stud'] = [
            'perfect' => [],
            'oneGood' => [],
            'perfectGood' => [],
            'oneRegular' => [],
            'regular' => [],
            'bad' => []
        ];

        foreach ($students as $k => $student) {
        	/** @var User $student */
            $s = [
                'info' => [
                    'name' => $student->name,
                    'no_score' => [],
                    'scores_lessons' => []
                ]
            ];

            foreach(Score::ALL_SCORES as $i ) {
                $s['total'][$i] = 0;
                $s['info']['scores_lessons'][$i] = [];
            }

            $studentSchedule = Schedule::getStudentSchedule($student)
                ->where('no_score', 0)
                ->pluck('name', 'id');

            foreach ($studentSchedule as $lessonId => $lessonName) {

                if (isset($scoresPeriod[$student->id][$lessonId])) {
                    $score = $scoresPeriod[$student->id][$lessonId]->first();
                    $s['total'][$score['value']]++;
                    $s['info']['scores_lessons'][$score['value']][]= $lessonName;
                }
                elseif(isset($scores[$student->id][$lessonId])) {
                    $score = $scores[$student->id][$lessonId]->first();



                    if($score['score']<2.7)
                        $score['value'] = 2;
                    elseif($score['score']<3.51)
                        $score['value'] = 3;
                    elseif($score['score']<4.51)
                        $score['value'] = 4;
                    else
                        $score['value'] = 5;

                    $s['total'][$score['value']]++;
                    $s['info']['scores_lessons'][$score['value']][]= $lessonName;
                    $s['info']['avg_score'][$score['value']][]= $lessonName;
                }
                else {
                    $s['info']['no_score'][]= $lessonName;
                }
            }

            if ($s['total'][5] > 0 && $s['total'][4] === 0 && $s['total'][3] === 0 && $s['total'][2] === 0) {
                $studentType['stud']['perfect'][] = $s['info'];
            }

            elseif ($s['total'][5] >= 0 && $s['total'][4] === 1 && $s['total'][3] === 0 && $s['total'][2] === 0) {
                $studentType['stud']['oneGood'][] = $s['info'];
            }

            elseif ($s['total'][5] >= 0 && $s['total'][4] > 1 && $s['total'][3] === 0 && $s['total'][2] === 0) {
                $studentType['stud']['perfectGood'][] = $s['info'];
            }

            elseif ($s['total'][5] >= 0 && $s['total'][4] >= 0 && $s['total'][3] === 1 && $s['total'][2] === 0) {
                $studentType['stud']['oneRegular'][] = $s['info'];
            }

            elseif ($s['total'][5] >= 0 && $s['total'][4] >= 0 && $s['total'][3] > 1 && $s['total'][2] === 0) {
                $studentType['stud']['regular'][] = $s['info'];
            }

            elseif ($s['total'][2] > 0) {
                $studentType['stud']['bad'][] = $s['info'];
            }

            else {
                $studentType['stud']['noInfo'][] = $s['info'];
            }
        }

        $studentType['total'] = count($students);
        $studentType['absolute']['up'] = $studentType['total'] - count($studentType['stud']['bad']);
        $studentType['absolute']['percentage'] = round(($studentType['absolute']['up']*100)/$studentType['total']);
        $studentType['quality']['up'] = count($studentType['stud']['perfect']) + count($studentType['stud']['oneGood']) + count($studentType['stud']['perfectGood']);
        $studentType['quality']['percentage'] = round(($studentType['quality']['up']*100)/$studentType['total']);

        return [
            'filter' => $filter,
            'studentType' => $studentType
        ];
    }

}
