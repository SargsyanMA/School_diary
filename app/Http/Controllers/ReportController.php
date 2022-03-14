<?php

namespace App\Http\Controllers;

use App\Custom\Period;
use App\Custom\Report;
use App\Custom\Year;
use App\Exports\HomeworkExport;
use App\Exports\ScoreAllExportAvg;
use App\Exports\ScoreExport;
use App\Exports\ScoreAllExport;
use App\Exports\AttendanceAllExport;
use App\Exports\ScoreAvgExport;
use App\Exports\RatingExport;
use App\Exports\ClassTeacherExport;
use App\Exports\ScoreSchoolExport;
use App\Homework;
use App\Score;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('report.index', [
            'title' => 'Отчеты'
        ]);
    }

 /*   public function homework($layout = null, Request $request)
    {
        $filter = Report::createFilter($request);

        return view('report.homework', [
            'title' => 'Отчеты: домашние задания',
            'layout' => $layout,
            'filter' => $filter,
            'homeworks' => Homework::reportQuery($filter)
        ]);
    } */

	public function homeworkExport(Request $request)
	{
		return Excel::download(new HomeworkExport($request), 'homework.xlsx');
	}

    public function score($layout = null, Request $request)
    {
        $filter = Report::createFilter($request, ['year', 'period', 'grade_id','grade_letter', 'student_id']);
        $student = null !== $filter['student_id']['value'] ? User::find($filter['student_id']['value']) : null;

        return view('report.score', [
            'title' => 'Отчеты: оценки',
            'filter' => $filter,
            'layout' => $layout,
			'schedule' => null !== $student ? Score::studentSchedule($student, $filter['year']['value']) : [],
			'scores' => Report::searchResult($filter),
            'attendance' => Report::attendanceStudent($filter),
            'weightedAverage' => Report::weightedAverageScore($filter),
            'totalAverage' => Report::weightedAverageScore($filter, null)[0],
            'student' => $student,
            'scorePeriod' => isset($student->scorePeriod) ? $student->scorePeriod->groupby(['period_number', 'lesson_id']) : []
        ]);
    }


	public function scoreExport(Request $request)
	{
		ScoreExport::excel($request);
	}

    public function scoreAll($layout = null, Request $request) {
		$filter = Report::createFilterScoreAll($request);

		$studentsScores = Report::createScoreAllData($request, $filter);
		$student = !empty($filter['student_id']['value'])?User::find($filter['student_id']['value']):null;

		//dd($studentsScores);

		return view('report.score-all', [
			'title' => 'Сводная ведомость учета успеваемости',
			'filter' => $filter,
			'layout' => $layout,
			'users' => $studentsScores,
			'lessons' => Report::takeLessonsScoreAll($filter, $student)
		]);
    }

    public function scoreAllAvg($layout = null, Request $request) {
        $filter = Report::createFilterScoreAll($request);
        $studentsScores = Report::createScoreAllAvgData($request, $filter);

       // dd($studentsScores);

        return view('report.score-all-avg', [
            'title' => 'Сводная ведомость учета успеваемости (Средний балл)',
            'filter' => $filter,
            'layout' => $layout,
            'users' => $studentsScores,
            'lessons' => Report::takeLessonsScoreAll($filter)
        ]);
    }

	public function scoreAllExport(Request $request)
	{
		return Excel::download(new ScoreAllExport($request), 'scoreAll.xlsx');
	}

    public function scoreAllAvgExport(Request $request)
    {
        return Excel::download(new ScoreAllExportAvg($request), 'scoreAll.xlsx');
    }

    public function scoreAvg($layout = null, Request $request) {
		Period::cutPeriodNames(null, true);
		$filter = Report::createFilterAttendanceAll($request);
		$res = Report::createScoreAvgData($request, $filter);
		$student = !empty($filter['student_id']['value'])?User::find($filter['student_id']['value']):null;

		return view('report.score-avg', [
			'title' => 'Средний балл ученика',
			'filter' => $filter,
			'layout' => $layout,
			'users' => $res['studentsScores'],
			'studentsScoresClass' => $res['studentsScoresClass'],
			'lessons' => Report::takeLessonsScoreAll($filter, $student)
		]);
    }

	public function scoreAvgExport(Request $request)
	{
		return Excel::download(new ScoreAvgExport($request), 'scoreAvg.xlsx');
	}

    public function scoreSchool(Request $request, $layout = null)
    {
		Period::cutPeriodNames(null, true);
		$filter = Report::createFilterScoreSchool($request);
		$schoolType = Report::createScoreSchoolData($request, $filter);

		return view('report.score-school', [
			'title' => 'Сводный отчет об успеваемости по школе',
			'filter' => $filter,
			'layout' => $layout,
			'schoolType' => $schoolType
		]);
    }

	public function scoreSchoolExport(Request $request)
	{
		return Excel::download(new ScoreSchoolExport($request), 'scoreSchool.xlsx');
	}

    public function attendanceAll($layout = null, Request $request) {
		Period::cutPeriodNames(null, true);
		$filter = Report::createFilterAttendanceAll($request);
		$res = Report::createAttendanceAllData($request, $filter);

		return view('report.attendance-all', [
			'title' => 'Сводная ведомость учета посещаемости и опозданий',
			'filter' => $filter,
			'layout' => $layout,
			'users' => $res['classStudents'],
			'studentAttendance' => $res['studentAttendance']
		]);
    }

	public function attendanceAllExport(Request $request)
	{
		return Excel::download(new AttendanceAllExport($request), 'attendanceAll.xlsx');
	}

    public function rating($layout = null, Request $request) {

        $filter = Report::createFilter($request, ['grade_id']);
        return view('report.rating', [
            'title' => 'Отчеты: рейтинг школы',
            'layout' => $layout,
            'filter' => $filter,
            'students' => User::ratingReportQuery()
        ]);
    }

	public function ratingExport()
	{
		return Excel::download(new RatingExport(), 'rating.xlsx');
	}

	public function classTeacher($layout = null, Request $request) {
		Period::cutPeriodNames(null, true);
		$filter = Report::createFilterClassTeacher($request);
		$studentType = Report::createClassTeacherData($request, $filter);

		return view('report.class-teacher', [
			'title' => 'Отчет классного руководителя за учебный период',
			'filter' => $filter,
			'layout' => $layout,
			'studentType' => $studentType
		]);
	}


	public function ClassTeacherExport(Request $request)
	{
		return Excel::download(new ClassTeacherExport($request), 'classTeacher.xlsx');
	}

    public function superviseTeacherHomework($layout = null, Request $request) {

        $filter = Report::createFilter($request, ['grade_id', 'date_single']);

        $students = User::query()
            ->where('class', $filter['grade_id']['value'])
            ->where('role_id', 2)
            ->get();


        $homework = Homework::query()
            ->select(
                'users.id as teacher_id',
                'users.name as teacher_name',
                'lesson.name as lesson_name',
                'homework.tms as tms',
                'homework.date as date',
                'homework.text as text',
                'schedule.grade_id'
            )
            ->join('schedule', 'schedule.id', '=', 'homework.lessonId')
            ->join('lesson', 'lesson.id', '=', 'schedule.lesson_id')
            ->join('schedule_teachers', 'schedule_teachers.schedule_id', '=', 'schedule.id')
            ->join('users', 'users.id', '=', 'schedule_teachers.teacher_id')
            ->where('homework.date', Carbon::parse($filter['date_single']['value']))
            ->where('schedule.grade_id', $filter['grade_id']['value'])
            ->get();



        $result = [];

        return view('report.supervise-teacher-homework', [
            'title' => 'Контроль заполнения домашних заданий',
            'homework' => $homework,
            'layout' => $layout,
            'filter' => $filter,
            'students' => $students
        ]);

    }
}
