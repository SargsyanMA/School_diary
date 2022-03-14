<?php

namespace App\Http\Controllers;

use App\Custom\Teacher;
use App\Homework;
use App\Grade;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function access() {
        return Auth::user()->role->name == 'teacher' || Auth::user()->curator || Auth::user()->role->name == 'admin' || Auth::user()->admin;
    }

    public function index(Request $request)
    {

        if(!$this->access()) {
            return abort(403);
        }

        $mode = $request->get('mode');
		$gradeId = $request->get('grade_id');
        $show_nav = true;
		$curatorClass = null;

        if(Auth::user()->role->name == 'teacher') {
            $teacher_id = Auth::user()->id;
            $show_nav = false;
        } else {
            $teacher_id = $request->get('teacher');
        }

        if (Auth::user()->curator && !empty(Auth::user()->class)) {
			$curatorClass = Auth::user()->class;
		}

        $teachers=Teacher::getAllTeacherOrSelfTeacher();
        if (null !== $teacher_id || null !== $gradeId) {
			$schedule = Schedule::getScheduleMain($teacher_id, $gradeId, $curatorClass);
		}
        $can_edit = $mode == 'teacher' || Auth::user()->admin || Auth::user()->curator || Auth::user()->role->name === 'admin';
        $teacher = User::find($teacher_id);

        return view('teacher-schedule', [
            'title'=>"Журнал ". ($teacher->name ?? ''),
            'show_nav' => $show_nav,
            'schedule'=>$schedule??[],
            'teachers'=>$teachers,
            'teacher'=>$teacher,
            'grades' => Grade::getActive(),
            'grade' => $request->get('grade_id'),
			'mode' => (null === $teacher_id && null === $gradeId)?null:$mode,
			'isAdmin' => Auth::user()->role->name === 'admin' || Auth::user()->admin || Auth::user()->curator,
            'currentTeacher'=> $teacher_id,
            'can_edit' => $can_edit,
        ]);
    }

    public function getFormData(Request $request) {

        $homework = Homework::find($request->get('id'));
        $schedule = Schedule::find($request->get('schedule_id'));

        if($homework) {
            $homework->append('studentsIds');
        }

        return [
            'students' => $schedule->students,
            'grade_id' => $homework !== null ? $homework->oGrade->id : $schedule->grade->id,
            'date' => $homework !== null ? $homework->date : $request->get('date'),
            'lesson_num' => $homework !== null ? $homework->lessonNum : $schedule->number,
            'lesson_id' => $homework !== null ? $homework->lessonId : $schedule->id,
            //'student_id' => $homework !== null ? $homework->student_id : $request->get('student_id'),
            'homework' => $homework ?? ['studentsIds'=> []],
            'child' => $homework !== null ? $homework->child : 0,
        ];
    }

    public function setHomework(Request $request) {

        $id = (int)$request->get('id');
        $students = (array)$request->get('student');

        $fields = [
            'grade' => $request->get('grade'),
            'date' =>  $request->get('date'),
            'lessonNum' => $request->get('lessonNum'),
            'lessonId' => $request->get('lessonId'),
            'child' => $request->get('child'),
            'text' => $request->get('text'),
            'tms' => Carbon::now()->toDateTimeLocalString()
        ];

        if(empty($id)) {
            $id = Homework::query()->insertGetId($fields);
        }
        else {
            Homework::query()->where('id', $id)->update($fields);
            DB::table('homework_child')->where('homework_id', $id)->delete();
        }

        if($fields['child'] && !empty($students)) {
            foreach ($students as $child_id) {
                DB::table('homework_child')->insert([
                    'homework_id' => $id,
                    'child_id' => $child_id,
                ]);
            }
        }

        DB::table('homework_log')->insert([
            'tms' => Carbon::now()->toDateTimeLocalString(),
            'author' => Auth::user()->id,
            'content' => $fields['text'],
            'date' =>  $fields['date'],
            'lessonNum' => $fields['lessonNum'],
            'lessonId' => $fields['lessonId'],
            'grade' => $fields['grade']
        ]);

        // todo log

        return Homework::find($id);

    }

    public function deleteHomework(Request $request) {
        $id = (int)$request->get('id');
        Homework::find($id)->delete();
    }


    public function getHomeworkForLesson(Request $request) {

        $homework = Homework::query()
            ->where('date', $request->get('date'))
            ->where('grade', $request->get('grade'))
            ->where('lessonNum', $request->get('lesson_num'))
            ->where('lessonId', $request->get('lesson_id'))
            ->get();

        return view('includes.calendar-homework', [
            'homework' => $homework,
            'can_edit' => true
        ]);
    }
}
