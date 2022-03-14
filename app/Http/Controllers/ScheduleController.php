<?php

namespace App\Http\Controllers;

use App\Grade;
use App\Lesson;
use App\Schedule;
use App\ScheduleTeacher;
use App\StudentParent;
use App\User;
use App\StudentGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Custom\Year;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $role = Auth::user()->role->name;
        if($role == 'student') {
            $scheduleType = [
                'student'=>'Ученик'
            ];

            $currentType='student';
            $students = User::where('id', Auth::user()->id)->get();
            $grade = Auth::user()->class;
            $grades=Grade::whereIn('id', $students->pluck('class'))->get();
            $currentStudent=Auth::user()->id;

        }
        elseif($role == 'parent') {
            $scheduleType = [
                'student'=>'Ученик'
            ];
            $currentType='student';
            $students = User::query()
                ->select('users.*')
                ->whereIn('users.id', StudentParent::getStudentsId())
                ->leftJoin('grade', 'grade.id', '=', 'users.class')
                ->orderBy('grade.year', 'desc')
                ->orderBy('users.name', 'asc')
                ->get();

            if(empty($students)) {
                abort(403);
            }
			$grades = Grade::whereIn('id', $students->pluck('class')->toArray())->get();
            $currentStudent=(int)$request->get('student', $students->first()->id);
			$grade = User::find($currentStudent)->class;

        }
        else {
            $scheduleType = [
                'class'=>'Параллель',
                'teacher'=>'Педагог',
                'student'=>'Ученик'
            ];
            $currentType=in_array($request->get('type'),array_keys($scheduleType)) ? $request->get('type') : 'class';

            $grades = Grade::getActive();
            $grade = $request->get('grade', $grades->first()->id);
            $students = User::query()
                ->where('class', $grade)
                ->when(Auth::user()->role->name == 'student', function ($query) {
					/** @var \Illuminate\Database\Eloquent\Builder $query */
					$query->where('id', Auth::user()->id);
                })
                ->get();

            $currentStudent=(int)$request->get('student');
        }

        $currentTeacher=(int)$request->get('teacher');

        $teachers = User::query()->where('role_id', 3);

        $teacher=(int)$request->get('teacher');
        $student = $currentStudent;
        $year = Year::getInstance()->getYear();
        $lessonTime = DB::table('schedule_time')
            ->where('grade', Grade::find($grade)->number)
            ->where('letter', Grade::find($grade)->letter)
            ->get();

        $yearBeginTms = strtotime(Year::getInstance()->getYearBegin());
        if ($yearBeginTms > time()) {
            $from=date('Y-m-d',$yearBeginTms);
        }
        else {
            $from=date('Y-m-d');
        }

        $schedule = Schedule::query()
            ->select(
                'schedule.*',
                DB::raw('users.name AS teacherName'),
                DB::raw('lesson.name AS lessonName'),
                DB::raw('lesson.type_code AS lessonType'),
                DB::raw('grade.year AS gradeYear'),
                DB::raw('grade.letter AS gradeLetter'),
                DB::raw('count(student_group_students.student_id) as studentsCount'),
                DB::raw('schedule_time.time_begin AS time_begin'),
                DB::raw('schedule_time.time_end AS time_end')

            )
            ->leftJoin('users', 'users.id', '=','schedule.teacher_id')
            ->leftJoin('student_group_students', 'student_group_students.group_id', '=', 'schedule.group_id' )
            ->leftJoin('grade', 'grade.id', 'schedule.grade_id')
			->leftJoin('schedule_teachers as st', 'schedule.id', '=', 'st.schedule_id')
            ->leftJoin('schedule_time', function($join) {
				/** @var \Illuminate\Database\Query\JoinClause $join */
				$join
                    ->on('schedule_time.grade', '=', DB::raw('schedule.year - grade.year + 1'))
                    ->on('schedule_time.lesson_number', '=', 'schedule.number');
            })
            ->leftJoin('lesson', 'lesson.id', 'schedule.lesson_id')
            ->when($grade, function ($query) use ($grade) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('schedule.grade_id', $grade);
            })
            ->when($teacher, function ($query) use ($teacher) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('st.teacher_id', $teacher);
            })
            ->when($student, function ($query) use ($student) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */

				$query
                    ->where(function($query) use ($student) {
                        $obStudent = User::find($student);
						/** @var \Illuminate\Database\Eloquent\Builder $query */
						$query
                            ->where('all_class', 1)
                            ->orWhere('grade_letter', $obStudent->class_letter)
                            ->orWhere('student_group_students.student_id', $obStudent->id);
                    });
            })

            //->where('user.active',1)
            ->when(Auth::user()->role->name != 'admin', function ($query) use ($from) {
                $query->where('schedule.tms','<=', $from);
            })
            ->when(Auth::user()->role->name != 'admin', function ($query) use ($from) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('schedule.tms_end', '>=', $from);
            })
            ->where('schedule.year', $year)
            ->groupBy('schedule.id')
            ->orderBy('schedule.tms_end', 'desc')
            ->orderBy('lesson.type_code', 'asc')
            ->orderBy('lesson.name', 'asc')
            ->orderBy('users.name', 'asc')
            ->get();

        //dd($schedule);

        $result=array();
        $weekDays=array('Понедельник','Вторник', 'Среда', 'Четверг', 'Пятница');

        if ($currentType == 'teacher') {
            for ($i=0; $i<=10; $i++) {
                $result['time'][$i] = [
                    'name' => $i . ' урок',
                ];

                foreach ($weekDays as $day => $dayName) {

                    $result['lessons'][$i][$day + 1] = array(
                        'number' => $i,
                        'weekday' => $day + 1,
                        'name' => $i . ' урок',
                        'lessons' => array()
                    );
                }
            }
        }
        else {
            foreach ($lessonTime as $time) {
                $result['time'][$time->lesson_number] = [
                    'name' => $time->lesson_number . ' урок',
                    'time' => [date('H:i', strtotime($time->time_begin)), date('H:i', strtotime($time->time_end))],
                ];

                foreach ($weekDays as $day => $dayName) {

                    $result['lessons'][$time->lesson_number][$day + 1] = array(
                        'number' => $time->lesson_number,
                        'weekday' => $day + 1,
                        'name' => $time->lesson_number . ' урок',
                        'time' => [date('H:i', strtotime($time->time_begin)), date('H:i', strtotime($time->time_end))],
                        'lessons' => array(),
                        'studentsCount' => 0
                    );
                }
            }
        }

        foreach ($schedule as $lesson) {

            $tmsBegin=strtotime($lesson->tms);
            $tmsEnd=strtotime($lesson->tms_end);

            if ($tmsEnd>=time()) {
                $lesson->active=1;
            }
            else {
                $lesson->active=0;
            }

            if ($tmsBegin > time()) {
                $lesson->future=1;
            }
            else {
                $lesson->future=0;
            }

            if ($lesson->active) {
                if(!isset($result['lessons'][$lesson->number][$lesson->weekday]['studentsCount'])) {
                    $result['lessons'][$lesson->number][$lesson->weekday]['studentsCount'] = 0;
                }
                $result['lessons'][$lesson->number][$lesson->weekday]['studentsCount'] += $lesson->studentsCount;
            }

            $lesson->gradeName = $lesson->grade->number.$lesson['gradeLetter'];

            $lesson['lessonTime'] = [
                date('H:i', strtotime($lesson['time_begin'])),
                date('H:i', strtotime($lesson['time_end']))
            ];

            $result['lessons'][$lesson->number][$lesson->weekday]['lessons'][] = $lesson;
            $result['lessonId'][]=$lesson->id;
        }

        $result['grade']=$grade;
        $result['weekDays']=$weekDays;
        $schedule = $result;


        if($currentType=='class') {
            $title = "Расписание ".Grade::find($grade)->number." параллели";
        }
        elseif($currentType=='teacher') {
            $title = "Расписание учителя: {$teachers->where('id', $currentTeacher)->first()}";
        }
        elseif($currentType=='student') {
            $title = "Расписание ученика";

            if(User::find($currentStudent)) {
                $obStudent = User::find($currentStudent);
                $title .= ": {$obStudent->name} ({$obStudent->grade->number}{$obStudent->class_letter} класс)";
            }

        }

        $fileName = $title??'';

        if (isset($_GET['lastname'])) {
            if ((bool)$_GET['lastname'])
                $fileName .= ' с фамилиями учеников';
            else
                $fileName .= ' без фамилий учеников';
        }

        return view('schedule', [
            'title'=>$title??'',
            'schedule'=>$schedule,
            'grades'=>$grades,
            'teachers'=>$teachers,
            'students'=>$students,
            'currentGrade'=>$grade,
            'currentTeacher'=>$currentTeacher,
            'currentStudent'=>$currentStudent,
            'currentType'=>$currentType,
            'scheduleType'=>$scheduleType,
            'lastName'=>(int)$request->get('lastname'),
            'fileName'=>$fileName,
            'yearEnd' => Year::getInstance()->getYearEnd(),
            'yearBegin' => Year::getInstance()->getYearBegin(),
            'can_edit' => Auth::user()->role->name == 'admin' || Auth::user()->admin,
            'role' => $role
        ]);
    }

    public function setLesson(Request $request) {
        $year = Year::getInstance()->getYear();
        $id = (int)$request->get('id');
        $letter = $request->get('group');
		$role = Auth::user()->role->name;

		$fields=array(
            'lesson_id' => $request->get('lesson'),
            'grade_id' => $request->get('grade'),
            'weekday' => $request->get('weekday'),
            'number' => $request->get('number'),
            'type' => $request->get('type'),
            'note' => $request->get('note'),
            'all_class' => $request->get('group') == 'all-class',
            'grade_letter' => $letter == 'А' || $letter == 'Б' || $letter == 'В' ? $letter : null,
            'group_id' => $request->get('group'),
            'tms' => Carbon::parse($request->get('tms'))->toDateTimeLocalString(),
            'tms_end' => Carbon::parse($request->get('tms_end'))->toDateTimeLocalString(),
            'year' => $year,
            'no_score' => $request->get('no_score'),
        );

        if (empty($id)) {
            $id = Schedule::query()->insertGetId($fields);
            if ($id) {
				ScheduleTeacher::setTeachersForSchedule($request->get('teacher'), $id);
			}
        }
        else {
            Schedule::query()->where('id', $id)->update($fields);
			ScheduleTeacher::query()->where('schedule_id', $id)->delete();
			ScheduleTeacher::setTeachersForSchedule($request->get('teacher'), $id);
		}

        //todo
        //Log::getInstance()->logAction(__CLASS__,__METHOD__,'Сохранен урок',User::getInstance()->getUserId(),$sql);

        return view('includes/schedule-lesson', [
            'schedules' => Schedule::query()
                ->where('grade_id', $fields['grade_id'])
                ->where('weekday', $fields['weekday'])
                ->where('number', $fields['number'])
                ->where('year', $year)
                ->get(),
            'can_edit' => true,
            'currentType' => $request->get('currentType'),
			'role' => $role
        ]);
    }

    public function deleteLesson($id) {
        Schedule::destroy($id);
		ScheduleTeacher::query()->where('schedule_id', $id)->delete();
	}

    public function copyLesson(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        $scheduleCopy = $schedule->replicate();

        $scheduleCopy->weekday = $request->get('weekday');
        $scheduleCopy->number =  $request->get('number');
        $scheduleCopy->save();

        ScheduleTeacher::setTeachersForSchedule($schedule->scheduleTeacher->pluck('teacher_id')->toArray(), $scheduleCopy->id);


        return view('includes/schedule-lesson', [
            'schedules' => Schedule::query()
                ->where('grade_id', $scheduleCopy->grade_id)
                ->where('weekday', $scheduleCopy->weekday)
                ->where('number', $scheduleCopy->number)
                ->where('year', $scheduleCopy->year)
                ->get(),
            'can_edit' => true,
            'currentType' => $request->get('currentType'),
            'role' => 'teacher'
        ]);
    }

    public function moveLesson(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        $scheduleCopy = $schedule->replicate();

        $scheduleCopy->weekday = $request->get('weekday');
        $scheduleCopy->number =  $request->get('number');
        $scheduleCopy->tms = Carbon::tomorrow();
        $scheduleCopy->save();

        foreach ($schedule->scheduleTeacher as $teacher) {
            $scheduleTeacher = new ScheduleTeacher();
            $scheduleTeacher->schedule_id = $scheduleCopy->id;
            $scheduleTeacher->teacher_id = $teacher->teacher_id;
            $scheduleTeacher->save();
        }

        $schedule->tms_end = Carbon::now();
        $schedule->save();



        return view('includes/schedule-lesson', [
            'schedules' => Schedule::query()
                ->where('grade_id', $scheduleCopy->grade_id)
                ->where('weekday', $scheduleCopy->weekday)
                ->where('number', $scheduleCopy->number)
                ->where('year', $scheduleCopy->year)
                ->get(),
            'can_edit' => true,
            'currentType' => $request->get('currentType'),
            'role' => 'teacher'
        ]);
    }

    public function form(Request $request, $id) {

        if(!empty($id)) {
            $schedule = Schedule::find($id);
            $selectedTeachers = [];
            foreach ($schedule->scheduleTeacher as $st) {
				$selectedTeachers[] = $st->teacher_id;
			}
        } else {
            $schedule = new Schedule();
            $schedule->tms = '2020-09-01';
            $schedule->tms_end = '2021-05-30';
        }

        $currentGradeId = (int)($request->get('gradeNum')??$schedule->grade_id);
		$grade = Grade::query()->where('id',  $currentGradeId)->first();

		return view('includes/schedule-edit', [
            'schedule' => $schedule,
            'selectedTeachers' => $selectedTeachers??[],
            'teachers' => User::query()->whereIn('role_id', [1,3])->get(),
            'lessons' => Lesson::all(),
            'currentGrade' => $grade->number,
            'groups' => StudentGroup::query()
                ->where('grade_id', $schedule->grade_id)
                ->where('year', Year::getInstance()->getYear())
                ->get(),
            'dayNum' => $request->get('dayNum'),
            'lessonNum' => $request->get('lessonNum'),

        ]);
    }
}
