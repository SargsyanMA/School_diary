<?php

namespace App\Http\Controllers\Report;

use App\Attendance;
use App\Custom\Student;
use App\Custom\Year;
use App\Exports\AttendanceExport;
use App\Exports\RatingExport;
use App\Grade;
use App\Holiday;
use App\Schedule;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceController extends Controller
{
    private $grade;
    private $period;
    private $student;

    public function __construct(Request $request)
    {
        $this->middleware('auth');

        $this->grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));
        $this->student = User::find($request->get('student_id'));

        $students = User::query()
             ->where('role_id', 2)
             ->where('users.class', $this->grade->id)
             ->get();

        //dd($this->student);

        if($this->student && !$students->pluck('id', 'id')->has($this->student->id)) {
            $this->student = null;
        }
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
                'name_field' => 'number'
            ],
            'student_id' => [
                'title' => 'Ученик',
                'type' => 'select',
                'options' => User::query()
                    ->where('role_id', 2)
                    ->where('users.class', $this->grade->id)
                    ->get(),
                'value' => $this->student->id ?? null,
                'name_field' => 'name'
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

        return view('report.attendance', [
            'title' => 'Отчеты: посещаемость',
            'layout' => $layout,
            'filter' => $filter,
            'student' => $this->student,
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
        $students = User::query()
            ->where('role_id', 2)
            ->where('users.class', $this->grade->id)
            ->get();


        $attendance = Attendance::query()
            ->when($this->student, function ($query) {
                $query->where('student_id', $this->student->id);
            }, function ($query) use ($students) {
                $query->whereIn('student_id', $students->pluck('id'));
            })
            ->get();
        if($this->student) {
            $attendance = $attendance->groupBy(['lesson_id', 'date']);
        }
        else {
            $attendance = $attendance->groupBy(['student_id', 'date']);
        }

        return [
            'dates' => CarbonPeriod::create($this->period->begin, $this->period->end),
            'students' => $students,
            'student' => $this->student,
            'attendance' => $attendance,
            'lessons' => $this->student ? Schedule::getStudentSchedule($this->student)->get() : []
        ];
    }
}
