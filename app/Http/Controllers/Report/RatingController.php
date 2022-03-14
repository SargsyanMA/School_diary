<?php

namespace App\Http\Controllers\Report;

use App\Custom\Report;
use App\Custom\Year;
use App\Exports\RatingExport;
use App\Grade;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class RatingController extends Controller
{
    private $grade;
    private $gradeLetter;
    private $period;

    public function __construct(Request $request)
    {
        $this->middleware('auth');

        $this->grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $this->gradeLetter = $request->get('grade_letter', null);

        $currentPeriodId = $request->get('period_id',
            Holiday::query()
                ->where('year', Year::getInstance()->getYear())
                ->where('period_type', $this->grade->isHighSchool ? 3 : 2)
                ->where('begin', '<=', Carbon::now())
                ->where('end', '>=', Carbon::now())
                ->first()->id ?? null
        );
        if($currentPeriodId !== null) {
            $this->period = Holiday::find($currentPeriodId);
        }
        else {
            $this->period = Holiday::query()
                ->where('year', Year::getInstance()->getYear())
                ->where('period_type', $this->grade->isHighSchool ? 3 : 2)
                ->first();
        }

    }

    public function index($layout = null, Request $request)
    {
        $filter = [
            'grade_id' => [
                'title' => 'Параллель',
                'type' => 'select',
                'options' => Grade::getActive(),
                'value' => $this->grade->id,
                'name_field' => 'numberLetter'
            ],


            'period_id' => [
                'title' => 'Период',
                'type' => 'select',
                'options' => Holiday::query()
                    ->where('year', Year::getInstance()->getYear())
                    ->where('period_type', $this->grade->isHighSchool ? 3 : 2)
                    ->get(),
                'value' => $this->period->id,
                'name_field' => 'name'
            ]
        ];

        return view('report.rating', [
            'title' => 'Отчеты: рейтинг школы',
            'layout' => $layout,
            'filter' => $filter,
            'students' => $this->getRating()
        ]);
    }

    public function excel(Request $request)
    {
        $rating = $this->getRating();
        return Excel::download(new RatingExport($rating), 'rating.xlsx');
    }

    private function getRating()
    {
        return User::query()
            ->select([
                'users.*',
                DB::raw("sum(if(score.value='.',2,score.value)*score_types.weight)/sum(score_types.weight) as score"),
                DB::raw('(select avg(student_social.value*0.01) as social from student_social where student_id=users.id) as social'),
                DB::raw("ifnull(sum(if(score.value='.',2,score.value)*score_types.weight)/sum(score_types.weight),0) + ifnull((select avg(student_social.value*0.01) as social from student_social where student_id=users.id),0) as total")
            ])
            ->leftJoin('score', function($join) {
                $join
                    ->on('score.student_id', '=', 'users.id')
                    ->where('date', '>=', $this->period->begin)
                    ->where('date', '<=', $this->period->end);
            })
            ->leftJoin('student_social', 'student_social.student_id', '=', 'users.id')
            ->leftJoin('score_types', 'score_types.id', '=', 'score.type_id')
            ->where('role_id', 2)
            ->when($this->grade->id, function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('users.class', $this->grade->id);
            })
            ->when($this->gradeLetter, function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('users.class_letter', $this->gradeLetter);
            })
            ->groupBy('users.id')
            ->orderBy('total', 'desc')
            ->get();
    }
}
