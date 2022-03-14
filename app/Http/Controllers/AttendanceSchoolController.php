<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AttendanceSchool;
use App\Custom\Period;
use App\Custom\Student;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class AttendanceSchoolController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return bool
     */
    private function access(): bool
    {
        //TODO suares кто получает доступ?
        return Auth::user()->role->name === 'teacher' ||
            Auth::user()->curator ||
            Auth::user()->role->name === 'admin' ||
            Auth::user()->admin;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|void
     */
    public function index(Request $request)
    {
        if (!$this->access()) {
            return abort(403);
        }

        $filter = AttendanceSchool::createFilter($request);

        $periodRange = Period::getPeriodOrAllYear($filter);
        $periodDates = CarbonPeriod::create($periodRange[0], $periodRange[1])->toArray();
        $gradeId = $request->get('grade_id');

        return view('attendance.index', [
            'title' => 'Опоздания и отсутствия',
            'filter' => $filter,
            'students' => Student::getStudentsByGradeId($gradeId),
            'attendance' => AttendanceSchool::createAttendanceByDate($gradeId, $periodRange),
            'dates' => $periodDates
        ]);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|null
     * @throws Exception
     */
    public function save(Request $request)
    {
        $date = Carbon::parse($request->get('date'));
        $student = User::find($request->get('student_id'));
        $attendance = AttendanceSchool::updateOrInsertAttendance($date, $student, $request);

        if ($attendance['success']) {
            $model = AttendanceSchool::find($attendance['id']);

            return view('attendance.cell', [
                'student' => $student,
                'attForDate' => $model,
                'date' => new Carbon($model->date)
            ]);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return response()->json([
            'res' => AttendanceSchool::deleteByIdAndStudent(
                $request->get('attendance_id'),
                $request->get('student_id')
            )
        ]);
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function form(Request $request)
    {
        $attendanceId = $request->get('attendance_id');
        $attendance = AttendanceSchool::find($attendanceId);

        if ($attendance !== null) {
            $studentId =  $attendance->student_id;
            $date = Carbon::parse($attendance->date);
        } else {
            $studentId = $request->get('student_id');
            $date = Carbon::parse($request->get('date'));
        }

        $student = User::find($studentId);

        return view('attendance.modal-content', [
            'title' => $student->name .' '. $date->format('d.m.Y'),
            'student' => $student,
            'date' => $date,
            'attendance' => $attendance,
            'minutes' => Attendance::MINUTES
        ]);
    }

}
