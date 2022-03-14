<?php

namespace App\Http\Controllers\Report;

use App\Attendance;
use App\Custom\Student;
use App\Custom\Teacher;
use App\Custom\Year;
use App\Exports\AttendanceExport;
use App\Exports\RatingExport;
use App\Grade;
use App\Holiday;
use App\Homework;
use App\Plan;
use App\Schedule;
use App\Score;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class SuperviseLogController extends Controller
{
    private $grade;
    private $period;
    private $student;

    public function __construct(Request $request)
    {
        $this->middleware('auth');

        $this->grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $this->date = Carbon::parse($request->get('date', Carbon::now()->format('d.m.Y')));


        $this->period = Holiday::find($request->get('period_id',  Holiday::query()
            ->where('year', Year::getInstance()->getYear())
            ->where('period_type', $this->grade->isHighSchool ? 3 : 2)
            ->where('begin', '<=', Carbon::now())
            ->where('end', '>=', Carbon::now())
            ->first()->id));
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
            'date' => [
                'title' => 'Дата',
                'type' => 'date',
                'value' => $this->date->format('d.m.Y')
            ],
        ];

        return view('report.supervise-log', [
            'title' => 'Отчеты: заполнение журнала',
            'layout' => $layout,
            'filter' => $filter,
            'data' => $this->getData()
        ]);
    }

    public function excel(Request $request)
    {
        $data = $this->getData();
        return Excel::download(new AttendanceExport($data), 'attendance.xlsx');
    }

    private function getData()
    {
        $schedules=Schedule::query()
            ->select('schedule.*', DB::raw('count(schedule.id) as cnt'))
            ->leftJoin('lesson', 'lesson.id', '=', 'schedule.lesson_id')
            ->leftJoin('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
            ->where('schedule.grade_id', $this->grade->id)
            ->where('schedule.year', Year::getInstance()->getYear())
            ->where('schedule.tms_end', '>=', $this->date->toDateString())
            ->where('schedule.tms', '<=', $this->date->toDateString())
            ->groupBy(['schedule.lesson_id', 'st.teacher_id', 'schedule.grade_letter', 'schedule.group_id', 'weekday'])
            ->orderBy('weekday', 'asc')
            ->orderBy('lesson.name', 'asc')
            ->get();



        $start = $this->date->startOfWeek()->clone();
        $end = $this->date->endOfWeek()->subDays(2)->clone();

        $period = [];
        foreach (CarbonPeriod::create($start, $end) as $n => $date)
            $period[$n+1] = $date;

        $homework = Homework::query()
            ->whereIn('lessonId', $schedules->pluck('id'))
            ->whereBetween('date', [
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d')
                ]
            )
            ->get()
            ->groupBy(['lessonId', 'date']);

        $scores = Score::query()
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->whereBetween('date', [
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d')
                ]
            )
            ->get()
            ->groupBy(['schedule_id', 'date']);

        $attendance = Attendance::query()
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->whereBetween('date', [
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d')
                ]
            )
            ->get()
            ->groupBy(['schedule_id', 'date']);

        $schedules = $schedules->groupBy('weekday');


        $plans = Plan::query()
            ->where('grade_num', $this->grade->number)
            ->get()
            ->groupBy(['lesson_id', 'lesson_num']);


        //dd($schedules[1][0]);
        //dd($plans[16]);
        //dd($schedules[1][0]->lessonNumber($start));

        return [
            'dates' => $period,
            'schedules' => $schedules,
            'homework' => $homework,
            'attendance' => $attendance,
            'plans' => $plans,
            'scores' => $scores
        ];
    }
}
