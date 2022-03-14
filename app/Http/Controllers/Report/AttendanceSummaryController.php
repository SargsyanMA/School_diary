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
use App\ScheduleComment;
use App\ScheduleHomework;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceSummaryController extends Controller
{
    private $grade;
    private $period;
    private $student;

    public function __construct(Request $request)
    {
        $this->middleware('auth');

        $this->grade = Grade::find($request->get('grade_id', Grade::getActive()->first()->id));

        $this->filter = [
            'grade_id' => [
                'title' => 'Параллель',
                'type' => 'select',
                'options' => Grade::getActive(),
                'value' => $this->grade->id,
                'name_field' => 'numberLetter'
            ],
            'date' => [
                'title' => 'Дата',
                'type' => 'date-range',
                'value' =>  $request->get('date', [
                    Carbon::parse('first day of this month')->format('d.m.Y'),
                    Carbon::parse('last day of this month')->format('d.m.Y')
                ])
            ],
        ];

    }

    public function index($layout = null, Request $request)
    {

        return view('report.attendance-summary', [
            'title' => 'Отчеты: дисциплина',
            'layout' => $layout,
            'filter' => $this->filter,
            'data' => $this->getData()
        ]);
    }

    public function student($studentId, $layout = null, Request $request)
    {
        unset($this->filter['grade_id']);

        $this->student = User::find($studentId);


        return view('report.attendance-summary-student', [
            'title' => 'Отчеты: дисциплина - '. $this->student->name,
            'layout' => $layout,
            'filter' => $this->filter,
            'student' => $this->student,
            'data' => $this->getData($this->student->id)
        ]);
    }

    public function excel(Request $request)
    {
        $data = $this->getData();
        return Excel::download(new AttendanceExport($data), 'attendance.xlsx');
    }

    private function getData($studentId=null)
    {
        if($studentId == null) {
            $students = User::query()
                ->where('role_id', 2)
                ->where('users.class', $this->grade->id)
                ->get();
        }
        else {
            $students = User::query()
                ->where('id', $studentId)
                ->where('role_id', 2)
                ->get();
        }


        $dates = CarbonPeriod::create(
            Carbon::parse($this->filter['date']['value'][0]),
            '1 day',
            Carbon::parse($this->filter['date']['value'][1])
        );

        $attendance = Attendance::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereBetween('date', [
                    Carbon::parse($this->filter['date']['value'][0]),
                    Carbon::parse($this->filter['date']['value'][1])
                ]
            )
            ->get()
            ->groupBy(['student_id', 'date']);

        $no_homework = ScheduleHomework::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereBetween('date', [
                    Carbon::parse($this->filter['date']['value'][0]),
                    Carbon::parse($this->filter['date']['value'][1])
                ]
            )
            ->get()
            ->groupBy(['student_id', 'date']);

        $comments = ScheduleComment::query()
            ->whereIn('student_id', $students->pluck('id'))
            ->whereBetween('date', [
                    Carbon::parse($this->filter['date']['value'][0]),
                    Carbon::parse($this->filter['date']['value'][1])
                ]
            )
            ->get()
            ->groupBy(['student_id', 'date']);

        return [
            'students' => $students,
            'student' => $this->student,
            'dates' => $dates,
            'attendance' => $attendance,
            'no_homework' => $no_homework,
            'comments' => $comments,
        ];
    }
}
